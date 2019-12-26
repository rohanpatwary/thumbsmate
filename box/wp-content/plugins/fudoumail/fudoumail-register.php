<?php
/*
 * 不動産マッチングメールプラグイン
 * @package WordPress3.5
 * @subpackage Fudousan Plugin
 * Fudousan mail Plugin
 * Version: 1.3.4
*/



//フォームを更新したときのアクション
function fudou_mail_update_profile(){
	global $user_ID;
	global $wpdb;

	$mail_coution = '';

	$user_mail_ID = $user_ID;
	if(isset($_POST['user_id'])) $user_mail_ID = $_POST['user_id'];

	if(isset($_POST['action'])){


		if(isset($_POST['user_mail_name'])) $user_mail_name = $_POST['user_mail_name'];
		if(isset($_POST['user_mail_name2'])) $user_mail_name2 = $_POST['user_mail_name2'];



		if( !empty($user_mail_name) && !empty($user_mail_ID) ){

			if( $user_mail_name == $user_mail_name2 ){
				$wpdb->update( $wpdb->users, array( 'user_email' => $user_mail_name ), array( 'ID' => $user_mail_ID ), array( '%s' ), array( '%d' ) );
				//wp_update_user( array ('ID' => $user_mail_ID, 'user_email' => $user_mail_name) ) ;
			}else{
				$mail_coution = '<font color="#FF2200">もう一度、メールアドレスの設定をお願いします。</font>';
			}
		}


		//配信可否
		update_user_meta($user_mail_ID, "user_mail", isset($_POST["user_mail"]) ? $_POST["user_mail"] : '');

		//種別
		update_user_meta($user_mail_ID, "user_mail_shu", isset($_POST["shu"]) ? maybe_serialize($_POST["shu"]) : '' );

		//市区
		update_user_meta($user_mail_ID, "user_mail_sik", isset($_POST["sik"]) ? maybe_serialize($_POST["sik"]) : '' );

		//駅
		update_user_meta($user_mail_ID, "user_mail_eki", isset($_POST["eki"]) ? maybe_serialize($_POST["eki"]) : '' );


		//条件価格
		update_user_meta($user_mail_ID, "user_mail_kalb", isset($_POST["kalb"]) ? $_POST["kalb"] : '' );
		update_user_meta($user_mail_ID, "user_mail_kahb", isset($_POST["kahb"]) ? $_POST["kahb"] : '' );
		update_user_meta($user_mail_ID, "user_mail_kalc", isset($_POST["kalc"]) ? $_POST["kalc"] : '' );
		update_user_meta($user_mail_ID, "user_mail_kahc", isset($_POST["kahc"]) ? $_POST["kahc"] : '' );


		//面積
		update_user_meta($user_mail_ID, "user_mail_tatemonomenseki_l", isset($_POST["tatemo_l"]) ? $_POST["tatemo_l"] : '' );
		update_user_meta($user_mail_ID, "user_mail_tatemonomenseki_h", isset($_POST["tatemo_h"]) ? $_POST["tatemo_h"] : '' );
		update_user_meta($user_mail_ID, "user_mail_tochikukaku_l",     isset($_POST["tochim_l"]) ? $_POST["tochim_l"] : '' );
		update_user_meta($user_mail_ID, "user_mail_tochikukaku_h",     isset($_POST["tochim_h"]) ? $_POST["tochim_h"] : '' );


		//間取り
		update_user_meta($user_mail_ID, "user_mail_madori", isset($_POST["mad"]) ? maybe_serialize($_POST["mad"]) : '' );

		//歩分
		update_user_meta($user_mail_ID, "user_mail_hohun", isset($_POST["hof"]) ? $_POST["hof"] : '' );

		//条件設備
		update_user_meta($user_mail_ID, "user_mail_setsubi", isset($_POST["set"]) ? maybe_serialize($_POST["set"]) : '' );


		//メール日付リセット
		$user_mail_reset = get_option('user_mail_reset');
		if($user_mail_reset != 1)
			update_user_meta( $user_mail_ID, 'mail_date', '' );


		//手動で登録した場合
		$today = date("Y/m/d");	// 2011/04/01

		if(get_user_meta( $user_mail_ID, 'login_count', true) == '')
			update_user_meta( $user_mail_ID, 'login_count', '0' );

		if(get_user_meta( $user_mail_ID, 'login_date', true) == '')
			update_user_meta( $user_mail_ID, 'login_date', $today );


		if( $mail_coution !=''){
			return $mail_coution;
		}else{
			return '更新しました。';
		}

	}
}
add_action("personal_options_update", "fudou_mail_update_profile");
add_action("profile_update", "fudou_mail_update_profile");




