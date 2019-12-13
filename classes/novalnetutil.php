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
class novalnetUtil {

    /**
     * Novalnet module version
     *
     * @var array
     */
    public $sNovalnetVersion = '11.1.6';

    /**
     * Novalnet module configuration
     *
     * @var array
     */
    public $aNovalnetConfig;

    /**
     * Current payment
     *
     * @var string
     */
    public $sCurrentPayment;

    /**
     * Novalnet redirection payments
     *
     * @var array
     */
    public $aRedirectPayments = array( 'novalnetonlinetransfer', 'novalnetideal', 'novalnetpaypal', 'novalneteps', 'novalnetgiropay', 'novalnetprzelewy24');


    public function __construct()
    {
        $this->oConfig         = oxRegistry::getConfig();
        $this->oLang           = oxRegistry::getLang();
        $this->oSession        = oxRegistry::getSession();
        $this->oUtils          = oxRegistry::getUtils();
        $this->aNovalnetConfig = $this->oConfig->getShopConfVar('aNovalnetConfig', '', 'novalnet');
    }

    /**
     * Performs payment request for all payments and return response for direct payments
     *
     * @param object $oOrder
     *
     * @return array
     */
    public function doPayment($oOrder)
    {
        $aNovalnetURL = array( 'PAYGATE'         => 'https://payport.novalnet.de/paygate.jsp',
                               'CC3DPCI'         => 'https://payport.novalnet.de/pci_payport',
                               'ONLINETRANSFER'  => 'https://payport.novalnet.de/online_transfer_payport',
                               'IDEAL'           => 'https://payport.novalnet.de/online_transfer_payport',
                               'PAYPAL'          => 'https://payport.novalnet.de/paypal_payport',
                               'EPS'             => 'https://payport.novalnet.de/giropay',                               
                               'GIROPAY'         => 'https://payport.novalnet.de/giropay',
                               'PRZELEWY24'      => 'https://payport.novalnet.de/globalbank_transfer'
                             );
        $oBasket = $this->oSession->getBasket();
        $oUser   = $oOrder->getOrderUser();
        $this->sCurrentPayment = $oBasket->getPaymentId();
        $this->sPaymentName    = strtoupper(substr($this->sCurrentPayment, 8, strlen($this->sCurrentPayment)));
        $aDynValue             = array_map('trim', $this->oSession->getVariable('dynvalue'));

        // prepares the parameter passed to Novalnet gateway
        $aRequest = $this->_importNovalnetParams($oBasket, $oUser);
        // perform the payment call to Novalnet server - if not redirect payments then makes curl request other wise redirect to Novalnet server
        if (!in_array($this->sCurrentPayment, $this->aRedirectPayments)) {
            $aResponse = $this->doCurlRequest($aRequest, $aNovalnetURL['PAYGATE']);
            $this->oSession->setVariable('aNovalnetGatewayResponse', $aResponse);
            return $aResponse;
        } else {
            $sNovalnetURL = $this->sCurrentPayment != 'novalnetcreditcard' ? $aNovalnetURL[$this->sPaymentName] : $aNovalnetURL['CC3DPCI'];
            $this->oSession->setVariable('aNovalnetRedirectRequest', $aRequest);
            $this->oSession->setVariable('sNovalnetRedirectURL', $sNovalnetURL);
            $oOrder->delete();
            $sRedirectURL = $this->oConfig->getShopCurrentURL() . 'cl=novalnetredirect';
            $this->oUtils->redirect($sRedirectURL);
        }
    }

    /**
     * Performs payment confirmation while Fraud module is enabled
     *
     * @param string $sCurrentPayment
     *
     * @return array
     */
    public function doFraudModuleSecondCall($sCurrentPayment)
    {
        $aFirstRequest  = $this->oSession->getVariable('aNovalnetGatewayRequest');
        $aFirstResponse = $this->oSession->getVariable('aNovalnetGatewayResponse');
        $iRequestType   = $this->getNovalnetConfigValue('iCallback' . $sCurrentPayment);
        $aDynValue      = array_map('trim', $this->oSession->getVariable('dynvalue'));
        $sRemoteIp      = $this->getIpAddress();

        // checks the second call request type of fraud prevention payments
        if ($aDynValue['newpin_' . $sCurrentPayment])
            $sRequestType = 'TRANSMIT_PIN_AGAIN';
        elseif (in_array($iRequestType, array('1', '2')))
            $sRequestType = 'PIN_STATUS';

        $sPinXmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
                              <nnxml>
                                  <info_request>
                                      <vendor_id>' . $aFirstRequest['vendor'] . '</vendor_id>
                                      <vendor_authcode>' . $aFirstRequest['auth_code'] . '</vendor_authcode>
                                      <request_type>' . $sRequestType . '</request_type>
                                      <tid>' . $aFirstResponse['tid'] . '</tid>
                                      <remote_ip>' . $sRemoteIp . '</remote_ip>';

        if ($sRequestType == 'PIN_STATUS')
            $sPinXmlRequest .= '<pin>' . trim($aDynValue['pinno_' . $sCurrentPayment]) . '</pin>';

        $sPinXmlRequest .= '</info_request></nnxml>';
        $sPinXmlResponse = $this->doCurlRequest($sPinXmlRequest, 'https://payport.novalnet.de/nn_infoport.xml', false);
        preg_match('/status>?([^<]+)/i', $sPinXmlResponse, $aStatus);
        $aResponse['status'] = $aStatus[1];
        preg_match('/status_message>?([^<]+)/i', $sPinXmlResponse, $aMessage);
        $aResponse['status_desc'] = $aMessage[1];
        preg_match('/tid_status>?([^<]+)/i', $sPinXmlResponse, $aTidStatus);
        $aResponse['tid_status'] = $aTidStatus[1];

        if (!empty($aResponse['tid_status'])) {
            $aFirstResponse['tid_status'] = $aTidStatus[1];
            $this->oSession->setVariable('aNovalnetGatewayResponse', $aFirstResponse);
        }

        return $aResponse;
    }

