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
	$('[data-toggle="tooltip"]').tooltip();

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
		console.log('created click inside public js');
	});


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
});