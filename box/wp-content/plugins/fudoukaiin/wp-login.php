<?php
/**
 * WordPress User Page
 *
 * Handles authentication, registering, resetting passwords, forgot password,
 * and other user handling.
 *
 * @package WordPress3.7
 * @subpackage Fudousan Plugin
 * Fudousan kaiin Plugin
 * Version: 1.4.1
 */

/** Make sure that the WordPress bootstrap has run before continuing. */


require( dirname(__FILE__) . '/../../../wp-load.php' );
remove_action('wp_print_styles', 'jqlb_css');	
remove_action('wp_print_scripts', 'jqlb_js');


// Redirect to https login if forced to use SSL
if ( force_ssl_admin() && !is_ssl() ) {
	if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
		wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
		exit();
	} else {
		wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit();
	}
}

/**
 * Outputs the header for the login page.
 *
 * @uses do_action() Calls the 'login_head' for outputting HTML in the Log In
 *		header.
 * @uses $error The error global, which is checked for displaying errors.
 *
 * @param string $title Optional. WordPress Log In Page title to display in
 *		<title/> element.
 * @param string $message Optional. Message to display in header.
 * @param WP_Error $wp_error Optional. WordPress Error Object
 */
function fudoukaiin_login_header($title = 'Log In', $message = '', $wp_error = '') {

	global $error, $is_iphone, $interim_login, $current_site;

	//SSL
	$fudou_ssl_site_url = get_option('fudou_ssl_site_url');
	if( $fudou_ssl_site_url !=''){
		$site_url = $fudou_ssl_site_url;
	}else{
		$site_url = get_option('siteurl');
	}

	// Don't index any of these forms
	add_filter( 'pre_option_blog_public', '__return_zero' );
	add_action( 'login_head', 'noindex' );

	if ( empty($wp_error) )
		$wp_error = new WP_Error();

	// Shake it!
	$shake_error_codes = array( 'empty_password', 'empty_email', 'invalid_email', 'invalidcombo', 'empty_username', 'invalid_username', 'incorrect_password' );
	$shake_error_codes = apply_filters( 'shake_error_codes', $shake_error_codes );

	//if ( $shake_error_codes && $wp_error->get_error_code() && in_array( $wp_error->get_error_code(), $shake_error_codes ) )
	//	add_action( 'login_head', 'fudoukaiin_wp_shake_js', 12 );

	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head>
		<title><?php bloginfo('name'); ?> &rsaquo; <?php echo $title; ?></title>
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		<link rel="stylesheet" id="login-css"  href="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/login.css" type="text/css" media="all" />
		<link rel="stylesheet" id="colors-fresh-css"  href="<?php echo $site_url; ?>/wp-admin/css/colors-fresh.css" type="text/css" media="all" />
		<script type="text/javascript" src="<?php echo $site_url; ?>/wp-includes/js/jquery/jquery.js"></script>
		<script type="text/javascript" src="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/js/utils.min.js"></script><!-- .3.6.1 -->
		<meta name='robots' content='noindex,nofollow' />
	<?php	if ( $is_iphone ) { ?>
			<meta name="viewport" content="width=320; initial-scale=0.9; maximum-scale=1.0; user-scalable=0;" />
			<style type="text/css" media="screen">
				form { margin-left: 0px; }
				#login { margin-top: 20px; }
			</style>
	<?php	} elseif ( isset($interim_login) && $interim_login ) { 	?>
			<style type="text/css" media="all">
				body {padding-top: 0px;}
				.login #login { margin: 0px auto; }
			</style>
	<?php	}

	if ( $shake_error_codes && $wp_error->get_error_code() && in_array( $wp_error->get_error_code(), $shake_error_codes ) )
		fudoukaiin_wp_shake_js();
	?>
	</head>


	<body class="login">
	<div id="login">

	<?php

	$message = apply_filters('login_message', $message);
	if ( !empty( $message ) ) echo $message . "\n";

	// Incase a plugin uses $error rather than the $errors object
	if ( !empty( $error ) ) {
		$wp_error->add('error', $error);
		unset($error);
	}

	if ( $wp_error->get_error_code() ) {
		$errors = '';
		$messages = '';
		foreach ( $wp_error->get_error_codes() as $code ) {
			$severity = $wp_error->get_error_data($code);
			foreach ( $wp_error->get_error_messages($code) as $error ) {
				if ( 'message' == $severity )
					$messages .= '	' . $error . "<br />\n";
				else
					$errors .= '	' . $error . "<br />\n";
			}
		}
		if ( !empty($errors) )
			$errors = str_replace('wp-login.php' , 'wp-content/plugins/fudoukaiin/wp-login.php' , $errors );
			echo '<div id="login_error">' . apply_filters('login_errors', $errors) . "</div>\n";
		if ( !empty($messages) )
			echo '<p class="message">' . apply_filters('login_messages', $messages) . "</p>\n";
	}
} // End of fudoukaiin_login_header()








