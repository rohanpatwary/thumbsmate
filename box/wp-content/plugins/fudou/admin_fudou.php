<?php
/*
 * 不動産プラグイン管理画面設定
 * @package WordPress3.7
 * @subpackage Fudousan Plugin
 * Version: 1.4.0
*/


//プラグイン更新表示を非表示
if(get_option('fudo_plugin_update') != 'true'){
	function wp_plugin_update_rows2() {
		if ( !current_user_can('update_plugins' ) )
			return;
		$plugins = get_site_transient( 'update_plugins' );

		if ( isset($plugins->response) && is_array($plugins->response) ) {
			$plugins = array_keys( $plugins->response );
			foreach( $plugins as $plugin_file ) {
				if($plugin_file != 'fudou/fudou.php')
				add_action( "after_plugin_row_$plugin_file", 'wp_plugin_update_row', 10, 2 );
			}
		}
	}
	add_action( 'admin_init', create_function( '$a', "remove_action( 'admin_init', 'wp_plugin_update_rows' );" ), 2 );
	add_action( 'admin_init', create_function( '$a', "add_action( 'admin_init', 'wp_plugin_update_rows2' );" ), 2 );
}


//WordPressのバージョンアップ通知を非表示
if(get_option('fudo_wp_update') != 'true'){

	add_filter( 'pre_site_transient_update_core', '__return_zero' );
	remove_action( 'wp_version_check', 'wp_version_check' );
	remove_action( 'admin_init', '_maybe_update_core' );

}



//プラグイン基本設定
function fudou_admin_menu() {
	require_once ABSPATH . '/wp-admin/admin.php';
	$plugin = new FudouPlugin;
	add_management_page('edit.php', '不動産プラグイン設定', 'edit_pages', __FILE__, array($plugin, 'form'));
}
add_action('admin_menu', 'fudou_admin_menu');


class FudouPlugin {

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
	function process_option2($name, $default, $params) {
		$value = '';
		if( isset( $_POST['fudo_eigyouken']) ){
			$value = stripslashes($params[$name]);
			$stored_value = get_option($name);

			if ($stored_value === false) {
				add_option($name, $value);
			} else {
				update_option($name, $value);
			}
		}
		return $value;
	}

	//fudo_form
	function process_option3($name, $default, $params) {
		$value = '';
		if( isset( $_POST['fudo_eigyouken']) ){
			$value = stripslashes($params[$name]);
			$stored_value = get_option($name);

				if ($stored_value === false) {
					add_option($name, $value);
				} else {
					update_option($name, $value);
				}
		}
		return $value;

	}


