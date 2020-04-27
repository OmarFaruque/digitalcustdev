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

	jQuery('.xdsoft_datepicker').datetimepicker();


})( jQuery );
