(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work...
	 */

	jQuery('.rwmb-input').find('input[name="_lp_price"]').removeAttr('min');
	jQuery('.rwmb-input').find('input[name="_lp_price"]').addClass('minOmar');
	 
	
	/*
	* Lesson video change event
	*/
	jQuery(document).on('click', 'a[data-id="intro_video_lesson"]', function(e){
		var thisitem = jQuery(this);
		e.preventDefault();
		var file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Select or upload file',
			library: { // remove these to show all
				type: ['video'] // specific mime
			},
			button: {
				text: 'Select'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});

		file_frame.open();


		  // file_frame.close();
		  file_frame.on('select', function () {
			// We set multiple to false so only get one image from the uploader
			var attachment = file_frame.state().get('selection').first().toJSON();
			/*
			* Save via auto-save
			*/
		   	/* Nothing */
				var outputhtml = '<div data-name="upload_intro_video" data-type="file" data-key="field_5d52623d7778a" class="acf-field acf-field-file acf-field-5d52623d7778a">'
				+'<div class="acf-input omar">'
					+'<div data-library="uploadedTo" data-mime_types="mp4" data-uploader="wp" class="acf-file-uploader has-value">'                                  
						+'<div class="show-if-value file-wrap">'
							+'<div class="file-icon">'
								+'<img data-name="icon" src="'+attachment.icon+'" alt="" title="'+attachment.title+'">'
							+'</div> '
							+'<div class="file-info">'
								+'<p><strong data-name="title">'+attachment.title+'</strong></p> '
								+'<p><strong>File name:</strong> '
								+'<a data-name="filename" href="'+attachment.url+'" target="_blank">'+attachment.filename+'</a></p> '
								+'<p><strong>File size:</strong> <span data-name="filesize">'+attachment.filesizeHumanReadable+'</span></p>'
							+'</div> '
							+'<div class="acf-actions -hover">'
								+'<a href="#" data-action="delete_admin_intro" title="Remove" class="acf-icon -cancel remove_lesson_media_attachment dark"></a>'
							+'</div>'
						+'</div>'
						+'</div>'
					+'</div>'
				+'</div>';
				thisitem.closest('.acf-file-uploader').addClass('has-value');
				thisitem.closest('.acf-file-uploader').find('.file-wrap').remove();
				thisitem.closest('.acf-file-uploader').prepend(outputhtml);
				delete attachment.compat;
				// console.log(attachment);
				jQuery('input[name="_lp_lesson_video_intro_internal"]').val(JSON.stringify(attachment));
		 });
	});


	/*
	* Delete Lesson Inro Video
	*/
	jQuery(document).on('click', 'a[data-action="delete_admin_intro"]', function(e){
		e.preventDefault();
		var thisitem = jQuery(this);
		jQuery('input[name="_lp_lesson_video_intro_internal"]').val('');
		thisitem.closest('.acf-file-uploader').find('.acf-field.acf-field-file').remove();
		thisitem.closest('.acf-file-uploader').removeClass('has-value');
	});

	// jQuery('.xdsoft_datepicker').datetimepicker();

	var getTimesbyDate = function (result, value) {
		var allowed_times = [];
		if (result.date === value) {
			$.each(result.allowed_times, function (c, t) {
				// console.log('t: ' + t);
				// console.log('c: ' + c);
				allowed_times.push(t);
			});
		}

		return allowed_times;
	}


	var showDatePicker = function ($event, allowed_times, $changeDate, disabledDates = false) {
		
		var fmt = new DateFormatter(),
		cardate = new Date();
		console.log(cardate);
		$($event.target).datetimepicker({
			format: 'd/m/Y H:i',
			// formatTime: 'h:i a',
			minDate: 0,
			step: 15,
			startDate: new Date(),
			closeOnDateSelect: false,
			validateOnBlur: false,
			yearStart: cardate.getFullYear(),
			yearEnd: cardate.getFullYear() + 1,
			onShow: function (ct) {
				var that = this;

				that.setOptions({
					allowTimes: allowed_times,
					disabledDates: [disabledDates]
				});

				$('.xdsoft_time_variant .xdsoft_time').each(function(index){
					// console.log('test omar');
				});

			},
			onGenerate:function(ct,$i){
				console.log(disabledDates);
				$('.xdsoft_time_variant .xdsoft_time').each(function(index){
					var thistime = $(this).text();
					if(disabledDates.indexOf(thistime) !== -1){
						$(this).addClass('xdsoft_disabled');
					}
				});
			}, 
			onChangeDateTime: function (dp, $input) {
				console.log('onchagne omar');
				$('.xdsoft_time_variant .xdsoft_time').removeClass('xdsoft_disabled');
				var selectedDate = new Date($input.val()),
				errors = '',
				time = $input.val().split(' ');
				time = time[1];
				var desabletime = '';
				
				$.ajax({
					method: 'POST',
					url: ajaxurl,
					data: {
						action: 'check_webinar_existing_date_for_zoom', 
						selected: $input.val()
					},
					success: function (result) {
						desabletime = result.hide_time;
						$('.xdsoft_time_variant .xdsoft_time').removeClass('xdsoft_disabled');
						$('.xdsoft_time_variant .xdsoft_time').each(function(index){
							var thistime = $(this).text();
							if(desabletime.indexOf(thistime) !== -1){
								$(this).addClass('xdsoft_disabled');
							}
						});

						/*
						* Show error msg
						*/
						if(desabletime.indexOf(time) !== -1){
							errors += '<li><strong>'+result.lessons[0].post_title+': </strong> The Webinar time is already occupied by the previous lesson, the webinar should start 15 mminutes after the end of the previous - correct it to submit your webinar.</li>';
							errors += '<li><strong>'+result.lessons[0].post_title+': </strong> It can\'t be earlier than the previous lession - correct it to submit your webinar. </li>';
							jQuery('<div class="zoom-error"><ul>'+errors+'</ul></div>').insertBefore(jQuery('div#zoom_section_wrap'));	
							// console.log('show error omar');
							jQuery('div#publishing-action').find('input[type="submit"]').prop('disabled', true);
						}else{
							jQuery(document).find('div.zoom-error').remove();
							jQuery('div#publishing-action').find('input[type="submit"]').prop('disabled', false);
						}


					}
				});



				// console.log(selectedDate);
				if (selectedDate !== "Invalid Date") {
					var roundMinute = (Math.ceil(selectedDate.getMinutes() / 15) * 15) % 60;
					selectedDate.setMinutes(roundMinute);

					var finalDate = fmt.formatDate(selectedDate, 'd/m/Y H:i');
					// console.log(finalDate);
					if ($changeDate) {
						// $vm.changeDate(finalDate);
					}
				}
			}
		});
		$($event.target).datetimepicker('show');
	}


	jQuery(document).on('click', '.xdsoft_datepicker', function(e){
		var lession_id = jQuery('input[name="post_ID"]').val();
		$.ajax({
			method: 'POST',
			url: ajaxurl,
			data: {
				action: 'check_webinar_existing_date_for_zoom', 
				selected: e.target.value,
				lession_id: lession_id
			},
			success: function (result) {
				var allowed_times = getTimesbyDate(result, e.target.value);
				console.log(result);
					if (result.disabled_date) {
						showDatePicker(e, allowed_times, true, result.disabled_date, );
					} else {
						showDatePicker(e, allowed_times, true, result.hide_time);
					}
			}
		});
	});




	// Data Table 
	if ( jQuery('#zvc_meetings_list_table_customOrder').length > 0) {
		
		var table = jQuery('#zvc_meetings_list_table_customOrder').dataTable({
			"pageLength": 25,
			"searchPanes": [{
				"viewTotal": true,
				"columns": [4, 5],
			}],
			"order": [[ 1, "asc" ]],
			"columnDefs": [{
				"targets": 0,
				"orderable": false
			}]
		});

		
	}


})( jQuery );
