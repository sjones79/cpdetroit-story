$(function() {

	// Get the form.
	var form = $('#event-contact');

	// Get the messages div.
	var formMessages = $('#form-messages');

	// Set up an event listener for the contact form.
	$(form).submit(function(e) {
		// Stop the browser from submitting the form.
		e.preventDefault();

		// Serialize the form data.
		var formData = $(form).serialize();

		// Submit the form using AJAX.
		$.ajax({
			type: 'POST',
			url: $(form).attr('action'),
			data: formData
		})
		.done(function(response) {
            
            //show the thank you view
            $('#eventform-main').hide();
            $('#eventthankyou').css('display', 'flex');
            //clear form fields
            $("#event-contact")[0].reset();
		})
		.fail(function(data) {
			// Make sure that the formMessages div has the 'error' class.
			$(formMessages).removeClass('success');
			$(formMessages).addClass('error');

			// Set the message text.
            $(formMessages).text('Oops! There was a problem with your submission. Please complete the form and try again.');
			
		});

	});

});