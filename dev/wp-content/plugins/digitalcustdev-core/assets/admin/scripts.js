jQuery(function ($) {

    var AdmindigitalcustdevPlugin = {

        init: function () {
            this.cacheDOM();
            this._loadPlugDependencies();
            this._addEventListeners();
        },

        cacheDOM: function () {
            this.webinarTbl = $('.admin-render-webinars-table');
            this.webinarDelete = $('.admin-delete-webinar');
        },

        _loadPlugDependencies: function () {
            if ($(this.webinarTbl).length > 0) {
                $(this.webinarTbl).dataTable();
            }
        },

        _addEventListeners: function () {
            this.webinarDelete.on('click', this._deleteWebinar.bind(this));
        },

        _deleteWebinar: function (e) {
            var result = confirm("Are you sure you want to delete this webinar ?");
            if (result) {
                var postData = $(e.currentTarget).data();
                postData.action = "delete_webinar";
                $.post(ajaxurl, postData).done(function (response) {
                    console.log(response);
                });
            } else {
                return false;
            }
        }
    };

    $(function () {
        AdmindigitalcustdevPlugin.init();
    });
});