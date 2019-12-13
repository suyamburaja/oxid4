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
class novalnetAccount_Order extends novalnetAccount_Order_parent {

    /**
     * Gets current theme
     *
     * @return string
     */
    public function novalnetGetTheme()
    {
        $oTheme = oxNew('oxTheme');
        return $oTheme->getActiveThemeId();
    }

    /**
     * Gets Novalnet payment name for given order
     *
     * @param string $sPaymentType
     *
     * @return string
     */
    public function getNovalnetPaymentName($sPaymentType)
    {
        $oPayment = oxNew('oxPayment');
        $oPayment->load($sPaymentType);
        return $oPayment->oxpayments__oxdesc->rawValue;
    }


    public function getNovalnetSubscriptionStatus($iOrderNo)
    {
        $aSubsDetails = $this->_getNovalnetTransDetails($iOrderNo);
            return ($aSubsDetails['GATEWAY_STATUS'] != '103' && !empty($aSubsDetails['SUBS_ID']) && empty($aSubsDetails['TERMINATION_REASON']));
    }

    /**
     * Gets Novalnet transaction details for given order
     *
     * @param integer $iOrderNo
     *
     * @return array
     */
    private function _getNovalnetTransDetails($iOrderNo)
    {
        $this->oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);

        $sSQL = 'SELECT trans.VENDOR_ID, trans.PRODUCT_ID, trans.TARIFF_ID, trans.AUTH_CODE, trans.PAYMENT_ID, trans.TID, trans.GATEWAY_STATUS, subs.TID AS SUB_TID, subs.SUBS_ID, subs.TERMINATION_REASON FROM novalnet_transaction_detail trans LEFT JOIN novalnet_subscription_detail subs ON trans.ORDER_NO = subs.ORDER_NO WHERE trans.ORDER_NO = "' . $iOrderNo . '"';

        return $this->oDb->getRow($sSQL);
    }

    /**
     * Gets Novalnet subscription cancellation reason
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
                      $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCEL_REASON_11')
                    );
    }

    /**
     * Cancels Novalnet subscription
     *
     */
    public function cancelNovalnetSuscription()
    {
        $aData = oxRegistry::getConfig()->getRequestParameter('novalnet');
        $oLang = oxRegistry::getLang();
        $oNovalnetUtil = oxNew('novalnetUtil');
        $aTransDetails = $this->_getNovalnetTransDetails($aData['iOrderNo']);
        if (!empty($aData['sCancelReason'])) {
            $aRequest['vendor']        = $aTransDetails['VENDOR_ID'];
            $aRequest['product']       = $aTransDetails['PRODUCT_ID'];
            $aRequest['tariff']        = $aTransDetails['TARIFF_ID'];
            $aRequest['auth_code']     = $aTransDetails['AUTH_CODE'];
            $aRequest['key']           = $aTransDetails['PAYMENT_ID'];
            $aRequest['tid']           = $aTransDetails['SUB_TID'];
            $aRequest['cancel_sub']    = 1;
            $aRequest['remote_ip']     = $oNovalnetUtil->getIpAddress();
            $aRequest['cancel_reason'] = $aData['sCancelReason'];

            $aResponse = $oNovalnetUtil->doCurlRequest($aRequest, 'https://payport.novalnet.de/paygate.jsp');

            if ($aResponse['status'] == '100') {
                $sMessage = $oLang->translateString('NOVALNET_SUBSCRIPTION_CANCELED_MESSAGE') . $aData['sCancelReason'];

                $sSQL = 'UPDATE novalnet_subscription_detail SET TERMINATION_REASON = "' . $aData['sCancelReason'] . '", TERMINATION_AT = "' . date('Y-m-d H:i:s') . '" WHERE TID = "' . $aRequest['tid'] . '"';
                $this->oDb->execute($sSQL);

                $sSQL = 'SELECT SUBS_ID FROM novalnet_subscription_detail WHERE TID='.$aRequest['tid'];
                $aSubId = $this->oDb->getRow($sSQL);

                $sSQL = 'SELECT ORDER_NO from novalnet_subscription_detail WHERE SUBS_ID  = "'.$aSubId['SUBS_ID'].'"';

                $aOrderNr = $this->oDb->getAll($sSQL);
                foreach($aOrderNr as $skey => $dValue) {
                    $sSQL = 'UPDATE oxorder SET NOVALNETCOMMENTS = CONCAT(IF(NOVALNETCOMMENTS IS NULL, "", NOVALNETCOMMENTS), "' . $sMessage . '") WHERE OXORDERNR = "' . $dValue['ORDER_NO'] . '"';
                    $this->oDb->execute($sSQL);
                }
            } else {
                $sMessage = $oNovalnetUtil->setNovalnetPaygateError($aResponse);
                echo '<script type="text/javascript">alert("' . $sMessage . '");</script>';
            }
        }
        $oNovalnetUtil->oUtils->redirect($oNovalnetUtil->oConfig->getShopCurrentURL().'cl=account_order', false);
    }


    /**
     * Return shop edition (EE|CE|PE)
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->getConfig()->getEdition();
    }
}
