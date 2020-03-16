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
		var price = jQuery('input[name="_lp_price"]').val(),
		sales_price = jQuery('input[name="_lp_sale_price"]').val(),
		desable = false;

	
		if(jQuery('input[name="descount_access"]').length){
			if(jQuery('input[name="descount_access"]').is(':checked')){
				if(sales_price < 999) desable = true;
				if(sales_price >= price) desable = true;
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
			jQuery(this).append('<span class="tooltip">?<span class="tooltiptext">Tooltip text</span></span>');
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

});

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