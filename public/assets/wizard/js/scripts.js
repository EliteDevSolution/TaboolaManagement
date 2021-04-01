let firstAccept = false, secondAccept = false;
function scroll_to_class(element_class, removed_height) {
	var scroll_to = $(element_class).offset().top - removed_height;
	if($(window).scrollTop() != scroll_to) {
		$('html, body').stop().animate({scrollTop: scroll_to}, 0);
	}
}

function bar_progress(progress_line_object, direction) {
	var number_of_steps = progress_line_object.data('number-of-steps');
	var now_value = progress_line_object.data('now-value');
	var new_value = 0;
	if(direction == 'right') {
		new_value = now_value + ( 100 / number_of_steps );
	}
	else if(direction == 'left') {
		new_value = now_value - ( 100 / number_of_steps );
	}
	progress_line_object.attr('style', 'width: ' + new_value + '%;').data('now-value', new_value);
}

jQuery(document).ready(function() {

	$('#email').val(localStorage.getItem('reg_email'));

	$('#btn-firstpage').click(function(evt){
		location.href = "/";
	});

	$('#accept_1').click(function(){
		if($(this).prop("checked") == true){
			firstAccept = true;
			if(firstAccept && secondAccept)
				$('#btn_submit').removeAttr('disabled');
		}
		else if($(this).prop("checked") == false){
			firstAccept = false;
			$('#btn_submit').attr('disabled', true);
		}
	});

	$('#accept_2').click(function(){
		if($(this).prop("checked") == true){
			secondAccept = true;
			if(firstAccept && secondAccept)
				$('#btn_submit').removeAttr('disabled');
		}
		else if($(this).prop("checked") == false){
			secondAccept = false;
			$('#btn_submit').attr('disabled', true);
		}
	});


    $('#top-navbar-1').on('shown.bs.collapse', function(){
    	$.backstretch("resize");
    });
    $('#top-navbar-1').on('hidden.bs.collapse', function(){
    	$.backstretch("resize");
    });
    
    /*
        Form
    */
    $('.f1 fieldset:first').fadeIn('slow');
    
    $('.f1 input[type="text"], .f1 input[type="password"], .f1 input[type="email"], .f1 textarea').on('focus', function() {
    	$(this).removeClass('input-error');
    });
    
    // next step
    $('.f1 .btn-next').on('click', function() {
    	var parent_fieldset = $(this).parents('fieldset');
    	var next_step = true;
    	// navigation steps / progress steps
    	var current_active_step = $(this).parents('.f1').find('.f1-step.active');
    	var progress_line = $(this).parents('.f1').find('.f1-progress-line');
    	
    	// fields validation
    	parent_fieldset.find('input[type="text"], input[type="password"], input[type="email"], textarea').each(function() {
    		if( $(this).val() == "" && $(this).attr('id') != 'phone' && $(this).attr('id') != 'other_info') {
    			$(this).addClass('input-error');
    			next_step = false;
    		}
    		else {
    			if($(this).attr('id') == 'email' && !/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test($('#email').val()))
				{
					$(this).addClass('input-error');
					next_step = false;
				} else
				{
					$(this).removeClass('input-error');
				}
    		}
    	});
    	// fields validation
    	
    	if( next_step ) {
    		parent_fieldset.fadeOut(400, function() {
    			// change icons
    			current_active_step.removeClass('active').addClass('activated').next().addClass('active');
    			// progress bar
    			bar_progress(progress_line, 'right');
    			// show next step
	    		$(this).next().fadeIn();
	    		// scroll window to beginning of the form
    			scroll_to_class( $('.f1'), 20 );
	    	});
    	}
    	
    });
    
    // previous step
    $('.f1 .btn-previous').on('click', function() {
    	// navigation steps / progress steps
    	var current_active_step = $(this).parents('.f1').find('.f1-step.active');
    	var progress_line = $(this).parents('.f1').find('.f1-progress-line');
    	
    	$(this).parents('fieldset').fadeOut(400, function() {
    		// change icons
    		current_active_step.removeClass('active').prev().removeClass('activated').addClass('active');
    		// progress bar
    		bar_progress(progress_line, 'left');
    		// show previous step
    		$(this).prev().fadeIn();
    		// scroll window to beginning of the form
			scroll_to_class( $('.f1'), 20 );
    	});
    });
    
    // submit
    $('.f1').on('submit', function(e) {

    	var parent_fieldset = $(this).parents('fieldset');

    	// fields validation
		parent_fieldset.find('input[type="text"], input[type="password"], input[type="email"], textarea').each(function() {
			if( $(this).val() == "" && $(this).attr('id') != 'phone' && $(this).attr('id') != 'other_info') {
    			e.preventDefault();
    			$(this).addClass('input-error');
    		}
    		else {
				if($(this).attr('id') == 'email' && !/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test($('#email').val()))
				{
					$(this).addClass('input-error');
					next_step = false;
				} else
				{
					$(this).removeClass('input-error');
				}
    		}
    	});
    	// fields validation
    	
    });

});
