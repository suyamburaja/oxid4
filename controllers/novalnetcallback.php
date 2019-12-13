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
class novalnetCallback extends oxUBase
{
    protected $_sThisTemplate    = 'novalnetcallback.tpl';

    protected $_oLang;// Get language object

    public $aCaptureParams;// Get REQUEST param

    public $aOrderDetails;// Get order details

    public $iRecurringOrder;// Get Recurring Order number

    public $technicNotifyMail = 'technic@novalnet.de';

    /** @Array Type of payment available - Level : 0 */
    protected $aPayments         = array( 'CREDITCARD', 'INVOICE_START', 'DIRECT_DEBIT_SEPA', 'GUARANTEED_INVOICE', 'GUARANTEED_DIRECT_DEBIT_SEPA', 'PAYPAL', 'ONLINE_TRANSFER', 'IDEAL', 'EPS', 'GIROPAY', 'PRZELEWY24','CASHPAYMENT');

    /** @Array Type of Chargebacks available - Level : 1 */
    protected $aChargebacks      = array( 'RETURN_DEBIT_SEPA', 'CREDITCARD_BOOKBACK', 'CREDITCARD_CHARGEBACK', 'PAYPAL_BOOKBACK', 'PRZELEWY24_REFUND', 'REFUND_BY_BANK_TRANSFER_EU', 'REVERSAL','CASHPAYMENT_REFUND', 'GUARANTEED_INVOICE_BOOKBACK', 'GUARANTEED_SEPA_BOOKBACK');

    /** @Array Type of CreditEntry payment and Collections available - Level : 2 */
    protected $aCollections      = array( 'INVOICE_CREDIT', 'CREDIT_ENTRY_CREDITCARD', 'CREDIT_ENTRY_SEPA', 'DEBT_COLLECTION_SEPA', 'DEBT_COLLECTION_CREDITCARD', 'ONLINE_TRANSFER_CREDIT', 'CASHPAYMENT_CREDIT');

    protected $aPaymentGroups    = array(
                                        'novalnetcreditcard'     => array( 'CREDITCARD', 'CREDITCARD_BOOKBACK', 'CREDITCARD_CHARGEBACK', 'CREDIT_ENTRY_CREDITCARD', 'DEBT_COLLECTION_CREDITCARD', 'SUBSCRIPTION_STOP', 'SUBSCRIPTION_REACTIVATE'),
                                        'novalnetsepa'           => array( 'DIRECT_DEBIT_SEPA', 'GUARANTEED_DIRECT_DEBIT_SEPA', 'RETURN_DEBIT_SEPA', 'DEBT_COLLECTION_SEPA', 'CREDIT_ENTRY_SEPA', 'SUBSCRIPTION_STOP', 'SUBSCRIPTION_REACTIVATE', 'REFUND_BY_BANK_TRANSFER_EU', 'TRANSACTION_CANCELLATION', 'GUARANTEED_SEPA_BOOKBACK'),
                                        'novalnetideal'          => array( 'IDEAL', 'REFUND_BY_BANK_TRANSFER_EU', 'ONLINE_TRANSFER_CREDIT', 'REVERSAL'),
                                        'novalnetonlinetransfer' => array( 'ONLINE_TRANSFER', 'REFUND_BY_BANK_TRANSFER_EU', 'ONLINE_TRANSFER_CREDIT', 'REVERSAL'),
                                        'novalnetpaypal'         => array( 'PAYPAL', 'PAYPAL_BOOKBACK', 'SUBSCRIPTION_STOP', 'SUBSCRIPTION_REACTIVATE'),
                                        'novalnetprepayment'     => array( 'INVOICE_START', 'INVOICE_CREDIT', 'SUBSCRIPTION_STOP','SUBSCRIPTION_REACTIVATE', 'REFUND_BY_BANK_TRANSFER_EU'),
                                        'novalnetinvoice'        => array( 'INVOICE_START', 'INVOICE_CREDIT', 'GUARANTEED_INVOICE', 'SUBSCRIPTION_STOP', 'SUBSCRIPTION_REACTIVATE', 'GUARANTEED_INVOICE_BOOKBACK', 'REFUND_BY_BANK_TRANSFER_EU', 'TRANSACTION_CANCELLATION'),
                                        'novalneteps'            => array( 'EPS', 'REFUND_BY_BANK_TRANSFER_EU'),                                       
                                        'novalnetgiropay'        => array( 'GIROPAY', 'REFUND_BY_BANK_TRANSFER_EU'),
                                        'novalnetprzelewy24'     => array( 'PRZELEWY24', 'PRZELEWY24_REFUND'),
                                        'novalnetbarzahlen'      => array( 'CASHPAYMENT', 'CASHPAYMENT_CREDIT', 'CASHPAYMENT_REFUND')
                                        );

    protected $aParamsRequired    = array('vendor_id', 'tid', 'payment_type', 'status', 'tid_status');

    protected $aAffParamsRequired = array('vendor_id', 'vendor_authcode', 'product_id', 'vendor_activation', 'aff_id', 'aff_authcode', 'aff_accesskey');

    /**
     * Returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        return $this->_sThisTemplate;
    }

    /**
     * Handles the callback request
     *
     * @return boolean
     */
    public function handleRequest()
    {
        $this->aCaptureParams     = array_map('trim', $_REQUEST);
        $this->oNovalnetUtil      = oxNew('novalnetutil');
        $this->oSession           = oxRegistry::getSession();
        $this->blProcessTestMode  = $this->oNovalnetUtil->getNovalnetConfigValue('blCallbackTestMode');
        $this->oDb                = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $this->_oLang             = oxNew('oxLang');

        $this->_aViewData['sNovalnetMessage'] = '';
        if ($this->_validateCaptureParams())
        {
            // check callpack is to update the affiliate detail or process the callback for the transaction
            if (!empty($this->aCaptureParams['vendor_activation']))
            {
                $this->_updateAffiliateActivationDetails();
            } else {
                $this->_processNovalnetCallback();
            }
        }
        return false;
    }

    /**
     * Adds affiliate account
     *
     */
    private function _updateAffiliateActivationDetails()
    {
        $sNovalnetAffSql     = 'INSERT INTO novalnet_aff_account_detail (VENDOR_ID, VENDOR_AUTHCODE, PRODUCT_ID, PRODUCT_URL, ACTIVATION_DATE, AFF_ID, AFF_AUTHCODE, AFF_ACCESSKEY) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )';
        $aNovalnetAffDetails = array( $this->aCaptureParams['vendor_id'], $this->aCaptureParams['vendor_authcode'], (!empty($this->aCaptureParams['product_id']) ? $this->aCaptureParams['product_id'] : ''), (!empty($this->aCaptureParams['product_url']) ? $this->aCaptureParams['product_url'] : ''), (!empty($this->aCaptureParams['activation_date']) ? date('Y-m-d H:i:s', strtotime($this->aCaptureParams['activation_date'])) : ''), $this->aCaptureParams['aff_id'], $this->aCaptureParams['aff_authcode'], $this->aCaptureParams['aff_accesskey'] );
        $this->oDb->execute( $sNovalnetAffSql, $aNovalnetAffDetails );

        $sMessage = 'Novalnet callback script executed successfully with Novalnet account activation information';
        $this->_sendMail($sMessage);
        $this->_displayMessage($sMessage);
    }

    /**
     * Validates the callback request
     *
     * @return boolean
     */
    private function _validateCaptureParams()
    {
        $sIpAllowed = gethostbyname('pay-nn.de');

        if (empty($sIpAllowed)) {
            $this->_displayMessage('Novalnet HOST IP missing');
            return false;
        }
        $sIpAddress = $this->oNovalnetUtil->getIpAddress();
        $sMessage   = '';
        if (($sIpAddress != $sIpAllowed) && empty($this->blProcessTestMode)) {
            $this->_displayMessage('Novalnet callback received. Unauthorised access from the IP [' . $sIpAddress . ']');
            return false;
        }

        $aParamsRequired = (!empty($this->aCaptureParams['vendor_activation'])) ? $this->aAffParamsRequired : $this->aParamsRequired;

        $this->aCaptureParams['shop_tid'] = $this->aCaptureParams['tid'];

        if (in_array($this->aCaptureParams['payment_type'], array_merge($this->aChargebacks, $this->aCollections))) {
            array_push($aParamsRequired, 'tid_payment');
            $this->aCaptureParams['shop_tid'] = $this->aCaptureParams['tid_payment'];
        } elseif (in_array($this->aCaptureParams['payment_type'], array('SUBSCRIPTION_STOP', 'SUBSCRIPTION_REACTIVATE')) || isset($this->aCaptureParams['subs_billing']) && $this->aCaptureParams['subs_billing'] == '1') {
            array_push($aParamsRequired, 'signup_tid');
            $this->aCaptureParams['shop_tid'] = $this->aCaptureParams['signup_tid'];
        }

        foreach ($aParamsRequired as $sValue) {
            if (empty($this->aCaptureParams[$sValue]))
                $sMessage .= 'Required param ( ' . $sValue . ' ) missing!<br>';
        }

        if (!empty($sMessage)) {
            $this->_displayMessage($sMessage);
            return false;
        }

        if (!empty($this->aCaptureParams['vendor_activation']))
            return true;

        if (!is_numeric($this->aCaptureParams['status']) || $this->aCaptureParams['status'] <= 0) {
            $this->_displayMessage('Novalnet callback received. Status (' . $this->aCaptureParams['status'] . ') is not valid');
            return false;
        }

        foreach (array('signup_tid', 'tid_payment', 'tid') as $sTid) {
            if (!empty($this->aCaptureParams[$sTid]) && !preg_match('/^\d{17}$/', $this->aCaptureParams[$sTid])) {
                $this->_displayMessage('Novalnet callback received. Invalid TID [' . $this->aCaptureParams[$sTid] . '] for Order');
                return false;
            }
        }
        return true;
    }

