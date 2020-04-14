;(function ($) {
    "use strict";

    Vue.component('e-course-item-settings', {
        template: '#tmpl-e-course-item-settings',
        props: ['section', 'item', 'itemData', 'request', 'content'],
        data: function () {
            return {
                dataChanged: false,
                $prevItem: false,
                $nextItem: false
            }
        },
        watch: {
            itemData: {
                handler: function (v) {
                    var vm = this;
                    vm.dataChanged = true;
                    Vue.nextTick(function () {
                        vm.dataChanged = false;
                    });

                    return v;
                }, deep: true
            },
            'itemData.id': function () {
                $(this.$el).removeAttr('class').scrollTop(0);
            },
            'itemData.settings': {
                handler: function (settings) {
                    this.updateItemSettings();
                },
                deep: true
            }
        },
        computed: {
            // itemData: function () {
            //     return this.item.item || {};
            // },
            title: function () {
                return this.item.title;
            },

        },
        created: function () {
        },
        mounted: function () {

            var settings = this.itemData.settings,
            ids = this.itemData.id;

            console.log('settings : ' + settings);
/* Lession Media start */
jQuery(document).on('click', '.e-tab.active button#insert-media-button.insert-media_cus', function(e){
    console.log('inside media cus');
    // jQuery(this).unbind();
    var thisid = jQuery(this).data('id');
    
    e.preventDefault();
    $.post(
        _wpUtilSettings.ajax,
        {
            action: 'check_foldersize',
            dataType: 'json',
            post_id: $('input[name="post_ID"]').val(),
            user_id: userSettings.uid,
        },
        function (data) {
            console.log('ajsx success');
            data = JSON.parse(data);
            // console.log(data);
            var $button = $(this);
            // Create the media frame.
            var file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select or upload file',
                library: { // remove these to show all
                    type: ['video'] // specific mime
                },
                button: {
                    text: 'Select'
                },
                multiple: false  // Set to true to allow multiple files to be selected
            });

              // When an image is selected in the media frame...
            file_frame.on('library:selection:add', function (e) {
                $.post(
                    _wpUtilSettings.ajax,
                    {
                        action: 'check_foldersize',
                        dataType: 'json',
                        post_id: $('input[name="post_ID"]').val(),
                        user_id: userSettings.uid,
                    },
                    function(response){
                        data = JSON.parse(response);
                        
                        var attachment1 = file_frame.state().get('selection').first().toJSON();
                        var totalsize_with_folder = data.titalsize + attachment1.size;
                        var mb = Math.round(totalsize_with_folder / 1048576);
                        if(mb >= 2000){
                            $( "div.media-frame-content" ).prepend('<div class="upload_limit_error"><h6><span class="crose_limit">'+data.display_msg+'</span></h6></div>');
                            $( 'div.media-router > button:first-child, .media-toolbar-primary.search-form button' ).hide();
                        }
                    }
                );
            });
            // file_frame.close();
            file_frame.on('select', function () {
                // We set multiple to false so only get one image from the uploader
    
                var attachment = file_frame.state().get('selection').first().toJSON();
                // console.log(attachment);
                // jQuery('textarea#lesson_media_url').val(attachment.url);
                // $button.siblings('input').val(attachment.url).change();
                
                /*
                * Save via auto-save
                */
            //    $.post(
            //     _wpUtilSettings.ajax,
            //     {
            //         action: 'step_two_custom_autosave',
            //         dataType: 'json',
            //         post_id: $('input[name="post_ID"]').val(),
            //         field_value: attachment,
            //         field_name: '_lp_lesson_video_intro_internal',
            //         item_id:  $('ul.e-section-content > li.e-section-item.e-selected').data('id')
            //     },
            //     function(response){
                
                    /* Nothing */
                    var lession_id = jQuery('ul.e-course-sections ul.e-section-content li.e-selected').data('id');
                    var outputhtml = '<div data-name="upload_intro_video" data-type="file" data-key="field_5d52623d7778a" class="acf-field acf-field-file acf-field-5d52623d7778a">'
                    +'<div class="acf-input">'
                        +'<div data-library="uploadedTo" data-mime_types="mp4" data-uploader="wp" class="acf-file-uploader has-value">'                                  
                            +'<div class="show-if-value file-wrap">'
                                +'<div class="file-icon">'
                                    +'<img data-name="icon" src="'+attachment.icon+'" alt="" title="'+attachment.title+'">'
                                +'</div> '
                                +'<div class="file-info">'
                                    +'<p><strong data-name="title">'+attachment.title+'</strong></p> '
                                    +'<p><strong>File name:</strong> '
                                    +'<a data-name="filename" href="'+attachment.url+'" target="_blank">'+attachment.filename+'</a></p> '
                                    +'<p><strong>File size:</strong> <span data-name="filesize">'+attachment.filesizeHumanReadable+'</span></p>'
                                +'</div> '
                                +'<div class="acf-actions -hover">'
                                    +'<a href="#" data-id="'+lession_id+'" title="Remove" class="acf-icon -cancel remove_lesson_media_attachment dark"></a>'
                                +'</div>'
                            +'</div>'
                            +'</div>'
                        +'</div>'
                    +'</div>';

                    
                        jQuery('body').find('.single_sub_section.add_video').next('.external_lession_media').addClass('hidden');
                        jQuery('body').find('div[data-id="'+thisid+'"]#lession_Int_media').append(outputhtml);
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').removeClass('hidden');
                        jQuery('body').find('div[data-id="'+thisid+'"]#lession_Int_media').find('.wp-media-buttons').addClass('hidden');
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('img[data-name="icon"]').attr('src', attachment.icon);
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.file-info p:nth-child(1)').text(attachment.title);
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.file-info p:nth-child(2)').find('a').text(attachment.filename).attr('href', attachment.url);
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.file-info p:nth-child(3)').find('span').text(attachment.filesizeHumanReadable);
                        // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.acf-actions').find('a').data('id', lession_id);
                        var editor = tinymce.get('e-item-content');
                        editor.setContent('[video width="1920" height="1080" mp4="'+attachment.url+'"][/video]' + editor.getContent());
    
                        // jQuery('input.inner_vide_field').val(attachment.id);
                        jQuery('li.omar3.e-form-field.text.textdown input').val(JSON.stringify(attachment));
                        
                        
                        // this.updateItemSettings();
                        
                            console.log('tis update inside ajax');
                            // console.log(this.itemData.settings);
                            // console.log('this id: ' + this.itemData.id);
                            console.log(settings);
                            FE_Helpers.startActivity();
                            FE_Helpers.Course_Editor_Request('', 'update-post-meta', {
                                postMeta: settings,
                                post_ID: ids
                            }).then(function (res) {
                                console.log('success upload');
                                FE_Helpers.stopActivity();
                            });
                        
                        // jQuery('li.omar3.e-form-field.text.textdown input').trigger('change');
                        // jQuery('textarea#lesson_media_url').trigger('change');
                // });
             });
    
            // Finally, open the modal
            file_frame.open();
            var totalsize_with_folder = data.titalsize;
            var mb = Math.round(totalsize_with_folder / 1048576);
            if(mb >= 2000){
                    $( "div.media-frame-content" ).prepend('<div class="upload_limit_error"><h6><span class="crose_limit">'+data.display_msg+'</span></h6></div>');
                    $( 'div.media-router > button:first-child, .media-toolbar-primary.search-form button' ).hide();
            }
        }
     );
});
/* Lession Media End  */





            this._mounted = true;
            return;
            this.getNavItems();
            var prop;
            for (prop in this.itemData) {
                if (!this.itemData.hasOwnProperty(prop)) {
                    continue;
                }


            }

  








        },
        methods: $.extend({}, FE_Base.Store_Methods, {
            /**
             * Update item settings to DB
             */
            updateItemSettings: FE_Helpers.debounce(function () {
                console.log('tis update');
                console.log(this.itemData.settings);
                // console.log('this id: ' + this.itemData.id);
                FE_Helpers.startActivity();
                FE_Helpers.Course_Editor_Request('', 'update-post-meta', {
                    postMeta: this.itemData.settings,
                    post_ID: this.itemData.id
                }).then(function (res) {
                    FE_Helpers.stopActivity();
                })
            }, 300, this),
            getContext: function () {
                return this.itemData ? this.itemData.type : '';
            },
            hasPrevItem: function () {
                this.getNavItems();
                return this.$prevItem;
            },
            hasNextItem: function () {
                this.getNavItems();
                return this.$nextItem;
            },
            close: function (e) {
                e.preventDefault();
                this.$emit('closeItemSettings', this);
                this.$parent.closeItemSettings();
            },
            getNavItems: function () {
                if (!this.item) {
                    return;
                }
                return;
                var $els = $('#e-course-curriculum').find('.e-section-item:not(.placeholder)').find('.item-title'),
                    index = $els.index(this.item.$('.item-title')),
                    $next = false,
                    $prev = false;


                if (index < $els.length - 1) {
                    $next = $els.eq(index + 1).data('$instance');
                }
                if (index > 0) {
                    $prev = $els.eq(index - 1).data('$instance');
                }

                if ($prev) {
                    this.$prevItem = $prev;
                } else {
                    this.$prevItem = false;
                }

                if ($next) {
                    this.$nextItem = $next;
                } else {
                    this.$nextItem = false;
                }

            },
            getNavItemText: function (nav) {
                if (nav === 'prev' && this.$prevItem) {
                    return this.$prevItem.item.title;
                } else if (this.$nextItem) {
                    return this.$nextItem.item.title;
                }

                return false;
            },
            getItemName: function () {
                var $vm = this,
                    t = this.$dataStore('course_item_types').find(function (a) {
                        return a.type == $vm.itemData.type;
                    });

                return t ? t.name : this.itemData.id;
            },
            nextItem: function () {

                if (this.$nextItem) {
                    this.item = this.$nextItem;
                    this.itemData = this.item.item;
                }
                this.getNavItems();
            },
            prevItem: function () {
                if (this.$prevItem) {
                    this.item = this.$prevItem;
                    this.itemData = this.item.item;
                }
                this.getNavItems();
            },
            getComponentItemSettings: function () {
                return 'e-item-settings-' + this.itemData.type;
            },
            update: function (callback) {
                var section = this.item.getSection();
                this.request('', 'update-item-settings', {
                    section_ID: section.id,
                    item_ID: this.itemData.id,
                    settings: this.getItemSettings(),
                    position: this.item.getPosition() + 1
                }).then($.proxy(function (response) {
                    this.updateComplete(response);
                    $.isFunction(callback) && callback.apply(this, response);
                }, this));
            },
            apply: function (i) {
            },
            updateComplete: function (response) {
                var data = response || {};

                if (data.result === 'error') {
                    return;
                }

                this.itemData.id = data.item.id;
            },
            getFormattedId: function () {
                return this.itemData.id ? '#' + this.itemData.id : '#####';
            },
            getItemSettings: function () {
                var settings = FE_Helpers.clone(this.itemData.settings) || {};
                delete settings['__FIELDS__'];

                settings.__title = this.itemData.title;
                settings.__content = this.itemData.content;
                settings.__type = this.itemData.type;

                var filteredSettings = $(document).triggerHandler('e-item-settings', settings);

                return settings;
            },
            enableGeneralSettings: function () {
                return this.itemData.type !== 'lp_quiz';
            },
            _updateSettings: function () {

            }
        })
    });

    Vue.component('e-form-field', {
        props: ['field', 'item', 'itemData', 'settings'],
        template: '#tmpl-e-form-field',
        computed: {},
        methods: {
            includeFormField: function (field) {
                field = field || this.field;

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
            }
        }
    });

    var __X = $.extend({}, FE_Base.Store_Methods, {
        loadSettings: function (callback) {
            var that = this;
            return;
            //if ($.isEmptyObject(this.item.item.settings)) {
            this.request('', 'load-item-settings', {
                item_ID: this.itemData.id,
                item_type: this.itemData.type
            }).then(function (response) {
                $.isFunction(callback) && callback.apply(that, [response])
            });
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
            console.log('test');
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

    var __Y = {
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
                console.log('watch 2');
                this.redraw();
            }
        },

        mounted: function () {
          console.log('test omar');
        },
        created: function () {
            this.loadSettings(this.loadSettingsCallback);
        },
        methods: $.extend({}, __X, {})
    }

    // Quiz
    var __Z = $.extend({}, __Y, {template: '#tmpl-e-course-item-settings-lp_quiz', 'name': 'e-item-settings-lp_quiz'});

    Vue.component('e-item-settings-lp_lesson', __Y);

    __Z.watch = $.extend(__Z.watch || {}, {
        'question.id': function (v) {
            if (v) {
                var $itemSettings = $(this.$el).addClass('is-showing-question').closest('#e-item-settings').addClass('editing-question');
                this.$nextTick(function () {
                    $(this.$el).find('.e-edit-question-form').css({
                        'top': $itemSettings.scrollTop(),
                        height: $itemSettings.height()
                    })
                })

            } else {
                $(this.$el).removeClass('is-showing-question').closest('#e-item-settings').removeClass('editing-question');
                $(this.$el).find('.e-edit-question-form').css('top', '')
            }
        },
        'itemData.id': function (v) {
            if( this.itemData.settings._lp_passing_grade === "" || typeof this.itemData.settings._lp_passing_grade === "undefined" ) {
                this.itemData.settings._lp_passing_grade = 80;
            }

            this.redraw();
        }
    })

    __Z.data = function () {
        return {
            drawComponent: true,
            currentTab: 'settings',
            settings: this.itemData.settings || {},
            question: null,
            xTitle: '',
            showSettingsBox: false
        }
    };

    __Z.computed = {
        settings: function () {
            return this.itemData.settings || {};
        },
        xTitle: function () {
            return this.question ? this.question.title : '';
        }
    }

    __Z.mounted = function () {
        var $vm = this;
        $(window).on('resize.resize-question-editor', FE_Helpers.debounce(function () {

            if (!$vm.question || !$vm.question.id) {
                return;
            }

            var $itemSettings = $($vm.$el).closest('#e-item-settings');
            $($vm.$el).find('.e-edit-question-form').css({
                'top': $itemSettings.scrollTop(),
                height: $itemSettings.height()
            })

        }, 300, this));

        // $(document).on('LP.open-item-settings', function (e, url, item) {
        //     if (item && $vm.item.id == item.id) {
        //
        //         var questionId = window.location.href.getQueryVar('question');
        //         console.log(window.location.href, questionId, item, $vm.item);
        //
        //         if (questionId) {
        //             $vm._setEditQuestion(questionId);
        //             return false;
        //         }
        //     }
        //
        //
        //     return url;
        // }).on('LP.close-item-settings', function (e, item) {
        //     if (item && $vm.item.id == item.id) {
        //         $vm.$nextTick(function () {
        //             $vm.question = false;
        //         })
        //         //$vm._closeQuestion();
        //     }
        // });

    };

    __Z.methods = $.extend(__Z.methods, {
        selectTab: function (e) {
            e.preventDefault();
            var tab = $(e.target).attr('data-tab');
            if (tab) {
                this.currentTab = tab;
                this.xTitle = '';
            }
        },
        isCurrent: function (tab) {
            return this.currentTab === tab;
        },
        getTabTitle: function (tab) {
            if (tab.id !== 'questions' || !this.isCurrent(tab.id)) {
                return tab.title;
            }

            return this.xTitle
        },
        isVisibleTab: function (tab) {
            if (tab.id !== 'questions') {
                return true;
            }

            return !this.xTitle;
        },
        _setEditQuestion: function (question) {

            if (!isNaN(question)) {
                question = this.itemData.questions ? this.itemData.questions.find(function (q) {
                    return q.id == question;
                }) : null;
            }

            if (!question) {
                return;
            }

            this.question = question;
            //FE_Helpers.setQueryVar('question', question.id);
            this.xTitle = question ? question.title : null;
        },
        isEditingQuestion: function () {
            return this.question;
        },
        _closeQuestion: function (e) {
            if (e) e.preventDefault();
            this.question = false;
            FE_Helpers.removeQueryVar('question');
        },
        updateQuestion: function () {

        },
        _updateSettings: function () {
        },
        loadQuestion: function (id, r) {
            if (r === 'position') {
                this._setEditQuestion(this.itemData.questions[id]);

            }
            $(this.$el).find('.e-edit-question-form').scrollTop(0);
        }
    });

    Vue.component('e-item-settings-lp_quiz', __Z);

    Vue.component('e-item-settings-general', {
        template: '#tmpl-e-course-item-settings-basic',
        props: ['item', 'itemData', 'request'],
        watch: {
            itemData: function () {
            }
        }
    });


})(jQuery);