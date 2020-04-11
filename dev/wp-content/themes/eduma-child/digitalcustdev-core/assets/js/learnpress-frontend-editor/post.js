;(function ($) {

    /**
     * Init checkboxes action in column checkbox of post list
     */
    var initColumnCB = function () {
        var $chkAll = $('#cb-select-all-1, #cb-select-all-2'),
            $chks = $('.check-column').find('input').not($chkAll);

        $chkAll.on('change', function () {
            $chks.prop('checked', this.checked);
            $chkAll.not(this).prop('checked', this.checked);
            toggleActions();
        });

        $chks.on('change', function () {
            var isCheckedAll = $chks.filter(':checked').length === $chks.length;
            if (isCheckedAll) {
                $chkAll.prop('checked', true);
            } else {
                $chkAll.prop('checked', false);
            }
            toggleActions();
        });

        var toggleActions = function () {
            if ($chks.filter(':checked').length) {
                $('.e-table-actions .move-to-trash').removeClass('e-hidden');
                $('.e-table-actions .go-to-trash').addClass('e-hidden');
            } else {
                $('.e-table-actions .move-to-trash').addClass('e-hidden');
                $('.e-table-actions .go-to-trash').removeClass('e-hidden');
            }
        }
    };

    /**
     * Even handler for button to editing post slug
     */
    function editSlugBox() {
        console.log('omar test');
        var $originSlug = $('#editable-post-name-full'),
            $edit = $('.edit-slug'),
            $save = $('#e-button-save-slug'),
            $cancel = $('#e-button-cancel-slug'),
            $samplePermalink = $('#sample-permalink'),
            $samplePermalinkEditable = $('#e-sample-permalink-editable'),
            originSlug = $originSlug.text();

        $edit.hide();
        $save.show();
        $cancel.show();
        $samplePermalink.hide();
        $samplePermalinkEditable.show().children('input').val(originSlug);

        $save.off('click').on('click', function () {
            console.log('clicked');
            var new_slug = $samplePermalinkEditable.children('input').val();

            if (new_slug == originSlug) {
                $cancel.click();
                return;
            }

            $.post(
                _wpUtilSettings.ajax,
                {
                    action: 'sample-permalink',
                    post_id: $('input[name="post_ID"]').val(),
                    new_slug: new_slug,
                    new_title: $('input[name="post_title"]').val(),
                    e_post: 1,
                    samplepermalinknonce: $('#samplepermalinknonce').val()
                },
                function (data) {
                    var $html = $(data);
                    $html.find('.edit-slug').addClass('e-button');
                    $('#e-wp-sample-permalink').html($html);

                    $edit.show();
                    $save.hide();
                    $cancel.hide();
                    $samplePermalink.show();
                    $samplePermalinkEditable.hide();
                    $('#post_name').val(new_slug);
                }
            );

        });

        $cancel.off('click').on('click', function () {
            $edit.show();
            $save.hide();
            $cancel.hide();
            $samplePermalink.show();
            $samplePermalinkEditable.hide();
        });

        $samplePermalinkEditable.children('input').off('keydown.update-slug').on('keydown.update-slug', function (e) {
            switch (e.keyCode) {
                case 13:
                    e.preventDefault();
                    $save.trigger('click');
                    break;
                case 27:
                    e.preventDefault();
                    $cancel.trigger('click');
            }
        }).focus();
    }

    function createNewPost(e) {
        console.log('omar test of');
        e.preventDefault();
        $(e.target).closest('a').addClass('disabled');
        $.ajax({
            url: lpFrontendCourseEditorSettings.rootURL + '?lp-ajax=create-new-post',
            data: $(this).data(),
            success: function (r) {
                r = LP.parseJSON(r);
                if (r.redirect) {
                    window.location.href = r.redirect;
                }
            }
        })
    }

    function watchChangePostData() {
        var $form = $('#e-edit-post'),
            data = $form.serialize(),
            changed = false;

        // setInterval(function () {
        //     window.onbeforeunload = $form.serialize() != data ? function () {
        //         return true;
        //     } : null;
        // }, 1000);
        //
        // $form.on('submit', function () {
        //     window.onbeforeunload = null;
        // })
    }

    function removeMessageFromUrl() {
        LP.setUrl(window.location.href.removeQueryVar('updated'))
    }

    $(document).on('ready', function () {


        initColumnCB();
        watchChangePostData();
        removeMessageFromUrl();
    }).on('click', '.e-post-attachment .set-attachment', function (event) {
        console.log('test console');
        event.preventDefault();
        event.stopPropagation();



        /*
        * Check folder size
        */
    //    action: 'sample-permalink',
    
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
                var imgContainer = $('.post-attachment');
                var hidden_id = $('#_thumbnail_id');
                var post_attachment_wrapper = $('.e-post-attachment');
        
                // When an image is selected in the media frame...
                wp.media.featuredImage.frame().on('library:selection:add', function (e) {


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
                            var attachment1 = wp.media.featuredImage.frame().state().get('selection').first().toJSON();
                            var totalsize_with_folder = data.titalsize + attachment1.size;
                            var mb = Math.round(totalsize_with_folder / 1048576);
                            if(mb >= 2000){
                                $( "div.media-frame-content" ).prepend('<div class="upload_limit_error"><h6><span class="crose_limit">'+data.display_msg+'</span></h6></div>');
                                $( 'div.media-router > button:first-child, .media-toolbar-primary.search-form button' ).hide();
                            }
                        }
                    );

                    /*
                    HIde other type
                    */
                   

                });
                wp.media.featuredImage.frame().on('select', function () {
        
                    // Get media attachment details from the frame state
                    var attachment = wp.media.featuredImage.frame().state().get('selection').first().toJSON();
                    
        
                    // Send the attachment URL to our custom image input field.
                    
                    imgContainer.html('<img src="' + attachment.url + '" alt="" style="max-width:100%;"/>');
            
                    post_attachment_wrapper.addClass('has-attachment');
            
                        // Send the attachment id to our hidden input
                    hidden_id.val(attachment.id);
                    hidden_id.trigger("change");
                    

                    // console.log(attachment);
                });
        
                wp.media.featuredImage.frame().open();
                // wp.media.featuredImage.frame().on('all', function (e) {
                //     console.log(e);
                // });
                
                wp.media.featuredImage.frame().on('selection:toggle', function (e) {

                    // var attachmenttest = wp.media.featuredImage.frame().state().get('selection').first();
                    // console.log(attachmenttest);
                    var attachment = wp.media.featuredImage.frame().state().get('selection').first().toJSON();
                      
                        if(attachment.mime){
                            var allowList = ['image/jpeg', 'image/png'];
                            
                            if(jQuery.inArray(attachment.mime, allowList) === -1){
                                console.log(attachment);      
                                var error = '<div class="acf-selection-error"><span class="selection-error-label">Restricted</span><span class="selection-error-filename">'+attachment.filename+'</span><span class="selection-error-message">File type must be jpeg or png.</span></div>';
                                jQuery('body').find('.media-frame-content, .media-frame-toolbar').find('.media-toolbar-primary.search-form').find('button.media-button-select').addClass('disabled');
                                jQuery('body').find('.media-frame-content, .media-frame-toolbar').find('.media-toolbar-primary.search-form > button.media-button-select').prop('disabled', true);
                                jQuery('body').find('.media-frame-content').find('.media-sidebar').html(error);
                                // jQuery('body').find('.media-frame-content').find('button.button-link.delete-attachment').trigger('click');
                            }
                        }
                    
                });
                wp.media.featuredImage.frame().on('library:selection:add', function (e) {
                    wp.Uploader.queue.on('reset', function(e) { 
                        var attachment = wp.media.featuredImage.frame().state().get('selection').first().toJSON();
                        if(attachment.mime){
                            var allowList = ['image/jpeg', 'image/png'];
                            if(jQuery.inArray(attachment.mime, allowList) === -1){
                                var error = '<div class="acf-selection-error"><span class="selection-error-label">Restricted</span><span class="selection-error-filename">'+attachment.filename+'</span><span class="selection-error-message">File type must be jpeg or png.</span></div>';
                                jQuery('body').find('.media-frame-content, .media-frame-toolbar').find('.media-toolbar-primary.search-form > button.media-button-select').addClass('disabled');
                                jQuery('body').find('.media-frame-content, .media-frame-toolbar').find('.media-toolbar-primary.search-form > button.media-button-select').prop('disabled', true);
                                jQuery('body').find('.media-frame-content').find('.media-sidebar').html(error);
                                // jQuery('body').find('.media-frame-content').find('button.button-link.delete-attachment').trigger('click');
                            }
                        }
                    });
                    window.confirm = function (e){
                        return true;
                    };
                });
                // wp.media.featuredImage.frame().on('uploader:ready', function (e) {
                //     // console.log('upload ready');
                //     jQuery('body').addClass('only-show-img-in-media');
                    
                    
                // });
                
                var totalsize_with_folder = data.titalsize;
                var mb = Math.round(totalsize_with_folder / 1048576);
                if(mb >= 2000){
                        $( "div.media-frame-content" ).prepend('<div class="upload_limit_error"><h6><span class="crose_limit">'+data.display_msg+'</span></h6></div>');
                        $( 'div.media-router > button:first-child, .media-toolbar-primary.search-form button' ).hide();
                }
                // jQuery('div.media-frame-content')
        }
        ); // End ajax



    }).on('click', '.e-post-attachment .remove-attachment a', function (event) {
        event.preventDefault();
        event.stopPropagation();

        // wp.media.featuredImage.remove();

        var imgContainer = $('.post-attachment');
        var hidden_id = $('#_thumbnail_id');
        var post_attachment_wrapper = $('.e-post-attachment');

        // wp.media.featuredImage.remove();
        // Clear out the preview image
        imgContainer.html('');

        post_attachment_wrapper.removeClass('has-attachment');

        // Delete the image id from the hidden input
        hidden_id.val('');

    }).on('click', 'input[name="_lp_course_result"]', function () {
        var a = $('input[name="_lp_course_result"]:checked')
        if (a.val() === 'evaluate_final_quiz') {

        }
    }).on('mouseup', function (e) {
        $('.anim').removeClass('anim')
    })
        .on('click', '.edit-slug', editSlugBox)
        .on('click', '.e-new-post', createNewPost)

    $(document).ajaxComplete(function (event, request, settings) {
        var m = settings.data ? settings.data.match(/action=get-post-thumbnail-html/) : false;
        if (m) {
            var n = settings.data.match(/thumbnail_id=(-?[0-9]+)/),
                thumbnail_id = n ? parseInt(n[1]) : -1;
            $('.post-attachment')
                .html($(request.responseJSON.data).find('img'))
                .parent()
                .toggleClass('has-attachment', thumbnail_id > 0)
                .find('input[name="_thumbnail_id"]').val(thumbnail_id);
        }
    })

})(jQuery);