<?php
/*
 * 不動産マッチングメールプラグイン
 * @package WordPress3.5
 * @subpackage Fudousan Plugin
 * Fudousan mail Plugin
 * Version: 1.3.3
*/


//メール送信
function users_mail_send($user_mail_ID,$send_mode){
	global $wpdb;

	$mail_comment = users_mail_bukkenlist($user_mail_ID,$send_mode);

	//メール送信

		$mail_send_ok = '未送信';

		//ヘッダー
		$user_mail_fromname = get_option('user_mail_fromname');
		$user_mail_frommail = get_option('user_mail_frommail');
		if($user_mail_frommail == '')
			$user_mail_frommail = get_option('admin_email');

		if($user_mail_fromname == '')
			$user_mail_fromname = $user_mail_frommail;

		$headers = 'From: '.$user_mail_fromname.' <'.$user_mail_frommail.'>' . "\r\n";

		//メール内容
		$user_mail_subject = get_option('user_mail_subject');
		$user_mail_comment = get_option('user_mail_comment');

		$last_name = get_user_meta( $user_mail_ID, 'last_name', true);
		$first_name = get_user_meta( $user_mail_ID, 'first_name', true);

		$sql = "SELECT U.user_login ,  U.user_email ";
		$sql .=  " FROM $wpdb->users AS U";
		$sql .=  " WHERE U.ID = " . $user_mail_ID;
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		$user_login = $metas->user_login;
		$user_email = $metas->user_email;

		$user_mail_comment = str_replace("[mail]", $user_email , $user_mail_comment);
		$user_mail_comment = str_replace("[name]", $last_name . ' ' . $first_name , $user_mail_comment);
		$user_mail_comment = str_replace("[bukken]", $mail_comment, $user_mail_comment);


		//メール送信
		if($send_mode == 'view'){

			echo 'To: ' . $user_email . '<br />';
			echo $headers . '<br />';
			echo 'Subject: ' . $user_mail_subject . '<br />';
			echo '<hr />';
			echo str_replace("\r\n", "<br />", $user_mail_comment);
			echo "<br />";

		}else{
			if( isset($_GET['sendmailmaga']) && $_GET['sendmailmaga'] == '1'){

				if($user_email !=''){
					wp_mail($user_email, $user_mail_subject, $user_mail_comment,$headers);
					//メール送信カウント更新
					$mail_count = get_user_meta( $user_mail_ID, 'mail_count', true);
					if($mail_count !=''){
						update_user_meta( $user_mail_ID, 'mail_count', $mail_count + 1 );
					}else{
						update_user_meta( $user_mail_ID, 'mail_count', '1' );
					}
					$mail_send_ok = '送信しました';
				}

			}else{

				if( $mail_comment !=''){
					if($user_email !=''){
						wp_mail($user_email, $user_mail_subject, $user_mail_comment,$headers);

						//メール日付更新
						date_default_timezone_set('Asia/Tokyo');
						$date = date("Y-m-d H:i:s") ; 
						update_user_meta( $user_mail_ID, 'mail_date', $date );


						//メール送信カウント更新
						$mail_count = get_user_meta( $user_mail_ID, 'mail_count', true);
						if($mail_count !=''){
							update_user_meta( $user_mail_ID, 'mail_count', $mail_count + 1 );
						}else{
							update_user_meta( $user_mail_ID, 'mail_count', '1' );
						}
						$mail_send_ok = '送信しました';
					}
				}
			}

			return  '* ' . $last_name . '　' . $first_name . '　' .$user_login . '　' . $user_email . '　' . $mail_send_ok .  '　'. $mail_comment ."\r\n" ;
		}
}