//プロフィール編集画面にフォームを追加
function fudou_mail_registration_form() {
	global $user_ID;

	$user_mail_ID = $user_ID;

	$user_email_name = '';

	if(isset($_GET['user_id'])) $user_mail_ID = $_GET['user_id'];
	if(isset($_POST['user_id'])) $user_mail_ID = $_POST['user_id'];

	if( $user_mail_ID !=''){
		$userdata = new WP_User($user_mail_ID);  
		$user_email_name = $userdata->user_email;
	}

	if( get_option('kaiin_users_rains_register') == 1 ){
		echo '<h3>会員閲覧条件・メール配信設定</h3>';
	}else{
		echo '<h3>メール配信設定</h3>';
	}

	//説明文

	echo '<div style="margin:0 0 20px 10px;">';

	echo get_option('kaiin_users_comment');

	//メール受取許可
	echo "\n";
	echo '<p>';
	echo '物件情報配信<br />';
	echo '<input type="checkbox" name="user_mail" id="user_mail" class="" value="1" tabindex="" ';
	if( get_user_meta( $user_mail_ID, 'user_mail', true) == 1 ) echo ' checked="checked"';
	echo ' /><label for="user_mail">メールを受取る</label>';

	//twitter @pseudo.twitter.com
	$pos = strpos( $user_email_name, '@pseudo.twitter.com' );
	if ($pos !== false) {
		echo '<br /><br /><font color="#FF2200">*物件情報配信希望の場合は メールアドレスを入力してください。 [必須]</font>';
		echo '<br /><label for="user_mail_name">メールアドレス </label><input type="text" name="user_mail_name" id="user_mail_name" class="" value="" /> ';
		echo '<br /><label for="user_mail_name2">もう一度入力　 </label><input type="text" name="user_mail_name2" id="user_mail_name2" class="" value="" /> ';
	}
	echo '</p>';

	echo '</div>';


	if( get_option('kaiin_users_rains_register') == 1 ){
		echo '<h3>閲覧条件・メール条件設定</h3>';
	}else{
		echo '<h3>メール条件設定</h3>';
	}


	echo '<div style="margin:0 0 20px 10px;">';

	//条件種別
			fudou_registration_form_syubetsu($user_mail_ID);

	//条件エリア
			fudou_registration_form_area($user_mail_ID);

	//条件路線駅
			fudou_registration_form_roseneki($user_mail_ID);

	//条件価格
			fudou_registration_form_kakaku($user_mail_ID);

	//面積
			fudou_registration_form_memseki($user_mail_ID);

	//条件間取り
			fudou_registration_form_madori($user_mail_ID);

	//駅歩分
			fudou_registration_form_hofun($user_mail_ID);

	//条件設備
			fudou_registration_form_setsubi($user_mail_ID);

	echo '</div>';

}
add_action("profile_personal_options", "fudou_mail_registration_form");
add_action("edit_user_profile", "fudou_mail_registration_form");


//条件種別
function fudou_registration_form_syubetsu($user_mail_ID) {

	global $wpdb,$work_bukkenshubetsu;
	asort($work_bukkenshubetsu);

	//表示設定
	$kaiin_users_mail_shu =	maybe_unserialize(get_option('kaiin_users_mail_shu'));

	//個人設定
	$user_mail_shu = maybe_unserialize( get_user_meta( $user_mail_ID, 'user_mail_shu', true) );


	$value = '';

	if (is_array($kaiin_users_mail_shu)) {

		foreach($work_bukkenshubetsu as $meta_box){
			$bukkenshubetsu_id = $meta_box['id'];


			if(is_array($kaiin_users_mail_shu)) {

				$i=0;
				foreach($kaiin_users_mail_shu as $meta_box2){

					if($kaiin_users_mail_shu[$i] == $bukkenshubetsu_id) {

						if( myRight($bukkenshubetsu_id,2) == '01' && $bukkenshubetsu_id !='1101' ) $value .= '<br />';

						$value .= '<span style="display: inline-block"><input type="checkbox" name="shu[]" value="'.$bukkenshubetsu_id.'"';

						if(is_array($user_mail_shu)) {
							$k=0;
							foreach($user_mail_shu as $meta_box2){
								if($user_mail_shu[$k] == $bukkenshubetsu_id) $value .= ' checked="checked"';
								$k++;
							}
						}
						$value .= ' id="shu'.$bukkenshubetsu_id.'"><label for="shu'.$bukkenshubetsu_id.'">'.$meta_box['name'].'</label></span>　';



					}

				$i++;
				}
			}
		}
	}

	if($value !=''){
		echo "\n";
		echo '<p>';
		echo 'ご希望の種別を選択して下さい(複数可) <font color="#FF2200"> [必須]</font><br />';
		//echo '<input type="checkbox" name="shu[] "value="1">売買全て　';
		//echo '<input type="checkbox" name="shu[] "value="2">賃貸全て　';
		echo $value;
		echo '</p>';
	}
}

//条件エリア
function fudou_registration_form_area($user_mail_ID) {

	global $wpdb;

	//表示設定
	$kaiin_users_mail_sik = maybe_unserialize( get_option('kaiin_users_mail_sik') );
	if (is_array($kaiin_users_mail_sik)) asort($kaiin_users_mail_sik);

	//個人設定
	$user_mail_sik = maybe_unserialize( get_user_meta( $user_mail_ID, 'user_mail_sik', true) );


	if (is_array($kaiin_users_mail_sik)) {

		echo '<p>';
		echo 'ご希望の地域を選択して下さい(複数可)<br />';


		for( $i=1; $i<48 ; $i++ ){

			$eigyouken_data = get_option('ken'.$i);

			$j=0;
			$ken_data = '';
			$shiku_in_data = '';
			foreach($kaiin_users_mail_sik as $meta_set){

				if($i < 10){
					$ken_data =  myLeft($kaiin_users_mail_sik[$j] ,1);
					$shiku_data = myLeft($kaiin_users_mail_sik[$j],4);
					$shiku_data = myRight($shiku_data,3);
				}else{
					$ken_data =  myLeft($kaiin_users_mail_sik[$j] ,2);
					$shiku_data = myLeft($kaiin_users_mail_sik[$j],5);
					$shiku_data = myRight($shiku_data,3);
				}

				if( $eigyouken_data == $ken_data ){
					$shiku_in_data .= ','. $shiku_data . '';
				}

				$j++;
			}

			if($shiku_in_data != ''){

				$shiku_in_data = ' IN ( 0 ' . $shiku_in_data . ') ';


				//営業県表示
				$sql = "SELECT middle_area_id, middle_area_name FROM ".$wpdb->prefix."area_middle_area WHERE middle_area_id = $eigyouken_data";
				$sql = $wpdb->prepare($sql,'');
				$metas = $wpdb->get_row( $sql );
				$middle_area_name = $metas->middle_area_name;

				echo $middle_area_name . '<br />';


				//市区
				$sql  = "SELECT narrow_area_id,narrow_area_name";
				$sql .= " FROM ".$wpdb->prefix."area_narrow_area";
				$sql .= " WHERE middle_area_id = ".$eigyouken_data."";
				$sql .= " AND narrow_area_id ".$shiku_in_data."";
				$sql .= " ORDER BY narrow_area_id ASC";

				$sql = $wpdb->prepare($sql,'');
				$metas2 = $wpdb->get_results( $sql, ARRAY_A );
				if(!empty($metas2)) {
				
					foreach ( $metas2 as $meta2 ) {
						$shozaichicode = $eigyouken_data . $meta2['narrow_area_id'] .'000000';
						echo '<span style="display: inline-block"><input name="sik[]" value="'. $shozaichicode .'" id="sik'.$shozaichicode.'" type="checkbox"';
								if(is_array($user_mail_sik)) {
									$k=0;
									foreach($user_mail_sik as $meta_box3){
										if($user_mail_sik[$k] == $shozaichicode ) echo ' checked="checked"';
										$k++;
									}
								}

						echo  ' /><label for="sik'.$shozaichicode.'">'.$meta2['narrow_area_name'].'</label></span>　';
					}

				}
				echo '<br />';
			}
		}
		echo '</p>';
	}

}