    /**
     * Performs CURL request
     *
     * @param mixed   $mxRequest
     * @param string  $sUrl
     * @param boolean $blBuildQuery
     *
     * @return mixed
     */
    public function doCurlRequest($mxRequest, $sUrl, $blBuildQuery = true)
    {
        $sPaygateQuery = ($blBuildQuery) ? http_build_query($mxRequest) : $mxRequest;
        $sCurlTimeOut  = $this->getNovalnetConfigValue('iGatewayTimeOut');
        $sProxy        = $this->getNovalnetConfigValue('sProxy');
        $sCurlTimeOut  = (!empty($iCurlTimeout) && $iCurlTimeout > 240) ? $iCurlTimeout : 240;
        $oCurl = oxNew('oxCurl');
        $oCurl->setMethod('POST');
        $oCurl->setUrl($sUrl);
        $oCurl->setQuery($sPaygateQuery);
        $oCurl->setOption('CURLOPT_FOLLOWLOCATION', 0);
        $oCurl->setOption('CURLOPT_SSL_VERIFYHOST', false);
        $oCurl->setOption('CURLOPT_SSL_VERIFYPEER', false);
        $oCurl->setOption('CURLOPT_RETURNTRANSFER', 1);
        $oCurl->setOption('CURLOPT_TIMEOUT', $sCurlTimeOut);
        if (!empty($sProxy))
            $oCurl->setOption('CURLOPT_PROXY', $sProxy);

        $mxData = $oCurl->execute();

        if ($blBuildQuery)
            parse_str($mxData, $mxData);

        return $mxData;
    }

    /**
     * Imports Novalnet parameters for payment call
     *
     * @param array  $oBasket
     * @param string $oUser
     *
     * @return array
     */
    private function _importNovalnetParams($oBasket, $oUser)
    {
        $aRequest = array();
        $this->sShopURL = str_replace('&amp;', '&', $this->oConfig->getShopMainUrl());

        $this->_importNovalnetCredentials($aRequest);
        $this->setAffiliateCredentials($aRequest, $oUser->oxuser__oxcustnr->value);
        $this->_importUserDetails($aRequest, $oUser);
        $this->_importReferenceParameters($aRequest);
        $this->_importOrderDetails($aRequest, $oBasket);
        $this->_importPaymentDetails($aRequest);
        $this->_importGuaranteedPaymentParameters($aRequest);

        // checks subscription payments for adding the subscription parameters
        if ($this->iTariffType != 2)
            $this->_importSubscriptionParameters($aRequest);

        $this->oSession->setVariable('aNovalnetGatewayRequest', $aRequest); // Store novalnet request in session to use at the end of the transaction

        // encodes the params and generates hash for redirect payments
        if (in_array($this->sCurrentPayment, $this->aRedirectPayments)) {
            $this->_importRedirectPaymentParameters($aRequest);
            $this->_encodeNovalnetParams($aRequest);
        }
        $aRequest = array_map('trim', $aRequest);
        return $aRequest;
    }

    /**
     * Gets Novalnet configuration value
     *
     * @param string $sConfig
     *
     * @return string
     */
    public function  getNovalnetConfigValue($sConfig)
    {
        return $this->aNovalnetConfig[$sConfig];
    }

    /**
     * Imports Novalnet credentials
     *
     * @param array &$aRequest
     */
    private function _importNovalnetCredentials(&$aRequest)
    {
        $aPayments = array( 'novalnetcreditcard' => 6, 'novalnetsepa' => 37, 'novalnetinvoice' => 27,'novalnetprepayment' => 27, 'novalnetonlinetransfer' => 33, 'novalnetideal' => 49, 'novalnetpaypal' => 34, 'novalneteps' => 50, 'novalnetgiropay' => 69, 'novalnetprzelewy24' => 78, 'novalnetbarzahlen' => 59);

        $aRequest  = array(
                            'vendor'    => $this->getNovalnetConfigValue('iVendorId'),
                            'auth_code' => $this->getNovalnetConfigValue('sAuthCode'),
                            'product'   => $this->getNovalnetConfigValue('iProductId'),
                            'key'       => $aPayments[$this->sCurrentPayment]
                          );
        $this->oSession->setVariable('sNovalnetAccessKey', $this->getNovalnetConfigValue('sAccessKey'));
        $aTariffId             = explode('-', $this->getNovalnetConfigValue('sTariffId'));
        $this->iTariffType     = $aTariffId[0];
        $aRequest['tariff']    = $aTariffId[1];
        $aRequest['test_mode'] = $this->getNovalnetConfigValue('blTestmode' . $this->sCurrentPayment);
    }

