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
class novalnetPayment extends novalnetPayment_parent {

    /**
     * Returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        $this->oNovalnetSession = $this->getSession();
        if ($this->oNovalnetSession->hasVariable('sNovalnetSession') && $this->oNovalnetSession->getVariable('sNovalnetSession') != $this->oNovalnetSession->getId()) {
            $this->oNovalnetSession->deleteVariable('sNovalnetSession');
            $oNovalnetUtil = oxNew('novalnetUtil');
            $oNovalnetUtil->clearNovalnetSession();
            $oNovalnetUtil->clearNovalnetPaymentLock();
            $this->oNovalnetSession->setVariable('sNovalnetSession', $this->oNovalnetSession->getId());
        } elseif (!$this->oNovalnetSession->hasVariable('sNovalnetSession')) {
            $this->oNovalnetSession->setVariable('sNovalnetSession', $this->oNovalnetSession->getId());
        }
        return parent::render();
    }

    /**
     * Gets payments to show on the payment page
     *
     * @return array
     */
    public function getPaymentList()
    {
        parent::getPaymentList();
        $this->oNovalnetUtil = oxNew('novalnetUtil');
        foreach ($this->_oPaymentList as $oPayment) {
            $sPaymentName = $oPayment->oxpayments__oxid->value;
            // checks the payments are Novalnet payments
            if (in_array($sPaymentName, array( 'novalnetcreditcard', 'novalnetsepa', 'novalnetinvoice', 'novalnetprepayment', 'novalnetpaypal', 'novalnetonlinetransfer', 'novalnetideal', 'novalneteps', 'novalnetgiropay', 'novalnetprzelewy24', 'novalnetbarzahlen' ))) {
                $blPaymentLock   = $this->oNovalnetSession->getVariable('blNovalnetPaymentLock' . $sPaymentName);
                // validates the time to lock the payment
                if ($this->_validateNovalnetConfig() === false || (in_array($sPaymentName, array('novalnetsepa', 'novalnetinvoice')) && ((!empty($blPaymentLock) && $this->oNovalnetSession->getVariable('sNovalnetPaymentLockTime' . $sPaymentName) > time()) || !$this->getGuaranteePaymentStatus($sPaymentName)))) {
                    // hides the payment on checkout page if the payment lock time dosen't exceed current time
                    unset($this->_oPaymentList[$sPaymentName]);
                } elseif (in_array($sPaymentName, array('novalnetsepa', 'novalnetinvoice')) && (!empty($blPaymentLock) && $this->oNovalnetSession->getVariable('sNovalnetPaymentLockTime' . $sPaymentName) <= time())) {
                    // shows the payment on checkout page the payment lock time exceeds current time
                    $this->oNovalnetSession->deleteVariable('blNovalnetPaymentLock' . $sPaymentName);
                    $this->oNovalnetSession->deleteVariable('sNovalnetPaymentLockTime' . $sPaymentName);
                }
            }
        }
        return $this->_oPaymentList;
    }

    /**
     * Gets Novalnet credential value
     *
     * @param string $sConfig
     *
     * @return string
     */
    public function getNovalnetConfig($sConfig)
    {
        $aNovalnetConfig = $this->oNovalnetUtil->aNovalnetConfig;
        $aNovalnetConfig = empty($aNovalnetConfig) ? $aNovalnetConfig : $this->getConfig()->getShopConfVar('aNovalnetConfig', '', 'novalnet');
        if (empty($aNovalnetConfig))
            return false;

        $aNovalnetConfig = array_map('trim', $aNovalnetConfig);

        return $aNovalnetConfig[$sConfig];
    }

    /**
     * Gets remote ip address
     *
     * @return string
     */
    public function getNovalnetRemoteIp()
    {
        return $this->oNovalnetUtil->getIpAddress();
    }

    /**
     * Gets Novalnet notification message
     *
     * @param string $sPaymentId
     *
     * @return string
     */
    public function getNovalnetNotification($sPaymentId)
    {
        return $this->getNovalnetConfig('sBuyerNotify' . $sPaymentId);
    }

    /**
     * Gets Novalnet test mode status for the Novalnet payments
     *
     * @param string $sPaymentId
     *
     * @return boolean
     */
    public function getNovalnetTestmode($sPaymentId)
    {
        return $this->getNovalnetConfig('blTestmode' . $sPaymentId);
    }

    /**
     * Gets the Unique Id
     *
     * @return string
     */
    public function getUniqueid()
    {
        $aKeys = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
        shuffle($aKeys);
        return substr(implode($aKeys, ''), 0, 30);
    }

