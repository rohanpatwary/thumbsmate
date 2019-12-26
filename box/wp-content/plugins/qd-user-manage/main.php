<?php
mb_language("Japanese");
mb_internal_encoding("UTF-8");
?>
<h2 style="font-weight:bold;">メール内容</h2>
<?php 
//お知らせ--------------------
if($_POST['merumaga_submit']||$_POST['jouken_submit']){
update_option('merumaga',esc_html($_POST['merumaga']));
update_option('merumaga_memo',esc_html($_POST['merumaga_memo']));
update_option('merumaga_title',esc_html($_POST['merumaga_title']));
update_option('merumaga_address',esc_html($_POST['merumaga_address']));
update_option('merumaga_mail',esc_html($_POST['merumaga_mail']));

if($_POST['merumaga_submit']){
echo '<div id="message" class="updated fade"><p><strong>メルマガの内容を保存しました。</strong></p></div>';}
}

//メルマガ内容--------------------
?>
<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
<p>差出人名<br />
<input type="text" name="merumaga_address" size="60"/ value="<?php echo get_option('merumaga_address');?>"><br />
差出人メールアドレス<br />
<input type="text" name="merumaga_mail" size="60" value="<?php echo get_option('merumaga_mail');?>"/><br />
件名<br />
<input type="text" name="merumaga_title" size="60" value="<?php echo get_option('merumaga_title');?>"/><br />
本文<br />
<div style="float:left"><textarea cols="60" rows="12" name="merumaga"><?php echo get_option('merumaga');?></textarea></div><div style="float:left;padding-left:10px"><strong>パーソナライズ機能</strong><br />会員登録した内容をもとに、メールの相手先ごとの情報をメール本文に差し込むことができます。 下記キーワードを本文中に記述する事によってメール送信時に個別に置き換ります。<br />
<br />
[name]　姓名</div>
<p style="clear:both;"><br />
送信メモ（ユーザーには表示されません。各ユーザーの条件メール送信履歴に残ります）：<br /><textarea cols="60" rows="5" name="merumaga_memo"><?php echo get_option('merumaga_memo');?></textarea></p>
<p><input type="submit" name="merumaga_submit" class="button-primary" value="メルマガを保存"></p>
<hr />
<?php 


//条件検索でユーザーを抽出-------------------
?>
<h2 style="font-weight:bold;">条件検索</h2>
<?php 
if($_POST['jouken_submit']){
//条件検索実行

echo '<div id="message" class="updated fade"><p><strong>条件でユーザーを絞りこみました。</strong></p></div>';

//チェックを引き継ぐ
echo '<script>(function($){$(function(){';
for($i=0;$i<count($_POST['shu']);$i++){?>
$('#shu<?php echo $_POST['shu'][$i];?>').attr('checked','checked');
<?php }
for($i=0;$i<count($_POST['sik']);$i++){?>
$('#sik<?php echo $_POST['sik'][$i];?>').attr('checked','checked');
<?php } 
for($i=0;$i<count($_POST['mad']);$i++){?>
$('#mad<?php echo $_POST['mad'][$i];?>').attr('checked','checked');
<?php }
for($i=0;$i<count($_POST['set']);$i++){?>
$('#set<?php echo $_POST['set'][$i];?>').attr('checked','checked');
<?php }?>
$('#kalb option[value="<?php echo $_POST['kalb'];?>"]').attr('selected','selected');
$('#kahb option[value="<?php echo $_POST['kahb'];?>"]').attr('selected','selected');
$('#kalc option[value="<?php echo $_POST['kalc'];?>"]').attr('selected','selected');
$('#kahc option[value="<?php echo $_POST['kahc'];?>"]').attr('selected','selected');
$('#tatemo_l option[value="<?php echo $_POST['tatemo_l'];?>"]').attr('selected','selected');
$('#tatemo_h option[value="<?php echo $_POST['tatemo_h'];?>"]').attr('selected','selected');
$('#hof option[value="<?php echo $_POST['hof'];?>"]').attr('selected','selected');
<?php 

echo '});})(jQuery);</script>';


//条件検索実行 完
}elseif($_POST['mailn_submit']){
//メール送信ループ
$send_address = $_POST['send_address'];
for($i = 0 ; $i < count($send_address); $i++){
	$to = get_userdata( $send_address[$i])->user_email;
	$subject = get_option('merumaga_title');
	$headers = 'From: '.get_option('merumaga_address').' <'.get_option('merumaga_mail').'>';
	//$headers[] = 'Cc: Cc宛先<ccatesaki@gmail.com>';
	//$headers[] = 'Bcc: Bcc宛先<bccatesaki@gmail.com>';
	$message = preg_replace('/(\[name\])/',get_userdata( $send_address[$i])->last_name,get_option('merumaga'));


	$wpmail = wp_mail($to,$subject,$message,$headers);
	$logs = get_user_meta($send_address[$i],'merumaga_log',true);
	$new = array(get_option('merumaga_title') , get_option('merumaga_memo'),time());
	$logs[] = $new;
	update_user_meta($send_address[$i],'merumaga_log',$logs);
	sleep(1);
}


if($wpmail){
echo '<div id="message" class="updated fade"><p><strong>'.count($send_address).'名にメールを送信しました。</strong></p></div>';
}elseif(!$wpmail){
echo '<div id="message" class="error fade"><p><strong>メール送信に失敗しました。</strong></p></div>';
}
}