    /**
     * Sets affiliate credentials for the payment call
     *
     * @param array &$aRequest
     * @param integer $iCustomerNo
     *
     */
    public function setAffiliateCredentials(&$aRequest, $iCustomerNo)
    {
        $oDb     = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $aResult = $oDb->getRow('SELECT AFF_ID FROM novalnet_aff_user_detail WHERE CUSTOMER_ID = "' . $iCustomerNo . '"');
        if (!empty($aResult['AFF_ID']))
            $this->oSession->setVariable('nn_aff_id', $aResult['AFF_ID']);

        // checks Novalnet affiliate id in session
        if ($this->oSession->getVariable('nn_aff_id')) {
            $aResult = $oDb->getRow('SELECT AFF_AUTHCODE, AFF_ACCESSKEY FROM novalnet_aff_account_detail WHERE AFF_ID = "' . $this->oSession->getVariable('nn_aff_id') . '"');
            if (!empty($aResult['AFF_AUTHCODE']) && !empty($aResult['AFF_ACCESSKEY'])) {
                $aRequest['vendor']    = $this->oSession->getVariable('nn_aff_id');
                $aRequest['auth_code'] = $aResult['AFF_AUTHCODE'];
                $this->oSession->setVariable('sNovalnetAccessKey', $aResult['AFF_ACCESSKEY']);
            }
        }
    }

    /**
     * Imports user details
     *
     * @param array  &$aRequest
     * @param object $oUser
     */
    private function _importUserDetails(&$aRequest, $oUser)
    {
        $sFirstName = $oUser->oxuser__oxfname->value;
        $sLastName  = $oUser->oxuser__oxlname->value;
        if(empty($sFirstName) || empty($sLastName)) {
            $sName = $sFirstName . $sLastName;
            list($sFirstName, $sLastName) = preg_match('/\s/',$sName) ? explode(' ', $sName, 2) : array($sName, $sName);
        }
        $aUserDetails = array(
                                'first_name'     => $this->_setUTFEncode($sFirstName),
                                'last_name'      => $this->_setUTFEncode($sLastName),
                                'city'           => $this->_setUTFEncode($oUser->oxuser__oxcity->value),
                                'zip'            => $oUser->oxuser__oxzip->value,
                                'email'          => $oUser->oxuser__oxusername->value,
                                'gender'         => 'u',
                                'customer_no'    => $oUser->oxuser__oxcustnr->value,
                                'tel'            => (!empty($oUser->oxuser__oxfon->value)) ? $oUser->oxuser__oxfon->value : $oUser->oxuser__oxprivfon->value,
                                'street'         => $this->_setUTFEncode($oUser->oxuser__oxstreet->value),
                                'session'        => $this->oSession->getId(),
                                'system_name'    => 'oxideshop',
                                'system_version' => $this->oConfig->getVersion() . '-NN' . $this->sNovalnetVersion,
                                'system_url'     => $this->sShopURL,
                                'system_ip'      => $this->getIpAddress(true),
                                'remote_ip'      => $this->getIpAddress(),
                                'lang'           => strtoupper($this->oLang->getLanguageAbbr()),
                                'country_code'   => $this->getCountryISO($oUser->oxuser__oxcountryid->value),
                             );

        if ($oUser->oxuser__oxbirthdate->value != '0000-00-00')
            $aUserDetails['birth_date'] = date('Y-m-d', strtotime($oUser->oxuser__oxbirthdate->value));

        if (!empty($oUser->oxuser__oxstreetnr->value)) {
            $aUserDetails['street']          .= ' ' . trim($oUser->oxuser__oxstreetnr->value);
            $aUserDetails['search_in_street'] = '1';
        }

        $oAddress = $oUser->getSelectedAddress();
        $sCompany = (!empty($oUser->oxuser__oxcompany->value) ? $oUser->oxuser__oxcompany->value : (!empty($oAddress->oxaddress__oxcompany->value) ? $oAddress->oxaddress__oxcompany->value : ''));

        if ($sCompany)
            $aUserDetails['company'] = $sCompany;

        if (!empty($oUser->oxuser__oxmobfon->value))
            $aUserDetails['mobile'] = $oUser->oxuser__oxmobfon->value;

        if (!empty($oUser->oxuser__oxfax->value))
            $aUserDetails['fax'] = $oUser->oxuser__oxfax->value;

        $aRequest = array_merge($aRequest, $aUserDetails);
    }

    /**
     * Imports reference parameters
     *
     * @param array &$aRequest
     */
    private function _importReferenceParameters(&$aRequest)
    {
        $sReferrerId    = $this->getNovalnetConfigValue('sReferrerID');
        $sNotifyURL     = $this->getNovalnetConfigValue('sNotifyURL');
        $sReferrenceOne = $this->getNovalnetConfigValue('sReferenceOne' . $this->sCurrentPayment);
        $sReferrenceTwo = $this->getNovalnetConfigValue('sReferenceTwo' . $this->sCurrentPayment);
        if (!empty($sReferrerId))
            $aRequest['referrer_id'] = $sReferrerId;

        if (!empty($sReferrenceOne)) {
            $aRequest['input1']    = 'Reference 1';
            $aRequest['inputval1'] = $sReferrenceOne;
        }

        if (!empty($sReferrenceTwo)) {
            $aRequest['input2']    = 'Reference 2';
            $aRequest['inputval2'] =  $sReferrenceTwo;
        }

        $aRequest['notify_url'] = !empty($sNotifyURL) ? $sNotifyURL : $this->oConfig->getShopCurrentURL() . 'cl=novalnetcallback&fnc=handlerequest';
    }

