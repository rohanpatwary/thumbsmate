<?php
/**
 * Edit user administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 * Version: 1.2.5
 */

/** WordPress Administration Bootstrap */
require_once '../../../wp-admin/admin.php';

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
</head>
<body>
<?php
echo '<font size="2">';
//
	$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
	$send_mode = 'view';
	if(!empty($_GET['user_id'])) users_mail_send($user_id,$send_mode);

//

echo '</font>';
echo '</body>';
echo '</html>';

?>