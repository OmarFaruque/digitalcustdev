<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package thim
 */
?>
	
		<?php
		
		global $wpdb;
		
		$error = '';
		$success = '';
	
		session_start();
		if(!isset($_SESSION['s'])){
		  $_SESSION['s'] = true;
		};	        
	
		if( !empty( $_POST ) && ($_SESSION['s']) ) {
			
		// check if we're in reset form
	        if( isset( $_POST['somfrp_action'] )) 
	        {
	            $email = trim($_POST['user_login']);
	            
				if ( ! wp_verify_nonce( $_POST['somfrp_action'], 'somfrp_action')){
				   $error =  'Извините, проверочные данные не верны.';
				} else if( empty( $email ) ) {
	                $error = 'Введите Email пользователя.';
	            } else if( ! is_email( $email )) {
	                $error = 'Некорректный Email';
	            } else if( ! email_exists( $email ) ) {
	                $error = 'Пользователь с таким Email не зарегистрирован.';
	            } else {
	                
	                $random_password = wp_generate_password( 10, false );
	                $user = get_user_by( 'email', $email );
	                
	                $update_user = wp_update_user( array (
	                        'ID' => $user->ID, 
	                        'user_pass' => $random_password
	                    )
	                );
	                
	                // if  update user return true then lets send user an email containing the new password
	                if( $update_user ) {
	                    $to = $email;
	                    $subject = 'DigitalCustDev - Ваш новый пароль';
	                    $sender = get_option('name');
	                    
						$message  = sprintf(__('Привет, %s'), ucfirst($user->first_name) ? ucfirst($user->first_name) : ucfirst($user->nickname));
						$message .= '<p>По вашему запросу, мы обновили ваши учетные даные:</p>';
						$message .= '<p>'.sprintf(__('Логин: %s'), $user->user_login.' или '.$user->user_email ).'<br>'.sprintf(__('Пароль: %s'), $random_password).'</p>';
						$message .= '<p>Доступ в личный кабинет:'.thim_get_login_page_url().'<br>В личном кабинете при необходимости можно изменить пароль.</p>';
						$message .= '<p>С уважением,<br>Команда сайта «DigitalCustDev.ru»<br>';
						$message .= get_option( 'siteurl' ).'</p>';
	                    
	                    $headers[] = 'MIME-Version: 1.0' . "\r\n";
	                    $headers[] = 'Content-type: text/html; charset=UTF-8' . "\r\n";
						$headers[] = "Return-Path: admin@digitalcustdev.ru \r\n";
	                    $headers[] = "X-Mailer: PHP \r\n";
	                    $headers[] = 'From: '.$sender.' < '.$email.'>' . "\r\n";
	                    
	                    $mail = wp_mail( $to, $subject, $message, $headers );
	                    if( $mail )
							$success = 'Проверьте ваш Email ящик, Вам отправлен новый пароль.';
	                        
	                } else {
	                    $error = 'Oops, что-то пошло не так.';
	                }
	                
	            }
	            
	            if( ! empty( $error ) ) {
	                echo '<div><p class="message message-error"><strong>Ошибка:</strong> '. $error .'</p></div></br>';
				}            
	            else if( ! empty( $success ) ) {
	                echo '<div id="success"><p class="message message-success">'. $success .'</p></div><br>';
				$_SESSION['s'] = false;
				}
	        }	
	
		}	
	    ?>
			
			<div class="thim-login form-submission-lost-password">
				<form id="lostpasswordform" action="" method="post">
					<fieldset>
						<h2 class="title"><?php esc_html_e( 'Get Your Password', 'eduma' ); ?></h2>
						<p class="description"><?php esc_html_e( 'Забыли пароль? Пожалуйста, введите Email пользователя.', 'eduma' ); ?></p>
						<?php $user_login = isset( $_POST['user_login'] ) ? $_POST['user_login'] : ''; ?>
						<p>
						<input placeholder="<?php esc_attr_e( 'Email', 'eduma' ); ?>" type="text" name="user_login" id="user_login" class="input">
	                    </p>
						<?php if( function_exists( 'gglcptch_display' ) ) { echo gglcptch_display(); } ; ?>
	
						<div class="lostpassword-submit">
							<input type="hidden" name="somfrp_action" value="<?php echo wp_create_nonce( 'somfrp_action'); ?>"/>
							<button type="submit" id="reset-pass-submit" name="reset-pass-submit" class="button button-primary button-large"><?php esc_attr_e( 'Обновить пароль', 'eduma' ); ?></button>
	
						</div>
	
					</fieldset>
				</form>
			</div>	
			
		<script>
			jQuery(document).ready(function($) {
				
				$( "#reset-pass-submit" ).click(function( event ) {
				var email = $("input#user_login").val();
				var googleResponse = grecaptcha.getResponse();
				
					if(IsEmail(email)==false){
						$("#user_login").css("border","1px solid red");
					return false;
					}	else {
						$("#user_login").css("border","1px solid #e5e5e5");
					}			
	
					if (googleResponse == ""){
						event.preventDefault();
	//					$("#main").prepend("<b>Prepended text</b>. ");
							$("#myDiv").remove();
							$('<div/>', {
							'id':'myDiv',
							'class':'myClass',
	//						'style':'cursor:pointer;font-weight:bold;',
							'html':'<p class="message message-error">Пройдите reCAPTCHA</p>',
	//						'click':function(){ alert(this.id) 
	//						},
	//						'mouseenter':function(){ $(this).css('color', 'red'); },
	//						'mouseleave':function(){ $(this).css('color', 'black'); }
						}).prependTo("#main");
					return false;	
					} else {            
	//				alert('true')
						$("#myDiv").remove()
						return true;		
					}	
	
				function IsEmail(email) {
				var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					if(!regex.test(email)) {
						return false;
					}else{
						return true;
					}
				}
					
				});
			});
		</script>


<?php while (have_posts()) : the_post(); ?>

    <?php get_template_part('content', 'page'); ?>

    <?php
    // If comments are open or we have at least one comment, load up the comment template
    if (comments_open() || get_comments_number()) :
        comments_template();
    endif;
    ?>

<?php endwhile; // end of the loop.  ?>
