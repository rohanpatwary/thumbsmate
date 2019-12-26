<?php
/*
 * 不動産会員プラグイン管理画面設定
 * @package WordPress3.5
 * @subpackage Fudousan Plugin
 * Fudousan kaiin Plugin
 * Version: 1.1.3
*/



//プラグイン基本設定
add_action('admin_menu', 'fudou_admin_kaiinmenu');
function fudou_admin_kaiinmenu() {
	require_once ABSPATH . '/wp-admin/admin.php';
	$plugin = new FudouKaiinPlugin;
	add_management_page('edit.php', '不動産会員設定', 'edit_pages', __FILE__, array($plugin, 'form'));
}

class FudouKaiinPlugin {
	//checkbox
	function process_option($name, $default, $params) {
		if (array_key_exists($name, $params)) {
			$value = stripslashes($params[$name]);
		} elseif (array_key_exists('_'.$name, $params)) {
		// unchecked checkbox value
			$value = stripslashes($params['_'.$name]);
		} else {
			$value = null;
		}
		$stored_value = get_option($name);
		if ($value == null) {
			if ($stored_value === false) {
				if (is_callable($default) && 
					method_exists($default[0], $default[1])) {
					$value = call_user_func($default);
				} else {
					$value = $default;
				}
				add_option($name, $value);
				} else {
					$value = $stored_value;
				}
			} else {
			if ($stored_value === false) {
				add_option($name, $value);
			} elseif ($stored_value != $value) {
				update_option($name, $value);
			}
		}
		return $value;
	}
	//textarea
	function process_option2($name, $default, $params) {
		$value = '';
		if( isset($_POST['_kaiin_title']) ){
			$value = isset($params[$name]) ? stripslashes($params[$name]) : '';
			$stored_value = get_option($name);

				if ($stored_value === false) {
					add_option($name, $value);
				} else {
					update_option($name, $value);
				}
		} else {
			$value = isset($params[$name]) ? stripslashes($params[$name]) : '';
		}
			return $value;

	}