/**
 * Outputs the footer for the login page.
 *
 * @param string $input_id Which input to auto-focus
 */
function fudoukaiin_login_footer($input_id = '') {

	//SSL
	$fudou_ssl_site_url = get_option('fudou_ssl_site_url');
	if( $fudou_ssl_site_url !=''){
		$site_url = $fudou_ssl_site_url;
	}else{
		$site_url = get_option('siteurl');
	}

	echo "</div>\n";

	if ( !empty($input_id) ) {
	?>
	<script type="text/javascript">
		try{document.getElementById('<?php echo $input_id; ?>').focus();}catch(e){}
		if(typeof wpOnload=='function')wpOnload();
	</script>
	<?php
		}
	?>
	<?php //do_action('fudoukaiin_login_footer'); ?>

	<script type='text/javascript'>
	/* <![CDATA[ */
	var pwsL10n = {
		empty: "強度インジケータ",
		short: "非常に弱い",
		bad: "弱い",
		good: "普通",
		strong: "強力",
		mismatch: "不一致"
	};
	try{convertEntities(pwsL10n);}catch(e){};
	/* ]]> */
	</script>
	<!-- .3.6.1 -->
	<script type="text/javascript" src="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/js/password-strength-meter.min.js"></script>
	<script type="text/javascript" src="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/js/user-profile.min.js"></script>

	</body>
	</html>
<?php
}





function fudoukaiin_wp_shake_js() {
	global $is_iphone;
	if ( $is_iphone )
		return;
	?>
	<script type="text/javascript">
		addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
		function s(id,pos){g(id).left=pos+'px';}
		function g(id){return document.getElementById(id).style;}
		function shake(id,a,d){c=a.shift();s(id,c);if(a.length>0){setTimeout(function(){shake(id,a,d);},d);}else{try{g(id).position='static';wp_attempt_focus();}catch(e){}}}
		addLoadEvent(function(){ var p=new Array(15,30,15,0,-15,-30,-15,0);p=p.concat(p.concat(p));var i=document.forms[0].id;g(i).position='relative';shake(i,p,20);});
	</script>
	<?php
}






/**
 * Handles sending password retrieval email to user.
 *
 * @uses $wpdb WordPress Database object
 *
 * @return bool|WP_Error True: when finish. WP_Error on error
 */
function fudoukaiin_retrieve_password() {
	global $wpdb, $current_site;

	$errors = new WP_Error();

	$u_login = isset($_POST['user_login']) ? trim($_POST['user_login']) : '';
	$u_email = isset($_POST['user_email']) ? trim($_POST['user_email']) : '';


	if ( empty( $u_login ) && empty( $_POST['user_email'] ) )
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.'));

	//スパム対策
	if (  $u_login == 'admin'  )
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.'));
	if (  isset($_POST['mail']) && $_POST['mail'] != ''  )
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.'));


	if ( isset( $u_login ) && strpos( $u_login, '@' ) ) {
		$user_data = get_user_by_email($u_login);
		if ( empty($user_data) )
			$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
	} else {
		$login = $u_login;
	//	$user_data = get_userdatabylogin($login);
		$user_data = get_user_by('login',$login) ;
	}

	do_action('lostpassword_post');

	if ( $errors->get_error_code() )
		return $errors;

	if ( !$user_data ) {
		$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.'));
		return $errors;
	}

	// redefining user_login ensures we return the right case in the email
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;

	do_action('retreive_password', $user_login);  // Misspelled and deprecated
	do_action('fudoukaiin_retrieve_password', $user_login);

	$allow = apply_filters('allow_password_reset', true, $user_data->ID);

	if ( ! $allow )
		return new WP_Error('no_password_reset', __('Password reset is not allowed for this user'));
	else if ( is_wp_error($allow) )
		return $allow;

	$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
	if ( empty($key) ) {
		// Generate something random for a key...
		$key = wp_generate_password(20, false);
		do_action('retrieve_password_key', $user_login, $key);
		// Now insert the new md5 key into the db
		$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
	}
	$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
	$message .= network_site_url() . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
	$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";

	//SSL
	$fudou_ssl_site_url = get_option('fudou_ssl_site_url');
	if( $fudou_ssl_site_url !=''){
	$message .= '<' . $fudou_ssl_site_url . "/wp-content/plugins/fudoukaiin/wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login) . ">\r\n";
	}else{
	$message .= '<' . network_site_url("wp-content/plugins/fudoukaiin/wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";
	}


	if ( is_multisite() )
		$blogname = $GLOBALS['current_site']->site_name;
	else
		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$title = sprintf( __('[%s] Password Reset'), $blogname );

	$title = apply_filters('retrieve_password_title', $title);
	$message = apply_filters('retrieve_password_message', $message, $key);

	if ( $message && !wp_mail($user_email, $title, $message) )
		wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );

	return true;
}










