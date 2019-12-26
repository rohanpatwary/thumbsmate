<?php
/**
 * Edit user administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once '../../../wp-admin/admin.php';

require_once 'fudoumail-register.php';

/*
	global $user_ID;
	$user_mail_ID = $user_ID;
	if($_POST['user_id'] !='') $user_mail_ID = $_POST['user_id'];
*/

global $userdata; 
get_currentuserinfo();   
$user_mail_ID = $userdata->ID;

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link href="f_user.css" rel="stylesheet" type="text/css" media="screen">
</head>
<body>
<?php


//フォーム更新
$user_koushin = fudou_mail_update_profile();


//フォーム
echo '<form id="your-profile" action="" method="post">';
if($user_koushin !='')
	echo  '<div id="message" class="updated"><p><strong>' . $user_koushin . '</strong></p></div>';

fudou_mail_registration_form();
echo '<input type="hidden" name="action" value="update" />';
echo '<input type="hidden" name="user_id" id="user_id" value="' . esc_attr($user_mail_ID). '" />';


echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="設定を更新" /></p>';

echo '</form>';
echo '</body>';
echo '</html>';

?>