<?php
/**
 * Template for displaying form for editing a post type.
 *
 * @author  ThimPress
 * @package LearnPress/Frontend-Editor/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;

global $frontend_editor;
$post_manage  = $frontend_editor->post_manage;
$post         = $post_manage->get_post();
$skill_level  = get_post_meta( $post->ID, 'thim_course_skill_level', true );
$course_lang  = get_post_meta( $post->ID, 'thim_course_language', true );
$youtube_link = get_post_meta( $post->ID, 'thim_course_media_intro', true );
?>
    <div class="row">
    <div class="e-form-field col-md-12">
        <label><?php esc_html_e( 'Title', 'learnpress-frontend-editor' ); ?></label>
        <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
        <div class="e-form-field-input">
            <input name="post_title" class="frontend-post-title om3" maxlength="100" type="text" value="<?php echo $post->post_title; ?>"
                   placeholder="<?php esc_attr_e( 'Course name', 'learnpress-frontend-editor' ); ?>">
                   
        </div>
    </div>

    <div class="e-form-field col-md-4 mt-2">
        <label><?php esc_html_e( 'Category', 'learnpress-frontend-editor' ); ?></label>
        <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
        <div class="e-form-field-input">

			<?php #$frontend_editor->get_template( 'edit/form-category' ); ?>

			<?php
			$categories = get_terms( array(
				'taxonomy'   => 'course_category',
				'hide_empty' => false,
			) );

			if ( ! empty( $categories ) ) {
				$course_selected_cats = get_the_terms( $post->ID, 'course_category' );
				if ( ! empty( $course_selected_cats ) ) {
					$selected_tags = array();
					foreach ( $course_selected_cats as $course_selected_cat ) {
						$selected_cats[] = $course_selected_cat->term_id;
					}
				}
				?>
                <select class="select2-select" name="course_category[]" multiple="multiple">
					<?php foreach ( $categories as $cat ) { ?>
                        <option value="<?php echo $cat->term_id; ?>" <?php echo ! empty( $selected_cats ) && in_array( $cat->term_id, $selected_cats ) ? 'selected' : false; ?>><?php echo $cat->name; ?></option>
					<?php } ?>
                </select>
			<?php } ?>
        </div>
    </div>

    <div class="e-form-field col-md-4 mt-2">
        <label><?php esc_html_e( 'Select Language', 'learnpress-frontend-editor' ); ?></label>
        <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
        <div class="e-form-field-input">
			<?php $languages = json_decode( file_get_contents( DIGITALCUSTDEV_PLUGIN_PATH . 'assets/languages.json' ) ); ?>
            <select class="form-control select2-select" name="post_language">
				<?php
				foreach ( $languages as $language ) {
					$selected = ! empty( $course_lang ) && $course_lang === $language->name ? 'selected' : false;
					?>
                    <option <?php echo $selected; ?> value="<?php echo $language->name; ?>"><?php echo $language->name; ?></option>
				<?php } ?>
            </select>
        </div>
    </div>

    <div class="e-form-field col-md-4 mt-2">
        <label><?php esc_html_e( 'Select Skill Level', 'learnpress-frontend-editor' ); ?></label>
        <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
        <div class="e-form-field-input">
            <select class="form-control select2-select" name="post_skill_level">
                <option value="Beginner" <?php selected( $skill_level, 'Beginner' ); ?>><?php _e( 'Master', 'digitalcustdev-core' ); ?></option>
                <option value="Medium" <?php selected( $skill_level, 'Medium' ); ?>><?php _e( 'Intermediate', 'digitalcustdev-core' ); ?></option>
                <option value="Expert" <?php selected( $skill_level, 'Expert' ); ?>><?php _e( 'Advanced', 'digitalcustdev-core' ); ?></option>
            </select>
        </div>
    </div>

    <div class="e-form-field col-md-12 mt-2">
        <label><?php esc_html_e( 'Create tag for the course or choose one from the list - its easier for users to find your course.', 'learnpress-frontend-editor' ); ?></label>
        <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
        <div class="e-form-field-dropdown">
			<?php
			$tags = get_terms( array(
				'taxonomy'   => 'course_tag',
				'hide_empty' => false,
			) );

			if ( ! empty( $tags ) ) {
				$course_selected_tags = get_the_terms( $post->ID, 'course_tag' );
				if ( ! empty( $course_selected_tags ) ) {
					$selected_tags = array();
					foreach ( $course_selected_tags as $course_selected_tag ) {
						$selected_tags[] = $course_selected_tag->term_id;
					}
				}
				?>
                <select class="learnpress-tags" name="course_tags[]" multiple="multiple">
					<?php foreach ( $tags as $tag ) { ?>
                        <option value="<?php echo $tag->name; ?>" <?php echo ! empty( $selected_tags ) && in_array( $tag->term_id, $selected_tags ) ? 'selected' : false; ?>><?php echo $tag->name; ?></option>
					<?php } ?>
                </select>
			<?php } ?>
        </div>
    </div>

    <div class="e-form-field col-md-4 mt-2">
		<?php if ( post_type_supports( $post_manage->get_post_type(), 'thumbnail' ) ) { ?>
            <label><?php esc_html_e( 'Add a course Image', 'learnpress-frontend-editor' ); ?></label>
            <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
            <div class="e-form-field-input">
				<?php $frontend_editor->get_template( 'edit/form-image' ); ?>
            </div>
		<?php } ?>
    </div>
    <div class="e-form-field col-md-4 mt-2">
        <div class="e-form-field-input">
			<?php
			$new_post = array(
				'post_id'            => $post->ID,
				'field_groups'       => array( 'group_5d526229700d0' ),
				'form'               => false,
				'html_before_fields' => '',
				'html_after_fields'  => '',
				'id'                 => '_lp_course_featured_video'
			);
			acf_form( $new_post );
			?>
        </div>

        <div class="e-form-field-input  hide-youtube-link-field">
            <label class="mt-2"><?php esc_html_e( 'Paste a link from youtube', 'learnpress-frontend-editor' ); ?></label>
            <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
            <input class="form-control thim_course_media_intro" name="thim_course_media_intro" type="url" value="<?php echo ! empty( $youtube_link ) ? $youtube_link : false; ?>" placeholder="<?php esc_attr_e( 'Paste youtube link', 'learnpress-frontend-editor' ); ?>">
            <div class="course_youtube_link_incorrect"></div>
        </div>
    </div>
    <div class="col-md-4 mt-2 video-intro-msg">
        <div class="message message-success mt-1 mb-1" role="alert">
            <p class="mb-1"><?php _e( 'Record max. 2 minutes video-intro for your webinar course.', 'digitalcustdev-core' ); ?></p>
            <p class="mb-0"><?php _e( 'It will be your business card presenting your course. Also, we\'ll use this video to advertise your course across community.', 'digitalcustdev-core' ); ?></p>
        </div>
    </div>

    <div class="e-form-field col-md-12 mt-2">
        <label><?php esc_html_e( 'Description', 'learnpress-frontend-editor' ); ?></label>
        <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
        <div class="e-form-field-input">
			<?php wp_editor( $post->post_content, 'post_content', array( 'rows' => 50 ) ); ?>
        </div>
    </div>

</div>