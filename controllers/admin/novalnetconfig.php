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

class novalnetConfig extends Shop_Config
{
    protected $_sThisTemplate = 'novalnetconfig.tpl';
    
    public $aPayments = array( 'novalnetonlinetransfer', 'novalnetideal', 'novalnetpaypal', 'novalneteps', 'novalnetgiropay', 'novalnetprzelewy24', 'novalnetbarzahlen', 'novalnetsepa', 'novalnetinvoice', 'novalnetcreditcard', 'novalnetprepayment');

    /**
     * Returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        $oConfig         = oxRegistry::getConfig();
        $sURL = $oConfig->getConfigParam('sShopURL') . $oConfig->getConfigParam('sAdminDir') . "/";

        if ($oConfig->getConfigParam('sAdminSSLURL')) {
            $sURL = $oConfig->getConfigParam('sAdminSSLURL');
        }
        $this->_aViewData['aPaymentUrl'] = $this->paymentDescription();
        $this->_aViewData['aShopUrl'] = $sURL;
        $this->_aViewData['aNovalnetConfig'] = $this->getConfig()->getShopConfVar('aNovalnetConfig', '', 'novalnet');
        return $this->_sThisTemplate;
    }

    /**
     * Saves the Novalnet configuration
     *
     */
    public function save()
    {
        $oLang           = oxRegistry::getLang();
        $aNovalnetConfig = $this->getConfig()->getRequestParameter('aNovalnetConfig');
        
        // checks to validate the Novalnet configuration before saving
        if ($this->_validateNovalnetConfig($aNovalnetConfig) === true) {
            $aNovalnetConfig = array_map('strip_tags', $aNovalnetConfig);
            $customLogo = $this->processFiles();
            $config = $this->getConfig()->getShopConfVar('aNovalnetConfig', '', 'novalnet');
            
            foreach($this->aPayments as $payment) {
                if($config['s'.$payment.'CustomLogo'] == 1) {
                    $aNovalnetConfig['s'.$payment.'CustomLogo'] = 1;
                }
            }
            if(!empty($customLogo)) {
                foreach($customLogo as $key => $value) {
                    $aNovalnetConfig['s'.$value.'CustomLogo'] = 1;
                }
            }
            $this->getConfig()->saveShopConfVar('arr', 'aNovalnetConfig', $aNovalnetConfig, '', 'novalnet');
            parent::save();
        }
    }

    /**
     * Gets server IP
     *
     * @param boolean $blServer
     *
     * @return string
     */
    public function getNovalnetIPAddress($blServer = false)
    {
        $oNovalnetUtil = oxNew('novalnetUtil');
        return $oNovalnetUtil->getIpAddress($blServer);
    }

