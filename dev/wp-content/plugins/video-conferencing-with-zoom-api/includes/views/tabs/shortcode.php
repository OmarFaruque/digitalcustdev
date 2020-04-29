<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="zvc-row">
    <div class="zvc-position-floater-left">
        <section class="zoom-api-example-section">
            <h3><?php _e( 'Using Shortcode Example', 'video-conferencing-with-zoom-api' ); ?></h3>
            <p><?php _e( 'Below are few examples of how you can add shortcodes manually into your posts.', 'video-conferencing-with-zoom-api' ); ?></p>

            <div class="zoom-api-basic-usage">
                <h3><?php _e( 'Basic Usage', 'video-conferencing-with-zoom-api' ); ?>:</h3>
                <code>[zoom_api_link meeting_id="123456789" link_only="no"]</code>
                <div class="zoom-api-basic-usage-description">
                    <label><?php _e( 'Description', 'video-conferencing-with-zoom-api' ); ?>:</label>
                    <p><?php _e( 'Show a list with meeting details for a specific meeting ID with join links.', 'video-conferencing-with-zoom-api' ); ?></p>
                    <label><?php _e( 'Parameters', 'video-conferencing-with-zoom-api' ); ?>:</label>
                    <ul>
                        <li><strong>meeting_id</strong> : Your meeting ID.</li>
                        <li><strong>link_only</strong> : Yes or No - Adding yes will show join link only. Removing this parameter from shortcode will
                            output description.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="zoom-api-basic-usage" style="margin-top: 20px;border-top:1px solid #ccc;">
                <h3><?php _e( 'Listing Zoom Meetings', 'video-conferencing-with-zoom-api' ); ?>:</h3>
                <code>[zoom_list_meetings per_page="5" category="test,test2,test3" order="ASC" type="upcoming"]</code>
                <div class="zoom-api-basic-usage-description">
                    <label><?php _e( 'Description', 'video-conferencing-with-zoom-api' ); ?>:</label>
                    <p><?php _e( 'Shows a list of meetings with start time, date and link to the meetings page. This is customizable by overriding from your
                        theme folder.', 'video-conferencing-with-zoom-api' ); ?></p>
                    <label><?php _e( 'Parameters', 'video-conferencing-with-zoom-api' ); ?>:</label>
                    <ul>
                        <li><strong>per_page</strong> : Posts per page.</li>
                        <li><strong>category</strong> : Show linked categories.</li>
                        <li><strong>order</strong> : ASC or DESC based on post created time.</li>
                        <li><strong>type</strong> : "upcoming" or "past" - To show only upcoming meeting based on start time (Update to meeting is required for old post type meetings).</li>
                    </ul>
                </div>
            </div>
            <!--<div class="zoom-api-basic-usage" style="margin-top: 20px;border-top:1px solid #ccc;">
                <h3><?php /*_e( 'Embed Zoom Meeting into Browser', 'video-conferencing-with-zoom-api' ); */ ?>:</h3>
                <code>[zoom_join_via_browser meeting_id="1234556789" title="Meeting Title" id="div-class-id" login_required="no" help="yes" height="500px"]</code>
                <div class="zoom-api-basic-usage-description">
                    <label><?php /*_e( 'Description', 'video-conferencing-with-zoom-api' ); */ ?>:</label>
                    <p><?php /*_e( 'Embed a Zoom Meeting into your page or post. This allows you to directly join meeting or start it from the page where you added
                        this shortcode. Remember this is an IFRAME window !', 'video-conferencing-with-zoom-api' ); */ ?></p>
                    <label><?php /*_e( 'Parameters', 'video-conferencing-with-zoom-api' ); */ ?>:</label>
                    <ul>
                        <li><strong>meeting_id</strong> : Meeting ID for the meeting to be joined or started (required).</li>
                        <li><strong>title</strong> : Title of the meeting to be shown in the head. (optional)</li>
                        <li><strong>id</strong> : For styling (optional).</li>
                        <li><strong>login_required</strong> : "yes" or "no" - User required to be logged in to join or view (optional).</li>
                        <li><strong>help</strong> : "yes" or "no" - Additional help text in the header. (optional).</li>
                        <li><strong>height</strong> : in "px" (optional).</li>
                    </ul>
                </div>
            </div>-->
        </section>
    </div>
</div>
