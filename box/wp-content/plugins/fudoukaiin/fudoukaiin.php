<?php
/*
Plugin Name: Fudousan kaiin Plugin
Plugin URI: http://nendeb.jp/
Description: Fudousan kaiin Plugin for Real Estate
Version: 1.4.1
Author: nendeb
Author URI: http://nendeb.jp/
*/

// Define current version constant
define( 'FUDOU_KAIIN_VERSION', '1.4.1' );


/*  Copyright 2013 nendeb (email : nendeb@gmail.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/


if (!defined('WP_CONTENT_URL'))
      define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
      define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL'))
      define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR'))
      define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');

require_once 'admin_fudoukaiin.php';
require_once 'fudoukaiin-register.php';


//show_admin_bar_front false
//add_filter( 'show_admin_bar', '__return_false' );


// add extra fields to registration form
add_action('register_form', 'fudou_registration_form', 1);



//不動産プラグインチェック
function fudou_active_plugins_check_kaiin(){
	global $is_fudouktai,$is_fudoumap,$is_fudoukaiin,$is_fudoumail,$is_fudourains,$is_fudoucsv;
	$fudo_active_plugins = get_option('active_plugins');
	if(is_array($fudo_active_plugins)) {
		foreach($fudo_active_plugins as $meta_box){
			if( $meta_box == 'fudouktai/fudouktai.php') $is_fudouktai=true;
			if( $meta_box == 'fudoumap/fudoumap.php') $is_fudoumap=true;
			if( $meta_box == 'fudoukaiin/fudoukaiin.php') $is_fudoukaiin=true;
			if( $meta_box == 'fudoumail/fudoumail.php') $is_fudoumail=true;
			if( $meta_box == 'fudourains/fudourains.php') $is_fudourains=true;
			if( $meta_box == 'fudoucsv/fudoucsv.php') $is_fudoucsv=true;
		}
	}
}
add_action('init', 'fudou_active_plugins_check_kaiin');



// 会員ログイン・ログアウト
function fudoukaiin_main(){
	$fudoukaiin = isset($_POST['md']) ? trim($_POST['md']) : null;
	if($fudoukaiin == '')
		$fudoukaiin = isset($_GET['md']) ? trim($_GET['md']) : null;
	switch ($fudoukaiin) {
		case ("login"):
			fudoukaiin_login();
			break;

		case ("logout"):
			fudoukaiin_logout();
			break;
	}
}
add_action('init', 'fudoukaiin_main');

//thickbox
function fudoukaiin_thickbox(){
	if (function_exists('add_thickbox')) add_thickbox();
}
add_action('init', 'fudoukaiin_thickbox');




// 会員ログイン
function fudoukaiin_login(){

	$redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : '';
	if (!$redirect_to) 
		$redirect_to = get_bloginfo('url');

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
	}else{
		if ( $user_login && $password && $user_mail=='' ) {
			$creds = array();
			$creds['user_login'] = $user_login;
			$creds['user_password'] = $password;
			$creds['remember'] = $rememberme;
			$user = wp_signon( $creds, false );
			if ( is_wp_error($user) ){
				//echo $user->get_error_message();
			}else{
				wp_redirect($redirect_to);
				exit();
			}
		}
	}
}


// 会員ログアウト
function fudoukaiin_logout(){
	$redirect_to = get_bloginfo('url');
///	wp_clearcookie();
	wp_clear_auth_cookie();
	do_action('wp_logout');
	nocache_headers();
	wp_redirect($redirect_to);
	exit();
}

//簡易ログ
function fudoukaiin_userlogin_success($user_id) {
	//日付
	$today = date("Y/m/d");	// 2011/04/01
	$login_date	= get_user_meta( $user_id, 'login_date', true);
	$login_count	= get_user_meta( $user_id, 'login_count', true);
	if($login_count == '') $login_count = 0;

	if( $today != $login_date ){
		$login_count ++ ;
		update_user_meta( $user_id, 'login_count', $login_count );
		update_user_meta( $user_id, 'login_date', $today );
	}else{
		if($login_count == '0')
			update_user_meta( $user_id, 'login_count', '1' );
	}
}


// 会員ログインウィジェット
function fudo_widgetInit_kaiin() {
	register_widget('fudo_widget_kaiin');
}
add_action('widgets_init', 'fudo_widgetInit_kaiin');

// 会員ログインウィジェット
class fudo_widget_kaiin extends WP_Widget {

	/** constructor */
	function fudo_widget_kaiin() {
		parent::WP_Widget(false, $name = '会員ログイン');
	}

	/** @see WP_Widget::form */	
	function form($instance) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$caution = isset($instance['caution']) ?  $instance['caution'] : '';
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">
		<?php _e('title'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('caution'); ?>">
		注意事項 <textarea rows='5' cols='15' class="widefat" id="<?php echo $this->get_field_id('caution'); ?>" name="<?php echo $this->get_field_name('caution'); ?>"><?php echo $caution; ?></textarea></label></p>

		<?php 
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {

		// outputs the content of the widget
		extract( $args );
		$title = '';
		$caution = '';
		$title = apply_filters('widget_title', $instance['title']);
		$caution =$instance['caution'];

		if(is_home()) {
			$redirect_to = get_bloginfo('url');
		}else{
		//	$redirect_to = get_permalink();
			$redirect_to = $_SERVER['REQUEST_URI'];
		}


	if (get_option('kaiin_users_can_register')){

		echo $before_widget;

		//SSL
		$fudou_ssl_site_url = get_option('fudou_ssl_site_url');
		if( $fudou_ssl_site_url !=''){
			$site_url = $fudou_ssl_site_url;
		}else{
			$site_url = get_option('siteurl');
		}


		if ( $title != '')
			echo $before_title . $title . $after_title; 

			global $user_login;

			if (!is_user_logged_in()){

				$rememberme = ! empty( $_POST['rememberme'] );

?>
				<?php echo $caution; ?>

				<div>
					<?php if( isset($_POST['md']) && $_POST['md'] == 'login' ) echo "<p>ログイン失敗しました。</p>"; ?>

					<form  name="loginform" id="loginform" method="post">
					<label for="user_login">ユーザー名</label>
					<input type="text" name="log" id="user_login" class="input" value="" size="10" tabindex="10" /><br />
					<label for="password">パスワード</label>
					<input type="password" name="pwd" id="password" class="input" value="" size="10" tabindex="20" /><br />
					<input type="text" name="mail" value="" size="10" style="display:none;" />
					<input type="checkbox" name="rememberme" id="rememberme" value="forever" tabindex="90"<?php checked( $rememberme ); ?> /> ログイン情報を記憶<br />
					<input type="hidden" name="redirect_to" value="<?php echo $redirect_to;?>" />
					<input type="hidden" name="testcookie" value="1" />
					<input type="hidden" name="md" value="login" />
					<input type="submit" name="submit" value="ログイン" />
					</form>

					<?php if( get_option('kaiin_moushikomi') != 1 ){ ?>
					<a href="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/wp-login.php?action=register&amp;KeepThis=true&amp;TB_iframe=true&amp;height=500&amp;width=400" class="thickbox">会員登録</a> | 
					<?php } ?>
					<a href="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/wp-login.php?action=lostpassword&amp;KeepThis=true&amp;TB_iframe=true&amp;height=270&amp;width=400" class="thickbox">パスワード忘れた</a>

				</div>
				<?php
				//gianism
				if (function_exists('gianism_login')){
					echo '<div style="margin:10px 0;text-align:center;">';
					echo "こちらからでもログインできます。";
					gianism_login();
					echo '</div>';
				}


			} else { 

				echo '<div>';

					global $user_ID;
					$new_pass  = isset($_POST['pass1']) ? trim($_POST['pass1']) : '';
					$new_pass2 = isset($_POST['pass2']) ? trim($_POST['pass2']) : '';

					if ($new_pass != '' && $new_pass2 != '' && isset($_POST['md']) && $_POST['md'] == 'repass' ) {
						global $wpdb,$userdata,$login_status;

						if ($new_pass == $new_pass2) {
							$new_pass = md5($new_pass);
							$wpdb->update( $wpdb->users, array( 'user_pass' => $new_pass ), array( 'ID' => $user_ID ), array( '%s' ), array( '%d' ) );
							echo "<p>パスを変更しました。</p>";

						} else {
							echo "<p>パスが合いません。</p>";
						}
					}
					//簡易ログ
					fudoukaiin_userlogin_success($user_ID);

?>
					<span class="login_comment">こんにちは！<?php echo $user_login; ?>さん</span><br />
					<span class="logout_title"><a href="<?php echo get_bloginfo("url") ."/?md=logout";?>">ログアウト</a></span> | <span class="repass_title"><a href="javascript:repass('repass');">パス変更</a></span>
				</div>

				<div id="repass">
					<br />
					<form  id="rpass" name="form" method="post" action="">
					<input type="hidden" id="user_login" value="<?php echo $user_login; ?>" autocomplete="off" />
					<label for="pass1">新しいパスを入力して下さい</label>
					<input type="text" name="pass1" id="pass1" class="input" value="" size="10" tabindex="20" autocomplete="off" /><br />
					<label for="pass2">もう一度入力して下さい</label>
					<input type="text" name="pass2" id="pass2" class="input" value="" size="10" tabindex="20" autocomplete="off" /><br />
					<div id="pass-strength-result" class="hide-if-no-js"><?php _e('Strength indicator'); ?></div>
					<input type="submit" name="submit" value="パス変更" />
					<img src="<?php bloginfo('wpurl'); ?>/wp-includes/images/wlw/wp-comments.png" alt="" title='<?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?>' class="passhelp" />
					<input type="hidden" name="md" value="repass" />
					</form>
				</div>

				<?php

				global $is_fudouktai,$is_fudoumap,$is_fudoukaiin,$is_fudoumail;

				//マッチングメール設定
				if( get_option('kaiin_users_mail') =='1' && $is_fudoumail ){
					echo '<ul>';
					echo '<div id="maching_mail"><a href="'.WP_PLUGIN_URL.'/fudoumail/fudou_user.php?KeepThis=true&TB_iframe=true&height=500&width=680" class="thickbox">';

					if( get_option('kaiin_users_rains_register') =='1' && $is_fudoumail ){
						echo '閲覧条件・メール設定</a></div>';
					}else{
						echo 'マッチングメール設定</a></div>';
					}
					echo '</ul>';
				}

			?>

				<style type="text/css" media="all">
				<!--
					input#pass1,input#pass2{
						width: auto;
					}
				
					#repass { display:none; }
					ul #repass { display:none; }
					.passhelp{vertical-align:middle;}

					#pass-strength-result {
						background-color: #eee;
						border-color: #ddd !important;
						border-style: solid;
						border-width: 1px;
						margin:5px 0;
						padding: 5px;
						text-align: center;
						width: auto;
						display: none;
					}

					#pass-strength-result.bad {
						background-color: #ffb78c;
						border-color: #ff853c !important;
					}

					#pass-strength-result.good {
						background-color: #ffec8b;
						border-color: #ffcc00 !important;
					}

					#pass-strength-result.short {
						background-color: #ffa0a0;
						border-color: #f04040 !important;
					}

					#pass-strength-result.strong {
						background-color: #c3ff88;
						border-color: #8dff1c !important;
					}

					/* maching_mail */
					#main #maching_mail a {
						-webkit-border-radius: 6px;
						-moz-border-radius: 6px;
						border-radius: 6px;
						text-shadow:1px 1px 1px #CC5559;
						color: #ffffff;
						font-size: 14px;
						text-decoration: none;
						vertical-align: middle;
						display:block;
						font-weight: bold;
						padding: 5px 8px;
						background: #ff9b9d; /* Old browsers */
						background: -moz-linear-gradient(top, #ff9b9d 0%, #ce6166 100%); /* FF3.6+ */
						background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ff9b9d), color-stop(100%, #ce6166)); /* Chrome,Safari4+ */
						background: -webkit-linear-gradient(top, #ff9b9d 0%, #ce6166 100%); /* Chrome10+,Safari5.1+ */
						background: -o-linear-gradient(top, #ff9b9d 0%, #ce6166 100%); /* Opera11.10+ */
						background: -ms-linear-gradient(top, #ff9b9d 0%, #ce6166 100%); /* IE10+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#FF9B9D', endColorstr='#CE6166', GradientType=0 ); /* IE6-9 */
						background: linear-gradient(top, #ff9b9d 0%, #ce6166 100%); /* W3C */
						text-align: center;
						margin: 5px 0px;
						width: 150px;
					}

					#main #maching_mail a:hover {
						background: #ff9b9d;
						text-decoration: underline;
					}

					#main #maching_mail a:active {
						background: #faa8cd;
						text-decoration: none;
					}

				-->
				</style>
				<!-- .3.6.1 -->
				<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/fudoukaiin/js/password-strength-meter.min.js"></script>
				<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/fudoukaiin/js/user-profile.min.js"></script>

				<script type='text/javascript'>
					/* <![CDATA[ */
					function repass(menu_id) {
						var ul=document.getElementById(menu_id);
						if (ul.style.display == "block") ul.style.display="none";
						else {
							ul.style.display="block";
						};
					};
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

			<?php 
			}	//is_user_logged_in()

			echo $after_widget;
		}
	}
} // fudo_widget_kaiin




