[{assign var="aPaypalDetails" value=$oView->getNovalnetPaymentDetails($sPaymentID)}]
[{assign var="displayPaypalPort" value=""}]
[{if $aPaypalDetails.iShopType == 1}]
    [{assign var="displayPaypalPort" value="style='display:none;'"}]
    <input type="hidden" name="dynvalue[novalnet_paypal_new_details]" id="novalnet_paypal_new_details" value="[{$aPaypalDetails.blOneClick}]" />
    <li class='novalnet_paypal_ref_acc'>
        <table>
            <tr>
                <td colspan="2"><span id="novalnet_paypal_ref_acc" style="color:blue; text-decoration:underline; cursor:pointer;" onclick="changePaypalAccountType(event, 'novalnet_paypal_new_acc')">[{oxmultilang ident="NOVALNET_PAYPAL_NEW_ACCOUNT_DETAILS"}]</span></td>
            </tr>
            [{if $aPaypalDetails.paypal_transaction_id}]
                <tr>
                    <td>[{oxmultilang ident="NOVALNET_PAYPAL_REFERENCE_TID" }]</td>
                    <td>[{$aPaypalDetails.paypal_transaction_id}]</td>
                </tr>
            [{/if}]
            <tr>
                <td>[{oxmultilang ident="NOVALNET_REFERENCE_TID" }]</td>
                <td>[{$smarty.session.sPaymentRefnovalnetpaypal}]</td>
            </tr>
        </table>
    </li>
    <li class='novalnet_paypal_new_acc' [{$displayPaypalPort}]>
        <span id='novalnet_paypal_new_acc' style="color:blue; text-decoration:underline; cursor:pointer;" onclick="changePaypalAccountType(event, 'novalnet_paypal_ref_acc')">[{oxmultilang ident="NOVALNET_PAYPAL_GIVEN_ACCOUNT_DETAILS"}]</span>
    </li>
[{/if}]
[{oxscript add="$('#paymentOption_$sPaymentID').find('.dropdown').oxDropDown();"}]
[{oxscript include=$oViewConf->getModuleUrl('novalnet', 'out/src/js/novalnetpaypal.js')}]
[{block name="checkout_payment_longdesc"}]
    <li>
        <div class="payment-desc">
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
        </div>
    </li>
[{/block}]