    /**
     * Imports order details
     *
     * @param array  &$aRequest
     * @param object $oBasket
     */
    private function _importOrderDetails(&$aRequest, $oBasket)
    {
        $aPaymentType = array( 'novalnetcreditcard' => 'CREDITCARD', 'novalnetsepa' => 'DIRECT_DEBIT_SEPA', 'novalnetinvoice' => 'INVOICE_START', 'novalnetprepayment' => 'INVOICE_START', 'novalnetonlinetransfer' => 'ONLINE_TRANSFER', 'novalnetideal' => 'IDEAL', 'novalnetpaypal' => 'PAYPAL', 'novalneteps' => 'EPS', 'novalnetgiropay' => 'GIROPAY', 'novalnetprzelewy24' => 'PRZELEWY24' , 'novalnetbarzahlen' => 'CASHPAYMENT');

        $this->dOrderAmount       = str_replace(',', '', number_format($oBasket->getPrice()->getBruttoPrice(), 2)) * 100;
        $dOnHoldLimit             = $this->getNovalnetConfigValue('dOnholdLimit'. $this->sCurrentPayment);
        $aRequest['amount']       = $this->dOrderAmount;
        $aRequest['currency']     = $oBasket->getBasketCurrency()->name;
        $aRequest['payment_type'] = $aPaymentType[$this->sCurrentPayment];

        // checks to set the onhold
        if (in_array($this->sCurrentPayment, array( 'novalnetcreditcard', 'novalnetsepa', 'novalnetinvoice', 'novalnetpaypal' )) && $this->getNovalnetConfigValue('sPaymentAction'. $this->sCurrentPayment) == 'authorize' && is_numeric($dOnHoldLimit) && $dOnHoldLimit <= $aRequest['amount'])
            $aRequest['on_hold'] = 1;


        // checks the shop type is zero amount booking and sets amount as zero for credit card, sepa and paypal payments
        if (in_array($aRequest['key'], array('6', '37', '34')) && $this->iTariffType == '2' && $this->getNovalnetConfigValue('iShopType'.$this->sCurrentPayment) == '2' &&  $this->oSession->getVariable('blGuaranteeEnabled' . $this->sCurrentPayment) !== 1) {
            $aRequest['amount'] = 0;
            unset($aRequest['on_hold']);
        }
    }

    /**
     * Imports Novalnet subscription credentials
     *
     * @param array &$aRequest
     */
    private function _importSubscriptionParameters(&$aRequest)
    {
        $sTariffPeriod        = $this->getNovalnetConfigValue('sTariffPeriod');
        $sTariffPeriod2       = $this->getNovalnetConfigValue('sTariffPeriod2');
        $dTariffPeriod2Amount = $this->getNovalnetConfigValue('dTariffPeriod2Amount');
        if (!empty($sTariffPeriod))
            $aRequest['tariff_period'] = $sTariffPeriod;
        if (!empty($sTariffPeriod2))
            $aRequest['tariff_period2'] = $sTariffPeriod2;
        if (!empty($dTariffPeriod2Amount))
            $aRequest['tariff_period2_amount'] = $dTariffPeriod2Amount;
    }

    /**
     * Imports guaranteed payment details for direct debit sepa and invoice
     *
     * @param array &$aRequest
     */
    private function _importGuaranteedPaymentParameters(&$aRequest)
    {
        if ($this->oSession->getVariable('blGuaranteeEnabled' . $this->sCurrentPayment)) {
            $aDynValue                = array_map('trim', $this->oSession->getVariable('anovalnetdynvalue'));
            $aRequest['payment_type'] = $aRequest['key'] == 27 ? 'GUARANTEED_INVOICE' : 'GUARANTEED_DIRECT_DEBIT_SEPA';
            $aRequest['key']          = $aRequest['key'] == 27 ? 41 : 40; // Default - 41 for Guaranteed Invoice and 40 for Guaranteed Sepa
            $aRequest['birth_date']   = date('Y-m-d', strtotime($aDynValue['birthdate' . $this->sCurrentPayment]));
        }
        if ($this->getNovalnetConfigValue('blGuaranteenovalnetsepa') == '1' && $this->iTariffType == '2') {
            unset($aRequest['create_payment_ref']);
        }
    }

    /**
     * Imports redirection payment parameters
     *
     * @param array &$aRequest
     */
    private function _importRedirectPaymentParameters(&$aRequest)
    {
        $sReturnURL = htmlspecialchars_decode($this->oConfig->getShopCurrentURL()) . 'cl=order&fnc=novalnetGatewayReturn';

        // checks credit card 3d and skips parameters
        if ($this->sCurrentPayment != 'novalnetcreditcard') {
            $aRequest['implementation']  = 'ENC';
            $aRequest['user_variable_0'] = $this->sShopURL;
        } else {
            $aRequest['implementation'] = 'PHP_PCI';
        }
        $aRequest = array_merge($aRequest, array( 'input3'              => 'shop_lang',
                                                  'inputval3'           => $this->oLang->getBaseLanguage(),
                                                  'input4'              => 'stoken',
                                                  'inputval4'           => $this->oConfig->getRequestParameter('stoken'),
                                                  'uniqid'              => $this->getUniqid(),
                                                  'return_url'          => $sReturnURL,
                                                  'error_return_url'    => $sReturnURL,
                                                  'return_method'       => 'POST',
                                                  'error_return_method' => 'POST',
                                                ));
    }

