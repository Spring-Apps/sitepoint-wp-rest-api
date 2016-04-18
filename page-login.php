<?php
/*
Template Name: Login Page
*/

$client_id = '0eM2o2EOsba0CKxO8acX5keJKAHv9Y';
$client_secret = 'z9clbBoQqQ1BRDlpsuzFrFZFoJg6fq';

$code = false;
if(isset($_GET['code'])) {
    $code = $_GET['code'];
}

//Quick switch to detect ID
if(!$code) {

    $url = site_url() . '?oauth=authorize&response_type=code&client_id=' . $client_id;
    header('Location: ' . $url);
    die();

} else {

    $auth = base64_encode($client_id.':'.$client_secret);
    try {
        /*
         * Making the Call to get the access token
         *
         */
        $args = [
            'headers' => [
                'Authorization' => 'Basic ' . $auth
            ],
            'body' => [
            'grant_type' => 'authorization_code',
            'code' => $code
            ],
    ];
        $json = wp_remote_post( site_url() . '?oauth=token', $args );

        if(is_array($json) && isset($json['body'])) {

            $json = json_decode($json['body']);
            $auth_token = $json->access_token;
            $user_id = get_current_user_id();

            setcookie('wordpress_access_token', $auth_token, time() + 3600, '/', preg_replace('#^https?://#', '', rtrim(site_url(),'/')), 0);

            update_user_meta($user_id, 'wordpress_access_token', $auth_token);
        } else {
            print_r($json);
            die();
        }

        header('Location: ' . site_url());
    } catch (Exception $e) {
        var_dump($e);
    }

}