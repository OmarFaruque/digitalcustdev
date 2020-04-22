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
            // console.log(this);
            var $this = this;
            
        },
        watch: function(){
            this.push('omarF');
            console.log('this is watch');
        },
        mounted: function () {
            var $this = this;
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
        beforeCreate: function (){
            var self = this;
            var lession_id = jQuery('ul.e-course-sections li.e-section-item.e-sort-item.lp_lesson.e-selected').data('id');
            // console.log(self);
            $.post(
                _wpUtilSettings.ajax,
                {
                    action: 'fource_get_lesson_attachment_for_vue',
                    dataType: 'json',
                    lession_id: lession_id,
                },
                function (data){
                    // console.log(data);
                    if(data.msg == 'success'){
                        self.itemData.settings['_lp_lesson_video_intro_internal'] = (data.meta != 'undefined') ? data.meta : '';   
                        // self.itemData.content = data.post_content;
                    }else{
                        self.itemData.settings['_lp_lesson_video_intro_internal'] = '';   
                    }
                }
            );
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
            // this.itemData.settings['_lp_lesson_video_intro_internal'] = JSON.parse(this.itemData.settings['_lp_lesson_video_intro_internal']);
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


    // Need update
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
            },
        },
        created: function () {
        },
        mounted: function () {
            var selectDropDown = $(this.$el).find('select').select2({
                width: '100%'
            });
            selectDropDown.on('select2:select', function (e) {
                var event = new Event('change');
                e.target.dispatchEvent(event);
            });
        },
        methods: {
            /**
             * Update item settings to DB
             */
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
        // beforeCreate: function (){
        //     var self = this;
        //     var lession_id = jQuery('ul.e-course-sections li.e-section-item.e-sort-item.lp_lesson.e-selected').data('id');
        //     console.log(self);
        //     $.post(
        //         _wpUtilSettings.ajax,
        //         {
        //             action: 'fource_get_lesson_content_value',
        //             dataType: 'json',
        //             lession_id: lession_id,
        //         },
        //         function (data){
        //             if(data){
        //                 console.log('ajax content sucess');
        //                 self.content = data.post_content;   
        //             }
        //         }
        //     );
        // },
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
            // console.log(this.id);
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
                    // plugins: "media",
                    // menubar: "insert",
                    // toolbar: "media",
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
                // console.log('on change');
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