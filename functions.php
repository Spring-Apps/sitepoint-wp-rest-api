<?php
// wp-content/themes/twentysixteen-child/functions.php

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style')
    );
}

if (current_user_can('manage_options')) {
    add_action('admin_notices', 'display_user_token');
}
function display_user_token() {
    $user_id = get_current_user_id();
    $auth_token = get_user_meta( $user_id, 'wordpress_access_token', true);
    echo $auth_token;
}

add_filter( 'login_redirect', 'ab_login_redirect', 10, 3 );
function ab_login_redirect( $redirect_to, $request, $user ) {
    return site_url() . '/login';
}

add_action ( 'login_form_logout' , 'ab_cookie_remove_logout' );
function ab_cookie_remove_logout() {
    setcookie('wordpress_access_token', "expired", time() - 3600, '/', preg_replace('#^https?://#', '', rtrim(site_url(),'/')), 0);
    wp_logout();
}