(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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
	 * practising this, we should strive to set a better example in our own work.
	 */
	
	/*
	* Webinar Form submit while select order
	*/
	// jQuery("input[value=evaluate_final_quiz]").closest('div').hide();

	/*
	* Store browser date to cookies
	*/
	

	jQuery(document).on('change', 'form#webnartopform select[name="orderby"]', function(e){
		e.preventDefault();
		jQuery(this).closest('form').submit();
	});

	/*
	* Course Search Form 
	*/
	$(document).on('keydown', 'body.course-filter-active .course-search-filter',
	function(event) {
		jQuery(this).closest('form').attr('method', 'post');
	});



	function getParameterByName(name, url) {		
		name = name.replace(/[\[\]]/g, '\\$&');
		var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
			results = regex.exec(url);
			console.log(results);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, ' '));
	}

	/*
	* Duplicate pOsts
	*/
	var duplicatePost = function duplicatePost(e) {
		e.preventDefault();
	  
		var _self = $(this),
			_id = _self.data('post-id');
	  
		$.ajax({
		  url: '',
		  data: {
			'lp-ajax': 'duplicator',
			id: _id
		  },
		  success: function success(response) {
			response = LP.parseJSON(response);
			
			const post = getParameterByName('post', response.data);
			// console.log(lp_webinars.instructor_url);
			const post_url = lp_webinars.instructor_url + '/edit-post/' + post;
			// console.log(urlParams.get('post'));
			if (response.success) {
			//   window.location.href = post_url;
				location.reload();
			} else {
			  alert(response.data);
			}
		  }
		});
	  };
	  
	  jQuery(document).on('click', 'a.diuplicate_frontend', duplicatePost);



})( jQuery );


