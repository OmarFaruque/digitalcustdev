jQuery(function ($) {

    var digitalCustDevDom = {};
    var digitalCustTypingTimeout;

    var digitalCustDev = {
        init: function () {
            this.cacheDOM();
            this.loadDependencies();
            this.eventListeners();
        },

        cacheDOM: function () {
            this.body = $('body');
            this.loader = $('.dcd-loader-wrapper');

            digitalCustDevDom.courseSearch = $('.courses-search');
            digitalCustDevDom.role = $('.profile-switch-roles');
            digitalCustDevDom.profileCourseListDiv = $('.profile-courses-list');
            digitalCustDevDom.learnpressTags = $('.learnpress-tags');
            digitalCustDevDom.learnpressLang = $('.select2-select');
            digitalCustDevDom.loginLink = $('.thim-link-login').find('.profile');
        },

        loadDependencies: function () {
            if ($(digitalCustDevDom.learnpressTags).length > 0) {
                $(digitalCustDevDom.learnpressTags).select2({
                    width: '50%'
                });
            }

            if ($(digitalCustDevDom.learnpressLang).length > 0) {
                $(digitalCustDevDom.learnpressLang).select2({
                    width: '100%'
                });
            }
        },

        initLoader: function () {
            if (this.loader.length === 0) {
                this.body.prepend('<div class="dcd-loader-wrapper"><div class="dcd-loader"><div></div><div></div><div></div><div></div></div>');
            }
        },

        eventListeners: function () {
            $(document).on('click', '.digitalcustDev-create-post', this.createNewPost);
            digitalCustDevDom.role.on('click', this.switchRoles);
            digitalCustDevDom.courseSearch.on('keyup', this.implementCourseSearch.bind(this));
            $(document).on('click', '.delete-post-course-frontend', this.deleteCourse);

            $('#frontend-course-editor').find('.e-select-items').on('click', this.hideHeader.bind(this));
        },

        hideHeader: function (e) {
            $('.digitalcustdev-profile-page #masthead').css('z-index', 1);
            $('.e-modal-window .close').on('click', this.showHeader.bind(this));
        },

        showHeader: function (e) {
            $('.digitalcustdev-profile-page #masthead').css('z-index', 2);
        },

        createNewPost: function (e) {
            e.preventDefault();
            $(e.target).closest('a').addClass('disabled');
            var nonce = $(this).data('nonce');
            var type = $(this).data('type');
            var action = 'create_new_course';
            $.ajax({
                method: 'POST',
                url: digitalcustdev.ajaxurl,
                data: {nonce: nonce, type: type, action: action},
                success: function (r) {
                    if (r.redirect) {
                        window.open(r.redirect);
                    }
                }
            })
        },

        switchRoles: function (e) {
            e.preventDefault();
            $(this).text('Please Wait..');
            console.log(digitalcustdev.ajaxurl);
            $.post(digitalcustdev.ajaxurl, {action: 'switch_role', security: $(this).data('security')}).done(function (response) {
                console.log(response.redirect);
                window.location.reload();
            });

        },

        implementCourseSearch: function (event) {
            clearTimeout(digitalCustTypingTimeout);
            if ((event.which && event.which == 39) || (event.keyCode && event.keyCode == 39) || (event.which && event.which == 37) || (event.keyCode && event.keyCode == 37)) {
                //do nothing on left and right arrows
                return;
            }

            var ajaxsearchitem = $(event.target).val();
            var security = $(event.target).data('security');
            var type = $(event.target).data('searchtype');
            digitalCustTypingTimeout = setTimeout(function () {
                digitalCustDev.implementAjaxSearch(ajaxsearchitem, security, type);
            }, 500);
        },

        implementAjaxSearch: function (searchItem, security, type) {
            digitalCustDevDom.profileCourseListDiv.html('<p>Loading...</p>');
            var postData = {
                action: 'search_courses',
                s: searchItem,
                security: security,
                type: type
            };

            $.post(digitalcustdev.ajaxurl, postData).done(function (response) {
                digitalCustDevDom.profileCourseListDiv.html(response);
            });
        },

        deleteCourse: function (e) {
            e.preventDefault();
            var deleted = confirm("This will delete this course permanently. Are you sure ?");
            if (deleted) {
                //Dele the course
                var postData = {
                    action: 'delete_course_frontend',
                    post_id: $(this).data('postid'),
                    security: $(this).data('security')
                };

                digitalCustDev.initLoader();
                $.post(digitalcustdev.ajaxurl, postData).done(function (response) {
                    location.reload();
                });
            } else {
                return false;
            }
        }
    };

    $(function () {
        digitalCustDev.init();
    });
});