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
class novalnetoxShopControl extends novalnetoxShopControl_parent
{
    public function __construct()
    {
        $sNovalnetAffiliateId = oxRegistry::getConfig()->getRequestParameter('nn_aff_id');

        // checks the Novalnet affliate id is passed
        if (!empty($sNovalnetAffiliateId)) {
            $oNovalnetUtil = oxNew('novalnetUtil');
            $oNovalnetUtil->oSession->setVariable('nn_aff_id', $sNovalnetAffiliateId);
        }
    }
}
?>
