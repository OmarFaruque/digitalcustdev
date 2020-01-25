jQuery(document).ready(function($) {
	var timer =  $('.countdown span').text();
		if (timer.includes('00:00:00')) { 
			$('.countdown span').html('--:--:--');
		}
});