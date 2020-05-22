(function($){
    'use strict';



    // Data Table 
	if ( jQuery('#zvc_meetings_list_table_customOrder').length > 0) {
		console.log('its found');
		jQuery('#zvc_meetings_list_table_customOrder').dataTable({
			"pageLength": 25,
			"columnDefs": [{
				"targets": 0,
				"orderable": false
			}]
		});
    }
    
})(jQuery);