    /**
     * Get the payment form credentials
     *
     * @param string $sPaymentId
     *
     * @return array
     */
    public function getNovalnetPaymentDetails($sPaymentId)
    {
        $oDb          = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $this->oUser  = $this->getUser();
        $aPaymentType = array('novalnetcreditcard' => '"CREDITCARD"', 'novalnetsepa' => '"DIRECT_DEBIT_SEPA", "GUARANTEED_DIRECT_DEBIT_SEPA"', 'novalnetpaypal' => '"PAYPAL"');
        $iShopType    = (in_array($sPaymentId, array('novalnetsepa', 'novalnetpaypal')) || ($sPaymentId == 'novalnetcreditcard' && $this->getNovalnetConfig('blCC3DActive') != '1' && $this->getNovalnetConfig('blCC3DFraudActive') != '1')) ? $this->getNovalnetConfig('iShopType' . $sPaymentId) : '0';
        $blOneClick   = $this->oNovalnetSession->getVariable('blOneClick' . $sPaymentId);

        $aNovalnetConfig['vendor']    = $this->getNovalnetConfig('iVendorId');
        $aNovalnetConfig['auth_code'] = $this->getNovalnetConfig('sAuthCode');
        $this->oNovalnetUtil->setAffiliateCredentials($aNovalnetConfig, $this->oUser->oxuser__oxcustnr->value);
        $aPaymentDetails['iShopType'] = '';

        // checks the shopping type is one click
        if ($iShopType == '1') {
            $aPaymentDetails['iShowText']  = 1;
            $aResult = $oDb->getRow('SELECT TID, PROCESS_KEY, MASKED_DETAILS FROM novalnet_transaction_detail WHERE CUSTOMER_ID = "' . $this->oUser->oxuser__oxcustnr->value . '" AND PAYMENT_TYPE IN (' . $aPaymentType[$sPaymentId] . ') AND REFERENCE_TRANSACTION = "0" AND ZERO_TRANSACTION = "0" AND MASKED_DETAILS <> "" ORDER BY ORDER_NO DESC');
            if (!empty($aResult['MASKED_DETAILS']) && !empty($aResult['TID'])) {
                $aPaymentDetails               = unserialize($aResult['MASKED_DETAILS']);
                $aPaymentDetails['iShopType']  = $iShopType;
                $aPaymentDetails['blOneClick'] = !empty($blOneClick) ? $blOneClick : 0;
                $this->oNovalnetSession->setVariable('sPaymentRef' . $sPaymentId, $aResult['TID']);
                if ($sPaymentId == 'novalnetsepa')
                    $this->oNovalnetSession->setVariable('sHashRefnovalnetsepa', $aResult['PROCESS_KEY']);
            }
        } else if ($iShopType == '2') {
            $aPaymentDetails['blZeroBook'] = 1;
        }
        if ($sPaymentId != 'novalnetpaypal') {
            $aPaymentDetails['iVendorId'] = $aNovalnetConfig['vendor'];
            $aPaymentDetails['sAuthCode'] = $aNovalnetConfig['auth_code'];
        }
        return $aPaymentDetails;
    }