	//設備
	function process_option_setsubi() {
		$name = 'widget_seach_setsubi';
		$value = isset($_POST['set']) ? $_POST['set'] : '';
		if( isset( $_POST['fudo_eigyouken']) ){
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
		global $wp_version;

		if( isset( $_POST['fudo_eigyouken']) ){
			for( $i=1; $i<48 ; $i++ ){
				$stored_value = get_option('ken'.$i);
				$ken_data = isset($_POST['ken'.$i]) ? $_POST['ken'.$i] : '';
				if( $stored_value === false){
					add_option('ken'.$i, $ken_data);
				} elseif ($stored_value != $ken_data) {
					update_option('ken'.$i, $ken_data);
				}
			}
		}

		$opt_fudo_annnai = $this->process_option3('fudo_annnai','', $_POST);
		$opt_fudo_annnai = get_option('fudo_annnai');

		$opt_fudo_form = $this->process_option3('fudo_form','', $_POST);
		$opt_fudo_form = get_option('fudo_form');

		$opt_fudo_head_tag = $this->process_option3('fudo_head_tag','', $_POST);
		$opt_fudo_head_tag = get_option('fudo_head_tag');

		$opt_fudo_footer_tag = $this->process_option3('fudo_footer_tag','', $_POST);
		$opt_fudo_footer_tag = get_option('fudo_footer_tag');

		$opt_fudo_plugin_update = $this->process_option('fudo_plugin_update','true', $_POST);
		$opt_fudo_wp_update = $this->process_option('fudo_wp_update','true', $_POST);


		$opt_fudo_map_directions = $this->process_option('fudo_map_directions','true', $_POST);
		$opt_fudo_map_elevation = $this->process_option('fudo_map_elevation','true', $_POST);
		$fudo_map_comment	= $this->process_option2('fudo_map_comment','', $_POST);
		$fudo_map_comment = get_option('fudo_map_comment');


		$fudou_ssl_site_url	= $this->process_option2('fudou_ssl_site_url','', $_POST);
		$fudou_ssl_site_url = get_option('fudou_ssl_site_url');


		$newup_mark = $this->process_option2('newup_mark','', $_POST);
		$newup_mark = get_option('newup_mark');
		if($newup_mark == '') $newup_mark=14;

		//for WordPress3.5
		if ( $wp_version >= 3.5 ) {
			$upload_path	= $this->process_option2('upload_path','', $_POST);
		}

		$widget_seach_setsubi	= $this->process_option_setsubi();



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
			margin: 0 auto;;
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
		<h2>不動産プラグイン設定(基本設定)</h2>
		<div id="poststuff">


		<div id="post-body">
		<div id="post-body-content">

			<?php if ( !empty($_POST ) ) : ?>
			<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
			<?php endif; ?> 

		        <b>環境 </b>
			<div style="margin:0 0 0 20px;">

				PHPバージョン　<?php echo PHP_VERSION;?><br />
			<?php				
				$sapi_type = php_sapi_name();
				if (substr($sapi_type, 0, 3) == 'cgi') {
				    echo "PHP CGI 版を使用しています<br />";
				} else {
				    echo "PHP CGI 版を使用していません<br />";
				}

				//PHP Safe Mode
				if(ini_get('safe_mode')) echo "PHP セーフモード　はい<br />";
				else  echo "PHP セーフモード　いいえ<br />";

				//PHP Memory Limit 
			//	if(ini_get('memory_limit')) 
			//	echo "PHP Memory Limit　" . ini_get('memory_limit') . "<br />";

			?>
				MySQLバージョン　<?php echo $wpdb->db_version();?><br />
			</div>
			<br />
			<br />


			<form class="add:the-list: validate" method="post">

		        <input name="fudo_eigyouken" type="hidden" value="publish" />
		        <b>営業県 </b>
			<div style="margin:0 0 0 20px;">
			<?php
				$sql = "SELECT middle_area_id, middle_area_name FROM ".$wpdb->prefix."area_middle_area ORDER BY middle_area_id";
				$sql = $wpdb->prepare($sql,'');

				$metas = $wpdb->get_results( $sql, ARRAY_A );
				if(!empty($metas)) {
					$i=1;
					foreach ( $metas as $meta ) {

						$meta_id = $meta['middle_area_id'];
						$meta_valu = $meta['middle_area_name'];
						echo ' <span class="k_checkbox"><input type="checkbox" name="ken'.$meta['middle_area_id'].'" value="'.$meta['middle_area_id'].'" id="ken'.$meta['middle_area_id'].'"';
						if( get_option('ken'.$meta['middle_area_id']) != '' ){
							echo ' checked="checked" /> <label for="ken'.$meta['middle_area_id'].'"><b>'. $meta['middle_area_name'].'</b></label></span>　';
						}else{
							echo ' /> <label for="ken'.$meta['middle_area_id'].'">'. $meta['middle_area_name'].'</label></span>　';
						}
						if ($i == '07' ) echo '<br />';
						if ($i == '14' ) echo '<br />';
						if ($i == '20' ) echo '<br />';
						if ($i == '24' ) echo '<br />';
						if ($i == '30' ) echo '<br />';
						if ($i == '35' ) echo '<br />';
						if ($i == '39' ) echo '<br />';
						if ($i == '46' ) echo '<br />';

						$i++;
					}
				}
			?>

			</div>　　　*必要な県だけ設定してください。全て選択するとエラーがでる場合があります。
			<br />
			<br />
			<br />

			<b>物件問合せ先</b>
			<div id="postdivrich" class="postarea">
			<div style="margin:0 0 0 20px;">
				<style>
					textarea{ width:100%;}
				</style>
			<?php
				if ( version_compare($wp_version, '3.3', '<') ) {
					the_editor($opt_fudo_annnai, 'fudo_annnai', 'title', true, 2);
				}else{
					wp_editor($opt_fudo_annnai, 'fudo_annnai', 'title', true, 2);
				}

			?>
				<table id="post-status-info" cellspacing="0"><tbody><tr>
					<td id="wp-word-count">*物件詳細ページ下に表示されます。</td>
					<td class="autosave-info"><span id="autosave">&nbsp;</span></td>
				</tr></tbody></table>
				*免許番号は必ず表記してください。
				*&lt;/div&gt;等の閉じ忘れに注意してください。
			</div>
			
			</div>
			<br />


			<?php 
			$opt_fudo_form = str_replace( "<TEXTAREA" ,"■textareaは使用できません■" , $opt_fudo_form);
			$opt_fudo_form = str_replace( "<textarea" ,"■textareaは使用できません■" , $opt_fudo_form);
			$opt_fudo_form = str_replace( "</TEXTAREA>" ,"■textareaは使用できません■" , $opt_fudo_form);
			$opt_fudo_form = str_replace( "</textarea>" ,"■textareaは使用できません■" , $opt_fudo_form);
			?>

		        <b>問合せフォーム</b>
			<div id="postdivrich" class="postarea">
			<div style="margin:0 0 0 20px;">
			<div id='editorcontainer'><textarea rows='10' cols='40' name='fudo_form' tabindex='2' id='fudo_form'><?php echo $opt_fudo_form; ?></textarea></div>

				<table id="post-status-info" cellspacing="0"><tbody><tr>
					<td id="wp-word-count">*物件詳細ページ下に表示されます。</td>
					<td class="autosave-info"><span id="autosave">&nbsp;</span></td>
				</tr></tbody></table>
			
			</div>
			</div>

			<br />
			<br />

			<a name="set" id="set"></a>

		        <b>物件条件検索ウィジェット「設備・条件」　設定(表示設定)</b>
			<div id="postdivrich" class="postarea">
<?php
				echo '<div style="margin:0 0 0 20px;">';
				echo ' (表示したい項目にチェックを入れてください)<br />';
				global $work_setsubi;
			//	array_multisort($code,SORT_DESC,$work_setsubi); 
			//	asort($work_setsubi);
				foreach($work_setsubi as $meta_box){


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

					echo '<span class="k_checkbox"><input type="checkbox" name="set[]"  value="'.$meta_box['code'].'" id="set'.$meta_box['code'].'"';
					$chk_bold = false;
					if(is_array($widget_seach_setsubi)) {
						$i=0;
						foreach($widget_seach_setsubi as $meta_box2){
							if($widget_seach_setsubi[$i] == $meta_box['code']){
								echo ' checked="checked"';
								$chk_bold = true;
							}

							$i++;
						}
					}
					if( $chk_bold ){
						echo '"> <label for="set'.$meta_box['code'].'"><b>'.$meta_box['name'].'</b></label></span>　';
					}else{
						echo '"> <label for="set'.$meta_box['code'].'">'.$meta_box['name'].'</label></span>　';
					}
				}

				echo '<br /><br /> *物件にチェックが入っている項目が表示対象になります。';
				echo '<br /> *チェックが全く無い場合、全て表示対象になります。';

				echo '</div>';
?>

			<br />
			<br />



		        <b>NEW(新着)、UP(更新)マーク表示</b>
			<div id="postdivrich" class="postarea">
<?php
				echo '<div style="margin:0 0 0 20px;">';
				echo '<b>表示日数</b> (表示したい日数を入れてください。半角数値)<br />';
				echo '<input name="newup_mark" type="text" value="'. $newup_mark . '" size="4" />日間表示　( 0 で表示しなくなります。)<br />';
				echo ' *登録日と更新日が同じ場合はNEWマークになります。';
				echo '</div>';
?>

			<br />
			<br />

		        <b>地図表示 (物件詳細)</b>
			<div style="margin:0 0 0 20px;">

				<b>駅からのルートを表示</b><br />
				<select name="fudo_map_directions">
				<option value="true"<?php if($opt_fudo_map_directions == "true") echo ' selected="selected"'; ?>>表示する</option>
				<option value="false"<?php if($opt_fudo_map_directions != "true") echo ' selected="selected"'; ?>>表示しない</option>
				</select><br />

				<b>標高を表示</b><br />
				<select name="fudo_map_elevation">
				<option value="true"<?php if($opt_fudo_map_elevation == "true") echo ' selected="selected"'; ?>>表示する</option>
				<option value="false"<?php if($opt_fudo_map_elevation != "true") echo ' selected="selected"'; ?>>表示しない</option>
				</select>　
				*マーカークリック時に標高を表示します。<br />

				<b>マップ下コメント</b><br />
				<input name="fudo_map_comment" type="text" value="<?php echo $fudo_map_comment; ?>" style="width:400px;" /><br />
				*マップ下に簡単なコメントを表示できます。<br />

			</div>
			<br />
			<br />







		<?php if(FUDOU_SSL_MODE==1){ ?>	

			<b>SSL(会員申込み、パスワードリセット、物件問い合わせ)</b>
			<div style="margin:0 0 0 20px;">

				ベースURL　<input name="fudou_ssl_site_url" type="text" value="<?php echo $fudou_ssl_site_url; ?>" style="width:400px;" /><br />
				*使用しない場合は空欄にしてください。<br />

				*例 通常トップページが http://ドメイン/の場合　「https://ドメイン」 (最後のスラッシュは無し)<br />
				*例 通常トップページが http://ドメイン/の場合　「https://共用SSL」 (最後のスラッシュは無し)<br />
				*例 通常トップページが http://ドメイン/wp/の場合　「https://ドメイン/wp」 (最後のスラッシュは無し)<br />
				*例 通常トップページが http://ドメイン/wp/の場合　「https://共用SSL/wp」 (最後のスラッシュは無し)


			</div>
			<br />
			<br />
		<?php } ?>	


		        <b>不動プラグイン更新表示</b>
			<div style="margin:0 0 0 20px;">

			　<select name="fudo_plugin_update">
			<option value="true"<?php if($opt_fudo_plugin_update != "false") echo ' selected="selected"'; ?>>表示する</option>
			<option value="false"<?php if($opt_fudo_plugin_update == "false") echo ' selected="selected"'; ?>>表示しない</option>
			</select>
			*不動プラグイン更新表示(新しいバージョンの・・)をするかしないかを設定します。(更新カウント数はそのままです)
			</div>

			<br />
			<br />


		        <b>WordPressのバージョンアップ通知</b>
			<div style="margin:0 0 0 20px;">

			　<select name="fudo_wp_update">
			<option value="true"<?php if($opt_fudo_wp_update != "false") echo ' selected="selected"'; ?>>表示する</option>
			<option value="false"<?php if($opt_fudo_wp_update == "false") echo ' selected="selected"'; ?>>表示しない</option>
			</select>
			*WordPress のバージョンアップ通知をするかしないかを設定します。
			</div>

			<br />
			<br />


			<!-- for WordPress3.5 -->
			<?php if ( $wp_version >= 3.5 ) { ?>
		        <b><?php _e('Store uploads in this folder'); ?></b>
			<div style="margin:0 0 0 20px;">
				<input name="upload_path" type="text" id="upload_path" value="<?php echo esc_attr(get_option('upload_path')); ?>" class="regular-text code" />
				<p class="description"><?php _e('Default is <code>wp-content/uploads</code>'); ?></p>
			</div>　　　*<a href="options-media.php">メディア設定</a>で「アップロードしたファイルを年月ベースのフォルダに整理 」のチェックを外してください。
			<br />
			<br />
			<?php } ?>

			<b>ヘッダ・フッター埋め込みタグ</b>
			<div id="postdivrich" class="postarea">
			<div style="margin:0 0 0 20px;">
			ヘッダー
				<div id='editorcontainer'><textarea rows='10' cols='40' name='fudo_head_tag' tabindex='2' id='fudo_form'><?php echo $opt_fudo_head_tag; ?></textarea></div>
					<table id="post-status-info" cellspacing="0"><tbody><tr>
						<td id="wp-word-count">*他社のアクセスログ オリジナルのscript css等ヘッダに埋め込むタグを記述してください</td>
						<td class="autosave-info"><span id="autosave">&nbsp;</span></td>
					</tr></tbody></table>
				</div>
			</div>
			<div style="margin:0 0 0 20px;">
			フッター
				<div id='editorcontainer'><textarea rows='10' cols='40' name='fudo_footer_tag' tabindex='2' id='fudo_form'><?php echo $opt_fudo_footer_tag; ?></textarea></div>
					<table id="post-status-info" cellspacing="0"><tbody><tr>
						<td id="wp-word-count">*他社のアクセスログ オリジナルのscript css等ヘッダに埋め込むタグを記述してください</td>
						<td class="autosave-info"><span id="autosave">&nbsp;</span></td>
					</tr></tbody></table>
				</div>
			</div>
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

// ダッシュボードウィジェット
function fudodl_add_dashboard_widgets() {
	// Right Now
	wp_add_dashboard_widget( 'dashboard_right_now3', '不動産プラグイン', 'wp_dashboard_right_now3' );
}
//admin_init
//add_action('wp_dashboard_setup', 'fudodl_add_dashboard_widgets' );

function wp_dashboard_right_now3() {
	echo '<div class="table table_content">';
	echo '<iframe src="http://nendeb.jp/fudou_dl.html" height="350" width="100%" frameborder="0"></iframe>';
	echo '</div>';
}

// ダッシュボードウィジェット
function fudo_add_dashboard_widgets() {
	// Right Now
	wp_add_dashboard_widget( 'dashboard_right_now2', '物件', 'wp_dashboard_right_now2' );
}
//admin_init
//add_action('wp_dashboard_setup', 'fudo_add_dashboard_widgets' );

function wp_dashboard_right_now2() {
	global $wpdb;
	global $wp_registered_sidebars;


	echo '<style>
	#dashboard_right_now2 p.sub,#dashboard_right_now2 .table,#dashboard_right_now2 .versions{margin:-12px;}
	#dashboard_right_now2 .inside{font-size:12px;padding-top:20px;}
	#dashboard_right_now2 p.sub{font-style:italic;font-family:Georgia,"Times New Roman","Bitstream Charter",Times,serif;padding:5px 10px 15px;color:#777;font-size:13px;position:absolute;top:-17px;left:15px;}
	#dashboard_right_now2 .table{margin:0 -9px;padding:0 10px;position:relative;}
	#dashboard_right_now2 .table_content{float:left;border-top:#ececec 1px solid;width:45%;}
	#dashboard_right_now2 .table_discussion{float:right;border-top:#ececec 1px solid;width:45%;}

	#dashboard_right_now2 table td{padding:3px 0;white-space:nowrap;}
	#dashboard_right_now2 table tr.first td{border-top:none;}
	#dashboard_right_now2 td.b{padding-right:6px;text-align:right;font-family:Georgia,"Times New Roman","Bitstream Charter",Times,serif;font-size:16px;width:1%;}
	#dashboard_right_now2 td.b a{font-size:16px;}
	#dashboard_right_now2 td.b a:hover{color:#d54e21;}
	#dashboard_right_now2 .t{font-size:12px;padding-right:12px;padding-top:6px;color:#777;}
	#dashboard_right_now2 .t a{white-space:nowrap;}
	</style>';

	echo '<div class="table table_content">';
	echo '<p class="sub">公開</p>';

	echo '<table>';

	// Posts
	$num_posts2 = wp_count_posts( 'fudo' );
	$num =  $num_posts2->publish ;

	$sql = "SELECT count(DISTINCT P.ID) as co";
	$sql .=  " FROM $wpdb->posts AS P";
	$sql .=  " INNER JOIN $wpdb->postmeta AS PM ON P.ID = PM.post_id ";
	$sql .=  " WHERE P.post_status='publish' AND P.post_password = ''  AND P.post_type ='fudo' ";
	$sql .=  " AND PM.meta_key='kaiin' AND PM.meta_value = '1'";

	$sql = $wpdb->prepare($sql,'');
	$metas = $wpdb->get_row( $sql );
	if( !empty($metas) ) $metas_co = $metas->co;	

	$num = $num - $metas_co;
	$text = "件　一般公開 ";

	echo '<tr class="first">';
	echo '<td class="first b b-posts">' . $num . '</td>';
	echo '<td class="t posts">' . $text . '</td>';
	echo '</tr>';


	$num2 = $metas_co;
	$text2 = "件　会員公開";

	echo '<tr class="first">';
	echo '<td class="first b b_pages">' . $num2 . '</td>';
	echo '<td class="t pages">' . $text2 . '</td>';
	echo '</tr>';

	do_action('right_now_content_table_end');
	echo '</table></div>';

	if ( current_user_can( 'edit_posts' ) ) {

	echo '<div class="table table_discussion">';
	echo '<p class="sub">非公開</p>'."\n\t".'<table>';
	$num = number_format_i18n( $num_posts2->draft );

	$text = "件　下書き";
	echo '<tr class="first">';
	echo '<td class="first b b-posts">' . $num . '</td>';
	echo '<td class="t posts">' . $text . '</td>';
	echo '</tr>';


	$num = number_format_i18n( $num_posts2->private );
	$text = "件　非公開";
	echo '<tr class="first">';
	echo '<td class="first b b-posts">' . $num . '</td>';
	echo '<td class="t posts">' . $text . '</td>';
	echo '</tr>';
	}



	do_action('right_now_table_end');
	do_action('right_now_discussion_table_end');
	echo '</table></div>';

	echo '<br class="clear" />';
	do_action( 'activity_box_end' );
}


//管理画面投稿表示
function my_fudo_stuff(){
	if( FUDOU_TRA_COMMENT  ){
		register_post_type(
			'fudo', 
			array(
				'label' => __('物件'),
				'singular_label' => __('投稿'),
				'public' => true,
				'show_ui' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => false,
				'query_var' => false, 
				'menu_position' => 5, 
				'supports' => array(
					'title', 
					'editor', 
					'excerpt',
					'trackbacks',
					'comments',
				//	'revisions',
				//	'custom-fields',
					'thumbnail'
				)
			)
		);
	}else{
		register_post_type(
			'fudo', 
			array(
				'label' => __('物件'),
				'singular_label' => __('投稿'),
				'public' => true,
				'show_ui' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => false,
				'query_var' => false, 
				'menu_position' => 5, 
				'supports' => array(
					'title', 
					'editor', 
					'excerpt',
				//	'trackbacks',
				//	'comments',
				//	'revisions',
				//	'custom-fields',
					'thumbnail'
				)
			)
		);
	}

	//物件カテゴリ
	register_taxonomy(
		'bukken',	//タクソノミー名
		'fudo', 	//post type名
		array(
			'hierarchical' => true,
			'update_count_callback' => '_update_post_term_count',
			'label' => '物件カテゴリ',
			'singular_label' => '物件カテゴリ',
			'public' => true,
			'show_ui' => true
			//,'menu-order' => true

		)
	);


	//投稿タグ
	register_taxonomy(
		'bukken_tag',	//タクソノミー名
		'fudo', 	//post type名
		array(
			'public' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'show_in_nav_menus' => true,
			'hierarchical' => false,
			'labels' => array(
				'name' => '物件投稿タグ',
				'singular_name' => '物件投稿タグ',
				'searemperor_items' => '物件タグを検索',
				'popular_items' => 'よく使われている物件タグ',
				'all_items' => 'すべてのタグ',
				'edit_item' => '物件タグの編集',
				'update_item' => '更新',
				'add_new_item' => '新規物件タグを追加',
				'new_item_name' => '新しい物件投稿タグ',
				'choose_from_most_used' => 'よく使われている物件タグから選択'
			)
		)
	);

	add_post_type_support( 'fudo', 'author' );


}
add_action('init', 'my_fudo_stuff'); 








//管理画面一覧表示
function my_fudo_columns($columns){

	$columns_title = isset($_GET['title']) ? $_GET['title'] : '';
	$columns_kkk   = isset($_GET['kkk']) ?   $_GET['kkk']  : '';
	$columns_no    = isset($_GET['no']) ?    $_GET['no']   : '';
	$columns_mds   = isset($_GET['mds']) ?   $_GET['mds'] : '';
	$columns_mds2  = isset($_GET['mds2']) ?  $_GET['mds2'] : '';
	$columns_kds   = isset($_GET['kds']) ?   $_GET['kds'] : '';
	$columns_siy   = isset($_GET['siy']) ?   $_GET['siy'] : '';
	$columns_sik   = isset($_GET['sik']) ?  $_GET['sik'] : '';

	//sort
	$arr_params = array ('title' => 'desc','mds' => '','mds2' => '','kkk' => '','no' => '','kds' => '','siy' => '','sik' => '');

	//タイトル
	if ( $columns_title == 'asc'){
		$arr_params = array ('title' => 'desc','mds' => '','mds2' => '','kkk' => '','no' => '','kds' => '','siy' => '','sik' => '');
	}else{
		$arr_params = array ('title' =>  'asc','mds' => '','mds2' => '','kkk' => '','no' => '','kds' => '','siy' => '','sik' => '');
	}
		$title_url = esc_url(add_query_arg($arr_params, $_SERVER['REQUEST_URI']));
		$title_img = '<img src="../wp-content/plugins/fudou/img/sortbtm_'.$columns_title.'.png" border="0">';

	//価格
	if ( $columns_kkk == 'asc'){
		$arr_params = array ('title' => '','mds' => '','mds2' => '','kkk' => 'desc','no' => '','kds' => '','siy' => '','sik' => '');
	}else{
		$arr_params = array ('title' => '','mds' => '','mds2' => '','kkk' => 'asc','no' => '','kds' => '','siy' => '','sik' => '');
	}
		$kakaku_url = esc_url(add_query_arg($arr_params, $_SERVER['REQUEST_URI']));
		$kakaku_img = '<img src="../wp-content/plugins/fudou/img/sortbtm_'.$columns_kkk.'.png" border="0">';

	//物件番号
	if ( $columns_no == 'asc'){
		$arr_params = array ('title' => '','mds' => '','mds2' => '','kkk' => '','no' => 'desc','kds' => '','siy' => '','sik' => '');
	}else{
		$arr_params = array ('title' => '','mds' => '','mds2' => '','kkk' => '','no' => 'asc','kds' => '','siy' => '','sik' => '');
	}
		$no_url = esc_url(add_query_arg($arr_params, $_SERVER['REQUEST_URI']));
		$no_img = '<img src="../wp-content/plugins/fudou/img/sortbtm_'.$columns_no.'.png" border="0">';

	//公開日付
	if ( $columns_mds == 'asc'){
		$arr_params = array ('title' => '','mds' => 'desc','mds2' => '','kkk' => '','no' => '','kds' => '','siy' => '','sik' => '');
	}else{
		$arr_params = array ('title' => '','mds' => 'asc','mds2' => '','kkk' => '','no' => '','kds' => '','siy' => '','sik' => '');
	}
		$date_url = esc_url(add_query_arg($arr_params, $_SERVER['REQUEST_URI']));
		$date_img = '<img src="../wp-content/plugins/fudou/img/sortbtm_'.$columns_mds.'.png" border="0">';


	//更新日付
	if ( $columns_mds2== 'asc'){
		$arr_params = array ('title' => '','mds' => '','mds2' => 'desc','kkk' => '','no' => '','kds' => '','siy' => '','sik' => '');
	}else{
		$arr_params = array ('title' => '','mds' => '','mds2' => 'asc','kkk' => '','no' => '','kds' => '','siy' => '','sik' => '');
	}
		$datek_url = esc_url(add_query_arg($arr_params, $_SERVER['REQUEST_URI']));
		$datek_img = '<img src="../wp-content/plugins/fudou/img/sortbtm_'.$columns_mds2.'.png" border="0">';

	//掲載期限日
	if ( $columns_kds == 'asc'){
		$arr_params = array ('title' => '','mds' => '','mds2' => '','kkk' => '','no' => '','kds' => 'desc','siy' => '','sik' => '');
	}else{
		$arr_params = array ('title' => '','mds' => '','mds2' => '','kkk' => '','no' => '','kds' => 'asc','siy' => '','sik' => '');
	}
		$date2_url = esc_url(add_query_arg($arr_params, $_SERVER['REQUEST_URI']));
		$date2_img = '<img src="../wp-content/plugins/fudou/img/sortbtm_'.$columns_kds.'.png" border="0">';

	//成約日
	if ( $columns_siy == 'asc'){
		$arr_params = array ('title' => '','mds' => '','mds2' => '','kkk' => '','no' => '','kds' => '','siy' => 'desc','sik' => '');
	}else{
		$arr_params = array ('title' => '','mds' => '','mds2' => '','kkk' => '','no' => '','kds' => '','siy' => 'asc','sik' => '');
	}
		$date3_url = esc_url(add_query_arg($arr_params, $_SERVER['REQUEST_URI']));
		$date3_img = '<img src="../wp-content/plugins/fudou/img/sortbtm_'.$columns_siy.'.png" border="0">';

	//市区
	if ( $columns_sik == 'asc'){
		$arr_params = array ('title' => '','mds' => '','mds2' => '','kkk' => '','no' => '','kds' => '','siy' => '','sik' => 'desc');
	}else{
		$arr_params = array ('title' => '','mds' => '','mds2' => '','kkk' => '','no' => '','kds' => '','siy' => '','sik' => 'asc');
	}
		$sik_url = esc_url(add_query_arg($arr_params, $_SERVER['REQUEST_URI']));
		$sik_img = '<img src="../wp-content/plugins/fudou/img/sortbtm_'.$columns_sik.'.png" border="0">';


	if ( empty($columns_title) && empty($columns_kkk) && empty($columns_no) && empty($columns_mds) && empty($columns_mds2) && empty($columns_kds) && empty($columns_siy) && empty($columns_sik) ){
		$date_img = '<img src="../wp-content/plugins/fudou/img/sortbtm_desc.png" border="0">';
	}

	if( FUDOU_TRA_COMMENT ){
		$columns = array(
			'cb' => '<input type="checkbox"/>',
			'title' => '</a>タイトル<a href="#" onclick="location.href=\''.$title_url.'\'">'.$title_img.'</a>',
			'image' => '画像',
			'bukken' => '物件番号<a href="#" onclick="location.href=\''.$no_url.'\'">'.$no_img.'</a><br />市区<a href="#" onclick="location.href=\''.$sik_url.'\'">'.$sik_img.'</a> 路線駅',
			'kakaku' => '種別　価格<a href="#" onclick="location.href=\''.$kakaku_url.'\'">'.$kakaku_img.'</a><br />間取 地図',
			'bukken_tag' => '物件カテゴリ<br />物件投稿タグ',
			'newdate' => '</a>公開日<a href="#" onclick="location.href=\''.$date_url.'\'">'.$date_img.'</a><br />更新日<a href="#" onclick="location.href=\''.$datek_url.'\'">'.$datek_img.'</a>',
			'keisaikigenbi' => '掲載期限日<a href="#" onclick="location.href=\''.$date2_url.'\'">'.$date2_img.'</a><br />成約日<a href="#" onclick="location.href=\''.$date3_url.'\'">'.$date3_img.'</a>',
			'comments' => __('Comments'), 
		);
	}else{
		$columns = array(
			'cb' => '<input type="checkbox"/>',
			'title' => '</a>タイトル<a href="#" onclick="location.href=\''.$title_url.'\'">'.$title_img.'</a>',
			'image' => '画像',
			'bukken' => '物件番号<a href="#" onclick="location.href=\''.$no_url.'\'">'.$no_img.'</a><br />市区<a href="#" onclick="location.href=\''.$sik_url.'\'">'.$sik_img.'</a> 路線駅',
			'kakaku' => '種別　価格<a href="#" onclick="location.href=\''.$kakaku_url.'\'">'.$kakaku_img.'</a><br />間取 地図',
			'bukken_tag' => '物件カテゴリ<br />物件投稿タグ',
			'newdate' => '</a>公開日<a href="#" onclick="location.href=\''.$date_url.'\'">'.$date_img.'</a><br />更新日<a href="#" onclick="location.href=\''.$datek_url.'\'">'.$datek_img.'</a>',
			'keisaikigenbi' => '掲載期限日<a href="#" onclick="location.href=\''.$date2_url.'\'">'.$date2_img.'</a><br />成約日<a href="#" onclick="location.href=\''.$date3_url.'\'">'.$date3_img.'</a>',
		);
	}	
	return $columns;
}
add_filter('manage_edit-fudo_columns', 'my_fudo_columns');



function my_fudo_column($column){


	global $post;
	global $wpdb;

	$img_path = get_option('upload_path');
	if ($img_path == '')
		$img_path = 'wp-content/uploads';

	if('image' == $column){
		echo '<style>.sorting-indicator {background-image : none;display: none; height:  auto; margin: auto; width: auto;}</style>';

		for( $imgid=1; $imgid<=2; $imgid++ ){

			$fudoimg_data = get_post_meta($post->ID, "fudoimg$imgid", true);
			$fudoimgcomment_data = get_post_meta($post->ID, "fudoimgcomment$imgid", true);

			if($fudoimg_data !="" ){

				$sql  = "";
				$sql .=  "SELECT P.ID,P.guid";
				$sql .=  " FROM $wpdb->posts as P";
				$sql .=  " WHERE P.post_type ='attachment' AND P.guid LIKE '%/$fudoimg_data' ";
			//	$sql = $wpdb->prepare($sql,'');
				$metas = $wpdb->get_row( $sql );

				$attachmentid = '';
				if ( $metas != '' ){
					$attachmentid  =  $metas->ID;
					$guid_url  =  $metas->guid;
				}

				if($attachmentid !=''){
					//thumbnail、medium、large、full 
					$fudoimg_data1 = wp_get_attachment_image_src( $attachmentid, 'thumbnail');
					$fudoimg_url = $fudoimg_data1[0];
					if($fudoimg_url !=''){
						echo '<img src="' . $fudoimg_url.'" alt="'.$fudoimg_data.'" title="'.$fudoimg_data.'" width="64" height="64" />';
					}else{
						echo '<img src="' . $guid_url . '" alt="'.$fudoimg_data.'" title="'.$fudoimg_data.'" width="64" height="64"/>';
					}
				}
			}
		}
		echo "\n";
	} 

	elseif ("bukken" == $column){
		echo '番号:'.get_post_meta($post->ID, 'shikibesu', true).'';
		admin_custom_shozaichi_print($post->ID);
		echo get_post_meta($post->ID, 'shozaichimeisho', true);
		admin_custom_koutsu1_print($post->ID);
		admin_custom_koutsu2_print($post->ID);
		echo "\n";
	}
	elseif ("kakaku" == $column){ 
		global $work_bukkenshubetsu;
		$bukkenshubetsu_id = get_post_meta($post->ID,'bukkenshubetsu',true);
		foreach($work_bukkenshubetsu as $meta_box){
			if( $bukkenshubetsu_id ==  $meta_box['id'] ){
				echo ' ' . $meta_box['name'];
			}
		}

		echo "<br />";

		$kakaku_data = get_post_meta($post->ID,'kakaku',true);
		if(is_numeric($kakaku_data)){
			echo floatval($kakaku_data)/10000;
			echo "万円";
		}

		echo "<br />";

		echo get_post_meta($post->ID,'madorisu',true);
		global $work_madori;
		$madorisyurui_id = get_post_meta($post->ID,'madorisyurui',true);
		foreach($work_madori as $meta_box2){
			if( $madorisyurui_id ==  $meta_box2['code'] ){
				echo ' ' . $meta_box2['name'];
			}
		}

		$tatemonomenseki_data = get_post_meta($post->ID,'tatemonomenseki',true);
		if ($tatemonomenseki_data !="")
			echo ' ('.$tatemonomenseki_data.'㎡)';

		echo "<br />";

		if( get_post_meta($post->ID, 'bukkenido', true)!="" && get_post_meta($post->ID, 'bukkenkeido', true)!="")
			echo '<font color="#0000FF">地図有</font>　';

		if( get_post_meta($post->ID, 'kaiin', true) == 1)
			echo '<font color="#FF0000">会員</font>';

		echo "\n";
	}

	elseif ("bukken_tag" == $column){
		the_terms(0, 'bukken');
		echo '<br /><hr />';
		the_terms(0, 'bukken_tag');
		echo "\n";
	}

	elseif ("keisaikigenbi" == $column){
		echo get_post_meta($post->ID,'keisaikigenbi',true).'<br />';
			echo '<hr />';
		echo get_post_meta($post->ID,'seiyakubi',true);
		echo "\n";
	}

	elseif ("newdate" == $column){

		$h_time = mysql2date( __( 'Y/m/d' ), $post->post_date );
		$m_time = mysql2date( __( 'Y/m/d' ), $post->post_modified );

//		echo '<abbr>公開日' . apply_filters( 'post_date_column_time', $h_time, $post, $column_name, $mode ) . '</abbr>';
		echo '<abbr>公開日' . $h_time . '</abbr>';
		echo '<br />';
//		echo '<abbr>更新日' . apply_filters( 'post_date_column_time', $m_time, $post, $column_name, $mode ) . '</abbr>';
		echo '<abbr>更新日' . $m_time . '</abbr>';

		echo '<br />';
		if ( 'publish' == $post->post_status ) {
			_e( 'Published' );
		} elseif ( 'future' == $post->post_status ) {
			if ( $time_diff > 0 )
				echo '<strong class="attention">' . __( 'Missed schedule' ) . '</strong>';
			else
				_e( 'Scheduled' );
		} else {
			_e( 'Last Modified' );
		}
		// 状態
		admin_custom_jyoutai_print($post->ID);
	}
}
add_action('manage_posts_custom_column', 'my_fudo_column');





//物件投稿一覧フィルター
function shubetsu_restrict_manage_posts() {

	global $post_type,$is_fudourains,$is_fudoucsv,$is_fudouapaman;
	global $wpdb;

	if( $post_type == 'fudo') {

		$shubetsu = isset($_GET['shubetsu']) ? $_GET['shubetsu'] : '';
?>
		 <select name="shubetsu" class='postform'>
			<option value="1"<?php if( $shubetsu == "1"){echo ' selected="selected"';} ?>>物件すべて</option>
			<option value="2"<?php if( $shubetsu == "2"){echo ' selected="selected"';} ?>>売買すべて</option>
			<option value="3"<?php if( $shubetsu == "3"){echo ' selected="selected"';} ?>>　売買土地</option>
			<option value="4"<?php if( $shubetsu == "4"){echo ' selected="selected"';} ?>>　売買戸建</option>
			<option value="5"<?php if( $shubetsu == "5"){echo ' selected="selected"';} ?>>　売買マンション</option>
			<option value="6"<?php if( $shubetsu == "6"){echo ' selected="selected"';} ?>>　売買住宅以外の建物全部</option>
			<option value="7"<?php if( $shubetsu == "7"){echo ' selected="selected"';} ?>>　売買住宅以外の建物一部</option>
			<option value="10"<?php if( $shubetsu == "10"){echo ' selected="selected"';} ?>>賃貸すべて</option>
			<option value="11"<?php if( $shubetsu == "11"){echo ' selected="selected"';} ?>>　賃貸居住用</option>
			<option value="12"<?php if( $shubetsu == "12"){echo ' selected="selected"';} ?>>　賃貸事業用</option>

<?php

			$sql  =  "SELECT DISTINCT PM.meta_value AS csvtype";
			$sql .=  " FROM $wpdb->posts as P";
			$sql .=  " INNER JOIN $wpdb->postmeta as PM   ON P.ID = PM.post_id ";
			$sql .=  " WHERE P.post_type ='fudo'";
			$sql .=  " AND PM.meta_key='csvtype'";
			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql, ARRAY_A );
			if(!empty($metas)) {
				foreach ( $metas as $meta ) {
					$csvtype = $meta['csvtype'];

					echo '<option value="'.$csvtype.'"';
						
					if ($shubetsu == $csvtype )
					 	 echo ' selected="selected"';

					switch ($csvtype) {
						case "homes" : echo '>ホームズ</option>'; break;
						case "h_rains" : echo '>東日本レインズ</option>'; break;
						case "k_rains" : echo '>旧近畿レインズ</option>'; break;
						case "k_rains2" : echo '>新近畿レインズ</option>'; break;
						case "c21" : echo '>センチュリー21</option>'; break;
						case "apaman" : echo '>アパマン</option>'; break;
						case "fudoucsv" : echo '>汎用CSV</option>'; break;
						default: echo '>'.$csvtype.'</option>';	break;
					}
				}
			}
?>
		</select>
		<style type="text/css">
		<!--
		#wpbody-content th#title a{display:inline;}
		#wpbody-content th#date a{display:inline;}
		-->
		</style>
<?php
	}
}
add_action('restrict_manage_posts', 'shubetsu_restrict_manage_posts');



function shubetsu_posts_where($where) {

	if( is_admin() ) {
		global $wpdb;
		$where_data = "";

		$shubetsu = isset($_GET['shubetsu']) ? $_GET['shubetsu'] : '';

		switch ($shubetsu) {
			case "1" :	//物件すべて
				$postmeta_name = "bukkenshubetsu";
				break;
			case "2" :	//売買すべて
				$postmeta_name = "bukkenshubetsu";
				$where_data = " AND CAST( PM.meta_value AS SIGNED )<3000";
				break;
			case "3" :	//売買土地
				$postmeta_name = "bukkenshubetsu";
				$where_data = " AND Left(PM.meta_value,2) ='11'";
				break;
			case "4" :	//売買戸建
				$postmeta_name = "bukkenshubetsu";
				$where_data = " AND Left(PM.meta_value,2) ='12'";
				break;
			case "5" :	//売買マンション
				$postmeta_name = "bukkenshubetsu";
				$where_data = " AND Left(PM.meta_value,2) ='13'";
				break;
			case "6" :	//売住宅以外の建物全部
				$postmeta_name = "bukkenshubetsu";
				$where_data = " AND Left(PM.meta_value,2) ='14'";
				break;
			case "7" :	//売住宅以外の建物一部
				$postmeta_name = "bukkenshubetsu";
				$where_data = " AND Left(PM.meta_value,2) ='15'";
				break;

			case "10" :	//賃貸すべて
				$postmeta_name = "bukkenshubetsu";
				$where_data = " AND  CAST( PM.meta_value AS SIGNED )>3000";
				break;

			case "11" :	//賃貸居住用
				$postmeta_name = "bukkenshubetsu";
				$where_data = " AND Left(PM.meta_value,2) ='31'";
				break;
			case "12" :	//賃貸事業用
				$postmeta_name = "bukkenshubetsu";
				$where_data = " AND Left(PM.meta_value,2) ='32'";
				break;


			case "homes" :	//ホームズ
				$postmeta_name = "csvtype";
				$where_data = " AND PM.meta_value ='homes'";
				break;

			case "k_rains" :	//近畿レインズ
				$postmeta_name = "csvtype";
				$where_data = " AND PM.meta_value ='k_rains'";
				break;

			case "h_rains" :	//東日本レインズ
				$postmeta_name = "csvtype";
				$where_data = " AND PM.meta_value ='h_rains'";
				break;

			case "c21" :	//センチュリー21
				$postmeta_name = "csvtype";
				$where_data = " AND PM.meta_value ='c21'";
				break;

			case "k_rains2" :	//近畿レインズ新システム
				$postmeta_name = "csvtype";
				$where_data = " AND PM.meta_value ='k_rains2'";
				break;

			case "fudoucsv" :	//汎用CSV
				$postmeta_name = "csvtype";
				$where_data = " AND PM.meta_value ='fudoucsv'";
				break;

			case "apaman" :	//アパマン
				$postmeta_name = "csvtype";
				$where_data = " AND PM.meta_value ='apaman'";
				break;

			default:
				$postmeta_name = "bukkenshubetsu";
				break;
		}

		$sql = "";
		$sql = $sql . "SELECT DISTINCT (P.ID)";
		$sql = $sql . " FROM $wpdb->posts as P";
		$sql = $sql . " INNER JOIN $wpdb->postmeta as PM   ON P.ID = PM.post_id ";
		$sql = $sql . " WHERE PM.meta_key='$postmeta_name'";
		$sql = $sql . " AND P.post_type ='fudo'";
		$sql = $sql . $where_data;

		if( $shubetsu != "" ) {
			$where .= " AND ID IN ( ".$sql." )";
		}
	}

    return $where;
}
add_filter('posts_where', 'shubetsu_posts_where');




//物件投稿一覧ソート
function wp_order_by_order_fudou($orderby) {

	if( is_admin() ) {
		global $wpdb;

		if ( isset($_GET['title']) && $_GET['title'] == 'asc' ){
			$orderby = "$wpdb->posts.post_title ASC";
		}
		if ( isset($_GET['title']) && $_GET['title'] == 'desc'){
			$orderby = "$wpdb->posts.post_title DESC";
		}

		if ( isset($_GET['mds']) && $_GET['mds'] == 'asc' ){
			$orderby = "$wpdb->posts.post_date ASC";
		}
		if ( isset($_GET['mds']) && $_GET['mds'] == 'desc' ){
			$orderby = "$wpdb->posts.post_date DESC";
		}
		if ( isset($_GET['mds2']) && $_GET['mds2'] == 'asc' ){
			$orderby = "$wpdb->posts.post_modified ASC";
		}
		if ( isset($_GET['mds2']) && $_GET['mds2'] == 'desc' ){
			$orderby = "$wpdb->posts.post_modified DESC";
		}

		if ( isset($_GET['kkk']) && $_GET['kkk'] == 'asc' ){
			$orderby = "CAST($wpdb->postmeta.meta_value AS SIGNED) ASC";
		}
		if ( isset($_GET['kkk']) && $_GET['kkk'] == 'desc' ){
			$orderby = "CAST($wpdb->postmeta.meta_value AS SIGNED) DESC";
		}

		if ( isset($_GET['no']) && $_GET['no'] == 'asc' ){
			$orderby = "$wpdb->postmeta.meta_value ASC";
		}
		if ( isset($_GET['no']) && $_GET['no'] == 'desc' ){
			$orderby = "$wpdb->postmeta.meta_value DESC";
		}

		if ( isset($_GET['kds']) && $_GET['kds'] == 'asc' ){
			$orderby = "$wpdb->postmeta.meta_value ASC";
		}
		if ( isset($_GET['kds']) && $_GET['kds'] == 'desc' ){
			$orderby = "$wpdb->postmeta.meta_value DESC";
		}

		if ( isset($_GET['siy']) && $_GET['siy'] == 'asc' ){
			$orderby = "$wpdb->postmeta.meta_value ASC";
		}
		if ( isset($_GET['siy']) && $_GET['siy'] == 'desc' ){
			$orderby = "$wpdb->postmeta.meta_value DESC";
		}

		if ( isset($_GET['sik']) && $_GET['sik'] == 'asc' ){
			$orderby = "$wpdb->postmeta.meta_value ASC,PM.meta_value ASC";
		}
		if ( isset($_GET['sik']) && $_GET['sik'] == 'desc' ){
			$orderby = "$wpdb->postmeta.meta_value DESC,PM.meta_value DESC";
		}
	}
	return $orderby;
}
add_filter('posts_orderby', 'wp_order_by_order_fudou');

function wp_order_by_join_fudou( $join ){
	if( is_admin() ) {
		if( ( isset($_GET['kkk']) && $_GET['kkk'] != '' ) || ( isset($_GET['no']) && $_GET['no'] != '' ) || 	( isset($_GET['kds']) && $_GET['kds'] != '' ) || ( isset($_GET['siy']) && $_GET['siy'] != '' ) || ( isset($_GET['sik']) && $_GET['sik'] != '' ) ){
			global  $wpdb;
			$join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id";
		}
		if ( isset($_GET['sik']) && $_GET['sik'] != '' ){
			$join .= " LEFT JOIN $wpdb->postmeta AS PM ON " . $wpdb->posts . ".ID = PM.post_id ";
		}

	}
	return $join;
}
add_filter('posts_join', 'wp_order_by_join_fudou' );

function wp_order_by_where_fudou( $where ){
	if( is_admin() ) {
		global  $wpdb;
		//価格
		if( isset($_GET['kkk']) && $_GET['kkk'] != ''){
			$where .= " AND $wpdb->postmeta.meta_key = 'kakaku'";
		}
		//物件番号
		if( isset($_GET['no']) && $_GET['no'] != ''){
			$where .= " AND $wpdb->postmeta.meta_key = 'shikibesu'";
		}
		//掲載期限日
		if( isset($_GET['kds']) && $_GET['kds'] != ''){
			$where .= " AND $wpdb->postmeta.meta_key = 'keisaikigenbi'";
		}
		//成約日
		if( isset($_GET['siy']) && $_GET['siy'] != ''){
			$where .= " AND $wpdb->postmeta.meta_key = 'seiyakubi' ";
		}
		//市区
		if( isset($_GET['sik']) && $_GET['sik'] != ''){
			$where .= " AND $wpdb->postmeta.meta_key = 'shozaichicode' ";
			$where .= " AND PM.meta_key = 'shozaichimeisho' ";
		}

	}
	return $where;
}
add_filter('posts_where', 'wp_order_by_where_fudou' );



/* 17所在地 所在地コード */
function admin_custom_shozaichi_print($post_id) {
	global $wpdb;

	$shozaichiken_data = get_post_meta($post_id,'shozaichicode',true);
	$shozaichiken_data = myLeft($shozaichiken_data,2);

	if($shozaichiken_data=="")
		$shozaichiken_data = get_post_meta($post_id,'shozaichiken',true);

	if(!empty($shozaichiken_data)){
		$sql = "SELECT `middle_area_name` FROM `".$wpdb->prefix."area_middle_area` WHERE `middle_area_id`=".$shozaichiken_data."";
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if( !empty($metas) ) echo "<br />".$metas->middle_area_name." ";
	}

	$shozaichicode_data = get_post_meta($post_id,'shozaichicode',true);
	$shozaichicode_data = myLeft($shozaichicode_data,5);
	$shozaichicode_data = myRight($shozaichicode_data,3);

	if($shozaichiken_data !="" && $shozaichicode_data !=""){
		$sql = "SELECT narrow_area_name FROM ".$wpdb->prefix."area_narrow_area WHERE middle_area_id=".$shozaichiken_data." and narrow_area_id =".$shozaichicode_data."";

		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if( !empty($metas) ) echo $metas->narrow_area_name;
	}
}
/* 22交通路線1  駅路線設定パターンで選択した方法で路線をセット(0：数値4桁 1：数値4桁)*/
function admin_custom_koutsu1_print($post_id) {
	global $wpdb;

	$koutsurosen_data = get_post_meta($post_id, 'koutsurosen1', true);
	$koutsueki_data = get_post_meta($post_id, 'koutsueki1', true);

	$shozaichiken_data = get_post_meta($post_id,'shozaichicode',true);
	$shozaichiken_data = myLeft($shozaichiken_data,2);

	if($koutsurosen_data !=""){
		$sql = "SELECT `rosen_name` FROM `".$wpdb->prefix."train_rosen` WHERE `rosen_id` =".$koutsurosen_data."";
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if( !empty($metas) ) echo "<br />".$metas->rosen_name." ";
	}

	if($koutsurosen_data !="" && $koutsueki_data !=""){
		$sql = "SELECT DTS.station_name";
		$sql = $sql . " FROM ".$wpdb->prefix."train_rosen AS DTR";
		$sql = $sql . " INNER JOIN ".$wpdb->prefix."train_station AS DTS ON DTR.rosen_id = DTS.rosen_id";
		$sql = $sql . " WHERE DTS.station_id=".$koutsueki_data." AND DTS.rosen_id=".$koutsurosen_data."";
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if( !empty($metas) ) echo $metas->station_name.'<br />';
	}

}
function admin_custom_koutsu2_print($post_id) {
	global $wpdb;

	$koutsurosen_data = get_post_meta($post_id, 'koutsurosen2', true);
	$koutsueki_data = get_post_meta($post_id, 'koutsueki2', true);

	$shozaichiken_data = get_post_meta($post_id,'shozaichicode',true);
	$shozaichiken_data = myLeft($shozaichiken_data,2);

	if($koutsurosen_data !=""){
		$sql = "SELECT `rosen_name` FROM `".$wpdb->prefix."train_rosen` WHERE `rosen_id` =".$koutsurosen_data."";
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if( !empty($metas) ) echo "".$metas->rosen_name." ";
	}

	if($koutsurosen_data !="" && $koutsueki_data !=""){
		$sql = "SELECT DTS.station_name";
		$sql = $sql . " FROM ".$wpdb->prefix."train_rosen AS DTR";
		$sql = $sql . " INNER JOIN ".$wpdb->prefix."train_station AS DTS ON DTR.rosen_id = DTS.rosen_id";
		$sql = $sql . " WHERE DTS.station_id=".$koutsueki_data." AND DTS.rosen_id=".$koutsurosen_data."";
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if( !empty($metas) ) echo $metas->station_name.'<br />';
	}
}

// 6状態  1:空有/売出中 3:空無/売止 4:成約 9:削除 
function admin_custom_jyoutai_print($post_id) {
	$jyoutai_data = get_post_meta($post_id,'jyoutai',true);
	if($jyoutai_data=="1")  echo '<br />状態：空有/売出中';
	if($jyoutai_data=="3")  echo '<br />状態：空無/売止';
	if($jyoutai_data=="4")  echo '<br />状態：成約';
	if($jyoutai_data=="9")  echo '<br />状態：削除';
}

?>