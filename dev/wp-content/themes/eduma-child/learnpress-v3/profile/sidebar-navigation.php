<?php
/**
 * Template for displaying user profile tabs.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/profile/tabs.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$profile    = LP_Profile::instance();

// echo 'Profile: <br/><pre>';
// print_r($profile);
// echo '</pre>';


$profile_id = $profile->get_user()->get_id();
$tabs       = $profile->get_tabs()->tabs();
$howdy =  'fsfsf';//$profile->my_username();
$avatar = get_avatar( $profile_id, 60 );
$role = $profile->get_user()->get_role();



$switched_view = get_user_meta($profile_id,"lp_switch_view", true);

$view = (empty($switched_view) ? $role : $switched_view);

if ( ! user_can( $profile_id, 'edit_lp_courses' ) ) {
	unset( $tabs['instructor'] );
}	
?>

<?php //do_action( 'learn-press/before-profile-nav', $profile ); ?>
    <div class="navigation-sidebar-wrapper">
        <!-- <div class="side-nav-area"></div> -->
        <nav style="/*overflow-y: auto;*/" class="nav-tab-main-menu <?php echo is_admin_bar_showing() ? 'nav-tab-main-menu-top-padder' : false; ?>">
            <ul role="tablist" class="<?php echo $view;?>">
                <li class="home">
                    <a href="<?php echo home_url(); ?>"><i class="fa fa-home fa-2x"></i>
                        <span class="text">
                            <?php #do_action( 'learn-press/before-user-profile', $profile ); ?>
                            <?php
                            $thim_logo = get_theme_mod( 'thim_logo', false );
                            $style     = '';
                            if ( ! empty( $thim_logo ) ) {
	                            if ( is_numeric( $thim_logo ) ) {
		                            $logo_attachment = wp_get_attachment_image_src( $thim_logo, 'full' );
		                            if ( $logo_attachment ) {
			                            $src   = $logo_attachment[0];
			                            $style = 'width="' . $logo_attachment[1] . '" height="' . $logo_attachment[2] . '"';
		                            } else {
			                            $src   = get_template_directory_uri() . '/images/logo.png';
			                            $style = 'width="153" height="40"';
		                            }
	                            } else {
		                            $src = $thim_logo;
	                            }
                            } else {
	                            $src   = get_template_directory_uri() . '/images/logo-sticky.png';
	                            $style = 'width="153" height="40"';
                            }

                            $src = thim_ssl_secure_url( $src );
                            echo '<img src="' . $src . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" ' . $style . '>';
                            
                           ?>
                    </span>
                    </a>
                </li>
<!--
               <li id="wp-admin-bar-user-avtar" class="Student Interaction">
-->
               <li class="avatar omar">
					<a class="ab-item" tabindex="-1" href="javascript:void(0);">
						<i class="fa fa-user"></i>
						<span class="text">
							<?php echo $avatar; ?>
							<br/>
							<?php echo $profile->get_user()->get_first_name();?> <?php echo $profile->get_user()->get_last_name();?>
							<?php echo ($role == "instructor" || $role == "admin" ? "Instructor" : "Student");?>
						<span>
					</a>
            </li>
<!--
            <li><span class="username">  </span></li>
            <li><span><?php // echo $howdy ?></span></li>