/**
 * Retrieves a user row based on password reset key and login
 *
 * @uses $wpdb WordPress Database object
 *
 * @param string $key Hash to validate sending user's password
 * @param string $login The user login
 *
 * @return object|WP_Error
 */
function check_password_reset_key_fudou($key, $login) {
	global $wpdb;

	$key = preg_replace('/[^a-z0-9]/i', '', $key);

	if ( empty( $key ) || !is_string( $key ) )
		return new WP_Error('invalid_key', __('Invalid key'));

	if ( empty($login) || !is_string($login) )
		return new WP_Error('invalid_key', __('Invalid key'));

	$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login));

	if ( empty( $user ) )
		return new WP_Error('invalid_key', __('Invalid key'));

	return $user;
}



/**
 * Handles resetting the user's password.
 *
 * @uses $wpdb WordPress Database object
 *
 * @param string $key Hash to validate sending user's password
 */
function reset_password_fudou($user, $new_pass) {
	do_action('password_reset', $user, $new_pass);
	wp_set_password($new_pass, $user->ID);
	wp_password_change_notification($user);
}









/**
 * Handles registering a new user.
 *
 * @param string $user_login User's username for logging in
 * @param string $user_email User's email address to send password and add
 * @return int|WP_Error Either user's ID or error on failure.
 */
function register_new_user_fudou( $user_login, $user_email ) {

	$errors = new WP_Error();


	//option
	$user_zip = isset($_POST['user_zip']) ? esc_attr($_POST['user_zip']) : '';
	$user_adr = isset($_POST['user_adr']) ? esc_attr($_POST['user_adr']) : '';
	$user_tel = isset($_POST['user_tel']) ? esc_attr($_POST['user_tel']) : '';
	$first_name = isset($_POST['first_name']) ? esc_attr($_POST['first_name']) : '';
	$last_name = isset($_POST['last_name']) ? esc_attr($_POST['last_name']) : '';


	$sanitized_user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.' ) );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered, please choose another one.' ) );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ) );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
	}


	// Check the Option
	if( get_option('kaiin_users_mail_name') == '1' ){
		if( $first_name =='' && $last_name == '' && get_option('kaiin_users_mail_name_hissu') == '1' )
			$errors->add( 'empty_username', '<strong>エラー</strong>: お名前を入力してください。' );
	}

	if( get_option('kaiin_users_mail_zip') == '1' ){
		if( $user_zip =='' && get_option('kaiin_users_mail_zip_hissu') == '1' )
			$errors->add( 'empty_username', '<strong>エラー</strong>: 郵便番号を入力してください。' );
	}
	if( get_option('kaiin_users_mail_address') == '1' ){
		if( $user_adr =='' && get_option('kaiin_users_mail_address_hissu') == '1' )
			$errors->add( 'empty_username', '<strong>エラー</strong>: 住所を入力してください。' );
	}

	if( get_option('kaiin_users_mail_tel') == '1' ){
		if( $user_tel =='' && get_option('kaiin_users_mail_tel_hissu') == '1' )
			$errors->add( 'empty_username', '<strong>エラー</strong>: 電話番号を入力してください。' );
	}

	if( mb_strlen( $sanitized_user_login) < 4 ){
			$errors->add( 'empty_username', '<strong>エラー</strong>: ユーザー名が短すぎです。' );
	}

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->get_error_code() )
		return $errors;

	$user_pass = wp_generate_password( 12, false);
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
		return $errors;
	}

	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.


	//option
	update_user_meta( $user_id, 'first_name', $first_name );
	update_user_meta( $user_id, 'last_name', $last_name );


	if( $user_zip !='' )
		update_user_meta( $user_id, 'user_zip', $user_zip );

	if( $user_adr !='' )  
		update_user_meta( $user_id, 'user_adr', $user_adr );

	if( $user_tel !='' )
		update_user_meta( $user_id, 'user_tel', $user_tel );


	// IPアドレス
	$ipaddress = $_SERVER["REMOTE_ADDR"];
	if( $ipaddress !='' )
		update_user_meta( $user_id, 'ipaddress', $ipaddress );

	$useragent = esc_attr($_SERVER["HTTP_USER_AGENT"]);
	if( $useragent !='' )
		update_user_meta( $user_id, 'useragent', $useragent );

	$today = date("Y/m/d");	// 2011/04/01
	if( $today != '' )
		update_user_meta( $user_id, 'login_date', $today );

	update_user_meta( $user_id, 'login_count', '0' );

	//show_admin_bar_front false
	update_user_meta( $user_id, 'show_admin_bar_front', 'false' );


