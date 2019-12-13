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
class novalnetOrder_Overview extends novalnetOrder_Overview_parent
{
    public $aNovalnetPayments = array( 'novalnetcreditcard', 'novalnetsepa', 'novalnetinvoice', 'novalnetprepayment', 'novalnetpaypal', 'novalnetonlinetransfer', 'novalnetideal', 'novalneteps' , 'novalnetgiropay', 'novalnetprzelewy24','novalnetbarzahlen' );

    /**
     * Returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        $sTemplate = parent::render();
        $sOxId = $this->getEditObjectId();
        if (isset($sOxId) && $sOxId != "-1") {
            $oOrder = $this->_aViewData['edit'];
            if (in_array($oOrder->oxorder__oxpaymenttype->value, $this->aNovalnetPayments)) {
                $this->_aViewData['aNovalnetPayments'] = $this->aNovalnetPayments;
                $this->_aViewData['aNovalnetActions']  = $this->_displayNovalnetActions($oOrder->oxorder__oxordernr->value);
            }
        }
        return $sTemplate;
    }

    /**
     * Gets Novalnet transaction credentials
     *
     * @param integer $iOrderNo
     * @param boolean $blUpdate
     *
     * @return array
     */
    private function _getTransactionCredentials($iOrderNo, $blUpdate = false)
    {
        $this->oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $sSQL = 'SELECT trans.AMOUNT, trans.TOTAL_AMOUNT, trans.REFUND_AMOUNT, trans.GATEWAY_STATUS, trans.PAYMENT_ID, trans.ZERO_TRANSACTION, subs.SUBS_ID, subs.TERMINATION_REASON, invpre.DUE_DATE FROM novalnet_transaction_detail trans
        LEFT JOIN novalnet_subscription_detail subs ON trans.ORDER_NO = subs.ORDER_NO
        LEFT JOIN novalnet_preinvoice_transaction_detail invpre ON trans.ORDER_NO = invpre.ORDER_NO WHERE trans.ORDER_NO = "' . $iOrderNo . '"';
        if (!empty($blUpdate))
            $sSQL = 'SELECT trans.VENDOR_ID, trans.PRODUCT_ID, trans.TARIFF_ID, trans.AUTH_CODE, trans.TEST_MODE, trans.PAYMENT_TYPE, trans.PAYMENT_ID, trans.TID, trans.ZERO_TRXNDETAILS, trans.ZERO_TRXNREFERENCE, trans.MASKED_DETAILS, subs.TID AS SUB_TID, subs.SUBS_ID, subs.TERMINATION_REASON FROM novalnet_transaction_detail trans LEFT JOIN novalnet_subscription_detail subs ON trans.ORDER_NO = subs.ORDER_NO WHERE trans.ORDER_NO = "' . $iOrderNo . '"';

        return $this->oDb->getRow($sSQL);
    }