    /**
     * Validates Novalnet credentials
     *
     * @param array $aNovalnetConfig
     *
     * @return boolean
     */
    private function _validateNovalnetConfig($aNovalnetConfig)
    {
        
        $oLang           = oxRegistry::getLang();
        $aNovalnetConfig = array_map('trim', $aNovalnetConfig);
        if (!function_exists('curl_init') || !function_exists('crc32') || !function_exists('bin2hex') || !function_exists('base64_encode') || !function_exists('base64_decode') || !function_exists('pack')) {
            $this->_aViewData['sNovalnetError'] = $oLang->translateString('NOVALNET_INVALID_PHP_PACKAGE');
            return false;
        }
        if (empty($aNovalnetConfig['iActivationKey'])|| empty($aNovalnetConfig['sTariffId'])) {
            $this->_aViewData['sNovalnetError'] = $oLang->translateString('NOVALNET_INVALID_CONFIG_ERROR');
            return false;
        } elseif ((!empty($aNovalnetConfig['dOnholdLimit'])
                    && !is_numeric($aNovalnetConfig['dOnholdLimit'])) || (!empty($aNovalnetConfig['sReferrerID'])
                    && !is_numeric($aNovalnetConfig['sReferrerID'])) || (!empty($aNovalnetConfig['iGatewayTimeOut'])
                    && !is_numeric($aNovalnetConfig['iGatewayTimeOut']))) {
            $this->_aViewData['sNovalnetError'] = $oLang->translateString('NOVALNET_INVALID_CONFIG_ERROR');
            return false;
        } elseif ((!empty($aNovalnetConfig['sTariffPeriod']) && !preg_match('/[1-9][0-9]*[dmy]{1}$/', $aNovalnetConfig['sTariffPeriod'])) || (!empty($aNovalnetConfig['sTariffPeriod2']) && !preg_match('/[1-9][0-9]*[dmy]{1}$/', $aNovalnetConfig['sTariffPeriod2']))) {
            $this->_aViewData['sNovalnetError'] = $oLang->translateString('NOVALNET_INVALID_TARIFF_PERIOD_ERROR');
            return false;
        } elseif (((!empty($aNovalnetConfig['dTariffPeriod2Amount']) || !empty($aNovalnetConfig['sTariffPeriod2']))
                    && (!is_numeric($aNovalnetConfig['dTariffPeriod2Amount']) || !preg_match('/[a-zA-Z0-9]+$/', $aNovalnetConfig['sTariffPeriod2'])))
                    || (!empty($aNovalnetConfig['sTariffPeriod']) && !preg_match('/[a-zA-Z0-9]+$/', $aNovalnetConfig['sTariffPeriod']))) {
            $this->_aViewData['sNovalnetError'] = $oLang->translateString('NOVALNET_INVALID_CONFIG_ERROR');
            return false;
        } elseif (!empty($aNovalnetConfig['sCallbackMailToAddr']) || !empty($aNovalnetConfig['sCallbackMailBccAddr'])) {
            $oUtils = oxRegistry::getUtils();

            $aToMailAddress  = explode(',', $aNovalnetConfig['sCallbackMailToAddr']);
            $aMailAddress = array_map('trim', $aToMailAddress);
            foreach ($aMailAddress as $sMailAddress) {
                if (!empty($sMailAddress) && !$oUtils->isValidEmail($sMailAddress)) {
                    $this->_aViewData['sNovalnetError'] = $oLang->translateString('NOVALNET_INVALID_CONFIG_ERROR');
                    return false;
                }
            }

            $aBccMailAddress = explode(',', $aNovalnetConfig['sCallbackMailBccAddr']);
            $aMailAddress = array_map('trim', $aBccMailAddress);
            foreach ($aMailAddress as $sMailAddress) {
                if (!empty($sMailAddress) && !$oUtils->isValidEmail($sMailAddress)) {
                    $this->_aViewData['sNovalnetError'] = $oLang->translateString('NOVALNET_INVALID_CONFIG_ERROR');
                    return false;
                }
            }
        }
        if (!empty($aNovalnetConfig['iDueDatenovalnetsepa']) && (!is_numeric($aNovalnetConfig['iDueDatenovalnetsepa']) || $aNovalnetConfig['iDueDatenovalnetsepa'] < 7)) {
            $this->_aViewData['sNovalnetError'] = $oLang->translateString('NOVALNET_INVALID_SEPA_CONFIG_ERROR');
            return false;
        }

        foreach (array('novalnetsepa', 'novalnetinvoice') as $sPaymentName) {
            if ($aNovalnetConfig['dGuaranteeMinAmount' . $sPaymentName] != '' && (!is_numeric($aNovalnetConfig['dGuaranteeMinAmount' . $sPaymentName]) || $aNovalnetConfig['dGuaranteeMinAmount' . $sPaymentName] < 999)) {
                $this->_aViewData['sNovalnetError'] = $oLang->translateString('NOVALNET_INVALID_GUARANTEE_MINIMUM_AMOUNT_ERROR');
                return false;
            }
        }
        return true;
    }

    /**
     * Get current language
     *
     * @return string
     */
    public function getNovalnetLanguage()
    {
        return oxRegistry::getLang()->getLanguageAbbr(oxRegistry::getLang()->getTplLanguage());
    }
    