    /**
     * Imports payment details
     *
     * @param array &$aRequest
     */
    private function _importPaymentDetails(&$aRequest)
    {
        $aDynValue = array_map('trim', $this->oSession->getVariable('dynvalue'));
        if ($this->sCurrentPayment == 'novalnetcreditcard') {
            // checks the payment is proceed with one click shopping or not - credit card
            if (isset($aDynValue['novalnet_cc_new_details']) && $aDynValue['novalnet_cc_new_details'] == '0') {
                $aRequest['payment_ref'] = $this->oSession->getVariable('sPaymentRefnovalnetcreditcard');
                $this->oSession->deleteVariable('sPaymentRefnovalnetcreditcard');
            } else {
                $aRequest['nn_it']     = 'iframe';
                $aRequest['unique_id'] = $aDynValue['novalnet_cc_uniqueid'];
                $aRequest['pan_hash']  = $aDynValue['novalnet_cc_hash'];

               if (($this->getNovalnetConfigValue('blCC3DActive') == '1' && $this->getNovalnetConfigValue('blCC3DFraudActive') == '0') || ($this->getNovalnetConfigValue('blCC3DActive') == '1' && $this->getNovalnetConfigValue('blCC3DFraudActive') == '1')) {
                    $aRequest['cc_3d'] = 1;
                    array_push($this->aRedirectPayments, 'novalnetcreditcard'); // checks to set credit card payment as redirect
                } elseif ($this->getNovalnetConfigValue('blCC3DActive') == '0' && $this->getNovalnetConfigValue('blCC3DFraudActive') == '1') {
                    array_push($this->aRedirectPayments, 'novalnetcreditcard'); // checks to set credit card payment as redirect
                }  elseif ($this->getNovalnetConfigValue('iShopTypenovalnetcreditcard') != '') {
                    if($aDynValue['novalnet_cc_disclosure'] == 1)
                        $aRequest['create_payment_ref'] = 1;
                }
            }
        } elseif ($this->sCurrentPayment == 'novalnetsepa') {
            $aRequest['sepa_due_date']       = $this->_getDueDate(); // sets due date for direct debit sepa
            // checks the payment is proceed with one click shopping or not - direct debit sepa
            if (isset($aDynValue['novalnet_sepa_new_details']) && $aDynValue['novalnet_sepa_new_details'] == '0') {
                $aRequest['payment_ref'] = $this->oSession->getVariable('sPaymentRefnovalnetsepa');
                $this->oSession->deleteVariable('sPaymentRefnovalnetsepa');
            } else {
                $aRequest['bank_account_holder'] = $aDynValue['novalnet_sepa_holder'];
                $aRequest['iban']           = $aDynValue['novalnet_sepa_iban'];
                if ($this->getNovalnetConfigValue('iShopTypenovalnetsepa') != '') {
                    if($aDynValue['novalnet_sepa_disclosure'] == 1)
                        $aRequest['create_payment_ref'] = 1;
                }
            }
        } elseif (in_array($this->sCurrentPayment, array( 'novalnetinvoice', 'novalnetprepayment' ))) {
            $aRequest['invoice_type'] = $this->sPaymentName;
            if ($this->sCurrentPayment == 'novalnetinvoice') {
                $sDueDate = $this->_getDueDate(); // sets due date for invoice
                if($sDueDate)
                    $aRequest['due_date'] = $sDueDate;
            }
        } elseif ($this->sCurrentPayment == 'novalnetpaypal') {
            if (isset($aDynValue['novalnet_paypal_new_details']) && $aDynValue['novalnet_paypal_new_details'] == '0') {
                $aRequest['payment_ref'] = $this->oSession->getVariable('sPaymentRefnovalnetpaypal');
                $this->oSession->deleteVariable('sPaymentRefnovalnetpaypal');
                unset($this->aRedirectPayments[2]);
            } elseif ($this->getNovalnetConfigValue('iShopTypenovalnetpaypal') != '') {
                if($aDynValue['novalnet_paypal_disclosure'] == 1)
                    $aRequest['create_payment_ref'] = 1;
            }
        }elseif($this->sCurrentPayment == 'novalnetbarzahlen') {
            $sSlipDuedate = $this->_getDueDate();
            if($sSlipDuedate)
                $aRequest['cashpayment_due_date'] = $sSlipDuedate;
        }

        if ($this->getNovalnetConfigValue('iShopType'.$this->sCurrentPayment) == '2' && in_array($this->iTariffType, array(3, 4))) {
            unset($aRequest['create_payment_ref']);
        }

        $blCallbackEnabledStatus = $this->oSession->getVariable('blCallbackEnabled' . $this->sCurrentPayment);

        // checks to verify the fraud module activated
        if (in_array($this->sCurrentPayment, array( 'novalnetsepa', 'novalnetinvoice' )) && !empty($blCallbackEnabledStatus)) {
            // checks the fraud prevention type to add the custom parameters of the fraud prevention
            if ($this->getNovalnetConfigValue('iCallback' . $this->sCurrentPayment) == '1') {
                $aRequest['tel']             = $aDynValue['pinbycall_' . $this->sCurrentPayment];
                $aRequest['pin_by_callback'] = 1;
            } elseif ($this->getNovalnetConfigValue('iCallback' . $this->sCurrentPayment) == '2') {
                $aRequest['mobile']     = $aDynValue['pinbysms_' . $this->sCurrentPayment];
                $aRequest['pin_by_sms'] = 1;
            }
            $this->oSession->setVariable('dCallbackAmount' . $this->sCurrentPayment, $this->dOrderAmount);
        }
    }

