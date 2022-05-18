jQuery( document ).ready(function() {
	var x = getCookie('cf_email');
	if (!x) {
	// display modal on page load	
		jQuery('#myModal').modal('show');
	}
		
	// newsletter form submit event
	jQuery("body").on('click', '#submitted', function (e) {
		var error = 0;
		var error_msg = '';
		if (jQuery('#cf_name').val() == "") {
			error_msg = 'Please enter name';
			error = 1;
		}
		if (jQuery('#cf_email').val() == "") {
			error_msg = 'Please enter email';
			error = 1;
		} else {
			if( !isEmail(jQuery('#cf_email').val())) {
				error_msg = 'Please enter valid email';
				error = 1;
			}
		}
		if (jQuery('#cf_name').val() == "" && jQuery('#cf_email').val() == "") {
			error_msg = 'Please enter name and email';
			error = 1;
		}
		
		if(error == 0) {
			jQuery(".cf-error-message").html("");
			var data = {
				action: 'my_action',
				type: 'add',
				name: jQuery("#cf_name").val(),			
				email: jQuery("#cf_email").val()
			};

			jQuery.post(my_ajax_object.ajax_url, data, function(response) {
				var output = JSON.parse(response);
				if(output.status == 200) {					
					// set email as cookie
					setCookie('cf_email',jQuery("#cf_email").val(),7);
					jQuery(".cf-success-message").show();
					jQuery(".newsletter-form").hide();	
					setTimeout(function(){
					  $('#myModal').modal('hide')
					}, 2000);					
				} else {
					jQuery(".cf-error-message").html(output.message);
				}
			});
		} else {
			jQuery(".cf-error-message").html(error_msg);
		}
		e.preventDefault();
	});
	
	// to check if email is valid
	function isEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}
	
	// to set the new cookie
	function setCookie(name,value,days) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "")  + expires + "; path=/";
	}
	
	// to get the saved cookie
	function getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}
	
	// to erase the cookie
	function eraseCookie(name) {   
		document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
	}
});