    /**
     * Gets the guarantee payment activation status for direct debit sepa and invoice
     *
     * @param string $sPaymentId
     *
     * @return boolean
     */
    public function getGuaranteePaymentStatus($sPaymentId)
    {
        $oBasket           = $this->oNovalnetSession->getBasket();
        $dAmount           = str_replace(',', '', number_format($oBasket->getPriceForPayment(), 2)) * 100;
        $blGuaranteeActive = $this->getNovalnetConfig('blGuarantee' . $sPaymentId);
        $this->oNovalnetSession->deleteVariable('blGuaranteeEnabled' . $sPaymentId);
        $this->oNovalnetSession->deleteVariable('blGuaranteeForceDisabled' . $sPaymentId);
        // checks to enable the guarantee payment
        if (!empty($blGuaranteeActive)) {
            $sOxAddressId = oxRegistry::getSession()->getVariable('deladrid');
            $blValidShippingAddress = true;
            if ($sOxAddressId) {
                $oDelAddress  = oxNew('oxaddress');
                $oDelAddress->load($sOxAddressId);
                $oUser        = $this->getUser();
                $blValidShippingAddress = ($oDelAddress->oxaddress__oxcountryid->value == $oUser->oxuser__oxcountryid->value && $oDelAddress->oxaddress__oxzip->value == $oUser->oxuser__oxzip->value && $oDelAddress->oxaddress__oxcity->value == $oUser->oxuser__oxcity->value && $oDelAddress->oxaddress__oxstreet->value == $oUser->oxuser__oxstreet->value && $oDelAddress->oxaddress__oxstreetnr->value == $oUser->oxuser__oxstreetnr->value);
            }
            $dGuaranteeMinAmount = trim($this->getNovalnetConfig('dGuaranteeMinAmount' . $sPaymentId));
            $dGuaranteeMinAmount = empty($dGuaranteeMinAmount) ? 999 : $dGuaranteeMinAmount;
            $error = '';
            if (!$blValidShippingAddress) {
                $error .= '<li> The shipping address must be the same as the billing address </li>';
            }
            if (!in_array($this->oNovalnetUtil->getCountryISO($this->getUser()->oxuser__oxcountryid->value), array('DE', 'AT', 'CH'))) {
                $error .= '<li> Only Germany, Austria or Switzerland are allowed </li>';
            }
            if ($oBasket->getBasketCurrency()->name != 'EUR') {
                $error .= '<li> Only EUR currency allowed </li>';
            }
            if ($dGuaranteeMinAmount > $dAmount) {
                $error .= '<li> Minimum order amount must be'.$dGuaranteeMinAmount.' </li>';
            }
            if (empty($error)) {
                $this->oNovalnetSession->setVariable('blGuaranteeEnabled' . $sPaymentId, 1);
            } elseif ($this->getNovalnetConfig('blGuaranteeForce' . $sPaymentId) != '1') {
                $error_msg = 'The payment cannot be processed, because the basic requirements for the payment guarantee haven\'t been met.</br>';
                $this->oNovalnetSession->setVariable('blGuaranteeForceDisabled' . $sPaymentId, 1);
                $this->oNovalnetSession->setVariable('blGuaranteeErrorMsg' . $sPaymentId, $error_msg.$error);
            }
        }
        return true;
    }

    /**
     * Gets the fraud module activation status for credit card, direct debit sepa and invoice
     *
     * @param string $sPaymentId
     *
     * @return boolean
     */
    public function getFraudModuleStatus($sPaymentId)
    {
        $oSess                     = oxRegistry::getSession();
        $dAmount                   = str_replace(',', '', number_format($oSess->getBasket()->getPriceForPayment(), 2)) * 100;
        $dNovalnetFraudModuleLimit = $this->getNovalnetConfig('dCallbackAmount' . $sPaymentId);

        // checks to enable the fraud module status
        if (!$oSess->getVariable('blGuaranteeEnabled' . $sPaymentId) && !$oSess->getVariable('blGuaranteeForceDisabled' . $sPaymentId) && $this->getNovalnetConfig('iCallback' . $sPaymentId) != '' && (!is_numeric($dNovalnetFraudModuleLimit) || $dAmount >= $dNovalnetFraudModuleLimit) && in_array($this->oNovalnetUtil->getCountryISO($this->getUser()->oxuser__oxcountryid->value), array('DE', 'AT', 'CH'))) {
            $oSess->setVariable('blCallbackEnabled' . $sPaymentId, 1);
            return true;
        }

        $oSess->deleteVariable('blCallbackEnabled' . $sPaymentId);
        return false;
    }

    /**
     * Get the birth date for guarantee payments
     *
     * @return string
     */
    public function getNovalnetBirthDate()
    {
        $oUser = $this->getUser();
        return date('Y-m-d', strtotime(isset($oUser->oxuser__oxbirthdate->rawValue) && $oUser->oxuser__oxbirthdate->rawValue != '0000-00-00' ? $oUser->oxuser__oxbirthdate->rawValue : date('Y-m-d')));
    }

    /**
     * Get the Novalnet signature for the Creditcard form
     *
     * @return string
     */
    public function getNovalnetSignature()
    {
        $sLanguageParam = '&ln=' . $this->oNovalnetUtil->oLang->getLanguageAbbr(); // getting language and set the language parameter
        return base64_encode("vendor=".$this->getNovalnetConfig('iVendorId')."&product=".$this->getNovalnetConfig('iProductId')."&server_ip=".$this->oNovalnetUtil->getIpAddress(true)."&lang=".$sLanguageParam);
    }

    /**
     * Validates Novalnet credentials
     *
     * @return boolean
     */
    private function _validateNovalnetConfig()
    {
        $sProcessKey = $this->getNovalnetConfig('iActivationKey');
        return !empty($sProcessKey);
    }
}
?>
