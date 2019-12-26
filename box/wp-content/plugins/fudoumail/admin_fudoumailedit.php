<?php
/*
 * 不動産マッチングメールプラグイン管理画面設定
 * @package WordPress3.5
 * @subpackage Fudousan Plugin
 * Fudousan mail Plugin
 * Version: 1.3.3
*/



//プラグイン基本設定
add_action('admin_menu', 'fudou_admin_mailmenu');
function fudou_admin_mailmenu() {
	require_once ABSPATH . '/wp-admin/admin.php';
	$plugin = new FudoumailPlugin;
	add_management_page('edit.php', '不動産会員メール設定', 'edit_pages', __FILE__, array($plugin, 'form'));
}
//プラグイン基本設定
class FudoumailPlugin {

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

		if( isset($_POST['_kaiin_users_mail']) ){
			$value = stripslashes($params[$name]);
			$stored_value = get_option($name);

				if ($stored_value === false) {
					add_option($name, $value);
				} else {
					update_option($name, $value);
				}
		}
		return get_option($name);

	}

	//種別
	function process_option_shu() {
		$name = 'kaiin_users_mail_shu';
		$value = isset($_POST['shu']) ? $_POST['shu'] : '';
		if( isset($_POST['_kaiin_users_mail']) ){
			$stored_value = get_option($name);

			if ($stored_value === false) {
				add_option($name, maybe_serialize($value) );
			} else {
				update_option($name, maybe_serialize($value) );
			}
		}
		return maybe_unserialize(get_option($name));
	}

	//間取り
	function process_option_madori() {
		$name = 'kaiin_users_mail_madori';
		$value = isset($_POST['mad']) ? $_POST['mad'] : '';
		if( isset($_POST['_kaiin_users_mail']) ){
			$stored_value = get_option($name);

			if ($stored_value === false) {
				add_option($name, maybe_serialize($value) );
			} else {
				update_option($name, maybe_serialize($value) );
			}
		}
		return maybe_unserialize(get_option($name));
	}


	//設備
	function process_option_setsubi() {
		$name = 'kaiin_users_mail_setsubi';
		$value = isset($_POST['set']) ? $_POST['set'] : '';
		if( isset($_POST['_kaiin_users_mail']) ){
			$stored_value = get_option($name);

			if ($stored_value === false) {
				add_option($name, maybe_serialize($value) );
			} else {
				update_option($name, maybe_serialize($value) );
			}
		}
		return maybe_unserialize(get_option($name));
	}


	//市区
	function process_option_sik() {
		$name = 'kaiin_users_mail_sik';
		$value = isset($_POST['sik']) ? $_POST['sik'] : '';
		if( isset($_POST['_kaiin_users_mail']) ){
			$stored_value = get_option($name);

			if ($stored_value === false) {
				add_option($name, maybe_serialize($value) );
			} else {
				update_option($name, maybe_serialize($value) );
			}
		}
		return maybe_unserialize(get_option($name));
	}


	//駅
	function process_option_eki() {
		$name = 'kaiin_users_mail_eki';
		$value = isset($_POST['eki']) ? $_POST['eki'] : '';
		if( isset($_POST['_kaiin_users_mail']) ){
			$stored_value = get_option($name);

			if ($stored_value === false) {
				add_option($name, maybe_serialize($value) );
			} else {
				update_option($name, maybe_serialize($value) );
			}
		}
		return maybe_unserialize(get_option($name));
	}



	//プラグイン設定フォーム
	function form() {
		global $post;
		global $wpdb;
		global $is_fudouktai,$is_fudoumap,$is_fudoukaiin,$is_fudoumail;


		$kaiin_users_mail	= $this->process_option('kaiin_users_mail','', $_POST);
		$kaiin_users_comment	= $this->process_option2('kaiin_users_comment','', $_POST);
		$kaiin_users_mail_shu		= $this->process_option_shu();
		$kaiin_users_mail_kakaku	= $this->process_option('kaiin_users_mail_kakaku','', $_POST);
		$kaiin_users_mail_kakaku2	= $this->process_option('kaiin_users_mail_kakaku2','', $_POST);

		$kaiin_users_mail_tatemonomenseki	= $this->process_option('kaiin_users_mail_tatemonomenseki','', $_POST);
		$kaiin_users_mail_tochikukaku	= $this->process_option('kaiin_users_mail_tochikukaku','', $_POST);

		$kaiin_users_mail_sik	= $this->process_option_sik();
		$kaiin_users_mail_eki	= $this->process_option_eki();
		$kaiin_users_mail_hohun	= $this->process_option('kaiin_users_mail_hohun','', $_POST);
		$kaiin_users_mail_madori	= $this->process_option_madori();
		$kaiin_users_mail_setsubi	= $this->process_option_setsubi();


		$user_accsess_log_day	= $this->process_option2('user_accsess_log_day','', $_POST);


		$kaiin_caution = '';
		$fudou_ver =  defined('FUDOU_VERSION') ? str_replace('.' , '' , FUDOU_VERSION ) : '';
		$fudou_k_ver  =  defined('FUDOU_K_VERSION') ? str_replace('.' , '' , FUDOU_K_VERSION ) : '';
		$fudou_m_ver  =  defined('FUDOU_M_VERSION') ? str_replace('.' , '' , FUDOU_M_VERSION ) : '';
	//	$fudou_wt_ver =  defined('FUDOU_WT_VERSION') ? str_replace('.' , '' , FUDOU_WT_VERSION ) : '';

		if( is_numeric($fudou_ver) && $fudou_ver < 62 )
			$kaiin_caution .= '不動産ブラグインを0.6.2以降にバージョンアップしてください。現在：'.FUDOU_VERSION.'<br />';
		if( is_numeric($fudou_k_ver) &&  $fudou_k_ver < 109 )
			$kaiin_caution .= '不動産携帯ブラグインを1.0.9以降にバージョンアップしてください。現在：'.FUDOU_K_VERSION.'<br />';
		if( is_numeric($fudou_m_ver) &&  $fudou_m_ver < 109 )
			$kaiin_caution .= '不動産マップブラグインを1.0.9以降にバージョンアップしてください。現在：'.FUDOU_M_VERSION.'<br />';

		if($kaiin_caution != '' )
			echo '<div class="error">' . $kaiin_caution . '</div>';


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
		<h2>会員情報表示設定</h2>
		<div id="poststuff">


		<div id="post-body">
		<div id="post-body-content">

			<?php if ( !empty($_POST ) ) : ?>
			<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
			<?php endif; ?> 

			<form class="add:the-list: validate" method="post">


			<b>会員メール機能</b>
			<div style="margin:0 0 0 20px;">
			<input type="checkbox" name="kaiin_users_mail" value="1" <?php if( get_option('kaiin_users_mail') == '1' ) echo 'checked=checked'; ?> id="mail" /><label for="mail"> 会員メール機能を利用する</label><br />
		        <input name="_kaiin_users_mail" type="hidden" value="0" />
			</div>
			<br />
			<br />
			<br />


			<b>会員メール設定説明文</b>
			<div style="margin:0 0 0 20px;">
			<textarea rows="10" cols="40" name="kaiin_users_comment" style="width:400px;"><?php echo $kaiin_users_comment; ?></textarea><br />
			*ユーザーがログイン後に 条件設定 する部分の説明文を入力してください。。
			</div>
			<br />
			<br />
			<br />



			<b>会員アクセスログ保存期間</b>
			<div style="margin:0 0 0 20px;">
				<select name="user_accsess_log_day">
					<option value="0"<?php if($user_accsess_log_day == "0") echo ' selected="selected"'; ?>>利用しない</option>
					<option value="-28"<?php if($user_accsess_log_day == "-28") echo ' selected="selected"'; ?>>4週間</option>
					<option value="-42"<?php if($user_accsess_log_day == "-42") echo ' selected="selected"'; ?>>6週間</option>
					<option value="-56"<?php if($user_accsess_log_day == "-56") echo ' selected="selected"'; ?>>8週間</option>
					<option value="-70"<?php if($user_accsess_log_day == "-70") echo ' selected="selected"'; ?>>10週間</option>
					<option value="-98"<?php if($user_accsess_log_day == "-98") echo ' selected="selected"'; ?>>14週間</option>
				</select>
				*保存したい期間を選択してください。
			</div>
			<br />
			<br />
			<br />



		        <b>会員マッチングメール条件設定 (「追加機能」の表示設定)</b>

			<div id="postdivrich" class="postarea">
<?php
			//条件種別
				echo '<div style="margin:10px 0 0 20px;">';
				echo '<hr /> ';

				echo '<b>種別</b> (表示したい項目にチェックを入れてください) [必須]<br /><br />';
				global $work_bukkenshubetsu;
				asort($work_bukkenshubetsu);

				echo '<div style="margin:0 0 0 20px;">';
				foreach($work_bukkenshubetsu as $meta_box){
					$bukkenshubetsu_id = $meta_box['id'];

					if( myRight($bukkenshubetsu_id,2) == '01' && $bukkenshubetsu_id !='1101' ) echo '<br />';
					echo '<span class="k_checkbox"><input type="checkbox" name="shu[]" value="'.$bukkenshubetsu_id.'"';
					$chk_bold = false;
					if(is_array($kaiin_users_mail_shu)) {
						$i=0;
						foreach($kaiin_users_mail_shu as $meta_box2){
							if($kaiin_users_mail_shu[$i] == $bukkenshubetsu_id){
								echo ' checked="checked"';
								$chk_bold = true;
							}
							$i++;
						}
					}
					if( $chk_bold ){
						echo ' id="shu'.$bukkenshubetsu_id.'"><label for="shu'.$bukkenshubetsu_id.'"> <b>'.$meta_box['name'].'</b></label></span>　';
					}else{
						echo ' id="shu'.$bukkenshubetsu_id.'"><label for="shu'.$bukkenshubetsu_id.'"> '.$meta_box['name'].'</label></span>　';
					}
				}
				echo '</div>';
				echo '</div>';
				echo '<br /> ';



			//条件エリア
				echo '<div style="margin:0 0 0 20px;">';
				echo '<hr /> ';

				echo '<b>地域</b> (表示したい項目にチェックを入れてください)<br />';

				//営業県
				//if($shozaichiken_data==""){
					$shozaichiken_data = '0';
					for( $i=1; $i<48 ; $i++ ){
						if( get_option('ken'.$i) != ''){
							$shozaichiken_data .= ','.get_option('ken'.$i);
						}
					}
				//}


				//営業県表示
				$sql = "SELECT middle_area_id, middle_area_name FROM ".$wpdb->prefix."area_middle_area WHERE middle_area_id in ($shozaichiken_data) ORDER BY middle_area_id";
				$sql = $wpdb->prepare($sql,'');

				$metas = $wpdb->get_results( $sql, ARRAY_A );
				if(!empty($metas)) {

					//県
					foreach ( $metas as $meta ) {
						$meta_id = $meta['middle_area_id'];
						$meta_valu = $meta['middle_area_name'];
						echo '<br />'.$meta_valu.'<br />';
						echo '<div style="margin:0 0 0 20px;">';

						//市区
						$sql = "SELECT narrow_area_id,narrow_area_name FROM ".$wpdb->prefix."area_narrow_area WHERE middle_area_id = ".$meta_id." ORDER BY narrow_area_id ASC";
						$sql = $wpdb->prepare($sql,'');
						$metas2 = $wpdb->get_results( $sql, ARRAY_A );
						if(!empty($metas2)) {

							foreach ( $metas2 as $meta2 ) {
								$shozaichicode = $meta_id . $meta2['narrow_area_id'] . '000000';
								echo '<span class="k_checkbox"><input type="checkbox" name="sik[]" value="'.$shozaichicode.'" id="sik'.$shozaichicode.'"';
								$chk_bold = false;
								if(is_array($kaiin_users_mail_sik)) {
									$i=0;
									foreach($kaiin_users_mail_sik as $meta_box2){
										if($kaiin_users_mail_sik[$i] == $shozaichicode ){
											echo ' checked="checked"';
											$chk_bold = true;
										}
										$i++;
									}
								}
								if( $chk_bold ){
									echo ' /><label for="sik'.$shozaichicode.'"> <b>'.$meta2['narrow_area_name'].'</b></label></span>　';
								}else{
									echo ' /><label for="sik'.$shozaichicode.'"> '.$meta2['narrow_area_name'].'</label></span>　';
								}
							}
						}
						echo '</div>';

					}
				}
				echo '</div>';
				echo '<br />';


			//条件路線駅
				echo '<div style="margin:0 0 0 20px;">';
				echo '<hr /> ';
				echo '<b>路線駅</b> (表示したい項目にチェックを入れてください)<br />';


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
						$meta_id = sprintf('%06d', $meta['rosen_id'] );
						$meta_valu = $meta['rosen_name'];

						if($meta_valu != 'バス'){
					//		echo '<br /><input type="checkbox" name="ros[]"value="'.$meta_id.'"> '.$meta_valu.'<br />';
							echo '<br />'.$meta_valu.'<br />';
							echo '<div style="margin:0 0 0 20px;">';


							//駅
							$sql = "SELECT DTS.station_id, DTS.station_name";
							$sql = $sql . " FROM ".$wpdb->prefix."train_station AS DTS";
							$sql = $sql . " WHERE DTS.rosen_id=".$meta['rosen_id']." AND DTS.middle_area_id in (".$shozaichiken_data.")";
							$sql = $sql . " ORDER BY DTS.station_ranking";

							$sql = $wpdb->prepare($sql,'');
							$metas2 = $wpdb->get_results( $sql, ARRAY_A );
							if(!empty($metas2)) {

								foreach ( $metas2 as $meta2 ) {
									$station_id = $meta_id . ''. sprintf('%06d', $meta2['station_id']);

									echo '<span class="k_checkbox"><input type="checkbox" name="eki[]" value="'.$station_id.'" id="eki'.$station_id.'"';
									$chk_bold = false;
									if(is_array($kaiin_users_mail_eki)) {
										$i=0;
										foreach($kaiin_users_mail_eki as $meta_box2){
											$value = $kaiin_users_mail_eki[$i];
											$i++;

											if($value == $station_id ){
												echo ' checked="checked"';
												$chk_bold = true;
											}
										}
									}
									if( $chk_bold ){
										echo ' /><label for="eki'.$station_id.'"> <b>'.$meta2['station_name'].'</b></label></span>　';
									}else{
										echo ' /><label for="eki'.$station_id.'"> '.$meta2['station_name'].'</label></span>　';
									}
								}
							}
							echo "\n";
							echo "\n";

							echo '</div>';
						}

					}
				}
				echo '</div>';
				echo '<br />';




			//条件価格
				echo '<div style="margin:0 0 0 20px;">';
				echo '<hr /> ';
				echo '<b>価格帯</b> (表示したい項目にチェックを入れてください)<br /><br />';
				?>
				<input type="checkbox" name="kaiin_users_mail_kakaku" value="1" <?php if( get_option('kaiin_users_mail_kakaku') == '1' ) echo 'checked="checked"'; ?> id="kakaku" /><label for="kakaku"> 価格を表示する(売買)</label><br />
				<input type="checkbox" name="kaiin_users_mail_kakaku2" value="1" <?php if( get_option('kaiin_users_mail_kakaku2') == '1' ) echo 'checked="checked"'; ?> id="kakaku2" /><label for="kakaku2"> 賃料を表示する(賃貸)</label><br />
				<?php
		        	echo '<input name="_kaiin_users_mail_kakaku" type="hidden" value="0" />';
		        	echo '<input name="_kaiin_users_mail_kakaku2" type="hidden" value="0" />';
				echo '</div>';
				echo '<br />';



		if( ( is_numeric($fudou_ver) && $fudou_ver > 105 && empty($fudou_k_ver) ) || ( is_numeric($fudou_ver) && $fudou_ver > 105  && is_numeric($fudou_k_ver) &&  $fudou_k_ver > 124 ) ){

			//面積
				echo '<div style="margin:0 0 0 20px;">';
				echo '<hr /> ';
				echo '<b>面積</b> (表示したい項目にチェックを入れてください)<br /><br />';
				?>
				<input type="checkbox" name="kaiin_users_mail_tatemonomenseki" value="1" <?php if( get_option('kaiin_users_mail_tatemonomenseki') == '1' ) echo 'checked="checked"'; ?> id="tatemonomenseki" /><label for="tatemonomenseki"> 専有面積を表示する</label><br />
				<input type="checkbox" name="kaiin_users_mail_tochikukaku" value="1" <?php if( get_option('kaiin_users_mail_tochikukaku') == '1' ) echo 'checked="checked"'; ?> id="tochikukaku" /><label for="tochikukaku"> 区画(土地)面積を表示する</label><br />
				<?php
		        	echo '<input name="_kaiin_users_mail_tatemonomenseki" type="hidden" value="0" />';
		        	echo '<input name="_kaiin_users_mail_tochikukaku" type="hidden" value="0" />';
				echo '</div>';
				echo '<br />';
		}



			//条件間取り
				echo '<div style="margin:0 0 0 20px;">';
				echo '<hr /> ';
				echo '<b>間取り</b> (表示したい項目にチェックを入れてください)<br /><br />';
				global $work_madori;
				$madori_dat = '';
				for( $madorisu_data = 1; $madorisu_data < 6; $madorisu_data++ ){

					foreach( $work_madori as $meta_box ){
					$madori_code = $meta_box['code'] ;
						if( $madorisu_data != 1 && $madori_code == 10 ){
						}else{
							$tmp_madori = $madorisu_data.$madori_code;
							$madori_dat .= '<input name="mad[]" value="'.$madorisu_data.$madori_code.'" id="mad'.$madorisu_data.$madori_code.'" type="checkbox"';
							$chk_bold = false;
							if(is_array($kaiin_users_mail_madori)) {
								$i=0;
								foreach( $kaiin_users_mail_madori as $meta_box2 ){
									if( $kaiin_users_mail_madori[$i] == $tmp_madori){
										$madori_dat .= ' checked="checked"';
										$chk_bold = true;
									}
									$i++;
								}
							}
							if( $chk_bold ){
								$madori_dat .= ' /><label for="mad'.$madorisu_data.$madori_code.'"> <b>'.$madorisu_data.$meta_box['name'].'</b></label>　';
							}else{
								$madori_dat .= ' /><label for="mad'.$madorisu_data.$madori_code.'"> '.$madorisu_data.$meta_box['name'].'</label>　';
							}
						}
					}
					$madori_dat .= '<br />';
				}
				echo $madori_dat;
				echo '</div>';
				echo '<br />';


			//駅歩分


				echo '<div style="margin:0 0 0 20px;">';
				echo '<hr /> ';
				echo '<b>駅歩分</b> (表示したい項目にチェックを入れてください)<br /><br />';
		?>
				<input type="checkbox" name="kaiin_users_mail_hohun" value="1" <?php if( get_option('kaiin_users_mail_hohun') == '1' ) echo 'checked="checked"'; ?> id="hohun" /><label for="hohun"> 駅歩分を表示する</label><br />
		<?php
		        	echo '<input name="_kaiin_users_mail_hohun" type="hidden" value="0" />';
				echo '</div>';
				echo '<br />';



			//条件設備

				echo '<div style="margin:0 0 0 20px;">';
				echo '<hr /> ';
				echo '<b>設備・条件</b> (表示したい項目にチェックを入れてください)<br /><br />';
				global $work_setsubi;
			//	array_multisort($code,SORT_DESC,$work_setsubi); 
			//	asort($work_setsubi);
				foreach($work_setsubi as $meta_box){

					if( is_numeric($fudou_ver) && $fudou_ver >= 108 ){

						//条件
						if( $meta_box['code'] == "10001") echo "<hr />";
						//キッチン                                    
						if( $meta_box['code'] == "20701") echo "<hr />";
						//バス・トイレ                                
						if( $meta_box['code'] == "21001") echo "<hr />";
						//冷暖房                                      
						if( $meta_box['code'] == "21301") echo "<hr />";
						//収納                                        
						if( $meta_box['code'] == "21401") echo "<hr />";
						//放送・通信                                  
						if( $meta_box['code'] == "21901") echo "<hr />";
						//セキュリティ                                
						if( $meta_box['code'] == "22301") echo "<hr />";
						//ガス水道                                    
						if( $meta_box['code'] == "20101") echo "<hr />";
						//その他                                      
						if( $meta_box['code'] == "22401") echo "<hr />";
					}

					echo '<span class="k_checkbox"><input type="checkbox" name="set[]"  value="'.$meta_box['code'].'" id="set'.$meta_box['code'].'"';
					$chk_bold = false;
					if(is_array($kaiin_users_mail_setsubi)) {
						$i=0;
						foreach($kaiin_users_mail_setsubi as $meta_box2){
							if($kaiin_users_mail_setsubi[$i] == $meta_box['code']){
								echo ' checked="checked"';
								$chk_bold = true;
							}
							$i++;
						}
					}
					if( $chk_bold ){
						echo '"><label for="set'.$meta_box['code'].'"> <b>'.$meta_box['name'].'</b></label></span>　';
					}else{
						echo '"><label for="set'.$meta_box['code'].'"> '.$meta_box['name'].'</label></span>　';
					}
				}
				echo '</div>';
?>
			</div>
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