    /**
     * Uploaded file processor (filters, etc), sets configuration parameters to
     * passed object and returns it.
     *
     * @param object $oObject          object, that parameters are modified according to passed files
     * @param array  $aFiles           name of files to process
     * @param bool   $blUseMasterImage use master image as source for processing
     * @param bool   $blUnique         TRUE - forces new file creation with unique name
     *
     * @return object
     */
    public function processFiles($aFiles = array(), $blUseMasterImage = false, $blUnique = true)
    {
        $aFiles = $_FILES;
        if (isset($aFiles['myfile']['name'])) {

            $oConfig = $this->getConfig();
            $oStr = getStr();

            // A. protection for demoshops - strictly defining allowed file extensions
            $blDemo = (bool) $oConfig->isDemoShop();

            // folder where images will be processed
            $sTmpFolder = $oConfig->getConfigParam("sCompileDir");

            $iNewFilesCounter = 0;
            $aSource = $aFiles['myfile']['tmp_name'];
            $aError = $aFiles['myfile']['error'];
            $sErrorsDescription = '';

            $oEx = oxNew("oxExceptionToDisplay");
            // process all files
            while (list($sKey, $sValue) = each($aFiles['myfile']['name'])) {
                $sSource = $aSource[$sKey];
                $iError = $aError[$sKey];
                $aFiletype = explode("@", $sKey);
                $sKey = $aFiletype[1];
                $sType = $aFiletype[0];
                $sValue = strtolower($sValue);
                $sImagePath = $this->getImagePath($sType);

                // Should translate error to user if file was uploaded
                if (UPLOAD_ERR_OK !== $iError && UPLOAD_ERR_NO_FILE !== $iError) {
                    $sErrorsDescription = $this->translateError($iError);
                    $oEx->setMessage($sErrorsDescription);
                   
                    oxRegistry::get("oxUtilsView")->addErrorToDisplay($oEx, false);
                }
                // checking file type and building final file name
                
                if ($sSource && ($sValue)) {
                    // moving to tmp folder for processing as safe mode or spec. open_basedir setup
                    // usually does not allow file modification in php's temp folder
                    $sProcessPath = $sTmpFolder . basename($sSource);

                    if ($sProcessPath) {

                        if ($blUseMasterImage) {
                            //using master image as source, so only copying it to
                            $blMoved = $this->_copyFile($sSource, $sImagePath . $sValue);
                        } else {
                            $blMoved = $this->_moveImage($sSource, $sImagePath);
                        }
                    }
                }
                
                $oUtilsPic = oxNew('oxUtilsPic');
                $pic = $oUtilsPic->resizeImage($sImagePath, $sImagePath, 70, 50);
                if(!empty($aFiles['myfile']['name'][$sType])) {
                    $payment[] = $sType;
                }
            }
             return $payment;
        }       
    }
    
    /**
     * Returns image storage path
     *
     * @param string $image image type
     *
     * @return string
     */
    
    public function getThumbnailUrl($image)
    {
        $viewConfig = oxNew('oxViewConfig');
        $config = $this->getConfig()->getShopConfVar('aNovalnetConfig', '', 'novalnet');

        $path = $viewConfig->getModuleUrl('novalnet','out/img/');
        if(isset($config['s'.$image.'CustomLogo']) && $config['s'.$image.'CustomLogo'] == 1) {
            $url = $path.$image.'_custom_logo.png';
        } else {
            $url = $path.$image.'.png';
        }
        
        return $url;

    }
    
    public function getImagePath($image)
    {
        $viewConfig = oxNew('oxViewConfig');
        $path = realpath(dirname(__FILE__).'/../../').'/out/img/';
        
        return $path.$image.'_custom_logo.png';

    }
    
    public function translateError($iError)
    {
        $message = 'UPload failed'.$iError;
        return $message;
    }
    
