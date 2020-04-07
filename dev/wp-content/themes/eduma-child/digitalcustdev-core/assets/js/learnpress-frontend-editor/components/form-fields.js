;(function ($) {


    Vue.component('e-form-field-textarea', {
        template: '#tmpl-e-form-field-textarea',
        props: ['item', 'itemData', 'request', 'field', 'settings'],
        data: function () {
            return {
                drawComponent: true,
            }
        },
        created: function () {
        },
        mounted: function () {
            jQuery(document).on('click', 'button#insert-media-button.insert-media_cus', function(e){
                // console.log('tst omar');
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
                            jQuery('textarea#lesson_media_url').val(attachment.url);
                            // $button.siblings('input').val(attachment.url).change();
                            
                            /*
                            * Save via auto-save
                            */

                           $.post(
                            _wpUtilSettings.ajax,
                            {
                                action: 'step_two_custom_autosave',
                                dataType: 'json',
                                post_id: $('input[name="post_ID"]').val(),
                                field_value: attachment.url,
                                field_name: '_lp_lesson_video_intro',
                                item_id:  $('ul.e-section-content > li.e-section-item.e-selected').data('id'),
                                user_id: userSettings.uid,
                            },
                            function(data_return){
                                /* Nothing */
                            }
                        );

                
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
            
        },
        methods: {
            redraw: function () {
                var vm = this;
                vm.drawComponent = false;
                Vue.nextTick(function () {
                    console.log('test omar');
                    vm.drawComponent = true;
                });
            }
        }
    });


    


    Vue.component('e-form-field-text', {
        template: '#tmpl-e-form-field-text',
        props: ['item', 'itemData', 'request', 'field', 'settings'],
        data: function () {
            return {
                drawComponent: true
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
            }
        }
    });



    Vue.component('e-form-field-duration', {
        template: '#tmpl-e-form-field-duration',
        props: ['item', 'itemData', 'request', 'field', 'settings'],
        data: function () {
            return {
                drawComponent: true,
                settingValue: this.get()
            }
        },
        watch: {
            settingValue: function (value) {
                this.itemData.settings[this.field.id] = value.join(' ')
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
                    number = parseInt(settings[this.field.id]),
                    v = (settings[this.field.id] + '').replace(/[0-9]+\s?/, '');
                return [number ? number : 0, v ? v : 'minute']
            }
        }
    });

    Vue.component('e-form-field-lesson-duration', {
        template: '#tmpl-e-form-field-lesson-duration',
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
                    number = parseInt(settings[this.field.id]),
                    v = (settings[this.field.id] + '').replace(/[0-9]+\s?/, '');
                return number && v ? number + ' ' + v : '45 minute';
            }
        }
    });

    Vue.component('e-form-field-yes-no', {
        template: '#tmpl-e-form-field-yes-no',
        props: ['item', 'itemData', 'request', 'field', 'settings'],
        data: function () {
            return {
                drawComponent: true,
                //settingValue: this.get()
            }
        },
        computed: {
            settingValue: {
                get: function () {
                    var settings = this.itemData.settings || {};
                    return settings[this.field.id];
                },
                set: function (v) {
                    this.itemData.settings[this.field.id] = v;
                }
            }
        },
        watch: {
            settingValuex: function (value) {
                this.itemData.settings[this.field.id] = value ? 'yes' : 'no';
                return value;
            }
        },
        methods: {
            redraw: function () {
                // var vm = this;
                // vm.drawComponent = false;
                // Vue.nextTick(function () {
                //     vm.drawComponent = true;
                // });
            },
            // get: function () {
            //     var settings = this.itemData.settings || {};
            //     //this.itemData.settings[this.field.id] === 'yes'
            //     console.log('GET', settings[this.field.id])
            //     return settings[this.field.id] === 'yes';
            // }
        }
    });

    Vue.component('e-tinymce', {
        template: '#tmpl-e-tinymce',
        props: {
            id: {
                type: 'String',
                required: true
            },
            value: {default: ''},
            redraw: {
                type: 'Boolean'
            }
        },
        data: function () {
            return {
                content: '',
                isTyping: false,
                editorMode: true
            }
        },
        beforeDestroy: function () {
            this.$editor.destroy();
        },
        watch: {
            value: function (newValue) {
                if (!this.isTyping && this.$editor !== null) {
                    setTimeout(function ($m, value) {
                        $m.$editor.setContent(value);
                    }, 70, this, newValue)
                }
                this.$emit('input', newValue);
                return newValue;
            },
            content: function (v) {
                //this.$editor.setContent(v);
                return v;
            }
        },
        mounted: function () {
            this.content = this.value;
            this.init();
            console.log(this.id);
        },
        methods: {
            init: function () {
                var self = this;
                var tinyMCEInit = $.extend({}, tinyMCEPreInit.mceInit.post_content, {
                    selector: '#' + this.id,
                    setup: function ($editor) {
                        self.$editor = $editor;

                        $editor.on('Change KeyUp', $.proxy(function (e, b) {
                            this.onChange();
                        }, self));

                    },
                    height: 300
                });
                tinymce.init(tinyMCEInit);

                var tags = $.extend({}, tinyMCEPreInit.qtInit['post_content']);
                tags.id = this.id;
                FE_Helpers.QuickTags(this.id, tags);
                // console.log('init call');
                // console.log('this id: ' . this.id);
            },
            onChange: function () {
                this.isTyping = true;
                FE_Helpers.debounce(function ($vm) {
                    $vm.isTyping = false;
                }, 100)(this);
                this.$emit('input', this.$editor.getContent());
            },
            switchMode: function () {
                this.editorMode = !this.editorMode;
                tinymce.execCommand('mceToggleEditor', this.editorMode, this.id)
            },
            getEditorId: function () {
                return 'wp-' + this.id + '-wrap';
            }
        }
    });
})(jQuery);