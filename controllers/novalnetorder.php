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
class novalnetOrder extends novalnetOrder_parent
{
    /**
     * Receives Novalnet response for redirect payment
     *
     * @return boolean
     */
    public function novalnetGatewayReturn()
    {
        $oConfig = $this->getConfig();
        oxRegistry::getSession()->deleteVariable('sNovalnetRedirectURL');
        oxRegistry::getSession()->deleteVariable('aNovalnetRedirectRequest');
        $sKey    = $oConfig->getRequestParameter('key') ? $oConfig->getRequestParameter('key') : $oConfig->getRequestParameter('payment_id');
        // checks to verify the current payment is Novalnet payment
        if (!empty($sKey) && in_array($sKey, array('6', '33', '34', '49', '50', '69', '78'))) {
            $sInputVal3 = $oConfig->getRequestParameter('inputval3');
            $sInputVal4 = $oConfig->getRequestParameter('inputval4');
            $_POST['shop_lang']           = !empty($sInputVal3) ? $sInputVal3 : $_POST['shop_lang'];
            $_POST['stoken']              = !empty($sInputVal4) ? $sInputVal4 : $_POST['stoken'];
            $_POST['actcontrol']          = $oConfig->getTopActiveView()->getViewConfig()->getTopActiveClassName();
            $_POST['sDeliveryAddressMD5'] = $this->getDeliveryAddressMD5();
            if (!$oConfig->getRequestParameter('ord_agb') && $oConfig->getConfigParam( 'blConfirmAGB'))
                $_POST['ord_agb'] = 1;

            if ($oConfig->getConfigParam('blEnableIntangibleProdAgreement')) {
                if (!$oConfig->getRequestParameter('oxdownloadableproductsagreement'))
                    $_POST['oxdownloadableproductsagreement'] = 1;

                if (!$oConfig->getRequestParameter('oxserviceproductsagreement'))
                    $_POST['oxserviceproductsagreement'] = 1;
            }
        }

        oxRegistry::getLang()->setBaseLanguage($oConfig->getRequestParameter('shop_lang'));

        return $this->execute();
    }
}
?>