    /**
     * Handles the Novalnet extension features visibility features
     *
     * @param integer $iOrderNo
     */
    private function _displayNovalnetActions($iOrderNo)
    {
        $oOrder        = $this->_aViewData['edit'];
        $oNovalnetUtil = oxNew('novalnetUtil');
        $aTransDetails = $this->_getTransactionCredentials($iOrderNo);
        $sOrderDate    = strtotime(date('Y-m-d', strtotime($oOrder->oxorder__oxorderdate->value)));

        $this->_aViewData['dNovalnetAmount'] = $aTransDetails['TOTAL_AMOUNT'];
        $this->_aViewData['dOrderAmount']    = $oOrder->oxorder__oxtotalordersum->value * 100;

        if (in_array($aTransDetails['PAYMENT_ID'], array( '27', '41', '59' ))) {
            $sSql = 'SELECT SUM(AMOUNT) AS PAID_AMOUNT FROM novalnet_callback_history where ORDER_NO = "' . $iOrderNo . '"';
            $aResult = $this->oDb->getRow($sSql);
        }

        $dAmount = $aResult['PAID_AMOUNT'];
        if (!empty($aTransDetails['REFUND_AMOUNT'])) {
           $dAmount =  $aTransDetails['REFUND_AMOUNT'] + $aResult['PAID_AMOUNT'];
        }

        $this->_aViewData['blZeroBook']       = !empty($aTransDetails['ZERO_TRANSACTION']) && $aTransDetails['AMOUNT'] === '0' && in_array($aTransDetails['PAYMENT_ID'], array( '6', '34', '37' )) && $aTransDetails['GATEWAY_STATUS'] != '103';
        $this->_aViewData['blOnHold']         = $aTransDetails['AMOUNT'] !== '0' && in_array($aTransDetails['GATEWAY_STATUS'], array( '85', '91', '98', '99' ));
        $this->_aViewData['blAmountUpdate']   = $aTransDetails['TOTAL_AMOUNT'] !== '0' && $aTransDetails['PAYMENT_ID'] == '37' && $aTransDetails['GATEWAY_STATUS'] == '99';
        $this->_aViewData['blDuedateUpdate']  = $aTransDetails['TOTAL_AMOUNT'] !== '0' && (in_array($aTransDetails['PAYMENT_ID'] ,array( '59','27'))) && $aTransDetails['GATEWAY_STATUS'] == '100' && ($aTransDetails['TOTAL_AMOUNT'] > $dAmount);
        $this->_aViewData['sKey']   = $aTransDetails['PAYMENT_ID'];
        $this->_aViewData['sNovalnetDueDate'] = !empty($this->_aViewData['blDuedateUpdate']) ? $aTransDetails['DUE_DATE'] : '';
        $this->_aViewData['blAmountRefund']   = $aTransDetails['TOTAL_AMOUNT'] !== '0' && $aTransDetails['GATEWAY_STATUS'] == '100';
        $this->_aViewData['blRefundRef']      = !empty($this->_aViewData['blAmountRefund']) && $sOrderDate < strtotime(date('Y-m-d'));
        $this->_aViewData['blSubsCancel']     = $aTransDetails['AMOUNT'] !== '0' && $aTransDetails['GATEWAY_STATUS'] != '103' && !empty($aTransDetails['SUBS_ID']) && empty($aTransDetails['TERMINATION_REASON']);
    }