//条件路線駅
function fudou_registration_form_roseneki($user_mail_ID) {

	global $wpdb;

	//表示設定
	$kaiin_users_mail_eki =	maybe_unserialize(get_option('kaiin_users_mail_eki'));

	//個人設定
	$user_mail_eki = maybe_unserialize( get_user_meta( $user_mail_ID, 'user_mail_eki', true) );


	if (is_array($kaiin_users_mail_eki)) {

		echo '<p>';
		echo 'ご希望の駅を選択して下さい(複数可)<br />';

		//営業県
		$shozaichiken_data = '';
		if($shozaichiken_data==""){
			$shozaichiken_data = '0';
			for( $i=1; $i<48 ; $i++ ){
				if( get_option('ken'.$i) != ''){
					$shozaichiken_data .= ','.get_option('ken'.$i);
				}
			}
		}

		$sql = "SELECT DISTINCT DTR.rosen_id, DTR.rosen_name";
		$sql = $sql . " FROM ".$wpdb->prefix."train_area_rosen AS DTAR";
		$sql = $sql . " INNER JOIN ".$wpdb->prefix."train_rosen AS DTR ON DTAR.rosen_id = DTR.rosen_id";
		$sql = $sql . " WHERE DTAR.middle_area_id in (".$shozaichiken_data.") ";
		$sql = $sql . " ORDER BY DTR.rosen_name";


		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_results( $sql, ARRAY_A );
		if(!empty($metas)) {

			//路線
			foreach ( $metas as $meta ) {
				$tmp_rosen = 0;

			//	$meta_id = $meta['rosen_id'];
				$meta_id = sprintf('%06d', $meta['rosen_id'] );

				$meta_valu = $meta['rosen_name'];
				$i = 0 ;
				foreach($kaiin_users_mail_eki as $meta_set){
					$rosen_data = myLeft($kaiin_users_mail_eki[$i],6);
				//	$eki_data = myRight($kaiin_users_mail_eki[$i],6);
					if($meta_id == $rosen_data){
					//	echo '<br /><input type="checkbox" name="ros[]"value="'.$meta_id.'">'.$meta_valu.'<br />';
						echo ''.$meta_valu.'<br />';
						$tmp_rosen = 1;
						break;
					}
					$i++;
				}

				//駅
				$sql = "SELECT DTS.station_id, DTS.station_name";
				$sql = $sql . " FROM ".$wpdb->prefix."train_station AS DTS";
				$sql = $sql . " WHERE DTS.rosen_id=".$meta['rosen_id']." AND DTS.middle_area_id in (".$shozaichiken_data.")";
				$sql = $sql . " ORDER BY DTS.station_ranking";

				$sql = $wpdb->prepare($sql,'');
				$metas2 = $wpdb->get_results( $sql, ARRAY_A );
				if(!empty($metas2)) {
					foreach ( $metas2 as $meta2 ) {

						$i = 0 ;
						foreach($kaiin_users_mail_eki as $meta_set){
							$rosen_data = myLeft($kaiin_users_mail_eki[$i],6);
							$eki_data = myRight($kaiin_users_mail_eki[$i],6);
							if($meta_id == $rosen_data && $meta2['station_id'] == $eki_data){

								$station_id = $meta_id . ''. sprintf('%06d', $meta2['station_id']);
								
								echo '<span style="display: inline-block"><input type="checkbox" name="eki[]" value="'.$station_id.'" id="eki'.$station_id.'"';
								if(is_array($user_mail_eki)) {
									$k=0;
									foreach($user_mail_eki as $meta_box2){
										$value = $user_mail_eki[$k];
										$k++;

										if($value == $station_id ){
											echo ' checked="checked"';
										}
									}
								}
								echo ' /><label for="eki'.$station_id.'">'.$meta2['station_name'].'</label></span>　';
								break;
							}
							$i++;
						}


					}
				}
				if($tmp_rosen == 1) echo '<br />';
			}
		}

		echo '</p>';
	}


}



