[{assign var="aPaypalDetails" value=$oView->getNovalnetPaymentDetails($sPaymentID)}]
[{assign var="displayPaypalPort" value=""}]
[{if $oViewConf->getActiveTheme() == 'flow'}]
	<input type="hidden" name="novalnet_paypal_disclosure_text" id="novalnet_paypal_disclosure_text" value="[{$aPaypalDetails.iShowText}]">
    [{if $aPaypalDetails.iShopType == 1}]
     <input type="hidden" name="dynvalue[novalnet_paypal_new_details]" id="novalnet_paypal_new_details" value="[{$aPaypalDetails.blOneClick}]">
        [{assign var="displayPaypalPort" value="style='display:none;'"}]
        <div class="form-group novalnet_paypal_ref_acc">
            <label class="control-label col-lg-3"><span id="novalnet_paypal_ref_acc" style="color:blue; text-decoration:underline; cursor:pointer; white-space:nowrap;" onclick="changePaypalAccountType(event, 'novalnet_paypal_new_acc')">[{ oxmultilang ident="NOVALNET_PAYPAL_NEW_ACCOUNT_DETAILS" }]</span></label>
        </div>
        <div class="form-group novalnet_paypal_ref_acc">
            <label class="control-label col-lg-3">[{ oxmultilang ident="NOVALNET_REFERENCE_TID" }]</label>
            <div class="col-lg-9">
                <label class="control-label">[{$smarty.session.sPaymentRefnovalnetpaypal}]</label>
            </div>
        </div>
         [{if $aPaypalDetails.paypal_transaction_id}]
            <div class="form-group novalnet_paypal_ref_acc">
                <label class="control-label col-lg-3">[{ oxmultilang ident="NOVALNET_PAYPAL_REFERENCE_TID" }]</label>
                <div class="col-lg-9">
                    <label class="control-label">[{$aPaypalDetails.paypal_transaction_id}]</label>
                </div>
            </div>
        [{/if}]
        <div class="form-group novalnet_paypal_new_acc" [{$displayPaypalPort}]>
            <label class="control-label col-lg-3"><span id='novalnet_paypal_new_acc' style="color:blue; text-decoration:underline; cursor:pointer;" onclick="changePaypalAccountType(event, 'novalnet_paypal_ref_acc')">[{ oxmultilang ident="NOVALNET_PAYPAL_GIVEN_ACCOUNT_DETAILS" }]</span></label>
            
        </div>
    [{/if}]
    <div class="col-lg-offset-3" id = "novalnet_paypal_disclosure_confirm">
		<input type="hidden" name="dynvalue[novalnet_paypal_disclosure]" id="novalnet_paypal_disclosure" value= 0>
		<div class="checkbox">
			<label for="novalnet_paypal_disclosure_confirm">
				<input type="checkbox" name="novalnet_paypal_confirmation" id="novalnet_paypal_confirmation" aria-invalid="false"> Save my card details for future purchases
			</label>
		</div>
		<br/>
	</div>
	[{oxscript include=$oViewConf->getModuleUrl('novalnet', 'out/src/js/novalnetpaypal.js')}]
[{else}]
    <ul class="form">
        [{if $aPaypalDetails.iShopType == 1}]
            [{assign var="displayPaypalPort" value="style='display:none;'"}]
            <input type="hidden" name="dynvalue[novalnet_paypal_new_details]" id="novalnet_paypal_new_details" value="[{$aPaypalDetails.blOneClick}]">
            <div class="col-lg-9 col-lg-offset-3" id = "novalnet_paypal_disclosure_confirm">
				<input type="hidden" name="dynvalue[novalnet_paypal_disclosure]" id="novalnet_paypal_disclosure" value= 0>
				<div class="checkbox">
					<label for="novalnet_paypal_disclosure_confirm">
						<input type="checkbox" name="novalnet_paypal_confirmation" id="novalnet_paypal_confirmation" aria-invalid="false"> Save my card details for future purchases
					</label>
				</div>
				<br/>
			</div>
            <li class='novalnet_paypal_ref_acc'>
                <table>
                    <tr>
                        <td colspan="2"><span id="novalnet_paypal_ref_acc" style="color:blue; text-decoration:underline; cursor:pointer;" onclick="changePaypalAccountType(event, 'novalnet_paypal_new_acc')">[{ oxmultilang ident="NOVALNET_PAYPAL_NEW_ACCOUNT_DETAILS" }]</span></td>
                    </tr>
                    <tr>
                        <td><label>[{ oxmultilang ident="NOVALNET_REFERENCE_TID" }]</label></td>
                        <td><label>[{$smarty.session.sPaymentRefnovalnetpaypal}]</label></td>
                    </tr>
                     [{if $aPaypalDetails.paypal_transaction_id}]
                        <tr>
                            <td><label>[{ oxmultilang ident="NOVALNET_PAYPAL_REFERENCE_TID" }]</label></td>
                            <td><label>[{$aPaypalDetails.paypal_transaction_id}]</label></td>
                        </tr>
                    [{/if}]
                </table>
            </li>
            <li class='novalnet_paypal_new_acc' [{$displayPaypalPort}]>
                <span id='novalnet_paypal_new_acc' style="color:blue; text-decoration:underline; cursor:pointer;" onclick="changePaypalAccountType(event, 'novalnet_paypal_ref_acc')">[{ oxmultilang ident="NOVALNET_PAYPAL_GIVEN_ACCOUNT_DETAILS" }]</span>
                [{oxscript include=$oViewConf->getModuleUrl('novalnet', 'out/src/js/novalnetpaypal.js')}]
            </li>
        [{/if}]
    </ul>
[{/if}]
[{block name="checkout_payment_longdesc"}]
    <div class="desc alert alert-info col-lg-offset-3">
        [{if $aPaypalDetails.iShopType == 1}]
            <span class='novalnet_paypal_ref_acc'>
                [{ oxmultilang ident='NOVALNET_PAYPAL_REFERENCE_DESCRIPTION_MESSAGE' }]
            </span>
            <span class='novalnet_paypal_new_acc' [{$displayPaypalPort}]>
                [{ $paymentmethod->oxpayments__oxlongdesc->getRawValue() }]
                <br>[{ oxmultilang ident='NOVALNET_REDIRECT_DESCRIPTION_MESSAGE' }]
            </span>
        [{else}]
            [{ $paymentmethod->oxpayments__oxlongdesc->getRawValue() }]
            <br>[{ oxmultilang ident='NOVALNET_REDIRECT_DESCRIPTION_MESSAGE' }]
        [{/if}]
        [{if $oView->getNovalnetNotification($sPaymentID) != '' }]
            <br><br>[{$oView->getNovalnetNotification($sPaymentID)}]
        [{/if}]
        [{if $oView->getNovalnetTestmode($sPaymentID) }]
            <br><br><span style="color:red">[{ oxmultilang ident='NOVALNET_TEST_MODE_MESSAGE' }]</span>
        [{/if}]
        [{if $aPaypalDetails.blZeroBook }]
			<br><br><span style="color:red">[{ oxmultilang ident='NOVALNET_ZERO_AMOUNT_MESSAGE' }]</span>
		[{/if}]
    </div>
[{/block}]

