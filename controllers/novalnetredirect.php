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
class novalnetRedirect extends oxUBase
{
    /**
     * Returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $oUser    = oxNew('oxuser');
        $oSession = oxRegistry::getSession();
        $sUserID  = $oSession->getVariable('usr');
        $oUser->load($sUserID);
        if (!$oUser->getUser())
            oxRegistry::getUtils()->redirect(oxRegistry::getConfig()->getShopMainUrl(), false);

        $this->_aViewData['sNovalnetFormAction'] = $oSession->getVariable('sNovalnetRedirectURL');
        $this->_aViewData['aNovalnetFormData']   = $oSession->getVariable('aNovalnetRedirectRequest');

        // checks to verify the redirect payment details available
        if (empty($this->_aViewData['sNovalnetFormAction']) || empty($this->_aViewData['aNovalnetFormData']))
            oxRegistry::getUtils()->redirect(oxRegistry::getConfig()->getShopMainUrl() . 'index.php?cl=payment', false);
        elseif (!empty($this->_aViewData['sNovalnetFormAction']) && !empty($this->_aViewData['aNovalnetFormData']))
            return 'novalnetredirect.tpl';

        oxRegistry::getUtils()->redirect(oxRegistry::getConfig()->getShopMainUrl(), false);
    }
}
