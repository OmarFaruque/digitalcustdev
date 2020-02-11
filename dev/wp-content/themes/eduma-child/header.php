
<?php
    $user     = LP_Global::user();
    $profile = LP_Profile::instance();

if ( e_is_frontend_editor() ) {
    if ( is_page( 'profile' ) && $profile->get_user_data( 'id' ) == get_current_user_id()) {
        
        get_header( 'profile' );
    } else {
        
        get_header('main');
    }
}

elseif(is_page('profile') && is_user_logged_in() && $profile->get_user()->get_id() == get_current_user_id()){
    get_header( 'profile' );
}
else {
    get_header('main');
}
