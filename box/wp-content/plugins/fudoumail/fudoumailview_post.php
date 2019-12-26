<?php
/**
 * User administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 * Version: 1.3.3
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
<script  type="text/javascript">
<!-- <![CDATA[
	function confirm_mdcron() {
		res = confirm("この物件のアクセスログをリセット(削除)します。\nリセット(削除)すると元には戻りません。\nよろしいですか？");

		if (res == true) {
			return true;
		} else {
			return false;
		}
	}
// ]]> -->
</script>

</head>
<body>
<div id="your-profile" style="margin:0 0 20px 10px;">
<?php

	//削除
	$delete_mode = 0;

	// WordPress sets the correct timezone
	date_default_timezone_set('Asia/Tokyo');


	//保存期間
	$log_day = get_option('user_accsess_log_day');
	if( empty($log_day) ) $log_day = -28;
	$end_tmp_day = date("Y/m/d",strtotime("$log_day day"));



	//表示期間
	$back_day = -13;
	$back_today =  0;

	$post_id = isset($_GET['p']) ? myIsNum_f($_GET['p']) : '';
	$page_id = isset($_GET['pg']) ? myIsNum_f($_GET['pg']) : '';


	if( !empty($post_id) ) $post_id_array = get_post( $post_id ); 
	if( !empty($post_id_array) ) $post_title = '['. get_post_meta( $post_id, 'shikibesu', true) . '] ' .$post_id_array->post_title; else $post_title = '';


	if( !empty($page_id ))
		$back_today = $page_id * (-14);


	$post_reset = isset($_POST['post_reset']) ? myIsNum_f($_POST['post_reset']) : '';
	$post_reset_id = isset($_POST['post_id']) ? myIsNum_f($_POST['post_id']) : '';

	if( !empty($post_reset) && !empty($post_reset_id)  ){
		delete_post_meta( $post_reset_id, 'post_accsess_log3', false );
	}


	//google chart
	if(!empty($post_id)){

		$post_accsess_log3 = maybe_unserialize( get_post_meta( $post_id, 'post_accsess_log3', true) );

		if (is_array($post_accsess_log3)) {

			//sort
			krsort($post_accsess_log3);

			$chd = '';
			$chxl_x = '|';

			$d = $back_day + $back_today ;
			$max_count = 0;
			while ($d <= $back_today ) {
				$tmp_day = date("Y/m/d",strtotime("$d day"));
				$post_count = 0;

				foreach ($post_accsess_log3 as $key => $val) {	//$key:日付

					if( $end_tmp_day < $key ){
						foreach ($val as $key2 => $val2) {
							if($tmp_day == $key )	//date
								$post_count += $val2;
						}
					}
				}

				if($max_count < $post_count ) $max_count = $post_count;

				if($chd != '') $chd .= ",";
				$chd .= $post_count;

				$chxl_x .= date("n/d",strtotime("$d day")) . '|';
				$d++;
			}


			//title
			$before_link = '';
			$next_link = '';


			if( empty($page_id )){
				$before_link = '<a href="fudoumailview_post.php?p='.$post_id.'&pg=1" >< </a>';
			}else{
				$next_link =   '<a href="fudoumailview_post.php?p='.$post_id.'&pg=' . ($page_id - 1) . '"> ></a>';
				if($log_day < ($page_id + 1) * (-14) )
				$before_link = '<a href="fudoumailview_post.php?p='.$post_id.'&pg=' . ($page_id + 1) . '">< </a>';
			}


			$d = $back_day + $back_today ;
			$title = '会員アクセス '. $before_link . date("Y/m/d",strtotime("$d day")) . '～' . $tmp_day. $next_link ;


			$chds='0,100';
			$chxl_y = '|0|10|20|30|40|50|60|70|80|90|100';

			if( $max_count <= 10 ){ $chxl_y = '|0|5|10'; $chds='0,10'; }
			if( $max_count > 10 &&  $max_count <= 50 ){ $chxl_y = '|0|10|20|30|40|50'; $chds='0,50'; }
			if( $max_count > 50 &&  $max_count <= 100 ){ $chxl_y = '|0|10|20|30|40|50|60|70|80|90|100'; $chds='0,100'; }
			if( $max_count > 100 &&  $max_count <= 500 ){ $chxl_y = '|0|100|200|300|400|500'; $chds='0,500'; }
			if( $max_count > 500 ){ $chxl_y = '|0|max'; $chds=''; }

			$tmp_code = '?';
			$tmp_code .= 'chs=450x200';
			$tmp_code .= '&cht=lc';
			$tmp_code .= '&chco=ff0000';
			$tmp_code .= '&chds=' . $chds . '';
			$tmp_code .= '&chxl=0:' . $chxl_x . '1:' . $chxl_y . '';
			$tmp_code .= '&chd=t:' . $chd . '';
			$tmp_code .= '&chg=7.7,10,1,5';
			$tmp_code .= '&chxt=x,y';
			$tmp_code .= '&chm=x,FF0000,0,-1,5,-1';
			//$tmp_code .= '&chtt=' . $title ;

			echo '<h3>' . $title . '</h3>';
			echo '　' .  $post_title;

			echo '<img src="http://chart.apis.google.com/chart'.$tmp_code.'" alt="" />';
		}
	}



	//data
	echo '<h3>日付別会員アクセス</h3>';
	echo '<table id="table-01">';
	echo '<tr>';
	echo '<th>アクセス日</th>';
	echo '<th>ユーザー</th>';
	echo '<th>アクセス数</th>';
	echo '</tr>';


	if(!empty($post_id)){

		$post_accsess_log3 = maybe_unserialize( get_post_meta( $post_id, 'post_accsess_log3', true) );

		if (is_array($post_accsess_log3)) {

			//sort
			krsort($post_accsess_log3);

			//表示期間
			$d = $back_day + $back_today ;
			$tmp_day = date("Y/m/d",strtotime("$d day"));
			$tmp_today = date("Y/m/d",strtotime("$back_today day"));


			foreach ($post_accsess_log3 as $key => $val) {

				if( $key >= $tmp_day && $key <= $tmp_today ){

					foreach ($val as $key2 => $val2) {
						$sql = "SELECT U.user_login FROM $wpdb->users AS U WHERE U.ID = " . $key2;
						$sql = $wpdb->prepare($sql,'');
						$metas = $wpdb->get_row( $sql );
						if( !empty($metas) ){
							$user_login = $metas->user_login;
						}else{
							$user_login = '-';
						}

						echo '<tr>';
						echo '<td>' . $key   . '</td>' ;	//date
						echo '<td>' . $user_login  . '</td>' ;	//UID->name
						echo '<td>' . $val2  . '</td>' ;	//count
						echo '</tr>';

					}

				}
			}
		}
	}
	echo '</table>';
	echo '</div>';




	//Log Delete
	if( !empty($post_reset) && !empty($post_reset_id)  ){
		$post_accsess_log3 = maybe_unserialize( get_post_meta( $post_reset_id, 'post_accsess_log3', true) );
		if (!is_array($post_accsess_log3)) {
			echo '<div id="your-profile" style="margin:0 0 20px 10px;">';
			echo '<br />　　リセット(削除)しました。';
			echo '</div>';
		}
	}else{
		$post_accsess_log3 = maybe_unserialize( get_post_meta( $post_id, 'post_accsess_log3', true) );
		if ( is_array($post_accsess_log3) && $delete_mode ) {
?>
			<div id="your-profile" style="margin:0 0 20px 10px;">
				<form name="auto_send" method="post" action="fudoumailview_post.php" onsubmit="return confirm_mdcron()">
				<input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
				<input type="hidden" name="post_reset" value="1" />
				<p class="submit" style="margin:20px 0 0 0;"><input type="submit" name="" id="submit" class="button-primary" value="アクセスログ リセット"  /></p>
			</div>
<?php
		}
	}
?>


</body>
</html>
