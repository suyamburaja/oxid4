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

/**
 * Metadata version
 */
$sMetadataVersion = '1.2';

/**
 * Module information
 */
$aModule = array(
                   'id'          => 'novalnet',
                   'title'       => 'Novalnet',
                   'url'         => 'https://www.novalnet.de',
                   'email'       => 'technic@novalnet.de',
                   'thumbnail'   => 'novalnet.png',
                   'version'     => '11.1.6',
                   'author'      => 'Novalnet',
                   'description' => array(
                                           'de' => 'Novalnet Zahlungsmodul',
                                           'en' => 'Novalnet Payment module'
                                         ),
                   'events'      => array(
                                           'onActivate'   => 'novalnetEvents::onActivate',
                                           'onDeactivate' => 'novalnetEvents::onDeactivate'
                                         ),
                   'files'       => array(
                                           'novalnetevents'   => 'oe/novalnet/core/novalnetevents.php',
                                           'novalnetconfig'   => 'oe/novalnet/controllers/admin/novalnetconfig.php',
                                           'novalnetadmin'    => 'oe/novalnet/controllers/admin/novalnetadmin.php',
                                           'novalnetutil'     => 'oe/novalnet/classes/novalnetutil.php',
                                           'novalnetcallback' => 'oe/novalnet/controllers/novalnetcallback.php',
                                           'novalnetredirect' => 'oe/novalnet/controllers/novalnetredirect.php'
                                         ),
                   'templates'   => array(
                                           'novalnetconfig.tpl'           => 'oe/novalnet/views/admin/tpl/novalnetconfig.tpl',
                                           'novalnetadmin.tpl'            => 'oe/novalnet/views/admin/tpl/novalnetadmin.tpl',
                                           'novalnetredirect.tpl'         => 'oe/novalnet/views/tpl/novalnetredirect.tpl',
                                           'novalnetcallback.tpl'         => 'oe/novalnet/views/tpl/novalnetcallback.tpl',
                                           'novalnetcreditcard.tpl'       => 'oe/novalnet/views/blocks/page/checkout/inc/novalnetcreditcard.tpl',
                                           'novalnetsepa.tpl'             => 'oe/novalnet/views/blocks/page/checkout/inc/novalnetsepa.tpl',
                                           'novalnetinvoice.tpl'          => 'oe/novalnet/views/blocks/page/checkout/inc/novalnetinvoice.tpl',
                                           'novalnetpaypal.tpl'           => 'oe/novalnet/views/blocks/page/checkout/inc/novalnetpaypal.tpl',
                                           'novalnetcreditcardmobile.tpl' => 'oe/novalnet/views/blocks/mobile/page/checkout/inc/novalnetcreditcardmobile.tpl',
                                           'novalnetsepamobile.tpl'       => 'oe/novalnet/views/blocks/mobile/page/checkout/inc/novalnetsepamobile.tpl',
                                           'novalnetinvoicemobile.tpl'    => 'oe/novalnet/views/blocks/mobile/page/checkout/inc/novalnetinvoicemobile.tpl',
                                           'novalnetpaypalmobile.tpl'     => 'oe/novalnet/views/blocks/mobile/page/checkout/inc/novalnetpaypalmobile.tpl'
                                         ),
                   'blocks'      => array(
                                           array(
                                                  'template' => 'page/checkout/payment.tpl',
                                                  'block'    => 'select_payment',
                                                  'file'     => '/views/blocks/page/checkout/novalnetpayments.tpl'
                                                ),
                                           array(
                                                  'template' => 'page/checkout/payment.tpl',
                                                  'block'    => 'mb_select_payment',
                                                  'file'     => '/views/blocks/mobile/page/checkout/novalnetpayments.tpl'
                                                ),
                                           array(
                                                  'template' => 'email/html/order_cust.tpl',
                                                  'block'    => 'email_html_order_cust_username',
                                                  'file'     => '/views/blocks/email/html/novalnettransaction.tpl'
                                                ),
                                           array(
                                                  'template' => 'email/html/order_owner.tpl',
                                                  'block'    => 'email_html_order_owner_username',
                                                  'file'     => '/views/blocks/email/html/novalnettransaction.tpl'
                                                ),
                                           array(
                                                  'template' => 'email/plain/order_cust.tpl',
                                                  'block'    => 'email_plain_order_cust_username',
                                                  'file'     => '/views/blocks/email/html/novalnettransaction.tpl'
                                                ),
                                           array(
                                                  'template' => 'email/plain/order_owner.tpl',
                                                  'block'    => 'email_plain_order_ownerusername',
                                                  'file'     => '/views/blocks/email/html/novalnettransaction.tpl'
                                                ),
                                           array(
                                                  'template' => 'page/account/order.tpl',
                                                  'block'    => 'account_order_history',
                                                  'file'     => '/views/blocks/page/account/novalnetorder.tpl'
                                                ),
                                           array(
                                                  'template' => 'page/checkout/order.tpl',
                                                  'block'    => 'checkout_order_btn_confirm_bottom',
                                                  'file'     => '/views/blocks/page/checkout/novalnetorder.tpl'
                                                ),
                                           array(
                                                  'template' => 'order_overview.tpl',
                                                  'block'    => 'admin_order_overview_checkout',
                                                  'file'     => '/views/admin/blocks/novalnetcomments.tpl'
                                                ),
                                           array(
                                                  'template' => 'order_overview.tpl',
                                                  'block'    => 'admin_order_overview_dynamic',
                                                  'file'     => '/views/admin/blocks/novalnetdynamic.tpl'
                                                ),
                                           array(
                                                  'template' => 'order_overview.tpl',
                                                  'block'    => 'admin_order_overview_export',
                                                  'file'     => '/views/admin/blocks/novalnetextensions.tpl'
                                                ),
                                           array(
                                                 'template' => 'page/checkout/thankyou.tpl',
                                                 'block'    => 'checkout_thankyou_proceed',
                                                 'file'     => 'views/blocks/page/checkout/novalnet_checkout_thankyou.tpl'
                                            ),
                                         ),
                   'extend'      => array(
                                            'order_overview'   => 'oe/novalnet/controllers/admin/novalnetorder_overview',
                                            'account_order'    => 'oe/novalnet/controllers/novalnetaccount_order',
                                            'order'            => 'oe/novalnet/controllers/novalnetorder',
                                            'payment'          => 'oe/novalnet/controllers/novalnetpayment',
                                            'oxinputvalidator' => 'oe/novalnet/core/novalnetoxinputvalidator',
                                            'oxshopcontrol'    => 'oe/novalnet/core/novalnetoxshopcontrol',
                                            'oxorder'          => 'oe/novalnet/models/novalnetoxorder',
                                            'oxpaymentgateway' => 'oe/novalnet/models/novalnetoxpaymentgateway',
                                            'oxlang'           => 'oe/novalnet/core/novalnetoxlang',
                                            'thankyou'         => 'oe/novalnet/controllers/novalnet_thankyou_view',
                                         )
                );
