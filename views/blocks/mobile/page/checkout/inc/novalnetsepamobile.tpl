[{if $oView->getNovalnetConfig('blAutoRefill') }]
    [{ assign var="dynvalue" value=$oView->getDynValue()}]
[{/if}]
[{if !empty($smarty.session.sCallbackTidnovalnetsepa)}]
    [{if in_array($oView->getNovalnetConfig('iCallbacknovalnetsepa'), array(1, 2))}]
        <li>
            <input type="text" size="20" name="dynvalue[pinno_novalnetsepa]" autocomplete="off" placeholder="[{oxmultilang ident="NOVALNET_FRAUD_MODULE_PIN"}]" />
        </li>
        <li>
            <input type="checkbox" size="20" name="dynvalue[newpin_novalnetsepa]" /> &nbsp;[{ oxmultilang ident="NOVALNET_FRAUD_MODULE_FORGOT_PIN" }]
        </li>
    [{/if}]
[{else}]
    [{assign var="aSepaDetails" value=$oView->getNovalnetPaymentDetails($sPaymentID)}]
    [{assign var="displaySepaForm" value=""}]
    [{if $aSepaDetails.iShopType == 1}]
        [{assign var="displaySepaForm" value="style='display:none;'"}]
        <input type="hidden" name="dynvalue[novalnet_sepa_new_details]" id="novalnet_sepa_new_details" value=[{$aSepaDetails.blOneClick}] />
        <li class='novalnet_sepa_ref_acc'>
            <table>
                <tr>
                    <td colspan="2"><span id="novalnet_sepa_ref_acc" style="color:blue; text-decoration:underline; cursor:pointer;" onclick="changeSepaAccountType(event, 'novalnet_sepa_new_acc')">[{oxmultilang ident="NOVALNET_NEW_ACCOUNT_DETAILS"}]</span></td>
                </tr>
                <tr>
                    <td>[{oxmultilang ident="NOVALNET_SEPA_HOLDER_NAME"}]</td>
                    <td>[{$aSepaDetails.bankaccount_holder}]</td>
                </tr>
                <tr>
                    <td>IBAN</td>
                    <td>[{$aSepaDetails.iban}]</td>
                </tr>
                [{if $aSepaDetails.bic != '123456'}]
                    <tr>
                        <td>BIC</td>
                        <td>[{$aSepaDetails.bic}]</td>
                    </tr>
                [{/if}]
            </table>
        </li>

        <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
            <span id='novalnet_sepa_new_acc' style="color:blue; text-decoration:underline; cursor:pointer;" onclick="changeSepaAccountType(event, 'novalnet_sepa_ref_acc')">[{oxmultilang ident="NOVALNET_GIVEN_ACCOUNT_DETAILS"}]</span>
        </li>
    [{/if}]

    <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
        <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" id="novalnet_sepa_holder" name="dynvalue[novalnet_sepa_holder]" value="[{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}]" placeholder="[{oxmultilang ident="NOVALNET_SEPA_HOLDER_NAME"}]" onkeypress="return isValidKeySepa(event);" />
        <p class="validation-error">
            <span class="js-oxError_notEmpty">[{oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS"}]</span>
        </p>
    </li>
    <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
        <div class="dropdown">
            <input type="hidden" id="novalnet_sepa_country" />
            <div class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                <a id="novalnetCountrySelected" role="button" href="#">
                    <span id="dNovalnetCountrySelected"></span>
                    <i class="glyphicon-chevron-down"></i>
                </a>
            </div>
            <ul class="dropdown-menu" role="menu" aria-labelledby="novalnetCountrySelected">
                [{foreach from=$oViewConf->getCountryList() item=country}]
                    [{assign var=sCountryName value=$country->oxcountry__oxtitle->value}]
                    [{assign var=sCountryID value=$country->oxcountry__oxisoalpha2->value}]
                    <li class="dropdown-option" [{if $oxcmp_user->oxuser__oxcountryid->value == $country->oxcountry__oxid->value}]selected[{/if}]>
                        <a tabindex="-1" data-selection-id="[{$sCountryID}]">[{$sCountryName}]</a>
                    </li>
                    [{if $oxcmp_user->oxuser__oxcountryid->value == $country->oxcountry__oxid->value}]
                        [{oxscript add="$('#novalnet_sepa_country').val('$sCountryID');"}]
                        [{oxscript add="$('#dNovalnetCountrySelected').html('$sCountryName');"}]
                    [{/if}]
                [{/foreach}]
            </ul>
        </div>
    </li>
    <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
        <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" id="novalnet_sepa_acc_no" autocomplete="off" placeholder="[{oxmultilang ident="NOVALNET_SEPA_IBAN"}]" onkeypress="return isValidKeySepa(event);" />
        <span id="novalnet_sepa_iban_span"></span>
        <p class="validation-error">
            <span class="js-oxError_notEmpty">[{oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS"}]</span>
        </p>
    </li>
    <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
        <input type="text" size="20" id="novalnet_sepa_bank_code" autocomplete="off" placeholder="[{oxmultilang ident="NOVALNET_SEPA_BIC"}]" onkeypress="return isValidKeySepa(event);" />
        <span id="novalnet_sepa_bic_span"></span>
    </li>
    [{if !empty($smarty.session.blGuaranteeEnablednovalnetsepa) && empty($smarty.session.blGuaranteeForceDisablednovalnetsepa) }]
        <li>
            <label>[{oxmultilang ident="NOVALNET_BIRTH_DATE"}]</label>
            <input type="text" size="20" id="novalnet_sepa_birth_date" name="dynvalue[birthdatenovalnetsepa]" autocomplete="off" placeholder="YYYY-MM-DD" value="[{$oView->getNovalnetBirthDate()}]" />
        </li>
    [{/if}]
    <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
        <input type="checkbox" class="js-oxValidate js-oxValidate_notEmpty" size="20" id="novalnet_sepa_mandate_confirm" autocomplete="off" /> &nbsp;[{ oxmultilang ident="NOVALNET_SEPA_MANDATE_TERMS" }]
        <span class="novalnetloader" id="novalnet_sepa_loader"></span>
        <input type="hidden" id="novalnet_sepa_invalid_message" value="[{ oxmultilang ident="NOVALNET_SEPA_INVALID_DETAILS" }]" />
        <input type="hidden" id="novalnet_sepa_unconfirm_message" value="[{ oxmultilang ident="NOVALNET_SEPA_UNCONFIRM_DETAILS" }]" />
        <input type="hidden" id="novalnet_sepa_country_invalid_message" value="[{ oxmultilang ident="NOVALNET_SEPA_INVALID_COUNTRY" }]" />
        <input type="hidden" id="novalnet_sepa_merchant_invalid_message" value="[{ oxmultilang ident="NOVALNET_INVALID_MERCHANT_DETAILS" }]" />
        <input type="hidden" id="novalnet_sepa_vendor_id" value="[{$aSepaDetails.iVendorId}]" />
        <input type="hidden" id="novalnet_sepa_vendor_authcode" value="[{$aSepaDetails.sAuthCode}]" />
        <input type="hidden" id="novalnet_remote_ip" value="[{$oView->getNovalnetRemoteIp()}]">
        <input type="hidden" id="novalnet_sepa_iban" />
        <input type="hidden" id="novalnet_sepa_bic" />
        [{oxscript include=$oViewConf->getModuleUrl('novalnet', 'out/src/js/novalnetsepa.js')}]
        [{oxstyle  include=$oViewConf->getModuleUrl('novalnet', 'out/src/css/novalnet.css')}]
    </li>

    [{if $oView->getFraudModuleStatus($sPaymentID) }]
        [{if $oView->getNovalnetConfig('iCallbacknovalnetsepa') == 1}]
            <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
                <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" name="dynvalue[pinbycall_novalnetsepa]" autocomplete="off" placeholder="[{oxmultilang ident="NOVALNET_FRAUD_MODULE_PHONE"}]" value="[{$oxcmp_user->oxuser__oxfon->value}]" />
                <p class="validation-error">
                    <span class="js-oxError_notEmpty">[{oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS"}]</span>
                </p>
            </li>
        [{elseif $oView->getNovalnetConfig('iCallbacknovalnetsepa') == 2}]
            <li class='novalnet_sepa_new_acc' [{$displaySepaForm}]>
                <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" name="dynvalue[pinbysms_novalnetsepa]" autocomplete="off" placeholder="[{oxmultilang ident="NOVALNET_FRAUD_MODULE_MOBILE"}]" value="[{$oxcmp_user->oxuser__oxmobfon->value}]" />
                <p class="validation-error">
                    <span class="js-oxError_notEmpty">[{oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS"}]</span>
                </p>
            </li>
        [{/if}]
    [{/if}]
    <input type="hidden" name="dynvalue[novalnet_sepa_uniqueid]" id="novalnet_sepa_uniqueid" value="" />
    <input type="hidden" name="dynvalue[novalnet_sepa_hash]" id="novalnet_sepa_hash" value="" />
    [{oxscript add="$('#paymentOption_$sPaymentID').find('.dropdown').oxDropDown();"}]
[{/if}]