if($_POST['jouken_submit']){
//条件検索実行 2

/*
print_r($_POST['shu']);
print_r($_POST['sik']);
print_r($_POST['kalb']);
print_r($_POST['kahb']);
print_r($_POST['kalc']);
print_r($_POST['kahc']);
print_r($_POST['tatemo_l']);
print_r($_POST['tatemo_h']);
print_r($_POST['mad']);
print_r($_POST['hof']);
print_r($_POST['set']);

echo '<pre>';
print_r($users);
echo '</pre>';*/

//回数
$umbers = 0;
$ken =array('shu','sik','mad','set','hof');
for($i=0;$i < count($ken);$i++){
if($_POST[$ken[$i]]){
$umbers++;
}
}

//地域
if($_POST['sik']){
$set_array = $_POST['sik'];
$q .= "((meta_key = 'user_mail_sik') AND (";

for($i=0; $i < count($set_array) ; $i++){
if($i != 0){ $q .= ' OR ';}
$q .= "(meta_value LIKE '%$set_array[$i]%')";
}$q .= '))';
}

//種別
if($_POST['shu']){
if($q){$q .= ' OR '; }
$set_array = $_POST['shu'];
$q .= "((meta_key = 'user_mail_shu') AND (";

for($i=0; $i < count($set_array) ; $i++){
if($i != 0){ $q .= ' OR ';}
$q .= "(meta_value LIKE '%$set_array[$i]%')";
}$q .= '))';
}

//間取り
if($_POST['mad']){
if($q){$q .= ' OR '; }
$set_array = $_POST['mad'];
$q .= "((meta_key = 'user_mail_madori') AND (";

for($i=0; $i < count($set_array) ; $i++){
if($i != 0){ $q .= ' OR ';}
$q .= "(meta_value LIKE '%$set_array[$i]%')";
}$q .= '))';
}

//駅
if($_POST['hof']){
if($q){$q .= ' OR '; }
$set_array = $_POST['hof'];
$q .= "((meta_key = 'user_mail_hohun') AND (meta_value <= $set_array ))";
}

//設備・条件
if($_POST['set']){
if($q){$q .= ' OR '; }
$set_array = $_POST['set'];
$q .= "((meta_key = 'user_mail_setsubi') AND (";

for($i=0; $i < count($set_array) ; $i++){
if($i != 0){ $q .= ' OR ';}
$q .= "(meta_value LIKE '%$set_array[$i]%')";
}$q .= '))';
}

//echo $q;

$results = $wpdb->get_results("SELECT user_id FROM $wpdb->usermeta WHERE $q");
//$results = $wpdb->get_results("SELECT user_id FROM $wpdb->usermeta WHERE ((meta_key = 'user_mail_shu') AND ((meta_value LIKE '%3101%'))) OR ((meta_key = 'user_mail_setsubi') AND ((meta_value LIKE '%10001%')))");

$ids = array();
$idset= array();
foreach($results as $r){
$user_id = $r->user_id;

if(in_array($r->user_id,$ids)){
$s = $idset[$r->user_id] + 1 ;
$idset[$user_id] = $s;
}else {
$ids[] = $r->user_id ;
$idset[$user_id] = 0;
}

if($idset[$user_id] == $umbers-1){
$users[] = $r;
}
}
/*
echo '<pre>';
print_r($idset);
echo '</pre>';


$q = array('orderby'=>ID,'order'=>ASC);
for($i=0;$i < count($_POST['shu']);$i++){
$q .= array('meta_key' => 'user_mail_shu','meta_value' => $_POST['shu'][$i],'meta_compare' => "LIKE");
}



$q .= array('meta_key' => 'user_mail_setsubi','meta_value' => '10001','meta_compare' => "LIKE");
$q .= array('meta_key' => 'user_mail_madori','meta_value' => '140','meta_compare' => "LIKE");
print_r($q);
$users =get_users( $q );
*/


//条件リスト
if(!$users){
echo '<div id="message" class="error fade"><p><strong>該当ユーザーが見つかりませんでした。</strong></p></div>';
}else{
?>
<div id="post-body-content">

<table id="user_table" class="wp-list-table widefat fixed users"><tr><th>メール送信</th><th>ユーザーID</th><th>ユーザー名</th><th>名前</th><th>メールアドレス</th><th>ログイン数</th><th>最終ログイン</th><th>メール送信数</th><th>最終送信日</th></tr>
<?php
foreach($users as $user){
$user_id = $user->user_id;
$userdata = get_userdata($user_id);
$user_count = get_user_meta( $user_id, 'mail_count', true);

echo
'<tr><td><input type="checkbox" name="send_address[]" value="'.$userdata->ID.'"></td>
<td>'.$userdata->ID.'</td>
<td><a href="user-edit.php?user_id='.$userdata->ID.'">'.$userdata->user_login.'</a></td>
<td>'.$userdata->last_name.'　'.$userdata->first_name.'</td>
<td>'.$userdata->user_email.'</td>
<td>'.get_user_meta( $user_id, 'login_count', true).'</td>
<td>'.get_user_meta( $user_id, 'login_date', true).'</td>
<td>'.get_user_meta( $user_id, 'mail_count', true);
if(!empty($user_count)){
echo  '　<a href="'.WP_PLUGIN_URL.'/fudoumail/fudoumailview_user_sendlog.php?user_id='.$user_id.'&KeepThis=true&TB_iframe=true&height=500&width=500" class="thickbox">[ログ]</a>　　';
}
echo '</td><td>'.get_user_meta( $user_id, 'mail_date', true).'</td></tr>';
}?>
</table>
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
<p><input type="submit" name="mailn_submit" class="button-primary" value="メール送信" onclick="return confirm_mds()"><br />
<span style="color:red">※送信数が多い場合は完了まで時間がかかります。</span></p>

</div>
<?php }




//リセット
echo '<p><a class="button-primary" href="'.$_SERVER['REQUEST_URI'].'">リセット</a></p>';
echo '<hr>';

//条件検索実行 2 完
}
?>
<div style="margin:0 0 20px 10px;"><?php 

	//条件種別
			fudou_registration_form_syubetsu();

	//条件エリア
			fudou_registration_form_area();

	//条件路線駅
			fudou_registration_form_roseneki();

	//条件価格
			fudou_registration_form_kakaku();

	//面積
			fudou_registration_form_memseki();

	//条件間取り
			fudou_registration_form_madori();

	//駅歩分
			fudou_registration_form_hofun();

	//条件設備
			fudou_registration_form_setsubi();

?>
<input type="submit" name="jouken_submit" id="submit" class="button-primary" value="条件で絞込検索">
</div></form>

<style>
#post-body-content {
margin-left: 8px;
margin: 20px auto;
line-height: 1.5;
padding: 16px 16px 30px;
border-radius: 11px;
background: #fff;
border: 1px solid #e5e5e5;
box-shadow: rgba(200, 200, 200, 1) 0 4px 18px;
width: 90%;
font-size: 12px;
float:none;
}
#user_table {
background-color:white;
font-size: 100%;
border-collapse: collapse;
border-spacing: 0;
}
#user_table th,#user_table td{
background-color:white;
padding:5px;
border:1px solid #eee;

}

</style>