//条件価格
function fudou_registration_form_kakaku($user_mail_ID) {

	//個人設定
	$kalb_data = get_user_meta( $user_mail_ID, 'user_mail_kalb', true);
	$kahb_data = get_user_meta( $user_mail_ID, 'user_mail_kahb', true);
	$kalc_data = get_user_meta( $user_mail_ID, 'user_mail_kalc', true);
	$kahc_data = get_user_meta( $user_mail_ID, 'user_mail_kahc', true);


	if( get_option('kaiin_users_mail_kakaku') == 1 ){
		echo "\n";
		echo '<p>';
		echo '価格(売買)<br />';
		echo '<select name="kalb" id="kalb">';
		echo '<option value="0">下限なし</option>';
		echo '<option value="300"'; 			if ($kalb_data == '300') echo ' selected="selected"';			echo '>300万円</option>';
		echo '<option value="400"';			if ($kalb_data == '400') echo ' selected="selected"';			echo '>400万円</option>';
		echo '<option value="500"';			if ($kalb_data == '500') echo ' selected="selected"';			echo '>500万円</option>';
		echo '<option value="600"';			if ($kalb_data == '600') echo ' selected="selected"';			echo '>600万円</option>';
		echo '<option value="700"';			if ($kalb_data == '700') echo ' selected="selected"';			echo '>700万円</option>';
		echo '<option value="800"';			if ($kalb_data == '800') echo ' selected="selected"';			echo '>800万円</option>';
		echo '<option value="900"';			if ($kalb_data == '900') echo ' selected="selected"';			echo '>900万円</option>';
		echo '<option value="1000"';			if ($kalb_data == '1000') echo ' selected="selected"';			echo '>1000万円</option>';
		echo '<option value="1100"';			if ($kalb_data == '1100') echo ' selected="selected"';			echo '>1100万円</option>';
		echo '<option value="1200"';			if ($kalb_data == '1200') echo ' selected="selected"';			echo '>1200万円</option>';
		echo '<option value="1300"';			if ($kalb_data == '1300') echo ' selected="selected"';			echo '>1300万円</option>';
		echo '<option value="1400"';			if ($kalb_data == '1400') echo ' selected="selected"';			echo '>1400万円</option>';
		echo '<option value="1500"';			if ($kalb_data == '1500') echo ' selected="selected"';			echo '>1500万円</option>';
		echo '<option value="1600"';			if ($kalb_data == '1600') echo ' selected="selected"';			echo '>1600万円</option>';
		echo '<option value="1700"';			if ($kalb_data == '1700') echo ' selected="selected"';			echo '>1700万円</option>';
		echo '<option value="1800"';			if ($kalb_data == '1800') echo ' selected="selected"';			echo '>1800万円</option>';
		echo '<option value="1900"';			if ($kalb_data == '1900') echo ' selected="selected"';			echo '>1900万円</option>';
		echo '<option value="2000"';			if ($kalb_data == '2000') echo ' selected="selected"';			echo '>2000万円</option>';
		echo '<option value="3000"';			if ($kalb_data == '3000') echo ' selected="selected"';			echo '>3000万円</option>';
		echo '<option value="5000"';			if ($kalb_data == '5000') echo ' selected="selected"';			echo '>5000万円</option>';
		echo '<option value="7000"';			if ($kalb_data == '7000') echo ' selected="selected"';			echo '>7000万円</option>';
		echo '<option value="10000"';			if ($kalb_data == '10000') echo ' selected="selected"';			echo '>1億円</option>';
		echo '</select>～';

		echo '<select name="kahb" id="kahb">';
		echo '<option value="300"';			if ($kahb_data == '300') echo ' selected="selected"';			echo '>300万円</option>';
		echo '<option value="400"';			if ($kahb_data == '400') echo ' selected="selected"';			echo '>400万円</option>';
		echo '<option value="500"';			if ($kahb_data == '500') echo ' selected="selected"';			echo '>500万円</option>';
		echo '<option value="600"';			if ($kahb_data == '600') echo ' selected="selected"';			echo '>600万円</option>';
		echo '<option value="700"';			if ($kahb_data == '700') echo ' selected="selected"';			echo '>700万円</option>';
		echo '<option value="800"';			if ($kahb_data == '800') echo ' selected="selected"';			echo '>800万円</option>';
		echo '<option value="900"';			if ($kahb_data == '900') echo ' selected="selected"';			echo '>900万円</option>';
		echo '<option value="1000"';			if ($kahb_data == '1000') echo ' selected="selected"';			echo '>1000万円</option>';
		echo '<option value="1100"';			if ($kahb_data == '1100') echo ' selected="selected"';			echo '>1100万円</option>';
		echo '<option value="1200"';			if ($kahb_data == '1200') echo ' selected="selected"';			echo '>1200万円</option>';
		echo '<option value="1300"';			if ($kahb_data == '1300') echo ' selected="selected"';			echo '>1300万円</option>';
		echo '<option value="1400"';			if ($kahb_data == '1400') echo ' selected="selected"';			echo '>1400万円</option>';
		echo '<option value="1500"';			if ($kahb_data == '1500') echo ' selected="selected"';			echo '>1500万円</option>';
		echo '<option value="1600"';			if ($kahb_data == '1600') echo ' selected="selected"';			echo '>1600万円</option>';
		echo '<option value="1700"';			if ($kahb_data == '1700') echo ' selected="selected"';			echo '>1700万円</option>';
		echo '<option value="1800"';			if ($kahb_data == '1800') echo ' selected="selected"';			echo '>1800万円</option>';
		echo '<option value="1900"';			if ($kahb_data == '1900') echo ' selected="selected"';			echo '>1900万円</option>';
		echo '<option value="2000"';			if ($kahb_data == '2000') echo ' selected="selected"';			echo '>2000万円</option>';
		echo '<option value="3000"';			if ($kahb_data == '3000') echo ' selected="selected"';			echo '>3000万円</option>';
		echo '<option value="5000"';			if ($kahb_data == '5000') echo ' selected="selected"';			echo '>5000万円</option>';
		echo '<option value="7000"';			if ($kahb_data == '7000') echo ' selected="selected"';			echo '>7000万円</option>';
		echo '<option value="10000"';			if ($kahb_data == '10000') echo ' selected="selected"';			echo '>1億円</option>';
		echo '<option value="0"';			if ($kahb_data == '0' ||$kahb_data == '' ) echo ' selected="selected"';			echo '>上限なし</option>';
		echo '</select>';
		echo '</p>';
	}

	if( get_option('kaiin_users_mail_kakaku2') == 1 ){
		echo "\n";
		echo '<p>';
		echo '賃料(賃貸)<br />';
		echo '<select name="kalc" id="kalc">';
		echo '<option value="0">下限なし</option>';
		echo '<option value="3"'; 			if ($kalc_data == '3') echo ' selected="selected"';			echo '>3万円</option>';
		echo '<option value="4"';			if ($kalc_data == '4') echo ' selected="selected"';			echo '>4万円</option>';
		echo '<option value="5"';			if ($kalc_data == '5') echo ' selected="selected"';			echo '>5万円</option>';
		echo '<option value="6"';			if ($kalc_data == '6') echo ' selected="selected"';			echo '>6万円</option>';
		echo '<option value="7"';			if ($kalc_data == '7') echo ' selected="selected"';			echo '>7万円</option>';
		echo '<option value="8"';			if ($kalc_data == '8') echo ' selected="selected"';			echo '>8万円</option>';
		echo '<option value="9"';			if ($kalc_data == '9') echo ' selected="selected"';			echo '>9万円</option>';
		echo '<option value="10"';			if ($kalc_data == '10') echo ' selected="selected"';			echo '>10万円</option>';
		echo '<option value="11"';			if ($kalc_data == '11') echo ' selected="selected"';			echo '>11万円</option>';
		echo '<option value="12"';			if ($kalc_data == '12') echo ' selected="selected"';			echo '>12万円</option>';
		echo '<option value="13"';			if ($kalc_data == '13') echo ' selected="selected"';			echo '>13万円</option>';
		echo '<option value="14"';			if ($kalc_data == '14') echo ' selected="selected"';			echo '>14万円</option>';
		echo '<option value="15"';			if ($kalc_data == '15') echo ' selected="selected"';			echo '>15万円</option>';
		echo '<option value="16"';			if ($kalc_data == '16') echo ' selected="selected"';			echo '>16万円</option>';
		echo '<option value="17"';			if ($kalc_data == '17') echo ' selected="selected"';			echo '>17万円</option>';
		echo '<option value="18"';			if ($kalc_data == '18') echo ' selected="selected"';			echo '>18万円</option>';
		echo '<option value="19"';			if ($kalc_data == '19') echo ' selected="selected"';			echo '>19万円</option>';
		echo '<option value="20"';			if ($kalc_data == '20') echo ' selected="selected"';			echo '>20万円</option>';
		echo '<option value="30"';			if ($kalc_data == '30') echo ' selected="selected"';			echo '>30万円</option>';
		echo '<option value="50"';			if ($kalc_data == '50') echo ' selected="selected"';			echo '>50万円</option>';
		echo '<option value="100"';			if ($kalc_data == '100') echo ' selected="selected"';			echo '>100万円</option>';
		echo '</select>～';

		echo '<select name="kahc" id="kahc">';
		echo '<option value="3"';			if ($kahc_data == '3') echo ' selected="selected"';			echo '>3万円</option>';
		echo '<option value="4"';			if ($kahc_data == '4') echo ' selected="selected"';			echo '>4万円</option>';
		echo '<option value="5"';			if ($kahc_data == '5') echo ' selected="selected"';			echo '>5万円</option>';
		echo '<option value="6"';			if ($kahc_data == '6') echo ' selected="selected"';			echo '>6万円</option>';
		echo '<option value="7"';			if ($kahc_data == '7') echo ' selected="selected"';			echo '>7万円</option>';
		echo '<option value="8"';			if ($kahc_data == '8') echo ' selected="selected"';			echo '>8万円</option>';
		echo '<option value="9"';			if ($kahc_data == '9') echo ' selected="selected"';			echo '>9万円</option>';
		echo '<option value="10"';			if ($kahc_data == '10') echo ' selected="selected"';			echo '>10万円</option>';
		echo '<option value="11"';			if ($kahc_data == '11') echo ' selected="selected"';			echo '>11万円</option>';
		echo '<option value="12"';			if ($kahc_data == '12') echo ' selected="selected"';			echo '>12万円</option>';
		echo '<option value="13"';			if ($kahc_data == '13') echo ' selected="selected"';			echo '>13万円</option>';
		echo '<option value="14"';			if ($kahc_data == '14') echo ' selected="selected"';			echo '>14万円</option>';
		echo '<option value="15"';			if ($kahc_data == '15') echo ' selected="selected"';			echo '>15万円</option>';
		echo '<option value="16"';			if ($kahc_data == '16') echo ' selected="selected"';			echo '>16万円</option>';
		echo '<option value="17"';			if ($kahc_data == '17') echo ' selected="selected"';			echo '>17万円</option>';
		echo '<option value="18"';			if ($kahc_data == '18') echo ' selected="selected"';			echo '>18万円</option>';
		echo '<option value="19"';			if ($kahc_data == '19') echo ' selected="selected"';			echo '>19万円</option>';
		echo '<option value="20"';			if ($kahc_data == '20') echo ' selected="selected"';			echo '>20万円</option>';
		echo '<option value="30"';			if ($kahc_data == '30') echo ' selected="selected"';			echo '>30万円</option>';
		echo '<option value="50"';			if ($kahc_data == '50') echo ' selected="selected"';			echo '>50万円</option>';
		echo '<option value="100"';			if ($kahc_data == '100') echo ' selected="selected"';			echo '>100万円</option>';
		echo '<option value="0"';			if ($kahc_data == '0' ||$kahc_data == '' ) echo ' selected="selected"';			echo '>上限なし</option>';
		echo '</select>';
		echo '</p>';
	}
}

