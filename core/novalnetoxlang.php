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

class novalnetOxLang extends novalnetOxLang_parent
{

    /**
     * Returns active shop language id
     *
     * @return string
     */
    public function getBaseLanguage()
    {
        if ($this->_iBaseLanguageId === null) {

            $myConfig = $this->getConfig();
            $blAdmin = $this->isAdmin();

            // languages and search engines
            if ($blAdmin && (($iSeLang = oxRegistry::getConfig()->getRequestParameter('changelang')) !== null)) {
                $this->_iBaseLanguageId = $iSeLang;
            }

            $sKey    = $myConfig->getRequestParameter('key') ? $myConfig->getRequestParameter('key') : $myConfig->getRequestParameter('payment_id');

            // checks to verify the current payment is Novalnet payment
            if (!empty($sKey) && in_array($sKey, array('6', '33', '34', '49', '50', '69', '78'))) {
                unset($_POST['lang']);
            }

            // Recurring order
            $iSignupTid = $myConfig->getRequestParameter('signup_tid');
            $iSubsBilling = $myConfig->getRequestParameter('subs_billing');

            if (!empty($iSignupTid) && !empty($iSubsBilling)) {
               $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
                $sSql     = 'SELECT trans.ORDER_NO FROM novalnet_transaction_detail trans JOIN oxorder o ON o.OXORDERNR = trans.ORDER_NO where trans.tid = "' . $myConfig->getRequestParameter('signup_tid') . '"';
                $aOrderDetails = $oDb->getRow($sSql);
                $aOrderDetails = $oDb->getRow('SELECT OXLANG FROM oxorder where OXORDERNR = "' . $aOrderDetails['ORDER_NO']. '"');
                $_POST['lang'] = $aOrderDetails['OXLANG'];
            }


            if (is_null($this->_iBaseLanguageId)) {
                $this->_iBaseLanguageId = oxRegistry::getConfig()->getRequestParameter('lang');
            }

            //or determining by domain
            $aLanguageUrls = $myConfig->getConfigParam('aLanguageURLs');

            if (!$blAdmin && is_array($aLanguageUrls)) {
                foreach ($aLanguageUrls as $iId => $sUrl) {
                    if ($sUrl && $myConfig->isCurrentUrl($sUrl)) {
                        $this->_iBaseLanguageId = $iId;
                        break;
                    }
                }
            }

            if (is_null($this->_iBaseLanguageId)) {
                $this->_iBaseLanguageId = oxRegistry::getConfig()->getRequestParameter('language');
                if (!isset($this->_iBaseLanguageId)) {
                    $this->_iBaseLanguageId = oxRegistry::getSession()->getVariable('language');
                }
            }

            // if language still not set and not search engine browsing,
            // getting language from browser
            if (is_null($this->_iBaseLanguageId) && !$blAdmin && !oxRegistry::getUtils()->isSearchEngine()) {

                // getting from cookie
                $this->_iBaseLanguageId = oxRegistry::get("oxUtilsServer")->getOxCookie('language');

                // getting from browser
                if (is_null($this->_iBaseLanguageId)) {
                    $this->_iBaseLanguageId = $this->detectLanguageByBrowser();
                }
            }

            if (is_null($this->_iBaseLanguageId)) {
                $this->_iBaseLanguageId = $myConfig->getConfigParam('sDefaultLang');
            }

            $this->_iBaseLanguageId = (int) $this->_iBaseLanguageId;

            // validating language
            $this->_iBaseLanguageId = $this->validateLanguage($this->_iBaseLanguageId);

            oxRegistry::get("oxUtilsServer")->setOxCookie('language', $this->_iBaseLanguageId);
        }

        return $this->_iBaseLanguageId;
    }

}
