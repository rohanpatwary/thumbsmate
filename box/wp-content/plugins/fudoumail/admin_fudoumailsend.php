<?php
/*
 * 不動産マッチングメールプラグイン管理画面設定
 * @package WordPress3.5
 * @subpackage Fudousan Plugin
 * Fudousan mail Plugin
 * Version: 1.3.3
*/


//メール送信
function fudou_admin_mailsend() {
	require_once ABSPATH . '/wp-admin/admin.php';
	$plugin = new FudoumailSendPlugin;
	add_management_page('edit.php', '不動産会員メール送信', 'edit_pages', __FILE__, array($plugin, 'form'));
}
add_action('admin_menu', 'fudou_admin_mailsend');


//メール送信選択
class FudoumailSendPlugin {


	//textarea//textbox
	function process_option2($name, $default, $params) {

		if( isset($_POST['md']) && ( $_POST['md'] =='m' || $_POST['md'] =='cron' ) ){
			$value = stripslashes($params[$name]);
			$stored_value = get_option($name);

				if ($stored_value === false) {
					add_option($name, $value);
				} else {
					update_option($name, $value);
				}
		} else {
			$value = stripslashes($params[$name]);
		}
			return $value;
	}


	//メール送信フォーム
	function form() {
		global $post;
		global $wpdb;
		global $user_ID;

		if( isset($_POST['md']) && $_POST['md'] =='m'){
			$user_mail_fromname = $this->process_option2('user_mail_fromname','', $_POST);
			$user_mail_frommail = $this->process_option2('user_mail_frommail','', $_POST);
			$user_mail_subject = $this->process_option2('user_mail_subject','', $_POST);
			$user_mail_comment = $this->process_option2('user_mail_comment','', $_POST);
			$user_mail_bukkenlimit = $this->process_option2('user_mail_bukkenlimit','', $_POST);
			$user_mail_kaiin = $this->process_option2('user_mail_kaiin','', $_POST);
			$user_mail_sleep = $this->process_option2('user_mail_sleep','', $_POST);
			echo '<div id="message" class="updated fade"><p><strong>設定を保存しました。</strong></p></div>';
		}

			$user_mail_fromname = get_option('user_mail_fromname');
			$user_mail_frommail = get_option('user_mail_frommail');
			$user_mail_subject = get_option('user_mail_subject');
			$user_mail_comment = get_option('user_mail_comment');

			$user_mail_bukkenlimit = get_option('user_mail_bukkenlimit');
			if($user_mail_bukkenlimit == '') $user_mail_bukkenlimit = 20;
			$user_mail_kaiin = get_option('user_mail_kaiin');
			$user_mail_sleep = get_option('user_mail_sleep');
			if( $user_mail_sleep == '' ) $user_mail_sleep = 1;
			if (!is_numeric($user_mail_sleep))  $user_mail_sleep = 1;


		if( isset($_POST['md']) && $_POST['md'] =='cron'){
			wp_clear_scheduled_hook('users_mail_cron');
			$user_mail_cron = $this->process_option2('user_mail_cron','', $_POST);
			$user_mail_cron_daily = $this->process_option2('user_mail_cron_daily','', $_POST);
			$user_mail_reset = $this->process_option2('user_mail_reset','', $_POST);
			echo '<div id="message" class="updated fade"><p><strong>設定を保存しました。</strong></p></div>';
		}
			$user_mail_cron = get_option('user_mail_cron');
			$user_mail_cron_daily = get_option('user_mail_cron_daily');
			$user_mail_reset = get_option('user_mail_reset');


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
		<div id="icon-users" class="icon32"><br /></div>
		<h2>会員メール送信</h2>
		<div id="poststuff">
		<div id="post-body">

<?php

		$users =  isset($_GET['users']) ? $_GET['users'] : '';

		//手動メール送信
		if( is_array($users) && $_GET['md'] =='s'  ){
			$i=0;
			foreach($users as $meta_box){
				$mail_send_caution = users_mail_send( $users[$i], '');

				$mail_send_caution = str_replace("\r\n", "<br />", $mail_send_caution);

				echo str_pad('',256);
				echo '<div class="error">' . $mail_send_caution . '</div>';
				flush();

				sleep($user_mail_sleep);

				$i++;
			}
		}else{
			if( isset($_GET['md']) && $_GET['md'] =='s'  )
				echo '<div class="error"><strong>ユーザーを選択してください。</strong></div>';
		}

		$page_orderby = isset($_GET['orderby']) ?  $_GET['orderby'] : '';	//ソート項目
		$page_order   = isset($_GET['order']) ?  $_GET['order'] : '';		//昇順・降順



		$user_id_order 		= 'asc';
		$user_login_order 	= 'desc';
		$user_name_order 	= 'asc';
		$user_email_order 	= 'asc';
		$login_count_order 	= 'asc';
		$login_date_order 	= 'asc';

	//	$user_order_data = ' ORDER BY U.ID ASC';
		$user_order_data = ' ORDER BY U.user_login ASC';

		switch ($page_order) {
			case 'asc':
				//ID
				if($page_orderby == 'user_id'){ 
					$user_id_order = 'desc';
					$user_order_data = ' ORDER BY U.ID ASC';
				}
				//ユーザー名
				if($page_orderby == 'user_login'){ 
					$user_login_order = 'desc';
					$user_order_data = ' ORDER BY U.user_login ASC';
				}
				//姓名
				if($page_orderby == 'user_name'){ 
					$user_name_order = 'desc';
					$user_order_data = ' ORDER BY UM4.meta_value,UM5.meta_value ASC';
				}
				//メール
				if($page_orderby == 'user_email'){ 
					$user_email_order = 'desc';
					$user_order_data = ' ORDER BY U.user_email ASC';
				}
				//ログイン数
				if($page_orderby == 'login_count'){ 
					$login_count_order = 'desc';
					$user_order_data = ' ORDER BY  CAST( UM2.meta_value AS SIGNED) ASC';
				}
				//最終ログイン
				if($page_orderby == 'login_date'){ 
					$login_date_order = 'desc';
					$user_order_data = ' ORDER BY UM3.meta_value ASC';
				}
				break;

			case 'desc':
				//ID
				if($page_orderby == 'user_id'){ 
					$user_id__order = 'asc';
					$user_ordr_data = ' ORDER BY U.user_id DESC';
				}
				//ユーザー名
				if($page_orderby == 'user_login'){ 
					$user_login_order = 'asc';
					$user_order_data = ' ORDER BY U.user_login DESC';
				}
				//姓名
				if($page_orderby == 'user_name'){ 
					$user_name_order = 'asc';
					$user_order_data = ' ORDER BY UM4.meta_value DESC,UM5.meta_value DESC';
				}
				//メール
				if($page_orderby == 'user_email'){ 
					$user_email_order = 'asc';
					$user_order_data = ' ORDER BY U.user_email DESC';
				}
				//ログイン数
				if($page_orderby == 'login_count'){ 
					$login_count_order = 'asc';
					$user_order_data = ' ORDER BY CAST( UM2.meta_value AS SIGNED) DESC';
				}
				//最終ログイン
				if($page_orderby == 'login_date'){ 
					$login_date_order = 'asc';
					$user_order_data = ' ORDER BY UM3.meta_value DESC';
				}
				break;
		}


		//ページ辺りのユーザー数
		$users_per_page	= get_user_meta( $user_ID, 'users_per_page', true);
	//	$users_per_page	= 2;
		if( $users_per_page != '' ){
		}else{
			$users_per_page = 20;
		}


		$users_page_data = isset($_GET['paged']) ? $_GET['paged'] : '';	//ページ
		if($users_page_data < 2)
			$users_page_data="";


		//1ページに表示する物件数
		if($users_page_data == ""){
			$limit_from = "0";
			$limit_to = $users_per_page;
		}else{
			$limit_from = $users_per_page * $users_page_data - $users_per_page;
			$limit_to = $users_per_page;
		}

		//カウント
		$sql  = "SELECT count(DISTINCT U.ID) as co";
		$sql .=  " FROM (((($wpdb->users  AS U";
		$sql .=  " INNER JOIN $wpdb->usermeta AS UM   ON U.ID = UM.user_id ) ";
		$sql .=  " INNER JOIN $wpdb->usermeta AS UM2  ON U.ID = UM2.user_id )  ";
		$sql .=  " INNER JOIN $wpdb->usermeta AS UM3  ON U.ID = UM3.user_id ) ";
		$sql .=  " INNER JOIN $wpdb->usermeta AS UM4  ON U.ID = UM4.user_id ) ";
		$sql .=  " INNER JOIN $wpdb->usermeta AS UM5  ON U.ID = UM5.user_id  ";


		$sql .=  " WHERE UM.meta_key  = '".$wpdb->prefix."user_level' AND UM.meta_value ='0'";
	//	$sql .=  " AND UM2.meta_key  = 'user_mail' AND UM2.meta_value ='1'";
		$sql .=  " AND UM2.meta_key  = 'login_count'";
		$sql .=  " AND UM3.meta_key  = 'login_date'";
		$sql .=  " AND UM4.meta_key  = 'last_name'";
		$sql .=  " AND UM5.meta_key  = 'first_name'";

		//リスト
		$sql2 = str_replace("SELECT count(DISTINCT U.ID) as co","SELECT DISTINCT U.ID, U.user_login, U.user_email",$sql);
		$sql2 .=  $user_order_data;
		$sql2 .=  " LIMIT ".$limit_from.",".$limit_to."";


		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		$record_count = $metas->co;


		//ページナビ
		$joken_url = 'tools.php?page=fudoumail/admin_fudoumailsend.php';
		$page_navigation = '<div class="tablenav"><div class="tablenav-pages">';
		$page_navigation .= users_page_navi($record_count,$users_per_page,$users_page_data,$page_orderby,$page_order,$joken_url);
		$page_navigation .= '<br class="clear" /></div></div>';


		if (function_exists('add_thickbox')) add_thickbox();

?>

		<div id="post-body-content">

		<b>メール文章</b>
		<br />
		<form class="add:the-list: validate" method="post" action="tools.php?page=fudoumail%2Fadmin_fudoumailsend.php">
		<script src="http://nendeb.jp/acc5/acctag.js" type="text/javascript"></script>
		<input type="hidden" name="orderby" value="<?php echo $page_orderby; ?>" />
		<input type="hidden" name="order" value="<?php echo $page_order; ?>" />
		<input type="hidden" name="paged" value="<?php echo $users_page_data; ?>" />
		<input type="hidden" name="md" value="m" />


		<div style="margin:20px 0 0 20px;">

			<table border="0" cellpadding="0" cellspacing="0" id="rosenekimap">
				<tr>
					<td>差出人名<br /><input name="user_mail_fromname" type="text" value="<?php echo $user_mail_fromname; ?>" style="width:400px;" /></td>
					<td></td>
				</tr>
				<tr>
					<td>差出人メールアドレス<br /><input name="user_mail_frommail" type="text" value="<?php echo $user_mail_frommail; ?>" style="width:400px;" /></td>
					<td></td>
				</tr>
				<tr>
					<td>件名<br /><input name="user_mail_subject" type="text" value="<?php echo $user_mail_subject; ?>" style="width:400px;" /></td>
					<td></td>
				</tr>
				<tr>
					<td>本文<br /><textarea rows="10" cols="40" name="user_mail_comment" style="width:400px;"><?php echo $user_mail_comment; ?></textarea></td>
					<td><div style="margin:0 0 0 10px;">
						<b>パーソナライズ機能</b><br />
						会員登録した内容をもとに、メールの相手先ごとの情報をメール本文に差し込むことができます。
						下記キーワードを本文中に記述する事によってメール送信時に個別に置き換ります。<br /><br />
						[name]　姓名<br />
						[mail]　メールアドレス<br />
						[bukken]　個別に条件抽出された物件リスト<br />
						</div>
					</td>
				</tr>

				<tr>
					<td><br />メール掲載物件最大数<br /><input name="user_mail_bukkenlimit" type="text" value="<?php echo $user_mail_bukkenlimit; ?>" style="width:30px;" />物件</td>
					<td></td>
				</tr>

				<tr>
					<td><br />メール掲載対象物件<br />
					<select name="user_mail_kaiin">
					<option value="0"<?php if($user_mail_kaiin == "0") echo ' selected="selected"'; ?>>全ての物件</option>
					<option value="1"<?php if($user_mail_kaiin == "1") echo ' selected="selected"'; ?>>会員物件のみ</option>
					<option value="2"<?php if($user_mail_kaiin == "2") echo ' selected="selected"'; ?>>一般公開物件のみ</option>
					</select></td>
					<td></td>
				</tr>

				<tr>
					<td><br />メール送信間隔<br />
					<select name="user_mail_sleep">
					<option value="1"<?php if($user_mail_sleep == "1") echo ' selected="selected"'; ?>>1秒</option>
					<option value="2"<?php if($user_mail_sleep == "2") echo ' selected="selected"'; ?>>2秒</option>
					<option value="3"<?php if($user_mail_sleep == "3") echo ' selected="selected"'; ?>>3秒</option>
					<option value="4"<?php if($user_mail_sleep == "4") echo ' selected="selected"'; ?>>4秒</option>
					<option value="5"<?php if($user_mail_sleep == "5") echo ' selected="selected"'; ?>>5秒</option>
					</select>
					</td>
					<td></td>
				</tr>



			</table>
		</div>
		<p class="submit" style="margin:0 0 0 20px;"><input type="submit" name="" id="submit" class="button-primary" value="変更"  /></p>
		</form>
		</div>





		<div id="post-body-content">
		<script  type="text/javascript">
		<!-- <![CDATA[
			function confirm_mdcron() {
				var auto_send_index = document.auto_send.user_mail_cron.selectedIndex; 
				if(auto_send_index == '1'){
					res = confirm("１回目の送信状態になります。よろしいですか？");
				}else{
					res = confirm("自動メール送信を解除します。よろしいですか？");
				}
				if (res == true) {
					return true;
				} else {
					return false;
				}
			}
		// ]]> -->
		</script>

		<b>自動メール送信</b>

		<form class="add:the-list: validate" name="auto_send" method="post" action="tools.php?page=fudoumail%2Fadmin_fudoumailsend.php" onsubmit="return confirm_mdcron()">
		<input type="hidden" name="orderby" value="<?php echo $page_orderby; ?>" />
		<input type="hidden" name="order" value="<?php echo $page_order; ?>" />
		<input type="hidden" name="paged" value="<?php echo $users_page_data; ?>" />
		<input type="hidden" name="md" value="cron" />


		<div style="margin:20px 0 0 20px;">

			自動メール送信 <select name="user_mail_cron">
			<option value="0"<?php if($user_mail_cron != "1") echo ' selected="selected"'; ?>>自動メール送信しない</option>
			<option value="1"<?php if($user_mail_cron == "1") echo ' selected="selected"'; ?>>自動メール送信する</option>
			</select><br />

			送信間隔 <select name="user_mail_cron_daily">
			<option value="0"<?php if($user_mail_cron_daily != "1") echo ' selected="selected"'; ?>>毎週</option>
			<option value="1"<?php if($user_mail_cron_daily == "1") echo ' selected="selected"'; ?>>毎日</option>
			<!--
			<option value="2"<?php if($user_mail_cron_daily == "2") echo ' selected="selected"'; ?>>毎時(テスト用)</option>
			-->
			</select><br />

			「最終送信日」リセット <select name="user_mail_reset">
			<option value="0"<?php if($user_mail_reset != "1") echo ' selected="selected"'; ?>>リセットする</option>
			<option value="1"<?php if($user_mail_reset == "1") echo ' selected="selected"'; ?>>リセットしない</option>
			</select> 
			<br />* 会員が条件を設定する度に「最終送信日」をリセット する/しない を設定してください

			<br />
			<br />
			<font color="FF0000">【ご注意】</font><br />
			*設定変更するとすぐ１回目の送信状態になりますのでご注意ください。<br />
			*ユーザーが設定した条件で「最終送信日」以降に登録・修正された物件をリストにして自動で送信します。(物件リストが無いユーザーは送信されません)<br />
			　(但し会員が設定する度に「最終送信日」はリセットされます。)<br />
			*ユーザーが多い場合はサーバー負荷がかり、誤送信(未配信・重複送信等)をする場合がありますのでその際には使用を中止してください。<br />

		</div>
		<p class="submit" style="margin:0 0 0 20px;"><input type="submit" name="" id="submit" class="button-primary" value="変更"  /></p>
		</form>
		</div>





		<div id="post-body-content">
		<script  type="text/javascript">
		<!-- <![CDATA[
			function confirm_mds() {
				res = confirm("メールを送信してもよろしいですか？");
				if (res == true) {
					return true;
				} else {
					return false;
				}
			}
		// ]]> -->
		</script>
		<a name="userlist" id="userlist"></a>

		<b>手動メール送信</b>
		<div style="margin:0 0 0 20px;">
		<form class="add:the-list: validate" method="get" action="tools.php" onsubmit="return confirm_mds()">
		<input type="hidden" name="page" value="fudoumail/admin_fudoumailsend.php" />
		<input type="hidden" name="orderby" value="<?php echo $page_orderby; ?>" />
		<input type="hidden" name="order" value="<?php echo $page_order; ?>" />
		<input type="hidden" name="paged" value="<?php echo $users_page_data; ?>" />
		<input type="hidden" name="md" value="s" />

		<?php 	echo $page_navigation;?>
		<table class="wp-list-table widefat fixed users" cellspacing="0">
			<thead>
			<tr>
				<th scope='col' id='cb' class='manage-column column-cb check-column'  style=""><input type="checkbox" /></th>
				<th scope='col' id='username' class='manage-column column-username sortable desc'  style=""><a href="tools.php?page=fudoumail/admin_fudoumailsend.php&orderby=user_login&order=<?php echo $user_login_order; ?>#userlist"><span>ユーザー名</span><span class="sorting-indicator"></span></a></th>
				<th scope='col' id='name' class='manage-column column-name sortable desc'  style=""><a href="tools.php?page=fudoumail/admin_fudoumailsend.php&orderby=user_name&order=<?php echo $user_name_order; ?>#userlist"><span>名前</span><span class="sorting-indicator"></span></a></th>
				<th scope='col' id='email' class='manage-column column-email sortable desc'  style=""><a href="tools.php?page=fudoumail/admin_fudoumailsend.php&orderby=user_email&order=<?php echo $user_email_order; ?>#userlist"><span>メールアドレス</span><span class="sorting-indicator"></span></a></th>
				<th scope='col' id='login_count' class='manage-column column-login_count sortable desc'  style=""><a href="tools.php?page=fudoumail/admin_fudoumailsend.php&orderby=login_count&order=<?php echo $login_count_order; ?>#userlist"><span>ログイン数</span><span class="sorting-indicator"></span></a></th>
				<th scope='col' id='login_date' class='manage-column column-login_count sortable desc'  style=""><a href="tools.php?page=fudoumail/admin_fudoumailsend.php&orderby=login_date&order=<?php echo $login_date_order; ?>#userlist"><span>最終ログイン</span><span class="sorting-indicator"></span></a></th>
				<th scope='col'  class='manage-column column-role'  style="">メール送信数</th>
				<th scope='col'  class='manage-column column-posts num'  style="">最終送信日</th>	</tr>

			</tr>
			</thead>

			<tfoot>
			<tr>
				<th scope='col' id='cb' class='manage-column column-cb check-column'  style=""><input type="checkbox" /></th>
				<th scope='col' id='username' class='manage-column column-username sortable desc'  style=""><a href="tools.php?page=fudoumail/admin_fudoumailsend.php&orderby=user_login&order=<?php echo $user_login_order; ?>#userlist"><span>ユーザー名</span><span class="sorting-indicator"></span></a></th>
				<th scope='col' id='name' class='manage-column column-name sortable desc'  style=""><a href="tools.php?page=fudoumail/admin_fudoumailsend.php&orderby=user_name&order=<?php echo $user_name_order; ?>#userlist"><span>名前</span><span class="sorting-indicator"></span></a></th>
				<th scope='col' id='email' class='manage-column column-email sortable desc'  style=""><a href="tools.php?page=fudoumail/admin_fudoumailsend.php&orderby=user_email&order=<?php echo $user_email_order; ?>#userlist"><span>メールアドレス</span><span class="sorting-indicator"></span></a></th>
				<th scope='col' id='login_count' class='manage-column column-login_count sortable desc'  style=""><a href="tools.php?page=fudoumail/admin_fudoumailsend.php&orderby=login_count&order=<?php echo $login_count_order; ?>#userlist"><span>ログイン数</span><span class="sorting-indicator"></span></a></th>
				<th scope='col' id='login_date' class='manage-column column-login_count sortable desc'  style=""><a href="tools.php?page=fudoumail/admin_fudoumailsend.php&orderby=login_date&order=<?php echo $login_date_order; ?>#userlist"><span>最終ログイン</span><span class="sorting-indicator"></span></a></th>
				<th scope='col'  class='manage-column column-role'  style="">メール送信数</th>
				<th scope='col'  class='manage-column column-posts num'  style="">最終送信日</th>	</tr>
			</tfoot>

			<tbody id="the-list" class='list:user'>
<?php

			$sql2 = $wpdb->prepare($sql2,'');
			$metas = $wpdb->get_results( $sql2, ARRAY_A );
			if(!empty($metas)) {

				foreach ( $metas as $meta ) {
					$user_id= $meta['ID'];
					$user_login= $meta['user_login'];
					$user_email= $meta['user_email'];

					$user_mail = get_user_meta( $user_id, 'user_mail', true);

					$first_name	= get_user_meta( $user_id, 'first_name', true);
					$last_name	= get_user_meta( $user_id, 'last_name', true);

					$login_count = get_user_meta( $user_id, 'login_count', true);
					$login_date = get_user_meta( $user_id, 'login_date', true);

					$mail_count = get_user_meta( $user_id, 'mail_count', true);
					$mail_date = get_user_meta( $user_id, 'mail_date', true);
				//	if( $mail_date != '' ) $mail_date = date('Y-m-d H:i:s' ,$mail_date);

					echo '<tr id="user-'.$user_id.'">';
					echo '<th scope="row" class="check-column">';
					if($user_mail == 1 ) echo '<input type="checkbox" name="users[]" id="user_'.$user_id.'" class="subscriber" value="'.$user_id.'" />';
					echo '</th>';
					echo '<td class="username column-username"><strong><a href="user-edit.php?user_id='.$user_id.'&wp_http_referer=">'.$user_login.'</a></strong><br />';
					echo '<div class="row-actions"><span class="edit"><a href="user-edit.php?user_id='.$user_id.'&wp_http_referer=">編集</a>　';
					if($user_mail == 1 )
					echo '<a href="'.WP_PLUGIN_URL.'/fudoumail/fudoumailsend_view.php?user_id='.$user_id.'&KeepThis=true&TB_iframe=true&height=500&width=500" class="thickbox">メール内容</a>';
					echo '</span></div></td>';
					echo '	<td class="name column-name">'.$last_name.''.$first_name.'</td>';
					echo '	<td class="email column-email"><a href="mailto:'.$user_email.'" title="メールアドレス: '.$user_email.'">'.$user_email.'</a></td>';
					echo '	<td class="posts column-posts num">' . $login_count . '</td>';
					echo '	<td class="role column-role">' . $login_date . '</td>';

					echo '	<td class="posts column-posts num">' . $mail_count . '';
					if(!empty($mail_count))
						echo  '　<a href="'.WP_PLUGIN_URL.'/fudoumail/fudoumailview_user_sendlog.php?user_id='.$user_id.'&KeepThis=true&TB_iframe=true&height=500&width=500" class="thickbox">[ログ]</a>　　';
					echo '</td>';
					
					echo '	<td class="role column-role">' . $mail_date . '</td>';
					echo '</tr>';
				}
			}

?>

			</tbody>
		</table>
		<?php 	echo $page_navigation;?>
		<input type="checkbox" name="sendmailmaga" value="1" />物件情報が無くてもメール送信する (メルマガ配信用)

<?php

		if($user_mail_subject == ''){
			echo '<br /><b><font color="FF3300">件名を入力してください。</font></b>';
		}
		if($user_mail_comment == ''){
			echo '<br /><b><font color="FF3300">本文を入力してください。</font></b><br />';
		}
		if($user_mail_subject != '' && $user_mail_comment != ''){
			echo '<div class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="メール送信"  /></div>';
		}
?>


		</form>
		*送信したいユーザーにチェックを入れて「メール送信」ボタンを押してください。(「メールを受取る」にチェックを入れているユーザーのみ)<br />
		*ユーザーが設定した条件で「最終送信日」以降に登録・修正された物件をリストにして送信します。(物件リストが無いユーザーは送信されません)<br />
		*メルマガ配信用として「物件情報が無くてもメール送信する」にチェックを入れると物件リストが無くても送信対象になります。(手動メール配信専用)<br />
		*手動メール配信はリスト表示ページごとになりますので、配信作業はページごとに繰返してください。<br />
		</div>

		</div>
		</div>
		</div>

	</div>
<?php
	}

}


//ページナビゲション
function users_page_navi($record_count,$page_size,$page_count,$page_orderby,$page_order,$joken_url){

	$navi_max = 10;
	$k = 0;

	$move_str = $record_count.'件 ';

	if($page_count=="")
		$page_count =1;

	if ($record_count > $page_size){

		$w_max_page = intval($record_count / $page_size);

		if( ($record_count % $page_size) <> 0 )
			$w_max_page = $w_max_page + 1;

		if( intval($page_count) >= intval($navi_max)){
			$w_loop_start = $page_count - intval($navi_max/2);
		}else{
			$w_loop_start = 1;
		}

		if( $w_max_page < ($page_count + intval($navi_max/2)))
			$w_loop_start = $w_max_page-$navi_max + 1;

		if( $w_loop_start < 1)
			$w_loop_start =  1;

		if( $page_count > 1){
			$move_str .='<a class="prev-page" href="'.$joken_url.'&amp;paged='.($page_count-1).'&amp;orderby='.$page_orderby.'&amp;order='.$page_order.'">&laquo;</a> ';
		}


		if( $w_loop_start <> 1)
			$move_str .='<a class="prev-page" href="'.$joken_url.'&amp;paged=&amp;orderby='.$page_orderby.'&amp;order='.$page_order.'">1</a> ';

		if( $w_loop_start > 2)
			$move_str .='.. ';


		for ($j=$w_loop_start; $j<$w_max_page+1;$j++){

			if ($j == $page_count){
				$move_str .='<a class="current-page disabled">'.$j.'</a> ';
			}else{
				$move_str .='<a class="prev-page" href="'.$joken_url.'&amp;paged='.$j.'&amp;orderby='.$page_orderby.'&amp;order='.$page_order.'">'.$j.'</a> ';
			}
			
			$k++;
			if ($k >= $navi_max)
				break;
		}

		if($w_max_page > $j)
			$move_str .='.. ';

		if($w_max_page > $j ){
			if( $w_max_page > ($page_count + intval($navi_max/2)) )
				$move_str .='<a class="last-page" href="'.$joken_url.'&amp;paged='.($w_max_page).'&amp;orderby='.$page_orderby.'&amp;order='.$page_order.'">'.$w_max_page.'</a> ';
		}

		if( $record_count > $page_size * $page_count){
			$move_str .='<a class="next-page" href="'.$joken_url.'&amp;paged='.($page_count+1).'&amp;orderby='.$page_orderby.'&amp;order='.$page_order.'">&raquo;</a>';
		}


		if( $page_count > 1){
			$w_first_page = ($page_count - 1) * $page_size;
		}else{
			$w_first_page = 1;
		}

		return $move_str;
	}
}

?>