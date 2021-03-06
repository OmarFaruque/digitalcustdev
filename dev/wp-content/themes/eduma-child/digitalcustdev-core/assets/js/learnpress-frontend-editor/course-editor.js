/**
 * Course Editor
 *
 * @author ThimPress
 * @package CourseEditor/JS
 * @version 3.0.0
 */
;(function ($) {

    function toggleSalePriceSchedule() {

        var $el = $(this),
            $dateFields = $('.lp-course-sale_start-field, .lp-course-sale_end-field'),
            id = $el.attr('id');

        if (id === '_lp_sale_price_schedule') {
            $(this).hide();
            $dateFields.removeClass('hide-if-js');
            $(window).trigger('resize.calculate-tab');
        } else {
            $('#_lp_sale_price_schedule').show();
            $dateFields.addClass('hide-if-js').find('#_lp_sale_start, #_lp_sale_end').val('');
            $(window).trigger('resize.calculate-tab');
            $('#_lp_sale_start, #_lp_sale_end').siblings('.error').remove();
        }

        return false;
    }

    function initDatePicker(el) {
        var $dateFields = $(el).find('.rwmb-datetime').each(function () {
            $(this).removeClass('hasDatepicker').datetimepicker($.extend({}, {
                onSelect: function () {
                    var startDate = Date.parse($dates[0].value),
                        endDate = Date.parse($dates[1].value),
                        nowDate = Date.now(),
                        $startDate = $dates.eq(0).parent('.rwmb-input'),
                        $endDate = $dates.eq(1).parent('.rwmb-input');

                    hasError = false;

                    if ($dates[0].value && isNaN(startDate)) {
                        showError($startDate, 'start_date_invalid');
                    } else if ($dates[1].value && isNaN(endDate)) {
                        showError($endDate, 'end_date_invalid');
                    } else {
                        if ($dates[1].value && endDate < nowDate) {
                            showError($endDate, 'notice_sale_start_date');
                        } else if ($dates[0].value && $dates[1].value) {
                            if (startDate >= endDate) {
                                showError($endDate, 'notice_sale_start_date');
                            }
                        }
                    }

                    if (hasError) {
                        return;
                    }

                    $startDate.find('.error').remove();
                    $endDate.find('.error').remove();
                }
            }, $(this).data('options')));
        }).filter(function () {
            return this.value !== '';
        });

        var $dates = $('#_lp_sale_start, #_lp_sale_end'),
            showError = function (el, i18n) {
                var msg = lpAdminCourseEditorSettings.i18n[i18n] || i18n,
                    $input = $(el).find('input[type="text"]');

                $(el).find('.error').remove();
                $('<div class="error">' + msg + '</div>').insertAfter($input);

                hasError = true;
            },
            hasError = false;

        $(document)
            .off('change', '#_lp_sale_start')
            .off('change', '#_lp_sale_end')
            .on('click', '#_lp_sale_price_schedule', toggleSalePriceSchedule)
            .on('click', '#_lp_sale_price_schedule_cancel', toggleSalePriceSchedule)

        if ($dateFields.length) {
            $('#_lp_sale_price_schedule').trigger('click')
        }
    }

    Vue.component('e-course-category', {
        template: '#tmpl-e-course-category',
        props: ['categories'],

        methods: {
            hasCategories: function () {
                return !$.isEmptyObject(this.categories);
            }
        }
    });

    Vue.component('e-course-category-option-deep', {
        template: '#tmpl-e-course-category-option-deep',
        props: ['categories'],
        mounted: function () {
        },
        methods: {
            hasCategories: function () {
                return !$.isEmptyObject(this.categories);
            }
        }
    });

    function Course_Editor($store) {
        return new Vue({
            el: '#frontend-editor',
            data: function () {
                return {
                    item: {id: 0},
                    showSettings: false,
                    showModalSelectItemsToggle: false,
                    xyz: {
                        show: false
                    },
                    modalData: null,
                    activity: false,
                    activityType: '',
                    newCategory: {
                        id: 0,
                        name: '',
                        parent: 0
                    },
                    formData: {}
                }
            },
            events: {
                getStore: function () {
                    return this.$store;
                }
            },
            computed: {
                countItems: function () {
                    return 10;
                },
                sections: function () {
                    return this.$dataStore().sections;
                },
                categories: {
                    get: function () {
                        return this.$dataStore().categories;
                    },
                    set: function (v) {
                        this.$dataStore().categories = v;
                    }
                },
                flattenCategories: {
                    get: function () {
                        return this.$dataStore().flattenCategories;
                    },
                    set: function (v) {
                        this.$dataStore().flattenCategories = v;
                    }
                }
            },
            watch: {
                categories: {
                    handler: FE_Helpers.debounce(function () {
                        this.updateCourseCategories();
                    }, 300, this),
                    deep: true
                },
                sections: {
                    handler: FE_Helpers.debounce(function () {
                        var $sections_exists = false;
                        if (this.sections.length > 0) {
                            $(this.sections).each(function (i, r) {
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
                            $('span.step_hide_error').remove();
                            $('.dcd-course-next-curriculum').show();
                        } else {
                            $('span.step_hide_error').remove();
                           var steperror = '<span class="step_hide_error d-block text-right error">'+lp_webinars.lesson_create_msg+'</span>';
                           $('.dcd-course-next-curriculum').closest('div.e-form-field').append(steperror);
                           $('.dcd-course-next-curriculum').hide();
                        }
                    }, 300, this),
                    deep: true
                }
            },
            created: function () {
                setTimeout(function (a) {
                    a.init.apply(a)
                }, 10, this);

                this.__$store = $store;
                this.$request = new FE_Helpers.Request(this.$store(), {
                    course_ID: this.$dataStore().course_ID
                });

                this.$on('select-item', function (args) {

                });
               
                
            },
            mounted: function () {
                var $vm = this;
                initDatePicker(this.$el);
                $('.learn-press-tip').LP('QuickTip');

                var oldData = JSON.stringify(this.$dataStore());

                $(document).on('fe.start-activity', function () {
                    $vm.activity = true;
                }).on('fe.stop-activity', function () {
                    $vm.activity = false;
                });

                $(document).on('FE.request-start', function (e, data) {
                    if (!data) {
                        return;
                    }
                    $vm.activityTimeout && clearTimeout($vm.activityTimeout);
                    $vm.setActivity(data.__activity);
                }).on('FE.request-completed', function (e, data) {
                    if (!data) {
                        return;
                    }
                    $vm.activityTimeout && clearTimeout($vm.activityTimeout);
                    $vm.setActivity(data.__activity);
                    $vm.activityTimeout = setTimeout(function () {
                        $vm.setActivity(false);
                    }, 2000)
                });

                (function () {
                    function sync() {
                        var jsonData = $($vm.$el).serializeJSON(),
                            _data = JSON.stringify(jsonData);
                            console.log(jsonData);   
                        if (_data !== formData) {

                            FE_Helpers.Course_Editor_Request('', 'update_course', $.extend({}, jsonData, {__activity: true})).then(function (response) {
                                
                                if (undefined !== response.meta['_lp_course_forum']) {
                                    var thePost = response.meta['_lp_course_forum'],
                                        $sel = $('#_lp_course_forum');
                                    if (!$sel.find('option[value="' + thePost.id + '"]').length) {
                                        $sel.append('<option value="' + thePost.id + '">' + thePost.name + '</option>')
                                    }

                                    $sel.val(thePost.id);
                                }

                                if( jsonData.post_on_review ){
                                    $('#pendingpopup').fadeIn(500, function() {
                                        window.setTimeout( function(){
                                             window.location.href = jsonData.post_on_review;
                                        }, 5000 );
                                    });
                                }
                            });

                            formData = _data;
                            $vm.formData = jsonData;
                        } else {
                            if( jsonData.post_on_review ) {
                                window.location.href = jsonData.post_on_review;
                            }
                        }
                    }

                    this.formData = $vm.$().serializeJSON();
                    var formData = JSON.stringify(this.formData);
                    
                    $vm.$('.dcd-course-next-save').on('click', FE_Helpers.debounce(function () {
                        jQuery('span.mobile_section_toggle').hide();

                        /*
                        * Mobile toggle for editor second tab
                        */
                        if(jQuery('#e-course-curriculum').length){
                            if(jQuery('#e-course-curriculum').is(':visible')){
                                if(jQuery( window ).width() <= 768){
                                    jQuery('span.mobile_section_toggle').remove();
                                    var html = '<span class="mobile_section_toggle active"><</span>';
                                    jQuery('div#e-course-curriculum .e-course-sections').append(html);
                                    jQuery('#frontend-editor #e-tab-content-curriculum').addClass('toggle-active');
                                    jQuery('#frontend-editor #e-tab-content-curriculum #e-course-curriculum').css('left', '0')
                                }
                            }
                        }

                        /*
                        * Close mobile button
                        */
                        // jQuery('.mobile_section_toggle').addClass('active');
                        // jQuery('#frontend-editor #e-tab-content-curriculum #e-course-curriculum').css('left', '0')
                        
                        
                        // if(!jQuery('#e-course-curriculum').is(':visible')){
                        //     jQuery('span.mobile_section_toggle').hide();
                        // }else{
                        //     jQuery('span.mobile_section_toggle').show();
                        // }
                        // jQuery('.mobile_section_toggle').css('right', '0');


                        if(jQuery(this).hasClass('submit-for-review') && dcd_fe_object.course_type == 'webinar'){
                            var post_id = jQuery('input[name="post_ID"]').val();
                            $('div#frontend-course-editor').find('ul#error_msg_ajax').remove();
                            $.ajax({
                                method: 'POST',
                                url: dcd_fe_object.ajax_url,
                                data: {
                                    action: 'check_webinar_ocuppied_lesson_time', 
                                    postid: post_id
                                },
                                success: function (result) {
                                    // console.log(result);
                                    if(result.msg == 'has_error'){
                                        var html = '';
                                        $(result.errors).each(function(k, v){
                                            html += '<li>'+v+'</li>';
                                        });
                                        $( "div#frontend-course-editor" ).prepend( "<ul id='error_msg_ajax'>"+html+"</p>" );
                                        if(jQuery('input.webinar_start_time').length){
                                            jQuery('input.webinar_start_time').addClass('error');
                                        }
                                    }else{
                                        sync();
                                    }
                                }
                            });
                        }else{
                            sync();
                        }

                        
                    }, 1000)).trigger('dispatch');


                  














                    jQuery(document).on('keypress change', '.e-tab.active[data-name="general"] input, .e-tab.active[data-name="general"] select, .e-tab.active[data-name="general"] textarea', function(){
                       console.log('update');
                        sync();
                    });

                    if (typeof tinymce !== 'undefined') {
                        setTimeout(function () {
                            var $editor = tinymce.get('post_content');
                            if ($editor) {
                                $editor.on('Change KeyUp', function (e, b) {
                                    sync();
                                });
                            }
                        }, 1000);
                    }
                })();


                // $('#lesson_media_url').on('change', FE_Helpers.debounce(function () {
                //     console.log('changed');
                // }, 1000)).trigger('dispatch');

               

                if (typeof tinymce !== 'undefined') {
                    setTimeout(function () {
                        var $editor = tinymce.get('post_content');
                        if ($editor) {
                            $editor.on('Change KeyUp', function (e, b) {
                                $('#' + this.settings.id).val(this.getContent())
                            });
                        }
                    }, 1000);
                }

                $(this.$el).addClass('ready');

                $(document).trigger('FE.editor-rendered');
            },
            methods: Course_Editor.Methods,
            $store: $store
        });
    }


    Course_Editor.Methods = {
        addItem: function ($item) {
            if (!this.$items) {
                this.$items = {};
                // Select first item
                this.item = $item.item;
            }

            this.$items[$item.itemId] = $item;
        },
        getItem: function (item) {
            if (isNaN(item) && item.itemId) {
                return this.$items[item.itemId];
            }

            return this.$items[item];
        },
        init: function () {
            var x = 0,
                $tabs = $(this.$el).find('.e-tab').each(function () {
                    var $tab = $(this).find('.e-tab-label');
                    if (x > 0) {
                        $tab.css('left', x)
                    }
                    x += $tab.outerWidth();
                }),
                $active = $tabs.filter('[data-name="' + this.$dataStore().active_tab + '"]');

            if (!$active.length) {
                $active = $tabs.first();
            }

            this.selectTab(null, $active);
            var item_ID = this.isEdit();

            if (!item_ID) {
                item_ID = this.$('.e-section-item:first').data('id');
            }

            if (item_ID && this.$items && this.$items[item_ID]) {
                this.openItemSettings(this.$items[item_ID]);
            }
        },
        isEdit: function () {
            var item_ID, m = location.href.match(/edit-post\/[0-9]+\/([0-9]+)/);

            if (m && m[1]) {
                item_ID = m[1];
            }

            return item_ID;
            //return this.$dataStore().item_ID;
        },
        isCourse: function () {
            var item_ID, m = location.href.match(/edit-post\/([0-9]+)\/([0-9]+)?/);
            if (m) {
                if (m[2]) {
                    item_ID = m[2];
                } else if (m[1]) {
                    item_ID = m[1];
                }
            }

            return item_ID;
        },
        hasCategory: function () {
            return Object.values(this.$dataStore().categories).length;
        },
        updateCourseCategories: function () {

            var i, categories = [];

            function _getCatChild(cat) {
                if (!cat.nodes) {
                    return;
                }

                var j;
                for (j in cat.nodes) {
                    if (!cat.nodes.hasOwnProperty(j)) {
                        continue;
                    }

                    if (cat.nodes[j].selected) {
                        categories.push(cat.nodes[j].id);
                    }
                    _getCatChild(cat.nodes[j]);
                }
            }

            for (i in this.categories) {
                if (!this.categories.hasOwnProperty(i)) {
                    continue;
                }

                if (this.categories[i].selected) {
                    categories.push(this.categories[i].id);
                }

                _getCatChild(this.categories[i]);
            }
            FE_Helpers.startActivity();
            FE_Helpers.Course_Editor_Request('', 'update-course-categories', {'categories': categories}).then(function (r) {
                //resolve(r);
            }, function (r) {
                //reject(r)
                FE_Helpers.stopActivity();
            })
        },
        getCatOptionValue: function (cat) {
            return ('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').repeat(cat.deep - 1) + cat.name;
        },
        selectTab: function (e, $tab) {
            var edId;
            $tab = $tab || $(e.target).closest('.e-tab');

            if ($tab.length && !$tab.hasClass('active')) {
                var $oldTab = $tab.addClass('active').siblings().removeClass('active');
                this.$request(
                    '',
                    'fe/active-tab',
                    {
                        tab: $tab.data('name')
                    }
                );

                $('#frontend-editor').removeClass(
                    $tab.siblings().map(function () {
                        return $(this).attr('data-name') + '-active';
                    }).get().join(' ')).addClass($tab.attr('data-name') + '-active');

                for (edId in tinyMCEPreInit.qtInit) {
                    FE_Helpers.QuickTags(edId, tinyMCEPreInit.qtInit[edId]);
                }

                $(window).trigger('resize.frontend-editor-settings')
            }
        },
        countItems: function () {
            return this.$dataStore().countItems;
        },

        getItemIds: function () {
            var ids = [];
            $.each(this.$dataStore().sections, function () {
                ids = ids.concat(this.items.listPluck('id'))
            });

            return ids;
        },

        openItemSettings: function ($item, $section) {
            $(document).triggerHandler('LP.close-item-settings', [this.item]);

            this.showSettings = true;
            this.item = $item.item;
            // console.log(this.item);
            var url = (typeof this.item != 'undefined') ? this.$dataStore('coursePermalink') + '/' + this.item.id + '/' : '';
            //url = $(document).triggerHandler('LP.open-item-settings', [url, $item.item]);

            if (url) {
                LP.setUrl(url);
            }


        },
        closeItemSettings: function () {
            this.showSettings = false;
        },
        openModelSelectItems: function (args) {
            this.showModalSelectItemsToggle = true;
            this.xyz.show = true;
            this.modalData = args;
        },
        setActivity: function (activity) {
            switch ($.type(activity)) {
                case 'boolean':
                case 'string':
                    this.activity = activity;
                    this.activityType = '';
                    break;
                case 'object':
                    this.activity = activity.message;
                    this.activityType = activity.type;
            }
        },
        currentUserCanPublishCourse: function () {
            var store = this.$dataStore();
            if (store.settings.reviewCourseBeforePublish === 'no') {
                return true;
            }

            if (store.courseStatus === 'publish') {
                return true;
            }

            if (store.courseStatus === 'publish') {
                return true;
            }

            return false;
        },
        _deleteCourse: function (e, course_ID, permanently, deleteItems) {
            e.preventDefault();
            if (!confirm(FE_Localize.get('confirm_trash_course'))) {
                return;
            }
            $(e.target).addClass('icon icon-ajax').closest('.e-course-actions').css('visibility', 'visible');
            FE_Helpers.Course_Editor_Request('', 'trash-course', {
                course_ID: course_ID,
                permanently: permanently,
                deleteItems: deleteItems
            }).then(function (r) {
                LP.reload()
            })
        },
        /**
         * Restore course trashed.
         *
         * @param {MouseEvent} e
         * @param {int} course_ID
         * @private
         */
        _restoreCourse: function (e, course_ID) {
            e.preventDefault();
            $(e.target).addClass('icon icon-ajax').closest('.e-course-actions').css('visibility', 'visible');
            FE_Helpers.Course_Editor_Request('', 'restore-course', {
                course_ID: course_ID
            }).then(function (r) {
                if (r && r.course_ID) {
                    LP.reload();
                }
            })
        },
        _addCategory: function (e) {
            if (!this.newCategory.name) {
                return;
            }

            var $vm = this;

            FE_Helpers.Course_Editor_Request('', 'add-new-category', {
                category: this.newCategory
            }).then(function (r) {
                console.log('this omae');
                if (r && r.cats) {
                    Vue.set($vm, 'categories', r.cats);
                }
            });

            this.newCategory = {};
        }
    };

    Course_Editor.Methods = $.extend({}, FE_Base.Store_Methods, Course_Editor.Methods);

    $(document).ready(function () {
        window.FE_Course_Editor = Course_Editor(window.FE_Course_Store);
        window.FE_Helpers.Course_Editor_Request = new FE_Helpers.Request(FE_Course_Store, {
            course_ID: FE_Course_Store.getters.course_ID
        });
    });

    $(document).on('mousedown', function () {
        $(this).data('mouse_hold_time_start', Date.now());
    }).on('mouseup', function (e) {
        var holdTime = Date.now() - $(this).data('mouse_hold_time_start');
        $(e.target).data('mouse_hold_time', holdTime);
    });


    Object.defineProperty(Array.prototype, 'findElementByField', {
        value: function (field, value, find, compare) {

            return this.find(function (a, i) {

                var found = false;
                if (i == 0) {
                    if ($.isArray(find)) {
                        while (find.length) {
                            find.pop()
                        }
                    } else {
                        for (var j in find) {
                            delete find[j];
                        }
                    }
                }

                switch (compare) {
                    case '===':
                        found = a[field] === value;
                        break;
                    default:
                        if ($.isFunction(compare)) {
                            found = compare.apply(this, [a, value]);
                        } else {
                            found = a[field] == value;
                        }
                }

                if (found) {
                    if ($.isArray(find)) {
                        find.push(a);
                        find.push(i);
                    } else {
                        find.value = a;
                        find.position = i;
                    }
                    return true;
                }
                return false;
            })
        }
    });


    Object.defineProperty(Array.prototype, 'random', {
        value: function () {
            function getRandomInt(min, max) {
                return Math.floor(Math.random() * (max - min + 1)) + min;
            }

            return this[getRandomInt(0, this.length - 1)];
        }
    });

    Object.defineProperty(Array.prototype, 'diffArray', {
        value: function (arr) {

            var a1 = [], a2 = [], a3 = [], i, n;

            for (i = 0, n = this.length; i < n; i++) {
                a1.push('' + this[i]);
            }

            for (i = 0, n = arr.length; i < n; i++) {
                a2.push('' + arr[i]);
            }

            a1 = a1.filter(function (elm) {
                return a2.indexOf(elm) === -1;
            });

            for (i = 0, n = a1.length; i < n; i++) {
                for (var j = 0, m = this.length; j < m; j++) {
                    if (this[j] == a1[i]) {
                        a3.push(this[j]);
                    }
                }
            }

            return a3;
        }
    });

    Object.__proto__.random = function () {
        function getRandomInt(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        var items = this.values();

        return this[getRandomInt(0, this.length - 1)];
    }

    $(window).on('resize.fe', FE_Helpers.debounce(function () {
        var h = $('#wpadminbar').outerHeight(),
            $browseItems = $('#e-browse-items'),
            $modalWrap = $browseItems.closest('.e-settings-window'),
            $prevElement = $browseItems.prev();

        $('#frontend-editor, #e-page-header').css('top', h);

        if ($prevElement.length) {
            $browseItems.css({
                height: $modalWrap.height() - ($prevElement.offset().top + $prevElement.outerHeight() + 80)
            });
        }
    }, 300));

    $(document).on('FE.count-question-answers', function (e, count, $vm) {
        if ($vm.question.type === 'fill_in_blank') {
            try {
                count = $vm.question.answers[0].blanks.length;
            } catch (e) {
                count = 0;
            }
        }
        return count;
    });

})(jQuery);