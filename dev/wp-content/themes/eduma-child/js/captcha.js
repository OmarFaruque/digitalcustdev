jQuery(document).ready(function($) {
    $(window).resize(function() {
    var recaptcha = $(".g-recaptcha");
        if(recaptcha.css('margin') == '1px') {
            var newScaleFactor = recaptcha.parent().innerWidth() / 304;
            recaptcha.css('transform', 'scale(' + newScaleFactor + ')');
            recaptcha.css('transform-origin', '0 0');
        		console.log('newScaleFactor: ' + newScaleFactor.toString());

        }
        else {
            recaptcha.css('transform', 'scale(1)');
            recaptcha.css('transform-origin', '0 0');
        }
    });
});
