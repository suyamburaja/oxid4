[{include file="headitem.tpl" title="Novalnet Configuration"}]
[{oxscript include="js/libs/jquery.min.js"}]
[{oxscript include=$oViewConf->getModuleUrl('novalnet', 'out/admin/src/js/novalnetconfig.js')}]
[{assign var="oConfig" value=$oViewConf->getConfig()}]
[{assign var="oSession" value=$oConfig->getSession()}]

<link rel="stylesheet" href="[{$oViewConf->getModuleUrl('novalnet', 'out/admin/src/css/novalnetconfig.css')}]" type="text/css" />
<div align="right">
    <a href="[{oxmultilang ident="NOVALNET_LINK_URL"}]" title="Novalnet AG" target="_new">
        <img src="[{$oViewConf->getModuleUrl('novalnet')}]novalnet.png" alt="Novalnet" border="0" >
    </a>
</div>
[{if $sNovalnetError != ''}]
    <div id="novalnet_admin_config_error">
        <div style="color:red;">[{ $sNovalnetError }]</div>
    </div>
[{/if}]
<hr/>
<div style="padding:20px;" id="novalnet_config" >
    <form name="myedit" id="myedit" class="novalnet_config_form" action="[{$oViewConf->getSelfLink()}]" method="post" enctype= "multipart/form-data">
        <input type="hidden" name="cl" value="novalnetconfig">
        <input type="hidden" name="fnc" value="save">
        [{ $oViewConf->getHiddenSid() }]
        <div class="novalnetCont">
            <div class="novalnet_config_title">
                <span payment_id="global_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                <b>[{ oxmultilang ident="NOVALNET_GLOBAL_CONFIGURATION" }]</b>
                <div class="configCnt" style="display:none;" id="global_config">
                    <dl>
                        <dd>
                            <span class="configDesc" id="admin_config_message">[{ oxmultilang ident="NOVALNET_ADMIN_CONFIG_MESSAGE" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_PRODUCT_ACTIVATION_KEY_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" id="novalnet_activation_key" name="aNovalnetConfig[iActivationKey]" value="[{$aNovalnetConfig.iActivationKey}]" />
                            <input type="hidden" id='system_ip' value="[{$oView->getNovalnetIPAddress(true)}]" />
                            <input type="hidden" id='remote_ip' value="[{$oView->getNovalnetIPAddress(false)}]" />
                            <input type="hidden" id='language' value="[{$oView->getNovalnetLanguage()|upper}]" />
                            <input type="hidden" id="stoken" name="stoken" value="[{$oSession->getSessionChallengeToken()}]">
                            <input type="hidden" id="getUrl" name="getUrl" value="[{ $aShopUrl }]">
                            <input type="hidden" id='ajax_process' value="1" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PRODUCT_ACTIVATION_KEY_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dd style="display:none;">
                            <input type="text" id="novalnet_vendorid" name="aNovalnetConfig[iVendorId]" value="[{$aNovalnetConfig.iVendorId}]" readonly />
                        </dd>
                    </dl>
                    <dl style="display:none;">
                        <dd>
                            <input type="text" id="novalnet_authcode" name="aNovalnetConfig[sAuthCode]" value="[{$aNovalnetConfig.sAuthCode}]" readonly />
                        </dd>
                    </dl>
                    <dl style="display:none;">
                        <dd>
                            <input type="text" id="novalnet_productid" name="aNovalnetConfig[iProductId]" value="[{$aNovalnetConfig.iProductId}]" readonly />
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_TARIFF_ID_TITLE" }]</label>
                        </dt>
                        <dd>
                            <select id="novalnet_tariffid"  name="aNovalnetConfig[sTariffId]" value="[{$aNovalnetConfig.sTariffId}]">
                                <option value="" [{if $aNovalnetConfig.sTariffId == ''}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_OPTION_NONE" }]</option>
                            </select>
                            <input type="hidden" id="novalnet_saved_tariff" value="[{$aNovalnetConfig.sTariffId}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TARIFF_ID_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl style="display:none;">
                        <dd>
                            <input id="novalnet_accesskey" type="text" name="aNovalnetConfig[sAccessKey]" value="[{$aNovalnetConfig.sAccessKey}]" readonly />
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_PROXY_SERVER_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sProxy]" value="[{$aNovalnetConfig.sProxy}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PROXY_SERVER_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_GATEWAY_TIMEOUT_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[iGatewayTimeOut]" value="[{if isset($aNovalnetConfig.iGatewayTimeOut)}][{$aNovalnetConfig.iGatewayTimeOut}][{else}]240[{/if}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_GATEWAY_TIMEOUT_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_REFERRER_ID_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sReferrerID]" value="[{$aNovalnetConfig.sReferrerID}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_REFERRER_ID_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt class='configTitle'>[{ oxmultilang ident="NOVALNET_LOGO_CONFIGURATION_TITLE" }]</dt>
                        <dd class='configHeadingDesc'>
                            [{ oxmultilang ident="NOVALNET_LOGO_CONFIGURATION_DESCRIPTION" }]
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blPaymentLogo]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blPaymentLogo]" value="1" [{if $aNovalnetConfig.blPaymentLogo == 1 || $aNovalnetConfig.blPaymentLogo == ''}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_CHECKOUT_PAYMENT_LOGO_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_CHECKOUT_PAYMENT_LOGO_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt class='configTitle'>[{ oxmultilang ident="NOVALNET_SUBSCRIPTION_CONFIGURATION_TITLE" }]</dt>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_TARIFF_PERIOD_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sTariffPeriod]" value="[{$aNovalnetConfig.sTariffPeriod}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TARIFF_PERIOD_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_TARIFF_PERIOD2_AMOUNT_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[dTariffPeriod2Amount]" value="[{$aNovalnetConfig.dTariffPeriod2Amount}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TARIFF_PERIOD2_AMOUNT_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_TARIFF_PERIOD2_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sTariffPeriod2]" value="[{$aNovalnetConfig.sTariffPeriod2}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TARIFF_PERIOD2_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt class='configTitle'>[{ oxmultilang ident="NOVALNET_CALLBACKSCRIPT_CONFIGURATION_TITLE" }]</dt>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blCallbackTestMode]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blCallbackTestMode]" value="1" [{if $aNovalnetConfig.blCallbackTestMode == 1}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_CALLBACK_TEST_MODE_TITLE" }]
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_CALLBACK_TEST_MODE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blCallbackMail]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blCallbackMail]" value="1" [{if $aNovalnetConfig.blCallbackMail}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_CALLBACK_ENABLE_MAIL_TITLE" }]
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_CALLBACK_TO_ADDRESS_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sCallbackMailToAddr]" value="[{if  isset($aNovalnetConfig.sCallbackMailToAddr)}][{$aNovalnetConfig.sCallbackMailToAddr}][{/if}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_CALLBACK_TO_ADDRESS_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_CALLBACK_BCC_ADDRESS_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sCallbackMailBccAddr]" value="[{if  isset($aNovalnetConfig.sCallbackMailBccAddr)}][{$aNovalnetConfig.sCallbackMailBccAddr}][{/if}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_CALLBACK_BCC_ADDRESS_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_NOTIFY_URL_TITLE" }]</label>
                        </dt>
                        <dd>
                            [{assign var="oConf" value=$oViewConf->getConfig()}]
                            <input type="text" name="aNovalnetConfig[sNotifyURL]" value="[{if $aNovalnetConfig.sNotifyURL == ''}][{$oConf->getShopUrl()}]?cl=novalnetcallback&fnc=handlerequest[{else}][{$aNovalnetConfig.sNotifyURL}][{/if}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_NOTIFY_URL_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="novalnet_config_title">
                <span payment_id="creditcard_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                <b>[{ oxmultilang ident="NOVALNET_CREDITCARD" }]</b>
                <div class="configCnt" style="display:none;" id="creditcard_config">
                     <dl>
                        <dd>
                            <span class="configDesc" id="admin_payment_message">[{ oxmultilang ident="NOVALNET_PAYMENT_MESSAGE" }] <a class="novalnet_config_link" href="[{$aPaymentUrl.novalnetcreditcard}]" target="_blank" style="color:#0080c9;"><b>[{ oxmultilang ident="NOVALNET_CREDITCARD" }]</b></a></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>Customize payment logo</label>
                        </dt>
                        [{assign var="sThumbUrl" value=$oView->getThumbnailUrl("novalnetcreditcard")}]
                        <dd>
                            <div class="picPreview"><img src="[{$sThumbUrl}]"></div>
                        </dd>
                        <dd>
                            <input class="editinput" name="myfile[novalnetcreditcard]" type="file">
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYMENT_LOGO_DESC" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blTestmodenovalnetcreditcard]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blTestmodenovalnetcreditcard]" value="1" [{if $aNovalnetConfig.blTestmodenovalnetcreditcard}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_TEST_MODE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TEST_MODE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <dd id="payment_action">
                            <input type="hidden" id="payment_name" name="payment_name" value="novalnetcreditcard" />
                            <select name="aNovalnetConfig[sPaymentActionnovalnetcreditcard]" id="sPaymentActionnovalnetcreditcard">
                                <option value="capture" [{if $aNovalnetConfig.sPaymentActionnovalnetcreditcard == "capture"}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_CAPTURE" }]</option>
                                <option value="authorize" [{if $aNovalnetConfig.sPaymentActionnovalnetcreditcard == "authorize"}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_AUTHORIZE" }]</option>
                            </select>
                    </dl>
                    [{assign var="cconholddisplay" value="none"}]
                    [{if $aNovalnetConfig.sPaymentActionnovalnetcreditcard == "capture"}]
                    [{assign var="cconholddisplay" value="none"}]
                    [{/if}]
                    [{if $aNovalnetConfig.sPaymentActionnovalnetcreditcard == "authorize"}]
                    [{assign var="cconholddisplay" value="block"}]
                    [{/if}]
                      <dl id="novalnetcreditcard_manualcheck" style="display:[{$cconholddisplay}];">
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_MANUAL_CHECK_LIMIT_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[dOnholdLimitnovalnetcreditcard]" value="[{$aNovalnetConfig.dOnholdLimitnovalnetcreditcard}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_MANUAL_CHECK_LIMIT_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blCC3DActive]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blCC3DActive]" value="1" [{if $aNovalnetConfig.blCC3DActive}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_CREDITCARD_3D_ACTIVE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_CREDITCARD_3D_ACTIVE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blCC3DFraudActive]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blCC3DFraudActive]" value="1" [{if $aNovalnetConfig.blCC3DFraudActive}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_CREDITCARD_3D_FRAUD_ACTIVE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_CREDITCARD_3D_FRAUD_ACTIVE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blAmexActive]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blAmexActive]" value="1" [{if $aNovalnetConfig.blAmexActive}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_CREDITCARD_AMEX_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_CREDITCARD_AMEX_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blMaestroActive]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blMaestroActive]" value="1" [{if $aNovalnetConfig.blMaestroActive}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_CREDITCARD_MAESTRO_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_CREDITCARD_MAESTRO_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_SHOP_TYPE_TITLE" }]</label>
                        </dt>
                        <dd>
                            <select name="aNovalnetConfig[iShopTypenovalnetcreditcard]">
                                <option value="" [{if $aNovalnetConfig.iShopTypenovalnetcreditcard == ''}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_OPTION_NONE" }]</option>
                                <option value="1" [{if $aNovalnetConfig.iShopTypenovalnetcreditcard == 1}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_ONE_CLICK_SHOP" }]</option>
                                <option value="2" [{if $aNovalnetConfig.iShopTypenovalnetcreditcard == 2}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_ZERO_AMOUNT_BOOK" }]</option>
                            </select>
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_SHOP_TYPE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sBuyerNotifynovalnetcreditcard]" value="[{$aNovalnetConfig.sBuyerNotifynovalnetcreditcard}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                      <dl>
                        <dt class="configTitle">[{ oxmultilang ident="NOVALNET_IFRAME_CONFIGURATION_TITLE" }]</dt>
                        <dd>
                            [{assign var="sCreditcardLabel" value="font-weight:bold;"}]
                            [{assign var="sCreditcardInput" value="background-color: #fff; background-image: none; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; color: #555; display: block; font-size: 14px; height: 34px; line-height: 1.42857; padding: 6px 12px; transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s; width: 75%; font-family: inherit; float: left; color: #555;"}]
                            [{assign var="sCreditcardCss" value="body{font-size:14px;font-family: Raleway,'Helvetica Neue',Helvetica,Arial,sans-serif; margin-bottom: 0;} .label-group{padding-top:10px;text-align:right;width:25%;} .input-group{width:70%;}"}]
                           
                            <table>
                                <tr>
                                    <th style="text-align:left;" colspan="2">[{ oxmultilang ident="NOVALNET_IFRAME_STYLE_CONFIGURATION_TITLE" }]</th>
                                </tr>
                                <tr>
                                    <td>[{ oxmultilang ident="NOVALNET_IFRAME_LABEL" }]</td>
                                    <td>
                                        <input type="text" name="aNovalnetConfig[sCreditcardDefaultLabel]" value="[{if !isset($aNovalnetConfig.sCreditcardDefaultLabel)}][{$sCreditcardLabel}][{else}][{$aNovalnetConfig.sCreditcardDefaultLabel}][{/if}]" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>[{ oxmultilang ident="NOVALNET_IFRAME_INPUT" }]</td>
                                    <td>
                                        <input type="text" name="aNovalnetConfig[sCreditcardDefaultInput]" value="[{if !isset($aNovalnetConfig.sCreditcardDefaultInput)}][{$sCreditcardInput}][{else}][{$aNovalnetConfig.sCreditcardDefaultInput}][{/if}]" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>[{ oxmultilang ident="NOVALNET_IFRAME_CSS" }]</td>
                                    <td>
                                        <input type="text" name="aNovalnetConfig[sCreditcardDefaultCss]" value="[{if !isset($aNovalnetConfig.sCreditcardDefaultCss)}][{$sCreditcardCss}][{else}][{$aNovalnetConfig.sCreditcardDefaultCss}][{/if}]" />
                                    </td>
                                </tr>
                            </table>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="novalnet_config_title">
                <span payment_id="sepa_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                <b>[{ oxmultilang ident="NOVALNET_SEPA" }]</b>
                <div class="configCnt" style="display:none;" id="sepa_config">
                    <dl>
                        <dd>
                            <span class="configDesc" id="admin_payment_message">[{ oxmultilang ident="NOVALNET_PAYMENT_MESSAGE" }] <a class="novalnet_config_link" href="[{$aPaymentUrl.novalnetsepa}]" target="_blank" style="color:#0080c9;"><b>[{ oxmultilang ident="NOVALNET_SEPA" }]</b></a></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>Customize payment logo</label>
                        </dt>
                        [{assign var="sThumbUrl" value=$oView->getThumbnailUrl("novalnetsepa")}]
                        <dd>
                            <div class="picPreview"><img src="[{$sThumbUrl}]"></div>
                        </dd>
                        <dd>
                            <input class="editinput" name="myfile[novalnetsepa]" type="file">
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYMENT_LOGO_DESC" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blTestmodenovalnetsepa]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blTestmodenovalnetsepa]" value="1" [{if
                            $aNovalnetConfig.blTestmodenovalnetsepa}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_TEST_MODE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TEST_MODE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <dd id="payment_action">
                            <input type="hidden" id="payment_name" name="payment_name" value="novalnetsepa" />
                            <select name="aNovalnetConfig[sPaymentActionnovalnetsepa]" id="sPaymentActionnovalnetsepa">
                                <option value="capture" [{if $aNovalnetConfig.sPaymentActionnovalnetsepa == "capture"}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_CAPTURE" }]</option>
                                <option value="authorize" [{if $aNovalnetConfig.sPaymentActionnovalnetsepa == "authorize"}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_AUTHORIZE" }]</option>
                            </select>
                    </dl>
                    [{assign var="sepaonholddisplay" value="none"}]
                    [{if $aNovalnetConfig.sPaymentActionnovalnetsepa == "capture"}]
                    [{assign var="sepaonholddisplay" value="none"}]
                    [{/if}]
                    [{if $aNovalnetConfig.sPaymentActionnovalnetsepa == "authorize"}]
                    [{assign var="sepaonholddisplay" value="block"}]
                    [{/if}]
                    <dl id="novalnetsepa_manualcheck" style="display:[{$sepaonholddisplay}];">
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_MANUAL_CHECK_LIMIT_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[dOnholdLimitnovalnetsepa]" value="[{$aNovalnetConfig.dOnholdLimitnovalnetsepa}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_MANUAL_CHECK_LIMIT_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_TITLE" }]</label>
                        </dt>
                        <dd>
                            <select name="aNovalnetConfig[iCallbacknovalnetsepa]">
                                <option value="" [{if $aNovalnetConfig.iCallbacknovalnetsepa == ''}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_OPTION_NONE" }]</option>
                                <option value="1" [{if $aNovalnetConfig.iCallbacknovalnetsepa == 1}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_OPTION_CALL" }]</option>
                                <option value="2" [{if $aNovalnetConfig.iCallbacknovalnetsepa == 2}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_OPTION_SMS" }]</option>
                            </select>
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_AMOUNT_LIMIT_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[dCallbackAmountnovalnetsepa]" value="[{$aNovalnetConfig.dCallbackAmountnovalnetsepa}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_AMOUNT_LIMIT_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_SEPA_DUE_DATE_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[iDueDatenovalnetsepa]" value="[{$aNovalnetConfig.iDueDatenovalnetsepa}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_SEPA_DUE_DATE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_SHOP_TYPE_TITLE" }]</label>
                        </dt>
                        <dd>
                            <select name="aNovalnetConfig[iShopTypenovalnetsepa]">
                                <option value="" [{if $aNovalnetConfig.iShopTypenovalnetsepa == ''}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_OPTION_NONE" }]</option>
                                <option value="1" [{if $aNovalnetConfig.iShopTypenovalnetsepa == 1}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_ONE_CLICK_SHOP" }]</option>
                                <option value="2" [{if $aNovalnetConfig.iShopTypenovalnetsepa == 2}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_ZERO_AMOUNT_BOOK" }]</option>
                            </select>
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_SHOP_TYPE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sBuyerNotifynovalnetsepa]" value="[{$aNovalnetConfig.sBuyerNotifynovalnetsepa}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt class='configTitle'>[{ oxmultilang ident="NOVALNET_GUARANTEE_CONFIGURATION_TITLE" }]</dt>
                        <dd class="configHeadingDesc">[{ oxmultilang ident="NOVALNET_GUARANTEE_CONFIGURATION_DESCRIPTION" }]</dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blGuaranteenovalnetsepa]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blGuaranteenovalnetsepa]" value="1" [{if
                            $aNovalnetConfig.blGuaranteenovalnetsepa}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_GUARANTEE_PAYMENT_TITLE" }]
                        </dd>
                    </dl>
                    <dl>
                        <dt><label>[{ oxmultilang ident="NOVALNET_GUARANTEE_MINIMUM_AMOUNT_TITLE" }]</label></dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[dGuaranteeMinAmountnovalnetsepa]" value="[{$aNovalnetConfig.dGuaranteeMinAmountnovalnetsepa}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_GUARANTEE_MINIMUM_AMOUNT_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blGuaranteeForcenovalnetsepa]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blGuaranteeForcenovalnetsepa]" value="1" [{if !isset($aNovalnetConfig.blGuaranteeForcenovalnetsepa) || $aNovalnetConfig.blGuaranteeForcenovalnetsepa}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_GUARANTEE_PAYMENT_FORCE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_GUARANTEE_PAYMENT_FORCE_DESCRIPTION" }]</span>
                        <dd>
                    </dl>
                </div>
            </div>
            <div class="novalnet_config_title">
                <span payment_id="invoice_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                <b>[{ oxmultilang ident="NOVALNET_INVOICE" }]</b>
                <div class="configCnt" style="display:none;" id="invoice_config">
                    <dl>
                        <dd>
                            <span class="configDesc" id="admin_payment_message">[{ oxmultilang ident="NOVALNET_PAYMENT_MESSAGE" }] <a class="novalnet_config_link" href="[{$aPaymentUrl.novalnetinvoice}]" target="_blank" style="color:#0080c9;"><b>[{ oxmultilang ident="NOVALNET_INVOICE" }]</b></a></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>Customize payment logo</label>
                        </dt>
                        [{assign var="sThumbUrl" value=$oView->getThumbnailUrl("novalnetinvoice")}]
                        <dd>
                            <div class="picPreview"><img src="[{$sThumbUrl}]"></div>
                        </dd>
                        <dd>
                            <input class="editinput" name="myfile[novalnetinvoice]" type="file">
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYMENT_LOGO_DESC" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blTestmodenovalnetinvoice]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blTestmodenovalnetinvoice]" value="1" [{if
                            $aNovalnetConfig.blTestmodenovalnetinvoice}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_TEST_MODE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TEST_MODE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <dd id="payment_action">
                            <input type="hidden" id="payment_name" name="payment_name" value="novalnetinvoice" />
                            <select name="aNovalnetConfig[sPaymentActionnovalnetinvoice]" id="sPaymentActionnovalnetinvoice">
                                <option value="capture" [{if $aNovalnetConfig.sPaymentActionnovalnetinvoice == "capture"}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_CAPTURE" }]</option>
                                <option value="authorize" [{if $aNovalnetConfig.sPaymentActionnovalnetinvoice == "authorize"}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_AUTHORIZE" }]</option>
                            </select>
                    </dl>
                    [{assign var="invoiceonholddisplay" value="none"}]
                    [{if $aNovalnetConfig.sPaymentActionnovalnetinvoice == "capture"}]
                    [{assign var="invoiceonholddisplay" value="none"}]
                    [{/if}]
                    [{if $aNovalnetConfig.sPaymentActionnovalnetinvoice == "authorize"}]
                    [{assign var="invoiceonholddisplay" value="block"}]
                    [{/if}]
                    <dl id="novalnetinvoice_manualcheck" style="display:[{$invoiceonholddisplay}];">
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_MANUAL_CHECK_LIMIT_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[dOnholdLimitnovalnetinvoice]" value="[{$aNovalnetConfig.dOnholdLimitnovalnetinvoice}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_MANUAL_CHECK_LIMIT_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_TITLE" }]</label>
                        </dt>
                        <dd>
                            <select name="aNovalnetConfig[iCallbacknovalnetinvoice]">
                                <option value="" [{if $aNovalnetConfig.iCallbacknovalnetinvoice == ''}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_OPTION_NONE" }]</option>
                                <option value="1" [{if $aNovalnetConfig.iCallbacknovalnetinvoice == 1}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_OPTION_CALL" }]</option>
                                <option value="2" [{if $aNovalnetConfig.iCallbacknovalnetinvoice == 2}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_OPTION_SMS" }]</option>
                            </select>
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_AMOUNT_LIMIT_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[dCallbackAmountnovalnetinvoice]" value="[{$aNovalnetConfig.dCallbackAmountnovalnetinvoice}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_AMOUNT_LIMIT_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_INVOICE_DUE_DATE_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[iDueDatenovalnetinvoice]" value="[{$aNovalnetConfig.iDueDatenovalnetinvoice}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_INVOICE_DUE_DATE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sBuyerNotifynovalnetinvoice]" value="[{$aNovalnetConfig.sBuyerNotifynovalnetinvoice}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt class='configTitle'>[{ oxmultilang ident="NOVALNET_GUARANTEE_CONFIGURATION_TITLE" }]</dt>
                        <dd class="configHeadingDesc">[{ oxmultilang ident="NOVALNET_GUARANTEE_CONFIGURATION_DESCRIPTION" }]</dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blGuaranteenovalnetinvoice]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blGuaranteenovalnetinvoice]" value="1" [{if
                            $aNovalnetConfig.blGuaranteenovalnetinvoice}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_GUARANTEE_PAYMENT_TITLE" }]
                        </dd>
                    </dl>
                    <dl>
                        <dt><label>[{ oxmultilang ident="NOVALNET_GUARANTEE_MINIMUM_AMOUNT_TITLE" }]</label></dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[dGuaranteeMinAmountnovalnetinvoice]" value="[{$aNovalnetConfig.dGuaranteeMinAmountnovalnetinvoice}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_GUARANTEE_MINIMUM_AMOUNT_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blGuaranteeForcenovalnetinvoice]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blGuaranteeForcenovalnetinvoice]" value="1" [{if !isset($aNovalnetConfig.blGuaranteeForcenovalnetinvoice) || $aNovalnetConfig.blGuaranteeForcenovalnetinvoice}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_GUARANTEE_PAYMENT_FORCE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_GUARANTEE_PAYMENT_FORCE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="novalnet_config_title">
                <span payment_id="prepayment_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                <b>[{ oxmultilang ident="NOVALNET_PREPAYMENT" }]</b>
                <div class="configCnt" style="display:none;" id="prepayment_config">
                    <dl>
                        <dd>
                            <span class="configDesc" id="admin_payment_message">[{ oxmultilang ident="NOVALNET_PAYMENT_MESSAGE" }] <a class="novalnet_config_link" href="[{$aPaymentUrl.novalnetprepayment}]" target="_blank" style="color:#0080c9;"><b>[{ oxmultilang ident="NOVALNET_PREPAYMENT" }]</b></a></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>Customize payment logo</label>
                        </dt>
                        [{assign var="sThumbUrl" value=$oView->getThumbnailUrl("novalnetprepayment")}]
                        <dd>
                            <div class="picPreview"><img src="[{$sThumbUrl}]"></div>
                        </dd>
                        <dd>
                            <input class="editinput" name="myfile[novalnetprepayment]" type="file">
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYMENT_LOGO_DESC" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blTestmodenovalnetprepayment]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blTestmodenovalnetprepayment]" value="1" [{if $aNovalnetConfig.blTestmodenovalnetprepayment}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_TEST_MODE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TEST_MODE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sBuyerNotifynovalnetprepayment]" value="[{$aNovalnetConfig.sBuyerNotifynovalnetprepayment}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="novalnet_config_title">
                <span payment_id="paypal_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                <b>[{ oxmultilang ident="NOVALNET_PAYPAL" }]</b>
                <div class="configCnt" style="display:none;" id="paypal_config">
                    <dl>
                        <dd>
                            <span class="configDesc" id="admin_payment_message">[{ oxmultilang ident="NOVALNET_PAYMENT_MESSAGE" }] <a class="novalnet_config_link" href="[{$aPaymentUrl.novalnetpaypal}]" target="_blank" style="color:#0080c9;"><b>[{ oxmultilang ident="NOVALNET_PAYPAL" }]</b></a></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>Customize payment logo</label>
                        </dt>
                        [{assign var="sThumbUrl" value=$oView->getThumbnailUrl("novalnetpaypal")}]
                        <dd>
                            <div class="picPreview"><img src="[{$sThumbUrl}]"></div>
                        </dd>
                        <dd>
                            <input class="editinput" name="myfile[novalnetpaypal]" type="file">
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYMENT_LOGO_DESC" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blTestmodenovalnetpaypal]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blTestmodenovalnetpaypal]" value="1" [{if $aNovalnetConfig.blTestmodenovalnetpaypal}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_TEST_MODE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TEST_MODE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                     <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <dd id="payment_action">
                            <input type="hidden" id="payment_name" name="payment_name" value="novalnetpaypal" />
                            <select name="aNovalnetConfig[sPaymentActionnovalnetpaypal]" id="sPaymentActionnovalnetpaypal">
                                <option value="capture" [{if $aNovalnetConfig.sPaymentActionnovalnetpaypal == "capture"}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_CAPTURE" }]</option>
                                <option value="authorize" [{if $aNovalnetConfig.sPaymentActionnovalnetpaypal == "authorize"}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_PAYMENT_ACTION_AUTHORIZE" }]</option>
                            </select>
                    </dl>
                    [{assign var="paypalonholddisplay" value="none"}]
                    [{if $aNovalnetConfig.sPaymentActionnovalnetpaypal == "capture"}]
                    [{assign var="paypalonholddisplay" value="none"}]
                    [{/if}]
                    [{if $aNovalnetConfig.sPaymentActionnovalnetpaypal == "authorize"}]
                    [{assign var="paypalonholddisplay" value="block"}]
                    [{/if}]
                    <dl id="novalnetpaypal_manualcheck" style="display:[{$paypalonholddisplay}];">
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_MANUAL_CHECK_LIMIT_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[dOnholdLimitnovalnetpaypal]" value="[{$aNovalnetConfig.dOnholdLimitnovalnetpaypal}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYPAL_MANUAL_CHECK_LIMIT_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_SHOP_TYPE_TITLE" }]</label>
                        </dt>
                        <dd>
                            <select name="aNovalnetConfig[iShopTypenovalnetpaypal]">
                                <option value="" [{if $aNovalnetConfig.iShopTypenovalnetpaypal == ''}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_OPTION_NONE" }]</option>
                                <option value="1" [{if $aNovalnetConfig.iShopTypenovalnetpaypal == 1}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_ONE_CLICK_SHOP" }]</option>
                                <option value="2" [{if $aNovalnetConfig.iShopTypenovalnetpaypal == 2}]selected="selected"[{/if}]>[{ oxmultilang ident="NOVALNET_ZERO_AMOUNT_BOOK" }]</option>
                            </select>
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_SHOP_TYPE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sBuyerNotifynovalnetpaypal]" value="[{$aNovalnetConfig.sBuyerNotifynovalnetpaypal}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                </div>
                </div>
            </div>
            <div class="novalnet_config_title">
                <span payment_id="instantbank_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                <b>[{ oxmultilang ident="NOVALNET_INSTANTBANK" }]</b>
                <div class="configCnt" style="display:none;" id="instantbank_config">
                    <dl>
                        <dd>
                            <span class="configDesc" id="admin_payment_message">[{ oxmultilang ident="NOVALNET_PAYMENT_MESSAGE" }] <a class="novalnet_config_link" href="[{$aPaymentUrl.novalnetonlinetransfer}]" target="_blank" style="color:#0080c9;"><b>[{ oxmultilang ident="NOVALNET_INSTANTBANK" }]</b></a></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>Customize payment logo</label>
                        </dt>
                        [{assign var="sThumbUrl" value=$oView->getThumbnailUrl("novalnetonlinetransfer")}]
                        <dd>
                            <div class="picPreview"><img src="[{$sThumbUrl}]"></div>
                        </dd>
                        <dd>
                            <input class="editinput" name="myfile[novalnetonlinetransfer]" type="file">
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYMENT_LOGO_DESC" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden"
                            name="aNovalnetConfig[blTestmodenovalnetonlinetransfer]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blTestmodenovalnetonlinetransfer]" value="1" [{if $aNovalnetConfig.blTestmodenovalnetonlinetransfer}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_TEST_MODE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TEST_MODE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sBuyerNotifynovalnetonlinetransfer]" value="[{$aNovalnetConfig.sBuyerNotifynovalnetonlinetransfer}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="novalnet_config_title">
                <span payment_id="ideal_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                <b>[{ oxmultilang ident="NOVALNET_IDEAL" }]</b>
                <div class="configCnt" style="display:none;" id="ideal_config">
                    <dl>
                        <dd>
                            <span class="configDesc" id="admin_payment_message">[{ oxmultilang ident="NOVALNET_PAYMENT_MESSAGE" }] <a class="novalnet_config_link" href="[{$aPaymentUrl.novalnetideal}]" target="_blank" style="color:#0080c9;"><b>[{ oxmultilang ident="NOVALNET_IDEAL" }]</b></a></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>Customize payment logo</label>
                        </dt>
                        [{assign var="sThumbUrl" value=$oView->getThumbnailUrl("novalnetideal")}]
                        <dd>
                            <div class="picPreview"><img src="[{$sThumbUrl}]"></div>
                        </dd>
                        <dd>
                            <input class="editinput" name="myfile[novalnetideal]" type="file">
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYMENT_LOGO_DESC" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blTestmodenovalnetideal]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blTestmodenovalnetideal]" value="1" [{if $aNovalnetConfig.blTestmodenovalnetideal}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_TEST_MODE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TEST_MODE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sBuyerNotifynovalnetideal]" value="[{$aNovalnetConfig.sBuyerNotifynovalnetideal}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="novalnet_config_title">
                <span payment_id="eps_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                <b>[{ oxmultilang ident="NOVALNET_EPS" }]</b>
                <div class="configCnt" style="display:none;" id="eps_config">
                    <dl>
                        <dd>
                            <span class="configDesc" id="admin_payment_message">[{ oxmultilang ident="NOVALNET_PAYMENT_MESSAGE" }] <a class="novalnet_config_link" href="[{$aPaymentUrl.novalneteps}]" target="_blank" style="color:#0080c9;"><b>[{ oxmultilang ident="NOVALNET_EPS" }]</b></a></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>Customize payment logo</label>
                        </dt>
                        [{assign var="sThumbUrl" value=$oView->getThumbnailUrl("novalneteps")}]
                        <dd>
                            <div class="picPreview"><img src="[{$sThumbUrl}]"></div>
                        </dd>
                        <dd>
                            <input class="editinput" name="myfile[novalneteps]" type="file">
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYMENT_LOGO_DESC" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blTestmodenovalneteps]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blTestmodenovalneteps]" value="1" [{if $aNovalnetConfig.blTestmodenovalneteps}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_TEST_MODE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TEST_MODE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sBuyerNotifynovalneteps]" value="[{$aNovalnetConfig.sBuyerNotifynovalneteps}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                </div>
            </div>
           <div class="novalnet_config_title">
                <span payment_id="giropay_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                <b>[{ oxmultilang ident="NOVALNET_GIROPAY" }]</b>
                <div class="configCnt" style="display:none;" id="giropay_config">
                    <dl>
                        <dd>
                            <span class="configDesc" id="admin_payment_message">[{ oxmultilang ident="NOVALNET_PAYMENT_MESSAGE" }] <a class="novalnet_config_link" href="[{$aPaymentUrl.novalnetgiropay}]" target="_blank" style="color:#0080c9;"><b>[{ oxmultilang ident="NOVALNET_GIROPAY" }]</b></a></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>Customize payment logo</label>
                        </dt>
                        [{assign var="sThumbUrl" value=$oView->getThumbnailUrl("novalnetgiropay")}]
                        <dd>
                            <div class="picPreview"><img src="[{$sThumbUrl}]"></div>
                        </dd>
                        <dd>
                            <input class="editinput" name="myfile[novalnetgiropay]" type="file">
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYMENT_LOGO_DESC" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blTestmodenovalnetgiropay]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blTestmodenovalnetgiropay]" value="1" [{if $aNovalnetConfig.blTestmodenovalnetgiropay}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_TEST_MODE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TEST_MODE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sBuyerNotifynovalnetgiropay]" value="[{$aNovalnetConfig.sBuyerNotifynovalnetgiropay}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="novalnet_config_title">
                <span payment_id="przelewy24_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                <b>[{ oxmultilang ident="NOVALNET_PRZELEWY24" }]</b>
                <div class="configCnt" style="display:none;" id="przelewy24_config">
                    <dl>
                        <dd>
                            <span class="configDesc" id="admin_payment_message">[{ oxmultilang ident="NOVALNET_PAYMENT_MESSAGE" }] <a class="novalnet_config_link" href="[{$aPaymentUrl.novalnetprzelewy24}]" target="_blank" style="color:#0080c9;"><b>[{ oxmultilang ident="NOVALNET_PRZELEWY24" }]</b></a></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>Customize payment logo</label>
                        </dt>
                        [{assign var="sThumbUrl" value=$oView->getThumbnailUrl("novalnetprzelewy24")}]
                        <dd>
                            <div class="picPreview"><img src="[{$sThumbUrl}]"></div>
                        </dd>
                        <dd>
                            <input class="editinput" name="myfile[novalnetprzelewy24]" type="file">
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYMENT_LOGO_DESC" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt></dt>
                        <dd>
                            <input type="hidden" name="aNovalnetConfig[blTestmodenovalnetprzelewy24]" value="0" />
                            <input type="checkbox" name="aNovalnetConfig[blTestmodenovalnetprzelewy24]" value="1" [{if $aNovalnetConfig.blTestmodenovalnetprzelewy24}]checked="checked"[{/if}] />
                            [{ oxmultilang ident="NOVALNET_TEST_MODE_TITLE" }]
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_TEST_MODE_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_TITLE" }]</label>
                        </dt>
                        <dd>
                            <input type="text" name="aNovalnetConfig[sBuyerNotifynovalnetprzelewy24]" value="[{$aNovalnetConfig.sBuyerNotifynovalnetprzelewy24}]" />
                        </dd>
                        <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_DESCRIPTION" }]</span>
                        </dd>
                    </dl>
                </div>
                </div>
                <div class="novalnet_config_title">
                    <span payment_id="barzahlen_config" onclick="novalnetToggleMe(this)" class="down-icon" ></span>
                    <b>[{ oxmultilang ident="NOVALNET_BARZAHLEN" }]</b>
                    <div class="configCnt" style="display:none;" id="barzahlen_config">
                        <dl>
                        <dd>
                            <span class="configDesc" id="admin_payment_message">[{ oxmultilang ident="NOVALNET_PAYMENT_MESSAGE" }] <a class="novalnet_config_link" href="[{$aPaymentUrl.novalnetbarzahlen}]" target="_blank" style="color:#0080c9;"><b>[{ oxmultilang ident="NOVALNET_BARZAHLEN" }]</b></a></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt>
                            <label>Customize payment logo</label>
                        </dt>
                        [{assign var="sThumbUrl" value=$oView->getThumbnailUrl("novalnetbarzahlen")}]
                        <dd>
                            <div class="picPreview"><img src="[{$sThumbUrl}]"></div>
                        </dd>
                        <dd>
                            <input class="editinput" name="myfile[novalnetbarzahlen]" type="file">
                        </dd>
                         <dd>
                            <span class="configDesc">[{ oxmultilang ident="NOVALNET_PAYMENT_LOGO_DESC" }]</span>
                        </dd>
                    </dl>
                        <dl>
                            <dt></dt>
                            <dd>
                                <input type="hidden" name="aNovalnetConfig[blTestmodenovalnetbarzahlen]" value="0" />
                                <input type="checkbox" name="aNovalnetConfig[blTestmodenovalnetbarzahlen]" value="1" [{if $aNovalnetConfig.blTestmodenovalnetbarzahlen}]checked="checked"[{/if}] />
                                [{ oxmultilang ident="NOVALNET_TEST_MODE_TITLE" }]
                            </dd>
                            <dd>
                                <span class="configDesc">[{ oxmultilang ident="NOVALNET_TEST_MODE_DESCRIPTION" }]</span>
                            </dd>
                        </dl>
                        <dl>
                            <dt>
                                <label>[{ oxmultilang ident="NOVALNET_BARZAHLEN_DUE_DATE_TITLE" }]</label>
                            </dt>
                            <dd>
                                <input type="text" name="aNovalnetConfig[iDueDatenovalnetbarzahlen]" value="[{$aNovalnetConfig.iDueDatenovalnetbarzahlen}]" />
                            </dd>
                            <dd>
                                <span class="configDesc">[{ oxmultilang ident="NOVALNET_BARZAHLEN_DUE_DATE_DESCRIPTION" }]</span>
                            </dd>
                        </dl>
                        <dl>
                            <dt>
                                <label>[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_TITLE" }]</label>
                            </dt>
                            <dd>
                                <input type="text" name="aNovalnetConfig[sBuyerNotifynovalnetbarzahlen]" value="[{$aNovalnetConfig.sBuyerNotifynovalnetbarzahlen}]" />
                            </dd>
                            <dd>
                                <span class="configDesc">[{ oxmultilang ident="NOVALNET_BUYER_NOTIFICATION_DESCRIPTION" }]</span>
                            </dd>
                        </dl>
                    </div>
                </div>
            <input type="submit" id="novalnet_config_submit" value= [{ oxmultilang ident="GENERAL_SAVE" }] />
        </div>
    </form>
</div>
[{include file="bottomitem.tpl"}]