//ユーザー別物件リスト
function users_mail_bukkenlist($user_mail_ID,$send_mode){

	global $wpdb;
	$next_sql = true;

	$mail_date_data = "";
	$mail_date = get_user_meta( $user_mail_ID, 'mail_date', true);
	if( $mail_date != '') 
		$mail_date_data = " AND P.post_modified >= CAST( '".$mail_date."' AS DATETIME) ";

	$user_mail_kaiin = get_option('user_mail_kaiin');
	if($user_mail_kaiin == '') $user_mail_kaiin = 0;

	$user_mail_bukkenlimit = get_option('user_mail_bukkenlimit');
	if($user_mail_bukkenlimit == '') $user_mail_bukkenlimit = 20;


	//条件種別
		$user_mail_shu = maybe_unserialize( get_user_meta( $user_mail_ID, 'user_mail_shu', true) );

		if (is_array($user_mail_shu)) {
			$i=0;
			$shu_data = ' IN ( 0 ';
			foreach($user_mail_shu as $meta_set){
				$shu_data .= ','. $user_mail_shu[$i] . '';
				$i++;
			}
			$shu_data .= ') ';

			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM $wpdb->posts AS P";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM ON P.ID = PM.post_id ";
			$sql .=  " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=  " AND PM.meta_key='bukkenshubetsu' AND PM.meta_value ".$shu_data."";
			$sql .=   $mail_date_data;

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			$id_data = '';
			if(!empty($metas)) {
				$id_data = ' AND P.ID IN ( 0 ';
				foreach ( $metas as $meta ) {
						$id_data .= ','. $meta['ID'];
				}
				$id_data .= ') ';

			}else{
				$next_sql = false;
			}
		}


	//echo '<br />条件種別 ';
	//echo $id_data;


	//成約 3:空無/売止 4:成約 9:削除 成約日

		if( $next_sql ){

			$not_id_data = ' AND P.ID NOT IN ( 0 ';

			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM $wpdb->posts AS P";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM   ON P.ID = PM.post_id ";
			$sql .=  " WHERE  P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=  " AND PM.meta_key='seiyakubi' AND PM.meta_value != '' ";
			$sql .=    $mail_date_data;
			$sql .=    $id_data;
			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			if(!empty($metas)) {
				foreach ( $metas as $meta ) {
						$not_id_data .= ','. $meta['ID'];
				}
			}

			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM $wpdb->posts AS P";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM_1 ON P.ID = PM_1.post_id ";
			$sql .=  " WHERE  P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=  " AND PM_1.meta_key='jyoutai' AND PM_1.meta_value >= 3 ";
			$sql .=    $mail_date_data;
			$sql .=    $id_data;
			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			if(!empty($metas)) {
				foreach ( $metas as $meta ) {
						$not_id_data .= ','. $meta['ID'];
				}
			}

			$not_id_data .= ') ';


			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM $wpdb->posts AS P";
			$sql .=  " WHERE  P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=    $mail_date_data;
			$sql .=    $id_data;
			$sql .=    $not_id_data;

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			$id_data = '';
			if(!empty($metas)) {
				$id_data = ' AND P.ID IN ( 0 ';
				foreach ( $metas as $meta ) {
						$id_data .= ','. $meta['ID'];
				}
				$id_data .= ') ';
			}else{
				$next_sql = false;
			}
		}


	//条件エリア
		$user_mail_sik = maybe_unserialize( get_user_meta( $user_mail_ID, 'user_mail_sik', true) );

		if(is_array( $user_mail_sik ) && $next_sql ){
			$i=0;
			$sik_data = ' IN ( 0 ';
			foreach($user_mail_sik as $meta_set){
				$sik_data .= ','. $user_mail_sik[$i] . '';
				$i++;
			}
			$sik_data .= ') ';

			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM $wpdb->posts AS P";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM ON P.ID = PM.post_id ";
			$sql .=  " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=  " AND PM.meta_key='shozaichicode' AND PM.meta_value ".$sik_data."";
			$sql .=   $mail_date_data;
			$sql .=   $id_data;

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			$id_data = '';
			if(!empty($metas)) {
				$id_data = ' AND P.ID IN ( 0 ';
				foreach ( $metas as $meta ) {
						$id_data .= ','. $meta['ID'];
				}
				$id_data .= ') ';
			}else{
				$next_sql = false;
			}
		}

	//echo '<br />エリア ';
	//echo $id_data;

	//条件路線駅
		$user_mail_eki = maybe_unserialize( get_user_meta( $user_mail_ID, 'user_mail_eki', true) );


		if(is_array( $user_mail_eki ) && $next_sql ){
			$i=0;
			$eki_data = ' IN ( 0 ';
			foreach($user_mail_eki as $meta_set){
				$eki_data .= ',' . intval(myLeft($user_mail_eki[$i],6)) . intval(myRight($user_mail_eki[$i],6));
				$i++;
			}
			$eki_data .= ') ';

			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM ($wpdb->posts AS P";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM   ON P.ID = PM.post_id) ";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM_1 ON P.ID = PM_1.post_id ";
			$sql .=  " WHERE  P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=    $mail_date_data;
			$sql .=    $id_data;
			$sql .=  " AND PM.meta_key='koutsurosen1' AND PM_1.meta_key='koutsueki1' ";
			$sql .=  " AND PM.meta_value !='' ";
			$sql .=  " AND concat( PM.meta_value,PM_1.meta_value) " . $eki_data . "";


			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			$id_data2 = '';
			if(!empty($metas)) {
				$id_data2 = ' OR (P.ID IN ( 0 ';
				foreach ( $metas as $meta ) {
						$id_data2 .= ','. $meta['ID'];
				}
				$id_data2 .= ')) ';
			}

			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM ($wpdb->posts AS P";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM   ON P.ID = PM.post_id) ";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM_1 ON P.ID = PM_1.post_id ";
			$sql .=  " WHERE ( P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=    $mail_date_data;
			$sql .=    $id_data;
			$sql .=  " AND PM.meta_key='koutsurosen2' AND PM_1.meta_key='koutsueki2' ";
			$sql .=  " AND PM.meta_value !='' ";
			$sql .=  " AND concat( PM.meta_value,PM_1.meta_value) " . $eki_data . ")";
			$sql .=  " " . $id_data2 . "";

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			$id_data = '';
			if(!empty($metas)) {
				$id_data = ' AND P.ID IN ( 0 ';
				foreach ( $metas as $meta ) {
						$id_data .= ','. $meta['ID'];
				}
				$id_data .= ') ';
			}else{
				$next_sql = false;
			}

		}

	//echo '<br />駅 ';
	//echo $id_data;


	//条件価格
		$kalb_data = get_user_meta( $user_mail_ID, 'user_mail_kalb', true);
		$kahb_data = get_user_meta( $user_mail_ID, 'user_mail_kahb', true);
		$kalc_data = get_user_meta( $user_mail_ID, 'user_mail_kalc', true);
		$kahc_data = get_user_meta( $user_mail_ID, 'user_mail_kahc', true);

		if($kalb_data+$kahb_data+$kalc_data+$kahc_data >0 && $next_sql){

			$kalb_data =$kalb_data*10000 ;

			if($kahb_data == '0' ){
				$kahb_data = 1000000000 ;
			}else{
				$kahb_data =$kahb_data*10000 ;
			}

			$kalc_data =$kalc_data*10000 ;

			if($kahc_data == '0' ){
				$kahc_data = 9990000 ;
			}else{
				$kahc_data =$kahc_data*10000 ;
			}


			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM ($wpdb->posts AS P";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM ON P.ID = PM.post_id )";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM_1 ON P.ID = PM_1.post_id ";
			$sql .=  " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=    $mail_date_data;
			$sql .=    $id_data;
			$sql .=  " AND PM_1.meta_key='bukkenshubetsu' AND CAST(PM_1.meta_value AS SIGNED) < 3000";
			$sql .=  " AND PM.meta_key='kakaku'";
			$sql .=  " AND CAST(PM.meta_value AS SIGNED) >= $kalb_data AND CAST(PM.meta_value AS SIGNED) <= $kahb_data  ";

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			$id_data2 = '';
			if(!empty($metas)) {
				$id_data2 = ' AND P.ID IN ( 0 ';
				foreach ( $metas as $meta ) {
						$id_data2 .= ','. $meta['ID'];
				}
				$id_data2 .= ') ';
			}else{
				$next_sql = false;
			}

			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM ($wpdb->posts AS P";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM ON P.ID = PM.post_id )";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM_1 ON P.ID = PM_1.post_id ";
			$sql .=  " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=    $mail_date_data;
			$sql .=    $id_data;
			$sql .=  " AND PM_1.meta_key='bukkenshubetsu' AND CAST(PM_1.meta_value AS SIGNED) > 3000";
			$sql .=  " AND PM.meta_key='kakaku'";
			$sql .=  " AND CAST(PM.meta_value AS SIGNED) >= $kalc_data  AND CAST(PM.meta_value AS SIGNED) <= $kahc_data ";
			$sql .=  " OR ( P.ID " . $id_data2 . ")";

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			$id_data = '';
			if(!empty($metas)) {
				$id_data = ' AND P.ID IN ( 0 ';
				foreach ( $metas as $meta ) {
						$id_data .= ','. $meta['ID'];
				}
				$id_data .= ') ';
			}else{
				$next_sql = false;
			}
		}

	//echo '<br />価格 ';
	//echo $id_data;



	//専有面積
		$tatemo_l_data = get_user_meta( $user_mail_ID, 'user_mail_tatemonomenseki_l', true);
		$tatemo_h_data = get_user_meta( $user_mail_ID, 'user_mail_tatemonomenseki_h', true);

		if( !empty($tatemo_l_data) || !empty($tatemo_h_data) ){

			if( $tatemo_h_data == '0' ) $tatemo_h_data = 9999 ;

			if(( $tatemo_l_data != 0 || $tatemo_h_data != 9999 ) && $id_data !='' ){
				$sql = "SELECT DISTINCT( P.ID )";
				$sql .=  " FROM $wpdb->posts AS P";
				$sql .=  " INNER JOIN $wpdb->postmeta AS PM_2 ON P.ID = PM_2.post_id ";
				$sql .=  " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
				$sql .=    $mail_date_data;
				$sql .=    $id_data;
				$sql .=  " AND PM_2.meta_key='tatemonomenseki'";

				$sql .=  " AND PM_2.meta_value *100 >= $tatemo_l_data*100 ";
				$sql .=  " AND PM_2.meta_value *100 <= $tatemo_h_data*100 ";


				$sql = $wpdb->prepare($sql,'');
				$metas = $wpdb->get_results( $sql,  ARRAY_A );
				$id_data = '';
				if(!empty($metas)) {
					$id_data = ' AND P.ID IN ( 0 ';
					foreach ( $metas as $meta ) {
							$id_data .= ','. $meta['ID'];
					}
					$id_data .= ') ';
				}else{
					$next_sql = false;
				}
			}
		}

	//echo '<br />専有面積 ';
	//echo $id_data;


	//土地面積
		$tochim_l_data = get_user_meta( $user_mail_ID, 'user_mail_tochikukaku_l', true);
		$tochim_h_data = get_user_meta( $user_mail_ID, 'user_mail_tochikukaku_h', true);

		if( !empty($tochim_l_data) || !empty($tochim_h_data) ){

			if( $tochim_h_data  == '0' ) $tochim_h_data = 9999 ;

			if(( $tochim_l_data != 0 || $tochim_h_data != 9999 ) && $id_data !='' ){
				$sql = "SELECT DISTINCT( P.ID )";
				$sql .=  " FROM $wpdb->posts AS P";
				$sql .=  " INNER JOIN $wpdb->postmeta AS PM_2 ON P.ID = PM_2.post_id ";
				$sql .=  " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
				$sql .=    $mail_date_data;
				$sql .=    $id_data;
				$sql .=  " AND PM_2.meta_key='tochikukaku'";
				$sql .=  " AND PM_2.meta_value *100 >= $tochim_l_data*100 ";
				$sql .=  " AND PM_2.meta_value *100 <= $tochim_h_data*100 ";

				$sql = $wpdb->prepare($sql,'');
				$metas = $wpdb->get_results( $sql,  ARRAY_A );
				$id_data = '';
				if(!empty($metas)) {
					$id_data = ' AND P.ID IN ( 0 ';
					foreach ( $metas as $meta ) {
							$id_data .= ','. $meta['ID'];
					}
					$id_data .= ') ';
				}else{
					$next_sql = false;
				}

			}
		}
	//echo '<br />土地面積 ';
	//echo $id_data;





	//条件間取り
		$user_mail_madori = maybe_unserialize( get_user_meta( $user_mail_ID, 'user_mail_madori', true) );

		if(is_array( $user_mail_madori ) && $next_sql ){
			$i=0;
			$madori_data = ' IN ( 0 ';
			foreach($user_mail_madori as $meta_set){
				$madori_data .= ','. $user_mail_madori[$i] . '';
				$i++;
			}
			$madori_data .= ') ';

			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM ($wpdb->posts AS P";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM   ON P.ID = PM.post_id) ";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM_1 ON P.ID = PM_1.post_id ";
			$sql .=  " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=    $mail_date_data;
			$sql .=    $id_data;
			$sql .=  " AND PM.meta_key='madorisu' AND PM_1.meta_key='madorisyurui' ";
			$sql .=  " AND concat( PM.meta_value,PM_1.meta_value) " . $madori_data . "";

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			$id_data = '';
			if(!empty($metas)) {
				$id_data = ' AND P.ID IN ( 0 ';
				foreach ( $metas as $meta ) {
						$id_data .= ','. $meta['ID'];
				}
				$id_data .= ') ';
			}else{
				$next_sql = false;
			}

		}

	//echo '<br />間取り ';
	//echo $id_data;

	//駅歩分
		$hof_data = get_user_meta( $user_mail_ID, 'user_mail_hohun', true);


		if( $hof_data != 0  && $next_sql ){

			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM $wpdb->posts AS P";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM_2   ON P.ID = PM_2.post_id ";
			$sql .=  " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=    $mail_date_data;
			$sql .=    $id_data;
			$sql .=  " AND (PM_2.meta_key='koutsutoho1f' OR PM_2.meta_key='koutsutoho2f' )";
			$sql .=  " AND CAST(PM_2.meta_value AS SIGNED) > 0 ";
			$sql .=  " AND CAST(PM_2.meta_value AS SIGNED) <= $hof_data ";

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			$id_data = '';
			if(!empty($metas)) {
				$id_data = ' AND P.ID IN ( 0 ';
				foreach ( $metas as $meta ) {
						$id_data .= ','. $meta['ID'];
				}
				$id_data .= ') ';
			}else{
				$next_sql = false;
			}
		}

	//echo '<br />歩分 ';
	//echo $id_data;


	//条件設備
		$user_mail_setsubi = maybe_unserialize( get_user_meta( $user_mail_ID, 'user_mail_setsubi', true) );

		if(is_array( $user_mail_setsubi ) && $next_sql ){
			$i=0;
			$setsubi_data = " AND (";
			foreach($user_mail_setsubi as $meta_set){
			//	if($i!=0) $setsubi_data .= " OR ";
				if($i!=0) $setsubi_data .= " AND ";
				$setsubi_data .= " PM.meta_value LIKE '%/". $user_mail_setsubi[$i] . "%' ";
				$i++;
			}
			$setsubi_data .= ")";


			$sql = "SELECT DISTINCT( P.ID )";
			$sql .=  " FROM $wpdb->posts AS P";
			$sql .=  " INNER JOIN $wpdb->postmeta AS PM ON P.ID = PM.post_id ";
			$sql .=  " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=   $mail_date_data;
			$sql .=   $id_data;
			$sql .=  " AND PM.meta_key='setsubi' ".$setsubi_data."";

		//	$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );
			$id_data = '';
			if(!empty($metas)) {
				$id_data = ' AND P.ID IN ( 0 ';
				foreach ( $metas as $meta ) {
						$id_data .= ','. $meta['ID'];
				}
				$id_data .= ') ';
			}else{
				$next_sql = false;
			}

		}

	//echo '<br />設備 ';
	//echo $id_data;


	//日付
	date_default_timezone_set('Asia/Tokyo');
	$date = date("Y/m/d") ; 

	$mail_comment = '';

	//物件データー
		if($id_data !='' && $user_mail_bukkenlimit > 0 ){

			$sql  = "SELECT P.ID,P.post_title,P.post_excerpt";
			$sql .= " FROM $wpdb->posts AS P";

			if($user_mail_kaiin == 1 )
				$sql .=  " INNER JOIN $wpdb->postmeta AS PM ON P.ID = PM.post_id ";

			$sql .= " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo'";
			$sql .=   $id_data;

			if($user_mail_kaiin == 1 )
				$sql .=  " AND PM.meta_key='kaiin' AND PM.meta_value = '1' ";

			$sql .= " ORDER BY P.post_modified DESC";

			if($user_mail_kaiin == 0 )
				$sql .=  " LIMIT ".$user_mail_bukkenlimit."";

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );

			$mail_comment = '';
			if(!empty($metas)) {

				$i = 1;
				foreach ( $metas as $meta ) {

					$post_id =  $meta['ID'];
					$post_title =  $meta['post_title'];
					$post_excerpt =  $meta['post_excerpt'];
					$post_url =  get_permalink($post_id);
					$post_url .=  "&ui=". $user_mail_ID;


					//SEND LOG
					if( empty($send_mode) ){
						users_sendmail_log($post_id,$user_mail_ID,$date);
					}

					if($user_mail_kaiin == 2 && get_post_meta($post_id,'kaiin',true) == '1'){
					
					}else{

						$mail_comment .= "\r\n　";

						//物件番号
						$mail_comment .= '物件番号 ' . get_post_meta($post_id,'shikibesu',true);
						if(get_post_meta($post_id,'kaiin',true)=="1")
							$mail_comment .= '　会員限定物件';
						$mail_comment .= "\r\n　";


						//タイトル
							$mail_comment .= $post_title . '　';

						//価格

							if( get_post_meta($post_id, 'seiyakubi', true) != "" ){
								$mail_comment .= 'ご成約済　';
							}else{
								//非公開の場合
								if(get_post_meta($post_id,'kakakukoukai',true) == "0"){
									$kakakujoutai_data = get_post_meta($post_id,'kakakujoutai',true);
									if($kakakujoutai_data_data=="1")	$mail_comment .= '相談　';
									if($kakakujoutai_data_data=="2")	$mail_comment .= '確定　';
									if($kakakujoutai_data_data=="3")	$mail_comment .= '入札　';

								}else{
									$kakaku_data = get_post_meta($post_id,'kakaku',true);
									if(is_numeric($kakaku_data)){
										$mail_comment .= floatval($kakaku_data)/10000;
										$mail_comment .= "万円　";
									}
								}					
							}


						//間取り

							$madorisyurui_data = get_post_meta($post_id,'madorisyurui',true);
							$mail_comment .= get_post_meta($post_id,'madorisu',true);
							if($madorisyurui_data=="10")	$mail_comment .= 'R ';
							if($madorisyurui_data=="20")	$mail_comment .= 'K ';
							if($madorisyurui_data=="25")	$mail_comment .= 'SK ';
							if($madorisyurui_data=="30")	$mail_comment .= 'DK ';
							if($madorisyurui_data=="35")	$mail_comment .= 'SDK ';
							if($madorisyurui_data=="40")	$mail_comment .= 'LK ';
							if($madorisyurui_data=="45")	$mail_comment .= 'SLK ';
							if($madorisyurui_data=="50")	$mail_comment .= 'LDK ';
							if($madorisyurui_data=="55")	$mail_comment .= 'SLDK ';

							$mail_comment .= "\r\n　";

						//抜粋
							if( $post_excerpt != "" ){
							if( $post_excerpt != " " ){
								$mail_comment .= $post_excerpt;
								$mail_comment .= "\r\n　";
							}
							}

						//所在地
							$shozaichiken_data = get_post_meta($post_id,'shozaichicode',true);
							$shozaichiken_data = myLeft($shozaichiken_data,2);
							$shozaichicode_data = get_post_meta($post_id,'shozaichicode',true);
							$shozaichicode_data = myLeft($shozaichicode_data,5);
							$shozaichicode_data = myRight($shozaichicode_data,3);

							if($shozaichiken_data !="" && $shozaichicode_data !=""){
								$sql = "SELECT narrow_area_name FROM ".$wpdb->prefix."area_narrow_area WHERE middle_area_id=".$shozaichiken_data." and narrow_area_id =".$shozaichicode_data."";

								$sql = $wpdb->prepare($sql,'');
								$metas = $wpdb->get_row( $sql );
								$mail_comment .= "".$metas->narrow_area_name."";
							}
							$mail_comment .= get_post_meta($post_id, 'shozaichimeisho', true);

							$mail_comment .= "\r\n　";

						//交通路線

							$koutsurosen_data = get_post_meta($post_id, 'koutsurosen1', true);
							$koutsueki_data = get_post_meta($post_id, 'koutsueki1', true);
							$shozaichiken_data = get_post_meta($post_id,'shozaichicode',true);
							$shozaichiken_data = myLeft($shozaichiken_data,2);

							if($koutsurosen_data !=""){
								$sql = "SELECT `rosen_name` FROM `".$wpdb->prefix."train_rosen` WHERE `rosen_id` =".$koutsurosen_data."";
								$sql = $wpdb->prepare($sql,'');
								$metas = $wpdb->get_row( $sql );
								$mail_comment .= "".$metas->rosen_name;
							}

							//交通駅
							if($koutsurosen_data !="" && $koutsueki_data !=""){
								$sql = "SELECT DTS.station_name";
								$sql = $sql . " FROM ".$wpdb->prefix."train_rosen AS DTR";
								$sql = $sql . " INNER JOIN ".$wpdb->prefix."train_station as DTS ON DTR.rosen_id = DTS.rosen_id";
								$sql = $sql . " WHERE DTS.station_id=".$koutsueki_data." AND DTS.rosen_id=".$koutsurosen_data."";
								$sql = $wpdb->prepare($sql,'');
								$metas = $wpdb->get_row( $sql );
								$mail_comment .= $metas->station_name.'駅';
							}


							if(get_post_meta($post_id, 'koutsubusstei', true) !="" || get_post_meta($post_id, 'koutsubussfun', true) !=""  )
								$mail_comment .= ' バス('.$koutsubusstei.' '.$koutsubussfun.'分)';

							if(get_post_meta($post_id, 'koutsutoho1', true) !="")
								$mail_comment .= ' 徒歩'.get_post_meta($post_id, 'koutsutoho1', true).'m';

							if(get_post_meta($post_id, 'koutsutoho1f', true) !="")
								$mail_comment .= ' 徒歩'.get_post_meta($post_id, 'koutsutoho1f', true).'分';


						$mail_comment .= "\r\n　";
						$mail_comment .= $post_url;
						$mail_comment .= "\r\n";

						$mail_comment .= "　----------------------------------------------------------\r\n";

						$i++;
					}

					if( $i > $user_mail_bukkenlimit ) break;
				}
			}
		}
		return  $mail_comment;
}



//ユーザー別送信物件LOG
function users_sendmail_log($post_id,$user_id,$date){


			//個別 LOG
			$user_sendmail_log = maybe_unserialize( get_user_meta( $user_id, 'user_sendmail_log', true) );
			$count_new = 1;
			if (is_array($user_sendmail_log)) {

				foreach ($user_sendmail_log as $key => $val) {
					if( $key == $date && isset( $val[$post_id])  ){
						$user_sendmail_log[$date][$post_id] = $val[$post_id] + 1;
						$count_new = 0;
					}
				}
			}

			if( $count_new == 1){
				$user_sendmail_log[$date][$post_id] = '1';
			}
/*
			//１ヶ月以前は削除
			$old_day = date("Y/m/d",strtotime("-1 month"));
			foreach ($user_sendmail_log as $key => $val) {
				if( $key < $old_day ){
					unset($user_sendmail_log[$key]);
				}
			}
*/
			update_user_meta( $user_id, 'user_sendmail_log', maybe_serialize($user_sendmail_log) );

}




?>