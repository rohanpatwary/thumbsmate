<?php
/*
 * 不動産会員プラグイン
 * @package WordPress3.1
 * @subpackage Fudousan Plugin
 * Fudousan kaiin Plugin
 * Version: 1.0.5
*/


//顧客情報入力
function fudou_registration_form() {

	echo "\n";
	echo '<!-- '.FUDOU_KAIIN_VERSION.' -->';

	$last_name =	isset($_POST['last_name'])  ? esc_attr($_POST['last_name']) : '';
	$first_name=	isset($_POST['first_name']) ? esc_attr($_POST['first_name']) : '';
	$user_zip =	isset($_POST['user_zip'])   ? esc_attr($_POST['user_zip']) : '';
	$user_adr =	isset($_POST['user_adr'])   ? esc_attr($_POST['user_adr']) : '';
	$user_tel =	isset($_POST['user_tel'])   ? esc_attr($_POST['user_tel']) : '';

	//姓名
		if( get_option('kaiin_users_mail_name') == '1' ){
			echo '<p>';
			echo 'お名前';
			if( get_option('kaiin_users_mail_name_hissu') == '1' ) echo '　<font color="#FF2200">(必須)</font>';
			echo '<br />姓 <input type="text" name="last_name" id="last_name" class="input1" value="'. $last_name .'" size="20" tabindex="10" />';
			echo '名 <input type="text" name="first_name" id="first_name" class="input1" value="'. $first_name .'" size="20" tabindex="20" />';
			echo '</p>';
		}


	//郵便番号
		if( get_option('kaiin_users_mail_zip') == 1 ){
		echo "\n";
			echo '<p>';
			echo '郵便番号';
			if( get_option('kaiin_users_mail_zip_hissu') == '1' ) echo '　<font color="#FF2200">(必須)</font>';

			echo '<br /><input type="text" name="user_zip" id="user_zip" class="input" value="'. $user_zip .'" size="20" tabindex="30" />';
			echo '</p>';
		}

	//住所
		if( get_option('kaiin_users_mail_address') == 1 ){
		echo "\n";
			echo '住所';
			if( get_option('kaiin_users_mail_address_hissu') == '1' ) echo '　<font color="#FF2200">(必須)</font>';
			echo '<br /><input type="text" name="user_adr" id="user_adr" class="input" value="'. $user_adr .'" size="20" tabindex="40" />';
			echo '</p>';
		}

	//電話番号
		if( get_option('kaiin_users_mail_tel') == 1 ){
		echo "\n";
			echo '<p>';
			echo '電話番号';
			if( get_option('kaiin_users_mail_tel_hissu') == '1' ) echo '　<font color="#FF2200">(必須)</font>';
			echo '<br /><input type="text" name="user_tel" id="user_tel" class="input" value="'. $user_tel .'" size="20" tabindex="50" />';
			echo '</p>';
		}


	echo '<!-- '.FUDOU_KAIIN_VERSION.' -->';


}



/**
 * コンタクトフィールド削除
 *
 * @param array $contactmethods
 * @return array
 */
function hide_profile_fields( $contactmethods ) {

	unset($contactmethods['aim']);
	unset($contactmethods['jabber']);
	unset($contactmethods['yim']);

	return $contactmethods;
}
add_filter('user_contactmethods','hide_profile_fields',10,1);

/**
 *プロフィール画面にフィールドを追加
 *
 * @param array $contactmethods
 * @return array
 */
function original_profile_fields( $contactmethods ) {
	if( get_option('kaiin_users_mail_zip') == 1 )
		$contactmethods["user_zip"] = "郵便番号";
	if( get_option('kaiin_users_mail_address') == 1 )
		$contactmethods["user_adr"] = "住所";
	if( get_option('kaiin_users_mail_tel') == 1 )
		$contactmethods["user_tel"] = "電話番号";
	return $contactmethods;
}
add_filter('user_contactmethods','original_profile_fields',11,1);









?>
