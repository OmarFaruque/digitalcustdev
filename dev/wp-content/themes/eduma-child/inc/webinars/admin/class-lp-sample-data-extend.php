<?php 
if(!class_exists('LP_Install_Sample_Data_Extend')){
    class LP_Install_Sample_Data_Extend extends LP_Install_Sample_Data{
    public $dummy_text = '';
    public function __construct()
    {
		parent::__construct();
		if ( $dummy_text = @file_get_contents( LP_PLUGIN_PATH . '/dummy-data/dummy-text.txt' ) ) {
			$this->dummy_text = preg_split( '!\s!', $dummy_text );
		}
	}
	

    public function create_sectionscustom($course_id){
		$section_length = 1;
		$return = false;
        for ( $i = 1; $i <= $section_length; $i ++ ) {
			$section_id = $this->create_section( 'Section ' . $i, $course_id );
			if ( $section_id ) {
				$this->create_section_items( $section_id, $course_id );
				return true;
			}
		}
		return $return;
    }





	/**
	 * Create section items.
	 *
	 * @param int $section_id
	 * @param int $course_id
	 */
	protected function create_section_items( $section_id, $course_id ) {

		static $lesson_count = 1;
		static $quiz_count = 1;

		$item_length = 1;
		for ( $i = 1; $i <= $item_length; $i ++ ) {
			$lesson_id = $this->create_lesson( 'Lesson ' . $lesson_count ++, $section_id, $course_id );
			// echo 'lession id: ' . $lesson_id . '<br/>';
			if ( $lesson_id ) {
				if ( $i == 1 ) {
					update_post_meta( $lesson_id, '_lp_preview', 'yes' );
				}
			}
		}
		// $this->create_quiz( 'Quiz ' . $quiz_count, $section_id, $course_id );
	}





    


    } // End Class
}
