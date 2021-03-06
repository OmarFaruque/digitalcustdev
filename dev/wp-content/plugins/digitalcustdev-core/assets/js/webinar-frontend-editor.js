;jQuery(function ($) {
    "use strict";
    var __X = $.extend({}, FE_Base.Store_Methods, {
        loadSettings: function (callback) {
            var that = this;
            //if ($.isEmptyObject(this.item.item.settings)) {
            // this.request('', 'load-item-settings', {
            //     item_ID: this.itemData.id,
            //     item_type: this.itemData.type
            // }).then(function (response) {
            //     $.isFunction(callback) && callback.apply(that, [response])
            // });
            // }
        },
        includeFormField: function (field) {
            var maps = {
                number: 'text'
            }, slug = field.type.replace(/_/, '-');

            if (maps[slug]) {
                field.xType = slug;
                field.type = maps[slug];
            } else {
                field.type = slug;
            }
            return 'e-form-field-' + field.type
        },
        redraw: function () {
            var vm = this;
            vm.drawComponent = false;
            Vue.nextTick(function () {
                vm.drawComponent = true;
            });
        },

        vueId: function () {
            return this._uid
        },

        getSettings: function (key) {
            return key && this.itemData.settings ? this.itemData.settings[key] : this.itemData.settings;
        },

        getFields: function (type) {
            var $postTypeFields = this.$dataStore().post_type_fields;
            return $postTypeFields[type];
        },

        loadSettingsCallback: function (response) {
            var content = response.__CONTENT__;

            delete response['__CONTENT__'];

            this.itemData.settings = response;
            this.itemData.content = content;

        },

        isEmptySettings: function () {
            var s = this.itemData.settings;
            return !s || ($.isPlainObject(s) && $.isEmptyObject(s)) || ($.isArray(s) && s.length === 0);
        }
    });
    var __A = {
        template: '#tmpl-e-course-item-settings-lp_lesson',
        props: ['item', 'itemData', 'request'],
        data: function () {
            return {
                drawComponent: true,
                settings: this.itemData.settings || {}
            }
        },
        computed: {
            settings: function () {
                return this.itemData.settings || {};
            }
        },
        watch: {
            // itemData: {
            //     handler: function (val) {
            //         console.log('Load Settings');
            //
            //         if (this.isEmptySettings()) {
            //             setTimeout(function ($i) {
            //                 $i.loadSettings($i.loadSettingsCallback);
            //                 $i.redraw();
            //             }, 70, this);
            //         } else {
            //             this.redraw();
            //         }
            //         return val;
            //     },
            //     deep: true
            // },
            'itemData.id': function () {
                this.redraw();
            }
        },

        created: function () {
            this.loadSettings(this.loadSettingsCallback);
        },
        methods: $.extend({}, __X, {})
    };

    Vue.component('e-item-settings-lp_lesson', __A);

    Vue.component('e-form-field-timezone', {
        template: '#tmpl-e-form-field-timezone',
        props: ['item', 'itemData', 'request', 'field', 'settings'],
        data: function () {
            return {
                drawComponent: true,
                settingValue: this.get()
            }
        },
        watch: {
            settingValue: function (value) {
                this.itemData.settings[this.field.id] = value;
                return value;
            }
        },
        created: function () {
        },
        mounted: function () {
        },
        methods: {
            redraw: function () {
                var vm = this;
                vm.drawComponent = false;
                Vue.nextTick(function () {
                    vm.drawComponent = true;
                });
            },
            get: function () {
                var settings = this.itemData.settings || {},
                    v = settings[this.field.id];
                return v && !Number.isInteger(v) ? v : 'Europe/Moscow';
            }
        }
    });

    Vue.component('e-form-field-when-webinar', {
        template: '#tmpl-e-form-field-when-webinar',
        props: ['item', 'itemData', 'request', 'field', 'settings'],
        data: function () {
            return {
                drawComponent: true
            }
        },
        created: function () {

        },
        mounted: function () {
            /*var $vm = this;
            $('.webinar_start_time').datetimepicker({
                dateFormat: "Y-m-d",
                defaultTime: '10:00',
                minDate: 0,
                step: 15,
                onChangeDateTime: function (dp, $input) {
                    var $picker = this;
                    $.ajax({
                        method: 'POST',
                        url: dcd_fe_object.ajax_url,
                        data: {action: 'check_webinar_existing_date', selected: $input.val(), display: 'inline'},
                        beforeSend: function () {
                            $('#frontend-editor').append('<div id="e-update-activity" class="updating"><span class="e-update-activity__icon"></span></div>');
                        },
                        success: function (result) {
                            $('#e-update-activity').remove();
                            if (result.success === false) {
                                alert(result.msg);

                                $picker.setOptions({
                                    value: $input[0]._value,
                                });
                            } else {
                                $vm.changeDate($input.val());
                            }
                        }
                    });
                }
            });*/
        },
        methods: {
            changeDate: function (selectedDate) {
                Vue.set(this.itemData.settings, this.field.id, selectedDate);
            },

            _changeDatePicker: function (e) {
                var $vm = this;
                $('#frontend-editor').append('<div id="e-update-activity" class="updating"><span class="e-update-activity__icon"></span></div>');
                $.ajax({
                    method: 'POST',
                    url: dcd_fe_object.ajax_url,
                    data: {action: 'check_webinar_existing_date', selected: e.target.value},
                    success: function (result) {
                        var allowed_times = $vm.getTimesbyDate(result, e.target.value);
                        if (result.disabled_date) {
                            $vm.showDatePicker($vm, e, allowed_times, true, result.disabled_date);
                        } else {
                            $vm.showDatePicker($vm, e, allowed_times, true);
                        }

                        $('#e-update-activity').remove();
                    }
                });
            },

            showDatePicker: function ($vm, $event, allowed_times, $changeDate, disabledDates = false) {
                var fmt = new DateFormatter();
                $($event.target).datetimepicker({
                    format: 'd/M/Y H:i',
                    // formatTime: 'h:i a',
                    minDate: 0,
                    step: 15,
                    closeOnDateSelect: true,
                    validateOnBlur: false,
                    yearStart: 2019,
                    onShow: function (ct) {
                        var that = this;

                        // var currentTime = fmt.formatDate(ct, 'Y-m-d');

                        /*if ($.inArray(currentTime, disabledDates) !== -1) {
                            disabledDates.splice($.inArray(currentTime, disabledDates), 1);
                        }*/

                        that.setOptions({
                            allowTimes: allowed_times,
                            disabledDates: [disabledDates]
                        });
                    },
                    onChangeDateTime: function (dp, $input) {
                        var selectedDate = new Date($input.val());
                        console.log(selectedDate);
                        if (selectedDate !== "Invalid Date") {
                            var roundMinute = (Math.ceil(selectedDate.getMinutes() / 15) * 15) % 60;
                            selectedDate.setMinutes(roundMinute);

                            var finalDate = fmt.formatDate(selectedDate, 'd/m/Y H:i');
                            console.log(finalDate);
                            if ($changeDate) {
                                $vm.changeDate(finalDate);
                            }
                        }
                    }
                });

                $($event.target).datetimepicker('show');
            },

            getTimesbyDate: function (result, value) {
                var allowed_times = [];
                if (result.date === value) {
                    $.each(result.allowed_times, function (c, t) {
                        allowed_times.push(t);
                    });
                }

                return allowed_times;
            },

            redraw: function () {
                vm.drawComponent = false;
                Vue.nextTick(function () {
                    vm.drawComponent = true;
                });
            }
        }
    });

    function validateURL(textval) {
        var urlregex = new RegExp("^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$");
        return urlregex.test(textval);
    }

    $(document).on('ready', function () {
        $(".thim_course_media_intro").on('keyup', function (e) {
            if (this.value.length > 3) {
                var url = $(this).val();
                if (!validateURL(url)) {
                    $(this).addClass('error');
                    $('.course_youtube_link_incorrect').html('<div class="message message-error mt-1 mb-1" role="alert">Please enter a valid youtube URL.</div>');
                    $('.dcd-course-next-save').addClass('disabled');
                } else {
                    $(this).removeClass('error');
                    $('.course_youtube_link_incorrect').html('');
                    $('.dcd-course-next-save').removeClass('disabled');
                }
            } else {
                $(this).removeClass('error');
                $('.course_youtube_link_incorrect').html('');
                $('.dcd-course-next-save').removeClass('disabled');
            }
        });

        setTimeout(function () {
            var step = localStorage.getItem("digitalcustdev_step");
            if (step === "2") {
                $('.e-course-tabs').find('.e-tab').each(function (i, r) {
                    if ($(r).data('name') === "curriculum") {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });

                localStorage.removeItem("digitalcustdev_step");
            }

            if ($('.acf-file-uploader').hasClass('has-value')) {
                $('.hide-youtube-link-field').hide();
            }

            if ($('.frontend-post-title').val().length > 0) {
                if ($('.frontend-post-title').val().length > 3) {
                    $('.dcd-course-next-btn').show();
                } else {
                    $('.dcd-course-next-btn').hide();
                }
            }
        }, 500);

        //Change Price Validations
        $('input[name="_lp_price"]').on('keyup', function (e) {
            if ($(this).val() >= 1000) {
                $(this).removeClass('error');
                $('.dcd-course-next-save').show();
            } else {
                $(this).addClass('error');
                $('.dcd-course-next-save').hide();
            }
        });

        //Change post title navigation btns
        $('.frontend-post-title').on('keyup', function (e) {
            if ($(this).val().length > 3) {
                $('.dcd-course-next-btn').show();
            } else {
                $('.dcd-course-next-btn').hide();
            }
        });

        //Change Youtube Field
        $('.acf-fields').on('change', function () {
            if ($('.acf-file-uploader').hasClass('has-value')) {
                $('.hide-youtube-link-field').show();
            } else {
                $('.hide-youtube-link-field').hide();
            }
        });

        $('.dcd-course-next-btn').click(function () {
            var currentTab = $(this).data('id');
            var currentCourseTab = $('.e-course-tabs').find('.e-tab');
            $(currentCourseTab).each(function (i, r) {
                if (currentTab === "general" && $(r).hasClass('active')) {
                    if ($(this).next().data('name') === "curriculum") {
                        $(this).next().addClass('active');
                        $(this).removeClass('active');

                        var $sections_exists = false;
                        if (FE_Course_Editor.sections.length > 0) {
                            $(FE_Course_Editor.sections).each(function (i, r) {
                                if (r.items.length > 0) {
                                    $(r.items).each(function (j, k) {
                                        if (k.type === "lp_lesson") {
                                            $sections_exists = true;
                                        }
                                    });
                                }
                            });
                        }

                        if ($sections_exists) {
                            $('.dcd-course-next-curriculum').show();
                        } else {
                            $('.dcd-course-next-curriculum').hide();
                        }
                    }
                }

                $('.submit-for-review').click(function () {
                    var redirect_url = $(this).data('redirect');
                    $(this).append('<span class="hidden-review-publish"><input type="hidden" name="post_on_review" value="' + redirect_url + '"></span>');
                });

                if (currentTab === "curriculum" && $(r).hasClass('active')) {
                    if ($(this).next().data('name') === "settings") {
                        $(this).next().addClass('active');
                        $(this).removeClass('active');

                        //Check Price length value
                        if ($('input[name="_lp_price"]').val() >= 1000) {
                            $('input[name="_lp_price"]').removeClass('error');
                            $('.dcd-course-next-save').show();
                        } else {
                            $('input[name="_lp_price"]').addClass('error');
                            $('.dcd-course-next-save').hide();
                        }
                    }
                }
            });
        });

        $('.dcd-course-prev-btn').click(function () {
            $('.hidden-review-publish').remove();
            var currentCourseTab = $('.e-course-tabs').find('.e-tab');
            $(currentCourseTab).each(function (i, r) {
                if ($(r).hasClass('active')) {
                    if ($(this).prev().data('name') === "curriculum") {
                        $(this).prev().addClass('active');
                        $(this).removeClass('active');
                    }

                    if ($(this).prev().data('name') === "general") {
                        $(this).prev().addClass('active');
                        $(this).removeClass('active');
                    }
                }
            });
        });
    });
});