/*
	update_user_meta( $user_id, 'nickname', $nickname );
	update_user_meta( $user_id, 'description', $description );
	update_user_meta( $user_id, 'rich_editing', $rich_editing );
	update_user_meta( $user_id, 'comment_shortcuts', $comment_shortcuts );
	update_user_meta( $user_id, 'admin_color', $admin_color );
	update_user_meta( $user_id, 'use_ssl', $use_ssl );
	update_user_meta( $user_id, 'show_admin_bar_front', $show_admin_bar_front );
	update_user_meta( $user_id, 'show_admin_bar_admin', $show_admin_bar_admin );
*/

	fudou_new_user_notification( $user_id, $user_pass );

	return $user_id;
}





/**
 * Notify the blog admin of a new user, normally via email.
 *
 * @since 2.0
 *
 * @param int $user_id User ID
 * @param string $plaintext_pass Optional. The user's plaintext password
 * wp-includes/pluggable.php
 */
function fudou_new_user_notification($user_id, $plaintext_pass = '') {

	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	if ( empty($plaintext_pass) )
		return;


	$kaiin_users_mail_new_subject = get_option('kaiin_users_mail_new_subject');
	$kaiin_users_mail_new__comment = get_option('kaiin_users_mail_new__comment');

	if($kaiin_users_mail_new_subject == '')
		$kaiin_users_mail_new_subject = sprintf(__('[%s] Your username and password'), $blogname);

	if($kaiin_users_mail_new__comment == ''){
		$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
		$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
		$message .= get_bloginfo('url') . "\r\n";
	}else{

		$kaiin_users_mail_new__comment = str_replace("[user_login]",  $user_login , $kaiin_users_mail_new__comment);
		$kaiin_users_mail_new__comment = str_replace("[user_psass]",  $plaintext_pass , $kaiin_users_mail_new__comment);
		$kaiin_users_mail_new__comment = str_replace("[user_mail]" ,  $user_email , $kaiin_users_mail_new__comment);

		$first_name = get_user_meta( $user_id, 'first_name', true) ;
		$last_name = get_user_meta( $user_id, 'last_name', true) ;
		$user_zip = get_user_meta( $user_id, 'user_zip', true) ;
		$user_adr = get_user_meta( $user_id, 'user_adr', true) ;
		$user_tel = get_user_meta( $user_id, 'user_tel', true) ;


		$kaiin_users_mail_new__comment = str_replace("[user_name]", $last_name . ' ' . $first_name , $kaiin_users_mail_new__comment);
		$kaiin_users_mail_new__comment = str_replace("[user_zip]", $user_zip, $kaiin_users_mail_new__comment);
		$kaiin_users_mail_new__comment = str_replace("[user_adr]", $user_adr, $kaiin_users_mail_new__comment);
		$kaiin_users_mail_new__comment = str_replace("[user_tel]", $user_tel, $kaiin_users_mail_new__comment);

		$message = $kaiin_users_mail_new__comment;
	
	}

	wp_mail($user_email, $kaiin_users_mail_new_subject, $message);

}



/********************************************************************
//	 Main
********************************************************************/


//差出人変更
$fudoukaiin_wp_mail_from = new fudoukaiin_wp_mail_from();

//SSL
$fudou_ssl_site_url = get_option('fudou_ssl_site_url');
if( $fudou_ssl_site_url !=''){
	$site_url = $fudou_ssl_site_url;
}else{
	$site_url = get_option('siteurl');
}



$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
$errors = new WP_Error();

if ( isset($_GET['key']) )
	$action = 'resetpass';

// validate action so as to default to the login screen
if ( !in_array($action, array('logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register', 'login'  , 'retrievepasswordok', 'loginok' , 'registerok'   ), true) && false === has_filter('login_form_' . $action) )
	$action = 'login';

nocache_headers();

header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

// Move flag is set
if ( defined('RELOCATE') ) { 
	if ( isset( $_SERVER['PATH_INFO'] ) && ($_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) )
		$_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );

	$schema = is_ssl() ? 'https://' : 'http://';
	if ( dirname($schema . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) != get_option('siteurl') )
		update_option('siteurl', dirname($schema . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) );
}

//Set a cookie now to see if they are supported by the browser.
setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
if ( SITECOOKIEPATH != COOKIEPATH )
	setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);

// allow plugins to override the default actions, and to add extra actions if they want
do_action('login_form_' . $action);

$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);