jQuery(document).ready(function($){
	'use strict';
	/*
	  * Course profile conent hiehgt
	  */
	if(jQuery('div.thim-course-content').length){
		jQuery('.thim-course-content.position-relative').height(jQuery('.thim-course-list .course-item .course-thumbnail').height());
	}


	/*
	* Bootstrap tooltip
	*/
	if(typeof tooltip == 'function'){
		$('[data-toggle="tooltip"]').tooltip();
	}

	/*
	* Mobile menu initial set
	*/
	if(jQuery('.switch_menu').length){
		// console.log(jQuery('ul.mobile_menu .switch_menu a.active').get(0));
		mobile_switch(jQuery('ul.mobile_menu .switch_menu a.active').get(0));
	}else{
		jQuery('li.instructor').hide();
		jQuery('li.student_menu').show();
	}



	/*
	* if discount active
	*/
	jQuery(document).on('change', 'input[name="descount_access"]',
	function(event) {
		descountaccess(jQuery(this));
	});
	if(jQuery('input[name="descount_access"]').length){
		descountaccess(jQuery('input[name="descount_access"]'));
	}

	if(jQuery('input[name="_lp_sale_price"]').length){
		jQuery(document).on('change', 'input[name="_lp_sale_price"]', function(){
			makebuttonnonclickable();
		});
	}

	if(jQuery('input#terms_n_condition').length){
		jQuery(document).on('change', 'input#terms_n_condition', function(){
			makebuttonnonclickable();
		});
	}
	

	function descountaccess(d){
		if(d.is(':checked')){
			d.closest('label').next('input').show();
			makebuttonnonclickable();
		}else{
			d.closest('label').next('input').hide();
			makebuttonnonclickable();
		}
	}

	/*
	* Make button non-clicable 
	*/
	function makebuttonnonclickable(){
		if(jQuery('input[name="_lp_sale_price"]').val() == ''){
			jQuery('input[name="_lp_sale_price"]').val(1000);
		}
		var price = jQuery('input[name="_lp_price"]').val(),
		sales_price = jQuery('input[name="_lp_sale_price"]').val(),
		desable = false;

	
		if(jQuery('input[name="descount_access"]').length){
			if(jQuery('input[name="descount_access"]').is(':checked')){
				if(sales_price < 999) desable = true;
				if(sales_price >= price) desable = true;
			}
		}

		if(jQuery('input#terms_n_condition').length){
			if(jQuery('input#terms_n_condition').is(':checked')){
				desable = false;
			}else{
				desable = true;	
			}
		}


		if(desable){
			jQuery('a.dcd-course-next-save.submit-for-review').addClass('disabled');
			jQuery('input[name="_lp_sale_price"]').addClass('error');
		}else{
			jQuery('a.dcd-course-next-save.submit-for-review').removeClass('disabled');
			jQuery('input[name="_lp_sale_price"]').removeClass('error');
		}
	}


	/*
	* Remove TynimCE hidden
	*/
	if(jQuery('.mce-container.mce-toolbar.mce-stack-layout-item.mce-last').length){
		jQuery('.mce-container.mce-toolbar.mce-stack-layout-item.mce-last').show();
	}


	/*
	* Set tooltip for metabox
	*/
	if(jQuery('#learn-press-admin-editor-metabox-settings').length){
		jQuery('.rwmb-label').each(function( index ){
			var label = jQuery(this).find('label').attr('for');
			if(jQuery(this).text() != '') jQuery(this).append('<span class="tooltip">?<span class="tooltiptext">Tooltip text</span></span>');
		});
	}

	/*
	* Default time for webinar's
	*/
	jQuery(document).on('click', '.xdsoft_today_button', function(){
	// jQuery(".xdsoft_today_button").on("touchend mousedown.xdsoft", function() {
		console.log('test omar');
		// J.data("changed", !0), j.setCurrentTime(0, !0), J.trigger("afterOpen.xdsoft");
	});


	/*
	* Limit course / webinars name
	*/
	jQuery(document).on('keyup', 'h4.e-section-head input.section-title, ul.e-section-content input.item-title, input.question-loop-title, .e-form-field-input input[name="post_title"], div.e-item-heading-input input', function(){
		if(jQuery(this).val().length <= 3){
			if(jQuery(this).hasClass('section-title')){
				jQuery(this).closest('h4').addClass('error');
			}else if(jQuery(this).hasClass('item-title')){
				jQuery(this).closest('li.e-section-item').addClass('error');
			}else if(jQuery(this).hasClass('question-loop-title')){
				jQuery(this).closest('li.e-question-loop').addClass('error');
			}else{
				jQuery(this).addClass('error');
			}
		}else{
			jQuery(this).removeClass('error');
			jQuery(this).closest('h4').removeClass('error');
			jQuery(this).closest('li.e-section-item').removeClass('error');
			jQuery(this).closest('li.e-question-loop').removeClass('error');
		}	
	});

	/*
	* Toggle Comming Soon section
	*/
	
	jQuery(document).on('change', 'input[name="_lp_coming_soon"]', function(){
		commingsoonToggle();
	});
	var commingsoonToggle = function(){
		var newdate = new Date(),
		newdate = newdate.setMonth(newdate.getMonth() + 2),
		newdate = new Date(newdate),
		month = ("0" + (newdate.getMonth() + 1)).slice(-2),
		newgetdate = ("0" + newdate.getDate()).slice(-2),
		minit = newdate.getMinutes(),
		shouldadd = 5 - (minit % 5),
		newmint = (shouldadd < 5) ? minit + shouldadd : minit,
		newmint = ("0" + newmint).slice(-2),
		hours = ("0" + newdate.getHours()).slice(-2),
		date = newdate.getFullYear() + '-' + month + '-' + newgetdate + ' '+ hours + ':' + newmint;


		if(jQuery('input[name="_lp_coming_soon"]').is(':checked')){
			jQuery('li#meta-box-tab-course_coming_soon > div > div').hide();
			jQuery('li#meta-box-tab-course_coming_soon > div > div:nth-child(4)').show();

			/*
			* All default value for comming soon section 
			*/
			jQuery('input#_lp_coming_soon_end_time').val(date);
			jQuery("input#_lp_coming_soon_countdown, input#_lp_coming_soon_showtext, input#_lp_coming_soon_metadata, input#_lp_coming_soon_details").prop("checked", true);
		}else{
			jQuery('li#meta-box-tab-course_coming_soon > div > div').hide();
			jQuery('li#meta-box-tab-course_coming_soon > div > div:nth-child(4)').show();
		}
	}
	commingsoonToggle();



	
	/*
	* Mobile switcher for fron-editor
	*/
	jQuery(document).on('click', 'span.mobile_section_toggle', function(){
		var object_width = jQuery('#frontend-editor #e-tab-content-curriculum #e-course-curriculum').width();
		if(jQuery(this).hasClass('active')){
			jQuery(this).removeClass('active');
			jQuery(this).text('>');
			jQuery('#frontend-editor #e-tab-content-curriculum #e-course-curriculum').animate({
				left: '-100%'
			});
			jQuery(this).animate({
				right: '0px'
			});
			jQuery('#frontend-editor #e-tab-content-curriculum').removeClass('toggle-active');
		}else{
			jQuery(this).addClass('active');
			jQuery(this).text('<');
			jQuery('#frontend-editor #e-tab-content-curriculum #e-course-curriculum').animate({
				left: '0'
			});
			jQuery(this).animate({
				right: '0px'
			});
			jQuery('#frontend-editor #e-tab-content-curriculum').addClass('toggle-active');
		}
	});




	jQuery(document).on('click', 'a.remove_lesson_media_attachment', function(e){
		// console.log('this itemata id: ' + $this.itemData.id);
		
		e.preventDefault();
		var attachment_id = jQuery(this).data('id');
		var thisevent = jQuery(this);
		$.post(
			_wpUtilSettings.ajax,
			{
				action: 'delete_lession_attachment_video',
				dataType: 'json',
				lession_id: attachment_id,
			},
			function (data){
				// console.log(data);
				var outputhtml = '<div id="wp-content-media-buttons" class="wp-media-buttons">'
				+'<button type="button" id="insert-media-button" class="button e-button insert-media_cus add_media">'
					+'<span class="wp-media-buttons-icon"></span>Add Media</button>'
				+'</div>';
				if(data.msg == 'success'){
					jQuery('body').find('li#_lp_lesson_video_intro_internal').find('input').val('');
					jQuery('body').find('div#lession_Int_media').html(outputhtml);
					// jQuery('body').find('div#lession_Int_media').find('.acf-field').addClass('hidden');
					jQuery('body').find('.single_sub_section.add_video').next('.external_lession_media').removeClass('hidden'); 
					jQuery('body').find('.single_sub_section.add_video').addClass('etstingclass');
				}
			}
		);
	});


	/* Lession Media start */
jQuery(document).on('click', '.e-tab.active button#insert-media-button.insert-media_cus', function(e){
    // jQuery(this).unbind();
    var thisid = jQuery(this).data('id');
    
    e.preventDefault();
    $.post(
        _wpUtilSettings.ajax,
        {
            action: 'check_foldersize',
            dataType: 'json',
            post_id: $('input[name="post_ID"]').val(),
            user_id: userSettings.uid,
        },
        function (data) {
            // console.log('ajsx success');
            data = JSON.parse(data);
            // console.log(data);
            var $button = $(this);
            // Create the media frame.
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

              // When an image is selected in the media frame...
            file_frame.on('library:selection:add', function (e) {
                $.post(
                    _wpUtilSettings.ajax,
                    {
                        action: 'check_foldersize',
                        dataType: 'json',
                        post_id: $('input[name="post_ID"]').val(),
                        user_id: userSettings.uid,
                    },
                    function(response){
                        data = JSON.parse(response);
                        
                        var attachment1 = file_frame.state().get('selection').first().toJSON();
                        var totalsize_with_folder = data.titalsize + attachment1.size;
                        var mb = Math.round(totalsize_with_folder / 1048576);
                        if(mb >= 2000){
                            $( "div.media-frame-content" ).prepend('<div class="upload_limit_error"><h6><span class="crose_limit">'+data.display_msg+'</span></h6></div>');
                            $( 'div.media-router > button:first-child, .media-toolbar-primary.search-form button' ).hide();
                        }
                    }
				);
				


				/* Check file type while reset after upload */
				wp.Uploader.queue.on('reset', function(e) { 
					var attachment = file_frame.state().get('selection').first().toJSON();
					if(attachment.mime){
						var allowList = ['video/mp4'];
						if(jQuery.inArray(attachment.mime, allowList) === -1){
							var error = '<div class="acf-selection-error"><span class="selection-error-label">Restricted</span><span class="selection-error-filename">'+attachment.filename+'</span><span class="selection-error-message">File type must be video/mp4.</span></div>';
							jQuery('body').find('.media-frame-content, .media-frame-toolbar').find('.media-toolbar-primary.search-form > button.media-button-select').addClass('disabled');
							jQuery('body').find('.media-frame-content, .media-frame-toolbar').find('.media-toolbar-primary.search-form > button.media-button-select').prop('disabled', true);
							jQuery('body').find('.media-frame-content').find('.media-sidebar').html(error);

							$.post(
								_wpUtilSettings.ajax,
								{
									action: 'delete_recent_media_attachment',
									dataType: 'json',
									attachment_id: attachment.id
								},
								function(response){
									/* Nothing */ 
								}
							);

							// jQuery('body').find('.media-frame-content').find('button.button-link.delete-attachment').trigger('click');
						}
					}
				});

			});
			


			file_frame.on('selection:toggle', function (e) {

				// var attachmenttest = wp.media.featuredImage.frame().state().get('selection').first();
				// console.log(attachmenttest);
				var attachment = file_frame.state().get('selection').first().toJSON();
					if(attachment.mime){
						var allowList = ['video/mp4'];
						if(jQuery.inArray(attachment.mime, allowList) === -1){
							// console.log(attachment);      
							var error = '<div class="acf-selection-error"><span class="selection-error-label">Restricted</span><span class="selection-error-filename">'+attachment.filename+'</span><span class="selection-error-message">File type must be Video/mp4.</span></div>';
							jQuery('body').find('.media-frame-content, .media-frame-toolbar').find('.media-toolbar-primary.search-form').find('button.media-button-select').addClass('disabled');
							jQuery('body').find('.media-frame-content, .media-frame-toolbar').find('.media-toolbar-primary.search-form > button.media-button-select').prop('disabled', true);
							jQuery('body').find('.media-frame-content').find('.media-sidebar').html(error);
							// jQuery('body').find('.media-frame-content').find('button.button-link.delete-attachment').trigger('click');
							$.post(
								_wpUtilSettings.ajax,
								{
									action: 'delete_recent_media_attachment',
									dataType: 'json',
									attachment_id: attachment.id
								},
								function(response){
									/* Nothing */ 
								}
							);
						}else{
							jQuery('body').find('.media-frame-content').find('.media-sidebar').find('.acf-selection-error').remove();
						}
					}
			});

            // file_frame.close();
            file_frame.on('select', function () {
                // We set multiple to false so only get one image from the uploader
                var attachment = file_frame.state().get('selection').first().toJSON();
                /*
                * Save via auto-save
                */
               $.post(
                _wpUtilSettings.ajax,
                {
                    action: 'step_two_custom_autosave',
                    dataType: 'json',
                    post_id: $('input[name="post_ID"]').val(),
                    field_value: attachment,
					field_name: '_lp_lesson_video_intro_internal',
					// post_content: '[video width="1920" height="1080" mp4="'+attachment.url+'"][/video]' + tinymce.get('e-item-content').getContent(),
                    item_id:  $('ul.e-section-content > li.e-section-item.e-selected').data('id')
                },
                function(response){
                    /* Nothing */
                    var lession_id = jQuery('ul.e-course-sections ul.e-section-content li.e-selected').data('id');
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
                                    +'<a href="#" data-id="'+lession_id+'" title="Remove" class="acf-icon -cancel remove_lesson_media_attachment dark"></a>'
                                +'</div>'
                            +'</div>'
                            +'</div>'
                        +'</div>'
                    +'</div>';
                        jQuery('body').find('.single_sub_section.add_video').next('.external_lession_media').addClass('hidden');
                        jQuery('body').find('div[data-id="'+lession_id+'"]#lession_Int_media').append(outputhtml);
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').removeClass('hidden');
						jQuery('body').find('div[data-id="'+lession_id+'"]#lession_Int_media').find('.wp-media-buttons').addClass('hidden');
						jQuery('body').find('li#_lp_lesson_video_intro_internal').find('input').val(attachment);
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('img[data-name="icon"]').attr('src', attachment.icon);
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.file-info p:nth-child(1)').text(attachment.title);
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.file-info p:nth-child(2)').find('a').text(attachment.filename).attr('href', attachment.url);
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.file-info p:nth-child(3)').find('span').text(attachment.filesizeHumanReadable);
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.acf-actions').find('a').data('id', lession_id);
						// var editor = tinymce.get('e-item-content');
						// var editor = tinymce.get(thisid);
                        // editor.setContent('[video width="1920" height="1080" mp4="'+attachment.url+'"][/video]' + editor.getContent());
                });
             });
    
            // Finally, open the modal
            file_frame.open();
            var totalsize_with_folder = data.titalsize;
            var mb = Math.round(totalsize_with_folder / 1048576);
            if(mb >= 2000){
                    $( "div.media-frame-content" ).prepend('<div class="upload_limit_error"><h6><span class="crose_limit">'+data.display_msg+'</span></h6></div>');
                    $( 'div.media-router > button:first-child, .media-toolbar-primary.search-form button' ).hide();
            }
        }
     );
});
/* Lession Media End  */



function lesson_countdown(){
	var countertime = jQuery('#lesson_zoom_counter').data('time');
	var countDownDate = new Date(countertime);
	// console.log('terget time: ' + countDownDate);
	countDownDate = countDownDate.getTime();

	// Update the count down every 1 second
	var x = setInterval(function() {

	// Get today's date and time
	var now = new Date();
	now = new Date(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(), now.getUTCHours(), now.getUTCMinutes(), now.getUTCSeconds());
	
	now = now.getTime();
	

	// Find the distance between now and the count down date
	var distance = countDownDate - now;

	// Time calculations for days, hours, minutes and seconds
	var days = Math.floor(distance / (1000 * 60 * 60 * 24));
	var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	var seconds = Math.floor((distance % (1000 * 60)) / 1000);

	// Display the result in the element with id="demo"
	var display = '';
	if(days > 0) display += '<span class="countdown-section"><span class="countdown-amount">' + days + '</span><span class="countdown-period">Days</span></span>';
	if(hours > 0 || days > 0) display += '<span class="countdown-section"><span class="countdown-amount">' + hours + '</span><span class="countdown-period">Hours</span></span>';
	if(minutes > 0 || hours > 0) display += '<span class="countdown-section"><span class="countdown-amount">' + minutes + '</span><span class="countdown-period">Minutes</span></span>';
	if(seconds > 0 || minutes > 0) display += '<span class="countdown-section"><span class="countdown-amount">' + seconds + '</span><span class="countdown-period">Seconds</span></span>';
	document.getElementById("lesson_zoom_counter").innerHTML =  '<span class="countdown-row">' + display + '</span>';

	// If the count down is finished, write some text

	if (distance < 600000) {
		clearInterval(x);
		location.reload();
	}
	}, 1000);
}
if(jQuery('#lesson_zoom_counter').length){
	lesson_countdown();
}


});  // Document ready end