// 物件カウント表示
function fudo_widgetInit_bukkensu() {
	register_widget('fudo_widget_bukkensu');
}
add_action('widgets_init', 'fudo_widgetInit_bukkensu');

// 物件カウント表示ウィジェット
class fudo_widget_bukkensu extends WP_Widget {

	/** constructor */
	function fudo_widget_bukkensu() {
		parent::WP_Widget(false, $name = '物件カウント表示');
	}

	/** @see WP_Widget::form */	
	function form($instance) {

		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
	//	$caution = isset($instance['caution']) ?  $instance['caution'] : '';
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">
		<?php _e('title'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

		<?php 
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		// outputs the content of the widget
	        extract( $args );

		$title = '';
	        $title = apply_filters('widget_title', $instance['title']);
	//	$caution = isset($instance['caution']) ? $instance['caution'] : '';

		if(is_home()) {
			$redirect_to = get_bloginfo('url');
		}else{
		//	$redirect_to = get_permalink();
			$redirect_to = $_SERVER['REQUEST_URI'];
		}

		echo "\n";
		echo $before_widget;

		if ( $title != '')
			echo $before_title . $title . $after_title; 

		//物件カウント
		global $wpdb;
		//$num_posts2 = wp_count_posts( 'fudo' );

		$sql = "SELECT count(DISTINCT P.ID) as co";
		$sql .=  " FROM $wpdb->posts AS P";
		$sql .=  " WHERE P.post_status='publish' AND P.post_password = ''  AND P.post_type ='fudo' ";

		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		$num_posts2 = $metas->co;


		$sql = "SELECT count(DISTINCT P.ID) as co";
		$sql .=  " FROM $wpdb->posts AS P";
		$sql .=  " INNER JOIN $wpdb->postmeta AS PM ON P.ID = PM.post_id ";
		$sql .=  " WHERE P.post_status='publish' AND P.post_password = ''  AND P.post_type ='fudo' ";
		$sql .=  " AND PM.meta_key='kaiin' AND PM.meta_value = '1'";

		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );

		$kaiin_co = $metas->co;	
		$ippan_co = $num_posts2 - $kaiin_co;

	//	echo '<div>';
		echo '　一般公開物件：<span style="color:#FF0000;">'.$ippan_co.'件</span><br />';
		echo '　会員限定物件：<span style="color:#FF0000;">'.$kaiin_co.'件</span>';
	//	echo '</div>';

		echo $after_widget;
		echo "\n";

	}
} // fudo_widget_kaiin