    /**
     * Gets due date for invoice and direct debit sepa
     *
     * @return string
     */
    private function _getDueDate()
    {
        $iDueDate = trim($this->getNovalnetConfigValue('iDueDate' . $this->sCurrentPayment));
        if ($this->sCurrentPayment == 'novalnetinvoice') {
            $iDueDate = (!empty($iDueDate) && is_numeric($iDueDate)) ? $iDueDate : '';
        } elseif($this->sCurrentPayment == 'novalnetsepa') {
            $iDueDate = (empty($iDueDate) || $iDueDate <= 6) ? 7 : $iDueDate;
        } else {
            $iDueDate = (!empty($iDueDate) && is_numeric($iDueDate)) ? $iDueDate : '';
        }

        return ($iDueDate) ? date('Y-m-d', strtotime('+' . $iDueDate . ' days')) : false;
    }


    /**
     * Encodes Novalnet parameters and Generates hash value for redirect payments
     *
     * @param array &$aRequest
     */
    private function _encodeNovalnetParams(&$aRequest)
    {
        $this->_getEncodeData($aRequest, array( 'auth_code', 'product', 'tariff', 'amount', 'test_mode', 'uniqid' ));    // encodes the parameters

        $aRequest['hash'] = $this->_generateHash($aRequest); // generates the hash

    }

    /**
     * Generates the hash value
     *
     * @param array $aRequest
     *
     * @return string
     */
    private function _generateHash($aRequest)
    {
        return hash('sha256', ($aRequest['auth_code'] . $aRequest['product'] . $aRequest['tariff'] . $aRequest['amount'] . $aRequest['test_mode'] . $aRequest['uniqid'] . strrev($this->oSession->getVariable('sNovalnetAccessKey'))));
    }

    /**
     * Checks the hash value for redirection payment
     *
     * @param array &$aResponse
     */
    public function checkHash(&$aResponse)
    {
        // checks hash2 and newly generated hash - returns false if both are differed
        if ($aResponse['hash2'] != $this->_generateHash($aResponse))
            return false;

        $this->_getDecodeData($aResponse, array( 'auth_code', 'product', 'tariff', 'amount', 'test_mode', 'uniqid' ));
    }

    /**
     * Encode the required parameters
     *
     * @param array &$aRequest
     * @param array $aEncodeFields
     *
     * @return boolean
     */
    private function _getEncodeData(&$aRequest, $aEncodeFields)
    {
        $encode = array( 'auth_code', 'product', 'tariff', 'amount', 'test_mode');
        foreach ($encode as $sValue) {
            $aRequest[$sValue] = htmlentities(base64_encode(openssl_encrypt($aRequest[$sValue], "aes-256-cbc", $this->oSession->getVariable('sNovalnetAccessKey'), true, $aRequest['uniqid'])));
        }
    }

    /**
     * Decodes the required parameters in the response from server
     *
     * @param array &$aResponse
     * @param array $aDecodeParams
     *
     * @return boolean
     */
    private function _getDecodeData(&$aResponse, $aDecodeParams)
    {
        $decode = array( 'auth_code', 'product', 'tariff', 'amount', 'test_mode');
        foreach ($decode as $sValue) {
            $aResponse[$sValue] = openssl_decrypt(base64_decode($aResponse[$sValue]), "aes-256-cbc", $this->oSession->getVariable('sNovalnetAccessKey'), true, $aResponse['uniqid']);
        }
    }

    /**
     * Get country ISO code
     *
     * @param string $sCountryId
     *
     * @return string
     */
    public function getCountryISO($sCountryId)
    {
        $oCountry = oxNew('oxCountry');
        $oCountry->load($sCountryId);
        return $oCountry->oxcountry__oxisoalpha2->value;
    }

    /**
     * Set the UTF8 encoding
     *
     * @param string $sStr
     *
     * @return string
     */
    private function _setUTFEncode($sStr)
    {
        return (mb_detect_encoding($sStr, 'UTF-8', true) === false) ? utf8_encode($sStr) : $sStr;
    }

