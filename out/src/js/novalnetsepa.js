$(document).ready(function() {
    if($('#novalnet_sepa_new_details').length && $('#novalnet_sepa_new_details').val() == 1) {
        $('.novalnet_sepa_new_acc').show();
        $('.novalnet_sepa_ref_acc').hide();
    }
    
    if($('#novalnet_sepa_disclosure_text').val() == 1 && $('#novalnet_sepa_disclosure_text').val() != undefined)
		$('#novalnet_sepa_disclosure_confirm').css('display', 'block');
	else
		$('#novalnet_sepa_disclosure_confirm').css('display', 'none');
	
   $('#novalnet_sepa_confirmation').change(function() {
		 if(this.checked) {
			 $('#novalnet_sepa_disclosure').val(1);
		 } else {
			 $('#novalnet_sepa_disclosure').val(0);
		 }
	});
});


/**
 * Toggles account type while onclick shopping enabled for sepa
 *
 */
function changeSepaAccountType(event, accType)
{
    var currentAccType = event.target.id;
    $('.' + currentAccType).hide();
    $('.' + accType).show();
    if (accType == 'novalnet_sepa_new_acc') {
		$('#novalnet_sepa_disclosure_confirm').css('display', 'block');
        $('#novalnet_sepa_new_details').val(1);
    } else {
        $('#novalnet_sepa_new_details').val(0);
        $('#novalnet_sepa_disclosure_confirm').css('display', 'none');
	}
}


/**
 * Validates entered key in input of sepa form
 *
 * @returns {boolean}
 */
function isValidKeySepa(evt)
{
    var keycode = ('which' in evt) ? evt.which : evt.keyCode;
    var reg = /^(?:[A-Za-z0-9]+$)/;
    if(evt.target.id == 'novalnet_sepa_holder') {
        var reg = /^(?:[A-Za-züÜäÄöÖß&.-\s]+$)/;
    }
    return (reg.test(String.fromCharCode(keycode)) || keycode == 0 || keycode == 8 || (evt.ctrlKey == true && keycode == 114) || (evt.target.id == 'novalnet_sepa_holder' && keycode == 45)) ? true : false;
}


/**
 * Refills the account details in mobile theme
 *
 */
function novalnetMobileRefillSepa(id)
{
    var element = $(id).parent();
    var selectedvalue = $(element.find('span'));
    var dropdownoptions = $(element.find('.dropdown-option'));
    dropdownoptions.removeClass('selected');
    var selectedoption = $(element.find('a[data-selection-id="' + $(id).val() + '"]'));
    selectedvalue.html($(selectedoption).html());
    selectedoption.parent().addClass('selected');
}

/**
 * Remove special characters and spaces
 *
 */
function removeSpecialCharactersSepa(input)
{
    return input.replace(/[^a-zA-Z0-9]/g, '');
}