//差出人変更
if ( !class_exists('fudoukaiin_wp_mail_from') ) {

	class fudoukaiin_wp_mail_from {

		function fudoukaiin_wp_mail_from() {
			$kaiin_users_mail_fromname = get_option('kaiin_users_mail_fromname');
			$kaiin_users_mail_frommail = get_option('kaiin_users_mail_frommail');
			if($kaiin_users_mail_frommail !='' )
				add_filter( 'wp_mail_from', array(&$this, 'fb_mail_from') );
			if($kaiin_users_mail_fromname !='' )
				add_filter( 'wp_mail_from_name', array(&$this, 'fb_mail_from_name') );
		}

		 // new name
		function fb_mail_from_name() {
			$kaiin_users_mail_fromname = get_option('kaiin_users_mail_fromname');
			$name = $kaiin_users_mail_fromname;
			// alternative the name of the blog
			// $name = get_option('blogname');
			$name = esc_attr($name);
			return $name;
		}

		 // new email-adress
		function fb_mail_from() {
			$kaiin_users_mail_frommail = get_option('kaiin_users_mail_frommail');
			$email = $kaiin_users_mail_frommail;
			$email = is_email($email);
			return $email;
		}
	}
//	$wp_mail_from = new fudoukaiin_wp_mail_from();
}

//Copyright
function fudoukaiin_footer_post() {
    echo "<!-- FUDOU_KAIIN_VERSION " . FUDOU_KAIIN_VERSION . " -->\n";
}
add_filter( 'wp_footer', 'fudoukaiin_footer_post' );
?>
