/**
 * Loads Creditcard Iframe
 *
 */
function loadCreditcardIframe() {
    if($('#novalnet_cc_new_details').length && $('#novalnet_cc_new_details').val() == 1) {
        $('.novalnet_cc_new_acc').show();
        $('.novalnet_cc_ref_acc').hide();
    }

    var request = {
        callBack    : 'createElements',
        customStyle : {
            labelStyle : $('#novalnet_cc_default_label').val(),
            inputStyle : $('#novalnet_cc_default_input').val(),
            styleText  : $('#novalnet_cc_default_css').val(),
        },
        customText  : {
            card_holder : {
                labelText : $('#novalnet_cc_holder_label_text').val(),
                inputText : $('#novalnet_cc_holder_placeholder').val(),
            },
            card_number : {
                labelText : $('#novalnet_cc_number_label_text').val(),
                inputText : $('#novalnet_cc_number_placeholder').val(),
            },
            expiry_date : {
                labelText : $('#novalnet_cc_exp_label_text').val(),
                inputText : $('#novalnet_cc_exp_placeholder').val(),
            },
            cvc  : {
                labelText : $('#novalnet_cc_cvc_input_text').val(),
                inputText : $('#novalnet_cc_cvc_placeholder').val(),
            },
            cvcHintText : $('#novalnet_cc_cvc_hint').val(),
            errorText   : $('#novalnet_cc_error_text').val(),
        }
    };
    loadNovalnetIframe(request);
    $('#novalnetiframe').attr('height', 0);
    loadNovalnetIframe({callBack : 'getHeight'});
}

$(document).ready(function() {
    var novalnetCCForm      = $('#novalnetiframe').closest('form').attr('id');
    var novalnetCCsubmit    = $('#' + novalnetCCForm).find(':submit');
    var iframe              = $('#novalnetiframe')[0];

    $(novalnetCCsubmit).click(function(e) {
        if ($("input[name=paymentid]:checked").val() == 'novalnetcreditcard' && (!$('#novalnet_cc_new_details').length || $('#novalnet_cc_new_details').val() == 1)) {
            $('#novalnet_invalid_card_details').css('display', "none");
            loadNovalnetIframe({callBack : 'getHash'});
            if ($('#novalnet_cc_hash').val() == '')
                return false;
        }
        
    });
    
    if($('#novalnet_cc_disclosure_text').val() == 1 && $('#novalnet_cc_disclosure_text').val() != undefined)
		$('#novalnet_cc_disclosure_confirm').css('display', 'block');
	else
		$('#novalnet_cc_disclosure_confirm').css('display', 'none');
	
   $('#novalnet_cc_confirmation').change(function() {
		 if(this.checked) {
			 $('#novalnet_cc_disclosure').val(1);
		 } else {
			 $('#novalnet_cc_disclosure').val(0);
		 }
	});
});

$("input:radio[name=paymentid], #novalnet_cc_ref_acc").click(function() {
    if (this.value == 'novalnetcreditcard' || this.id == 'novalnet_cc_ref_acc') {
        $('#novalnetiframe').attr('height', 0);
        loadNovalnetIframe({callBack : 'getHeight'});
        $(this.id).unbind( "click" );
        return true;
    }
});

$("#sPaymentSelected").change(function() {
    if (this.value == 'novalnetcreditcard') {
        $('#novalnetiframe').attr('height', 0);
        loadNovalnetIframe({callBack : 'getHeight'});
    }
});

$(window).resize(function() {
    $('#novalnetiframe').attr('height', 0);
    loadNovalnetIframe({callBack : 'getHeight'});
});

if (window.addEventListener) {
    window.addEventListener('message', function(e) {
        performNovalnetMessage(e);
    }, false);

} else {
    window.attachEvent('onmessage', function (e) {
        performNovalnetMessage(e);
    });
}

/**
 * Loads novalnet iframe
 *
 */
function loadNovalnetIframe(request) {
    var iframe = $('#novalnetiframe')[0];
    var novalnetTargetOrgin = 'https://secure.novalnet.de';
    iframeWindow = iframe.contentWindow ? iframe.contentWindow : iframe.contentDocument.defaultView;
    iframeWindow.postMessage(request, novalnetTargetOrgin);
}

/**
 * Perform the message listener
 *
 */
function performNovalnetMessage(e) {
    if (typeof e.data === 'string') {
        var data = eval('(' + e.data + ')');
    } else {
        var data = e.data;
    }
    if (e.origin === 'https://secure.novalnet.de') {
        if (data['callBack'] == 'getHash') {
            if(data['error_message'] != undefined) {
                $('#novalnet_invalid_card_details').css('display', 'block');
                $('#novalnet_invalid_card_details').html(data['error_message']);
            } else {
                var novalnetCCForm = $('#novalnetiframe').closest('form');
                $('#novalnet_invalid_card_details').css('display', 'none');
                $('#novalnet_cc_hash').val(data['hash']);
                $('#novalnet_cc_uniqueid').val(data['unique_id']);
                $(novalnetCCForm).submit();
            }
        } else if(data['callBack'] == 'getHeight') {
            $('#novalnetiframe').attr('height', data['contentHeight']);
        }
    }
}

/**
 * Manages the onclick shopping form
 *
 */
function changeCCAccountType(event, accType)
{
    var currentAccType = event.target.id;
    $('.' + currentAccType).hide();
    $('.' + accType).show();
    if (accType == 'novalnet_cc_new_acc') {
        $('#novalnet_cc_new_details').val(1);
        $('#novalnet_cc_disclosure_confirm').css('display', 'block');
    } else {
        $('#novalnet_cc_new_details').val(0);
        $('#novalnet_cc_disclosure_confirm').css('display', 'none');
	}
}

/**
 * Validates the triggered key is valid
 *
 * @returns {boolean}
 */
function isValidKeyCC(evt, allowSpace)
{
    var keycode = ('which' in evt) ? evt.which : evt.keyCode;
    var reg = /^(?:[0-9]+$)/;

    return (reg.test(String.fromCharCode(keycode)) || keycode == 0 || keycode == 8 || (evt.ctrlKey == true && keycode == 114) || (allowSpace == true && keycode == 32)) ? true : false;
}
