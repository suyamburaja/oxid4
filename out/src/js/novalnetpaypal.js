$(document).ready(function() {
    if($('#novalnet_paypal_new_details').length && $('#novalnet_paypal_new_details').val() == 1) {
        $('.novalnet_paypal_new_acc').show();
        $('.novalnet_paypal_ref_acc').hide();
    }
    
    if($('#novalnet_paypal_disclosure_text').val() == 1 && $('#novalnet_paypal_disclosure_text').val() != undefined)
		$('#novalnet_paypal_disclosure_confirm').css('display', 'block');
	else
		$('#novalnet_paypal_disclosure_confirm').css('display', 'none');
	
   $('#novalnet_paypal_confirmation').change(function() {
		 if(this.checked) {
			 $('#novalnet_paypal_disclosure').val(1);
		 } else {
			 $('#novalnet_paypal_disclosure').val(0);
		 }
	});
});

/**
 * Manages the onclick shopping form
 *
 */
function changePaypalAccountType(event, accType)
{
    var currentAccType = event.target.id;
    $('.' + currentAccType).hide();
    $('.' + accType).show();
    if (accType == 'novalnet_paypal_new_acc') {
		$('#novalnet_paypal_disclosure_confirm').css('display', 'block');
        $('#novalnet_paypal_new_details').val(1);
	} else {
        $('#novalnet_paypal_new_details').val(0);
        $('#novalnet_paypal_disclosure_confirm').css('display', 'none');
	}
}
