(function ($) {
    $(document).ready(function () {
        var date_time = new Date($('.countdown').attr('data-meeting-time'));
        if (date_time && date_time != "Invalid Date") {
            $('.countdown').downCount({
                date: date_time,
                offset: (new Date()).getTimezoneOffset() / -60
            }, function () {
                setTimeout(function () {
                    window.location.reload();
                });
            });
        }
    });
})(jQuery);
