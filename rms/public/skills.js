function validator() {

	var validator = $( "#skills" ).validate({	
		submitHandler: function(form) {	
		},
		errorClass: "form-invalid",
		validClass: "form-success",
		debug: true,
		rules: {
		/*	user: {
				required: true,
				min: 1,
				number: true
			}*/
		},
		messages: {
			user: "Please specify your name."
		}
	});

	var dt 				= new Date();
	var time2 			= dt.getHours() + '' + dt.getMinutes();
	var time			= parseFloat(time2);
	
	var id_checklist 	= $("#id_checklist").val();
	
	validator.form();
	var errors = 0;
	var errors = validator.numberOfInvalids();
	var unchecked_lengh = $( "input:checkbox:unchecked" ).length;
	var checked_lengh = $( "input:checkbox:checked" ).length;
	var unchecked = $( "input:checkbox:unchecked" );
	var checked = $( "input:checkbox:checked" );
//	var username = $( "select option:selected" );
	
/*	if(username.text() == 'User') {	

		alert('Please specify your name.');
		$('#user').css( "background-color", "#a1ff7c" );
		errors = errors + 1;
	}*/	

	if(unchecked_lengh >= 1) {	
		$.each(unchecked, function(key, val) {
			var task_error_name		= $("#label-"+val.name).text();
			var task_error_comment	= $("#comment-"+val.name).val();

			$('label#label-'+val.name).css( "background-color", "#a1ff7c" );

			if(task_error_comment.length <= 1) {
				errors = errors + 1;
				$('label#label-'+val.name).css( "background-color", "#ff4741" );
			}
		});
	}

	if(errors >= 1) { 
		$( "div.error_cont" ).text( "You must check ALL checkbox!" );
	}
	if(errors < 1) {
		$("div.error").hide();
		if(window.confirm('Do you swear on your honor that you have VERY carefully read this checklist and you did not forgot something?')) {
			document.tasks.submit();
		}
	}
}

/**
$('#tasks').change(function() {
	var unchecked_lengh = $( "input:checkbox:unchecked" ).length;
	var username = $( "select option:selected" );
	
	if(unchecked_lengh == 0) {		
		if(username.text() == 'User') {	
			alert('Please specify your name.');
			$('#user').css( "background-color", "#a1ff7c" );
		} else {
			if(window.confirm('Do you swear on your honor that you have VERY carefully read this checklist and you did not forgot something?')) {
				document.tasks.submit();
			}
		}
	}
});
**/

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
