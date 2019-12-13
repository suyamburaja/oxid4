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
class Novalnet_Thankyou_View extends Novalnet_Thankyou_View_parent {


    public function render() {
        $sTemplate = parent::render();
        $oOrder     = $this->getOrder();
        $this->oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $iOrderNr = $oOrder->oxorder__oxordernr->value;
        if ($oOrder->oxorder__oxpaymenttype->value == 'novalnetbarzahlen') {

            $sSql = 'SELECT PAYMENT_REF FROM novalnet_preinvoice_transaction_detail where ORDER_NO = "' . $iOrderNr . '"';
            $aPaymentRef = $this->oDb->getRow($sSql);
            $sData = unserialize($aPaymentRef['PAYMENT_REF']);
            $aToken = explode('|', $sData['cp_checkout_token']);
            $sBarzahlenLink = ($aToken[1] == 1) ? 'https://cdn.barzahlen.de/js/v2/checkout-sandbox.js' : 'https://cdn.barzahlen.de/js/v2/checkout.js';
            $this->_aViewData['aNovalnetBarzahlensUrl'] = $sBarzahlenLink;
            $this->_aViewData['aNovalnetToken'] = $aToken[0];
        }

        return $sTemplate;
    }

}