//面積
function fudou_registration_form_memseki($user_mail_ID) {

	//個人設定
	$tatemo_l_data = get_user_meta( $user_mail_ID, 'user_mail_tatemonomenseki_l', true);
	$tatemo_h_data = get_user_meta( $user_mail_ID, 'user_mail_tatemonomenseki_h', true);
	$tochim_l_data = get_user_meta( $user_mail_ID, 'user_mail_tochikukaku_l', true);
	$tochim_h_data = get_user_meta( $user_mail_ID, 'user_mail_tochikukaku_h', true);

	if( get_option('kaiin_users_mail_tatemonomenseki') == 1 ){
		echo "\n";
		echo '<p>';
		echo '専有面積<br />';
		echo '<select name="tatemo_l" id="tatemo_l">';
		echo '<option value="0">下限なし</option>';
		echo '<option value="10"';			if ($tatemo_l_data == '10') echo ' selected="selected"';			echo '>10m&sup2;</option>';
		echo '<option value="15"';			if ($tatemo_l_data == '15') echo ' selected="selected"';			echo '>15m&sup2;</option>';
		echo '<option value="20"';			if ($tatemo_l_data == '20') echo ' selected="selected"';			echo '>20m&sup2;</option>';
		echo '<option value="25"';			if ($tatemo_l_data == '25') echo ' selected="selected"';			echo '>25m&sup2;</option>';
		echo '<option value="30"';			if ($tatemo_l_data == '30') echo ' selected="selected"';			echo '>30m&sup2;</option>';
		echo '<option value="35"';			if ($tatemo_l_data == '35') echo ' selected="selected"';			echo '>35m&sup2;</option>';
		echo '<option value="40"';			if ($tatemo_l_data == '40') echo ' selected="selected"';			echo '>40m&sup2;</option>';
		echo '<option value="50"';			if ($tatemo_l_data == '50') echo ' selected="selected"';			echo '>50m&sup2;</option>';
		echo '<option value="60"';			if ($tatemo_l_data == '60') echo ' selected="selected"';			echo '>60m&sup2;</option>';
		echo '<option value="70"';			if ($tatemo_l_data == '70') echo ' selected="selected"';			echo '>70m&sup2;</option>';
		echo '<option value="80"';			if ($tatemo_l_data == '80') echo ' selected="selected"';			echo '>80m&sup2;</option>';
		echo '<option value="90"';			if ($tatemo_l_data == '90') echo ' selected="selected"';			echo '>90m&sup2;</option>';
		echo '<option value="100"';			if ($tatemo_l_data == '100') echo ' selected="selected"';			echo '>100m&sup2;</option>';
		echo '<option value="150"';			if ($tatemo_l_data == '150') echo ' selected="selected"';			echo '>150m&sup2;</option>';
		echo '<option value="200"';			if ($tatemo_l_data == '200') echo ' selected="selected"';			echo '>200m&sup2;</option>';
		echo '<option value="250"';			if ($tatemo_l_data == '250') echo ' selected="selected"';			echo '>250m&sup2;</option>';
		echo '<option value="300"';			if ($tatemo_l_data == '300') echo ' selected="selected"';			echo '>300m&sup2;</option>';
		echo '<option value="350"';			if ($tatemo_l_data == '350') echo ' selected="selected"';			echo '>350m&sup2;</option>';
		echo '<option value="400"';			if ($tatemo_l_data == '400') echo ' selected="selected"';			echo '>400m&sup2;</option>';
		echo '<option value="450"';			if ($tatemo_l_data == '450') echo ' selected="selected"';			echo '>450m&sup2;</option>';
		echo '<option value="500"';			if ($tatemo_l_data == '500') echo ' selected="selected"';			echo '>500m&sup2;</option>';
		echo '</select>～';
		echo '<select name="tatemo_h" id="tatemo_h">';
		echo '<option value="10"';			if ($tatemo_h_data == '10') echo ' selected="selected"';			echo '>10m&sup2;</option>';
		echo '<option value="15"';			if ($tatemo_h_data == '15') echo ' selected="selected"';			echo '>15m&sup2;</option>';
		echo '<option value="20"';			if ($tatemo_h_data == '20') echo ' selected="selected"';			echo '>20m&sup2;</option>';
		echo '<option value="25"';			if ($tatemo_h_data == '25') echo ' selected="selected"';			echo '>25m&sup2;</option>';
		echo '<option value="30"';			if ($tatemo_h_data == '30') echo ' selected="selected"';			echo '>30m&sup2;</option>';
		echo '<option value="35"';			if ($tatemo_h_data == '35') echo ' selected="selected"';			echo '>35m&sup2;</option>';
		echo '<option value="40"';			if ($tatemo_h_data == '40') echo ' selected="selected"';			echo '>40m&sup2;</option>';
		echo '<option value="50"';			if ($tatemo_h_data == '50') echo ' selected="selected"';			echo '>50m&sup2;</option>';
		echo '<option value="60"';			if ($tatemo_h_data == '60') echo ' selected="selected"';			echo '>60m&sup2;</option>';
		echo '<option value="70"';			if ($tatemo_h_data == '70') echo ' selected="selected"';			echo '>70m&sup2;</option>';
		echo '<option value="80"';			if ($tatemo_h_data == '80') echo ' selected="selected"';			echo '>80m&sup2;</option>';
		echo '<option value="90"';			if ($tatemo_h_data == '90') echo ' selected="selected"';			echo '>90m&sup2;</option>';
		echo '<option value="100"';			if ($tatemo_h_data == '100') echo ' selected="selected"';			echo '>100m&sup2;</option>';
		echo '<option value="150"';			if ($tatemo_h_data == '150') echo ' selected="selected"';			echo '>150m&sup2;</option>';
		echo '<option value="200"';			if ($tatemo_h_data == '200') echo ' selected="selected"';			echo '>200m&sup2;</option>';
		echo '<option value="250"';			if ($tatemo_h_data == '250') echo ' selected="selected"';			echo '>250m&sup2;</option>';
		echo '<option value="300"';			if ($tatemo_h_data == '300') echo ' selected="selected"';			echo '>300m&sup2;</option>';
		echo '<option value="350"';			if ($tatemo_h_data == '350') echo ' selected="selected"';			echo '>350m&sup2;</option>';
		echo '<option value="400"';			if ($tatemo_h_data == '400') echo ' selected="selected"';			echo '>400m&sup2;</option>';
		echo '<option value="450"';			if ($tatemo_h_data == '450') echo ' selected="selected"';			echo '>450m&sup2;</option>';
		echo '<option value="500"';			if ($tatemo_h_data == '500') echo ' selected="selected"';			echo '>500m&sup2;</option>';
		echo '<option value="0"';			if ($tatemo_h_data == '0' ||$tatemo_h_data == '' ) echo ' selected="selected"';	echo '>上限なし</option>';
		echo '</select>';
		echo '</p>';

	}
	if( get_option('kaiin_users_mail_tochikukaku') == 1 ){
		echo "\n";
		echo '<p>';
		echo '区画(土地)面積<br />';
		echo '<select name="tochim_l" id="tochim_l">';
		echo '<option value="0">下限なし</option>';
		echo '<option value="30"';			if ($tochim_l_data == '30') echo ' selected="selected"';			echo '>30m&sup2;</option>';
		echo '<option value="35"';			if ($tochim_l_data == '35') echo ' selected="selected"';			echo '>35m&sup2;</option>';
		echo '<option value="40"';			if ($tochim_l_data == '40') echo ' selected="selected"';			echo '>40m&sup2;</option>';
		echo '<option value="50"';			if ($tochim_l_data == '50') echo ' selected="selected"';			echo '>50m&sup2;</option>';
		echo '<option value="60"';			if ($tochim_l_data == '60') echo ' selected="selected"';			echo '>60m&sup2;</option>';
		echo '<option value="70"';			if ($tochim_l_data == '70') echo ' selected="selected"';			echo '>70m&sup2;</option>';
		echo '<option value="80"';			if ($tochim_l_data == '80') echo ' selected="selected"';			echo '>80m&sup2;</option>';
		echo '<option value="90"';			if ($tochim_l_data == '90') echo ' selected="selected"';			echo '>90m&sup2;</option>';
		echo '<option value="100"';			if ($tochim_l_data == '100') echo ' selected="selected"';			echo '>100m&sup2;</option>';
		echo '<option value="150"';			if ($tochim_l_data == '150') echo ' selected="selected"';			echo '>150m&sup2;</option>';
		echo '<option value="200"';			if ($tochim_l_data == '200') echo ' selected="selected"';			echo '>200m&sup2;</option>';
		echo '<option value="250"';			if ($tochim_l_data == '250') echo ' selected="selected"';			echo '>250m&sup2;</option>';
		echo '<option value="300"';			if ($tochim_l_data == '300') echo ' selected="selected"';			echo '>300m&sup2;</option>';
		echo '<option value="350"';			if ($tochim_l_data == '350') echo ' selected="selected"';			echo '>350m&sup2;</option>';
		echo '<option value="400"';			if ($tochim_l_data == '400') echo ' selected="selected"';			echo '>400m&sup2;</option>';
		echo '<option value="450"';			if ($tochim_l_data == '450') echo ' selected="selected"';			echo '>450m&sup2;</option>';
		echo '<option value="500"';			if ($tochim_l_data == '500') echo ' selected="selected"';			echo '>500m&sup2;</option>';
		echo '<option value="600"';			if ($tochim_l_data == '600') echo ' selected="selected"';			echo '>600m&sup2;</option>';
		echo '<option value="700"';			if ($tochim_l_data == '700') echo ' selected="selected"';			echo '>700m&sup2;</option>';
		echo '<option value="800"';			if ($tochim_l_data == '800') echo ' selected="selected"';			echo '>800m&sup2;</option>';
		echo '<option value="900"';			if ($tochim_l_data == '900') echo ' selected="selected"';			echo '>900m&sup2;</option>';
		echo '<option value="1000"';			if ($tochim_l_data == '1000') echo ' selected="selected"';			echo '>1000m&sup2;</option>';
		echo '</select>～';
		echo '<select name="tochim_h" id="tochim_h">';
		echo '<option value="30"';			if ($tochim_h_data == '30') echo ' selected="selected"';			echo '>30m&sup2;</option>';
		echo '<option value="35"';			if ($tochim_h_data == '35') echo ' selected="selected"';			echo '>35m&sup2;</option>';
		echo '<option value="40"';			if ($tochim_h_data == '40') echo ' selected="selected"';			echo '>40m&sup2;</option>';
		echo '<option value="50"';			if ($tochim_h_data == '50') echo ' selected="selected"';			echo '>50m&sup2;</option>';
		echo '<option value="60"';			if ($tochim_h_data == '60') echo ' selected="selected"';			echo '>60m&sup2;</option>';
		echo '<option value="70"';			if ($tochim_h_data == '70') echo ' selected="selected"';			echo '>70m&sup2;</option>';
		echo '<option value="80"';			if ($tochim_h_data == '80') echo ' selected="selected"';			echo '>80m&sup2;</option>';
		echo '<option value="90"';			if ($tochim_h_data == '90') echo ' selected="selected"';			echo '>90m&sup2;</option>';
		echo '<option value="100"';			if ($tochim_h_data == '100') echo ' selected="selected"';			echo '>100m&sup2;</option>';
		echo '<option value="150"';			if ($tochim_h_data == '150') echo ' selected="selected"';			echo '>150m&sup2;</option>';
		echo '<option value="200"';			if ($tochim_h_data == '200') echo ' selected="selected"';			echo '>200m&sup2;</option>';
		echo '<option value="250"';			if ($tochim_h_data == '250') echo ' selected="selected"';			echo '>250m&sup2;</option>';
		echo '<option value="300"';			if ($tochim_h_data == '300') echo ' selected="selected"';			echo '>300m&sup2;</option>';
		echo '<option value="350"';			if ($tochim_h_data == '350') echo ' selected="selected"';			echo '>350m&sup2;</option>';
		echo '<option value="400"';			if ($tochim_h_data == '400') echo ' selected="selected"';			echo '>400m&sup2;</option>';
		echo '<option value="450"';			if ($tochim_h_data == '450') echo ' selected="selected"';			echo '>450m&sup2;</option>';
		echo '<option value="500"';			if ($tochim_h_data == '500') echo ' selected="selected"';			echo '>500m&sup2;</option>';
		echo '<option value="600"';			if ($tochim_h_data == '600') echo ' selected="selected"';			echo '>600m&sup2;</option>';
		echo '<option value="700"';			if ($tochim_h_data == '700') echo ' selected="selected"';			echo '>700m&sup2;</option>';
		echo '<option value="800"';			if ($tochim_h_data == '800') echo ' selected="selected"';			echo '>800m&sup2;</option>';
		echo '<option value="900"';			if ($tochim_h_data == '900') echo ' selected="selected"';			echo '>900m&sup2;</option>';
		echo '<option value="1000"';			if ($tochim_h_data == '1000') echo ' selected="selected"';			echo '>1000m&sup2;</option>';
		echo '<option value="0"';			if ($tochim_h_data == '0' ||$tochim_h_data == '' ) echo ' selected="selected"';	echo '>上限なし</option>';
		echo '</select>';
		echo '</p>';
	}
}