    /**
     * Performs the Novalnet extension actions
     *
     */
    public function performNovalnetAction()
    {
        $aData = oxRegistry::getConfig()->getRequestParameter('novalnet');
        $oLang = oxRegistry::getLang();
        if ($this->_validateNovalnetRequest($aData)) {
            $this->oNovalnetUtil = oxNew('novalnetUtil');
            $aTransDetails = $this->_getTransactionCredentials($aData['iOrderNo'], true);
            $aRequest['vendor']    = $aTransDetails['VENDOR_ID'];
            $aRequest['product']   = $aTransDetails['PRODUCT_ID'];
            $aRequest['tariff']    = $aTransDetails['TARIFF_ID'];
            $aRequest['auth_code'] = $aTransDetails['AUTH_CODE'];
            $aRequest['key']       = $aTransDetails['PAYMENT_ID'];
            $aRequest['tid']       = $aTransDetails['TID'];
            if ($aData['sRequestType'] == 'status_change') {
                $aRequest['status']      = $aData['sTransStatus'];
                $aRequest['edit_status'] = 1;
            } elseif (in_array($aData['sRequestType'], array( 'amount_update', 'amount_duedate_update' ))) {
                $aRequest['status']            = 100;
                $aRequest['amount']            = $aData['sUpdateAmount'];
                $aRequest['edit_status']       = 1;
                $aRequest['update_inv_amount'] = 1;
                if ($aData['sRequestType'] == 'amount_duedate_update')
                    $aRequest['due_date'] = date('Y-m-d', strtotime($aData['sUpdateDate']));
            } elseif ($aData['sRequestType'] == 'amount_refund') {
                if (!empty($aData['sRefundRef']))
                    $aRequest['refund_ref'] = $aData['sRefundRef'];

                $aRequest['refund_param']   = $aData['sRefundAmount'];
                $aRequest['refund_request'] = 1;
            } elseif ($aData['sRequestType'] == 'subscription_cancel') {
                if (!empty($aTransDetails['SUB_TID'])) {
                    $aRequest['tid']       = $aTransDetails['SUB_TID'];
                }
                $aRequest['cancel_sub']    = 1;
                $aRequest['cancel_reason'] = $aData['sSubsCancel'];
            } elseif ($aData['sRequestType'] == 'amount_book') {
                $aRequest                = unserialize($aTransDetails['ZERO_TRXNDETAILS']);
                $aRequest['amount']      = $aData['sBookAmount'];
                $aRequest['order_no']    = $aData['iOrderNo'];
                $aRequest['payment_ref'] = $aTransDetails['ZERO_TRXNREFERENCE'];
                if ($aRequest['key'] == '37')
                    $aRequest['sepa_due_date'] = date('d.m.Y', strtotime('+' . (empty($aRequest['sepa_due_date']) || $aRequest['sepa_due_date'] <= 6 ? 7 : $aRequest['sepa_due_date']) . ' days'));
            }
            $aRequest['remote_ip']  = $this->oNovalnetUtil->getIpAddress();
            $aResponse              = $this->oNovalnetUtil->doCurlRequest($aRequest, 'https://payport.novalnet.de/paygate.jsp');
            $aResponse['child_tid'] = !empty($aResponse['tid']) ? $aResponse['tid'] : '';
            if ($aResponse['status'] == '100') {
                $aData = array_merge($aResponse, $aRequest, $aData);
                $aData['test_mode']      = $aTransDetails['TEST_MODE'];
                $aData['payment_type']   = $aTransDetails['PAYMENT_TYPE'];
                $aData['masked_details'] = '';
                if ($aData['key'] == '34')
                    $aData['masked_details'] = $aTransDetails['MASKED_DETAILS'];
                $this->_updateNovalnetComments($aData);
            } else {
                $sError = $this->oNovalnetUtil->setNovalnetPaygateError($aResponse);
                if ($aData['sRequestType'] == 'status_change')
                    $this->_aViewData['sOnHoldFailure'] = $sError;
                elseif ($aData['sRequestType'] == 'amount_update')
                    $this->_aViewData['sAmountUpdateFailure'] = $sError;
                elseif ($aData['sRequestType'] == 'amount_refund')
                    $this->_aViewData['sAmountRefundFailure'] = $sError;
                elseif ($aData['sRequestType'] == 'amount_duedate_update')
                    $this->_aViewData['sDuedateUpdateFailure'] = $sError;
                elseif ($aData['sRequestType'] == 'subscription_cancel')
                    $this->_aViewData['sSubsCancelFailure'] = $sError;
                elseif ($aData['sRequestType'] == 'amount_book')
                    $this->_aViewData['sZeroBookFailure'] = $sError;
            }
        }
    }

    /**
     * Validates the extension requests
     *
     * @param array $aData
     *
     * @return boolean
     */
    private function _validateNovalnetRequest($aData)
    {
        $oLang   = oxRegistry::getLang();
        $blError = true;
        if ($aData['sRequestType'] == 'amount_duedate_update') {
            $sRequestDate    = strtotime($aData['sUpdateDate']);
            $sRequestDueDate = date('Y-m-d', $sRequestDate);
            $sCurrentDate    = strtotime(date('Y-m-d'));
            if ($sRequestDueDate != $aData['sUpdateDate']) {
                $blError = false;
                $this->_aViewData['sDuedateUpdateFailure'] = $oLang->translateString('NOVALNET_INVALID_DUEDATE');
            } elseif ($sRequestDate < $sCurrentDate) {
                $blError = false;
                $this->_aViewData['sDuedateUpdateFailure'] = $oLang->translateString('NOVALNET_INVALID_PAST_DUEDATE');
            }
        }
        return $blError;
    }