switch ($action) {

	case 'logout' :
		check_admin_referer('log-out');
		wp_logout();

		$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'wp-content/plugins/fudoukaiin/wp-login.php?loggedout=true';
		wp_safe_redirect( $redirect_to );
		exit();

		break;

	case 'lostpassword' :
	case 'retrievepassword' :

		if ( $http_post ) {
			$errors = fudoukaiin_retrieve_password();
			if ( !is_wp_error($errors) ) {
				$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'wp-content/plugins/fudoukaiin/wp-login.php?checkemail=confirm';
				wp_safe_redirect( $redirect_to );
				exit();
			}
		}

		if ( isset($_GET['error']) && 'invalidkey' == $_GET['error'] ) $errors->add('invalidkey', __('Sorry, that key does not appear to be valid.'));
		$redirect_to = apply_filters( 'lostpassword_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '' );

		$redirect_to = 'wp-login.php?action=retrievepasswordok';


		do_action('lost_password');
		fudoukaiin_login_header(__('Lost Password'), '<p class="message">' . __('Please enter your username or email address. You will receive a link to create a new password via email.') . '</p>', $errors);

		$user_login = isset($_POST['user_login']) ? stripslashes($_POST['user_login']) : '';

	?>
		<style type="text/css" media="all">
			body {	    padding-top: 10px;	}
			.login #login { margin: 0px auto; }
		</style>

		<form name="lostpasswordform" id="lostpasswordform" action="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/wp-login.php?action=lostpassword" method="post">
			<p>
				<label><?php _e('Username or E-mail:') ?><br />
				<input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr($user_login); ?>" size="20" tabindex="10" /></label>
				<input type="text" name="mail" value="" style="display:none;" />

			</p>
		<?php do_action('lostpassword_form'); ?>
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
			<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Get New Password'); ?>" tabindex="20" /></p>
		</form>

		<p id="nav">


		</p>

		<?php
		fudoukaiin_login_footer('user_login');
		break;

	case 'resetpass' :
	case 'rp' :
		$user = check_password_reset_key_fudou($_GET['key'], $_GET['login']);

		if ( is_wp_error($user) ) {
			wp_redirect( $site_url .'wp-content/plugins/fudoukaiin/wp-login.php?action=lostpassword&error=invalidkey' );
			exit;
		}

		$errors = '';

		if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] ) {
			$errors = new WP_Error('password_reset_mismatch', __('The passwords do not match.'));
		} elseif ( isset($_POST['pass1']) && !empty($_POST['pass1']) ) {
			reset_password_fudou($user, $_POST['pass1']);
			fudoukaiin_login_header(__('Password Reset'), '<p class="message reset-pass">' . __('Your password has been reset.') . ' <a href="' . get_option('siteurl') . '/wp-content/plugins/fudoukaiin/wp-login.php' . '">' . __('Log in') . '</a></p>');
			fudoukaiin_login_footer();
			exit;
		}

		wp_enqueue_script('utils');
		wp_enqueue_script('user-profile');

		fudoukaiin_login_header(__('Reset Password'), '<p class="message reset-pass">' . __('Enter your new password below.') . '</p>', $errors );

	?>
		<style type="text/css" media="all">
			body {	    padding-top: 10px;	}
			.login #login { margin: 0px auto; }
		</style>

		<form name="resetpassform" id="resetpassform" action="<?php echo $site_url . '/wp-content/plugins/fudoukaiin/wp-login.php?action=resetpass&key=' . urlencode($_GET['key']) . '&login=' . urlencode($_GET['login']); ?>" method="post">
			<input type="hidden" id="user_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" autocomplete="off" />

			<p>
				<label><?php _e('New password') ?><br />
				<input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" /></label>
			</p>
			<p>
				<label><?php _e('Confirm new password') ?><br />
				<input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" /></label>
			</p>

			<div id="pass-strength-result" class="hide-if-no-js"><?php _e('Strength indicator'); ?></div>
			<p class="description indicator-hint"><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></p>

			<br class="clear" />
			<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Reset Password'); ?>" tabindex="100" /></p>
		</form>

		<p id="nav">
		<a href="<?php echo get_option('siteurl'); ?>/wp-content/plugins/fudoukaiin/wp-login.php"><?php _e('Log in') ?></a>
		<?php if (get_option('kaiin_users_can_register')) : ?>
		 | <a href="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/wp-login.php?action=register"><?php _e('Register') ?></a>
		<?php endif; ?>
		</p>

		<?php
		fudoukaiin_login_footer('user_pass');
		break;

	case 'register' :

		if ( is_multisite() ) {
			// Multisite uses wp-signup.php
			wp_redirect( apply_filters( 'wp_signup_location', site_url('wp-signup.php') ) );
			exit;
		}

		if( get_option('kaiin_moushikomi') == 1 ){
			exit;
		}

		if ( !get_option('kaiin_users_can_register') ) {
			wp_redirect( $site_url .'wp-content/plugins/fudoukaiin/wp-login.php?registration=disabled' );
			exit();
		}

		$user_login = '';
		$user_email = '';
		$user_email2 = '';

		if ( $http_post ) {
			//for 3.05
			global $wp_version;
			$tmp_wp_version = intval(mb_substr($wp_version,0,3,"UTF-8")*10) ;
			if ( $tmp_wp_version < 31 ) {
				require_once( ABSPATH . WPINC . '/registration.php');
			}

			$user_login = $_POST['user_login'];
			$user_email = $_POST['user_email'];

			$user_email2 = $_POST['user_email2'];

			if($user_email2 == ''){
				$errors = register_new_user_fudou($user_login, $user_email);
				if ( !is_wp_error($errors) ) {
				//	$redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : 'wp-login.php?checkemail=registered';

				if( $fudou_ssl_site_url !=''){

?>
					<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
					<head>
						<title><?php bloginfo('name'); ?> &rsaquo; <?php echo $title; ?></title>
						<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
						<link rel="stylesheet" id="login-css"  href="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/login.css" type="text/css" media="all" />
						<link rel="stylesheet" id="colors-fresh-css"  href="<?php echo $site_url; ?>/wp-admin/css/colors-fresh.css" type="text/css" media="all" />
						<meta name='robots' content='noindex,nofollow' />

					<?php if ( $is_iphone ) { ?>
						<meta name="viewport" content="width=320; initial-scale=0.9; maximum-scale=1.0; user-scalable=0;" />
						<style type="text/css" media="screen">
							form { margin-left: 0px; }
							#login { margin-top: 20px; }
						</style>
					<?php } elseif ( isset($interim_login) && $interim_login ) { 	?>
						<style type="text/css" media="all">
							body {padding-top: 0px;}
							.login #login { margin: 0px auto; }
						</style>
					<?php
						}
					?>
					</head>
					<body class="login">
					<div id="login">

					<p class="message">登録を完了しました。メールを確認してください。<br /></p>

					</div>
					</body>
					</html>
<?php



					exit();
				}else{
					$redirect_to = site_url('wp-content/plugins/fudoukaiin/wp-login.php?action=registerok');
				//	$redirect_to = $site_url . '/wp-content/plugins/fudoukaiin/wp-login.php?action=registerok';
					wp_safe_redirect( $redirect_to );
					exit();
				}

				}
			}
		}

		$redirect_to = $site_url . '/wp-content/plugins/fudoukaiin/wp-login.php?action=registerok';

		fudoukaiin_login_header(__('Registration Form'), '', $errors);
	?>

		<style type="text/css" media="all">
		<!--
			body {	    padding-top: 10px;	}
			.login #login { margin: 0px auto; }
			.input1 { 
				background: none repeat scroll 0 0 #FBFBFB;
				border: 1px solid #E5E5E5;
				font-size: 24px;
				margin-bottom: 16px;
				margin-right: 6px;
				margin-top: 2px;
				padding: 3px;
				width: 97%;
				border-color: #DFDFDF;
				color: #555555;
				width: 100px; 
				}
				
			h2 {
				border-left: 3px solid #ccc;
				margin: 5px 0 15px 0;
				padding: 0 0 0 8px;
				font-size: 14px;
				color: #777777;
			}
			#kaiin_kiyaku{
				    border: 1px solid #ccc;
				    float: left;
				    font-size: 12px;
				    height: 80px;
				    margin: 0 0 10px 0;
				    overflow: auto;
				    padding: 5x;
				    width: 100%;
			}
		-->
		</style>

		<form name="registerform" id="registerform" action="<?php echo $site_url . '/wp-content/plugins/fudoukaiin/wp-login.php?action=register'; ?>" method="post">
		<h2>会員登録</h2>

			<p>
				<?php _e('Username') ?> 　<font color="#FF2200">(必須)</font> (半角英数字)<br />
				<input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" tabindex="3" />
			</p>
			<p>
				<?php _e('E-mail') ?>　<font color="#FF2200">(必須)</font> <br />
				<input type="text" name="user_email" id="user_email" class="input" value="<?php echo esc_attr(stripslashes($user_email)); ?>" size="25" tabindex="4" />
			</p>

			<p style="display:none;">
				<?php _e('E-mail2') ?> 　<font color="#FF2200">(必須)</font> (半角英数字)<br />
				<input type="text" name="user_email2" id="user_email" class="input" value="" size="25" tabindex="-1" />
			</p>

		<?php do_action('register_form'); ?>

		<?php if( get_option('kaiin_kiyaku') != '' && !$is_iphone ){ ?>
			<div id="kaiin_kiyaku">

			<?php
			if( get_option('kaiin_kiyakubr') == '1' ){
				echo nl2br(get_option('kaiin_kiyaku')); 
			}else{
				echo get_option('kaiin_kiyaku'); 
			}
			?>

			</div>
		<?php } ?>

			<br class="clear" />
			<p id="reg_passmail">
			<?php if( get_option('kaiin_kiyaku') != '' ){ ?>
			*会員規約に同意の上登録ボタンを押してください。<br />
			<?php } ?>
			*<?php _e('A password will be e-mailed to you.') ?>
			</p>
			<br class="clear" />
			<input type="hidden" name="redirect_to" value="<?php echo $redirect_to ; ?>" />
			<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Register'); ?>" tabindex="100" /></p>


		<?php if( get_option('kaiin_kiyaku') != '' && $is_iphone ){ ?>
			<br class="clear" />
			<br class="clear" />
			<hr />
			<?php
			if( get_option('kaiin_kiyakubr') == '1' ){
				echo nl2br(get_option('kaiin_kiyaku')); 
			}else{
				echo get_option('kaiin_kiyaku'); 
			}
			?>
		<?php } ?>


		</form>

		<p id="nav">
		<!--
		<a href="<?php echo site_url('wp-content/plugins/fudoukaiin/wp-login.php', 'login') ?>"><?php _e('Log in') ?></a> |
		-->
		<a href="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/wp-login.php?action=lostpassword" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a>
		</p>


	<?php
		fudoukaiin_login_footer('user_login');
		break;

	case 'loginok' :
		fudoukaiin_login_header('ログイン', '', $errors);
		echo '<p class="message">ログインしました。<br /></p>';

		fudoukaiin_login_footer('user_login');
		break;

	case 'registerok' :
		fudoukaiin_login_header('会員登録', '', $errors);
		echo '<p class="message">登録を完了しました。メールを確認してください。<br /></p>';
		fudoukaiin_login_footer('user_login');
		break;


	case 'retrievepasswordok' :

		fudoukaiin_login_header(__('Registration Form'), '', $errors);
		echo '<p class="message">確認用のリンクをメールで送信しましたので、ご確認ください。<br /></p>';
		fudoukaiin_login_footer('user_login');

		break;


	case 'login' :
	default:


		$secure_cookie = '';
		$interim_login = isset($_REQUEST['interim-login']);

		// If the user wants ssl but the session is not ssl, force a secure cookie.
		if ( !empty($_POST['log']) && !force_ssl_admin() ) {
			$user_name = sanitize_user($_POST['log']);

			//if ( $user = get_userdatabylogin($user_name) ) {

			if ( $user = get_user_by('login',$user_name) ){
				if ( get_user_option('use_ssl', $user->ID) ) {
					$secure_cookie = true;
					force_ssl_admin(true);
				}
			}
		}

		if ( isset( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = $_REQUEST['redirect_to'];
			// Redirect to https if user wants ssl
			if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
				$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
		} else {

			$redirect_to = get_bloginfo('url');
		}



		$reauth = empty($_REQUEST['reauth']) ? false : true;

		// If the user was redirected to a secure login form from a non-secure admin page, and secure login is required but secure admin is not, then don't use a secure
		// cookie and redirect back to the referring non-secure admin page.  This allows logins to always be POSTed over SSL while allowing the user to choose visiting
		// the admin via http or https.

		if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
			$secure_cookie = false;

		// secure login
		//$user = wp_signon('', $secure_cookie);
		$user = fudoukaiin_login2($secure_cookie);


		//$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);

		if ( !is_wp_error($user) && !$reauth ) {
			if ( $interim_login ) {
				$message = '<p class="message">' . __('You have logged in successfully.') . '</p>';
				fudoukaiin_login_header( '', $message ); 
				?>
				<script type="text/javascript">setTimeout( function(){window.close()}, 8000);</script>
				<p class="alignright">
				<input type="button" class="button-primary" value="<?php esc_attr_e('Close'); ?>" onclick="window.close()" /></p>
				</div></body></html>
				<?php
				exit;
			}

			if (  empty( $redirect_to ) ) {
				$redirect_to = get_bloginfo('wpurl') . '/wp-content/plugins/fudoukaiin/wp-login.php?action=loginok';
			}
			wp_safe_redirect($redirect_to);
			exit();
		}




		$errors = $user;
		// Clear errors if loggedout is set.
		if ( !empty($_GET['loggedout']) || $reauth )
			$errors = new WP_Error();

		// If cookies are disabled we can't log in even with a valid user+pass
		if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
			$errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));

		// Some parts of this script use the main login form to display a message
		if( isset($_GET['loggedout']) && TRUE == $_GET['loggedout'] )
			$errors->add('loggedout', __('You are now logged out.'), 'message');
		elseif	( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )
			$errors->add('registerdisabled', __('User registration is currently not allowed.'));
		elseif	( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )
			$errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
		elseif	( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )
			$errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
		elseif	( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
			$errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');
		elseif	( $interim_login )
			$errors->add('expired', __('Your session has expired. Please log-in again.'), 'message');

		// Clear any stale cookies.
		if ( $reauth )
			wp_clear_auth_cookie();



		fudoukaiin_login_header(__('Log In'), '', $errors);

		if ( isset($_POST['log']) )
			$user_login = ( 'incorrect_password' == $errors->get_error_code() || 'empty_password' == $errors->get_error_code() ) ? esc_attr(stripslashes($_POST['log'])) : '';
		$rememberme = ! empty( $_POST['rememberme'] );


		$redirect_to = get_bloginfo('wpurl') . '/wp-content/plugins/fudoukaiin/wp-login.php?action=loginok';

	?>

		<form name="loginform" id="loginform" action="<?php echo site_url('wp-content/plugins/fudoukaiin/wp-login.php', 'login_post') ?>" method="post">
			<p>
				<label><?php _e('Username') ?><br />
				<input type="text" name="log" id="user_login" class="input" value="<?php echo esc_attr($user_login); ?>" size="20" tabindex="10" /></label>
			</p>
			<p>
				<label><?php _e('Password') ?><br />
				<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
			</p>
			<p style="display:none;">
				<label><?php _e('mail') ?><br />
				<input type="text" name="mail" id="user_mail" class="input" value="" size="20" tabindex="20" /></label>
			</p>

		<?php //do_action('login_form'); ?>

			<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90"<?php checked( $rememberme ); ?> /> <?php esc_attr_e('Remember Me'); ?></label></p>
			<p class="submit">
				<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Log In'); ?>" tabindex="100" />
		<?php	if ( $interim_login ) { ?>
				<input type="hidden" name="interim-login" value="1" />
		<?php	} else { ?>
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
		<?php 	} ?>
				<input type="hidden" name="testcookie" value="1" />
			</p>
		</form>



		<?php if ( !$interim_login ) { ?>
		<p id="nav">
		<?php if ( isset($_GET['checkemail']) && in_array( $_GET['checkemail'], array('confirm', 'newpass') ) ) : ?>

		<?php elseif ( get_option('kaiin_users_can_register') ) : ?>

			<?php if( get_option('kaiin_moushikomi') != 1 ){ ?>
				<a href="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/wp-login.php?action=register"><?php _e('Register') ?></a> |
			<?php } ?>
			<a href="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/wp-login.php?action=lostpassword" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a>
		<?php else : ?>
			<a href="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/wp-login.php?action=lostpassword" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a>
		<?php endif; ?>

		</p>
		</div>
		<!--
		<p id="backtoblog"><a href="<?php bloginfo('url'); ?>/" title="<?php esc_attr_e('Are you lost?') ?>"><?php printf(__('&larr; Back to %s'), get_bloginfo('title', 'display' )); ?></a></p>
		-->
		<?php } else { ?>
		</div>
		<?php } ?>

		<script type="text/javascript">
		function wp_attempt_focus(){
		setTimeout( function(){ try{
		<?php if ( $user_login || $interim_login ) { ?>
		d = document.getElementById('user_pass');
		d.value = '';
		<?php } else { ?>
		d = document.getElementById('user_login');
		<?php if ( 'invalid_username' == $errors->get_error_code() ) { ?>
		if( d.value != '' )
		d.value = '';
		<?php
		}
		}?>
		d.focus();
		d.select();
		} catch(e){}
		}, 200);
		}

		<?php if ( !$error ) { ?>
		wp_attempt_focus();
		<?php } ?>
		if(typeof wpOnload=='function')wpOnload();
		</script>
		<?php do_action( 'fudoukaiin_login_footer' ); ?>
		</body>
		</html>
		<?php

		break;
} // end action switch


