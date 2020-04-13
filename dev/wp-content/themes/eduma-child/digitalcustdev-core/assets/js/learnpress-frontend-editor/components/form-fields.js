;(function ($) {


    Vue.component('e-form-field-textarea', {
        template: '#tmpl-e-form-field-textarea',
        props: ['item', 'itemData', 'request', 'field', 'settings'],
        data: function () {
            return {
                drawComponent: true
            }
        },
        created: function () {
            console.log(this);
        },
        mounted: function () {
            var $this = this;
            
            jQuery(document).on('click', 'a[data-id="'+$this.itemData.id+'"].remove_lesson_media_attachment', function(e){
                e.preventDefault();
                var attachment_id = jQuery(this).data('id');
                $.post(
                    _wpUtilSettings.ajax,
                    {
                        action: 'delete_lession_attachment_video',
                        dataType: 'json',
                        lession_id: attachment_id,
                    },
                    function (data){
                        var outputhtml = '<div id="wp-content-media-buttons" class="wp-media-buttons">'
                        +'<button type="button" id="insert-media-button" class="button e-button insert-media_cus add_media">'
                            +'<span class="wp-media-buttons-icon"></span>Add Media</button>'
                        +'</div>';
                        if(data.msg == 'success'){
                            jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').prepend(outputhtml);
                            jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').addClass('hidden');
                            jQuery('body').find('.single_sub_section.add_video').next('.external_lession_media').removeClass('hidden'); 
                        }
                        
                    }
                );
            });

            jQuery(document).on('click', '.e-tab.active button[data-id="'+$this.itemData.id+'"]#insert-media-button.insert-media_cus', function(e){
                // jQuery(this).unbind();
                
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
                            // console.log(attachment);
                            // jQuery('textarea#lesson_media_url').val(attachment.url);
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
                                field_value: attachment,
                                field_name: '_lp_lesson_video_intro_internal',
                                item_id:  $('ul.e-section-content > li.e-section-item.e-selected').data('id'),
                                user_id: userSettings.uid,
                            },
                            function(data_return){
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

                                if(data.msg == 'success'){
                                    jQuery('body').find('.single_sub_section.add_video').next('.external_lession_media').addClass('hidden');
                                    jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').append(outputhtml);
                                    // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').removeClass('hidden');
                                    jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.wp-media-buttons').addClass('hidden');
                                    // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('img[data-name="icon"]').attr('src', attachment.icon);
                                    // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.file-info p:nth-child(1)').text(attachment.title);
                                    // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.file-info p:nth-child(2)').find('a').text(attachment.filename).attr('href', attachment.url);
                                    // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.file-info p:nth-child(3)').find('span').text(attachment.filesizeHumanReadable);
                                    // jQuery('body').find('div[data-id="'+$this.itemData.id+'"]#lession_Int_media').find('.acf-field').find('div.acf-actions').find('a').data('id', lession_id);
                                    var editor = tinymce.get('e-item-content');
                                    editor.setContent('[video width="1920" height="1080" mp4="'+attachment.url+'"][/video]' + editor.getContent());
                                }
                                
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


    Vue.component('e-form-field-hidden', {
        template: '#tmpl-e-form-field-hidden',
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
                    height: 300,
                    plugins: "media",
                    menubar: "insert",
                    toolbar: "media",
                    video_template_callback: function(data) {
                    return '<video width="' + data.width + '" height="' + data.height + '"' + (data.poster ? ' poster="' + data.poster + '"' : '') + ' controls="controls">\n' + '<source src="' + data.source1 + '"' + (data.source1mime ? ' type="' + data.source1mime + '"' : '') + ' />\n' + (data.source2 ? '<source src="' + data.source2 + '"' + (data.source2mime ? ' type="' + data.source2mime + '"' : '') + ' />\n' : '') + '</video>';
                    }
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