    /**
     * Process the callback request
     *
     * @return void
     */
    private function _processNovalnetCallback()
    {
        if (!$this->_getOrderDetails())
            return;

        $sSql              = 'SELECT SUM(amount) AS paid_amount FROM novalnet_callback_history where ORDER_NO = "' . $this->aOrderDetails['ORDER_NO'] . '"';
        $aResult           = $this->oDb->getRow($sSql);
        $dPaidAmount       = $aResult['paid_amount'];

        $dAmount = $this->aOrderDetails['TOTAL_AMOUNT'] - $this->aOrderDetails['REFUND_AMOUNT'];
        $dFormattedAmount  = sprintf('%0.2f', ($this->aCaptureParams['amount']/100)) . ' ' . $this->aCaptureParams['currency']; // Formatted callback amount
        $dFormattedAmount = str_replace('.', ',', $dFormattedAmount);

        $sLineBreak = '<br><br>';

        $iPaymentTypeLevel = $this->_getPaymentTypeLevel();

        $sPaymentSuccess = $this->aCaptureParams['status'] == 100 && $this->aCaptureParams['tid_status'] == 100;

         // Handling subscription stop & subscription reactivation process
        if ($this->_subscriptionProcess()) {
            return false;
        }

        if ($iPaymentTypeLevel === 0) {
            if (isset($this->aCaptureParams['subs_billing']) && $this->aCaptureParams['subs_billing'] == 1 ) {
                // checks status of callback. if 100, then recurring processed or subscription cancelled
               if (($this->aCaptureParams['status'] == '100' || ($this->aCaptureParams['payment_type'] == 'PAYPAL' && $this->aCaptureParams['status'] == '90')) && in_array($this->aCaptureParams['tid_status'], array('100',  '90', '91', '98', '99', '85'))) {
                    $sNovalnetComments = 'Novalnet Callback Script executed successfully for the subscription TID:' . $this->aCaptureParams['signup_tid'] . ' with amount: ' . $dFormattedAmount . ' on ' . date('Y-m-d H:i:s') . '. Please refer PAID transaction in our Novalnet Merchant Administration with the TID: ' . $this->aCaptureParams['tid'];
                    $this->_createFollowupOrder();

                    $this->_sendMail($sNovalnetComments);

                    $this->_displayMessage($sNovalnetComments, $this->iRecurringOrder);
                } else {
                    $this->_subscriptionCancel();
                }
            } elseif (in_array($this->aCaptureParams['payment_type'], array('PAYPAL', 'PRZELEWY24')) && $sPaymentSuccess) {
                if (!isset($dPaidAmount)) {
                    $sNovalnetCallbackSql = 'INSERT INTO novalnet_callback_history (PAYMENT_TYPE, STATUS, ORDER_NO, AMOUNT, CURRENCY, CALLBACK_TID, ORG_TID, PRODUCT_ID, CALLBACK_DATE) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )';
                    $aNovalnetCallbackDetails = array( $this->aCaptureParams['payment_type'], $this->aCaptureParams['status'], $this->aOrderDetails['ORDER_NO'], $this->aCaptureParams['amount'], $this->aCaptureParams['currency'], $this->aCaptureParams['tid'], $this->aCaptureParams['tid'], $this->aCaptureParams['product_id'], date('Y-m-d H:i:s') );
                    $this->oDb->execute($sNovalnetCallbackSql, $aNovalnetCallbackDetails);

                    $sNovalnetComments = 'Novalnet Callback Script executed successfully for the TID: ' . $this->aCaptureParams['tid'] . ' with amount ' . $dFormattedAmount . ' on ' . date('Y-m-d H:i:s');
                    $sComments = $sLineBreak . $sNovalnetComments;
                    $this->oDb->execute('UPDATE oxorder SET OXPAID = "' . date('Y-m-d H:i:s') . '", NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sComments . '") WHERE OXORDERNR ="' . $this->aOrderDetails['ORDER_NO'] . '"');
                    $this->oDb->execute('UPDATE novalnet_transaction_detail SET GATEWAY_STATUS = "' . $this->aCaptureParams['tid_status'] . '" WHERE ORDER_NO ="' . $this->aOrderDetails['ORDER_NO'] . '"');
                    $this->_sendMail($sNovalnetComments);

                    $this->_displayMessage($sNovalnetComments, $this->aOrderDetails['ORDER_NO']);
                } else {
                    $this->_displayMessage('Novalnet Callback script received. Order already Paid', $this->aOrderDetails['ORDER_NO']);
                }
            } elseif ($this->aCaptureParams['payment_type']=='PRZELEWY24' && !in_array($this->aCaptureParams['tid_status'], array('86', '100'))) {
                    $sNovalnetComments = 'The transaction has been canceled due to: ' . $this->oNovalnetUtil->setNovalnetPaygateError($this->aCaptureParams);
                    $sComments = $sLineBreak . $sNovalnetComments;
                    $this->oDb->execute('UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sComments . '") WHERE OXORDERNR ="' . $this->aOrderDetails['ORDER_NO'] . '"');

                    $this->oDb->execute('UPDATE novalnet_transaction_detail SET GATEWAY_STATUS = "' . $this->aCaptureParams['tid_status'] . '" WHERE ORDER_NO ="' . $this->aOrderDetails['ORDER_NO'] . '"');
                    $this->_sendMail($sNovalnetComments);

                    $this->_displayMessage($sNovalnetComments,  $this->aOrderDetails['ORDER_NO']);
            } elseif (in_array($this->aCaptureParams['payment_type'], array('CREDIT_CARD', 'INVOICE_START', 'GUARANTEED_INVOICE', 'GUARANTEED_DIRECT_DEBIT_SEPA', 'DIRECT_DEBIT_SEPA')) && in_array($this->aOrderDetails['GATEWAY_STATUS'], ['75','91','98','99']) && in_array($this->aCaptureParams['tid_status'], ['91', '99', '100'])) {
                     $sNovalnetComments = '';
                     $sMessage = '';
                    if (in_array($this->aOrderDetails['GATEWAY_STATUS'], array('75', '91', '98', '99')) && $this->aCaptureParams['tid_status'] == 100) {
                        $sNovalnetComments .= '<br>Novalnet callback received. The transaction has been confirmed on '. date('Y-m-d H:i:s');
                    } elseif ($this->aOrderDetails['GATEWAY_STATUS'] == 75 && in_array($this->aCaptureParams['tid_status'], array('91', '99'))) {
                        $sNovalnetComments .= ($this->aCaptureParams['payment_type'] == 'GUARANTEED_DIRECT_DEBIT_SEPA') ? '<br>' : '';
                        $sNovalnetComments .= 'Novalnet callback received. The transaction status has been changed from pending to on hold for the TID:'. $this->aCaptureParams['shop_tid']. ' on '. date('Y-m-d H:i:s').'<br>';
                        $sNovalnetComments .= ($this->aCaptureParams['payment_type'] == 'GUARANTEED_INVOICE') ? '<br>' : '';
                    }
					
					if ($this->aCaptureParams['tid_status'] == 100) {
						if ($this->aOrderDetails['OXPAYMENTTYPE'] == 'novalnetinvoice') {
							$aInvoiceDetails = '<br>';
							$aInvoiceDetails .= $this->_getTransactionComments($this->aOrderDetails['OXLANG']);

							$aInvoiceDetails .= $this->_getReferenceTransaction($this->aOrderDetails['OXLANG'], $this->aOrderDetails['ORDER_NO']);
							$sMessage .= $aInvoiceDetails;

							$sSQL = 'UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sMessage . '") WHERE OXORDERNR = "' . $this->aOrderDetails['ORDER_NO'] . '"';
							$this->oDb->execute($sSQL);

							$this->oNovalnetUtil->sendPaymentNotificationMail($this->aOrderDetails['OXLANG'], $sMessage, $this->aOrderDetails['ORDER_NO']);

						}

						if ($this->aOrderDetails['OXPAYMENTTYPE'] == 'novalnetsepa' && $this->aCaptureParams['payment_type'] == 'GUARANTEED_DIRECT_DEBIT_SEPA') {
							$sMessage = '<br>' . $this->_getTransactionComments($this->aOrderDetails['OXLANG']);

							$this->oDb->execute('UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sMessage . '") WHERE OXORDERNR ="' . $this->aOrderDetails['ORDER_NO'] . '"');
						}
						
						$dDate = ($this->aCaptureParams['payment_type'] == 'INVOICE_START') ? '' : date('Y-m-d H:i:s');
						$this->oDb->execute('UPDATE oxorder SET OXPAID = "' . $dDate . '" WHERE OXORDERNR ="' . $this->aOrderDetails['ORDER_NO'] . '"');
					}
                    
                    $this->oDb->execute('UPDATE novalnet_transaction_detail SET GATEWAY_STATUS =  "' . $this->aCaptureParams['tid_status'] . '" WHERE ORDER_NO ="' . $this->aOrderDetails['ORDER_NO'] . '"');

                    $sComments = '<br>' . $sNovalnetComments;

                    $this->oDb->execute('UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sComments . '") WHERE OXORDERNR ="' . $this->aOrderDetails['ORDER_NO'] . '"');

                    $this->_sendMail($sNovalnetComments);
                    $this->_displayMessage($sNovalnetComments, $this->aOrderDetails['ORDER_NO']);
            }
            elseif ($this->aCaptureParams['status'] != '100' || !in_array($this->aCaptureParams['tid_status'], array('100', '85', '86', '90', '91', '98', '99'))) {
                $this->_displayMessage('Novalnet callback received. Status is not valid');
            } else {
                $this->_displayMessage('Novalnet Callback script received. Payment type ( ' . $this->aCaptureParams['payment_type'] . ' ) is not applicable for this process!');
            }
        } elseif ($iPaymentTypeLevel == 1 && $sPaymentSuccess) {
            $sNovalnetComments = 'Novalnet callback received. Chargeback executed successfully for the TID: ' . $this->aCaptureParams['tid_payment'] . ' amount ' . $dFormattedAmount . ' on ' . date('Y-m-d H:i:s') . '. The subsequent TID: ' . $this->aCaptureParams['tid'];

            if (in_array($this->aCaptureParams['payment_type'], array('CREDITCARD_BOOKBACK', 'PAYPAL_BOOKBACK', 'PRZELEWY24_REFUND', 'REFUND_BY_BANK_TRANSFER_EU', 'GUARANTEED_INVOICE_BOOKBACK', 'GUARANTEED_SEPA_BOOKBACK'))) {
                $sNovalnetComments = 'Novalnet callback received. Refund/Bookback executed successfully for the TID: ' . $this->aCaptureParams['tid_payment'] . ' amount ' . $dFormattedAmount . ' on ' . date('Y-m-d H:i:s') . '. The subsequent TID: ' . $this->aCaptureParams['tid'];
            }
            $sComments = $sLineBreak . $sNovalnetComments;
            $sUpdateSql = 'UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sComments . '") WHERE OXORDERNR ="' . $this->aOrderDetails['ORDER_NO'] . '"';
            $this->oDb->execute($sUpdateSql);

            $this->_sendMail($sNovalnetComments);

            $this->_displayMessage($sNovalnetComments, $this->aOrderDetails['ORDER_NO']);
        } elseif ($iPaymentTypeLevel == 2 && $sPaymentSuccess) {
             if (in_array($this->aCaptureParams['payment_type'], array('INVOICE_CREDIT', 'CASHPAYMENT_CREDIT'))) {
                if (!isset($dPaidAmount) || $dPaidAmount < $dAmount) {
                    $dTotalAmount             = $dPaidAmount + $this->aCaptureParams['amount'];
                    $sNovalnetCallbackSql     = 'INSERT INTO novalnet_callback_history (PAYMENT_TYPE, STATUS, ORDER_NO, AMOUNT, CURRENCY, CALLBACK_TID, ORG_TID, PRODUCT_ID, CALLBACK_DATE) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )';
                    $aNovalnetCallbackDetails = array( $this->aCaptureParams['payment_type'], $this->aCaptureParams['status'], $this->aOrderDetails['ORDER_NO'], $this->aCaptureParams['amount'], $this->aCaptureParams['currency'], $this->aCaptureParams['tid'], $this->aCaptureParams['tid_payment'], $this->aCaptureParams['product_id'], date('Y-m-d H:i:s') );
                    $this->oDb->execute($sNovalnetCallbackSql, $aNovalnetCallbackDetails);

                    $sNovalnetComments = 'Novalnet Callback Script executed successfully for the TID: ' . $this->aCaptureParams['tid_payment'] . ' with amount ' . $dFormattedAmount . ' on ' . date('Y-m-d H:i:s') . '. Please refer PAID transaction in our Novalnet Merchant Administration with the TID: ' . $this->aCaptureParams['tid'];
                    $sComments =  $sLineBreak . $sNovalnetComments;
                    $sUpdateSql = 'UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sComments . '") WHERE OXORDERNR ="' . $this->aOrderDetails['ORDER_NO'] . '"';

                    if ($dAmount <= $dTotalAmount)
                        $sUpdateSql = 'UPDATE oxorder SET OXPAID = "' . date('Y-m-d H:i:s') . '", NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sComments . '") WHERE OXORDERNR ="' . $this->aOrderDetails['ORDER_NO'] . '"';

                    $this->oDb->execute($sUpdateSql);

                    $this->_sendMail($sNovalnetComments);

                    $this->_displayMessage($sNovalnetComments, $this->aOrderDetails['ORDER_NO']);
                } else {
                    $this->_displayMessage('Novalnet Callback script received. Order already Paid');
                }
            } else {
                $sNovalnetComments = 'Novalnet Callback Script executed successfully for the TID: ' . $this->aCaptureParams['tid_payment'] . ' with amount ' . $dFormattedAmount . ' on ' . date('Y-m-d H:i:s') . '. Please refer PAID transaction in our Novalnet Merchant Administration with the TID: ' . $this->aCaptureParams['tid'];

                $sComments =  $sLineBreak . $sNovalnetComments;

                $sUpdateSql = 'UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sComments . '") WHERE OXORDERNR ="' . $this->aOrderDetails['ORDER_NO'] . '"';
                $this->oDb->execute($sUpdateSql);
                $this->_sendMail($sNovalnetComments);

                $this->_displayMessage($sNovalnetComments, $this->aOrderDetails['ORDER_NO']);
         }
        } elseif ($this->aCaptureParams['status'] != '100' || $this->aCaptureParams['tid_status'] != '100') {
            $this->_displayMessage('Novalnet callback received. Status is not valid');
        } else {
            $this->_displayMessage('Novalnet callback script executed already');
        }
    }

    /**
     * Gets payment level of the callback request
     *
     * @return integer
     */
    private function _getPaymentTypeLevel()
    {
        if (in_array($this->aCaptureParams['payment_type'], $this->aPayments))
            return 0;
        elseif (in_array($this->aCaptureParams['payment_type'], $this->aChargebacks))
            return 1;
        elseif (in_array($this->aCaptureParams['payment_type'], $this->aCollections))
            return 2;
    }

    /**
     * Gets order details from the shop for the callback request
     *
     * @return boolean
     */
    private function _getOrderDetails()
    {
        if (!empty($this->aCaptureParams['order_no']) && !empty($this->aCaptureParams['status']) && in_array($this->aCaptureParams['status'], array('100', '90'))) {
			$sQuery = "SELECT OXPAYMENTTYPE from oxorder where OXORDERNR = '".$this->aCaptureParams['order_no']."'";
			$sRow = $this->oDb->getRow($sQuery);
			if (empty($sRow['OXPAYMENTTYPE']) || strpos($sRow['OXPAYMENTTYPE'], 'novalnet') === false) {

				list($sSubject, $sMessage) = $this->_buildNotificationMessage();

                // Send E-mail, if transaction not found
				$this->_sendNotifyMail($sSubject, $sMessage);

				$this->_displayMessage($sMessage);

                return false;
			}
		}

        // Handle the change payment method
        if ($this->_handleChangePaymentMethod()) {
            return false;
        }

        $iOrderNo = !empty($this->aCaptureParams['order_no']) ? $this->aCaptureParams['order_no'] : (!empty($this->aCaptureParams['order_id']) ? $this->aCaptureParams['order_id'] : '');
        $sSql     = 'SELECT trans.ORDER_NO, trans.TOTAL_AMOUNT, trans.NNBASKET, trans.REFUND_AMOUNT, trans.PAYMENT_ID, trans.GATEWAY_STATUS, o.OXLANG, o.OXPAYMENTTYPE FROM novalnet_transaction_detail trans JOIN oxorder o ON o.OXORDERNR = trans.ORDER_NO where trans.tid = "' . $this->aCaptureParams['shop_tid'] . '"';

        $this->aOrderDetails = $this->oDb->getRow($sSql);

        // Handle transaction cancellation
        if ($this->_transactionCancellation()) {
            return false;
        }

        // checks the payment type of callback and order
        if (empty($this->aOrderDetails['OXPAYMENTTYPE']) || !in_array($this->aCaptureParams['payment_type'], $this->aPaymentGroups[$this->aOrderDetails['OXPAYMENTTYPE']])) {
            $this->_displayMessage('Novalnet callback received. Payment Type [' . $this->aCaptureParams['payment_type'] . '] is not valid');
            return false;
        }

        // checks the order number in shop
        if (empty($this->aOrderDetails['ORDER_NO'])) {

            list($sSubject, $sMessage) = $this->_buildNotificationMessage();

            // Send E-mail, if transaction not found
            $this->_sendNotifyMail($sSubject, $sMessage);

            $this->_displayMessage($sMessage);

            return false;
        }

        // checks order number of callback and shop only when the callback having the order number
        if (!empty($iOrderNo) && $iOrderNo != $this->aOrderDetails['ORDER_NO']) {
            $this->_displayMessage('Novalnet callback received. Order Number is not valid');
            return false;
        }
        return true;
    }


    /**
     * Get Invoice Reference transaction details
     *
     * @param string  $sLang
     * @param integer $iOrderNo
     *
     * @return array
     */
    private function _getReferenceTransaction($sLang, $iOrderNo)
    {
        $oConfig = oxRegistry::getConfig();
        $this->_oLang->setBaseLanguage($sLang);
        $sSQL            = 'SELECT ACCOUNT_HOLDER as invoice_account_holder, BANK_IBAN AS invoice_iban, BANK_BIC AS invoice_bic, BANK_NAME AS invoice_bankname, BANK_CITY AS invoice_bankplace, TID AS tid,AMOUNT as amount, ORDER_NO as order_no, INVOICE_REF AS invoice_ref, PAYMENT_REF AS payment_ref FROM  novalnet_preinvoice_transaction_detail WHERE ORDER_NO = "' . $iOrderNo . '"';
        $aInvoiceDetails = $this->oDb->getRow($sSQL);
        $sFormattedAmount = $this->_oLang->formatCurrency($aInvoiceDetails['amount']/100, $oConfig->getCurrencyObject($this->aCaptureParams['currency'])) . ' ' . $this->aCaptureParams['currency'];
        $sInvoiceComments = $this->_oLang->translateString('NOVALNET_INVOICE_COMMENTS_TITLE');
        if (!empty($this->aCaptureParams['due_date'])) {
            $sInvoiceComments .= $this->_oLang->translateString('NOVALNET_DUE_DATE') . $this->aCaptureParams['due_date'];

            $this->oDb->execute('UPDATE novalnet_preinvoice_transaction_detail SET DUE_DATE =  "' . $this->aCaptureParams['due_date'] . '" WHERE ORDER_NO ="' . $iOrderNo . '"');
		}
        $sInvoiceComments .= $this->_oLang->translateString('NOVALNET_ACCOUNT') . $aInvoiceDetails['invoice_account_holder'];
        $sInvoiceComments .= '<br>IBAN: ' . $aInvoiceDetails['invoice_iban'];
        $sInvoiceComments .= '<br>BIC: '  . $aInvoiceDetails['invoice_bic'];
        $sInvoiceComments .= '<br>Bank: ' . $aInvoiceDetails['invoice_bankname'] . ' ' . $aInvoiceDetails['invoice_bankplace'];
        $sInvoiceComments .= $this->_oLang->translateString('NOVALNET_AMOUNT') . $sFormattedAmount;

        if (!empty($aInvoiceDetails['payment_ref'])) {
            $aInvoiceDetails = array_merge($aInvoiceDetails, unserialize($aInvoiceDetails['payment_ref']));
        } else {
            $sPaymentType = $this->aOrderDetails['OXPAYMENTTYPE'];
            $aInvPreReference = array( 'payment_ref1' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefOne' . $sPaymentType),
                                   'payment_ref2' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefTwo' . $sPaymentType),
                                   'payment_ref3' => $this->oNovalnetUtil->getNovalnetConfigValue('blRefThree' . $sPaymentType)
                                  );
            $aInvoiceDetails = array_merge($aInvoiceDetails, $aInvPreReference);
            $sInvPreReference = serialize($aInvPreReference);
            $this->oDb->execute('UPDATE novalnet_preinvoice_transaction_detail SET PAYMENT_REF = "'.$sInvPreReference.'" WHERE ORDER_NO = "' . $iOrderNo . '"');
        }
        $iReferences[1] = isset($aInvoiceDetails['payment_ref1']) ? $aInvoiceDetails['payment_ref1'] : '';
        $iReferences[2] = isset($aInvoiceDetails['payment_ref2']) ? $aInvoiceDetails['payment_ref2'] : '';
        $iReferences[3] = isset($aInvoiceDetails['payment_ref3']) ? $aInvoiceDetails['payment_ref3'] : '';
        $i = 1;
        $aCountReferenece = array_count_values($iReferences);

        $sInvoiceComments .= (($aCountReferenece['1'] > 1) ? $this->_oLang->translateString('NOVALNET_INVOICE_MULTI_REF_DESCRIPTION') : $this->_oLang->translateString('NOVALNET_INVOICE_SINGLE_REF_DESCRIPTION'));
        foreach ($iReferences as $iKey => $blValue) {
            if ($iReferences[$iKey] == 1) {
                $sInvoiceComments .= ($aCountReferenece['1'] == 1) ? $this->_oLang->translateString('NOVALNET_INVOICE_SINGLE_REFERENCE') : sprintf($this->_oLang->translateString('NOVALNET_INVOICE_MULTI_REFERENCE'), $i++);

                $sInvoiceComments .= ($iKey == 1) ? $aInvoiceDetails['invoice_ref'] : ($iKey == 2 ? 'TID '. $this->aCaptureParams['shop_tid'] : $this->_oLang->translateString('NOVALNET_ORDER_NO') . $iOrderNo);
            }
        }

        return $sInvoiceComments;
    }

    /**
     * Displays the message
     *
     * @param string  $sMessage
     * @param integer $iOrderNo
     *
     */
    private function _displayMessage($sMessage, $iOrderNo = '')
    {
        $this->_aViewData['sNovalnetMessage'] = !empty($iOrderNo) ? 'message='. $sMessage.'&order_no='.$iOrderNo : 'message='.$sMessage;
    }

    /**
     * Handling the subscription cancellation & reactivation process
     *
     * @return boolean
     *
     */
    private function _subscriptionProcess()
    {
        if ($this->aCaptureParams['payment_type'] == 'SUBSCRIPTION_STOP') {

            $this->_subscriptionCancel();
            return true;

        } elseif ($this->aCaptureParams['payment_type'] == 'SUBSCRIPTION_REACTIVATE') {
             $sNovalnetComments = '<br>Novalnet callback script received. Subscription has been reactivated for the TID: ' . $this->aCaptureParams['signup_tid'] . ' on ' . date('Y-m-d H:i:s');

             $this->oDb->execute('UPDATE novalnet_subscription_detail SET TERMINATION_REASON = "", TERMINATION_AT = "" WHERE TID = "' . $this->aCaptureParams['shop_tid'] . '"');

             $this->oDb->execute('UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sNovalnetComments . '") WHERE OXORDERNR ="' . $this->aOrderDetails['ORDER_NO'] . '"');

             $this->_sendMail($sNovalnetComments);

             $this->_displayMessage($sNovalnetComments);
             return true;
        }
        return false;
    }

    /**
     * Handling the subscription cancellation process
     *
     * @return none
     *
     */
    private function _subscriptionCancel()
    {
        $sTerminationReason = !empty($this->aCaptureParams['status_message']) ? $this->aCaptureParams['status_message'] : $this->aCaptureParams['termination_reason'];

        $sUpdateSql = 'UPDATE novalnet_subscription_detail SET TERMINATION_REASON = "' . $sTerminationReason . '", TERMINATION_AT = "' . date('Y-m-d H:i:s') . '" WHERE TID = "' . $this->aCaptureParams['shop_tid'] . '"';
        $this->oDb->execute($sUpdateSql);

        $sNovalnetComments = 'Novalnet callback script received. Subscription has been stopped for the TID: ' . $this->aCaptureParams['shop_tid'] . ' on ' . date('Y-m-d H:i:s') . '.<br>Reason for Cancellation: ' . $sTerminationReason;
        $sComments = '<br><br>'. $sNovalnetComments;

        $sSQL = 'SELECT SUBS_ID FROM novalnet_subscription_detail WHERE TID='.$this->aCaptureParams['shop_tid'];
        $aSubId = $this->oDb->getRow($sSQL);

        $sSQL = 'SELECT ORDER_NO from novalnet_subscription_detail WHERE SUBS_ID = "'.$aSubId['SUBS_ID'].'"';

        $aOrderNr = $this->oDb->getAll($sSQL);
        foreach($aOrderNr as $skey => $dValue) {
            $sUpdateSql = 'UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sComments . '") WHERE OXORDERNR ="' . $dValue['ORDER_NO'] . '"';
            $this->oDb->execute($sUpdateSql);
        }

        $this->_sendMail($sNovalnetComments);

        $this->_displayMessage($sNovalnetComments);

    }

    /**
     * Handle TRANSACTION_CANCELLATION payment type
     *
     * @return boolean
     *
     */
    private function _transactionCancellation()
    {
         if ($this->aCaptureParams['payment_type'] == 'TRANSACTION_CANCELLATION' && in_array($this->aOrderDetails['GATEWAY_STATUS'], array('75','91','99'))) {
           $sNovalnetComments = 'Novalnet callback received. The transaction has been canceled on '.date('Y-m-d H:i:s');

            $sUpdateSql = 'UPDATE novalnet_transaction_detail SET GATEWAY_STATUS =  "' . $this->aCaptureParams['tid_status'] . '" WHERE ORDER_NO ="' . $this->aOrderDetails['ORDER_NO'] . '"';
            $this->oDb->execute($sUpdateSql);

            $sComments = '<br><br>'.$sNovalnetComments;

            $this->oDb->execute('UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sComments . '") WHERE OXORDERNR ="' . $this->aOrderDetails['ORDER_NO'] . '"');

            $this->_sendMail($sNovalnetComments);

            $this->_displayMessage($sNovalnetComments);

            return true;
        }

        return false;
    }

     /**
     * Build the Notification Message
     *
     * @return array
     */
    private function _buildNotificationMessage()
    {
        $oConfig = oxRegistry::getConfig();
        $sShopName = $oConfig->getActiveShop()->oxshops__oxname->rawValue;
        $sSubject = 'Critical error on shop system '.$sShopName.' : order not found for TID: ' . $this->aCaptureParams['shop_tid'];
        $sMessage = "Dear Technic team,<br/><br/>Please evaluate this transaction and contact our payment module team at Novalnet.<br/><br/>";
        $sMessage .= 'Merchant ID: ' . $this->aCaptureParams['vendor_id'] . '<br/>';
        $sMessage .= 'Project ID: ' . $this->aCaptureParams['product_id'] . '<br/>';
        $sMessage .= 'TID: ' . $this->aCaptureParams['shop_tid'] . '<br/>';
        $sMessage .= 'TID status: ' . $this->aCaptureParams['tid_status'] . '<br/>';
        $sMessage .= 'Order no: ' . $this->aCaptureParams['order_no'] . '<br/>';
        $sMessage .= 'Payment type: ' . $this->aCaptureParams['payment_type'] . '<br/>';
        $sMessage .= 'E-mail: ' . $this->aCaptureParams['email'] . '<br/>';

        $sMessage .= '<br/><br/>Regards,<br/>Novalnet Team';

        return array($sSubject, $sMessage);
    }

    /*
     * Send mail notification
     *
     * @param string $sSubject
     * @param string $sMessage
     */
    private function _sendNotifyMail($sSubject, $sMessage)
    {
        $oMail   = oxNew('oxEmail');
        $oMail->setRecipient($this->technicNotifyMail);
        $oMail->setSubject( $sSubject );
        $oMail->setBody( $sMessage );
        $oMail->send();
    }

    /**
     * Sends messages as mail
     *
     * @param string $sMessage
     *
     * @return string
     */
    private function _sendMail($sMessage)
    {
        $oConfig = oxRegistry::getConfig();
        $blCallbackMail = $this->oNovalnetUtil->getNovalnetConfigValue('blCallbackMail');
        if (!empty($blCallbackMail)) {
            $oMail         = oxNew('oxEmail');
            $oUtils        = oxNew('oxUtils');
            $sToAddress    = $this->oNovalnetUtil->getNovalnetConfigValue('sCallbackMailToAddr');
            $sBccAddress   = $this->oNovalnetUtil->getNovalnetConfigValue('sCallbackMailBccAddr');
            $sEmailSubject = 'Novalnet Callback Script Access Report - '. $oConfig->getActiveShop()->oxshops__oxname->rawValue;
            $blValidTo     = false;
            // validates 'to' addresses
            if (!empty($sToAddress)) {
                $aToAddress = explode( ',', $sToAddress );
                foreach ($aToAddress as $sMailAddress) {
                    $sMailAddress = trim($sMailAddress);
                    if (oxNew('oxMailValidator')->isValidEmail($sMailAddress)) {
                        $oMail->setRecipient($sMailAddress);
                        $blValidTo = true;
                    }
                }
            }
            if (!$blValidTo)
                return 'Mail not sent<br>';

            // validates 'bcc' addresses
            if (!empty($sBccAddress)) {
                $aBccAddress = explode( ',', $sBccAddress );
                foreach ($aBccAddress as $sMailAddress) {
                    $sMailAddress = trim($sMailAddress);
                    if (oxNew('oxMailValidator')->isValidEmail($sMailAddress))
                        $oMail->AddBCC($sMailAddress);
                }
            }

            $oShop = $oMail->getShop();
            $oMail->setFrom($oShop->oxshops__oxorderemail->value);
            $oMail->setSubject( $sEmailSubject );
            $oMail->setBody( $sMessage );

            if ($oMail->send())
                return 'Mail sent successfully<br>';

        } else {
            return 'Mail not sent<br>';
        }

        return 'Mail not sent<br>';
    }

    /**
     * Handle the change payment method
     *
     * @return boolean
     *
     */
    private function _handleChangePaymentMethod()
    {
        if (isset($this->aCaptureParams['subs_id']) &&  !empty($this->aCaptureParams['signup_tid']) && isset($this->aCaptureParams['subs_billing']) && $this->aCaptureParams['subs_billing'] != 1) {
            if (empty($this->aCaptureParams['amount']) || $this->aCaptureParams['amount'] == 0) {
                $sSql     = 'SELECT ORDER_NO FROM novalnet_subscription_detail where TID = "' . $this->aCaptureParams['shop_tid'] . '"';
                $this->aRecurringOrderNo = $this->oDb->getRow($sSql);
                $aPaymentList = array('INVOICE_START' => 'novalnetinvoice', 'CREDITCARD' => 'novalnetcreditcard',
                        'DIRECT_DEBIT_SEPA' => 'novalnetsepa', 'GUARANTEED_DIRECT_DEBIT_SEPA' => 'novalnetsepa',
                        'PAYPAL' => 'novalnetpaypal', 'GUARANTEED_INVOICE' => 'novalnetinvoice');

                 $sSql     = 'SELECT PAYMENT_ID, PAYMENT_TYPE FROM novalnet_transaction_detail where ORDER_NO = "' . $this->aRecurringOrderNo['ORDER_NO'] . '"';

                 $aPaymentDetails = $this->oDb->getRow($sSql);

                 $snewPaymentCode = $aPaymentList[$this->aCaptureParams['payment_type']];

                $sNovalnetComments = '<br><br>Novalnet callback script received. Subscription has been changed from ' .$aPaymentDetails['PAYMENT_TYPE'] . ' to ' . $this->aCaptureParams['payment_type'] . ' on '. date('Y-m-d H:i:s');

                $this->oDb->execute('UPDATE novalnet_transaction_detail SET PAYMENT_ID = "' . $this->aCaptureParams['key'] . '", PAYMENT_TYPE = "' . $this->aCaptureParams['payment_type'] . '"  WHERE ORDER_NO ="' . $this->aRecurringOrderNo['ORDER_NO'] . '"');

                $this->oDb->execute('UPDATE oxorder SET OXPAYMENTTYPE = "'. $snewPaymentCode .'", NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sNovalnetComments . '") WHERE OXORDERNR ="' . $this->aRecurringOrderNo['ORDER_NO'] . '"');

                $this->_sendMail($sNovalnetComments);

                $this->_displayMessage($sNovalnetComments, $this->aRecurringOrderNo['ORDER_NO']);

                return true;
            }
        }

        return false;
    }

    /**
     * Create the new subscription order
     *
     * @return none
     *
     */
    private function _createFollowupOrder()
    {
        $oOrderNr = $this->aOrderDetails['ORDER_NO'];
        $aTxndetails = $this->aOrderDetails;

        // Get oxorder details
        $aOrderDetails = $this->oDb->getRow('SELECT * FROM oxorder where OXORDERNR = "' . $oOrderNr. '"');

        // Get User details
        $aUserdetails = $this->oDb->getRow('SELECT * FROM oxuser where OXID = "'.$aOrderDetails['OXUSERID'].'"');

        // Get oxorderarticles details
        $aOxorderarticles = $this->oDb->getAll('SELECT * FROM oxorderarticles where OXORDERID = "' . $aOrderDetails['OXID']. '"');

        // Load Order number
        $iCnt = oxNew( 'oxCounter' )->getNext( 'oxOrder' );

        $this->iRecurringOrder = $iCnt;

        $iNextSubsCycle = !empty($this->aCaptureParams['next_subs_cycle']) ? $this->aCaptureParams['next_subs_cycle'] : (!empty($this->aCaptureParams['paid_until']) ? $this->aCaptureParams['paid_until'] : '');

        $sOrderComments =  $this->_oLang->translateString('NOVALNET_TRANSACTION_DETAILS');
        if (in_array($this->aOrderDetails['PAYMENT_ID'], ['41','40'])) {
             $sOrderComments .= $this->_oLang->translateString('NOVALNET_PAYMENT_GUARANTEE_COMMENTS');
         }

        if (!in_array($aTxndetails['OXPAYMENTTYPE'], array('novalnetinvoice','novalnetprepayment'))) {
            $sOrderComments .= $this->_oLang->translateString('NOVALNET_TRANSACTION_ID') . $this->aCaptureParams['tid'];
            $sOrderComments .= !empty($this->aCaptureParams['test_mode']) ? $this->_oLang->translateString('NOVALNET_TEST_ORDER') : '';
        }

        $sOrderComments .= (in_array($aTxndetails['OXPAYMENTTYPE'], array('novalnetinvoice','novalnetprepayment'))) ? $this->_getBankdetails($aOrderDetails['OXLANG']).'<br><br>' : '';

        $sOrderComments .= $this->_oLang->translateString('NOVALNET_REFERENCE_ORDER_NUMBER'). $oOrderNr.'<br>';
        $sOrderComments .= $this->_oLang->translateString('NOVALNET_NEXT_CHARGING_DATE'). $iNextSubsCycle;

        $this->_insertOxorderTable($aUserdetails, $aOrderDetails, $sOrderComments, $iCnt);

        foreach($aOxorderarticles as $key => $aOxorderArticle) {
           $sUId = $this->oSession->getVariable('sOxid');
           $this->_insertOxorderArticlesTable($sUId, $aOxorderArticle);
           $this->getOxAmount($aOrderDetails['OXID']);
        }

        $this->_insertNovalnetTranTable($oOrderNr, $iCnt);
        $this->_insertNovalnetSubDetailsTable($oOrderNr, $iCnt);
        $this->_insertNovalnetCallbackTable($oOrderNr, $iCnt);
        if (in_array($aTxndetails['OXPAYMENTTYPE'], array('novalnetinvoice','novalnetprepayment'))) {
            $this->_insertNovalnetPreInvTable($oOrderNr, $iCnt);
        }

        $this->_sendOrderByEmail($sUId, $aTxndetails['NNBASKET']);

        $this->oSession->deleteVariable('sOxid');
    }

    /**
     * Insert the new order details on Oxorder table
     *
     * @param array $aUserdetails
     * @param array $aOrderDetails
     * @param string $sOrderComments
     * @param double $iCnt
     *
     */
     protected function _insertOxorderTable($aUserdetails, $aOrderDetails, $sOrderComments, $iCnt)
     {
         $aOrder['OXID'] = oxUtilsObject::getInstance()->generateUId();
         $this->oSession->setVariable('sOxid', $aOrder['OXID']);
         $oOrder = oxNew( "oxorder" );
         $oOrder->setId($aOrder['OXID']);
         $iInsertTime = time();
         $now = date('Y-m-d H:i:s', $iInsertTime);
         if ($this->aCaptureParams['payment_type'] == 'PAYPAL') {
              $sNovalnetPaidDate = $this->aCaptureParams['status'] == '100' && in_array($this->aCaptureParams['tid_status'], array('100','90')) ? $now : '0000-00-00 00:00:00';
          } else {
              $sNovalnetPaidDate = ($this->aCaptureParams['payment_type'] == 'INVOICE_START' && $this->aCaptureParams['status'] == '100' && in_array($this->aCaptureParams['tid_status'], array('100','91'))) ? '0000-00-00 00:00:00' : $now;
          }
         $oOrder->oxorder__oxshopid          = new oxField($aOrderDetails['OXSHOPID'], oxField::T_RAW);
         $oOrder->oxorder__oxuserid          = new oxField($aOrderDetails['OXUSERID'], oxField::T_RAW);
         $oOrder->oxorder__oxorderdate       = new oxField($now, oxField::T_RAW);
         $oOrder->oxorder__oxordernr         = new oxField($iCnt, oxField::T_RAW);
         $oOrder->oxorder__oxbillcompany     = new oxField($aOrderDetails['OXBILLCOMPANY'], oxField::T_RAW);
         $oOrder->oxorder__oxbillemail       = new oxField($aUserdetails['OXUSERNAME'], oxField::T_RAW);
         $oOrder->oxorder__oxbillfname       = new oxField($aUserdetails['OXFNAME'], oxField::T_RAW);
         $oOrder->oxorder__oxbilllname       = new oxField($aUserdetails['OXLNAME'], oxField::T_RAW);
         $oOrder->oxorder__oxbillstreet      = new oxField($aUserdetails['OXSTREET'], oxField::T_RAW);
         $oOrder->oxorder__oxbillstreetnr    = new oxField($aUserdetails['OXSTREETNR'], oxField::T_RAW);
         $oOrder->oxorder__oxbilladdinfo     = new oxField($aUserdetails['OXADDINFO'], oxField::T_RAW);
         $oOrder->oxorder__oxbillustid       = new oxField($aUserdetails['OXUSTID'], oxField::T_RAW);
         $oOrder->oxorder__oxbillcity        = new oxField($aOrderDetails['OXBILLCITY'], oxField::T_RAW);
         $oOrder->oxorder__oxbillcountryid   = new oxField($aOrderDetails['OXBILLCOUNTRYID'], oxField::T_RAW);
         $oOrder->oxorder__oxbillstateid     = new oxField($aOrderDetails['OXBILLSTATEID'], oxField::T_RAW);
         $oOrder->oxorder__oxbillzip         = new oxField($aUserdetails['OXZIP'], oxField::T_RAW);
         $oOrder->oxorder__oxbillfon         = new oxField($aOrderDetails['OXBILLFON'], oxField::T_RAW);
         $oOrder->oxorder__oxbillfax         = new oxField($aOrderDetails['OXBILLFAX'], oxField::T_RAW);
         $oOrder->oxorder__oxbillsal         = new oxField($aOrderDetails['OXBILLSAL'], oxField::T_RAW);
         $oOrder->oxorder__oxdelcompany      = new oxField($aOrderDetails['OXDELCOMPANY'], oxField::T_RAW);
         $oOrder->oxorder__oxdelfname        = new oxField($aOrderDetails['OXDELLNAME'], oxField::T_RAW);
         $oOrder->oxorder__oxdellname        = new oxField($aOrderDetails['OXDELCOMPANY'], oxField::T_RAW);
         $oOrder->oxorder__oxdelstreet       = new oxField($aOrderDetails['OXDELCOMPANY'], oxField::T_RAW);
         $oOrder->oxorder__oxdelstreetnr     = new oxField($aOrderDetails['OXDELSTREETNR'], oxField::T_RAW);
         $oOrder->oxorder__oxdeladdinfo      = new oxField($aOrderDetails['OXDELADDINFO'], oxField::T_RAW);
         $oOrder->oxorder__oxdelcity         = new oxField($aOrderDetails['OXDELCITY'], oxField::T_RAW);
         $oOrder->oxorder__oxdelcountryid    = new oxField($aOrderDetails['OXDELCOUNTRYID'], oxField::T_RAW);
         $oOrder->oxorder__oxdelstateid      = new oxField($aOrderDetails['OXDELSTATEID'], oxField::T_RAW);
         $oOrder->oxorder__oxdelzip          = new oxField($aOrderDetails['OXDELZIP'], oxField::T_RAW);
         $oOrder->oxorder__oxdelfon          = new oxField($aOrderDetails['OXDELFON'], oxField::T_RAW);
         $oOrder->oxorder__oxdelfax          = new oxField($aOrderDetails['OXDELFAX'], oxField::T_RAW);
         $oOrder->oxorder__oxdelsal          = new oxField($aOrderDetails['OXDELSAL'], oxField::T_RAW);
         $oOrder->oxorder__oxpaymentid       = new oxField($aOrderDetails['OXPAYMENTID'], oxField::T_RAW);
         $oOrder->oxorder__oxpaymenttype     = new oxField($aOrderDetails['OXPAYMENTTYPE'], oxField::T_RAW);
         $oOrder->oxorder__oxtotalnetsum     = new oxField($aOrderDetails['OXTOTALNETSUM'], oxField::T_RAW);
         $oOrder->oxorder__oxtotalbrutsum    = new oxField($aOrderDetails['OXTOTALBRUTSUM'], oxField::T_RAW);
         $oOrder->oxorder__oxtotalordersum   = new oxField($aOrderDetails['OXTOTALORDERSUM'], oxField::T_RAW);
         $oOrder->oxorder__oxartvat1         = new oxField($aOrderDetails['OXARTVAT1'], oxField::T_RAW );
         $oOrder->oxorder__oxartvatprice1    = new oxField($aOrderDetails['OXARTVATPRICE1'], oxField::T_RAW);
         $oOrder->oxorder__oxartvat2         = new oxField($aOrderDetails['OXARTVAT2'] , oxField::T_RAW);
         $oOrder->oxorder__oxartvatprice2    = new oxField($aOrderDetails['OXARTVATPRICE2'], oxField::T_RAW );
         $oOrder->oxorder__oxdelcost         = new oxField($aOrderDetails['OXDELCOST'], oxField::T_RAW);
         $oOrder->oxorder__oxdelvat          = new oxField($aOrderDetails['OXDELVAT'], oxField::T_RAW);
         $oOrder->oxorder__oxpaycost         = new oxField($aOrderDetails['OXPAYCOST'], oxField::T_RAW);
         $oOrder->oxorder__oxpayvat          = new oxField($aOrderDetails['OXPAYVAT'], oxField::T_RAW);
         $oOrder->oxorder__oxwrapcost        = new oxField($aOrderDetails['OXWRAPCOST'], oxField::T_RAW);
         $oOrder->oxorder__oxwrapvat         = new oxField($aOrderDetails['OXWRAPVAT'], oxField::T_RAW);
         $oOrder->oxorder__oxgiftcardcost    = new oxField($aOrderDetails['OXGIFTCARDCOST'], oxField::T_RAW);
         $oOrder->oxorder__oxgiftcardvat     = new oxField($aOrderDetails['OXGIFTCARDVAT'], oxField::T_RAW);
         $oOrder->oxorder__oxcardid          = new oxField($aOrderDetails['OXCARDID'], oxField::T_RAW);
         $oOrder->oxorder__oxcardtext        = new oxField($aOrderDetails['OXCARDTEXT']);
         $oOrder->oxorder__oxdiscount        = new oxField($aOrderDetails['OXDISCOUNT'], oxField::T_RAW);
         $oOrder->oxorder__oxexport          = new oxField($aOrderDetails['OXEXPORT'], oxField::T_RAW );
         $oOrder->oxorder__oxbillnr          = new oxField($aOrderDetails['OXBILLNR'], oxField::T_RAW );
         $oOrder->oxorder__oxbilldate        = new oxField($aOrderDetails['OXBILLDATE'], oxField::T_RAW );
         $oOrder->oxorder__oxtrackcode       = new oxField($aOrderDetails['OXTRACKCODE'], oxField::T_RAW );
         $oOrder->oxorder__oxsenddate        = new oxField($aOrderDetails['OXSENDDATE'], oxField::T_RAW);
         $oOrder->oxorder__oxremark          = new oxField($aOrderDetails['OXREMARK']);
         $oOrder->oxorder__oxvoucherdiscount = new oxField($aOrderDetails['OXVOUCHERDISCOUNT'], oxField::T_RAW);
         $oOrder->oxorder__oxcurrency        = new oxField($aOrderDetails['OXCURRENCY'], oxField::T_RAW);
         $oOrder->oxorder__oxcurrate         = new oxField($aOrderDetails['OXCURRATE'], oxField::T_RAW);
         $oOrder->oxorder__oxfolder          = new oxField($aOrderDetails['OXFOLDER'], oxField::T_RAW);
         $oOrder->oxorder__oxtransid         = new oxField($aOrderDetails['OXTRANSID'], oxField::T_RAW);
         $oOrder->oxorder__oxpayid           = new oxField($aOrderDetails['OXPAYID'], oxField::T_RAW);
         $oOrder->oxorder__oxxid             = new oxField($aOrderDetails['OXXID'], oxField::T_RAW);
         $oOrder->oxorder__oxpaid            = new oxField($sNovalnetPaidDate, oxField::T_RAW);
         $oOrder->oxorder__oxstorno          = new oxField($aOrderDetails['OXSTORNO'], oxField::T_RAW );
         $oOrder->oxorder__oxip              = new oxField($aOrderDetails['OXIP'], oxField::T_RAW);
         $oOrder->oxorder__oxtransstatus     = new oxField($aOrderDetails['OXTRANSSTATUS'], oxField::T_RAW);
         $oOrder->oxorder__oxlang            = new oxField($aOrderDetails['OXLANG'], oxField::T_RAW );
         $oOrder->oxorder__oxinvoicenr       = new oxField($aOrderDetails['OXINVOICENR'], oxField::T_RAW );
         $oOrder->oxorder__oxdeltype         = new oxField($aOrderDetails['OXDELTYPE'], oxField::T_RAW);
         $oOrder->oxorder__oxtsprotectid     = new oxField($aOrderDetails['OXTSPROTECTID'], oxField::T_RAW);
         $oOrder->oxorder__oxtsprotectcosts  = new oxField($aOrderDetails['OXTSPROTECTCOSTS'], oxField::T_RAW);
         $oOrder->oxorder__oxtimestamp       = new oxField($aOrderDetails['OXTIMESTAMP'], oxField::T_RAW);
         $oOrder->oxorder__oxisnettomode     = new oxField($aOrderDetails['OXISNETTOMODE'], oxField::T_RAW);
         $oOrder->oxorder__novalnetcomments  = new oxField($sOrderComments);
         $oOrder->save();
    }

    /**
     * Insert the new order articles details on OxorderArticles table
     *
     * @param mixed $sUId
     * @param array $aOxorderArticle
     *
     */
     protected function _insertOxorderArticlesTable($sUId, $aOxorderArticle)
     {
        $sUniqueid = oxUtilsObject::getInstance()->generateUId();
        $oOrderArticle = oxNew( 'oxorderArticle' );
        $oOrderArticle->oxorderarticles__oxoxid           = new oxField($sUniqueid, oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid        = new oxField($sUId, oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxamount         = new oxField($aOxorderArticle['OXAMOUNT'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxartid          = new oxField($aOxorderArticle['OXARTID'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxartnum         = new oxField($aOxorderArticle['OXARTNUM'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxtitle          = new oxField($aOxorderArticle['OXTITLE'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxshortdesc      = new oxField($aOxorderArticle['OXSHORTDESC'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxselvariant     = new oxField($aOxorderArticle['OXSELVARIANT'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxnetprice       = new oxField($aOxorderArticle['OXNETPRICE'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxbrutprice      = new oxField($aOxorderArticle['OXBRUTPRICE'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxvatprice       = new oxField($aOxorderArticle['OXVATPRICE'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxvat            = new oxField($aOxorderArticle['OXVAT'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxpersparam      = new oxField($aOxorderArticle['OXPERSPARAM']);
        $oOrderArticle->oxorderarticles__oxprice          = new oxField($aOxorderArticle['OXPRICE'], oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxbprice         = new oxField($aOxorderArticle['OXBPRICE'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxnprice         = new oxField($aOxorderArticle['OXNPRICE'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxwrapid         = new oxField($aOxorderArticle['OXWRAPID'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxexturl         = new oxField($aOxorderArticle['OXEXTURL'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxurldesc        = new oxField($aOxorderArticle['OXURLDESC'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxurlimg         = new oxField($aOxorderArticle['OXURLIMG'], oxField::T_RAW );
        $oOrderArticle->oxarticles__oxthumb               = new oxField($aOxorderArticle['OXTHUMB'], oxField::T_RAW );
        $oOrderArticle->oxarticles__oxpic1                = new oxField($aOxorderArticle['OXPIC1'], oxField::T_RAW );
        $oOrderArticle->oxarticles__oxpic2                = new oxField($aOxorderArticle['OXPIC2'], oxField::T_RAW );
        $oOrderArticle->oxarticles__oxpic3                = new oxField($aOxorderArticle['OXPIC3'], oxField::T_RAW );
        $oOrderArticle->oxarticles__oxpic4                = new oxField($aOxorderArticle['OXPIC4'], oxField::T_RAW );
        $oOrderArticle->oxarticles__oxpic5                = new oxField($aOxorderArticle['OXPIC5'], oxField::T_RAW );
        $oOrderArticle->oxarticles__oxweight              = new oxField($aOxorderArticle['OXWEIGHT'], oxField::T_RAW );
        $oOrderArticle->oxarticles__oxstock               = new oxField($aOxorderArticle['OXSTOCK'], oxField::T_RAW);
        $oOrderArticle->oxarticles__oxdelivery            = new oxField($aOxorderArticle['OXDELIVERY'], oxField::T_RAW);
        $oOrderArticle->oxarticles__oxinsert              = new oxField($aOxorderArticle['OXINSERT'], oxField::T_RAW);
        $iInsertTime = time();
        $now = date('Y-m-d H:i:s', $iInsertTime);
        $oOrderArticle->oxorderarticles__oxtimestamp      = new oxField( $now );
        $oOrderArticle->oxarticles__oxlength              = new oxField($aOxorderArticle['OXLENGTH'], oxField::T_RAW);
        $oOrderArticle->oxarticles__oxwidth               = new oxField($aOxorderArticle['OXWIDTH'], oxField::T_RAW);
        $oOrderArticle->oxarticles__oxheight              = new oxField($aOxorderArticle['OXHEIGHT'], oxField::T_RAW);
        $oOrderArticle->oxarticles__oxfile                = new oxField($aOxorderArticle['OXFILE'], oxField::T_RAW);
        $oOrderArticle->oxarticles__oxsearchkeys          = new oxField($aOxorderArticle['OXSEARCHKEYS'], oxField::T_RAW);
        $oOrderArticle->oxarticles__oxtemplate            = new oxField($aOxorderArticle['OXTEMPLATE'], oxField::T_RAW);
        $oOrderArticle->oxarticles__oxquestionemail       = new oxField($aOxorderArticle['OXQUESTIONEMAIL'], oxField::T_RAW);
        $oOrderArticle->oxarticles__oxissearch            = new oxField($aOxorderArticle['OXISSEARCH'], oxField::T_RAW);
        $oOrderArticle->oxarticles__oxfolder              = new oxField($aOxorderArticle['OXFOLDER'], oxField::T_RAW);
        $oOrderArticle->oxarticles__oxsubclass            = new oxField($aOxorderArticle['OXSUBCLASS'], oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxstorno         = new oxField($aOxorderArticle['OXSTORNO'], oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxordershopid    = new oxField($aOxorderArticle['OXORDERSHOPID'], oxField::T_RAW );
        $oOrderArticle->oxorderarticles__oxisbundle       = new oxField($aOxorderArticle['OXISBUNDLE'], oxField::T_RAW );
        $oOrderArticle->save();
    }

    /**
     * Get the Product Quantity and update the quantity in oxarticles table
     *
     * @param integer $oxAmount
     *
     */
    public function getOxAmount($oxAmount)
    {
        $this->oDb                = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $sSql = 'SELECT OXARTID, OXAMOUNT FROM oxorderarticles where OXORDERID = "' .  $oxAmount. '"';
        $dgetOxAmount           = $this->oDb->getRow($sSql);

        $sArtSql = 'SELECT OXSTOCK FROM oxarticles where OXID = "' .  $dgetOxAmount['OXARTID']. '"';
        $dgetArtCount = $this->oDb->getRow($sArtSql);
        $dProductId = $dgetArtCount['OXSTOCK'] - $dgetOxAmount['OXAMOUNT'];
        if ( $dProductId < 0) {
            $dProductId = 0;
        }
        // Stock updated in oxarticles table
        $sUpdateSql = 'UPDATE oxarticles SET OXSTOCK = "' . $dProductId . '" WHERE OXID ="' . $dgetOxAmount['OXARTID'] . '"';
        $this->oDb->execute($sUpdateSql);
    }

    /**
     * Insert new order details on Novalnet transaction table
     *
     * @param integer $oOrderNr
     * @param integer $iCnt
     */
    private function _insertNovalnetTranTable($oOrderNr, $iCnt)
    {
        $this->oDb                = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $aNNTransDetails = $this->oDb->getRow('SELECT * from novalnet_transaction_detail where ORDER_NO ='.$oOrderNr);

        // Insert new order details in Novalnet transaction details table
        $sInsertSql = 'INSERT INTO novalnet_transaction_detail (VENDOR_ID, PRODUCT_ID, AUTH_CODE, TARIFF_ID, TID, ORDER_NO, SUBS_ID, PAYMENT_ID, PAYMENT_TYPE, AMOUNT, CURRENCY, STATUS, GATEWAY_STATUS, TEST_MODE, CUSTOMER_ID, ORDER_DATE, REFUND_AMOUNT, TOTAL_AMOUNT, PROCESS_KEY, MASKED_DETAILS, ZERO_TRXNDETAILS, ZERO_TRXNREFERENCE, ZERO_TRANSACTION, REFERENCE_TRANSACTION, NNBASKET) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $aInsertValues  = array($aNNTransDetails['VENDOR_ID'], $aNNTransDetails['PRODUCT_ID'], $aNNTransDetails['AUTH_CODE'], $aNNTransDetails['TARIFF_ID'], $this->aCaptureParams['tid'], $iCnt, $aNNTransDetails['SUBS_ID'], $aNNTransDetails['PAYMENT_ID'], $aNNTransDetails['PAYMENT_TYPE'], $this->aCaptureParams['amount'], $aNNTransDetails['CURRENCY'], $this->aCaptureParams['status'], $this->aCaptureParams['tid_status'], $aNNTransDetails['TEST_MODE'], $aNNTransDetails['CUSTOMER_ID'], date('Y-m-d H:i:s'), '0', $this->aCaptureParams['amount'], $aNNTransDetails['PROCESS_KEY'], '', '', '', '', $aNNTransDetails['REFERENCE_TRANSACTION'], $aNNTransDetails['NNBASKET']);

        $this->oDb->execute( $sInsertSql, $aInsertValues );
    }

    /**
     * Insert new order details on Novalnet subscription table
     *
     * @param integer $oOrderNr
     * @param integer $iCnt
     */
    private function _insertNovalnetSubDetailsTable($oOrderNr, $iCnt)
    {
        $this->oDb                = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $aNNSubsDetails = $this->oDb->getRow('SELECT * from novalnet_subscription_detail where ORDER_NO ='.$oOrderNr);

        // Insert new order details in Novalnet subscription details table
        $sInsertSql = 'INSERT INTO novalnet_subscription_detail (ORDER_NO, SUBS_ID, TID, SIGNUP_DATE, TERMINATION_REASON, TERMINATION_AT) VALUES (?, ?, ?, ?, ?, ?)';

        $aInsertValues = array($iCnt, $aNNSubsDetails['SUBS_ID'], $aNNSubsDetails['TID'], date('Y-m-d H:i:s'), $aNNSubsDetails['TERMINATION_REASON'], $aNNSubsDetails['TERMINATION_AT']);

        $this->oDb->execute( $sInsertSql, $aInsertValues );
    }

    /**
     * Insert new order details in Novalnet Callback table
     *
     * @param integer $oOrderNr
     * @param integer $iCnt
     */
    private function _insertNovalnetCallbackTable($oOrderNr, $iCnt)
    {
        $this->oDb                = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $aNNCallbackDetails = $this->oDb->getRow('SELECT * from novalnet_callback_history where ORDER_NO ='.$oOrderNr);

        // Insert new order details in Novalnet subscription details table
        $sInsertSql = 'INSERT INTO novalnet_callback_history (PAYMENT_TYPE, STATUS, ORDER_NO, AMOUNT, CURRENCY, CALLBACK_TID, ORG_TID, PRODUCT_ID, CALLBACK_DATE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $aInsertValues = array($aNNCallbackDetails['PAYMENT_TYPE'], $aNNCallbackDetails['STATUS'], $iCnt, $aNNCallbackDetails['AMOUNT'], $aNNCallbackDetails['CURRENCY'], $aNNCallbackDetails['CALLBACK_TID'], $aNNCallbackDetails['ORG_TID'], $aNNCallbackDetails['PRODUCT_ID'], $aNNCallbackDetails['CALLBACK_DATE']);

        $this->oDb->execute( $sInsertSql, $aInsertValues );
    }

    /**
     * Insert new order details in Novalnet Preinvoice table
     *
     * @param integer $oOrderNr
     * @param integer $iCnt
     */
    public function _insertNovalnetPreInvTable($oOrderNr, $iCnt)
    {
        $this->oDb                = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $aNNPreInvDetails = $this->oDb->getRow('SELECT * from novalnet_preinvoice_transaction_detail where ORDER_NO ='.$oOrderNr);

         // Insert new order details in Novalnet Preinvoice transaction details table
        $sInsertSql = 'INSERT INTO novalnet_preinvoice_transaction_detail (ORDER_NO, TID, TEST_MODE, ACCOUNT_HOLDER, BANK_IBAN, BANK_BIC, BANK_NAME, BANK_CITY, AMOUNT, CURRENCY, INVOICE_REF, DUE_DATE, ORDER_DATE, PAYMENT_REF) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $aNNPreInvDetails['DUE_DATE'] = $this->aCaptureParams['due_date'];
        $aInsertValues = array($iCnt, $this->aCaptureParams['tid'], $aNNPreInvDetails['TEST_MODE'], $this->aCaptureParams['invoice_account_holder'], $this->aCaptureParams['invoice_iban'], $this->aCaptureParams['invoice_bic'], $this->aCaptureParams['invoice_bankname'], $this->aCaptureParams['invoice_bankplace'], $this->aCaptureParams['amount'], $this->aCaptureParams['CURRENCY'], $aNNPreInvDetails['INVOICE_REF'], $aNNPreInvDetails['DUE_DATE'], date('Y-m-d H:i:s'), $aNNPreInvDetails['PAYMENT_REF']);

        $this->oDb->execute( $sInsertSql, $aInsertValues );
    }

    /**
     * Get Transaction details
     *
     * @param string $sLang
     *
     * @return array
     */
    private function _getTransactionComments($sLang)
    {
        $oConfig = oxRegistry::getConfig();
        $this->_oLang->setBaseLanguage($sLang);
        $sTransactionComments = '';
        if (in_array($this->aCaptureParams['payment_type'], array('INVOICE_START', 'GUARANTEED_INVOICE'))) {
            $sTransactionComments .= $this->_oLang->translateString('NOVALNET_TRANSACTION_ID') . $this->aCaptureParams['shop_tid'];
            $sTransactionComments .= !empty($this->aCaptureParams['test_mode']) ? $this->_oLang->translateString('NOVALNET_TEST_ORDER') : '';
        }

        return $sTransactionComments;
    }

    /**
     * Get Invoice prepayment details
     *
     * @param string $sLang
     *
     * @return array
     */
    protected function _getBankdetails($sLang)
    {
        $oConfig = oxRegistry::getConfig();
        $this->_oLang->setBaseLanguage($sLang);
        $sFormattedAmount = $this->_oLang->formatCurrency($this->aCaptureParams['amount']/100, $oConfig->getCurrencyObject($this->aCaptureParams['amount'])) . ' ' . $this->aCaptureParams['currency'];

        $sInvoiceComments = $this->_oLang->translateString('NOVALNET_INVOICE_COMMENTS_TITLE');
        if (!empty($this->aCaptureParams['due_date'])) {
            $sInvoiceComments .= $this->_oLang->translateString('NOVALNET_DUE_DATE') . date('d.m.Y', strtotime($this->aCaptureParams['due_date']));
		}
        $sInvoiceComments .= $this->_oLang->translateString('NOVALNET_ACCOUNT') . $this->aCaptureParams['invoice_account_holder'];
        $sInvoiceComments .= '<br>IBAN: ' . $this->aCaptureParams['invoice_iban'];
        $sInvoiceComments .= '<br>BIC: '  . $this->aCaptureParams['invoice_bic'];
        $sInvoiceComments .= '<br>Bank: ' . $this->aCaptureParams['invoice_bankname'] . ' ' . $this->aCaptureParams['invoice_bankplace'];
        $sInvoiceComments .= $this->_oLang->translateString('NOVALNET_AMOUNT') . $sFormattedAmount;

       return $sInvoiceComments;
    }

    /**
     * Send new order mail for customer & Owner
     *
     * @param string $sOrderId
     * @param object $oBasketValue
     *
     */
    protected function _sendOrderByEmail($sOrderId, $oBasketValue)
    {
        $oOrder = oxNew( "oxorder" );
        $oOrder->load($sOrderId);

        $oUser = oxNew("oxuser");
        $oUser->load($oOrder->oxorder__oxuserid->value);
        $oOrder->_oUser = $oUser;

        $oPayment = oxNew('oxuserpayment');
        $oPayment->load($oOrder->oxorder__oxpaymentid->value);
        $oOrder->_oPayment = $oPayment;

        $oBasket = unserialize($oBasketValue);
        $oOrder->_oBasket = $oBasket;

        $oxEmail = oxNew('oxemail');

        // send order email to user
        $oxEmail->sendOrderEMailToUser( $oOrder );
        // send order email to shop owner
        $oxEmail->sendOrderEMailToOwner( $oOrder );
    }
}

/*
Level 0 Payments:
-----------------
CREDITCARD
INVOICE_START
DIRECT_DEBIT_SEPA
GUARANTEED_INVOICE
GUARANTEED_DIRECT_DEBIT_SEPA
PAYPAL
ONLINE_TRANSFER
IDEAL
EPS
GIROPAY
PRZELEWY24

Level 1 Payments:
-----------------
RETURN_DEBIT_SEPA
GUARANTEED_RETURN_DEBIT_DE
REVERSAL
CREDITCARD_BOOKBACK
CREDITCARD_CHARGEBACK
REFUND_BY_BANK_TRANSFER_EU
PRZELEWY24_REFUND

Level 2 Payments:
-----------------
INVOICE_CREDIT
CREDIT_ENTRY_CREDITCARD
CREDIT_ENTRY_SEPA
CREDIT_ENTRY_DE
DEBT_COLLECTION_SEPA
DEBT_COLLECTION_CREDITCARD
DEBT_COLLECTION_DE
DEBT_COLLECTION_AT
*/
?>
