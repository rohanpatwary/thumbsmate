<?php
/*
Plugin Name: Fudousan Mail Plugin
Plugin URI: http://nendeb.jp/
Description: Fudousan Mail Plugin for Real Estate
Version: 1.3.5
Author: nendeb
Author URI: http://nendeb.jp/
*/

// Define current version constant
define( 'FUDOU_MAIL_VERSION', '1.3.5' );


/*  Copyright 2012 nendeb (email : nendeb@gmail.com )

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

require_once 'admin_fudoumailedit.php';
require_once 'admin_fudoumailsend.php';
require_once 'fudoumail-register.php';
require_once 'fudoumailsend.php';




//不動産プラグインチェック
function fudou_active_plugins_check_mail(){
	global $is_fudouktai,$is_fudoumap,$is_fudoukaiin,$is_fudoumail;
	$fudo_active_plugins = get_option('active_plugins');
	if(is_array($fudo_active_plugins)) {
		foreach($fudo_active_plugins as $meta_box){
			if( $meta_box == 'fudouktai/fudouktai.php') $is_fudouktai=true;
			if( $meta_box == 'fudoumap/fudoumap.php') $is_fudoumap=true;
			if( $meta_box == 'fudoukaiin/fudoukaiin.php') $is_fudoukaiin=true;
			if( $meta_box == 'fudoumail/fudoumail.php') $is_fudoumail=true;
		}
	}
}
add_action('init', 'fudou_active_plugins_check_mail');



//メール送信 cron
function my_add_weekly( $schedules ) {
	$schedules['weekly'] = array(
		'interval' => 604800,
		'display' => __('Once Weekly')
	);
	return $schedules;
}
add_filter('cron_schedules', 'my_add_weekly');


add_action('users_mail_cron', 'users_mail_cron_do');

function users_mail_activation() {
	global $is_fudouktai,$is_fudoumap,$is_fudoukaiin,$is_fudoumail;

	if ( !wp_next_scheduled( 'users_mail_cron' ) && $is_fudoumail && $is_fudoukaiin ) {
		if( get_option('user_mail_cron_daily') == '0' ){
			wp_schedule_event(time(), 'weekly', 'users_mail_cron');
		}
		if( get_option('user_mail_cron_daily') == '1' ){
			wp_schedule_event(time(), 'daily', 'users_mail_cron');
		}
		if( get_option('user_mail_cron_daily') == '2' ){
			wp_schedule_event(time(), 'hourly', 'users_mail_cron');
		}
	}
}
add_action('wp', 'users_mail_activation');


function users_mail_cron_do(){
	global $is_fudouktai,$is_fudoumap,$is_fudoukaiin,$is_fudoumail;

	$user_mail_frommail = get_option('user_mail_frommail');
	if($user_mail_frommail == '')
		$user_mail_frommail = get_option('admin_email');


	$user_mail_sleep = get_option('user_mail_sleep');
	if( $user_mail_sleep == '' ) $user_mail_sleep = 1;
	if (!is_numeric($user_mail_sleep))  $user_mail_sleep = 1;


	if( get_option('user_mail_cron') == '1' ){

		if( $is_fudoumail==true && $is_fudoukaiin==true && get_option('kaiin_users_mail') == '1' ){

			global $wpdb;
			// WordPress sets the correct timezone
			date_default_timezone_set('Asia/Tokyo');
			$send_date = date("Y-m-d H:i:s") ; 

			$sql  = "SELECT DISTINCT U.ID";
			$sql .=  " FROM ($wpdb->users  AS U";
			$sql .=  " INNER JOIN $wpdb->usermeta AS UM   ON U.ID = UM.user_id ) ";
			$sql .=  " INNER JOIN $wpdb->usermeta AS UM2  ON U.ID = UM2.user_id   ";

			$sql .=  " WHERE UM.meta_key  = '".$wpdb->prefix."user_level' AND UM.meta_value ='0'";
			$sql .=  " AND UM2.meta_key  = 'user_mail' AND UM2.meta_value ='1'";

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql, ARRAY_A );

			if(!empty($metas)) {
				foreach ( $metas as $meta ) {
					$user_id_array[] = $meta['ID'];
				}
			}

			//メール送信
			$mail_send_caution = '';
			if(is_array($user_id_array)) {
				$i=0;
				foreach ( $user_id_array as $meta2 ) {
					$user_id= $user_id_array[$i];
					$mail_send_caution .= users_mail_send( $user_id, '') . "\r\n";
					sleep($user_mail_sleep);
					$i++;
				}
			}

			if( $mail_send_caution == ''){
				$mail_send_caution = 'マッチングメールレポート 送信開始時刻 '.$send_date. "\r\n\r\n";
				$mail_send_caution .= "該当の会員ユーザーはありませんでした。\r\n";
			}else{
				$mail_send_caution = 'マッチングメールレポート 送信開始時刻 '.$send_date. "\r\n\r\n" . $mail_send_caution;
			}

			wp_mail($user_mail_frommail, 'マッチングメールレポート', $mail_send_caution);

		}
	}
}
add_action('users_mail_cron', 'users_mail_cron_do');

//Copyright
function user_mail_footer_post() {
    echo "\n<!-- FUDOU_MAIL_VERSION " . FUDOU_MAIL_VERSION . " -->";
}
add_filter( 'wp_footer', 'user_mail_footer_post' );












//会員ユーザーアクセスログ
function add_single_user_count(){
	global $post;
	global $user_ID;

	if ( is_single() ){

		//会員
		$kaiin = 0;
		if( !is_user_logged_in() && get_post_meta( $post->ID, 'kaiin', true) == 1 ) $kaiin = 1;
		//ユーザー別会員物件リスト
		$kaiin_users_rains_register = get_option('kaiin_users_rains_register');
		$kaiin2 = users_kaiin_bukkenlist($post->ID,$kaiin_users_rains_register,get_post_meta($post->ID, 'kaiin', true));

		$ui_id = isset($_GET['ui']) ? $_GET['ui'] : '';
		if( empty($ui_id)  ) $ui_id = $user_ID;

		//日付
		date_default_timezone_set('Asia/Tokyo');
		//$date = date("Y-m-d H:i:s") ; 
		$date = date("Y/m/d") ; 

		//保存期間
		$log_day = get_option('user_accsess_log_day');
		if( empty($log_day) ) $log_day = -28;	//4週間


		//管理者チェック
		$user = wp_get_current_user();
		$user_subscriber = isset($user->allcaps["subscriber"]) ? $user->allcaps["subscriber"] : 0;

		if ( !empty($ui_id) &&  my_custom_kaiin_view('kaiin_title',$kaiin,$kaiin2) && ( $user_subscriber == 1 || isset($_GET['ui']) ) ){

			//user_meta

			//個別 LOG
			$user_accsess_log3 = maybe_unserialize( get_user_meta( $ui_id, 'user_accsess_log3', true) );
			$count_new = 1;
			if (is_array($user_accsess_log3)) {

				foreach ($user_accsess_log3 as $key => $val) {
					if( $key == $date && isset( $val[$post->ID])  ){
						$user_accsess_log3[$date][$post->ID] = $val[$post->ID] + 1;
						$count_new = 0;
					}
				}
			}

			if( $count_new == 1){
				$user_accsess_log3[$date][$post->ID] = '1';
			}

			//$log_day日 以前は削除
			$old_day = date("Y/m/d",strtotime("$log_day day"));
			foreach ($user_accsess_log3 as $key => $val) {
				if( $key < $old_day ){
					unset($user_accsess_log3[$key]);
				}
			}
			update_user_meta( $ui_id, 'user_accsess_log3', maybe_serialize($user_accsess_log3) );


			//post_meta

			//個別 LOG
			$post_accsess_log3 = maybe_unserialize( get_post_meta( $post->ID, 'post_accsess_log3', true) );

			$count_new = 1;
			if (is_array($post_accsess_log3)) {

				foreach ($post_accsess_log3 as $key => $val) {
					if( $key == $date && isset( $val[$ui_id])  ){
						$post_accsess_log3[$date][$ui_id] = $val[$ui_id] + 1;
						$count_new = 0;
					}
				}
			}

			if( $count_new == 1){
				$post_accsess_log3[$date][$ui_id] = '1';
			}

			//$log_day日 以前は削除
			$old_day = date("Y/m/d",strtotime("$log_day day"));
			foreach ($post_accsess_log3 as $key => $val) {
				if( $key < $old_day ){
					unset($post_accsess_log3[$key]);
				}
			}
			update_post_meta( $post->ID, 'post_accsess_log3', maybe_serialize($post_accsess_log3) );

		}
	}

}
add_action('wp_head', 'add_single_user_count');


















//物件管理画面一覧表示(項目)
function my_fudo_single_user_columns($columns){

	//保存期間
	$log_day = get_option('user_accsess_log_day');
	if( !empty($log_day) )
	$columns += array(
		'single_user_count' => '閲覧済会員',
	);
	return $columns;
}
add_filter('manage_edit-fudo_columns', 'my_fudo_single_user_columns');

//物件管理画面一覧表示(データー)
function my_fudo_single_user_column($column){

	global $post;
	global $wpdb;
	$user_array = array();

	if('single_user_count' == $column){

		$post_id = $post->ID;

		//保存期間
		$log_day = get_option('user_accsess_log_day');
		if( empty($log_day) ) $log_day = -28;	//4週間
		$tmp_day = date("Y/m/d",strtotime("$log_day day"));


		//個別 LOG
		$post_accsess_log3 = maybe_unserialize( get_post_meta( $post_id, 'post_accsess_log3', true) );

		if (is_array($post_accsess_log3)) {
			//sort
			krsort($post_accsess_log3);

			foreach ($post_accsess_log3 as $key => $val) { //$key:日付

				if( $tmp_day < $key ){

					foreach ($val as $key2 => $val2) {

						$tmp_a = 0;
						foreach ($user_array as $key3 => $val3) {
							if( $val3 == $key2 ){
								$tmp_a = 1;
							//	brake;
							}
						}

						if( $tmp_a == 0 ){
						
							$sql = "SELECT U.user_login FROM $wpdb->users AS U WHERE U.ID = " . $key2;
							$sql = $wpdb->prepare($sql,'');
							$metas = $wpdb->get_row( $sql );
							if(!empty($metas)){
								$user_login = $metas->user_login;
							}else{
								$user_login = '';
							}

							if( $user_login != '' ){
							//	echo '<a href="'.WP_PLUGIN_URL.'/fudoumail/fudoumailview_post_user.php?p='.$post_id.'&user_id='.$key2.'&KeepThis=true&TB_iframe=true&height=500&width=500" class="thickbox">';
							//	echo $user_login . '</a>　' ;
								echo $user_login . '　' ;
							}
							$user_array[] = $key2;
						}
					}
				}
			}
			echo '<div style="float:right"><a href="'.WP_PLUGIN_URL.'/fudoumail/fudoumailview_post.php?p='.$post_id.'&KeepThis=true&TB_iframe=true&height=500&width=500" class="thickbox">..more</a></div>';
		}
	}
}
add_action('manage_posts_custom_column', 'my_fudo_single_user_column');














//ユーザー管理画面一覧表示(データー)
function my_fudo_login_date_user_columns($columns){
	$columns += array(
		'single_login_date_count' => '最終ログイン日[数]',
	);
	return $columns;
}
add_filter('manage_users_columns', 'my_fudo_login_date_user_columns');

//物件管理画面一覧表示(データー)
function my_fudo_login_date_user_column($custom_column,$column_name,$user_id) {

	if('single_login_date_count' == $column_name){
		$custom_column .= get_user_meta( $user_id, 'login_date', true);

		$login_count = get_user_meta( $user_id, 'login_count', true);
		$custom_column .= ' ['.$login_count . ']';

		$ipaddress = get_user_meta( $user_id, 'ipaddress', true);
		$useragent = get_user_meta( $user_id, 'useragent', true);
		if( !empty($ipaddress) ) $custom_column .= '<br />[' . $ipaddress . ']';
		if( !empty($useragent) ) $custom_column .= ' ' . $useragent;

	}
	return $custom_column;
}
add_action('manage_users_custom_column', 'my_fudo_login_date_user_column',10,3);






//ユーザー管理画面一覧表示(項目)
function my_fudo_single_post_column($columns) {

	//保存期間
	$log_day = get_option('user_accsess_log_day');
	if( !empty($log_day) )
		$columns += array(
			'single_post_count' => '閲覧済物件',
		);

	return $columns;
}
add_filter('manage_users_columns', 'my_fudo_single_post_column');

//ユーザー管理画面一覧表示(データー)
function my_fudo_single_post_columns($custom_column,$column_name,$user_id) {
	global $post;
	$user_array = array();

	if('single_post_count' == $column_name){

		//保存期間
		$log_day = get_option('user_accsess_log_day');
		if( empty($log_day) ) $log_day = -28;	//4週間
		$tmp_day = date("Y/m/d",strtotime("$log_day day"));

		//個別 LOG
		$user_accsess_log3 = maybe_unserialize( get_user_meta( $user_id, 'user_accsess_log3', true) );

		if (is_array($user_accsess_log3)) {

			//sort
			krsort($user_accsess_log3);

			$i=0;
			foreach ($user_accsess_log3 as $key => $val) {	//$key:日付

				if( $tmp_day < $key ){

					if( $i < 5 ){
						foreach ($val as $key2 => $val2) {

							$tmp_a = 0;
							foreach ($user_array as $key3 => $val3) {
								if( $val3 == $key2 ){
									$tmp_a = 1;
								//	brake;
								}
							}
							if( $tmp_a == 0 ){
								$post_id_array = get_post( $key2 ); 
								if(!empty($post_id_array)) $title = $post_id_array->post_title; else $title = '';

								if( $title != '' ){
									$custom_column .= '<a href="' . get_permalink($key2) . '" title="" target="_blank" style="font-size: 10px;">['. get_post_meta( $key2, 'shikibesu', true) . '] ' .  $title . '</a>' ;	//ID->title
									if( get_post_meta($key2, 'kaiin', true) == 1 )
									$custom_column .= ' <span><img src="'. WP_PLUGIN_URL . '/fudou/img/kaiin_s.jpg" alt="" width="23" /></span>';
									$custom_column .= '<br />' ;
									$user_array[] = $key2;
									$i++;
								}
							}
						}
					}
				}


			}
			$custom_column .= '<div style="float:right"><a href="'.WP_PLUGIN_URL.'/fudoumail/fudoumailview_user.php?user_id='.$user_id.'&KeepThis=true&TB_iframe=true&height=500&width=500" class="thickbox">more</a></div>';
		}

	}
	return $custom_column;
}
add_action('manage_users_custom_column',  'my_fudo_single_post_columns',10,3);











?>
