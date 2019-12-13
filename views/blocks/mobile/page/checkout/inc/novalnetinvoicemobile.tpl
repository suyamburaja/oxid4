[{if !empty($smarty.session.sCallbackTidnovalnetinvoice)}]
    [{if in_array($oView->getNovalnetConfig('iCallbacknovalnetinvoice'), array(1, 2))}]
        <li>
            <input type="text" size="20" name="dynvalue[pinno_novalnetinvoice]" autocomplete="off" placeholder="[{oxmultilang ident="NOVALNET_FRAUD_MODULE_PIN"}]" />
        </li>
        <li>
            <input type="checkbox" size="20" name="dynvalue[newpin_novalnetinvoice]"> [{ oxmultilang ident="NOVALNET_FRAUD_MODULE_FORGOT_PIN" }]
        </li>
    [{/if}]
[{else}]
    [{if $smarty.session.blGuaranteeEnablednovalnetinvoice }]
        <li>
            <label>[{oxmultilang ident="NOVALNET_BIRTH_DATE"}]</label>
            <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" id="novalnet_invoice_birth_date" name="dynvalue[birthdatenovalnetinvoice]" autocomplete="off" placeholder="YYYY-MM-DD" value="[{$oView->getNovalnetBirthDate()}]" />
            <p class="validation-error">
                <span class="js-oxError_notEmpty">[{oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS"}]</span>
            </p>
        </li>
    [{/if}]
    [{if $oView->getFraudModuleStatus($sPaymentID) }]
        [{if $oView->getNovalnetConfig('iCallbacknovalnetinvoice') == 1}]
            <li>
                <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" name="dynvalue[pinbycall_novalnetinvoice]" autocomplete="off" placeholder="[{oxmultilang ident="NOVALNET_FRAUD_MODULE_PHONE"}]" value="[{$oxcmp_user->oxuser__oxfon->value}]" />
                <p class="validation-error">
                    <span class="js-oxError_notEmpty">[{oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS"}]</span>
                </p>
            </li>
        [{elseif $oView->getNovalnetConfig('iCallbacknovalnetinvoice') == 2}]
            <li>
                <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" name="dynvalue[pinbysms_novalnetinvoice]" autocomplete="off" placeholder="[{oxmultilang ident="NOVALNET_FRAUD_MODULE_MOBILE"}]" value="[{$oxcmp_user->oxuser__oxmobfon->value}]" />
                <p class="validation-error">
                    <span class="js-oxError_notEmpty">[{oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS"}]</span>
                </p>
            </li>
        [{/if}]
    [{/if}]
[{/if}]
