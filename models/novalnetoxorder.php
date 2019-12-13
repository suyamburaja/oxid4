<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License
 * that is bundled with this package in the file freeware_license_agreement.txt
 *
 * @author Novalnet <technic@novalnet.de>
 * @copyright Novalnet
 * @license GNU General Public License
 * @link https://www.novalnet.de
 *
 */
class novalnetOxOrder extends novalnetOxOrder_parent
{
    /**
     * Novalnet payments
     *
     * @var array
     */
    protected $_aNovalnetPayments = array( 'novalnetcreditcard', 'novalnetsepa', 'novalnetinvoice', 'novalnetprepayment', 'novalnetonlinetransfer', 'novalnetpaypal', 'novalnetideal', 'novalneteps', 'novalnetgiropay', 'novalnetprzelewy24','novalnetbarzahlen');

    /**
     * Finalizes the order in shop
     *
     * @param object  $oBasket
     * @param object  $oUser
     * @param boolean $blRecalculatingOrder
     *
     * @return boolean
     */
    public function finalizeOrder(oxBasket $oBasket, $oUser, $blRecalculatingOrder = false)
    {
        $this->sCurrentPayment = $oBasket->getPaymentId(); // to get the current payment
        $this->sNovalnetPaidDate = '0000-00-00 00:00:00';  // set default value for the paid date of the order for novalnet transaction
        // Checks the current payment method is not a Novalnet payment. If yes then skips the execution of this function
        if (!in_array($this->sCurrentPayment, $this->_aNovalnetPayments)) {
            return parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);
        }

       $this->oNovalnetSession = oxRegistry::getSession(); // to create a session object

        $sGetChallenge = $this->oNovalnetSession->getVariable('sess_challenge');

        if ($this->_checkOrderExist($sGetChallenge)) {
            oxRegistry::getUtils()->logger('BLOCKER');
            return self::ORDER_STATE_ORDEREXISTS;
        }

        if (!$blRecalculatingOrder) {
            $this->setId($sGetChallenge);

            if ($iOrderState = $this->validateOrder($oBasket, $oUser)) {
                return $iOrderState;
            }
        }

        $this->_setUser($oUser);

        $this->_loadFromBasket($oBasket);

        $oUserPayment = $this->_setPayment($oBasket->getPaymentId());

        if (!$blRecalculatingOrder) {
            $this->_setFolder();
        }

        $this->_setOrderStatus('NOT_FINISHED');

        $this->save();

        if (!$blRecalculatingOrder) {
            $blRet = $this->_executePayment($oBasket, $oUserPayment);
            if ($blRet !== true) {
                return $blRet;
            }
        }

        if (!$blRecalculatingOrder && $oBasket->getTsProductId()) {
            $blRet = $this->_executeTsProtection($oBasket);
            if ($blRet !== true) {
                return $blRet;
            }
        }

        $this->oNovalnetSession->deleteVariable('ordrem');
        $this->oNovalnetSession->deleteVariable('stsprotection');

        if (!$this->oxorder__oxordernr->value) {
            $this->_setNumber();
        } else {
            oxNew('oxCounter')->update($this->_getCounterIdent(), $this->oxorder__oxordernr->value);
        }

        // logs transaction details in novalnet tables
        if (!$blRecalculatingOrder) {
            $this->oNovalnetUtil = oxNew('novalnetUtil');
            $iOrderNo            = $this->oxorder__oxordernr->value;
            $this->_logNovalnetTransaction($oBasket);
            $this->_updateNovalnetComments();
            $this->_sendNovalnetPostbackCall(); // to send order number in post back call
            $this->oNovalnetUtil->clearNovalnetSession();
        }

        if (!$blRecalculatingOrder) {
            $this->_updateOrderDate();
        }

        $this->_setOrderStatus('OK');

        $oBasket->setOrderId($this->getId());

        $this->_updateWishlist($oBasket->getContents(), $oUser);

        $this->_updateNoticeList($oBasket->getContents(), $oUser);

        if (!$blRecalculatingOrder) {
            $this->_markVouchers($oBasket, $oUser);
        }

