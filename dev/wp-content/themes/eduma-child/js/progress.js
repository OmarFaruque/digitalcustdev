jQuery(document).ready(function($) {
	var progress =  $('span.result-percent').text();
	var array = progress.split("%");
	array = array.slice(0, -1);
	for (var i=0; i<array.length; i++){
		array[i] = parseInt(array[i], 10);
	}
	$('.column-status span.result-percent').each(function(index) {
//		console.log(this);	  
		$(this).css('width', array[index]+'%');
    }); 
		$('.column-status span.result-percent').each(function(index) {
		$(this).css('background-color', bgcolor(array[index]));
//		console.log('index: ' + array[index].toString());
//		console.log('return bgcolor: ' + bgcolor(array[index]).toString());

    });
});

var bgcolor = function (arr) {
	var bgcolors = '';
		if (arr < 10) {
			bgcolors = '#ff8c66';
		}		
		else if (arr >= 10 && arr < 20) {
			bgcolors = '#ffb366';
		} else if (arr >= 20 && arr < 30) {
			bgcolors = '#ffcc66';
		} else if (arr >= 30 && arr < 40) {
			bgcolors = '#ffd966';
		} else if (arr >= 40 && arr < 50) {
			bgcolors = '#ffff66';
		} else if (arr >= 50 && arr < 60) {
			bgcolors = '#ecff66';
		} else if (arr >= 60 && arr < 70) {
			bgcolors = '#f2ffcc';
		} else if (arr >= 70 && arr < 80) {
			bgcolors = '#e6ffcc';
		} else if (arr >= 80 && arr < 90) {
			bgcolors = '#b3ff66';
		} else if (arr >= 90 && arr < 100) {
			bgcolors = '#8cff66';		
		} 
		else {
			bgcolors =  '#00ff00';
		}
	
	return bgcolors;
};
