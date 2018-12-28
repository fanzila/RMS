function validator() {
	
	var validator = $( "#pos" ).validate({	
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
			}, 
			mov: {
				required: true,
				minlength: 1
			}
		},
		messages: {
			user: "Please specify your name",
			mov: "Please specify a movement",
		}
		
	});
	
	validator.form();
	var errors = 0;
	var errors = validator.numberOfInvalids();
	var unchecked = $( "input:checkbox:unchecked" );
	var checked = $( "input:checkbox:checked" );
	var username = $( "select option:selected" );	
	
	if(username.text() == 'User') {			
		$('#user').css( "background-color", "#a1ff7c" );
		errors = errors + 1;
	}	

	if(errors < 1) {
		$("div.error").hide();
		if(window.confirm('Do you swear on your honor that you really have done the things that you\'re signing on ?')) {
			document.pos.submit();
		}
	}
}
