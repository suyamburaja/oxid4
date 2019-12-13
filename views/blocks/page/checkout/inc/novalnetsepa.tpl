<noscript>
    <div class="desc" style="color:red;">
        <br/>[{ oxmultilang ident='NOVALNET_NOSCRIPT_MESSAGE' }]
    </div>
    <input type="hidden" name="novalnet_sepa_noscript" value="1">
    <style>#novalnet_sepa_form{display:none;}</style>
</noscript>

[{if $oViewConf->getActiveTheme() == 'flow'}]
    [{if !empty($smarty.session.sCallbackTidnovalnetsepa)}]
        [{if in_array($oView->getNovalnetConfig('iCallbacknovalnetsepa'), array(1, 2))}]
            <div class="form-group">
                <label class="req control-label col-lg-3">[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_PIN" }]</label>
                <div class="col-lg-9">
                    <input type="text" size="20" name="dynvalue[pinno_novalnetsepa]" autocomplete="off" value="">
                </div>
            </div>
            <div class="form-group">
                <label class="req control-label col-lg-3">&nbsp;</label>
                <div class="col-lg-9">
                    <input type="checkbox" size="20" name="dynvalue[newpin_novalnetsepa]">&nbsp;[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_FORGOT_PIN" }]
                </div>
            </div>
        [{/if}]
    [{else}]
        [{assign var="aSepaDetails" value=$oView->getNovalnetPaymentDetails($sPaymentID)}]
        [{assign var="displaySepaForm" value="style='width:100%;'"}]
        <input type="hidden" name="novalnet_sepa_disclosure_text" id="novalnet_sepa_disclosure_text" value="[{$aSepaDetails.iShowText}]">
        [{if $aSepaDetails.iShopType == 1}]
            [{assign var="displaySepaForm" value="style='width:100%; display:none;'"}]
            <input type="hidden" name="dynvalue[novalnet_sepa_new_details]" id="novalnet_sepa_new_details" value=[{$aSepaDetails.blOneClick}]>
            <div class="form-group novalnet_sepa_ref_acc">
                <label class="control-label col-lg-3"><span id="novalnet_sepa_ref_acc" style="color:blue; text-decoration:underline; cursor:pointer;" onclick="changeSepaAccountType(event, 'novalnet_sepa_new_acc')">[{oxmultilang ident="NOVALNET_NEW_ACCOUNT_DETAILS"}]</span></label>
            </div>
            <div class="form-group novalnet_sepa_ref_acc">
                <label class="control-label col-lg-3">[{oxmultilang ident="NOVALNET_SEPA_HOLDER_NAME"}]</label>
                <div class="col-lg-9">
                    <label class="control-label" style="padding-left:0">[{$aSepaDetails.bankaccount_holder}]</label>
                </div>
            </div>
            <div class="form-group novalnet_sepa_ref_acc">
                <label class="control-label col-lg-3">IBAN</label>
                <div class="col-lg-9">
                    <label class="control-label">[{$aSepaDetails.iban}]</label>
                </div>
            </div>
              [{if !empty($smarty.session.blGuaranteeEnablednovalnetsepa) && empty($smarty.session.blGuaranteeForceDisablednovalnetsepa) && empty($oxcmp_user->oxuser__oxcompany->value) }]
            <div class="form-group novalnet_sepa_ref_acc" style='width:100%;'">
                <label class="req control-label col-lg-3">[{ oxmultilang ident="NOVALNET_BIRTH_DATE" }]</label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" size="20" id="novalnet_sepa_birth_date" name="dynvalue[birthdatenovalnetsepa]" value="[{$oView->getNovalnetBirthDate()}]" placeholder="YYYY-MM-DD" autocomplete="off">
                </div>
            </div>
        [{/if}]
            <div class="form-group novalnet_sepa_new_acc" [{$displaySepaForm}]>
                <label class="control-label col-lg-3"><span id='novalnet_sepa_new_acc' style="color:blue; text-decoration:underline; cursor:pointer;" onclick="changeSepaAccountType(event, 'novalnet_sepa_ref_acc')">[{oxmultilang ident="NOVALNET_GIVEN_ACCOUNT_DETAILS"}]</span></label>
            </div>
        [{/if}]
        <div class="form-group novalnet_sepa_new_acc" [{$displaySepaForm}]>
            <label class="req control-label col-lg-3">[{ oxmultilang ident="NOVALNET_SEPA_HOLDER_NAME" }]</label>
            <div class="col-lg-9">
                <input type="text" class="form-control js-oxValidate js-oxValidate_notEmpty" size="20" id="novalnet_sepa_holder" name="dynvalue[novalnet_sepa_holder]" autocomplete="off" value="[{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}]" onkeypress="return isValidKeySepa(event);">
            </div>
        </div>
        
        <div class="form-group novalnet_sepa_new_acc" [{$displaySepaForm}]>
            <label class="req control-label col-lg-3">[{ oxmultilang ident="NOVALNET_SEPA_IBAN" }]</label>
            <div class="col-lg-9">
                <input type="text" class="form-control js-oxValidate js-oxValidate_notEmpty" size="20" id="novalnet_sepa_acc_no" name="dynvalue[novalnet_sepa_iban]" autocomplete="off" onkeypress="return isValidKeySepa(event);" style = "text-transform: uppercase;"><span id="novalnet_sepa_iban_span"></span>
            </div>
        </div>
        
        <div class="col-lg-offset-3" id = "novalnet_sepa_disclosure_confirm">
			<input type="hidden" name="dynvalue[novalnet_sepa_disclosure]" id="novalnet_sepa_disclosure" value= 0>
			<div class="checkbox">
				<label for="novalnet_sepa_disclosure_confirm">
					<input type="checkbox" name="novalnet_sepa_confirmation" id="novalnet_sepa_confirmation" aria-invalid="false"> Save my card details for future purchases
				</label>
			</div>
			<br/>
		</div>
        
         [{if !empty($smarty.session.blGuaranteeEnablednovalnetsepa) && empty($smarty.session.blGuaranteeForceDisablednovalnetsepa) && empty($oxcmp_user->oxuser__oxcompany->value)}]
            <div class="form-group novalnet_sepa_new_acc" [{$displaySepaForm}]>
                <label class="req control-label col-lg-3">[{ oxmultilang ident="NOVALNET_BIRTH_DATE" }]</label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" size="20" id="novalnet_sepa_birth_date" name="dynvalue[birthdatenovalnetsepa]" value="[{$oView->getNovalnetBirthDate()}]" placeholder="YYYY-MM-DD" autocomplete="off">
                </div>
            </div>
        [{/if}]
        <div class="form-group novalnet_sepa_new_acc" [{$displaySepaForm}]>
            <label class="req control-label col-lg-3">&nbsp;</label>
            <div class="col-lg-9">
               <div class="col-md-12">
      <div class="form-group">
        <a data-toggle="collapse" data-target="#sepa_mandate_information"><strong><strong>Ich erteile hiermit das SEPA-Lastschriftmandat</strong> (elektronische Übermittlung) <strong>und bestätige, dass die Bankverbindung korrekt ist.</strong></strong></a>
        <div class="collapse panel panel-default" id="sepa_mandate_information" style="padding:5px;">
          Ich ermächtige den Zahlungsempfänger, Zahlungen von meinem Konto mittels Lastschrift einzuziehen. Zugleich weise ich mein Kreditinstitut an, die von dem Zahlungsempfänger auf mein Konto gezogenen Lastschriften einzulösen.
          <br>
          <br>
          <strong>Gläubiger-Identifikationsnummer: DE53ZZZ00000004253</strong>
          <br>
          <br>
          <strong>Hinweis:</strong> Ich kann innerhalb von acht Wochen, beginnend mit dem Belastungsdatum, die Erstattung des belasteten Betrages verlangen. Es gelten dabei die mit meinem Kreditinstitut vereinbarten Bedingungen.
        </div>
      </div>
    </div>
                <span class="novalnetloader" id="novalnet_sepa_loader"></span>
                <input type="hidden" id="novalnet_sepa_invalid_message" value="[{ oxmultilang ident="NOVALNET_SEPA_INVALID_DETAILS" }]">
                <input type="hidden" id="novalnet_sepa_unconfirm_message" value="[{ oxmultilang ident="NOVALNET_SEPA_UNCONFIRM_DETAILS" }]">
                <input type="hidden" id="novalnet_sepa_country_invalid_message" value="[{ oxmultilang ident="NOVALNET_SEPA_INVALID_COUNTRY" }]">
                <input type="hidden" id="novalnet_sepa_merchant_invalid_message" value="[{ oxmultilang ident="NOVALNET_INVALID_MERCHANT_DETAILS" }]">
                <input type="hidden" id="novalnet_sepa_vendor_id" value="[{$aSepaDetails.iVendorId}]">
                <input type="hidden" id="novalnet_sepa_vendor_authcode" value="[{$aSepaDetails.sAuthCode}]">
                <input type="hidden" id="novalnet_remote_ip" value="[{$oView->getNovalnetRemoteIp()}]">
                <input type="hidden" id="novalnet_sepa_iban" value="">
                <input type="hidden" id="novalnet_sepa_bic" value="">
            </div>
            [{oxscript include=$oViewConf->getModuleUrl('novalnet', 'out/src/js/novalnetsepa.js')}]
            [{oxstyle  include=$oViewConf->getModuleUrl('novalnet', 'out/src/css/novalnet.css')}]
        </div>
        [{if $oView->getFraudModuleStatus($sPaymentID) }]
            [{if $oView->getNovalnetConfig('iCallbacknovalnetsepa') == 1}]
                <div class="form-group novalnet_sepa_new_acc" id="novalnet_sepa_form" [{$displaySepaForm}]>
                    <label class="req control-label col-lg-3">[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_PHONE" }]</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control js-oxValidate js-oxValidate_notEmpty" size="20" name="dynvalue[pinbycall_novalnetsepa]" autocomplete="off" value="[{$oxcmp_user->oxuser__oxfon->value}]">
                    </div>
                </div>
            [{elseif $oView->getNovalnetConfig('iCallbacknovalnetsepa') == 2}]
                <div class="form-group novalnet_sepa_new_acc" id="novalnet_sepa_form" [{$displaySepaForm}]>
                    <label class="req control-label col-lg-3">[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_MOBILE" }]</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control js-oxValidate js-oxValidate_notEmpty" size="20" name="dynvalue[pinbysms_novalnetsepa]" autocomplete="off" value="[{$oxcmp_user->oxuser__oxmobfon->value}]">
                    </div>
                </div>
            [{/if}]
        [{/if}]
        <input type="hidden" name="dynvalue[novalnet_sepa_uniqueid]" id="novalnet_sepa_uniqueid" value="">
        <input type="hidden" name="dynvalue[novalnet_sepa_hash]" id="novalnet_sepa_hash" value="">
    [{/if}]
[{else}]
    <ul class="form" id="novalnet_sepa_form" style="width:100%;">
        [{if !empty($smarty.session.sCallbackTidnovalnetsepa)}]
            [{if in_array($oView->getNovalnetConfig('iCallbacknovalnetsepa'), array(1, 2))}]
                <li>
                    <label>[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_PIN" }]</label>
                    <input type="text" size="20" name="dynvalue[pinno_novalnetsepa]" autocomplete="off" value="">
                </li>
                <li>
                    <label>&nbsp;</label>
                    <input type="checkbox" size="20" name="dynvalue[newpin_novalnetsepa]">&nbsp;[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_FORGOT_PIN" }]
                </li>
            [{/if}]
        [{else}]
            [{assign var="aSepaDetails" value=$oView->getNovalnetPaymentDetails($sPaymentID)}]
            [{assign var="displaySepaForm" value="style='width:100%;'"}]
            <input type="hidden" name="novalnet_sepa_disclosure_text" id="novalnet_sepa_disclosure_text" value="[{$aSepaDetails.iShowText}]">
            [{if $aSepaDetails.iShopType == 1}]
                [{assign var="displaySepaForm" value="style='width:100%; display:none;'"}]
                <input type="hidden" name="dynvalue[novalnet_sepa_new_details]" id="novalnet_sepa_new_details" value=[{$aSepaDetails.blOneClick}]>
                <li class='novalnet_sepa_ref_acc'>
                    <table>
                        <tr>
                            <td colspan="2"><span id="novalnet_sepa_ref_acc" style="color:blue; text-decoration:underline; cursor:pointer;" onclick="changeSepaAccountType(event, 'novalnet_sepa_new_acc')">[{oxmultilang ident="NOVALNET_NEW_ACCOUNT_DETAILS"}]</span></td>
                        </tr>
                        <tr>
                            <td><label>[{oxmultilang ident="NOVALNET_SEPA_HOLDER_NAME" }]</label></td>
                            <td><label>[{$aSepaDetails.bankaccount_holder}]</label></td>
                        </tr>
                        <tr>
                            <td><label>IBAN</label></td>
                            <td><label>[{$aSepaDetails.iban}]</label></td>
                        </tr>
                    </table>
                </li>
                <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
                    <span id='novalnet_sepa_new_acc' style="color:blue; text-decoration:underline; cursor:pointer;" onclick="changeSepaAccountType(event, 'novalnet_sepa_ref_acc')">[{oxmultilang ident="NOVALNET_GIVEN_ACCOUNT_DETAILS"}]</span>
                </li>
            [{/if}]
            <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
                <label>[{ oxmultilang ident="NOVALNET_SEPA_HOLDER_NAME" }]</label>
                <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" id="novalnet_sepa_holder" name="dynvalue[novalnet_sepa_holder]" autocomplete="off" value="[{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}]" onkeypress="return isValidKeySepa(event);">
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
                <label>[{ oxmultilang ident="NOVALNET_SEPA_IBAN" }]</label>
                <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" id="novalnet_sepa_acc_no"  name="dynvalue[novalnet_sepa_iban]" autocomplete="off" onkeypress="return isValidKeySepa(event);" style = "text-transform: uppercase;">&nbsp;<span id="novalnet_sepa_iban_span"></span>
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li>
				<div class="col-lg-offset-3" id = "novalnet_sepa_disclosure_confirm">
					<input type="hidden" name="dynvalue[novalnet_sepa_disclosure]" id="novalnet_sepa_disclosure" value= 0>
					<div class="checkbox">
						<label for="novalnet_sepa_disclosure_confirm">
							<input type="checkbox" name="novalnet_sepa_confirmation" id="novalnet_sepa_confirmation" aria-invalid="false"> Save my card details for future purchases
						</label>
					</div>
					<br/>
				</div>
			</li>
            [{if !empty($smarty.session.blGuaranteeEnablednovalnetsepa) && empty($smarty.session.blGuaranteeForceDisablednovalnetsepa) && empty($oxcmp_user->oxuser__oxcompany->value) }]
                <li>
                    <label>[{ oxmultilang ident="NOVALNET_BIRTH_DATE" }]</label>
                    <input type="text" size="20" id="novalnet_sepa_birth_date" name="dynvalue[birthdatenovalnetsepa]"
                    value="[{$oView->getNovalnetBirthDate()}]" placeholder="YYYY-MM-DD" autocomplete="off">
                </li>
            [{/if}]
            <li class='novalnet_sepa_new_acc' [{$displaySepaForm}] style="width:100%;">
                <label>&nbsp;</label>
                <div class="col-md-12">
      <div class="form-group">
        <a data-toggle="collapse" data-target="#sepa_mandate_information"><strong><strong>Ich erteile hiermit das SEPA-Lastschriftmandat</strong> (elektronische Übermittlung) <strong>und bestätige, dass die Bankverbindung korrekt ist.</strong></strong></a>
        <div class="collapse panel panel-default" id="sepa_mandate_information" style="padding:5px;">
          Ich ermächtige den Zahlungsempfänger, Zahlungen von meinem Konto mittels Lastschrift einzuziehen. Zugleich weise ich mein Kreditinstitut an, die von dem Zahlungsempfänger auf mein Konto gezogenen Lastschriften einzulösen.
          <br>
          <br>
          <strong>Gläubiger-Identifikationsnummer: DE53ZZZ00000004253</strong>
          <br>
          <br>
          <strong>Hinweis:</strong> Ich kann innerhalb von acht Wochen, beginnend mit dem Belastungsdatum, die Erstattung des belasteten Betrages verlangen. Es gelten dabei die mit meinem Kreditinstitut vereinbarten Bedingungen.
        </div>
      </div>
    </div>
                <span class="novalnetloader" id="novalnet_sepa_loader"></span>
                <input type="hidden" id="novalnet_sepa_invalid_message" value="[{ oxmultilang ident="NOVALNET_SEPA_INVALID_DETAILS" }]">
                <input type="hidden" id="novalnet_sepa_unconfirm_message" value="[{ oxmultilang ident="NOVALNET_SEPA_UNCONFIRM_DETAILS" }]">
                <input type="hidden" id="novalnet_sepa_country_invalid_message" value="[{ oxmultilang ident="NOVALNET_SEPA_INVALID_COUNTRY" }]">
                <input type="hidden" id="novalnet_sepa_merchant_invalid_message" value="[{ oxmultilang ident="NOVALNET_INVALID_MERCHANT_DETAILS" }]">
                <input type="hidden" id="novalnet_sepa_vendor_id" value="[{$aSepaDetails.iVendorId}]">
                <input type="hidden" id="novalnet_sepa_vendor_authcode" value="[{$aSepaDetails.sAuthCode}]">
                <input type="hidden" id="novalnet_remote_ip" value="[{$oView->getNovalnetRemoteIp()}]">
                <input type="hidden" id="novalnet_sepa_iban" value="">
                <input type="hidden" id="novalnet_sepa_bic" value="">
                [{oxscript include=$oViewConf->getModuleUrl('novalnet', 'out/src/js/novalnetsepa.js')}]
                [{oxstyle  include=$oViewConf->getModuleUrl('novalnet', 'out/src/css/novalnet.css')}]
            </li>

            [{if $oView->getFraudModuleStatus($sPaymentID) }]
                [{if $oView->getNovalnetConfig('iCallbacknovalnetsepa') == 1}]
                    <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
                        <label>[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_PHONE" }]</label>
                        <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" name="dynvalue[pinbycall_novalnetsepa]" autocomplete="off" value="[{$oxcmp_user->oxuser__oxfon->value}]">
                        <p class="oxValidateError">
                            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                        </p>
                    </li>
                [{elseif $oView->getNovalnetConfig('iCallbacknovalnetsepa') == 2}]
                    <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
                        <label>[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_MOBILE" }]</label>
                        <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" name="dynvalue[pinbysms_novalnetsepa]" autocomplete="off" value="[{$oxcmp_user->oxuser__oxmobfon->value}]">
                        <p class="oxValidateError">
                            <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                        </p>
                    </li>
                [{/if}]
            [{/if}]
            <input type="hidden" name="dynvalue[novalnet_sepa_uniqueid]" id="novalnet_sepa_uniqueid" value="">
            <input type="hidden" name="dynvalue[novalnet_sepa_hash]" id="novalnet_sepa_hash" value="">
        [{/if}]
    </ul>
[{/if}]
[{block name="checkout_payment_longdesc"}]
<div class="alert alert-info col-lg-offset-3 desc">
	[{if ($sPaymentID == 'novalnetsepa' && !empty($smarty.session.blGuaranteeForceDisablednovalnetsepa)) || ($sPaymentID == 'novalnetinvoice' && !empty($smarty.session.blGuaranteeForceDisablednovalnetinvoice)) }]
		<span style="color:red">[{ $smarty.session.blGuaranteeErrorMsgnovalnetinvoice }]</span><br><br>
	[{/if}]
   
	[{if $paymentmethod->oxpayments__oxlongdesc->value|trim}]
		[{ $paymentmethod->oxpayments__oxlongdesc->getRawValue()}]
	[{/if}]
	[{if $oView->getNovalnetNotification($sPaymentID) != '' }]
		<br><br>[{$oView->getNovalnetNotification($sPaymentID)}]
	[{/if}]
	[{if $oView->getNovalnetTestmode($sPaymentID) }]
		<br><br><span style="color:red">[{ oxmultilang ident='NOVALNET_TEST_MODE_MESSAGE' }]</span>
	[{/if}]
	[{if $aSepaDetails.blZeroBook }]
		<br><br><span style="color:red">[{ oxmultilang ident='NOVALNET_ZERO_AMOUNT_MESSAGE' }]</span>
	[{/if}]
</div>
[{/block}]

