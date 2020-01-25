jQuery(function ($) {

    var digitalcustdevPlugin = {

        init: function () {
            this.cacheDOM();
            this._loadPlugDependencies();
            this.addEventListeners();
        },

        cacheDOM: function () {
            this.body = $('body');
            this.loader = $('.dcd-loader-wrapper');
            this.webinar_frontend = $('.render-webinars-frontend');
            this.create_webinar = $('.create-webinar');
        },

        _loadPlugDependencies: function () {
            if ($(this.webinar_frontend).length > 0) {
                $(this.webinar_frontend).dataTable();
            }
        },

        addEventListeners: function () {
            this.create_webinar.on('click', this.createWebinar.bind(this));
        },

        initLoader: function () {
            if (this.loader.length === 0) {
                this.body.prepend('<div class="dcd-loader-wrapper"><div class="dcd-loader"><div></div><div></div><div></div><div></div></div>');
            }
        },

        createWebinar: function (e) {
            e.preventDefault();
            $(e.target).closest('a').addClass('disabled');
            var nonce = $(e.currentTarget).data('nonce');
            var type = $(e.currentTarget).data('type');
            var action = 'create_new_webinar';
            $.ajax({
                method: 'POST',
                url: dcd.ajaxurl,
                data: {nonce: nonce, type: type, action: action, webinar: true},
                success: function (r) {
                    if (r.redirect) {
                        window.open(r.redirect);
                    }
                }
            })
        },
    };

    $(function () {
        digitalcustdevPlugin.init();
    });
});