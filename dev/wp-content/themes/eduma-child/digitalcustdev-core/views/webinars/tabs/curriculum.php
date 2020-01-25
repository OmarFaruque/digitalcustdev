<div class="col-sm-12">
	<?php
	$course_id   = isset( $_GET['id'] ) ? $_GET['id'] : false;
	$curriculums = e_get_course_editor_sections( $course_id );
	//	dump( $curriculums );
	if ( ! empty( $course_id ) ) {
		?>
        <div class="webinar-course-curriculum webinar-main-accordion-list">
			<?php
			if ( ! empty( $curriculums ) ) {
				foreach ( $curriculums as $curriculum ) {
					$lessons         = 0;
					$quizes          = 0;
					$assignments     = 0;
					$course_duration = get_post_meta( $curriculum['course_id'], '_lp_duration', true );
					?>
                    <div class="accordion-content-wrap">
                        <input type="hidden" name="hidden_section_name[<?php echo $curriculum['id'] ?>]" value="<?php echo $curriculum['title']; ?>">
                        <input type="hidden" name="hidden_section_description[<?php echo $curriculum['id'] ?>]" value="<?php echo $curriculum['description']; ?>">
                        <div class="accordion-head">
                            <i class="fa fa-arrow-right"></i> <?php echo $curriculum['title']; ?>
                            <div class="section-editing-wrapper pull-right">
                                <a href="#open-section-edit-popup-<?php echo $curriculum['id'] ?>" class="magnific-popup-webinar"><i class="fa fa-pencil"></i></a>
								<?php require DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tabs/curriculum/edit-section.php'; ?>
                                &nbsp;<a href="javascript:void(0);" data-courseid="<?php echo $course_id; ?>" data-sectionid="<?php echo $curriculum['id']; ?>" class="webinar-delete-section"><i class="fa fa-trash"></i></a>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="accordion-content">
                            <div class="webinar-section-description">
								<?php echo apply_filters( 'the_content', $curriculum['description'] ); ?>
                            </div>
							<?php
							if ( ! empty( $curriculum['items'] ) ) {
								foreach ( $curriculum['items'] as $item ) {
									if ( $item['type'] === "lp_lesson" ) {
										$lessons ++;

									}

									if ( $item['type'] === "lp_quiz" ) {
										$quizes ++;
									}

									if ( $item['type'] === "lp_assignment" ) {
										$assignments ++;
									}
								}
							}
							?>
                            <div class="row webinar-section-description-chunks">
                                <div class="col-md-3"><?php echo $lessons; ?> Webinar Lessons</div>
                                <div class="col-md-3"><?php echo $quizes; ?> Quizzes</div>
                                <div class="col-md-3"><?php echo $assignments; ?> Assignments</div>
                                <div class="col-md-3">0 Downloads</div>
                                <div class="col-md-12">
                                    <div class="webinar-section-description-links"><a href="#open-new-create-lesson-popup-<?php echo $curriculum['id'] ?>" class="magnific-popup-webinar">Lesson</a> | <a href="#open-new-create-quiz-popup-<?php echo $curriculum['id'] ?>" class="magnific-popup-webinar">Quiz</a> | <a href="#open-new-create-assignment-popup-<?php echo $curriculum['id'] ?>" class="magnific-popup-webinar">Asssignments</a> | Downloads</div>
                                </div>
                                <div class="clear"></div>
                                <div class="col-md-12">
                                    <div class="webinar-course-curriculum webinar-course-curriculum-content-accordion">
										<?php
										if ( ! empty( $curriculum['items'] ) ) {
											foreach ( $curriculum['items'] as $item ) {
												?>
                                                <div class="accordion-content-wrap">
                                                    <div class="accordion-head">
														<?php echo ! empty( dcd_core_get_curriculum_type_icons( $item['type'] ) ) ? dcd_core_get_curriculum_type_icons( $item['type'] ) : false; ?>&nbsp;<?php echo $item['title']; ?>
                                                        <div class="pull-right">
															<?php if ( $item['type'] === "lp_lesson" ) { ?>
                                                                <a href="#open-lesson-edit-popup-<?php echo $item['id']; ?>" class="magnific-popup-webinar"><i class="fa fa-pencil"></i></a>&nbsp;<a href="javascript:void(0);" class="webinar-delete-lesson"><i class="fa fa-trash"></i></a>
																<?php require DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tabs/curriculum/edit-lesson.php'; ?>
															<?php } else if ( $item['type'] === "lp_assignment" ) { ?>
                                                                <a href="#open-assignment-edit-popup-<?php echo $item['id']; ?>" class="magnific-popup-webinar"><i class="fa fa-pencil"></i></a>&nbsp;<a href="javascript:void(0);" class="webinar-delete-assignment"><i class="fa fa-trash"></i></a>
																<?php require DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tabs/curriculum/edit-assignment.php'; ?>
															<?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-content">
														<?php echo $item['content']; ?>
                                                    </div>
                                                </div>
												<?php
											}
										} ?>
                                    </div>
                                </div>

								<?php require DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tabs/curriculum/create-new-lesson.php'; ?>
								<?php require DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tabs/curriculum/create-new-quiz.php'; ?>
								<?php require DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tabs/curriculum/create-new-assignment.php'; ?>
                            </div>
                        </div>
                    </div>
					<?php
				}
			}
			?>
        </div>

        <div class="row webinar-course-lessons-quizes-assigments">
			<?php require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tabs/curriculum/create-new-section.php'; ?>
        </div>
		<?php
	} else {
		?>
        <div class="message message-success">Please create this course first in order to add a curriculum.</div>
		<?php
	}
	?>
</div>
