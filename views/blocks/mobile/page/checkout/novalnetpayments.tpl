[{if in_array($sPaymentID, array('novalnetcreditcard', 'novalnetsepa', 'novalnetinvoice', 'novalnetprepayment', 'novalnetonlinetransfer', 'novalnetideal', 'novalnetpaypal', 'novalneteps', 'novalnetgiropay', 'novalnetprzelewy24','novalnetbarzahlen'))}]
    [{assign var="oConf" value=$oViewConf->getConfig()}]
    <div id="paymentOption_[{$sPaymentID}]" class="payment-option [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]active-payment[{/if}]">
        <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked="checked"[{/if}] />
        <ul class="form">
            [{if $paymentmethod->getPrice()}]
                <li>
                    <div class="payment-charge">
                        [{if $oxcmp_basket->getPayCostNet()}]
                            ([{$paymentmethod->getFNettoPrice()}] [{$currency->sign}] [{oxmultilang ident="PLUS_VAT"}] [{$paymentmethod->getFPriceVat()}] )
                        [{else}]
                            ([{$paymentmethod->getFBruttoPrice()}] [{$currency->sign}])
                        [{/if}]
                    </div>
                </li>
            [{/if}]
            [{if $oView->getNovalnetConfig('blPaymentLogo')}]
                <span>
                    <a href="[{ oxmultilang ident='NOVALNET_LINK_URL' }]" target="_blank" title="[{$paymentmethod->oxpayments__oxdesc->value}]" style="text-decoration:none;">

                        [{if $sPaymentID == "novalnetcreditcard"}]
                            <img src="[{$oViewConf->getModuleUrl('novalnet','out/img/')}]novalnetvisa.png" alt="[{$paymentmethod->oxpayments__oxdesc->value}]"/>
                            <img src="[{$oViewConf->getModuleUrl('novalnet','out/img/')}]novalnetmaster.png" alt="[{$paymentmethod->oxpayments__oxdesc->value}]"/>

                            [{if $oView->getNovalnetConfig('blAmexActive')}]
                                <img src="[{$oViewConf->getModuleUrl('novalnet','out/img/')}]novalnetamex.png" alt="[{$paymentmethod->oxpayments__oxdesc->value}]"/>
                            [{/if}]
                            [{if $oView->getNovalnetConfig('blMaestroActive')}]
                                <img src="[{$oViewConf->getModuleUrl('novalnet','out/img/')}]novalnetmaestro.png" alt="[{$paymentmethod->oxpayments__oxdesc->value}]"/>
                            [{/if}]
                        [{else}]
                            <img src="[{$oViewConf->getModuleUrl('novalnet','out/img/')}][{$sPaymentID}].png" alt="[{$paymentmethod->oxpayments__oxdesc->value}]"/>
                        [{/if}]
                    </a>
                </span>
            [{/if}]
            [{if $sPaymentID == "novalnetcreditcard"}]
                [{include file="novalnetcreditcardmobile.tpl"}]
            [{elseif $sPaymentID == "novalnetsepa"}]
                [{include file="novalnetsepamobile.tpl"}]
            [{elseif $sPaymentID == "novalnetinvoice"}]
                [{include file="novalnetinvoicemobile.tpl"}]
            [{elseif $sPaymentID == "novalnetpaypal"}]
                [{include file="novalnetpaypalmobile.tpl"}]
            [{/if}]
            [{if !in_array($sPaymentID, array('novalnetcreditcard', 'novalnetpaypal')) }]
                [{block name="checkout_payment_longdesc"}]
					[{if ($sPaymentID == 'novalnetsepa' && !empty($smarty.session.blGuaranteeForceDisablednovalnetsepa)) || ($sPaymentID == 'novalnetinvoice' && !empty($smarty.session.blGuaranteeForceDisablednovalnetinvoice)) }]
						<li>
							<div class="payment-desc" style="color:red">
								[{ oxmultilang ident='NOVALNET_GUARANTEE_FORCE_DISABLED_MESSAGE' }]</span><br><br>
							</div>
						</li>
					[{/if}]
                    [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                        <li>
                            <div class="payment-desc">
                                [{$paymentmethod->oxpayments__oxlongdesc->getRawValue()}]
                                [{if in_array($sPaymentID, array('novalnetonlinetransfer', 'novalnetideal', 'novalneteps', 'novalnetgiropay', 'novalnetprzelewy24'))}]
                                    <br>[{ oxmultilang ident='NOVALNET_REDIRECT_DESCRIPTION_MESSAGE' }]
                                [{/if}]
                            </div>
                        </li>
                    [{/if}]
                [{/block}]
            [{/if}]
            [{if $oView->getNovalnetNotification($sPaymentID) != '' }]
                <li>
                    <div class="payment-desc">
                        [{$oView->getNovalnetNotification($sPaymentID)}]
                    </div>
                </li>
            [{/if}]
            [{if $oView->getNovalnetTestmode($sPaymentID) }]
                <li>
                    <div class="payment-desc" style="color:red">
                        [{oxmultilang ident='NOVALNET_TEST_MODE_MESSAGE'}]
                    </div>
                </li>
            [{/if}]
        </ul>
    </div>
[{else}]
    [{$smarty.block.parent}]
[{/if}]
