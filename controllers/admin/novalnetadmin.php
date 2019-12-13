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
class novalnetadmin extends Shop_Config
{
    protected $_sThisTemplate = 'novalnetadmin.tpl';
    
    /**
     * Auto Config Url.
     *
     * @var string
     */
    protected $_sAutoConfigUrl = 'https://payport.novalnet.de/autoconfig';

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
     * Get current language
     *
     * @return string
     */
    public function getNovalnetLanguage()
    {
        return oxRegistry::getLang()->getLanguageAbbr(oxRegistry::getLang()->getTplLanguage());
    }

    /**
     * Get Image path
     *
     * @return string
     */
    public function getImagePath($image)
    {
        $viewConfig = oxNew('oxViewConfig');
        return  $viewConfig->getModuleUrl('novalnet', 'out/admin/img/updates/'.$this->getNovalnetLanguage()."/".$image);

    }
    
    /**
     * Get hash value
     *
     */
    public function getMerchantDetails()
    {
        $oNovalnetUtil = oxNew('novalnetUtil');
        $aRequest  = ['hash' => trim($this->getConfig()->getRequestParameter('hash')),
                      'lang' => $oNovalnetUtil->oLang->getLanguageAbbr()];
        $data = http_build_query($aRequest);
        $aResponse = $oNovalnetUtil->doCurlRequest($data, $this->_sAutoConfigUrl, false);
        echo json_encode(['details'=> 'true','response' => $aResponse]);
        exit();
    }
}