-->            
				<?php
				$exclude_list = array("orders", "gradebook", "assignment");
				// echo 'profile id: ' . $profile->get_user()->get_id() . '<br/>';
				// require_once LP_ADDON_GRADEBOOK_PLUGIN_PATH . "/inc/functions.php";
				
				// $card = new LP_User_CURD();
				// $courses = $card->get_orders( $profile->get_user()->get_id(), array( 'status' => 'completed' ) );
				// $cours_r = array();
				// foreach($courses as $k => $sk) array_push($cours_r, $k);
				// $gradebook = learn_press_gradebook_get_user_data($profile->get_user()->get_id(), $cours_r);
				

				if ($view == "user" || $view == 'student') 
				{
					// foreach ( $tabs as $tab_key => $tab_data ):
					// 	echo 'tabkey: ' . $tab_key . '<br/>';
					// endforeach;
                ?>
                    <li class="Course dropdown">
                        <!--tabs 1-->
                        <a class="dropbtn" href="" onClick="return false;" data-slug="@">
							<i class="fa fa-book"></i><span class="text"><?php _e('My Courses', 'webinar'); ?></span>  <i class="fa fa-caret-right small"></i>                  
						</a>
						<div class="dropdown-content">
							<a id="studentPurchasedCourseLink" href="<?php echo get_site_url()."/profile/courses/purchased/";?>"><?php _e('Purchased', 'webinar'); ?></a> 
							<?php if ( is_plugin_active( 'learnpress-wishlist/learnpress-wishlist.php' ) ) { ?>
								<a href="<?php echo $profile->get_tab_link( "wishlist", true );?>">Wishlist</a>
							<?php } ?>
						</div>
					</li>
					
                    <li class="Webinars omar">
                        <!--tabs 2-->
                        <a href="<?php echo $profile->get_tab_link( "webinars", true );?>" data-slug="">
							<i class="fa fa-play-circle omar"></i><span class="text"><?php _e('My Webinars', 'webinar'); ?></span>
						</a>
					</li>
					
                    <li class="Events">
                        <!--tabs 3-->
                        <a href="<?php echo $profile->get_tab_link( "events", true );?>" data-slug="">
							<i class="fa fa-calendar"></i><span class="text"><?php _e('My Events', 'webinar'); ?></span>
						</a>
					</li>

					<li class="Mytests">
                        <!--tabs 3-->
                        <a href="<?php echo $profile->get_tab_link( "quizzes", true );?>" data-slug="">
						<i class="fa fa-question-circle"></i><span class="text"><?php _e('Quizzes', 'learnpress'); ?></span>
						</a>
					</li>
					
                    <li class="assignment">
                        <!--tabs 4-->
							<a href="<?php echo $profile->get_tab_link( "assignment", true );?>">
							<i class="fa fa-handshake-o"></i><span class="text"><?php _e('Assignments', 'learnpress'); ?></span>
							</a>
						
					</li>
					
                    <li class="Communication">
                        <!--tabs 5-->
                        <a href="<?php echo $profile->get_tab_link('communication'); ?>" data-slug="">
							<i class="fa fa-comment-o"></i><span class="text"><?php _e('Communication', 'webinar'); ?></span>
						</a>
					</li>
					
                    <li class="Additional dropdown">
                        <!--tabs 6-->
                        <a class="dropbtn" onClick="return false;" href="javascript:void(0);" data-slug="@">
							<i class="fa fa-graduation-cap"></i><span class="text"><?php _e('Additional', 'webinar'); ?></span>  <i class="fa fa-caret-right small"></i>                   
						</a>
						<div class="dropdown-content">
							<a href="<?php echo $profile->get_tab_link('downloads'); ?>">Downloads</a>
							<a href="<?php echo $profile->get_tab_link('orders'); ?>">My Orders</a>
						</div>
					</li>
                    
                    <li class="Profile dropdown">
                        <!--tabs 7-->
                        <a onClick="return false;" class="dropbtn" href="" data-slug="">
							<i class="fa fa-user"></i><span class="text"><?php _e('Profile Settings', 'webinar'); ?></span> <i class="fa fa-caret-right small"></i>
						</a>
						<div class="dropdown-content">
							<a href="<?php echo $profile->get_tab_link('settings'); ?>basic-information/"><?php _e('General', 'webinar'); ?></a>
							<a href="<?php echo $profile->get_tab_link('settings'); ?>avatar/"><?php _e('Avatar', 'webinar'); ?></a>
							<a href="<?php echo $profile->get_tab_link('settings'); ?>change-password/"><?php _e('Password', 'webinar'); ?></a>
						</div>

                    </li>
             

                <?php 
                } 
                elseif($view == "admin" || $view == "instructor") 
                {
					unset($tabs['instructor']);
					unset($tabs['quizzes']);
					unset($tabs['wishlist']);
					// unset($tabs['settings']['sections']['publicity']);
					
					
					foreach ( $tabs as $tab_key => $tab_data ) 
					{
							if ( $tab_data->is_hidden() || ! $tab_data->user_can_view() ) {
								continue;
							}
							
							if (!in_array( $tab_key, $exclude_list))
							{
								$slug        = $profile->get_slug( $tab_data, $tab_key );
								$link        = $profile->get_tab_link( $tab_key, true );
								$tab_classes = array( esc_attr( $tab_key ) );

								if ( $profile->is_current_tab( $tab_key ) ) {
									$tab_classes[] = 'active';
								}
								switch($tab_key){
									case 'students': 
									case 'reports':
									case 'settings':
									?>
										<li class="Student Interaction dropdown">

										<!--tabs 9-->
										<a class="dropbtn" <?php echo (isset($tab_data['sections'])) ? "onClick='return false;'": ""; ?> href="" data-slug="http://localhost/digitalcustdev/profile/user3886106/events/">
										<?php echo apply_filters( 'learn_press_profile_' . $tab_key . '_tab_title', esc_html( $tab_data['title'] ), $tab_key ); ?>
									

											<?php if(isset($tab_data['sections'])): ?>
												<i class="fa fa-caret-right small"></i>
											<?php endif; ?>
										</a>
											<div class="dropdown-content">
											<?php foreach($tab_data['sections'] as $sik => $single_section):
												if($sik == 'publicity') break;
												$link  = ($sik == 'gradebook') ? $profile->get_tab_link( $sik, false ) : $profile->get_tab_link( $tab_key, false );
												$link = ($sik == 'gradebook') ? $link : $link . $sik;
												
												?>
												<?php if ( $sik == 'gradebook/courses' && is_plugin_active( 'learnpress-gradebook/learnpress-gradebook.php' ) ) { ?>
													<a href="<?php echo esc_url( $link ); ?>"><?php echo $single_section['title']; ?></a>
												<?php }else{ ?>
												<a href="<?php echo esc_url( $link ); ?>"><?php echo $single_section['title']; ?>
													<?php if($sik == 'assignments'): ?>
														<span class="countr buble-counter third"><?php echo get_author_assignments_not_set_yet(); ?></span>
													<?php endif; ?>
												</a>

												<?php } endforeach; ?>
											</div>
									</li>
									<?php 
									break;
									default: ?>


								<li class="<?php echo join( ' ', $tab_classes ) ?>">
									<!--tabs 8-->
									<a href="<?php echo esc_url( $link ); ?>" data-slug="<?php echo esc_attr( $link ); ?>">
										<?php echo apply_filters( 'learn_press_profile_' . $tab_key . '_tab_title', esc_html( $tab_data['title'] ), $tab_key ); ?>
									</a>

								</li>
							<?php 
								} // End Switch
							}
					} 
                	?>
                <?php 
                } 
                ?>
            </ul>
        </nav>
    </div>
<?php // do_action( 'learn-press/after-profile-nav', $profile ); ?>
