function validator() {
	
	var validator = $( "#discount" ).validate({	
		submitHandler: function(form) {	
		},
		errorClass: "form-invalid",
		validClass: "form-success",
		debug: true,
		rules: {
			user: {
				required: true,
				min: 1,
				number: true
			}
		},
		messages: {
			user: "Please specify your name"
		}
	});
	
	validator.form();
	var errors = 0;
	var errors = validator.numberOfInvalids();
	var username = $( "select option:selected" );
	
	if(username.text() == 'User') {	
		
		alert('Please specify your name.');
		$('#user').css( "background-color", "#a1ff7c" );
		errors = errors + 1;
	}	
	
	if(errors < 1) {
		$("div.error").hide();
		if(window.confirm('Do you swear on your honor that you really have done the things that you\'re signing on ?')) {
			document.discount.submit();
		}
	}
}

$(document).ready(function() {
    $('body').append('<a href="#top" class="back-to-top">Back to top</a>');
    $('a.back-to-top').click(function(e){
		$('html, body').animate({scrollTop:0}, 'fast');
        e.preventDefault();
    });
    $(window).scroll(function() {
        if ($('body').offset().top < $(window).scrollTop()) {
            $('.back-to-top').slideDown('fast');
        } else {
            $('.back-to-top').slideUp('fast');
        }
    });
});
