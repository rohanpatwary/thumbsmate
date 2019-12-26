<?php
/*
Plugin Name: QD User Management System
Description: ユーザー管理システム。メルマガ配信など
Version: 1.0
Author: クオンツデザイン
Author URI: http://quantsdesign.com/
*/
function qd_user_manage_system() {
	add_menu_page('条件メール送信', '条件メール送信', 5,WP_PLUGIN_DIR.'/qd-user-manage/main.php');
 	add_submenu_page(WP_PLUGIN_DIR.'/qd-user-manage/main.php', '物件メール送信', '物件メール送信',5,'/tools.php?page=fudoumail/admin_fudoumailsend.php');
}
add_action('admin_menu', 'qd_user_manage_system');




?>