function mobile_switch(obj){
	if(jQuery( window ).width() <= 768){
		console.log(obj);
		jQuery.ajax({
			url: ajaxurl,
			dataType: 'json',
			data: {
				text: obj.innerHTML,
				action: 'mobileswitchcallback'
			},
			success: function success(data) {
			  if (data.meta == 'student') {
					jQuery('.switch_menu a').removeClass('active');
					jQuery('.switch_menu a:nth-child(2)').addClass('active');
					jQuery('li.instructor').hide();
					jQuery('li.student_menu').show();
			  }else{
					jQuery('.switch_menu a').removeClass('active');
					jQuery('.switch_menu a:first-child').addClass('active');  
					jQuery('li.student_menu').hide();
					jQuery('li.instructor').show();
			  } 
			}
		  });
		return false ;
	}else{
		return true;
	}
}




/*
* Set cookie current time
*/
jQuery(window).load(function(){
	var newdate = new Date(),
	month = ("0" + (newdate.getMonth() + 1)).slice(-2),
	newgetdate = ("0" + newdate.getDate()).slice(-2),
	minit = newdate.getMinutes(),
    shouldadd = 5 - (minit % 5),
    newmint = (shouldadd < 5) ? minit + shouldadd : minit,
    newmint = ("0" + newmint).slice(-2)
	cookiedate = newdate.getFullYear() + '-' + month + '-' + newgetdate + ' '+ newdate.getHours() + ':' + newmint
	document.cookie = "nowdate=" + cookiedate;

	// Store timezone
	document.cookie = "wb_timezone=" + Intl.DateTimeFormat().resolvedOptions().timeZone + ';'+ "expires="+ new Date(new Date().getTime()+60*60*1000*24).toGMTString()+";path=/";

});