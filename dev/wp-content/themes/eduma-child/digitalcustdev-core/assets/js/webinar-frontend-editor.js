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
            // console.log('return: ' + $postTypeFields[type]);
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
                console.log('watch');
                this.redraw();
            }
        },
        mounted: function () {
                    // var thisvalue = $(this.$el).find('input').val(),
                    // thisvalue = thisvalue.split(':'),
                    // thisvalue = thisvalue[1] % 5,
                    // thisdate = new Date(),
                    // month = ("0" + (thisdate.getMonth() + 1)).slice(-2),
                    // minit = thisdate.getMinutes(),
                    // shouldadd = 5 - (minit % 5);
                    
                    // if(thisvalue > 0){
                    //     var newmint = (shouldadd < 5) ? minit + shouldadd : minit;
                    //     var hours   = (minit > 55 ) ? thisdate.getHours() + 1 : thisdate.getHours();
                    //     var newmint = (minit > 55 ) ? '00' : newmint;
                    //     var newmint = ("0" + newmint).slice(-2),
                    //     newgetdate  = ("0" + thisdate.getDate()).slice(-2),
                    //     newdate = newgetdate + '/' + month + '/' + thisdate.getFullYear() + ' ' + hours + ':' + newmint;
                    //     $(this.$el).find('input').val(newdate);
                    // }
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
            $(this.$el).find('select').select2({
                width: '100%'
            });
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

            // var thisvalue = $(this.$el).find('input').val(),
            // thisvalue = thisvalue.split(':'),
            // thisvalue = thisvalue[1] % 5,
            // thisdate = new Date(),
            // month = ("0" + (thisdate.getMonth() + 1)).slice(-2),
            // minit = thisdate.getMinutes(),
            // shouldadd = 5 - (minit % 5);
            
            // if(thisvalue > 0){
            //     var newmint = (shouldadd < 5) ? minit + shouldadd : minit;
            //     var hours   = (minit > 55 ) ? thisdate.getHours() + 1 : thisdate.getHours();
            //     var newmint = (minit > 55 ) ? '00' : newmint;
            //     var newmint = ("0" + newmint).slice(-2),
            //     newgetdate  = ("0" + thisdate.getDate()).slice(-2),
            //     newdate = newgetdate + '/' + month + '/' + thisdate.getFullYear() + ' ' + hours + ':' + newmint;
            //     $(this.$el).find('input').val(newdate);
            // }
            // console.log('mounted omar');
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
                console.log('test update 5');
                var $vm = this,
                lession_id = $('ul.e-course-sections ul.e-section-content li.e-selected').data('id');
                
                $('#frontend-editor').append('<div id="e-update-activity" class="updating"><span class="e-update-activity__icon"></span></div>');
                $.ajax({
                    method: 'POST',
                    url: dcd_fe_object.ajax_url,
                    data: {
                        action: 'check_webinar_existing_date', 
                        selected: e.target.value,
                        lession_id: lession_id
                    },
                    success: function (result) {
                        var allowed_times = $vm.getTimesbyDate(result, e.target.value);
                            if (result.disabled_date) {
                                $vm.showDatePicker($vm, e, allowed_times, true, result.disabled_date, );
                            } else {
                                $vm.showDatePicker($vm, e, allowed_times, true, result.hide_time);
                            }
                        $('#e-update-activity, .updating').remove();
                    }
                });
            },

            showDatePicker: function ($vm, $event, allowed_times, $changeDate, disabledDates = false) {
                var fmt = new DateFormatter(),
                cardate = new Date();

                $($event.target).datetimepicker({
                    format: 'd/M/Y H:i',
                    // formatTime: 'h:i a',
                    minDate: 0,
                    step: 15,
                    closeOnDateSelect: false,
                    validateOnBlur: false,
                    yearStart: cardate.getFullYear(),
                    yearEnd: cardate.getFullYear() + 1,
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
                        // console.log(ct);
                        // console.log(that);
                        // jQuery('.xdsoft_time').addClass('omar');
                        $('.xdsoft_time_variant .xdsoft_time').each(function(index){
                            // console.log('test omar');
                        });

                    },

                    onClose: function(time, input){
                        $('div#frontend-course-editor').find('ul#error_msg_ajax').remove();
                        jQuery('input.webinar_start_time').removeClass('error');
                        var thisvalue = time, 
                        thisdate = new Date(thisvalue),
                        month = ("0" + (thisdate.getMonth() + 1)).slice(-2),
                        minit = thisdate.getMinutes(),
                        shouldadd = 5 - (minit % 5),
                        newmint = (shouldadd < 5) ? minit + shouldadd : minit,
                        newmint = ("0" + newmint).slice(-2),
                        newgetdate = ("0" + thisdate.getDate()).slice(-2),
                        newdate = newgetdate + '/' + month + '/' + thisdate.getFullYear() + ' ' + thisdate.getHours() + ':' + newmint;
                        input.val(newdate);
                    },
                    onGenerate:function(ct,$i){
                            $('.xdsoft_time_variant .xdsoft_time').each(function(index){
                                var thistime = $(this).text();
                                if(disabledDates.indexOf(thistime) !== -1){
                                    $(this).addClass('xdsoft_disabled');
                                }
                            });
                    }, 
                    onChangeDateTime: function (dp, $input) {
                        var selectedDate = new Date($input.val());
                        $.ajax({
                            method: 'POST',
                            url: dcd_fe_object.ajax_url,
                            data: {
                                action: 'check_webinar_existing_date', 
                                selected: $input.val()
                            },
                            success: function (result) {
                                // console.log(result);
                                $('.xdsoft_time_variant .xdsoft_time').each(function(index){
                                    var thistime = $(this).text(),
                                    desabletime = result.hide_time;
                                    if(desabletime.indexOf(thistime) !== -1){
                                        $(this).addClass('xdsoft_disabled');
                                    }
                                });
                            }
                        });



                        // console.log(selectedDate);
                        if (selectedDate !== "Invalid Date") {
                            var roundMinute = (Math.ceil(selectedDate.getMinutes() / 15) * 15) % 60;
                            selectedDate.setMinutes(roundMinute);

                            var finalDate = fmt.formatDate(selectedDate, 'd/m/Y H:i');
                            // console.log(finalDate);
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
                        // console.log('t: ' + t);
                        // console.log('c: ' + c);
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

        $(document.body).on('keyup', 'input.form-control.webinar_start_time', function(){
            $(this).val('');
        });


        

        $(".thim_course_media_intro").on('keyup', function (e) {
            console.log('chagne o');
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
            if ($(this).val() >= 2000) {
                $(this).removeClass('error');
                $('.dcd-course-next-save').removeClass('disabled');
            } else {
                $(this).addClass('error');
                $('.dcd-course-next-save').addClass('disabled');
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
            jQuery('span.mobile_section_toggle').hide();
            $('.hidden-review-publish').remove();
            var currentCourseTab = $('.e-course-tabs').find('.e-tab');
            $(currentCourseTab).each(function (i, r) {
                if ($(r).hasClass('active')) {
                    if ($(this).prev().data('name') === "curriculum") {
                        $(this).prev().addClass('active');
                        $(this).removeClass('active');
                        jQuery('span.mobile_section_toggle').show();
                        if(jQuery('span.mobile_section_toggle').hasClass('active')){
                            jQuery('span.mobile_section_toggle').text('<');
                            jQuery('.mobile_section_toggle').css('right', '0');
                        }else{
                            jQuery('span.mobile_section_toggle').text('>');
                        }
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