//条件間取り
function fudou_registration_form_madori($user_mail_ID) {

	global $work_madori;

	//表示設定
	$kaiin_users_mail_madori = maybe_unserialize( get_option('kaiin_users_mail_madori') );

	//個人設定
	$user_mail_madori = maybe_unserialize( get_user_meta( $user_mail_ID, 'user_mail_madori', true) );


	$value = '';

	if (is_array($kaiin_users_mail_madori)) {

		for( $madorisu_data = 1; $madorisu_data < 6; $madorisu_data++ ){
			$value_base = '';
			foreach( $work_madori as $meta_box ){
				$madori_code = $meta_box['code'] ;
				$tmp_madori = $madorisu_data.$madori_code;

				if(is_array($kaiin_users_mail_madori)) {
					$i=0;
					foreach( $kaiin_users_mail_madori as $meta_box2 ){
						if( $kaiin_users_mail_madori[$i] == $tmp_madori){
							$value_base .= '<span style="display: inline-block"><input name="mad[]" value="'.$tmp_madori.'" id="mad'.$tmp_madori.'" type="checkbox"';
						
							if(is_array($user_mail_madori)) {
								$k=0;
								foreach( $user_mail_madori as $meta_box2 ){
									if( $user_mail_madori[$k] == $tmp_madori )  $value_base .= ' checked="checked"';
									$k++;
								}
							}
							$value_base .= ' /><label for="mad'.$tmp_madori.'">'.$madorisu_data.$meta_box['name'].'</label></span>　';
						}
						$i++;
					}
				}
			}
			if($value_base != ''){
				$value .= $value_base .'<br />';
			}
		}
	}

	if($value !=''){
		echo "\n";
		echo '<p>';
		echo '間取り<br />';
		echo $value;
		echo '</p>';
	}




}


