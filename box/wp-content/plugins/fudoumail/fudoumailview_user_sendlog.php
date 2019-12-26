<?php
/**
 * User administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 * Version: 1.3.5
 */

/** WordPress Administration Bootstrap */
require_once '../../../wp-admin/admin.php';

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<link href="f_user.css" rel="stylesheet" type="text/css" media="screen">
</head>
<script  type="text/javascript">
<!-- <![CDATA[
	function confirm_mdcron() {
		res = confirm("このユーザーの送信ログをリセット(削除)します。\nリセット(削除)すると元には戻りません。\nよろしいですか？");

		if (res == true) {
			return true;
		} else {
			return false;
		}
	}
// ]]> -->
</script>

<body>
<?php

	$user_id = isset($_GET['user_id']) ? myIsNum_f($_GET['user_id']) : '';

	//保存期間
	$log_day = get_option('user_accsess_log_day');
	if( empty($log_day) ) $log_day = -28;	//4週間
	$tmp_day = date("Y/m/d",strtotime("$log_day day"));

	//削除
	$delete_mode = 0;
	$user_reset = isset($_POST['user_reset']) ? myIsNum_f($_POST['user_reset']) : '';
	$user_reset_id = isset($_POST['user_id']) ? myIsNum_f($_POST['user_id']) : '';


	if( !empty($user_reset) && !empty($user_reset_id)  ){
		delete_user_meta( $user_reset_id, 'user_sendmail_log', false );
	}

	if( !empty($user_id) ){

		//ユーザー名
		$sql = "SELECT U.user_login FROM $wpdb->users AS U WHERE U.ID = " . $user_id;
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if(!empty($metas)){
			$user_login = $metas->user_login;
		}else{
			$user_login = '';
		}


		echo '<div id="your-profile" style="margin:0 0 20px 10px;">';

		//個別 LOG
		$user_sendmail_log = maybe_unserialize( get_user_meta( $user_id, 'user_sendmail_log', true) );

		//個別 LOG
		$user_accsess_log3 = maybe_unserialize( get_user_meta( $user_id, 'user_accsess_log3', true) );

		echo '<h3>マッチングメール送信物件　　'.$user_login.'</h3>';
		echo '<table id="table-01">';
		echo '<tr>';
		echo '<th>送信日</th>';
		echo '<th>送信した物件</th>';
		echo '<th>アクセス数</th>';
		echo '</tr>';

		if (is_array($user_sendmail_log)) {

			//sort
			krsort($user_sendmail_log);
			//sort
			if ( !empty($user_accsess_log3) && is_array($user_accsess_log3) ) krsort($user_accsess_log3);

			foreach ($user_sendmail_log as $key => $val) {	//$key:日付

					foreach ($val as $key2 => $val2) {
						$post_id_array = get_post( $key2 ); 
						if(!empty($post_id_array)) $title = $post_id_array->post_title; else $title = '';

						if( $title != '' ){

							echo '<tr>';
							echo '<td>' . $key   . '</td>' ;	//date
							echo '<td><a href="' . get_permalink($key2) . '" title="" target="_blank">['. get_post_meta( $key2, 'shikibesu', true) . '] ' . $title . '</a>' ;	//ID->title

							if( get_post_meta($key2, 'kaiin', true) == 1 )
							echo  ' <span><img src="'. WP_PLUGIN_URL . '/fudou/img/kaiin_s.jpg" alt="" width="23" /></span>';
						//	echo '　<a href="'.WP_PLUGIN_URL.'/fudoumail/fudoumailview_post_user.php?p='.$key2.'&user_id='.$user_id.'&ref=fudoumailview_user_sendlog">[ログ]</a>' ;
							echo '</td>' ;

							//count
							$tmp_count = 0;
							if ( !empty($user_accsess_log3) && is_array($user_accsess_log3)) {
								foreach ($user_accsess_log3 as $key_b => $val_b) {

									foreach ($val_b as $key_b2 => $val_b2) {

										if ( $key2 == $key_b2 ){		//post_id
											if( $key <= $key_b )		//date
												$tmp_count += $val_b2 ;	//count
										}
									}
								}
							}

							if( $tmp_day < $key ){
								echo '<td>' . $tmp_count . '</td>';	//count
							}else{
								echo '<td>0</td>';	//count
							}

							echo '</tr>';
						}
					}
			}
		}else{
			echo '　データーがありません。';
		}
		echo '</table>';
		echo '</div>';
	}



	//Log Delete
	if( !empty($user_reset) && !empty($user_reset_id)  ){
		$user_sendmail_log = maybe_unserialize( get_user_meta( $user_reset_id, 'user_sendmail_log', true) );
		if (!is_array($user_sendmail_log)) {
			echo '<div id="your-profile" style="margin:0 0 20px 10px;">';
			echo '<br />　　リセット(削除)しました。';
			echo '</div>';
		}
	}else{
		if ( is_array($user_sendmail_log) && $delete_mode ) {
?>
			<div id="your-profile" style="margin:0 0 20px 10px;">
				<form name="auto_send" method="post" action="fudoumailview_user_sendlog.php" onsubmit="return confirm_mdcron()">
				<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
				<input type="hidden" name="user_reset" value="1" />
				<p class="submit" style="margin:20px 0 0 0;"><input type="submit" name="" id="submit" class="button-primary" value="送信ログ リセット"  /></p>
			</div>
<?php
		}
	}
?>


</body>
</html>