    /**
     * Moves image from source to target location
     *
     * @param string $sSource image location
     * @param string $sTarget image copy location
     *
     * @return bool
     */
    protected function _moveImage($sSource, $sTarget)
    {
        $blDone = false;
        if (!is_dir(dirname($sTarget))) {
            mkdir(dirname($sTarget), 0744, true);
        }
        if ($sSource === $sTarget) {
            $blDone = true;
        } else {
            $blDone = move_uploaded_file($sSource, $sTarget);
        }

        if ($blDone) {
            $blDone = @chmod($sTarget, 0644);
        }

        return $blDone;
    }
    
     /**
     * Copy file from source to target location
     *
     * @param string $sSource file location
     * @param string $sTarget file location
     *
     * @return bool
     */
    protected function _copyFile($sSource, $sTarget)
    {
        $blDone = false;

        if ($sSource === $sTarget) {
            $blDone = true;
        } else {
            $blDone = copy($sSource, $sTarget);
        }

        if ($blDone) {
            $blDone = @chmod($sTarget, 0644);
        }

        return $blDone;
    }
    
        /**
     * Setter for param _iNewFilesCounter which counts how many new files added.
     *
     * @param integer $iNewFilesCounter New files count.
     */
    protected function _setNewFilesCounter($iNewFilesCounter)
    {
        $this->_iNewFilesCounter = (int) $iNewFilesCounter;
    }
    
    public function paymentDescription()
    {
        $oLang           = oxRegistry::getLang()->getLanguageAbbr(oxRegistry::getLang()->getTplLanguage());
        $payment_url = array();
        if ($oLang == 'de') {
            $payment_url['novalnetinvoice'] = 'https://www.novalnet.de/kauf-auf-rechnung-online-payment';
            $payment_url['novalnetsepa'] = 'https://www.novalnet.de/sepa-lastschrift';
            $payment_url['novalnetcreditcard'] = 'https://www.novalnet.de/zahlungsart-kreditkarte';
            $payment_url['novalnetbarzahlen'] = 'https://www.novalnet.de/barzahlen';
            $payment_url['novalnetprepayment'] = 'https://www.novalnet.de/vorkasse-internet-payment';
            $payment_url['novalnetonlinetransfer'] = 'https://www.novalnet.de/online-ueberweisung-sofortueberweisung';
            $payment_url['novalnetgiropay'] = 'https://www.novalnet.de/giropay';
            $payment_url['novalneteps'] = 'https://www.novalnet.de/eps-online-ueberweisung';
            $payment_url['novalnetideal'] = 'https://www.novalnet.de/ideal-online-ueberweisung';
            $payment_url['novalnetpaypal'] = 'https://www.novalnet.de/mit-paypal-weltweit-sicher-verkaufen';
            $payment_url['novalnetprzelewy24'] = 'https://www.novalnet.de/przelewy24';
        } else {
            $payment_url['novalnetinvoice'] = 'https://www.novalnet.com/invoice';
            $payment_url['novalnetsepa'] = 'https://www.novalnet.com/sepa-direct-debit';
            $payment_url['novalnetcreditcard'] = 'https://www.novalnet.com/credit-card';
            $payment_url['novalnetbarzahlen'] = 'https://www.novalnet.com/barzahlen';
            $payment_url['novalnetprepayment'] = 'https://www.novalnet.com/prepayment';
            $payment_url['novalnetonlinetransfer'] = 'https://www.novalnet.com/online-instant-transfer';
            $payment_url['novalnetgiropay'] = 'https://www.novalnet.com/giropay';
            $payment_url['novalneteps'] = 'https://www.novalnet.com/eps-online-payment';
            $payment_url['novalnetideal'] = 'https://www.novalnet.com/ideal';
            $payment_url['novalnetpaypal'] = 'https://www.novalnet.com/paypal';
            $payment_url['novalnetprzelewy24'] = 'https://www.novalnet.com/przelewy24';
        }
        
        return $payment_url;
    }
}
?>