//駅歩分
function fudou_registration_form_hofun($user_mail_ID) {

	//個人設定
	$hof_data = get_user_meta( $user_mail_ID, 'user_mail_hohun', true);

	if( get_option('kaiin_users_mail_hohun') == 1 ){

		echo "\n";
		echo '<p>';
		echo '駅歩分<br />';
		echo '<select name="hof" id="hof">';
		echo '<option value="0">指定なし</option>';
		echo '<option value="1"';
			if ($hof_data == '1') echo ' selected="selected"';
			echo '>1分以内</option>';
		echo '<option value="3"';
			if ($hof_data == '3') echo ' selected="selected"';
			echo '>3分以内</option>';
		echo '<option value="5"';
			if ($hof_data == '5') echo ' selected="selected"';
			echo '>5分以内</option>';
		echo '<option value="10"';
			if ($hof_data == '10') echo ' selected="selected"';
			echo '>10分以内</option>';
		echo '<option value="15"';
			if ($hof_data == '15') echo ' selected="selected"';
			echo '>15分以内</option>';
		echo '</select>';
		echo '</p>';
	}
}


//条件設備
function fudou_registration_form_setsubi($user_mail_ID) {

	global $work_setsubi;

	//表示設定
	$kaiin_users_mail_setsubi = maybe_unserialize( get_option('kaiin_users_mail_setsubi') );

	//個人設定
	$user_mail_setsubi = maybe_unserialize( get_user_meta( $user_mail_ID, 'user_mail_setsubi', true) );

	$value = '';

	//array_multisort($code,SORT_DESC,$work_setsubi); 
	//asort($work_setsubi);


	foreach($work_setsubi as $meta_box){
		if(is_array($kaiin_users_mail_setsubi)) {

			$i=0;
			foreach($kaiin_users_mail_setsubi as $meta_box2){
				if($kaiin_users_mail_setsubi[$i] == $meta_box['code']){
					$value .= '<span style="display: inline-block"><input type="checkbox" name="set[]"  value="'.$meta_box['code'].'" id="set'.$meta_box['code'].'"';

					if(is_array($user_mail_setsubi)) {
						$k=0;
						foreach($user_mail_setsubi as $meta_box3){
							if($user_mail_setsubi[$k] == $meta_box['code']) $value .= ' checked="checked"';
							$k++;
						}
					}

					$value .= '"><label for="set'.$meta_box['code'].'">'.$meta_box['name'].'</label></span>　';
 				}
				$i++;
			}
		}
	}

	if($value !=''){
		echo "\n";
		echo '<p>';
		echo 'ご希望の必須の設備・条件を選択して下さい<br />';
		echo $value;
		echo '</p>';
	}

}


?>