    /**
     * Get Server / Remote IP address
     *
     * @param boolean $blServer
     *
     * @return string
     */
    public function getIpAddress($blServer = false)
    {
        if (empty($blServer)) {
            $oUtilsServer = oxNew('oxUtilsServer');
            $sIP = $oUtilsServer->getRemoteAddress();
            return filter_var($sIP, FILTER_VALIDATE_IP);
        } else {
            $sIP = $_SERVER['SERVER_ADDR'];
             if (empty($sIP)) {
                // Handled for IIS server
                return gethostbyname($_SERVER['SERVER_NAME']);
            } else {
                return (filter_var($sIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) ? '127.0.0.1' : $sIP;
            }
        }
    }


    /**
     * Sets error message from the failure response of novalnet
     *
     * @param array $aResponse
     *
     * @return string
     */
    public function setNovalnetPaygateError($aResponse)
    {
        return !empty($aResponse['status_desc']) ? $aResponse['status_desc'] : (!empty($aResponse['status_text']) ? $aResponse['status_text'] : (!empty($aResponse['status_message']) ? $aResponse['status_message'] : $this->oLang->translateString('NOVALNET_DEFAULT_ERROR_MESSAGE')));
    }

    /**
     * Forms invoice comments for invoice and prepayment orders
     *
     * @param array $aInvoiceDetails
     *
     * @return string
     */
    public function getInvoiceComments($aInvoiceDetails)
    {
        $sFormattedAmount = $this->oLang->formatCurrency($aInvoiceDetails['amount']/100, $this->oConfig->getCurrencyObject($aInvoiceDetails['currency'])) . ' ' . $aInvoiceDetails['currency'];
        $sInvoiceComments = '<br>'.$this->oLang->translateString('NOVALNET_INVOICE_COMMENTS_TITLE');
        if (!empty($aInvoiceDetails['due_date']))
            $sInvoiceComments .= $this->oLang->translateString('NOVALNET_DUE_DATE') . date('d.m.Y', strtotime($aInvoiceDetails['due_date']));
        $sInvoiceComments .= $this->oLang->translateString('NOVALNET_ACCOUNT') . $aInvoiceDetails['invoice_account_holder'];
        $sInvoiceComments .= '<br>IBAN: ' . $aInvoiceDetails['invoice_iban'];
        $sInvoiceComments .= '<br>BIC: '  . $aInvoiceDetails['invoice_bic'];
        $sInvoiceComments .= '<br>Bank: ' . $aInvoiceDetails['invoice_bankname'] . ' ' . $aInvoiceDetails['invoice_bankplace'];
        $sInvoiceComments .= $this->oLang->translateString('NOVALNET_AMOUNT') . $sFormattedAmount;
        
        $sInvoiceComments .=  $this->oLang->translateString('NOVALNET_INVOICE_MULTI_REF_DESCRIPTION');
        
        $sInvoiceComments .= sprintf($this->oLang->translateString('NOVALNET_INVOICE_MULTI_REFERENCE'), 1) . ':' . $aInvoiceDetails['invoice_ref'];
        $sInvoiceComments .= sprintf($this->oLang->translateString('NOVALNET_INVOICE_MULTI_REFERENCE'), 2) . ':' . $this->oLang->translateString('NOVALNET_ORDER_NO') . $aInvoiceDetails['order_no'];
        
        return $sInvoiceComments;
    }

    /**
     * Sets redirection URL while any invalid conceptuals during payment process
     *
     * @param string $sMessage
     *
     * @return string
     */
    public function setRedirectURL($sMessage)
    {
        return $this->oConfig->getSslShopUrl() . 'index.php?cl=payment&payerror=-1&payerrortext=' . urlencode($this->_setUTFEncode($sMessage));
    }

    /**
     * Clears Novalnet session
     */
    public function clearNovalnetSession()
    {
        $aNovalnetSessions = array( 'sNovalnetAccessKey','aNovalnetGatewayRequest', 'aNovalnetGatewayResponse',
                                    'anovalnetdynvalue', 'nn_aff_id', 'dynvalue', 'blOneClicknovalnetcreditcard',
                                    'blOneClicknovalnetsepa', 'blOneClicknovalnetpaypal', 'blGuaranteeEnablednovalnetsepa',
                                    'blGuaranteeEnablednovalnetinvoice', 'blGuaranteeForceDisablednovalnetsepa', 'blGuaranteeForceDisablednovalnetinvoice',
                                    'blCallbackEnablednovalnetsepa', 'sCallbackTidnovalnetsepa', 'dCallbackAmountnovalnetsepa',
                                    'blCallbackEnablednovalnetinvoice', 'sCallbackTidnovalnetinvoice','dCallbackAmountnovalnetinvoice' );

        foreach ($aNovalnetSessions as $sSession) {
            $this->oSession->deleteVariable($sSession);
        }
    }

    /**
     * Clears Novalnet fraud modules session
     */
    public function clearNovalnetFraudModulesSession()
    {
        $aPinPayments = array( 'novalnetsepa', 'novalnetinvoice' );
        foreach ($aPinPayments as $sPayment) {
            $this->oSession->deleteVariable('sCallbackTid' . $sPayment);
            $this->oSession->deleteVariable('dCallbackAmount' . $sPayment);
        }
    }

    /**
     * Clears Novalnet payment lock
     */
    public function clearNovalnetPaymentLock()
    {
        $aPinPayments = array( 'novalnetsepa', 'novalnetinvoice' );
        foreach ($aPinPayments as $sPayment) {
            $this->oSession->deleteVariable('blNovalnetPaymentLock' . $sPayment);
            $this->oSession->deleteVariable('sNovalnetPaymentLockTime' . $sPayment);
        }
    }

    /**
     * Forms comments for barzhalan nearest store details
     *
     * @param array   $aBarzahlenDetails
     * @param boolean $blValue
     *
     * @return string
     */
    public function getBarzahlenComments($aBarzahlenDetails , $blValue = false)
    {
        $iStoreCounts = 1;
        if($blValue) {
            $aBarzalan =array();
            foreach ($aBarzahlenDetails as $sKey => $sValue){
                if(stripos($sKey,'nearest_store')!==false){
                    $aBarzalan[$sKey] = $sValue;
                }
            }

            return $aBarzalan;
        }

        foreach ($aBarzahlenDetails as $sKey => $sValue)
        {
            if (strpos($sKey, 'nearest_store_street') !== false)
            {
                $iStoreCounts++;
            }
        }
        $oCountry = oxNew('oxCountry');
        $sBarzahlenComments = $this->oLang->translateString('NOVALNET_BARZAHLEN_DUE_DATE') . date('d.m.Y', strtotime($aBarzahlenDetails['cashpayment_due_date']));
        if($iStoreCounts !=1)
            $sBarzahlenComments .= $this->oLang->translateString('NOVALNET_BARZAHLEN_PAYMENT_STORE');
        for ($i = 1; $i < $iStoreCounts; $i++)
        {
            $sBarzahlenComments .= $aBarzahlenDetails['nearest_store_title_' . $i] . '<br>';
            $sBarzahlenComments .= $aBarzahlenDetails['nearest_store_street_' . $i ] . '<br>';
            $sBarzahlenComments .= $aBarzahlenDetails['nearest_store_city_' . $i ] . '<br>';
            $sBarzahlenComments .= $aBarzahlenDetails['nearest_store_zipcode_' . $i ] . '<br>';
            $oCountry->loadInLang(oxRegistry::getLang()->getObjectTplLanguage(), $oCountry->getIdByCode($aBarzahlenDetails['nearest_store_country_' . $i ]));
            $sBreak = '<br><br>';
            if ( ($iStoreCounts -2) < $i )
                $sBreak ='';
            $sBarzahlenComments .= $oCountry->oxcountry__oxtitle->value . $sBreak;
        }

        return $sBarzahlenComments;
    }

    /**
     * Send payment notification mail
     *
     * @param string $sLang
     * @param string $sComments
     * @param integer $iOrderNo
     *
     */
    public function sendPaymentNotificationMail($sLang, $sComments, $iOrderNo)
    {
        $oMail   = oxNew('oxEmail');
        $oLang   = oxNew('oxLang');
        $oLang->setBaseLanguage($sLang);
        $oDb     = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $sShopName = $this->oConfig->getActiveShop()->oxshops__oxname->rawValue;
        $sQuery = "SELECT OXUSERID from oxorder where OXORDERNR = '".$iOrderNo."'";
        $sRow = $oDb->getRow($sQuery);
        $sSelectQuery = "SELECT OXFNAME, OXLNAME, OXUSERNAME from oxuser where OXID = '".$sRow['OXUSERID']."'";
        $aCustomerData = $oDb->getRow($sSelectQuery);

        if (!empty($aCustomerData['OXUSERNAME'])) {
            $sSubject = $oLang->translateString('NOVALNET_ORDER_CONFIRMATION') . $iOrderNo. $oLang->translateString('NOVALNET_ORDER_CONFIRMATION1')  .$sShopName. $oLang->translateString('NOVALNET_ORDER_CONFIRMATION2') ;
            $email_content    = '<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px; margin:0; padding:0;">
                                    <div style="width:55%;height:auto;margin: 0 auto;background:rgb(247, 247, 247);border: 2px solid rgb(223, 216, 216);border-radius: 5px;box-shadow: 1px 7px 10px -2px #ccc;">
                                        <div style="min-height: 300px;padding:20px;">
                                            <table cellspacing="0" cellpadding="0" border="0" width="100%">

                                                <tr><b>Dear Mr./Ms./Mrs.</b> '.$aCustomerData['OXFNAME'].' '.$aCustomerData['OXLNAME'].' </tr></br></br>

                                                <tr>'.$oLang->translateString('NOVALNET_ORDER_CONFIRMATION3').'</tr></br></br>
                                                <tr>'. $oLang->translateString('NOVALNET_PAYMENT_INFORMATION') .'</br>
                                                '.$sComments.'
                                                </tr></br>

                                            </table>
                                        </div>
                                        <div style="width:100%;height:20px;background:#00669D;"></div>
                                    </div>
                                </body>';
            $oShop = $oMail->getShop();
            $oMail->setFrom($oShop->oxshops__oxorderemail->value);
            $oMail->setRecipient($aCustomerData['OXUSERNAME']);
            $oMail->setSubject( $sSubject );
            $oMail->setBody( $email_content );
            $oMail->send();
        } else {
            return 'Mail not sent<br>';
        }
    }
    
    /**
     * Get unique id
     *
     * @return string
     */
    public function getUniqid()
    {
        $randomwordarray = explode(',', '8,7,6,5,4,3,2,1,9,0,9,7,6,1,2,3,4,5,6,7,8,9,0');
        shuffle($randomwordarray);
        return substr(implode($randomwordarray, ''), 0, 16);
    }
}
?>
