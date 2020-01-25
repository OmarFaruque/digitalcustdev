<?php


class LPE_Profile_Menu extends WP_Widget {
	// class constructor
	public function __construct() {
		
		$widget_ops = array( 
			'classname' => 'lpe_profile_menu',
			'description' => "This is a profile menu widget for the learnpress"
		);
		
		parent::__construct( 'lpe_profile_menu', 'LPE Profile Menu', $widget_ops );
	}
	
	// output the widget content on the front-end
	public function widget( $args, $instance ) 
	{
		//echo $args['before_widget'];
		
		$current_user = wp_get_current_user();

		if ($current_user->ID > 0)
		{
				global $post;
		
				
				$user_info = get_userdata($current_user->ID);

				$role = $user_info->roles;
				$getmetas = get_user_meta( get_current_user_id() , 'lp_switch_view', true );
				$insructorClass = ($getmetas == 'instructor')? 'active':'';
				$studentClass = ($getmetas == 'student')? 'active':'';
				if (in_array('administrator', $role) || in_array('lp_teacher', $role))
				{
					echo "
						<div class='switch_menu'>
							<a class='".$insructorClass."' href='".get_the_permalink( $post->ID )."?switch=instructor'>".$instance['pm_instructor_label']."</a> | 
							<a class='".$studentClass."' href='".get_the_permalink( $post->ID )."?switch=student'>".$instance['pm_student_label']."</a> | 
							<a href='".wp_logout_url(get_site_url())."'>".$instance['pm_logout_label']."</a>
						</div>
					";
				}
				else
				{
					echo "
						<div class=''>
							<a href='".get_the_permalink( $post->ID )."'>".$instance['pm_profile_label']."</a> | 
							<a href='".wp_logout_url(get_site_url())."'>".$instance['pm_logout_label']."</a>
						</div>
					";
				}
				
				

			
			
		}
		else
		{
			echo "
				<div class=''>
					<a href='".wp_login_url(get_site_url())."'>Login</a>
				</div>
			";
		}
		
		
		
		
		
		//echo $args['after_widget'];
	}

	// output the option form field in admin Widgets screen
	public function form( $instance ) 
	{
		//$main_title = ! empty( $instance['fq_main_title'] ) ? $instance['fq_main_title'] : '';
		$student_label = ! empty( $instance['pm_student_label'] ) ? $instance['pm_student_label'] : '';
		
		$instructor_label = ! empty( $instance['pm_instructor_label'] ) ? $instance['pm_instructor_label'] : '';
		
		$profile_label = ! empty( $instance['pm_profile_label'] ) ? $instance['pm_profile_label'] : '';
		
		$logout_label = ! empty( $instance['pm_logout_label'] ) ? $instance['pm_logout_label'] : '';
		
		$post = ! empty( $instance['pm_post'] ) ? unserialize($instance['pm_post']) : array();
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pm_student_label' ) ); ?>">
			<?php esc_attr_e( 'Student Label:', 'text_domain' );?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pm_student_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pm_student_label' ) ); ?>" value="<?php echo esc_attr( $student_label ); ?>" /> 
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pm_instructor_label' ) ); ?>">
			<?php esc_attr_e( 'Instructor Label:', 'text_domain' );?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pm_instructor_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pm_instructor_label' ) ); ?>" value="<?php echo esc_attr( $instructor_label ); ?>" /> 
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pm_profile_label' ) ); ?>">
			<?php esc_attr_e( 'Profile Label:', 'text_domain' );?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pm_profile_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pm_profile_label' ) ); ?>" value="<?php echo esc_attr( $profile_label ); ?>" /> 
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pm_logout_label' ) ); ?>">
			<?php esc_attr_e( 'Logout Label:', 'text_domain' );?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pm_logout_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pm_logout_label' ) ); ?>" value="<?php echo esc_attr( $logout_label ); ?>" /> 
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pm_post' ) ); ?>">
			<?php esc_attr_e( 'Page to Display:', 'text_domain' );?>
			</label> 
			<div style="width: 100%; height: 200px; overflow-y: scroll;">
			<?php
				$posts = get_posts(array('numberposts' => -1, 'post_type' => 'page'));
			 
				foreach( $posts as $pt ) 
				{
					if ( in_array( $pt->ID, $post) )
					{
						?>
						<p><input class="widefat" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'pm_post' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pm_post' ) ); ?>[]" value="<?php echo $pt->ID;?>" checked /> <?php echo get_the_title($pt);?></p>
						<?php
					}
					else
					{
						?>
						<p><input class="widefat" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'pm_post' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pm_post' ) ); ?>[]" value="<?php echo $pt->ID;?>" /> <?php echo get_the_title($pt);?></p>
						<?php
					}
					
					
					
				} 
			?>
			</div>
		</p>
		<?php
		//echo esc_attr( $category );
	}

	// save options
	public function update( $new_instance, $old_instance ) 
	{
		$instance = array();
		//$instance['fq_main_title'] = ( ! empty( $new_instance['fq_main_title'] ) ) ? strip_tags( $new_instance['fq_main_title'] ) : '';
		$instance['pm_student_label'] = ( ! empty( $new_instance['pm_student_label'] ) ) ? strip_tags( $new_instance['pm_student_label'] ) : '';
		
		$instance['pm_instructor_label'] = ( ! empty( $new_instance['pm_instructor_label'] ) ) ? strip_tags( $new_instance['pm_instructor_label'] ) : '';
		
		$instance['pm_profile_label'] = ( ! empty( $new_instance['pm_profile_label'] ) ) ? strip_tags( $new_instance['pm_profile_label'] ) : '';
		
		$instance['pm_logout_label'] = ( ! empty( $new_instance['pm_logout_label'] ) ) ? strip_tags( $new_instance['pm_logout_label'] ) : '';
		$instance['pm_post'] = ( ! empty( $new_instance['pm_post'] ) ) ? serialize( $new_instance['pm_post'] ) : '';
		
		return  $instance;
	}

}


// register LPE_Profile_Menu
add_action( 'widgets_init', function(){
	register_widget( 'LPE_Profile_Menu' );
});