	//プラグイン設定フォーム
	function form() {
		global $post;
		global $wpdb;
		global $is_fudouktai,$is_fudoumap,$is_fudoukaiin,$is_fudoumail;


		$kaiin_title		= $this->process_option('kaiin_title','', $_POST);
		$kaiin_excerpt		= $this->process_option('kaiin_excerpt','', $_POST);
		$kaiin_kakaku		= $this->process_option('kaiin_kakaku','', $_POST);
		$kaiin_menseki		= $this->process_option('kaiin_menseki','', $_POST);
		$kaiin_gazo		= $this->process_option('kaiin_gazo','', $_POST);
		$kaiin_madori		= $this->process_option('kaiin_madori','', $_POST);
		$kaiin_shozaichi	= $this->process_option('kaiin_shozaichi','', $_POST);
		$kaiin_kotsu		= $this->process_option('kaiin_kotsu','', $_POST);
		$kaiin_tikunen		= $this->process_option('kaiin_tikunen','', $_POST);
		$kaiin_kaisu		= $this->process_option('kaiin_kaisu','', $_POST);
		$kaiin_shikibesu	= $this->process_option('kaiin_shikibesu','', $_POST);
		$kaiin_keisaikigenbi	= $this->process_option('kaiin_keisaikigenbi','', $_POST);


		$kaiin_kiyaku = $this->process_option2('kaiin_kiyaku','', $_POST);
		$kaiin_kiyakubr = $this->process_option('kaiin_kiyakubr','', $_POST);

		$kaiin_moushikomi	= $this->process_option('kaiin_moushikomi','', $_POST);
		$kaiin_users_can_register	= $this->process_option('kaiin_users_can_register','', $_POST);

		if($is_fudoumail)
		$kaiin_users_rains_register	= $this->process_option('kaiin_users_rains_register','', $_POST);


		$kaiin_users_mail_name	= $this->process_option('kaiin_users_mail_name','', $_POST);
		$kaiin_users_mail_zip	= $this->process_option('kaiin_users_mail_zip','', $_POST);
		$kaiin_users_mail_address = $this->process_option('kaiin_users_mail_address','', $_POST);
		$kaiin_users_mail_tel	= $this->process_option('kaiin_users_mail_tel','', $_POST);

		$kaiin_users_mail_name_hissu	= $this->process_option('kaiin_users_mail_name_hissu','', $_POST);
		$kaiin_users_mail_zip_hissu	= $this->process_option('kaiin_users_mail_zip_hissu','', $_POST);
		$kaiin_users_mail_address_hissu = $this->process_option('kaiin_users_mail_address_hissu','', $_POST);
		$kaiin_users_mail_tel_hissu	= $this->process_option('kaiin_users_mail_tel_hissu','', $_POST);


		$kaiin_users_mail_fromname	= $this->process_option2('kaiin_users_mail_fromname','', $_POST);
		$kaiin_users_mail_frommail	= $this->process_option2('kaiin_users_mail_frommail','', $_POST);

		$kaiin_users_mail_fromname = get_option('kaiin_users_mail_fromname');
		$kaiin_users_mail_frommail = get_option('kaiin_users_mail_frommail');


		$fudou_ssl_site_url	= $this->process_option2('fudou_ssl_site_url','', $_POST);
		$fudou_ssl_site_url = get_option('fudou_ssl_site_url');


		$kaiin_users_mail_new_subject	= $this->process_option2('kaiin_users_mail_new_subject','', $_POST);
		$kaiin_users_mail_new__comment	= $this->process_option2('kaiin_users_mail_new__comment','', $_POST);

		$kaiin_users_mail_new_subject = get_option('kaiin_users_mail_new_subject');
		$kaiin_users_mail_new__comment = get_option('kaiin_users_mail_new__comment');



		$kaiin_caution = '';
		$fudou_ver =  defined('FUDOU_VERSION') ? str_replace('.' , '' , FUDOU_VERSION ) : '';
		$fudou_k_ver =  defined('FUDOU_K_VERSION') ? str_replace('.' , '' , FUDOU_K_VERSION ) : '';
		$fudou_m_ver =  defined('FUDOU_M_VERSION') ? str_replace('.' , '' , FUDOU_M_VERSION ) : '';
	//	$fudou_wt_ver =  defined('FUDOU_WT_VERSION') ? str_replace('.' , '' , FUDOU_WT_VERSION ) : '';
		$fudou_mail_ver =  defined('FUDOU_MAIL_VERSION') ? str_replace('.' , '' , FUDOU_MAIL_VERSION ) : '';

		if( get_option('kaiin_users_rains_register') == '1' ) {
			if( is_numeric($fudou_ver) && $fudou_ver < 80 )
				$kaiin_caution .= '　不動産ブラグインを0.8.0以降にバージョンアップしてください。現在：'.FUDOU_VERSION.'<br />';
			if( is_numeric($fudou_k_ver) &&  $fudou_k_ver < 114 )
				$kaiin_caution .= '　不動産携帯ブラグインを1.1.4以降にバージョンアップしてください。現在：'.FUDOU_K_VERSION.'<br />';
			if( is_numeric($fudou_m_ver) &&  $fudou_m_ver < 114 )
				$kaiin_caution .= '　不動産マップブラグインを1.1.4以降にバージョンアップしてください。現在：'.FUDOU_M_VERSION.'<br />';
			if( is_numeric($fudou_mail_ver) &&  $fudou_mail_ver < 104 )
				$kaiin_caution .= '　不動産マッチングメールブラグインを1.0.4以降にバージョンアップしてください。現在：'.FUDOU_MAIL_VERSION.'<br />';
		}

		if($kaiin_caution != '' )
			echo '<div class="error">追加機能を利用するには<br />' . $kaiin_caution . '</div>';


	if ( is_multisite() ) {
			echo '<div class="error" style="text-align: center;"><p>マルチサイトでは利用できません。</p></div>';
	}else{


?>
<style type="text/css"> 
<!--
.k_checkbox {
	display: inline-block;
	margin: 0 1em 0 0;
}

#post-body-content {
/*	margin-left:8px; */
	margin: 20px auto;;
	line-height: 1.5;

	padding:16px 16px 30px;
	border-radius:11px;
	background:#fff;
	border:1px solid #e5e5e5;
	box-shadow:rgba(200, 200, 200, 1) 0 4px 18px;
	width: 90%;
	font-size: 12px;
}

// -->
</style>
       
	<div class="wrap">
		<div id="icon-tools" class="icon32"><br /></div>
		<h2>会員設定</h2>
		<div id="poststuff">

		<div id="post-body">
		<div id="post-body-content">

			<?php if ( !empty($_POST ) ) : ?>
			<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
			<?php endif; ?> 

			<form class="add:the-list: validate" method="post">


			<b>会員機能</b>
			<div style="margin:0 0 0 20px;">
			　<input type="checkbox" name="kaiin_users_can_register" value="1" <?php if( get_option('kaiin_users_can_register') == '1' ) echo 'checked=checked'; ?> /> 会員機能を利用する<br />
		        <input name="_kaiin_users_can_register" type="hidden" value="0" />

			<?php if($is_fudoumail && is_numeric($fudou_mail_ver) &&  $fudou_mail_ver > 103 && is_numeric($fudou_ver) && $fudou_ver >= 80){ ?>
			　<input type="checkbox" name="kaiin_users_rains_register" value="1" <?php if( get_option('kaiin_users_rains_register') == '1' ) echo 'checked=checked'; ?> /> 追加機能を利用する(会員物件をユーザーが閲覧条件設定した物件のみ内容表示する)<br />
		        <input name="_kaiin_users_rains_register" type="hidden" value="0" />
			<?php }?>

			</div>
			<br />
			<br />

		        <b>会員物件表示設定 (非ログイン時)</b>
			<div style="margin:0 0 0 20px;">

			　<input type="checkbox" name="kaiin_title" value="1" <?php if( get_option('kaiin_title') == '1' ) echo 'checked=checked'; ?> /> タイトル　
			<input type="checkbox" name="kaiin_excerpt" value="1" <?php if( get_option('kaiin_excerpt') == '1' ) echo 'checked=checked'; ?> /> 抜粋　
			<input type="checkbox" name="kaiin_kakaku" value="1" <?php if( get_option('kaiin_kakaku') == '1' ) echo 'checked=checked'; ?> /> 価格　
			<input type="checkbox" name="kaiin_menseki" value="1" <?php if( get_option('kaiin_menseki') == '1' ) echo 'checked=checked'; ?> /> 面積　
			<input type="checkbox" name="kaiin_gazo" value="1" <?php if( get_option('kaiin_gazo') == '1' ) echo 'checked=checked'; ?> /> 画像　
			<input type="checkbox" name="kaiin_madori" value="1" <?php if( get_option('kaiin_madori') == '1' ) echo 'checked=checked'; ?> /> 間取　
			<br />
			　<input type="checkbox" name="kaiin_shozaichi" value="1" <?php if( get_option('kaiin_shozaichi') == '1' ) echo 'checked=checked'; ?> /> 所在地　
			<input type="checkbox" name="kaiin_kotsu" value="1" <?php if( get_option('kaiin_kotsu') == '1' ) echo 'checked=checked'; ?> /> 交通　
			<input type="checkbox" name="kaiin_tikunen" value="1" <?php if( get_option('kaiin_tikunen') == '1' ) echo 'checked=checked'; ?> /> 築年月　
			<input type="checkbox" name="kaiin_kaisu" value="1" <?php if( get_option('kaiin_kaisu') == '1' ) echo 'checked=checked'; ?> /> 階数　
			<input type="checkbox" name="kaiin_shikibesu" value="1" <?php if( get_option('kaiin_shikibesu') == '1' ) echo 'checked=checked'; ?> /> 物件番号　
			<input type="checkbox" name="kaiin_keisaikigenbi" value="1" <?php if( get_option('kaiin_keisaikigenbi') == '1' ) echo 'checked=checked'; ?> /> 掲載期限日　
			<br />　*会員物件を一般の方(非ログイン時)が閲覧した時の表示する項目です。
			<br />　*表示したい項目にチェックを入れてください。

		        <input name="_kaiin_title" type="hidden" value="0" />
		        <input name="_kaiin_excerpt" type="hidden" value="0" />
		        <input name="_kaiin_kakaku" type="hidden" value="0" />
		        <input name="_kaiin_menseki" type="hidden" value="0" />
		        <input name="_kaiin_gazo" type="hidden" value="0" />
		        <input name="_kaiin_madori" type="hidden" value="0" />
		        <input name="_kaiin_shozaichi" type="hidden" value="0" />
		        <input name="_kaiin_kotsu" type="hidden" value="0" />
		        <input name="_kaiin_tikunen" type="hidden" value="0" />
		        <input name="_kaiin_kaisu" type="hidden" value="0" />
		        <input name="_kaiin_shikibesu" type="hidden" value="0" />
		        <input name="_kaiin_keisaikigenbi" type="hidden" value="0" />
			</div>

			<br />
			<br />

<!--
			物件表示コメント
			<div style="margin:0 0 0 20px;">
				<style>
					textarea{ width:300px; margin:0 0 0 10px;}
					#post-status-info{ width:301px; margin:0 0 0 10px;}
				</style>

			<div id='editorcontainer2'><textarea rows='10' cols='40' name='kaiin_comment' id=''><?php echo get_option('kaiin_comment'); ?></textarea></div>
				<table id="post-status-info" cellspacing="0"><tr>
					<td id="wp-word-count">*物件表示コメント</td>
					<td class="autosave-info"><span id="autosave">&nbsp;</span></td>
				</tr></table><br />
			</div>
			<br />
			<br />
-->



			<b>会員申し込み</b>
			<div style="margin:0 0 0 20px;">
			　<input type="checkbox" name="kaiin_moushikomi" value="1" <?php if( get_option('kaiin_moushikomi') == '1' ) echo 'checked=checked'; ?> /> 会員申込みフォームを表示しない<br />
		        　*会員申込みフォームを表示しない場合はチェックを入れてください。
		        <input name="_kaiin_moushikomi" type="hidden" value="0" />

			</div>
			<br />
			<br />




			<b>会員規約/プライバシーポリシー</b>
			<div style="margin:0 0 0 20px;">
				<style>
					textarea{ width:300px; margin:0 0 0 10px;}
					#post-status-info{ width:301px; margin:0 0 0 10px;}
				</style>

			<div id='editorcontainer2'><textarea rows='10' cols='40' name='kaiin_kiyaku' id=''><?php echo get_option('kaiin_kiyaku'); ?></textarea></div>
				<table id="post-status-info" cellspacing="0"><tr>
					<td id="wp-word-count">*会員申込みフォームに表示します。</td>
					<td class="autosave-info"><span id="autosave">&nbsp;</span></td>
				</tr></table><br />
			　<input type="checkbox" name="kaiin_kiyakubr" value="1" <?php if( get_option('kaiin_kiyakubr') == '1' ) echo 'checked=checked'; ?> /> 自動的に改行する
		        <input name="_kaiin_kiyakubr" type="hidden" value="0" />

			</div>
			<br />
			<br />




			<b>個人情報(入力欄)</b>
			<div style="margin:0 0 0 20px;">

			<input type="checkbox" name="kaiin_users_mail_name" value="1" <?php if( get_option('kaiin_users_mail_name') == '1' ) echo 'checked=checked'; ?> id="name" /><label for="name"> 姓名を表示する</label>
			　<input type="checkbox" name="kaiin_users_mail_name_hissu" value="1" <?php if( get_option('kaiin_users_mail_name_hissu') == '1' ) echo 'checked=checked'; ?> id="name_hissu" /> <label for="name_hissu">必須にする</label>
			<br />

			<input type="checkbox" name="kaiin_users_mail_zip" value="1" <?php if( get_option('kaiin_users_mail_zip') == '1' ) echo 'checked=checked'; ?> id="zip" /><label for="zip"> 郵便番号を表示する</label>
			　<input type="checkbox" name="kaiin_users_mail_zip_hissu" value="1" <?php if( get_option('kaiin_users_mail_zip_hissu') == '1' ) echo 'checked=checked'; ?> id="zip_hissu" /> <label for="zip_hissu">必須にする</label>
			<br />
			<input type="checkbox" name="kaiin_users_mail_address" value="1" <?php if( get_option('kaiin_users_mail_address') == '1' ) echo 'checked=checked'; ?> id="address" /><label for="address"> 住所を表示する</label>
			　<input type="checkbox" name="kaiin_users_mail_address_hissu" value="1" <?php if( get_option('kaiin_users_mail_address_hissu') == '1' ) echo 'checked=checked'; ?> id="address_hissu" /> <label for="address_hissu">必須にする</label>
			<br />
			<input type="checkbox" name="kaiin_users_mail_tel" value="1" <?php if( get_option('kaiin_users_mail_tel') == '1' ) echo 'checked=checked'; ?> id="tel" /><label for="tel"> 電話番号を表示する</label>
			　<input type="checkbox" name="kaiin_users_mail_tel_hissu" value="1" <?php if( get_option('kaiin_users_mail_tel_hissu') == '1' ) echo 'checked=checked'; ?> id="tel_hissu" /> <label for="tel_hissu">必須にする</label>
			<br />
			*表示したい項目にチェックを入れてください。<br />

		        <input name="_kaiin_users_mail_name" type="hidden" value="0" />
		        <input name="_kaiin_users_mail_zip" type="hidden" value="0" />
		        <input name="_kaiin_users_mail_address" type="hidden" value="0" />
		        <input name="_kaiin_users_mail_tel" type="hidden" value="0" />

		        <input name="_kaiin_users_mail_name_hissu" type="hidden" value="0" />
		        <input name="_kaiin_users_mail_zip_hissu" type="hidden" value="0" />
		        <input name="_kaiin_users_mail_address_hissu" type="hidden" value="0" />
		        <input name="_kaiin_users_mail_tel_hissu" type="hidden" value="0" />
			</div>
			<br />
			<br />


			<b>差出人(自動返信メール用)</b>
			<div style="margin:0 0 0 20px;">

				差出人名　<input name="kaiin_users_mail_fromname" type="text" value="<?php echo $kaiin_users_mail_fromname; ?>" /><br />
				差出人メールアドレス　<input name="kaiin_users_mail_frommail" type="text" value="<?php echo $kaiin_users_mail_frommail; ?>" /><br />
				*通常は WordPress&lt;wordpress@ドメイン&gt; になりますので必要ならば変更してください。

			</div>
			<br />
			<br />



			<b>会員新規登録時返信メール内容(パスワード通知)</b>
			<div style="margin:0 0 0 20px;">
			<table border="0" cellpadding="0" cellspacing="0" id="">
				<tr>
					<td>件名<br />&nbsp;<input name="kaiin_users_mail_new_subject" type="text" value="<?php echo $kaiin_users_mail_new_subject; ?>" style="width:400px;" /></td>
					<td></td>
				</tr>
				<tr>
					<td>本文<br /><textarea rows="15" cols="40" name="kaiin_users_mail_new__comment" style="width:400px;"><?php echo $kaiin_users_mail_new__comment; ?></textarea></td>
					<td><div style="margin:0 0 0 10px;">
						<b>パーソナライズ機能</b><br />
						会員登録した内容をもとに、情報をメール本文に差し込むことができます。<br />
						下記キーワードを本文中に記述する事によってメール送信時に置き換ります。<br /><br />
						[user_login]　ユーザー名(半角英数字)<br />
						[user_psass]　パスワード<br />
						[user_mail]　メールアドレス<br />
						[user_name]　お名前<br />
						[user_zip]　郵便番号<br />
						[user_adr]　住所<br />
						[user_tel]　電話番号<br />


						</div>
					</td>
				</tr>
			</table>

			</div>
			<br />
			<br />


		<?php if(FUDOU_SSL_MODE==1){ ?>	
			<b>SSL(会員申込み、パスワードリセット、物件問い合わせ)</b>
			<div style="margin:0 0 0 20px;">

				ベースURL　<input name="fudou_ssl_site_url" type="text" value="<?php echo $fudou_ssl_site_url; ?>" style="width:400px;" /><br />
				*使用しない場合は空欄にしてください。<br />
				*例 通常トップページが http://ドメイン/ の場合　「https://ドメイン」 (最後のスラッシュは無し)<br />
				*例 通常トップページが http://ドメイン/ の場合　「https://共用SSL」 (最後のスラッシュは無し)<br />
				*例 通常トップページが http://ドメイン/wp/ の場合　「https://ドメイン/wp」 (最後のスラッシュは無し)<br />
				*例 通常トップページが http://ドメイン/wp/ の場合　「https://共用SSL/wp」 (最後のスラッシュは無し)


			</div>
			<br />
			<br />
		<?php } ?>	

		<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="変更を保存"  /></p>

		</div>
		</div>



	    </form>
	</div>

<?php
    }
    }
}


?>