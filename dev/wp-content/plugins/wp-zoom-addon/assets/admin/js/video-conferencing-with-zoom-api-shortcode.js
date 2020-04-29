jQuery(function ($) {
    // Register buttons
    tinymce.create('tinymce.plugins.zvcShortcodes', {
        _assetsUrl: function (url) {
            var aUrl, i, l, sUrl = '';
            aUrl = url.split('/');
            for (i = 0, l = aUrl.length - 1; i < l; i++) {
                sUrl += aUrl[i] + '/';
            }

            return sUrl;
        },
        _imgUrl: function (url) {
            return this._assetsUrl(url) + 'images/video_logo.png';
        },
        init: function (editor, url) {
            // Add button that inserts shortcode into the current position of the editor
            editor.addButton('zvc_shortcode_button', {
                title: 'Add Zoom Meetings (Clicking will take some time to load data). Please be patient.',
                image: this._imgUrl(url),
                width: 500,
                height: 600,
                onclick: function () {
                    var send_data = {do: "listUsers", action: "zvc_get_users_tinymce"};
                    var response_arr = [];
                    var meetings_arr = [];
                    $.post(ajaxurl, send_data).done(function (response) {
                        $.each(response, function (i, v) {
                            response_arr.push({
                                text: v.first_name + ' ' + v.last_name + ' (' + v.email + ')',
                                value: v.id
                            });
                        });

                        // Open a TinyMCE modal
                        editor.windowManager.open({
                            title: 'Choose a User to View Meetings',
                            body: [{
                                type: 'listbox',
                                name: 'zoom_users',
                                label: 'Choose a User',
                                values: response_arr,
                                tooltip: 'Select a User'
                            }],
                            onsubmit: function (e) {
                                //Store HosID
                                var host_id = e.data.zoom_users;

                                //Now Get Meetings List
                                var send_data = {do: "getMeetings", host_id: host_id, action: "zvc_get_users_tinymce"};
                                $.post(ajaxurl, send_data).done(function (meetings_response) {
                                    $.each(meetings_response, function (i, v) {
                                        meetings_arr.push({
                                            text: v.topic,
                                            value: v.id
                                        });
                                    });

                                    if (meetings_arr.length != 0) {
                                        editor.windowManager.open({
                                            title: 'Select a Meeting ID to Insert',
                                            body: [{
                                                type: 'listbox',
                                                name: 'zoom_meeting_list',
                                                label: 'Select a Meeting ID',
                                                values: meetings_arr,
                                            },
                                                {
                                                    type: 'textbox',
                                                    name: 'zoom_link_label',
                                                    label: 'Link Label',
                                                    value: '',
                                                    tooltip: 'Label of the Link'
                                                },
                                                {
                                                    type: 'textbox',
                                                    name: 'zoom_link_id',
                                                    label: 'ID for the Link',
                                                    value: '',
                                                    tooltip: 'Leave blank for none'
                                                },
                                                {
                                                    type: 'textbox',
                                                    name: 'zoom_link_class',
                                                    label: 'Class for the Link',
                                                    value: '',
                                                    tooltip: 'Leave blank for none'
                                                },
                                                {
                                                    type: 'listbox',
                                                    name: 'zoom_meeting_link_target',
                                                    label: 'Link Targe',
                                                    values: [{
                                                        text: "Self",
                                                        value: "_self"
                                                    },
                                                        {
                                                            text: "New Window",
                                                            value: "_blank"
                                                        }],
                                                    tooltip: 'Open Link in a new browser tab or open in same tab.'
                                                }],
                                            onsubmit: function (v) {
                                                var insert_string = '[zoom_api_link meeting_id="' + v.data.zoom_meeting_list + '"';
                                                insert_string += v.data.zoom_link_class ? ' class="' + v.data.zoom_link_class + '"' : "";
                                                insert_string += v.data.zoom_link_id ? ' id="' + v.data.zoom_link_id + '"' : "";
                                                insert_string += v.data.zoom_meeting_link_target ? ' target="' + v.data.zoom_meeting_link_target + '"' : "";
                                                insert_string += v.data.zoom_link_label ? ' title="' + v.data.zoom_link_label + '"' : "";
                                                insert_string += ']';
                                                editor.insertContent(insert_string);
                                            }
                                        });
                                    } else {
                                        alert("No Meetings Found for this User. Select other User !");
                                    }
                                });
                            }
                        });
                    });
                }
            });
        },
        createControl: function (n, cm) {
            return null;
        }
    });
    // Add buttons
    tinymce.PluginManager.add('zvc_shortcode_button', tinymce.plugins.zvcShortcodes);
});