    /**
     * Gets Novalnet subscription cancel reasons
     *
     * @return array
     */
    public function getNovalnetSubsReasons()
    {
        $oLang = oxRegistry::getLang();
        return array( $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_1'),
                      $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_2'),
                      $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_3'),
                      $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_4'),
                      $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_5'),
                      $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_6'),
                      $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_7'),
                      $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_8'),
                      $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_9'),
                      $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_10'),
                      $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_11'),
                    );
    }

    /**
     * Updates Novalnet comments in orders
     *
     * @param array $aData
     */
    private function _updateNovalnetComments($aData)
    {
        $oLang  = oxRegistry::getLang();
        $sOxId  = $this->getEditObjectId();
        $oOrder = oxNew('oxorder');
        $oOrder->load($sOxId);
        if ($aData['sRequestType'] == 'status_change') {
            $sConfirmMessage = in_array($aData['key'], array(27, 41)) ? sprintf($oLang->translateString('NOVALNET_STATUS_UPDATE_CONFIRMED_MESSAGE_WITH_DUEDATE'), $aData['tid'], $aData['due_date']) : sprintf($oLang->translateString('NOVALNET_STATUS_UPDATE_CONFIRMED_MESSAGE'), date('Y-m-d'), date('H:i:s'));

            $sMessage = $aData['sTransStatus'] == 100 ? $sConfirmMessage : sprintf($oLang->translateString('NOVALNET_STATUS_UPDATE_CANCELED_MESSAGE'), date('Y-m-d'), date('H:i:s'));

            $sSQL = 'UPDATE novalnet_transaction_detail SET GATEWAY_STATUS = "' . $aData['tid_status'] . '" WHERE ORDER_NO = "' . $aData['iOrderNo'] . '"';
            if( $aData['sTransStatus'] == 100 && $aData['key'] == '34') {
                $sUpdateSQL = 'UPDATE oxorder SET OXPAID = "' . date('Y-m-d H:i:s') . '" WHERE OXORDERNR ="' . $aData['iOrderNo'] . '"';
                $this->oDb->execute($sUpdateSQL);
            }
            if ($aData['masked_details'] != '' && !empty($aData['paypal_transaction_id'])) {
                $sMaskedDetails = serialize(array( 'paypal_transaction_id' => $aData['paypal_transaction_id']));
                $sSQL = "UPDATE novalnet_transaction_detail SET GATEWAY_STATUS = '" . $aData['tid_status'] . "', MASKED_DETAILS = '" . $sMaskedDetails . "' WHERE ORDER_NO = '" . $aData['iOrderNo'] . "'";
            }
            $this->oDb->execute($sSQL);
              if (in_array($aData['key'], array('27', '41')) && $aData['status'] != '103') {
                 $sSQL = 'UPDATE novalnet_preinvoice_transaction_detail SET DUE_DATE = "'. $aData['due_date'] .'" WHERE ORDER_NO ="' . $aData['iOrderNo'] . '"';
                 $this->oDb->execute($sSQL);
                    $sSQL            = 'SELECT ACCOUNT_HOLDER as invoice_account_holder, BANK_IBAN AS invoice_iban, BANK_BIC AS invoice_bic, BANK_NAME AS invoice_bankname, BANK_CITY AS invoice_bankplace, TID AS tid,AMOUNT as amount, ORDER_NO as order_no, INVOICE_REF AS invoice_ref, PAYMENT_REF AS payment_ref FROM novalnet_preinvoice_transaction_detail WHERE ORDER_NO = "' . $aData['iOrderNo'] . '"';
                    $aInvoiceDetails = $this->oDb->getRow($sSQL);
                    $aInvoiceDetails['due_date'] = $aData['due_date'];
                    $aInvoiceDetails['currency'] = $oOrder->oxorder__oxcurrency->value;
                    if (!empty($aInvoiceDetails['payment_ref'])) {

                        $aInvoiceDetails = array_merge($aInvoiceDetails, unserialize($aInvoiceDetails['payment_ref']));
                    } else {
                        $sPaymentType = in_array($aData['payment_type'], array('INVOICE', 'novalnetinvoice')) ? 'novalnetinvoice' : 'novalnetprepayment';
                        $aInvPreReference = array( 'payment_ref1' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefOne' . $sPaymentType),
                                                   'payment_ref2' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefTwo' . $sPaymentType),
                                                   'payment_ref3' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefThree' . $sPaymentType)
                                                  );
                        $aInvoiceDetails  = array_merge($aInvoiceDetails, $aInvPreReference);
                        $sInvPreReference = serialize($aInvPreReference);
                    }
                     $aInvoiceComments = '<br>'.$oLang->translateString('NOVALNET_TRANSACTION_ID') . $aData['tid'];

                    if (!empty($aData['test_mode'])) {
                        $aInvoiceComments .= $oLang->translateString('NOVALNET_TEST_ORDER');
                    }
                    if ($aData['key'] == '41') {
                        $aInvoiceComments .= '<br>'.$oLang->translateString('NOVALNET_PAYMENT_GUARANTEE_COMMENTS');
                    }
                    $aInvoiceComments .= $this->oNovalnetUtil->getInvoiceComments($aInvoiceDetails);
                    $sMessage .= $aInvoiceComments;
                    $sLang = $this->oDb->getRow('SELECT OXLANG FROM oxorder where OXORDERNR = "' . $aData['order_no']. '"');
                    $this->oNovalnetUtil->sendPaymentNotificationMail($sLang['OXLANG'], $aInvoiceComments, $aData['order_no']);
                }
        } elseif ($aData['sRequestType'] == 'amount_update') {
            $sFormattedAmount = $oLang->formatCurrency($aData['sUpdateAmount']/100) . ' ' . $oOrder->oxorder__oxcurrency->rawValue;
            $sMessage         = sprintf($oLang->translateString('NOVALNET_AMOUNT_UPDATED_MESSAGE'), $sFormattedAmount, date('Y-m-d'), date('H:i:s'));

            $sSQL = 'UPDATE novalnet_transaction_detail SET TOTAL_AMOUNT = "' . $aData['amount'] . '" WHERE ORDER_NO = "' . $aData['iOrderNo'] . '"';
            $this->oDb->execute($sSQL);
        } elseif ($aData['sRequestType'] == 'amount_book') {
            $sFormattedAmount = $oLang->formatCurrency($aData['sBookAmount']/100) . ' ' . $oOrder->oxorder__oxcurrency->rawValue;
            $sMessage = '<br><br>'.$oLang->translateString('NOVALNET_TRANSACTION_ID') . $aData['tid'];
             if (!empty($aData['test_mode']))
                    $sMessage .= $oLang->translateString('NOVALNET_TEST_ORDER');


            $sMessage .= sprintf($oLang->translateString('NOVALNET_AMOUNT_BOOKED_MESSAGE'), $sFormattedAmount, $aData['tid']);

            $sSQL = 'UPDATE novalnet_transaction_detail SET GATEWAY_STATUS = "' . $aData['tid_status'] . '", AMOUNT = "' . $aData['amount'] . '", TOTAL_AMOUNT = "' . $aData['amount'] . '", TID = "' . $aData['tid'] . '" WHERE ORDER_NO = "' . $aData['iOrderNo'] . '"';
            $this->oDb->execute($sSQL);

            $sSQL = 'UPDATE novalnet_callback_history SET AMOUNT = "' . $aData['amount'] . '", ORG_TID = "' . $aData['tid'] . '" WHERE ORDER_NO = "' . $aData['iOrderNo'] . '"';
            $this->oDb->execute($sSQL);
        } elseif ($aData['sRequestType'] == 'amount_duedate_update') {
            $sFormattedAmount = $oLang->formatCurrency($aData['sUpdateAmount']/100) . ' ' . $oOrder->oxorder__oxcurrency->rawValue;
            $sMessage = sprintf($oLang->translateString('NOVALNET_AMOUNT_DATE_UPDATED_MESSAGE'), $sFormattedAmount, date('d.m.Y', strtotime($aData['due_date'])));
            $sMessage .= '<br><br>' . $oLang->translateString('NOVALNET_TRANSACTION_DETAILS');
            $sMessage .= $oLang->translateString('NOVALNET_TRANSACTION_ID') . $aData['tid'];
             if (!empty($aData['test_mode']))
                    $sMessage .= $oLang->translateString('NOVALNET_TEST_ORDER');

            if ($aData['key'] == '27') {
                $sSQL            = 'SELECT ACCOUNT_HOLDER as invoice_account_holder, BANK_IBAN AS invoice_iban, BANK_BIC AS invoice_bic, BANK_NAME AS invoice_bankname, BANK_CITY AS invoice_bankplace, TID AS tid, ORDER_NO as order_no, INVOICE_REF AS invoice_ref, PAYMENT_REF AS payment_ref FROM novalnet_preinvoice_transaction_detail WHERE ORDER_NO = "' . $aData['iOrderNo'] . '"';
                $aInvoiceDetails = $this->oDb->getRow($sSQL);
                $aInvoiceDetails['amount']   = $aData['amount'];
                $aInvoiceDetails['due_date'] = $aData['due_date'];
                $aInvoiceDetails['currency'] = $oOrder->oxorder__oxcurrency->value;
                if (!empty($aInvoiceDetails['payment_ref'])) {

                    $aInvoiceDetails = array_merge($aInvoiceDetails, unserialize($aInvoiceDetails['payment_ref']));
                } else {
                    $sPaymentType = in_array($aData['payment_type'], array('INVOICE', 'novalnetinvoice')) ? 'novalnetinvoice' : 'novalnetprepayment';
                    $aInvPreReference = array( 'payment_ref1' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefOne' . $sPaymentType),
                                               'payment_ref2' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefTwo' . $sPaymentType),
                                               'payment_ref3' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefThree' . $sPaymentType)
                                              );
                    $aInvoiceDetails  = array_merge($aInvoiceDetails, $aInvPreReference);
                    $sInvPreReference = serialize($aInvPreReference);
                }
                $sMessage .= $this->oNovalnetUtil->getInvoiceComments($aInvoiceDetails);
            } else if ($aData['key'] == '59') {
                $sSQL            = 'SELECT PAYMENT_REF FROM novalnet_preinvoice_transaction_detail WHERE ORDER_NO = "' . $aData['iOrderNo'] . '"';
                $aBarzahlen = $this->oDb->getRow($sSQL);
                $aBarzahlenDetails = unserialize($aBarzahlen['PAYMENT_REF']);
                $aBarzahlenDetails['nearest_store']['cashpayment_due_date'] = $aData['due_date'];
                $aBarzahlenDetails['nearest_store']['amount']   = $aData['amount'];
                $sMessage .= $this->oNovalnetUtil->getBarzahlenComments($aBarzahlenDetails['nearest_store']);
            }

            $sSQL = 'UPDATE novalnet_preinvoice_transaction_detail SET AMOUNT = "' . $aData['amount'] . '", DUE_DATE = "' . $aData['due_date'] . '" WHERE ORDER_NO = "' . $aData['iOrderNo'] . '"';
            if (!empty($sInvPreReference))
                $sSQL = 'UPDATE novalnet_preinvoice_transaction_detail SET AMOUNT = "' . $aData['amount'] . '", DUE_DATE = "' . $aData['due_date'] . '", PAYMENT_REF = "' . $sInvPreReference . '" WHERE ORDER_NO = "' . $aData['iOrderNo'] . '"';

            $this->oDb->execute($sSQL);

            $sSQL = 'UPDATE novalnet_transaction_detail SET TOTAL_AMOUNT = "' . $aData['amount'] . '" WHERE ORDER_NO = "' . $aData['iOrderNo'] . '"';
            $this->oDb->execute($sSQL);
            $sSql = 'SELECT SUM(amount) AS paid_amount FROM novalnet_callback_history where ORDER_NO = "' . $aData['iOrderNo'] . '"';
            $aResult     = $this->oDb->getRow($sSql);
            $dPaidAmount = $aResult['paid_amount'];
            if (!empty($dPaidAmount) && ($dPaidAmount <= $aData['amount'])) {
                $sUpdateSQL = 'UPDATE oxorder SET OXPAID = "' . date('Y-m-d H:i:s') . '" WHERE OXORDERNR ="' . $aData['iOrderNo'] . '"';
                $this->oDb->execute($sUpdateSQL);
            }
        }

        $sSQL = 'UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sMessage . '") WHERE OXORDERNR = "' . $aData['iOrderNo'] . '"';
        $this->oDb->execute($sSQL);

        if ($aData['sRequestType'] == 'subscription_cancel') {
            $sMessage = $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCELED_MESSAGE') . $aData['sSubsCancel'];

            $sSQL = 'UPDATE novalnet_subscription_detail SET TERMINATION_REASON = "' . $aData['sSubsCancel'] . '", TERMINATION_AT = "' . date('Y-m-d H:i:s') . '" WHERE TID = "' . $aData['tid'] . '"';
            $this->oDb->execute($sSQL);

            $sSQL = 'SELECT SUBS_ID FROM novalnet_subscription_detail WHERE TID='.$aData['tid'];
            $aSubId = $this->oDb->getRow($sSQL);

            $sSQL = 'SELECT ORDER_NO from novalnet_subscription_detail WHERE SUBS_ID  = "'.$aSubId['SUBS_ID'].'"';

            $aOrderNr = $this->oDb->getAll($sSQL);
            foreach($aOrderNr as $skey => $dValue) {

               $sSQL = 'UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sMessage . '") WHERE OXORDERNR = "' . $dValue['ORDER_NO'] . '"';
               $this->oDb->execute($sSQL);
            }
        }

       if ($aData['sRequestType'] == 'amount_refund') {
            $sFormattedAmount = $oLang->formatCurrency($aData['sRefundAmount']/100) . ' ' . $oOrder->oxorder__oxcurrency->rawValue;
            $sMessage         = sprintf($oLang->translateString('NOVALNET_AMOUNT_REFUNDED_PARENT_TID_MESSAGE'), $aData['tid'], $sFormattedAmount);
            if (!empty($aData['child_tid'])) {
                $sMessage = $sMessage . sprintf($oLang->translateString('NOVALNET_AMOUNT_REFUNDED_CHILD_TID_MESSAGE'), $aData['child_tid']);
            }

            $sSQL = 'UPDATE novalnet_transaction_detail SET GATEWAY_STATUS = "' . $aData['tid_status'] . '", REFUND_AMOUNT = REFUND_AMOUNT+' . $aData['sRefundAmount'] . ' WHERE TID = "' . $aData['tid'] . '"';
            $this->oDb->execute($sSQL);

            if ($aData['status'] == '100' && $aData['tid_status'] != '100') {
                $sSQL = 'SELECT SUBS_ID FROM novalnet_subscription_detail WHERE TID='.$aData['tid'];
                $aSubId = $this->oDb->getRow($sSQL);
                 if (!empty($aSubId['SUBS_ID'])) {
                     $sSQL = 'UPDATE novalnet_subscription_detail SET TERMINATION_REASON  = "others" WHERE TID="' . $aData['tid'] . '"';
                     $this->oDb->execute($sSQL);
                 }
            }
            $sSQL = 'UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sMessage . '") WHERE OXORDERNR = "' . $aData['iOrderNo'] . '"';
            $this->oDb->execute($sSQL);
        }

    }
}
?>