// 会員ログイン
function fudoukaiin_login2($secure_cookie){

	$user_mail   = isset($_POST['mail']) ? $_POST['mail'] : '';
	$user_login = isset($_POST['log']) ? $_POST['log'] : '';
	$password = isset($_POST['pwd']) ? $_POST['pwd'] : '';
	$rememberme = isset($_POST['rememberme']) ? true : false;
	$user_login = sanitize_user( $user_login );

	//該当ユーザーの権限
	$user = get_user_by('login', $user_login );
	$user_contributor	= isset( $user->caps['contributor'] ) ?		$user->caps['contributor']  : 0;	//寄稿者
	$user_author		= isset( $user->caps['author'] ) ?		$user->caps['author']  : 0;		//投稿者
	$user_editor		= isset( $user->caps['editor'] ) ?		$user->caps['editor']  : 0;		//編集者
	$user_administrator	= isset( $user->caps['administrator'] ) ?	$user->caps['administrator']  : 0;	//管理者

	if (( $user_contributor + $user_author + $user_editor + $user_administrator ) > 0 ) {
		return new WP_Error('', '');
	}else{
		if ( $user_login && $password && $user_mail=='' ) {
			$creds = array();
			$creds['user_login'] = $user_login;
			$creds['user_password'] = $password;
			$creds['remember'] = $rememberme;
			$user = wp_signon( $creds, $secure_cookie );
			return $user;
		}else{
			return new WP_Error('', '');
		}
	}
}


?>