        if (!$blRecalculatingOrder) {
            $iRet = $this->_sendOrderByEmail($oUser, $oBasket, $oUserPayment);
        } else {
            $iRet = self::ORDER_STATE_OK;
        }

        return $iRet;
    }

    /**
     * Logs Novalnet transaction details into Novalnet tables in shop
     *
     */
    private function _logNovalnetTransaction($oBasket)
    {
        $this->oDb              = oxDb::getDb();
        $sProcessKey  = $sMaskedDetails = '';
        $sZeroTrxnDetails = $sZeroTrxnReference = NULL;
        $blZeroAmountBooking = $blReferenceTransaction = '0';
        $iOrderNo               = $this->oxorder__oxordernr->value;
        $oBasket = serialize($oBasket);
        $aRequest  = $this->oNovalnetSession->getVariable('aNovalnetGatewayRequest');
        $aResponse = $this->oNovalnetSession->getVariable('aNovalnetGatewayResponse');
        $this->aNovalnetData = array_merge($aRequest, $aResponse);

        $this->aNovalnetData['test_mode'] = $aRequest['test_mode'] == '1' ? $aRequest['test_mode'] : $aResponse['test_mode'];

        $sSubsId = !empty($this->aNovalnetData['subs_id']) ? $this->aNovalnetData['subs_id'] : '';

        // checks the current payment is credit card or direct debit sepa, Guaranteed direct debit sepa, Paypal
        if (in_array($this->aNovalnetData['key'], array( '6', '34', '37', '40' ))) {
            // checks the shopping type is zero amount booking - if yes need to save the transaction request
            if ($this->oNovalnetUtil->getNovalnetConfigValue('iShopType' . $this->sCurrentPayment) == '2' && $this->aNovalnetData['amount'] == 0) {
                if ($this->aNovalnetData['key'] == '6') {
                    unset($aRequest['unique_id'], $aRequest['pan_hash'], $aRequest['nn_it'], $aRequest['cc_3d']);
                } elseif (in_array($this->aNovalnetData['key'], array( '37', '40' ))) {
                    $aRequest['sepa_due_date'] = $this->oNovalnetUtil->getNovalnetConfigValue('iDueDatenovalnetsepa');
                    unset($aRequest['pin_by_callback'], $aRequest['pin_by_sms']);
                }
                unset($aRequest['on_hold'], $aRequest['create_payment_ref']);
                $sZeroTrxnDetails    = serialize($aRequest);
                $sZeroTrxnReference  = $this->aNovalnetData['tid'];
                $blZeroAmountBooking = '1';
            }
            if (!empty($this->aNovalnetData['create_payment_ref'])) {
                if ($this->aNovalnetData['key'] == '6') {
                    $sMaskedDetails = serialize( array( 'cc_type'      => $this->aNovalnetData['cc_card_type'],
                                                        'cc_holder'    => $this->aNovalnetData['cc_holder'],
                                                        'cc_no'        => $this->aNovalnetData['cc_no'],
                                                        'cc_exp_month' => $this->aNovalnetData['cc_exp_month'],
                                                        'cc_exp_year'  => $this->aNovalnetData['cc_exp_year']
                                                      )
                                               );
                } elseif ($this->aNovalnetData['key'] == '34') {
                    $sMaskedDetails = serialize(array( 'paypal_transaction_id' => $this->aNovalnetData['paypal_transaction_id']));
                } else {
                    $sMaskedDetails = serialize( array( 'bankaccount_holder' => html_entity_decode($this->aNovalnetData['bankaccount_holder']),
                                                        'iban'               => $this->aNovalnetData['iban'],
                                                        'bic'                => $this->aNovalnetData['bic'],
                                                      )
                                               );
                }
            }
            if (in_array($this->aNovalnetData['key'], array( '37', '40' ))) {
                $sProcessKey = !empty($this->aNovalnetData['sepa_hash']) ? $this->aNovalnetData['sepa_hash'] : $this->oNovalnetSession->getVariable('sHashRefnovalnetsepa');
                $this->oNovalnetSession->deleteVariable('sHashRefnovalnetsepa');
            }

            $blReferenceTransaction = (!empty($this->aNovalnetData['payment_ref'])) ? '1' : '0';
        }

       if ((in_array($this->aNovalnetData['key'], array( '6', '27', '37', '40', '41','59' )) && !isset($this->aNovalnetData['cc_3d']) && $this->oNovalnetUtil->getNovalnetConfigValue('blCC3DFraudActive') != '1') || ($this->aNovalnetData['key'] == '34' && isset($aRequest['payment_ref']))) {
            $this->aNovalnetData['amount'] = $this->aNovalnetData['amount'] * 100;
        }
        // logs the transaction credentials, status and amount details
        $this->oDb->execute('INSERT INTO novalnet_transaction_detail ( VENDOR_ID, PRODUCT_ID, AUTH_CODE, TARIFF_ID, TID, ORDER_NO, SUBS_ID, PAYMENT_ID, PAYMENT_TYPE, AMOUNT, CURRENCY, STATUS, GATEWAY_STATUS, TEST_MODE, CUSTOMER_ID, ORDER_DATE, TOTAL_AMOUNT, PROCESS_KEY, MASKED_DETAILS, REFERENCE_TRANSACTION, ZERO_TRXNDETAILS, ZERO_TRXNREFERENCE, ZERO_TRANSACTION, NNBASKET ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )', array( $this->aNovalnetData['vendor'], $this->aNovalnetData['product'], $this->aNovalnetData['auth_code'], $this->aNovalnetData['tariff'], $this->aNovalnetData['tid'], $iOrderNo, $sSubsId, $this->aNovalnetData['key'], $this->aNovalnetData['payment_type'], $this->aNovalnetData['amount'], $this->aNovalnetData['currency'], $this->aNovalnetData['status'], $this->aNovalnetData['tid_status'], $this->aNovalnetData['test_mode'], $this->aNovalnetData['customer_no'], date('Y-m-d H:i:s'), $this->aNovalnetData['amount'], $sProcessKey, $sMaskedDetails, $blReferenceTransaction, $sZeroTrxnDetails, $sZeroTrxnReference, $blZeroAmountBooking, $oBasket ));

        // check current payment is invoice or prepayment or guranteed invoice
        if (in_array($this->aNovalnetData['key'], array( '27', '41' ))) {
            $this->sInvoiceRef = 'BNR-' . $this->aNovalnetData['product'] . '-' . $iOrderNo;
            $aInvPreReference  = array(
                                        'payment_ref1' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefOne' . $this->sCurrentPayment),
                                        'payment_ref2' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefTwo' . $this->sCurrentPayment),
                                        'payment_ref3' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefThree' . $this->sCurrentPayment)
                                      );
            $this->aNovalnetData = array_merge($this->aNovalnetData, $aInvPreReference);
            $sInvPreReference    = serialize($aInvPreReference);

            $this->oDb->execute('INSERT INTO novalnet_preinvoice_transaction_detail ( ORDER_NO, TID, TEST_MODE, ACCOUNT_HOLDER, BANK_IBAN, BANK_BIC, BANK_NAME, BANK_CITY, AMOUNT, CURRENCY, INVOICE_REF, DUE_DATE, PAYMENT_REF, ORDER_DATE ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )', array( $iOrderNo, $this->aNovalnetData['tid'], $this->aNovalnetData['test_mode'], $this->aNovalnetData['invoice_account_holder'], $this->aNovalnetData['invoice_iban'], $this->aNovalnetData['invoice_bic'], $this->aNovalnetData['invoice_bankname'], $this->aNovalnetData['invoice_bankplace'], $this->aNovalnetData['amount'], $this->aNovalnetData['currency'], $this->sInvoiceRef, $this->aNovalnetData['due_date'], $sInvPreReference, date('Y-m-d H:i:s') ));
        }
        if ($this->aNovalnetData['key'] == '59') {
            $aStores['nearest_store'] = $this->oNovalnetUtil->getBarzahlenComments($this->aNovalnetData,true);
            $aStores['cp_checkout_token'] = $this->aNovalnetData['cp_checkout_token'] .'|'. $this->aNovalnetData['test_mode'];
            $this->oDb->execute('INSERT INTO novalnet_preinvoice_transaction_detail ( ORDER_NO, TID, TEST_MODE, AMOUNT, CURRENCY, DUE_DATE,PAYMENT_REF,ORDER_DATE ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )', array( $iOrderNo, $this->aNovalnetData['tid'], $this->aNovalnetData['test_mode'], $this->aNovalnetData['amount'], $this->aNovalnetData['currency'], $this->aNovalnetData['cashpayment_due_date'], serialize($aStores), date('Y-m-d H:i:s') ));
        }

        // logs the subscription details for subscription orders
        if (!empty($sSubsId)) {
            $this->oDb->execute('INSERT INTO novalnet_subscription_detail ( ORDER_NO, SUBS_ID, TID, SIGNUP_DATE ) VALUES ( ?, ?, ?, ?)', array($iOrderNo, $sSubsId, $this->aNovalnetData['tid'], date('Y-m-d H:i:s')));
        }

        // logs the transaction details in callback table
       if (!in_array($this->aNovalnetData['key'], array( '27', '59' )) && $this->aNovalnetData['status'] == 100 && !in_array($this->aNovalnetData['tid_status'], array('75', '86'))) {
            if ($this->aNovalnetData['tid_status'] != '85') // verifying paypal onhold status
                $this->sNovalnetPaidDate = date('Y-m-d H:i:s'); // set the paid date of the order for novalnet paid transaction

            $this->oDb->execute('INSERT INTO novalnet_callback_history ( PAYMENT_TYPE, STATUS, ORDER_NO, AMOUNT, CURRENCY, ORG_TID, PRODUCT_ID, CALLBACK_DATE ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )', array( $this->aNovalnetData['payment_type'], $this->aNovalnetData['status'], $iOrderNo, $this->aNovalnetData['amount'], $this->aNovalnetData['currency'], $this->aNovalnetData['tid'], $this->aNovalnetData['product'], date('Y-m-d H:i:s') ));
        }

        // logs the affiliate orders in affilliate table
        if ($this->oNovalnetSession->getVariable('nn_aff_id')) {
            $this->oDb->execute('INSERT INTO novalnet_aff_user_detail ( AFF_ID, CUSTOMER_ID, AFF_ORDER_NO) VALUES ( ?, ?, ?)', array($this->oNovalnetSession->getVariable('nn_aff_id'), $this->aNovalnetData['customer_no'], $iOrderNo));
        }

        $this->_checkNovalnetTestMode($aRequest['test_mode'], $aResponse['test_mode']);
    }

    /**
     * Send test transaction notification mail
     *
     * @param string $sRequestTestMode
     * @param string $sResponseTestMode
     *
     */
    private function _checkNovalnetTestMode($sRequestTestMode, $sResponseTestMode)
    {
        if ($this->oNovalnetUtil->getNovalnetConfigValue('blTestModeMail') && $sRequestTestMode == '0' && $sResponseTestMode == '1') {
            $oMail         = oxNew('oxEmail');
            $oUtils        = oxNew('oxUtils');
            $oShop         = $oMail->getShop();
            $sEmailSubject = $this->oNovalnetUtil->oLang->translateString('NOVALNET_TEST_MODE_NOTIFICATION_SUBJECT');
            $sMessage      = sprintf($this->oNovalnetUtil->oLang->translateString('NOVALNET_TEST_MODE_NOTIFICATION_MESSAGE'), $this->oxorder__oxordernr->value);
            $sEmailAddress   = trim($oShop->oxshops__oxowneremail->value);
            // validates 'to' address
            if ($oUtils->isValidEmail($sEmailAddress)) {
                $oMail->setRecipient($sEmailAddress);
                $oMail->setFrom($sEmailAddress);
            }

            $oMail->setSubject($sEmailSubject);
            $oMail->setBody( $sMessage );
            $oMail->send();
        }
    }

    /**
     * Updates Novalnet comments for the order in shop
     *
     */
    private function _updateNovalnetComments()
    {
        $sNovalnetComments  = $this->oNovalnetUtil->oLang->translateString('NOVALNET_TRANSACTION_DETAILS');

        $sNovalnetComments .= $this->oNovalnetUtil->oLang->translateString('NOVALNET_TRANSACTION_ID') . $this->aNovalnetData['tid'];

        if (!empty($this->aNovalnetData['test_mode'])) {
            $sNovalnetComments .= $this->oNovalnetUtil->oLang->translateString('NOVALNET_TEST_ORDER');
        }

         if (in_array($this->aNovalnetData['key'], array('41', '40')) && $this->aNovalnetData['tid_status'] == 75) {
                $sNovalnetComments .= '<br>'.$this->oNovalnetUtil->oLang->translateString('NOVALNET_PAYMENT_GUARANTEE_COMMENTS');
        }
        
        if ($this->aNovalnetData['key'] == '41' && $this->aNovalnetData['tid_status'] == 75) {
           $sNovalnetComments .= $this->oNovalnetUtil->oLang->translateString('NOVALNET_GUARANTEE_TEXT');
        }

        if (in_array($this->aNovalnetData['key'], array('27', '41'))) {
            $this->aNovalnetData['invoice_ref'] = $this->sInvoiceRef;
            $this->aNovalnetData['order_no']    = $this->oxorder__oxordernr->value;
            if (!in_array($this->aNovalnetData['tid_status'], array(75, 91))) {
                $sNovalnetInvoiceComments = $this->oNovalnetUtil->getInvoiceComments($this->aNovalnetData);
                $sNovalnetComments       .= $sNovalnetInvoiceComments;
            }
        }
        if ($this->aNovalnetData['key'] =='59') {
            $sNovalnetComments       .= $this->oNovalnetUtil->getBarzahlenComments($this->aNovalnetData);
        }

        $sUpdateSQL = 'UPDATE oxorder SET OXPAID = "' . $this->sNovalnetPaidDate . '", NOVALNETCOMMENTS = "' . $sNovalnetComments . '" WHERE OXORDERNR ="' . $this->oxorder__oxordernr->value . '"';

        $this->oDb->execute($sUpdateSQL);
        $this->oxorder__oxpaid           = new oxField($this->sNovalnetPaidDate);
        $this->oxorder__novalnetcomments = new oxField($sNovalnetComments);
    }

    /**
     * Sends the postback call to the Novalnet server.
     *
     */
    private function _sendNovalnetPostbackCall()
    {
        $aPostBackParams = array( 'vendor'    => $this->aNovalnetData['vendor'],
                                  'product'   => $this->aNovalnetData['product'],
                                  'tariff'    => $this->aNovalnetData['tariff'],
                                  'auth_code' => $this->aNovalnetData['auth_code'],
                                  'key'       => $this->aNovalnetData['key'],
                                  'status'    => 100,
                                  'tid'       => $this->aNovalnetData['tid'],
                                  'order_no'  => $this->oxorder__oxordernr->value,
                                  'remote_ip' => $this->oNovalnetUtil->getIpAddress()
                                );

        if (in_array($this->aNovalnetData['key'], array('27', '41')))
            $aPostBackParams['invoice_ref'] = $this->sInvoiceRef;

        $this->oNovalnetUtil->doCurlRequest($aPostBackParams, 'https://payport.novalnet.de/paygate.jsp');
    }

    /**
     * Exporting standard invoice pdf
     *
     * @param object $oPdf
     */
    public function exportStandart($oPdf)
    {
        // preparing order curency info
        $myConfig = $this->getConfig();

        $oPdfBlock = class_exists('InvoicepdfBlock') ? new InvoicepdfBlock() : new PdfBlock();

        $this->_oCur = $myConfig->getCurrencyObject($this->oxorder__oxcurrency->value);
        if (!$this->_oCur) {
            $this->_oCur = $myConfig->getActShopCurrencyObject();
        }

        // loading active shop
        $oShop = $this->_getActShop();

        // shop information
        $oPdf->setFont($oPdfBlock->getFont(), '', 6);
        $oPdf->text(15, 55, $oShop->oxshops__oxname->getRawValue() . ' - ' . $oShop->oxshops__oxstreet->getRawValue() . ' - ' . $oShop->oxshops__oxzip->value . ' - ' . $oShop->oxshops__oxcity->getRawValue());

        // billing address
        $this->_setBillingAddressToPdf($oPdf);

        // delivery address
        if ($this->oxorder__oxdelsal->value) {
            $this->_setDeliveryAddressToPdf($oPdf);
        }

        // loading user
        $oUser = oxNew('oxuser');
        $oUser->load($this->oxorder__oxuserid->value);

        // user info
        $sText = $this->translate('ORDER_OVERVIEW_PDF_FILLONPAYMENT');
        $oPdf->setFont($oPdfBlock->getFont(), '', 5);
        $oPdf->text(195 - $oPdf->getStringWidth($sText), 55, $sText);

        // customer number
        $sCustNr = $this->translate('ORDER_OVERVIEW_PDF_CUSTNR') . ' ' . $oUser->oxuser__oxcustnr->value;
        $oPdf->setFont($oPdfBlock->getFont(), '', 7);
        $oPdf->text(195 - $oPdf->getStringWidth($sCustNr), 59, $sCustNr);

        // setting position if delivery address is used
        if ($this->oxorder__oxdelsal->value) {
            $iTop = 115;
        } else {
            $iTop = 91;
        }

        // shop city
        $sText = $oShop->oxshops__oxcity->getRawValue() . ', ' . date('d.m.Y', strtotime($this->oxorder__oxbilldate->value));
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop + 8, $sText);

        // shop VAT number
        if ($oShop->oxshops__oxvatnumber->value) {
            $sText = $this->translate('ORDER_OVERVIEW_PDF_TAXIDNR') . ' ' . $oShop->oxshops__oxvatnumber->value;
            $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop + 12, $sText);
            $iTop += 8;
        } else {
            $iTop += 4;
        }

        // invoice number
        $sText = $this->translate('ORDER_OVERVIEW_PDF_COUNTNR') . ' ' . $this->oxorder__oxbillnr->value;
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop + 8, $sText);

        // marking if order is canceled
        if ($this->oxorder__oxstorno->value == 1) {
            $this->oxorder__oxordernr->setValue($this->oxorder__oxordernr->getRawValue() . '   ' . $this->translate('ORDER_OVERVIEW_PDF_STORNO'), oxField::T_RAW);
        }

        // order number
        $oPdf->setFont($oPdfBlock->getFont(), '', 12);
        $oPdf->text(15, $iTop, $this->translate('ORDER_OVERVIEW_PDF_PURCHASENR') . ' ' . $this->oxorder__oxordernr->value);

        // order date
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $aOrderDate = explode(' ', $this->oxorder__oxorderdate->value);
        $sOrderDate = oxRegistry::get('oxUtilsDate')->formatDBDate($aOrderDate[0]);
        $oPdf->text(15, $iTop + 8, $this->translate('ORDER_OVERVIEW_PDF_ORDERSFROM') . $sOrderDate . $this->translate('ORDER_OVERVIEW_PDF_ORDERSAT') . $oShop->oxshops__oxurl->value);
        $iTop += 16;

        // product info header
        $oPdf->setFont($oPdfBlock->getFont(), '', 8);
        $oPdf->text(15, $iTop, $this->translate('ORDER_OVERVIEW_PDF_AMOUNT'));
        $oPdf->text(30, $iTop, $this->translate('ORDER_OVERVIEW_PDF_ARTID'));
        $oPdf->text(45, $iTop, $this->translate('ORDER_OVERVIEW_PDF_DESC'));
        $oPdf->text(135, $iTop, $this->translate('ORDER_OVERVIEW_PDF_VAT'));
        $oPdf->text(148, $iTop, $this->translate('ORDER_OVERVIEW_PDF_UNITPRICE'));
        $sText = $this->translate('ORDER_OVERVIEW_PDF_ALLPRICE');
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop, $sText);

        // separator line
        $iTop += 2;
        $oPdf->line(15, $iTop, 195, $iTop);

        // #345
        $siteH = $iTop;
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);

        // order articles
        $this->_setOrderArticlesToPdf($oPdf, $siteH, true);

        // generating pdf file
        $oArtSumm = class_exists('InvoicepdfArticleSummary') ? new InvoicepdfArticleSummary($this, $oPdf) : new PdfArticleSummary($this, $oPdf);

        $iHeight = $oArtSumm->generate($siteH);

        if ($siteH + $iHeight > 258) {
            $this->pdfFooter($oPdf);
            $iTop = $this->pdfHeader($oPdf);
            $oArtSumm->ajustHeight($iTop - $siteH);
            $siteH = $iTop;
        }
        $oArtSumm->run($oPdf);
        $siteH += $iHeight + 8;

        if (strpos($this->oxorder__oxpaymenttype->value, 'novalnet') !== false) {
            $this->setNovalnetComments($oPdf, $oPdfBlock, $siteH);
        }
        if ($siteH + $iHeight > 258) {
            $this->pdfFooter($oPdf);
            $iTop = $this->pdfHeader($oPdf);
            $oArtSumm->ajustHeight($iTop - $siteH);
            $siteH = $iTop;
        }
        $siteH += 8;

        $oPdf->text(15, $siteH, $this->translate('ORDER_OVERVIEW_PDF_GREETINGS'));
    }

    /**
     * Sets Novalnet comments in pdf
     *
     * @param object $oPdf
     * @param object $oPdfBlock
     * @param integer $iStartPos
     */
    public function setNovalnetComments($oPdf, $oPdfBlock, &$iStartPos) {
        $sNovalnetComments = '';
        if (!empty($this->oxorder__novalnet_transaction->value))
            $sNovalnetComments .= $this->translate('NOVALNET_TRANSACTION_DETAILS') . $this->oxorder__novalnet_transaction->value;
        if (!empty($this->oxorder__novalnet_comments->value))
            $sNovalnetComments .= $this->oxorder__novalnet_comments->value;
        if (!empty($sNovalnetComments))
            $sNovalnetComments = str_replace(array(PHP_EOL, '<br />'), '<br>', html_entity_decode($sNovalnetComments));

        $sNovalnetComments .= $this->oxorder__novalnetcomments->value;
        $aNovalnetComments = explode('<br>', html_entity_decode($sNovalnetComments));
        $iStartPos += 4;

        foreach ( $aNovalnetComments as $sNovalnetComment ) {
            if ($iStartPos > 243) {
                $this->pdffooter($oPdf);
                $oPdfBlock = class_exists('InvoicepdfBlock') ? new InvoicepdfBlock() : new PdfBlock();
                $iStartPos = $this->pdfHeader($oPdf);
                $oPdf->setFont($oPdfBlock->getFont(), '', 8);
            }
            $aTransComments = explode(' ', $sNovalnetComment);
            $iCount         = count($aTransComments);
            if ($iCount > 12) {
                $sNovalnetComment = wordwrap($sNovalnetComment, 120, '<br>');
                $aTransComments   = explode('<br>', $sNovalnetComment);
                foreach ( $aTransComments as $sTransComments ) {
                    if ($iStartPos > 243) {
                        $this->pdffooter($oPdf);
                        $oPdfBlock = class_exists('InvoicepdfBlock') ? new InvoicepdfBlock() : new PdfBlock();
                        $iStartPos = $this->pdfHeader($oPdf);
                        $oPdf->setFont($oPdfBlock->getFont(), '', 8);
                    }
                    $sText = strip_tags($sTransComments);
                    $oPdf->setFont($oPdfBlock->getFont(), '', 8);
                    $oPdf->text(15, $iStartPos, $sText);
                    $iStartPos += 4;
                }
            } else {
                $sText = strip_tags($sNovalnetComment);
                $oPdf->setFont($oPdfBlock->getFont(), '', 8);
                $oPdf->text(15, $iStartPos, $sText);
                $iStartPos += 4;
            }
        }
    }
}
?>
