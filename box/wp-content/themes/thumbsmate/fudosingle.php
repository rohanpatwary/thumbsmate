<?php
/**
 * The Template for displaying fudou single posts.
 *
 * Template Name: single-fudo.php
 * 
 * @package WordPress3.5
 * @subpackage Fudousan Plugin
 * Version: 1.2.0
 */

status_header( 200 );

global $is_fudouktai,$is_fudoumap,$is_fudoukaiin,$is_fudoumail,$is_fudourains;
global $post_id,$blogid;

$post_id = myIsNum_f($_GET['p']);

/** for Contact Form7 hack
global $wpcf7;
$wpcf7->processing_within = 'p' . $post_id;
$wpcf7->unit_count = 0;
**/

//会員
$kaiin = 0;
if( !is_user_logged_in() && get_post_meta($_GET['p'], 'kaiin', true) == 1 ) $kaiin = 1;
//ユーザー別会員物件リスト
$kaiin_users_rains_register = get_option('kaiin_users_rains_register');
$kaiin2 = users_kaiin_bukkenlist($post_id,$kaiin_users_rains_register,get_post_meta($post_id, 'kaiin', true));

//title変更
//if($kaiin == 1 ) {
if ( !my_custom_kaiin_view('kaiin_title',$kaiin,$kaiin2) ){
	add_action('wp_title', 'add_post_type_wp_title_ka');
}
function add_post_type_wp_title_ka($title = '') {
	$title =  '会員物件　';
	return $title;
}

//get_header();

the_post();
//add_filter('the_excerpt', 'do_shortcode'); 

$post_id_array = get_post( $post_id ); 
$title = $post_id_array->post_title;
$excerpt = $post_id_array->post_excerpt;
$content = $post_id_array->post_content;
$modified = $post_id_array->post_modified;


	//newup_mark

	$newup_mark = get_option('newup_mark');
	if($newup_mark == '') $newup_mark=14;

	$post_modified_date =  vsprintf("%d-%02d-%02d", sscanf($modified, "%d-%d-%d"));
	$post_date =  vsprintf("%d-%02d-%02d", sscanf($post_id_array->post_date, "%d-%d-%d"));

	$newup_mark_img =  '';
	if( $newup_mark != 0 && is_numeric($newup_mark) ){

		if( ( abs(strtotime($post_modified_date) - strtotime(date("Y/m/d"))) / (60 * 60 * 24) ) < $newup_mark ){
			if($post_modified_date == $post_date ){
				$newup_mark_img = '<div class="new_mark">new</div>';
			}else{
				$newup_mark_img =  '<div class="new_mark">up</div>';
			}
		}
	}



	//SSL
	$fudou_ssl_site_url = get_option('fudou_ssl_site_url');
	if( $fudou_ssl_site_url !=''){
		$site_url = $fudou_ssl_site_url;
	}else{
		$site_url = get_option('siteurl');
	}




?>
<div id="container" class="site-content">
	<div id="content" role="main">

		<div id="list_simplepage2">

			<!-- #nav-above -->
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2 class="entry-title">

					<?php if( get_post_meta($post_id, 'kaiin', true) == 1 ) { ?>
						<span style="float:right;margin:3px;margin-right:50px"><img src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/fudou/img/kaiin_s.jpg" alt="" /></span>
					<?php } ?>
					<?php 
					//会員項目表示判定
					if ( !my_custom_kaiin_view('kaiin_title',$kaiin,$kaiin2) ){
						echo "　会員物件";
					}else{
						echo $title;
					} 
					
					echo  $newup_mark_img;
					?>
				</h2>
<p class="txc"><?php

//アイキャッチで大きい画像表示
if(is_user_logged_in()){
if(has_post_thumbnail()){the_post_thumbnail(array('530','400'));} }

do_action( 'single-fudo1' ); ?></p>

				<!-- .entry-content -->

				<div class="list_simple_box">
					<div class="entry-excerpt">
					<?php
						if ( my_custom_kaiin_view('kaiin_excerpt',$kaiin,$kaiin2) ){
								echo $excerpt; 
						}

					?>
					</div>

					<?php if( $kaiin == 1 ) { ?>

						<?php if( $is_fudoukaiin && get_option('kaiin_users_can_register') == 1 ){ ?>

							<br />
							この物件は、「会員様にのみ限定公開」している物件です。<br />
							非公開物件につき、詳細情報の閲覧には会員ログインが必要です。<br />
							非公開物件を閲覧・資料請求するには会員登録が必要です。<br />

							<?php if( get_option('kaiin_moushikomi') != 1 ){ ?>
								まだ会員登録をしていない方は、簡単に会員登録ができますので是非ご登録ください。<br />
								<br />
								<div align="center">
								<a href="<?php echo $site_url; ?>/wp-content/plugins/fudoukaiin/wp-login.php?action=register&KeepThis=true&TB_iframe=true&height=500&width=400" class="thickbox">
								<img src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/fudou/img/kaiin_botton.jpg" alt="会員登録" /></a>
								</div>
							<?php } ?>
							<br />

						<?php }else{ ?>
							<br />
							この物件は、閲覧できません。<br />
							<br />
						<?php }

					}else{

						//ユーザー別会員物件リスト
						if ($kaiin2 === false ) {

							echo '<br />';
							echo 'この物件は、「閲覧条件に合った物件のみ公開」している物件です。<br />';
							echo '条件変更をする事で閲覧ができますので、閲覧条件の登録・変更をしてください。<br />';
							echo '<br />';
							echo '<div align="center">';
							echo '<div id="maching_mail"><a href="'.WP_PLUGIN_URL.'/fudoumail/fudou_user.php?KeepThis=true&TB_iframe=true&height=500&width=680" class="thickbox">';
							echo '閲覧条件・メール設定</a></div>';
							echo '</div>';
							echo '<br />';

						}else{

				?>
						        <!-- ここから左ブロック --> 
						        <div class="list_picsam"><ul>
				<?php

								//画像
								if (!defined('FUDOU_IMG_MAX')){
									$fudou_img_max = 10;
								}else{
									$fudou_img_max = FUDOU_IMG_MAX;
								}

								//サムネイル画像
								$img_path = get_option('upload_path');
								if ($img_path == '')	$img_path = 'wp-content/uploads';

								for( $imgid=1; $imgid<=10; $imgid++ ){

									$fudoimg_data = get_post_meta($post_id, "fudoimg$imgid", true);
									$fudoimgcomment_data = get_post_meta($post_id, "fudoimgcomment$imgid", true);
									$fudoimg_alt = $fudoimgcomment_data . my_custom_fudoimgtype_print(get_post_meta($post_id, "fudoimgtype$imgid", true));

									if($fudoimg_data !="" ){

											$sql  = "";
											$sql .=  "SELECT P.ID,P.guid";
											$sql .=  " FROM $wpdb->posts as P";
											$sql .=  " WHERE P.post_type ='attachment' AND P.guid LIKE '%/$fudoimg_data' ";
										//	$sql = $wpdb->prepare($sql,'');
											$metas = $wpdb->get_row( $sql );
											$attachmentid = '';
											if( !empty($metas) ){
												$attachmentid  =  $metas->ID;
												$guid_url  =  $metas->guid;
											}

											if($attachmentid !=''){
												//thumbnail、medium、large、full 
												$fudoimg_data1 = wp_get_attachment_image_src( $attachmentid, 'thumbnail');
												$fudoimg_url = $fudoimg_data1[0];

												echo '<li><a class="fancyimg" href="' . $guid_url . '" rel="lightbox lytebox['.$post_id.']" title="'.$fudoimg_alt.'">';
												if($fudoimg_url !=''){
													echo '<img src="' . $fudoimg_url.'" alt="'.$fudoimg_alt.'" title="'.$fudoimg_alt.'" /></a>';
												}else{
													echo '<img src="' . $guid_url . '" alt="'.$fudoimg_alt.'" title="'.$fudoimg_alt.'"  />';
												}
											}else{
												echo '<img src="'.WP_PLUGIN_URL.'/fudou/img/nowprinting.jpg" alt="'. $fudoimg_data .'" />';
											}

									}else{
										if( $imgid==1 )
										echo '<img src="'.WP_PLUGIN_URL.'/fudou/img/nowprinting.jpg" alt="" />';
									}
									echo "\n";
								}

								//携帯QR
								if ( $is_fudouktai ){
									$yoursubject = '%e7%89%a9%e4%bb%b6%e3%82%b5%e3%82%a4%e3%83%88%e3%81%aeURL'; //物件サイトのURL
									echo "\n";
									echo '<a href="mailto:?subject='.$yoursubject.'&body='. urlencode( get_permalink($post_id) ) .'">';
									$options = '';
									$culum3 = false;
									if (function_exists('unpc_get_theme_options')) 
										$options = unpc_get_theme_options();

									if ( is_array( $options ) ){
										$current_layout = $options['theme_layout'];
										if ( in_array( $current_layout, array( 'sidebar-content-sidebar' ) ) )
											$culum3 = true;
									}

									if ( $culum3 ){
										echo '<img src="http://chart.apis.google.com/chart?chs=100x100&amp;cht=qr&amp;chl=' . urlencode( get_permalink($post_id) ) . '" alt="クリックでURLをメール送信" title="クリックでURLをメール送信" /></a></li>';
									}else{
										echo '<img src="http://chart.apis.google.com/chart?chs=130x130&amp;cht=qr&amp;chl=' . urlencode( get_permalink($post_id) ) . '" alt="クリックでURLをメール送信" title="クリックでURLをメール送信" /></a></li>';
									}
								}
							?>
							</ul></div>



							<!-- ここから右ブロック -->
							<div class="list_detail">
<?php if(post_custom('jyoutai')==4&&is_user_logged_in()){
echo '<div class="contracts"><p class="red">成約済</p>

空室になったらお知らせします。<br /><a href="http://thumbsmate.jp/box/wp-content/plugins/fudoumail/fudou_user.php?KeepThis=true&TB_iframe=true&height=500&width=680" class="thickbox match">マッチング条件にご登録はこちら</a></div>';
}

do_action( 'single-fudo2' ); ?>

								<dl class="list_price<?php if( get_post_meta($post_id,'bukkenshubetsu',true) > 3000 ) echo ' rent'; ?>">
									<table width="100%">
										<tr>
											<td>
												<dt><?php if ( get_post_meta($post_id,'bukkenshubetsu',true) <3000 ) { echo '価格';}else{echo '賃料';} ?></dt>
												<dd><div class="dpoint4"><?php  if( get_post_meta($post_id, 'seiyakubi', true) != "" ){ echo 'ご成約済'; }else{  my_custom_kakaku_print($post_id); } ?></div></dd>
												<dd><?php my_custom_bukkenshubetsu_print($post_id); ?></dd>

												<?php if( get_post_meta($post_id, 'madorisu', true) !=""){ ;?>
												<dt>間取</dt><dd><div class="dpoint4"><?php my_custom_madorisu_print($post_id); ?></div></dd>
												<?php } ?>
												<br />

												<?php if( get_post_meta($post_id, 'kakakutsubo', true) !=""){ ;?>
												<dt>坪単価</dt><dd><?php my_custom_kakakutsubo_print($post_id) ;?></dd>
												<?php } ?>

												<?php if( get_post_meta($post_id, 'kakakukyouekihi', true) !=""){ ;?>
												<dt>共益費・管理費</dt><dd><?php echo get_post_meta($post_id, 'kakakukyouekihi', true);?>円</dd>
												<?php } ?>

												<?php if( get_post_meta($post_id, 'kakakuhyorimawari', true) !="" ||  get_post_meta($post_id, 'kakakurimawari', true) !=""){ ;?>
												<br /><dt>満室時表面利回り</dt><dd><?php echo get_post_meta($post_id, 'kakakuhyorimawari', true);?>%</dd>
												<dt>現行利回り</dt><dd><?php echo get_post_meta($post_id, 'kakakurimawari', true);?>%</dd>
												<?php } ?>

												<?php if( get_post_meta($post_id, 'shakuchiryo', true) !="" || get_post_meta($post_id, 'shakuchikikan', true) !=""){ ;?>
												<dt></dt><dd><?php echo my_custom_shakuchi_print($post_id);?></dd>
												<?php } ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php if( get_post_meta($post_id, 'kakakushikikin', true) !=""){ ;?>
												<dt>敷金</dt><dd><?php my_custom_kakakushikikin_print($post_id); ?></dd>
												<?php } ?>

												<?php if( get_post_meta($post_id, 'kakakureikin', true) !=""){ ;?>
												<dt>礼金</dt><dd><?php my_custom_kakakureikin_print($post_id); ?></dd>
												<?php } ?>

												<?php if( get_post_meta($post_id, 'kakakuhoshoukin', true) !=""){ ;?>
												<dt>保証金</dt><dd><?php my_custom_kakakuhoshoukin_print($post_id); ?></dd>
												<?php } ?>

												<?php if( get_post_meta($post_id, 'kakakukenrikin', true) !=""){ ;?>
												<dt>権利金</dt><dd><?php my_custom_kakakukenrikin_print($post_id); ?></dd>
												<?php } ?>

												<?php if( get_post_meta($post_id, 'kakakushikibiki', true) !=""){ ;?>
												<dt>償却・敷引金</dt><dd><?php my_custom_kakakushikibiki_print($post_id); ?></dd>
												<?php } ?>

												<?php if( get_post_meta($post_id, 'kakakukoushin', true) !=""){ ;?>
												<dt>更新料</dt><dd><?php my_custom_kakakukoushin_print($post_id); ?></dd>
												<?php } ?>

											</td>
										</tr>
									</table>
								</dl>

								<!-- 2列table -->
								<table width="100%" id="list_add">
									<tr>
										<th>所在地</th>
										<td><?php my_custom_shozaichi_print($post_id); ?><?php echo get_post_meta($post_id, 'shozaichimeisho', true); ?>
										<?php if ( get_post_meta($post_id,'bukkenmeikoukai',true) != '0' ) echo '<br />'. get_post_meta($post_id,'bukkenmei',true);?></td>
									</tr>
									<tr>
										<th>交通</th>
										<td><?php my_custom_koutsu1_print($post_id); ?>
										<?php my_custom_koutsu2_print($post_id); ?>
										<?php if( get_post_meta($post_id, 'koutsusonota', true) !="") 	echo '<br />'.get_post_meta($post_id, 'koutsusonota', true);?></td>
									</tr>
								</table>

								<table width="100%" id="list_other">

								<!-- 土地以外 -->
								<?php if ( get_post_meta($post_id,'bukkenshubetsu',true) >1200 && get_post_meta($post_id,'bukkenshubetsu',true) != 3212 ) { ?>
									<tr>
										<th>築年月</th>
										<td><?php echo get_post_meta($post_id, 'tatemonochikunenn', true);?></td>
										<th class="th2">新築/中古</th>
										<td><?php my_custom_tatemonoshinchiku_print($post_id); ?></td>
									</tr>
									<tr>
										<th>面積</th>
										<td><?php echo get_post_meta($post_id, 'tatemonomenseki', true);?>m&sup2;</td>
										<th class="th2">計測方式</th>
										<td><?php my_custom_tatemonohosiki_print($post_id); ?></td>
									</tr>
									<tr>
										<th>バルコニー</th>
										<td><?php echo get_post_meta($post_id, 'heyabarukoni', true);?>m&sup2;</td>  
										<th class="th2">向き</th>
										<td><?php my_custom_heyamuki_print($post_id); ?></td>
									</tr>
									<tr>
										<th>建物階数</th>
										<td><?php if(get_post_meta($post_id, 'tatemonokaisu1', true)!="") echo '地上'.get_post_meta($post_id, 'tatemonokaisu1', true).'階　' ;?>
										    <?php if(get_post_meta($post_id, 'tatemonokaisu2', true)!="") echo '地下'.get_post_meta($post_id, 'tatemonokaisu2', true).'階' ;?></td>
										<th class="th2">部屋階数</th>
										<td><?php echo get_post_meta($post_id, 'heyakaisu', true);?>階</td>
									</tr>
									<tr>
										<th>部屋/区画番号</th>
										<td><?php echo get_post_meta($post_id, 'bukkennaiyo', true);?></td>
										<th class="th2">総戸/区画数</th>
										<td><?php echo get_post_meta($post_id, 'bukkensoukosu', true);?></td>
									</tr>
									<tr>
										<th>建物構造</th>
										<td colspan="3"><?php my_custom_tatemonokozo_print($post_id) ?></td>
									</tr>


									<?php if( get_post_meta($post_id,'tatemonozentaimenseki',true)!='' || get_post_meta($post_id,'tatemononobeyukamenseki',true)!='' ){ ?>
									<tr>
										<th>敷地全体面積</th>
										<td><?php echo get_post_meta($post_id, 'tatemonozentaimenseki', true);?>m&sup2;</td>
										<th class="th2">延べ床面積</th>
										<td><?php echo get_post_meta($post_id, 'tatemononobeyukamenseki', true);?>m&sup2;</td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tatemonokentikumenseki',true)!=''){ ?>
									<tr>
										<th>建築面積</th>
										<td><?php echo get_post_meta($post_id, 'tatemonokentikumenseki', true);?>m&sup2;</td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id, 'kanrininn', true)!='' || get_post_meta($post_id, 'kanrikeitai', true)!='' || get_post_meta($post_id, 'kanrikumiai', true)!='' ){ ?>
									<tr>
										<th>管理形態</th>
										<td colspan="3">
										<?php my_custom_kanrikeitai_print($post_id); ?>
										<?php my_custom_kanrininn_print($post_id);?>
										<?php my_custom_kanrikumiai_print($post_id); ?>
										</td>
									</tr>
									<?php } ?>

									<tr>
										<th>間取内容</th>
										<td colspan="3"><?php my_custom_madorinaiyo_print($post_id); ?><br />
										<?php echo get_post_meta($post_id, 'madoribiko', true);?></td>
									</tr>

									<?php if( get_post_meta($post_id, 'kakakuhoken', true)!='' || get_post_meta($post_id, 'kakakuhokenkikan', true)!='' || get_post_meta($post_id, 'kakakutsumitate', true)!='' ){ ?>
									<tr>
										<th>住宅保険料</th>
										<td><?php my_custom_kakakuhoken_print($post_id);?><?php echo get_post_meta($post_id, 'kakakuhokenkikan', true);?>年</td>
										<th class="th2">修繕積立金</th>
										<td><?php echo get_post_meta($post_id, 'kakakutsumitate', true); ?>円</td>
									</tr>
									<?php } ?>
									  
								<?php } ?>
									<tr>
										<th>駐車場</th>
										<td><?php my_custom_chushajo_print($post_id); ?></td>
										<th class="th2">取引態様</th>
										<td><?php my_custom_torihikitaiyo_print($post_id); ?></td>
									</tr>
									<tr>
										<th>引渡/入居時期</th>
										<td><?php my_custom_nyukyojiki_print($post_id); ?>
										<?php echo get_post_meta($post_id, 'nyukyonengetsu', true);?>
										<?php my_custom_nyukyosyun_print($post_id);?></td>
										<th class="th2">現況</th>
										<td><?php my_custom_nyukyogenkyo_print($post_id); ?></td>
									</tr>


								<!-- 土地 -->
									<?php if( get_post_meta($post_id,'tochichimoku',true)!='' || get_post_meta($post_id,'tochiyouto',true)!='' ){ ?>
									<tr>
										<th>地目</th>
										<td><?php my_custom_tochichimoku_print($post_id); ?></td>
										<th class="th2">用途地域</th>
										<td><?php my_custom_tochiyouto_print($post_id); ?></td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochikeikaku',true)!='' || get_post_meta($post_id,'tochichisei',true)!='' ){ ?>
									<tr>
										<th>都市計画</th>
										<td><?php my_custom_tochikeikaku_print($post_id); ?></td>
										<th class="th2">地勢</th>
										<td><?php my_custom_tochichisei_print($post_id); ?></td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochikukaku',true)!='' || get_post_meta($post_id,'tochisokutei',true)!='' ){ ?>
									<tr>
										<th>区画面積</th>
										<td><?php echo get_post_meta($post_id, 'tochikukaku', true);?>m&sup2;</td>
										<th class="th2">土地面積計測方式</th>
										<td><?php my_custom_tochisokutei_print($post_id); ?></td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochishido',true)!='' ){ ?>
									<tr>
										<th>私道負担面積</th>
										<td colspan="3"><?php echo get_post_meta($post_id, 'tochishido', true);?>m&sup2;</td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochisetback',true)!='' || get_post_meta($post_id,'tochisetback2',true)!='' ){ ?>
									<tr>
										<th>セットバック</th>
										<td><?php my_custom_tochisetback_print($post_id); ?></td>
										<th class="th2">セットバック量</th>
										<td><?php echo get_post_meta($post_id, 'tochisetback2', true);?>m&sup2;</td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochikenpei',true)!='' || get_post_meta($post_id,'tochiyoseki',true)!='' ){ ?>
									<tr>
										<th>建ぺい率</th>
										<td><?php echo get_post_meta($post_id, 'tochikenpei', true);?>%</td>
										<th class="th2">容積率</th>
										<td><?php echo get_post_meta($post_id, 'tochiyoseki', true);?>%</td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochikenri',true)!='' || get_post_meta($post_id,'tochisetsudo',true)!='' ){ ?>
									<tr>
										<th>土地権利</th>
										<td><?php my_custom_tochikenri_print($post_id); ?></td>
										<th class="th2">接道状況</th>
										<td><?php my_custom_tochisetsudo_print($post_id); ?></td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochisetsudohouko1',true)!='' || get_post_meta($post_id,'tochisetsudomaguchi1',true)!='' ){ ?>
									<tr>
										<th>接道方向1</th>
										<td><?php my_custom_tochisetsudohouko1_print($post_id); ?></td>
										<th class="th2">接道間口1</th>
										<td><?php echo get_post_meta($post_id, 'tochisetsudomaguchi1', true);?>m</td>
									</tr>
									<?php } ?>


									<?php if( get_post_meta($post_id,'tochisetsudoshurui1',true)!='' || get_post_meta($post_id,'tochisetsudofukuin1',true)!='' ){ ?>
									<tr>
										<th>接道種別1</th>
										<td><?php my_custom_tochisetsudoshurui1_print($post_id); ?></td>
										<th class="th2">接道幅員1</th>
										<td><?php echo get_post_meta($post_id, 'tochisetsudofukuin1', true);?>m</td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochisetsudoichishitei1',true)!='' ){ ?>
									<tr>
										<th>位置指定道路1</th>
										<td colspan="3"><?php my_custom_tochisetsudoichishitei1_print($post_id); ?></td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochisetsudohouko2',true)!='' || get_post_meta($post_id,'tochisetsudomaguchi2',true)!='' ){ ?>
									<tr>
										<th>接道方向2</th>
										<td><?php my_custom_tochisetsudohouko2_print($post_id); ?></td>
										<th class="th2">接道間口2</th>
										<td><?php echo get_post_meta($post_id, 'tochisetsudomaguchi2', true);?>m</td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochisetsudoshurui2',true)!='' || get_post_meta($post_id,'tochisetsudofukuin2',true)!='' ){ ?>
									<tr>
										<th>接道種別2</th>
										<td><?php my_custom_tochisetsudoshurui2_print($post_id); ?></td>
										<th class="th2">接道幅員2</th>
										<td><?php echo get_post_meta($post_id, 'tochisetsudofukuin2', true);?>m</td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochisetsudoichishitei2',true)!='' ){ ?>
									<tr>
										<th>位置指定道路2</th>
										<td colspan="3"><?php my_custom_tochisetsudoichishitei2_print($post_id); ?></td>
									</tr>
									<?php } ?>

									<?php if( get_post_meta($post_id,'tochikokudohou',true)!='' ){ ?>
									<tr>
										<th>国土法届出</th>
										<td colspan="3"><?php my_custom_tochikokudohou_print($post_id); ?></td>
									</tr>
									<?php } ?>

								<!-- .土地 -->


									<tr>
										<th>周辺環境</th>
										<td colspan="3">
										<?php if( get_post_meta($post_id, 'shuuhenshougaku', true) !='' ){ ?>
											<?php echo get_post_meta($post_id, 'shuuhenshougaku', true);?>　
										<?php } ?>
										<?php if( get_post_meta($post_id, 'shuuhenchuugaku', true) !='' ){ ?>
											<?php echo get_post_meta($post_id, 'shuuhenchuugaku', true);?>
										<?php } ?>
										<?php if( get_post_meta($post_id,'shuuhensonota',true)!='' && ( get_post_meta($post_id, 'shuuhenshougaku', true) !='' || get_post_meta($post_id, 'shuuhenchuugaku', true) !='')){ ?>
											<br />
										<?php } ?>
											<?php echo get_post_meta($post_id, 'shuuhensonota', true);?>
										</td>
									</tr>

									<tr>
										<th>設備・条件</th>
										<td colspan="3"><ul class="bi"><?php my_custom_setsubi_print($post_id); ?></ul></td>
									</tr>

									<?php if( get_post_meta($post_id,'targeturl',true)!='' ){ ?>
									<tr>
										<th>URL</th>
										<td colspan="3"><?php my_custom_targeturl_print($post_id); ?></td>
									</tr>
									<?php } ?>

									<tr>
										<th>物件番号</th>
										<td<?php if( get_post_meta($post_id,'keisaikigenbi',true)=='' ) echo ' colspan="3"'; ?>>
										<?php echo get_post_meta($post_id, 'shikibesu', true);?></td>

										<?php if( get_post_meta($post_id,'keisaikigenbi',true)!='' ){ ?>
										<th class="th2">掲載期限日</th>
										<td><?php echo get_post_meta($post_id, 'keisaikigenbi', true);?></td>
										<?php } ?>
									</tr>

									<?php if( get_post_meta($post_id,'koukaijisha',true)!='' || get_post_meta($post_id,'jyoutai',true)!='' ){ ?>
									<tr>
										<th>自社物</th>
										<td><?php my_custom_koukaijisha_print($post_id);?></td>
										<th class="th2">状態</th>
										<td><?php my_custom_jyoutai_print($post_id);?></td>
									</tr>
									<?php } ?>


								<?php do_action( 'single-fudo3' ); ?>

								</table>

								<!-- $content  -->
								<div class="entry-content"><div class="point_mark"></div>
<div class="content_post"><?php the_content();?></div>
<div class="clear"></div>
</div>

						<!-- GoogleMaps v3 -->

						<?php 
							//map
							$lat = get_post_meta($post_id,'bukkenido',true);
							$lng = get_post_meta($post_id,'bukkenkeido',true);
							
							$bukkenshubetsu_data = get_post_meta($post_id,'bukkenshubetsu',true);
							$icon_url = 'gmapmark_'.$bukkenshubetsu_data.'.png';
							if($bukkenshubetsu_data == '')
								$icon_url = 'gmapmark_1399.png';


							if ($lat != "" && $lng != ""){
						?>
								<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
								<script type="text/javascript"> 
									var map;
									var gmapmark = '<?php echo WP_PLUGIN_URL;?>/fudou/img/<?php echo $icon_url;?>';

									function initialize() {

										var myLatLng = new google.maps.LatLng(<?php echo $lat;?>,<?php echo $lng;?>);
										var myOptions = {
											zoom: 16,
											center: myLatLng,
											scrollwheel: false,
											mapTypeId: google.maps.MapTypeId.ROADMAP,
											streetViewControl: true
										};
										map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

										//ElevationService
										elev = new google.maps.ElevationService();
										var latlng = new Array();
										latlng[0] = new google.maps.LatLng(<?php echo $lat;?>,<?php echo $lng;?>);
										var req = {locations: latlng};
										elev.getElevationForLocations(req, elevResultCallback);

									<?php if( get_option('fudo_map_directions') == 'true' ){ ?>
										//DirectionsService
										var From = "<?php echo my_custom_shozaichi_eki_map($post_id); ?>";
										var To = new google.maps.LatLng(<?php echo $lat;?>,<?php echo $lng;?>); 
										if(From != '' ){
											new google.maps.DirectionsService().route({
												origin: From, 
												destination: To,
												travelMode: google.maps.DirectionsTravelMode.WALKING 
											}, function(result, status) {
												if (status == google.maps.DirectionsStatus.OK) {
												//	new google.maps.DirectionsRenderer({map: map}).setDirections(result);
													new google.maps.DirectionsRenderer({map: map,suppressMarkers: true }).setDirections(result);
												}
											});
										}
									<?php } ?>


									}  //End function initialize()



									function elevResultCallback(result, status) {
										if (status != google.maps.ElevationStatus.OK) {
											var image = new google.maps.MarkerImage( gmapmark , new google.maps.Size(44,41));
											var marker = new google.maps.Marker({
												position:result[0].location,
												map:map,
												icon: image
											});
											var content = '<div class="msg"><?php if ( !my_custom_kaiin_view('kaiin_title',$kaiin,$kaiin2) ){echo "　会員物件";}else{echo $title;} ?>' + 
													'<br /><?php if ( get_post_meta($post_id,'bukkenshubetsu',true) <3000 ) { echo '価格';}else{echo '賃料';} ?> ' + 
													'<?php  if( get_post_meta($post_id, 'seiyakubi', true) != "" ){ echo 'ご成約済'; }else{  my_custom_kakaku_print($post_id); } ?>' + 
													'　<?php my_custom_bukkenshubetsu_print($post_id); ?></div>';

										}else{
											var image = new google.maps.MarkerImage( gmapmark , new google.maps.Size(44,41));
											var marker = new google.maps.Marker({
												position:result[0].location,
												title: '標高 ' + result[0].elevation.toFixed(0) + 'm' ,
												map:map,
												icon: image
											});

											var content = '<div class="msg"><?php if ( !my_custom_kaiin_view('kaiin_title',$kaiin,$kaiin2) ){echo "　会員物件";}else{echo $title;} ?>' + 
													'<br /><?php if ( get_post_meta($post_id,'bukkenshubetsu',true) <3000 ) { echo '価格';}else{echo '賃料';} ?> ' + 
													'<?php  if( get_post_meta($post_id, 'seiyakubi', true) != "" ){ echo 'ご成約済'; }else{  my_custom_kakaku_print($post_id); } ?>' + 
													'　<?php my_custom_bukkenshubetsu_print($post_id); ?>' + 
												<?php if( get_option('fudo_map_elevation') == 'true' ){ ?>
													'<br />標高 ' + result[0].elevation.toFixed(0) + 'm' +
												<?php } ?>
													'</div>';
										}

											var infowindow = new google.maps.InfoWindow({
												content: content ,
												maxWidth: 300
											//	size: new google.maps.Size(50, 50)
											});
											google.maps.event.addListener(marker, 'click', function() {
												infowindow.open(map, marker);
											});
									}


								</script>

								<script type="text/javascript">
									addOnload_single(function() { initialize(); });
									function addOnload_single(func){
										try {
											window.addEventListener("load", func, false);
										} catch (e) {   
											window.attachEvent("onload", func);   	// IE用
										}
									}
								</script>

								<style type="text/css">
								<!--
								#content #map_canvas img {margin: 0;height: auto;max-width: none;width: auto;}
								#content #map_canvas .msg {margin: 5px;font-size:12px;}
								-->
								</style>
								
								<div class="map_canvas" id="map_canvas" style="border:1px solid #979797; background-color:#e5e3df; width:100%; height:340px; z-index:1">
									<div style="padding:1em; color:gray;">Loading...</div>
								</div>
								<?php echo get_option('fudo_map_comment'); ?>

							<?php } ?>

						<!-- end GoogleMaps v3 -->



						<?php
							//画像 11～20
							if( $fudou_img_max > 10 ){

								echo '<div id="second_img">';
								for( $imgid=11; $imgid<=$fudou_img_max; $imgid++ ){

									$fudoimg_data = get_post_meta($post_id, "fudoimg$imgid", true);
									$fudoimgcomment_data = get_post_meta($post_id, "fudoimgcomment$imgid", true);
									$fudoimg_alt = $fudoimgcomment_data . my_custom_fudoimgtype_print(get_post_meta($post_id, "fudoimgtype$imgid", true));

									if($fudoimg_data !="" ){
											$attachmentid = '';
											$sql  = "";
											$sql .=  "SELECT P.ID,P.guid";
											$sql .=  " FROM $wpdb->posts as P";
											$sql .=  " WHERE P.post_type ='attachment' AND P.guid LIKE '%/$fudoimg_data' ";
										//	$sql = $wpdb->prepare($sql,'');
											$metas = $wpdb->get_row( $sql );
											if( !empty($metas) ){
												$attachmentid  =  $metas->ID;
												$guid_url  =  $metas->guid;
											}

											if($attachmentid !=''){
												//thumbnail、medium、large、full 
												$fudoimg_data1 = wp_get_attachment_image_src( $attachmentid, 'thumbnail');
												$fudoimg_url = $fudoimg_data1[0];

												echo '<a href="' . $guid_url . '" rel="lightbox lytebox['.$post_id.']" title="'.$fudoimg_alt.'">';
												if($fudoimg_url !=''){
													echo '<img src="' . $fudoimg_url.'" alt="'.$fudoimg_alt.'" title="'.$fudoimg_alt.'" width="100" /></a>';
												}else{
													echo '<img src="' . $guid_url . '" alt="'.$fudoimg_alt.'" title="'.$fudoimg_alt.'" width="100"  />';
												}
											}
									}
								}
								echo '</div>';
							}
						?>
							<!-- 物件詳細ウィジェット -->
							<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('syousai_widgets') ) : ?>
							<?php endif; ?>


							<?php do_action( 'single-fudo4' ); ?>


						</div><?php 
//物件詳細情報はここまで
//echo post_custom('jyoutai');
?>
<!-- .list_detail -->

						<div class="list_detail_bottom_info">※物件掲載内容と現況に相違がある場合は現況を優先と致します。</div>

					<?php } ?>

					<?php } ?><!-- //ユーザー別会員物件リスト -->


				</div><!-- .list_simple_box -->

				<?php edit_post_link( '編集', '<span class="edit-link">', '</span>' ); ?>

			</div><!-- .#nav-above#post-## -->
<?php 
			//SSL
			$fudou_ssl_site_url = get_option('fudou_ssl_site_url');

			//物件問合せ先
			echo '<div id="toiawasesaki">';
			if( $fudou_ssl_site_url !=''){

				$fudo_annnai = get_option('fudo_annnai');
				$fudo_annnai = apply_filters('the_content', $fudo_annnai);
				$fudo_annnai = str_replace(']]>', ']]&gt;', $fudo_annnai);
				echo $fudo_annnai;
			}else{
echo '<h3>お問い合わせ先</h3><p>株式会社サムズアップ</p>
<p class="tel">TEL:072-252-2025</p>
<p>〒591-8032　大阪府堺市北区百舌鳥梅町1-30-1</p>';
			}

			echo '</div>';

			do_action( 'single-fudo5' );

			if( $kaiin == 1 ) {
			}else{

				if ( $kaiin2 ){

					//SSL
					if( $fudou_ssl_site_url !=''){
						//SSL問合せフォーム
						echo '<div id="ssl_botton" align="center">';
						echo '<a href="'.$fudou_ssl_site_url.'/wp-content/plugins/fudou/themes/contact.php?p='.$post_id.'&action=register&KeepThis=true&TB_iframe=true&height=500&width=620" class="thickbox">';
						echo '<img src="'.get_option('siteurl').'/wp-content/plugins/fudou/img/ask_botton.jpg" alt="物件お問合せ" title="物件お問合せ" /></a>';
						echo '</div>';
					}else{

						//問合せフォーム
						echo '<div id="contact_form">';
						$fudo_form = get_option('fudo_form');
						$fudo_form = apply_filters('the_content', $fudo_form);
						$fudo_form = str_replace(']]>', ']]&gt;', $fudo_form);
						echo $fudo_form;
						echo '</div>';
					}
				}
			}

			//コメント
			if( FUDOU_TRA_COMMENT )	 comments_template( '', true ); 

			do_action( 'single-fudo6' );

?>
		</div><!-- .list_simplepage2 -->

	</div><!-- .#content -->

</div><!-- .#container -->

<?php //get_sidebar(); ?>
<?php //get_footer(); ?>



<?php

// 坪単価
function my_custom_kakakutsubo_print($post_id) {
	$kakakutsubo_data = get_post_meta($post_id,'kakakutsubo',true);
	if(is_numeric($kakakutsubo_data)){
		echo floatval($kakakutsubo_data)/10000;
		echo "万円";
	}
}

// 5公開	自社物フラグ 0:先物　1:自社物
function my_custom_koukaijisha_print($post_id) {
	$koukaijisha_data = get_post_meta($post_id,'koukaijisha',true);
	if($koukaijisha_data=="0")  echo '先物';
	if($koukaijisha_data=="1")  echo '自社物';
}

// 6状態	 1:空有/売出中 3:空無/売止 4:成約 9:削除 (1:空有/売出中 の場合に掲載されます)
function my_custom_jyoutai_print($post_id) {
	$jyoutai_data = get_post_meta($post_id,'jyoutai',true);
	if($jyoutai_data=="1")  echo '空有/売出中';
	if($jyoutai_data=="3")  echo '空無/売止';
	if($jyoutai_data=="4")  echo '成約';
	if($jyoutai_data=="9")  echo '削除';
}

// 7種別	物件種別
function my_custom_bukkenshubetsu_print($post_id) {

	$bukkenshubetsu_data = get_post_meta($post_id,'bukkenshubetsu',true);

	if($bukkenshubetsu_data=="1101")  echo '売地';
	if($bukkenshubetsu_data=="1102")  echo '借地権譲渡';
	if($bukkenshubetsu_data=="1103")  echo '底地権譲渡';
	if($bukkenshubetsu_data=="1201")  echo '新築戸建';
	if($bukkenshubetsu_data=="1202")  echo '中古戸建';
	if($bukkenshubetsu_data=="1203")  echo '新築テラスハウス';
	if($bukkenshubetsu_data=="1204")  echo '中古テラスハウス';
	if($bukkenshubetsu_data=="1301")  echo '新築マンション';
	if($bukkenshubetsu_data=="1302")  echo '中古マンション';
	if($bukkenshubetsu_data=="1303")  echo '新築公団';
	if($bukkenshubetsu_data=="1304")  echo '中古公団';
	if($bukkenshubetsu_data=="1305")  echo '新築公社';
	if($bukkenshubetsu_data=="1306")  echo '中古公社';
	if($bukkenshubetsu_data=="1307")  echo '新築タウン';
	if($bukkenshubetsu_data=="1308")  echo '中古タウン';
	if($bukkenshubetsu_data=="1309")  echo 'リゾートマン';

	if($bukkenshubetsu_data=="1399")  echo 'その他';


	if($bukkenshubetsu_data=="1401")  echo '店舗';
	if($bukkenshubetsu_data=="1403")  echo '店舗付住宅';
	if($bukkenshubetsu_data=="1404")  echo '住宅付店舗';
	if($bukkenshubetsu_data=="1405")  echo '事務所';
	if($bukkenshubetsu_data=="1406")  echo '店舗・事務所';
	if($bukkenshubetsu_data=="1407")  echo 'ビル';
	if($bukkenshubetsu_data=="1408")  echo '工場';
	if($bukkenshubetsu_data=="1409")  echo 'マンション';
	if($bukkenshubetsu_data=="1410")  echo '倉庫';
	if($bukkenshubetsu_data=="1411")  echo 'アパート';
	if($bukkenshubetsu_data=="1412")  echo '寮';
	if($bukkenshubetsu_data=="1413")  echo '旅館';
	if($bukkenshubetsu_data=="1414")  echo 'ホテル';
	if($bukkenshubetsu_data=="1415")  echo '別荘';
	if($bukkenshubetsu_data=="1416")  echo 'リゾートマン';
	if($bukkenshubetsu_data=="1420")  echo '社宅';

	if($bukkenshubetsu_data=="1421")  echo '文化住宅';

	if($bukkenshubetsu_data=="1499")  echo 'その他';
	if($bukkenshubetsu_data=="1502")  echo '店舗';
	if($bukkenshubetsu_data=="1505")  echo '事務所';
	if($bukkenshubetsu_data=="1506")  echo '店舗・事務所';
	if($bukkenshubetsu_data=="1507")  echo 'ビル';
	if($bukkenshubetsu_data=="1509")  echo 'マンション';
	if($bukkenshubetsu_data=="1599")  echo 'その他';
	if($bukkenshubetsu_data=="3101")  echo 'マンション';
	if($bukkenshubetsu_data=="3102")  echo 'アパート';
	if($bukkenshubetsu_data=="3103")  echo '一戸建';
	if($bukkenshubetsu_data=="3104")  echo 'テラスハウス';
	if($bukkenshubetsu_data=="3105")  echo 'タウンハウス';
	if($bukkenshubetsu_data=="3106")  echo '間借り';
	if($bukkenshubetsu_data=="3110")  echo '寮・下宿';
	if($bukkenshubetsu_data=="3201")  echo '店舗(建物全部)';
	if($bukkenshubetsu_data=="3202")  echo '店舗(建物一部)';
	if($bukkenshubetsu_data=="3203")  echo '事務所';
	if($bukkenshubetsu_data=="3204")  echo '店舗・事務所';
	if($bukkenshubetsu_data=="3205")  echo '工場';
	if($bukkenshubetsu_data=="3206")  echo '倉庫';
	if($bukkenshubetsu_data=="3207")  echo '一戸建';
	if($bukkenshubetsu_data=="3208")  echo 'マンション';
	if($bukkenshubetsu_data=="3209")  echo '旅館';
	if($bukkenshubetsu_data=="3210")  echo '寮';
	if($bukkenshubetsu_data=="3211")  echo '別荘';
	if($bukkenshubetsu_data=="3212")  echo '土地';
	if($bukkenshubetsu_data=="3213")  echo 'ビル';
	if($bukkenshubetsu_data=="3214")  echo '住宅付店舗(一戸建)';
	if($bukkenshubetsu_data=="3215")  echo '住宅付店舗(建物一部)';
	if($bukkenshubetsu_data=="3282")  echo '駐車場';
	if($bukkenshubetsu_data=="3299")  echo 'その他';

	if($bukkenshubetsu_data=="3122")  echo 'コーポ';
	if($bukkenshubetsu_data=="3123")  echo 'ハイツ';
	if($bukkenshubetsu_data=="3124")  echo '文化住宅';
	if($bukkenshubetsu_data=="1104")  echo '建付土地';

}


// map 駅
function my_custom_shozaichi_eki_map($post_id) {
	global $wpdb;

	$value = '';
	$shozaichiken_data = get_post_meta($post_id,'shozaichicode',true);
	$shozaichiken_data = myLeft($shozaichiken_data,2);

	if($shozaichiken_data=="")
		$shozaichiken_data = get_post_meta($post_id,'shozaichiken',true);

	$sql = "SELECT `middle_area_name` FROM `".$wpdb->prefix."area_middle_area` WHERE `middle_area_id`=".$shozaichiken_data."";
	$sql = $wpdb->prepare($sql,'');
	$metas = $wpdb->get_row( $sql );
	if(!empty($metas)) $value .= $metas->middle_area_name;


	$koutsurosen_data = get_post_meta($post_id, 'koutsurosen1', true);
	$koutsueki_data = get_post_meta($post_id, 'koutsueki1', true);


	if($koutsurosen_data !="" && $koutsueki_data !=""){
		$sql = "SELECT DTS.station_name";
		$sql = $sql . " FROM ".$wpdb->prefix."train_rosen AS DTR";
		$sql = $sql . " INNER JOIN ".$wpdb->prefix."train_station AS DTS ON DTR.rosen_id = DTS.rosen_id";
		$sql = $sql . " WHERE DTS.station_id=".$koutsueki_data." AND DTS.rosen_id=".$koutsurosen_data."";
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if(!empty($metas)){
			if($metas->station_name != '＊＊＊＊') 	$value .= $metas->station_name.'駅';
		}
	}

	return $value;

}



// 17所在地 所在地コード
function my_custom_shozaichi_print($post_id) {
	global $wpdb;

	$shozaichiken_data = get_post_meta($post_id,'shozaichicode',true);
	$shozaichiken_data = myLeft($shozaichiken_data,2);

	if($shozaichiken_data=="")
		$shozaichiken_data = get_post_meta($post_id,'shozaichiken',true);

		$sql = "SELECT `middle_area_name` FROM `".$wpdb->prefix."area_middle_area` WHERE `middle_area_id`=".$shozaichiken_data."";
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if(!empty($metas)) echo $metas->middle_area_name;

	$shozaichicode_data = get_post_meta($post_id,'shozaichicode',true);
	$shozaichicode_data = myLeft($shozaichicode_data,5);
	$shozaichicode_data = myRight($shozaichicode_data,3);

	if($shozaichiken_data !="" && $shozaichicode_data !=""){
		$sql = "SELECT narrow_area_name FROM ".$wpdb->prefix."area_narrow_area WHERE middle_area_id=".$shozaichiken_data." and narrow_area_id =".$shozaichicode_data."";

		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if(!empty($metas)) echo $metas->narrow_area_name;
	}
}

// 22交通路線1  (0：数値4桁 1：数値4桁)
function my_custom_koutsu1_print($post_id) {
	global $wpdb;

	$rosen_name = '';
	$koutsurosen_data = get_post_meta($post_id, 'koutsurosen1', true);
	$koutsueki_data = get_post_meta($post_id, 'koutsueki1', true);

	$shozaichiken_data = get_post_meta($post_id,'shozaichicode',true);
	$shozaichiken_data = myLeft($shozaichiken_data,2);


	if($koutsurosen_data !=""){
		$sql = "SELECT `rosen_name` FROM `".$wpdb->prefix."train_rosen` WHERE `rosen_id` =".$koutsurosen_data."";
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if(!empty($metas)){
			$rosen_name = $metas->rosen_name;
			echo "".$rosen_name;
		}
	}

	if($koutsurosen_data !="" && $koutsueki_data !=""){
		$sql = "SELECT DTS.station_name";
		$sql = $sql . " FROM ".$wpdb->prefix."train_rosen AS DTR";
		$sql = $sql . " INNER JOIN ".$wpdb->prefix."train_station AS DTS ON DTR.rosen_id = DTS.rosen_id";
		$sql = $sql . " WHERE DTS.station_id=".$koutsueki_data." AND DTS.rosen_id=".$koutsurosen_data."";
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if(!empty($metas)){
			if($metas->station_name != '＊＊＊＊') 	echo $metas->station_name.'駅';
		}
	}

	$koutsubusstei=get_post_meta($post_id, 'koutsubusstei1', true);
	$koutsubussfun=get_post_meta($post_id, 'koutsubussfun1', true);
	$koutsutohob1f=get_post_meta($post_id, 'koutsutohob1f', true);
	if($koutsubusstei !="" || $koutsubussfun !=""){

		if($rosen_name == 'バス'){
			echo '(' . $koutsubusstei;
			if(!empty($koutsubussfun)) echo ' '.$koutsubussfun.'分';
		}else{
			echo ' バス(' . $koutsubusstei;
				if(!empty($koutsubussfun)) echo ' '.$koutsubussfun.'分';
		}

		if($koutsutohob1f !="" )
			echo ' 停歩'.$koutsutohob1f.'分';
		echo ')';
	}

	if(get_post_meta($post_id, 'koutsutoho1', true) !="")
		echo ' 徒歩'.get_post_meta($post_id, 'koutsutoho1', true).'m';

	if(get_post_meta($post_id, 'koutsutoho1f', true) !="")
		echo ' 徒歩'.get_post_meta($post_id, 'koutsutoho1f', true).'分';
}

function my_custom_koutsu2_print($post_id) {
	global $wpdb;

	$rosen_name = '';
	$koutsurosen_data = get_post_meta($post_id, 'koutsurosen2', true);
	$koutsueki_data = get_post_meta($post_id, 'koutsueki2', true);

	$shozaichiken_data = get_post_meta($post_id,'shozaichicode',true);
	$shozaichiken_data = myLeft($shozaichiken_data,2);

	if($koutsurosen_data !=""){
		$sql = "SELECT `rosen_name` FROM `".$wpdb->prefix."train_rosen` WHERE `rosen_id` =".$koutsurosen_data."";
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if(!empty($metas)){
			$rosen_name = $metas->rosen_name;
			echo "<br />".$rosen_name;
		}
	}

	if($koutsurosen_data !="" && $koutsueki_data !=""){
		$sql = "SELECT DTS.station_name";
		$sql = $sql . " FROM ".$wpdb->prefix."train_rosen AS DTR";
		$sql = $sql . " INNER JOIN ".$wpdb->prefix."train_station AS DTS ON DTR.rosen_id = DTS.rosen_id";
		$sql = $sql . " WHERE DTS.station_id=".$koutsueki_data." AND DTS.rosen_id=".$koutsurosen_data."";
		$sql = $wpdb->prepare($sql,'');
		$metas = $wpdb->get_row( $sql );
		if(!empty($metas)){
			if($metas->station_name != '＊＊＊＊') 	echo $metas->station_name.'駅';
		}
	}

	$koutsubusstei=get_post_meta($post_id, 'koutsubusstei2', true);
	$koutsubussfun=get_post_meta($post_id, 'koutsubussfun2', true);
	$koutsutohob2f=get_post_meta($post_id, 'koutsutohob2f', true);
	if($koutsubusstei !="" || $koutsubussfun !=""){

		if($rosen_name == 'バス'){
			echo '(' . $koutsubusstei;
			if(!empty($koutsubussfun)) echo ' '.$koutsubussfun.'分';
		}else{
			echo ' バス(' . $koutsubusstei;
				if(!empty($koutsubussfun)) echo ' '.$koutsubussfun.'分';
		}

		if($koutsutohob2f !="" )
			echo ' 停歩'.$koutsutohob2f.'分';
		echo ')';
	}

	if(get_post_meta($post_id, 'koutsutoho2', true) !="")
		echo ' 徒歩'.get_post_meta($post_id, 'koutsutoho2', true).'m';

	if(get_post_meta($post_id, 'koutsutoho2f', true) !="")
		echo ' 徒歩'.get_post_meta($post_id, 'koutsutoho2f', true).'分';
}

// 71建物	建物構造
function my_custom_tatemonokozo_print($post_id) {
	$tatemonokozo_data = get_post_meta($post_id,'tatemonokozo',true);
	if($tatemonokozo_data=="1") 	echo '木造';
	if($tatemonokozo_data=="2") 	echo 'ブロック';
	if($tatemonokozo_data=="3") 	echo '鉄骨造';
	if($tatemonokozo_data=="4") 	echo 'RC';
	if($tatemonokozo_data=="5") 	echo 'SRC';
	if($tatemonokozo_data=="6") 	echo 'PC';
	if($tatemonokozo_data=="7") 	echo 'HPC';
	if($tatemonokozo_data=="9") 	echo 'その他';
	if($tatemonokozo_data=="10") 	echo '軽量鉄骨';
	if($tatemonokozo_data=="11") 	echo 'ALC';
	if($tatemonokozo_data=="12") 	echo '鉄筋ブロック';
	if($tatemonokozo_data=="13") 	echo 'CFT(コンクリート充填鋼管)';

	//text
	if( $tatemonokozo_data !='' && !is_numeric($tatemonokozo_data) ) echo $tatemonokozo_data;
}

// 72建物	建物面積計測方式
function my_custom_tatemonohosiki_print($post_id) {
	if(get_post_meta($post_id,'tatemonohosiki',true)=="1")	echo '壁芯';
	if(get_post_meta($post_id,'tatemonohosiki',true)=="2")	echo '内法';
	//text
	if( get_post_meta($post_id,'tatemonohosiki',true) !='' && !is_numeric(get_post_meta($post_id,'tatemonohosiki',true)) ) echo get_post_meta($post_id,'tatemonohosiki',true);
}

// 80建物	新築・未入居
function my_custom_tatemonoshinchiku_print($post_id) {
	if(get_post_meta($post_id,'tatemonoshinchiku',true)=="0") echo '中古';
	if(get_post_meta($post_id,'tatemonoshinchiku',true)=="1") echo '新築・未入居';
	//text
	if( get_post_meta($post_id,'tatemonoshinchiku',true) !='' && !is_numeric(get_post_meta($post_id,'tatemonoshinchiku',true)) ) echo get_post_meta($post_id,'tatemonoshinchiku',true);
}

// 81	管理人	管理人 1:常駐 2:日勤 3:巡回 4:無 5:非常駐
function my_custom_kanrininn_print($post_id) {
	if(get_post_meta($post_id,'kanrininn',true)=="1")	echo '管理人常駐　';
	if(get_post_meta($post_id,'kanrininn',true)=="2")	echo '管理人日勤　';
	if(get_post_meta($post_id,'kanrininn',true)=="3")	echo '管理人巡回　';
	if(get_post_meta($post_id,'kanrininn',true)=="4")	echo '管理人無　';
	if(get_post_meta($post_id,'kanrininn',true)=="5")	echo '管理人非常駐　';
	//text
	if( get_post_meta($post_id,'kanrininn',true) !='' && !is_numeric(get_post_meta($post_id,'kanrininn',true)) ) echo get_post_meta($post_id,'kanrininn',true);
}

// 82	管理形態 【REINS】1:自主管理 2:一部委託 3:全部委託
function my_custom_kanrikeitai_print($post_id) {
	if(get_post_meta($post_id,'kanrikeitai',true)=="1")	echo '自主管理　';
	if(get_post_meta($post_id,'kanrikeitai',true)=="2")	echo '一部委託　';
	if(get_post_meta($post_id,'kanrikeitai',true)=="3")	echo '全部委託　';
	//text
	if( get_post_meta($post_id,'kanrikeitai',true) !='' && !is_numeric(get_post_meta($post_id,'kanrikeitai',true)) ) echo get_post_meta($post_id,'kanrikeitai',true);
}

// 83	管理組合有無 1:無 2:有
function my_custom_kanrikumiai_print($post_id) {
	if(get_post_meta($post_id,'kanrikumiai',true)=="1")	echo '管理組合無';
	if(get_post_meta($post_id,'kanrikumiai',true)=="2")	echo '管理組合有';
	//text
	if( get_post_meta($post_id,'kanrikumiai',true) !='' && !is_numeric(get_post_meta($post_id,'kanrikumiai',true)) ) echo get_post_meta($post_id,'kanrikumiai',true);
}

// 87部屋	向き
function my_custom_heyamuki_print($post_id) {
	if(get_post_meta($post_id,'heyamuki',true)=="1") 	echo '北';
	if(get_post_meta($post_id,'heyamuki',true)=="2") 	echo '北東';
	if(get_post_meta($post_id,'heyamuki',true)=="3") 	echo '東';
	if(get_post_meta($post_id,'heyamuki',true)=="4") 	echo '南東';
	if(get_post_meta($post_id,'heyamuki',true)=="5") 	echo '南';
	if(get_post_meta($post_id,'heyamuki',true)=="6") 	echo '南西';
	if(get_post_meta($post_id,'heyamuki',true)=="7") 	echo '西';
	if(get_post_meta($post_id,'heyamuki',true)=="8") 	echo '北西';
	//text
	if( get_post_meta($post_id,'heyamuki',true) !='' && !is_numeric(get_post_meta($post_id,'heyamuki',true)) ) echo get_post_meta($post_id,'heyamuki',true);
}

// 88	間取り	間取部屋数
// 89	間取り	間取部屋種類
function my_custom_madorisu_print($post_id) {
	$madorisyurui_data = get_post_meta($post_id,'madorisyurui',true);
	echo get_post_meta($post_id,'madorisu',true);
	if($madorisyurui_data=="10")	echo 'R';
	if($madorisyurui_data=="20")	echo 'K';
	if($madorisyurui_data=="25")	echo 'SK';
	if($madorisyurui_data=="30")	echo 'DK';
	if($madorisyurui_data=="35")	echo 'SDK';
	if($madorisyurui_data=="40")	echo 'LK';
	if($madorisyurui_data=="45")	echo 'SLK';
	if($madorisyurui_data=="50")	echo 'LDK';
	if($madorisyurui_data=="55")	echo 'SLDK';
}

// 90間取り 間取(種類) 
// 91間取り 間取(畳数)
// 92間取り 間取(所在階)
// 93間取り 間取(室数)
function my_custom_madorinaiyo_print($post_id) {
	$work_madorinaiyo =
	array(	
	"madorikai1"    => array("name" => "madorikai1"   ,"std" => "","title" => "所在階","type" => "text","description" => "階"),
	"madorisyurui1" => array("name" => "madorisyurui1","std" => "","title" => "種類","type" => "select","description" => ""),
	"madorijyousu1" => array("name" => "madorijyousu1","std" => "","title" => "畳数","type" => "text","description" => "畳"),
	"madorishitsu1" => array("name" => "madorishitsu1","std" => "","title" => "室数","type" => "text","description" => "室　"),
	"madoriend1    " => array("name" => "","std" => "","title" => " ","type" => "end","description" => ""),

	"madorikai2"    => array("name" => "madorikai2"   ,"std" => "","title" => "所在階","type" => "text","description" => "階"),
	"madorisyurui2" => array("name" => "madorisyurui2","std" => "","title" => "種類","type" => "select","description" => ""),
	"madorijyousu2" => array("name" => "madorijyousu2","std" => "","title" => "畳数","type" => "text","description" => "畳"),
	"madorishitsu2" => array("name" => "madorishitsu2","std" => "","title" => "室数","type" => "text","description" => "室　"),
	"madoriend2    " => array("name" => "","std" => "","title" => " ","type" => "end","description" => ""),

	"madorikai3   " => array("name" => "madorikai3"   ,"std" => "","title" => "所在階","type" => "text","description" => "階"),
	"madorisyurui3" => array("name" => "madorisyurui3","std" => "","title" => "種類","type" => "select","description" => ""),
	"madorijyousu3" => array("name" => "madorijyousu3","std" => "","title" => "畳数","type" => "text","description" => "畳"),
	"madorishitsu3" => array("name" => "madorishitsu3","std" => "","title" => "室数","type" => "text","description" => "室　"),
	"madoriend3    " => array("name" => "","std" => "","title" => " ","type" => "end","description" => ""),

	"madorikai4"    => array("name" => "madorikai4"   ,"std" => "","title" => "所在階","type" => "text","description" => "階"),
	"madorisyurui4" => array("name" => "madorisyurui4","std" => "","title" => "種類","type" => "select","description" => ""),
	"madorijyousu4" => array("name" => "madorijyousu4","std" => "","title" => "畳数","type" => "text","description" => "畳"),
	"madorishitsu4" => array("name" => "madorishitsu4","std" => "","title" => "室数","type" => "text","description" => "室"),
	"madoriend4    " => array("name" => "","std" => "","title" => " ","type" => "end","description" => ""),

	"madorikai5"    => array("name" => "madorikai5"   ,"std" => "","title" => "所在階","type" => "text","description" => "階"),
	"madorisyurui5" => array("name" => "madorisyurui5","std" => "","title" => "種類","type" => "select","description" => ""),
	"madorijyousu5" => array("name" => "madorijyousu5","std" => "","title" => "畳数","type" => "text","description" => "畳"),
	"madorishitsu5" => array("name" => "madorishitsu5","std" => "","title" => "室数","type" => "text","description" => "室"),
	"madoriend5    " => array("name" => "","std" => "","title" => " ","type" => "end","description" => ""),

	"madorikai6"    => array("name" => "madorikai6"   ,"std" => "","title" => "所在階","type" => "text","description" => "階"),
	"madorisyurui6" => array("name" => "madorisyurui6","std" => "","title" => "種類","type" => "select","description" => ""),
	"madorijyousu6" => array("name" => "madorijyousu6","std" => "","title" => "畳数","type" => "text","description" => "畳"),
	"madorishitsu6" => array("name" => "madorishitsu6","std" => "","title" => "室数","type" => "text","description" => "室"),
	"madoriend6    " => array("name" => "","std" => "","title" => " ","type" => "end","description" => ""),

	"madorikai7"    => array("name" => "madorikai7"   ,"std" => "","title" => "所在階","type" => "text","description" => "階"),
	"madorisyurui7" => array("name" => "madorisyurui7","std" => "","title" => "種類","type" => "select","description" => ""),
	"madorijyousu7" => array("name" => "madorijyousu7","std" => "","title" => "畳数","type" => "text","description" => "畳"),
	"madorishitsu7" => array("name" => "madorishitsu7","std" => "","title" => "室数","type" => "text","description" => "室"),
	"madoriend7    " => array("name" => "","std" => "","title" => " ","type" => "end","description" => ""),

	"madorikai8"    => array("name" => "madorikai8"   ,"std" => "","title" => "所在階","type" => "text","description" => "階"),
	"madorisyurui8" => array("name" => "madorisyurui8","std" => "","title" => "種類","type" => "select","description" => ""),
	"madorijyousu8" => array("name" => "madorijyousu8","std" => "","title" => "畳数","type" => "text","description" => "畳"),
	"madorishitsu8" => array("name" => "madorishitsu8","std" => "","title" => "室数","type" => "text","description" => "室"),
	"madoriend8    " => array("name" => "","std" => "","title" => " ","type" => "end","description" => ""),

	"madorikai9"    => array("name" => "madorikai9"   ,"std" => "","title" => "所在階","type" => "text","description" => "階"),
	"madorisyurui9" => array("name" => "madorisyurui9","std" => "","title" => "種類","type" => "select","description" => ""),
	"madorijyousu9" => array("name" => "madorijyousu9","std" => "","title" => "畳数","type" => "text","description" => "畳"),
	"madorishitsu9" => array("name" => "madorishitsu9","std" => "","title" => "室数","type" => "text","description" => "室"),
	"madoriend9    " => array("name" => "","std" => "","title" => " ","type" => "end","description" => ""),

	"madorikai10"    => array("name" => "madorikai10"   ,"std" => "","title" => "所在階","type" => "text","description" => "階"),
	"madorisyurui10" => array("name" => "madorisyurui10","std" => "","title" => "種類","type" => "select","description" => ""),
	"madorijyousu10" => array("name" => "madorijyousu10","std" => "","title" => "畳数","type" => "text","description" => "畳"),
	"madorishitsu10" => array("name" => "madorishitsu10","std" => "","title" => "室数","type" => "text","description" => "室")
	);

	foreach($work_madorinaiyo as $meta_box) {

		$madorinaiyo_data = get_post_meta($post_id, $meta_box['name'], true);

		if($madorinaiyo_data == "")
			$madorinaiyo_data = $meta_box['std'];

		if('select'==$meta_box['type']){
			if($madorinaiyo_data=="1") echo '和室';
			if($madorinaiyo_data=="2") echo '洋室';
			if($madorinaiyo_data=="3") echo 'DK';
			if($madorinaiyo_data=="4") echo 'LDK';
			if($madorinaiyo_data=="5") echo 'L';
			if($madorinaiyo_data=="6") echo 'D';
			if($madorinaiyo_data=="7")  echo 'K';
			if($madorinaiyo_data=="9")  echo 'その他';
			if($madorinaiyo_data=="21")  echo 'LK';
			if($madorinaiyo_data=="22")  echo 'LD';
			if($madorinaiyo_data=="23")  echo 'S';

		}else{

			if('end'==$meta_box['type']){
				echo '　';
			}else{
				if($madorinaiyo_data != "")
					echo ''.$madorinaiyo_data.''.$meta_box['description'].'';
			}
		}
	}
}

// 137	備考	URL	00:物件ページ,01:動画,02:パノラマ,03:自社HP)
function my_custom_targeturl_print($post_id) {
	$targeturl_data = get_post_meta($post_id,'targeturl',true);
	if($targeturl_data !=''){
		if(myLeft($targeturl_data,1)=="0"){
			$targeturl_data = myRight($targeturl_data,mb_strlen($targeturl_data)-3);
		}
		echo '<a href="'.$targeturl_data.'" target="_blank">'.$targeturl_data.'</a>';
	}
}

// 139	金銭面	賃料・価格
function my_custom_kakaku_print($post_id) {
	//非公開の場合
	if(get_post_meta($post_id,'kakakukoukai',true) == "0"){

		$kakakujoutai_data = get_post_meta($post_id,'kakakujoutai',true);
		if($kakakujoutai_data=="1")	echo '相談';
		if($kakakujoutai_data=="2")	echo '確定';
		if($kakakujoutai_data=="3")	echo '入札';

	}else{
		$kakaku_data = get_post_meta($post_id,'kakaku',true);
		if(is_numeric($kakaku_data)){
			echo floatval($kakaku_data)/10000;
			echo "万円";
		}
	}
}

// 147	金銭面	礼金・月数 ※ 100以上の場合は単位は"円"、それ以外は"ヶ月"
function my_custom_kakakureikin_print($post_id) {
	$kakakureikin_data = get_post_meta($post_id,'kakakureikin',true);
//	if(is_numeric($kakakureikin_data)){
		if($kakakureikin_data >= 100) {
			echo floatval($kakakureikin_data)/10000;
			echo "万円";
		}else{
			echo $kakakureikin_data;
			echo "ヶ月";
		}
//	}
}

// 149	金銭面	敷金・月数 ※ 100以上の場合は単位は"円"、それ以外は"ヶ月"
function my_custom_kakakushikikin_print($post_id) {
	$kakakushikikin_data = get_post_meta($post_id,'kakakushikikin',true);
//	if(is_numeric($kakakushikikin_data)){
		if($kakakushikikin_data >= 100) {
			echo floatval($kakakushikikin_data)/10000;
			echo "万円";
		}else{
			echo $kakakushikikin_data;
			echo "ヶ月";
//		}
	}
}

// 150	金銭面	保証金・月数 ※ 100以上の場合は単位は 円、それ以外はヶ月
function my_custom_kakakuhoshoukin_print($post_id) {
	$kakakuhoshoukin_data = get_post_meta($post_id,'kakakuhoshoukin',true);
//	if(is_numeric($kakakuhoshoukin_data)){
		if($kakakuhoshoukin_data >= 100) {
			echo floatval($kakakuhoshoukin_data)/10000;
			echo "万円";
		}else{
			echo $kakakuhoshoukin_data;
			echo "ヶ月";
		}
//	}
}

// 151	金銭面	権利金※ 100以上の場合は単位は 円、それ以外はヶ月
function my_custom_kakakukenrikin_print($post_id) {
	$kakakukenrikin_data = get_post_meta($post_id,'kakakukenrikin',true);
//	if(is_numeric($kakakukenrikin_data)){
		if($kakakukenrikin_data >= 100) {
			echo floatval($kakakukenrikin_data)/10000;
			echo "万円";
		}else{
			echo $kakakukenrikin_data;
			echo "ヶ月";
		}
//	}
}

// 155	金銭面	償却・敷引金 1～99:"ヶ月"、101～200:100を引いて"%" 201以上の場合単位は"円"
function my_custom_kakakushikibiki_print($post_id) {
	$kakakushikibiki_data = get_post_meta($post_id,'kakakushikibiki',true);

//	if(is_numeric($kakakushikibiki_data)){
		if($kakakushikibiki_data < 100) {
			echo $kakakushikibiki_data;
			echo "ヶ月";
		}elseif($kakakushikibiki_data>100 && $kakakushikibiki_data<=200){
			echo floatval($kakakushikibiki_data)-100;
			echo "%";
		}elseif($kakakushikibiki_data>200){
			echo floatval($kakakushikibiki_data)/10000;
			echo "万円";
		}
//	}
}


/* 157	更新料・月数 ※ 100以上の場合は単位は"円"、それ以外は"ヶ月" */
function my_custom_kakakukoushin_print($post_id) {
	$kakakukoushin_data = get_post_meta($post_id,'kakakukoushin',true);
//	if(is_numeric($kakakukoushin_data)){
		if($kakakukoushin_data >= 100) {
			echo $kakakukoushin_data;
			echo "円";
		}else{
			echo $kakakukoushin_data;
			echo "ヶ月";
		}
//	}
}



//160 住宅保険料 9:不要 9以外:要
function my_custom_kakakuhoken_print($post_id) {
	$kakakuhoken_data = get_post_meta($post_id,'kakakuhoken',true);
	if($kakakuhoken_data == '9') echo '不要　';
	if(is_numeric($kakakuhoken_data) && $kakakuhoken_data != 9){
		echo $kakakuhoken_data . '円　';
	}
}

// 162	金銭面	借地料
// 163	金銭面	借地契約期間(年)
// 164	金銭面	借地契約期間(月)	
// 165	金銭面	借地契約期間(区分)
function my_custom_shakuchi_print($post_id) {

	$shakuchiryo_data = get_post_meta($post_id,'shakuchiryo',true);
	$shakuchikikan_data = get_post_meta($post_id,'shakuchikikan',true);
	$shakuchikubun_data = get_post_meta($post_id,'shakuchikubun',true);
	if($shakuchiryo_data != ''){
			echo " 借地料 ".floatval($shakuchiryo_data)/10000;
			echo '万円';
	}

	if(is_numeric($shakuchikubun_data)){
		if($shakuchikubun_data = '1') {
			echo " 借地期限 ".$shakuchikikan_data;
		}elseif($shakuchikubun_data='2'){
			echo " 借地期間 ".$shakuchikikan_data;
		}
	}
}

// 178	駐車場	駐車場料金
// 180	駐車場	駐車場区分 【改REINS】1:空有 2:空無 3:近隣 4:無
function my_custom_chushajo_print($post_id) {
	$chushajoryokin_data = get_post_meta($post_id,'chushajoryokin',true);
	if($chushajoryokin_data !="")
		echo $chushajoryokin_data.'円　';
	$chushajokubun_data = get_post_meta($post_id,'chushajokubun',true);
	if($chushajokubun_data=="1")	echo '空有';
	if($chushajokubun_data=="2")	echo '空無';
	if($chushajokubun_data=="3")	echo '近隣';
	if($chushajokubun_data=="4")	echo '無';
	//text
	if( $chushajokubun_data !='' && !is_numeric($chushajokubun_data) ) echo $chushajokubun_data;
	echo '　'.get_post_meta($post_id,'chushajobiko',true);
}

// 184	引渡/入居日
function my_custom_nyukyogenkyo_print($post_id) {

	$bukkenshubetsu_data = intval(get_post_meta($post_id,'bukkenshubetsu',true));
	$nyukyogenkyo_data = get_post_meta($post_id,'nyukyogenkyo',true);

	if ( $bukkenshubetsu_data <1200 ) {
		if($nyukyogenkyo_data=="1")	echo '更地';
		if($nyukyogenkyo_data=="2")	echo '古屋あり';
		if($nyukyogenkyo_data=="10")	echo '古屋あり(更地引渡可)';
	}else{

		if($nyukyogenkyo_data=="1")	echo '居住中';
		if($nyukyogenkyo_data=="2")	echo '空家';
		if($nyukyogenkyo_data=="3")	echo '賃貸中';
		if($nyukyogenkyo_data=="4")	echo '未完成';
	}
	//text
	if( $nyukyogenkyo_data !='' && !is_numeric($nyukyogenkyo_data) ) echo $nyukyogenkyo_data;
}

// 185	引渡/入居日	引渡/入居時期 1:即時 2:相談 3:期日指定
function my_custom_nyukyojiki_print($post_id) {
	$nyukyojiki_data = get_post_meta($post_id,'nyukyojiki',true);
	if($nyukyojiki_data=="1")	echo '即時 ';
	if($nyukyojiki_data=="2")	echo '相談 ';
	if($nyukyojiki_data=="3")	echo '期日指定 ';
	//text
	if( $nyukyojiki_data !='' && !is_numeric($nyukyojiki_data) ) echo $nyukyojiki_data.' ';

}

//187	引渡/入居日	引渡/入居旬 1:上旬　2:中旬　3:下旬
function my_custom_nyukyosyun_print($post_id) {
	$nyukyosyun_data = get_post_meta($post_id,'nyukyosyun',true);
	if($nyukyosyun_data=="1")	echo '上旬';
	if($nyukyosyun_data=="2")	echo '中旬';
	if($nyukyosyun_data=="3")	echo '下旬';
	//text
	if( $nyukyosyun_data !='' && !is_numeric($nyukyosyun_data) ) echo $nyukyosyun_data.'';
}

// 198	取引態様	取引態様 【改REINS】1:売主/貸主 2:代理 3:専属 4: 専任 5:一般 6:仲介 9:その他
function my_custom_torihikitaiyo_print($post_id) {
	$torihikitaiyo_data = get_post_meta($post_id,'torihikitaiyo',true);

	if( get_post_meta($post_id,'bukkenshubetsu',true) < 3000 ) {
		if($torihikitaiyo_data=="1")	echo '売主';
	}else{
		if($torihikitaiyo_data=="1")	echo '貸主';
	}

	if($torihikitaiyo_data=="2")	echo '代理';
	if($torihikitaiyo_data=="3")	echo '専属';
	if($torihikitaiyo_data=="4")	echo '専任';
	if($torihikitaiyo_data=="5")	echo '一般';
	if($torihikitaiyo_data=="6")	echo '仲介';
	if($torihikitaiyo_data=="9")	echo 'その他';
	//text
	if( $torihikitaiyo_data !='' && !is_numeric($torihikitaiyo_data) ) echo $torihikitaiyo_data.'';
}

// 34土地	地目
function my_custom_tochichimoku_print($post_id) {
	$tochichimoku_data = get_post_meta($post_id,'tochichimoku',true);
	if($tochichimoku_data=="1")	echo '宅地';
	if($tochichimoku_data=="2")	echo '田';
	if($tochichimoku_data=="3")	echo '畑';
	if($tochichimoku_data=="4")	echo '山林';
	if($tochichimoku_data=="5")	echo '雑種地';
	if($tochichimoku_data=="9")	echo 'その他';
	if($tochichimoku_data=="10")	echo '原野';
	if($tochichimoku_data=="11")	echo '田･畑';
	//text
	if( $tochichimoku_data !='' && !is_numeric($tochichimoku_data) ) echo $tochichimoku_data.'';
}

//35土地	用途地域
function my_custom_tochiyouto_print($post_id) {
	$tochiyouto_data = get_post_meta($post_id,'tochiyouto',true);
	if($tochiyouto_data=="1")	echo '第一種低層住居専';
	if($tochiyouto_data=="2")	echo '第二種中高層住居専用';
	if($tochiyouto_data=="3")	echo '第二種住居';
	if($tochiyouto_data=="4")	echo '近隣商業';
	if($tochiyouto_data=="5")	echo '商業';
	if($tochiyouto_data=="6")	echo '準工業';
	if($tochiyouto_data=="7")	echo '工業';
	if($tochiyouto_data=="8")	echo '工業専用';
	if($tochiyouto_data=="10")	echo '第二種低層住居専用';
	if($tochiyouto_data=="11")	echo '第一種中高層住居専用';
	if($tochiyouto_data=="12")	echo '第一種住居';
	if($tochiyouto_data=="13")	echo '準住居';
	if($tochiyouto_data=="99")	echo '無指定';
	//text
	if( $tochiyouto_data !='' && !is_numeric($tochiyouto_data) ) echo $tochiyouto_data.'';
}

//36土地	都市計画
function my_custom_tochikeikaku_print($post_id) {
	$tochikeikaku_data = get_post_meta($post_id,'tochikeikaku',true);
	if($tochikeikaku_data=="1")	echo '市街化区域';
	if($tochikeikaku_data=="2")	echo '市街化調整区域';
	if($tochikeikaku_data=="3")	echo '非線引き区域';
	if($tochikeikaku_data=="4")	echo '都市計画区域外';
	//text
	if( $tochikeikaku_data !='' && !is_numeric($tochikeikaku_data) ) echo $tochikeikaku_data.'';
}

//37土地	地勢
function my_custom_tochichisei_print($post_id) {
	$tochichisei_data = get_post_meta($post_id,'tochichisei',true);
	if($tochichisei_data=="1")	echo '平坦';
	if($tochichisei_data=="2")	echo '高台';
	if($tochichisei_data=="3")	echo '低地';
	if($tochichisei_data=="4")	echo 'ひな段';
	if($tochichisei_data=="5")	echo '傾斜地';
	if($tochichisei_data=="9")	echo 'その他';
	//text
	if( $tochichisei_data !='' && !is_numeric($tochichisei_data) ) echo $tochichisei_data.'';
}

//38土地	土地面積計測方式
function my_custom_tochisokutei_print($post_id) {
	$tochisokutei_data = get_post_meta($post_id,'tochisokutei',true);
	if($tochisokutei_data=="1")	echo '公簿';
	if($tochisokutei_data=="2")	echo '実測';
	//text
	if( $tochisokutei_data !='' && !is_numeric($tochisokutei_data) ) echo $tochisokutei_data.'';
}

//43土地	セットバック
function my_custom_tochisetback_print($post_id) {
	$tochisetback_data = get_post_meta($post_id,'tochisetback',true);
	if($tochisetback_data=="1")	echo '無';
	if($tochisetback_data=="2")	echo '有';
	//text
	if( $tochisetback_data !='' && !is_numeric($tochisetback_data) ) echo $tochisetback_data.'';
}

//47土地	接道状況	【REINS】1:一方 2:角地 3:三方 4:四方 5:二方(除角地)
//【改REINS】1:一方 2:角地 3:三方 4:四方 5:二方(除角地) 10:接道なし
function my_custom_tochisetsudo_print($post_id) {
	$tochisetsudo_data = get_post_meta($post_id,'tochisetsudo',true);
	if($tochisetsudo_data=="1")	echo '一方';
	if($tochisetsudo_data=="2")	echo '角地';
	if($tochisetsudo_data=="3")	echo '三方';
	if($tochisetsudo_data=="4")	echo '四方';
	if($tochisetsudo_data=="5")	echo '二方(除角地)';
	if($tochisetsudo_data=="10")	echo '接道なし';
	//text
	if( $tochisetsudo_data !='' && !is_numeric($tochisetsudo_data) ) echo $tochisetsudo_data.'';
}

//53土地	接道方向	1:北 2:北東 3:東 4:南東 5:南 6:南西 7:西 8:北西
function my_custom_tochisetsudohouko1_print($post_id) {
	$tochisetsudohouko1_data = get_post_meta($post_id,'tochisetsudohouko1',true);
	if($tochisetsudohouko1_data=="1")	echo '北';
	if($tochisetsudohouko1_data=="2")	echo '北東';
	if($tochisetsudohouko1_data=="3")	echo '東';
	if($tochisetsudohouko1_data=="4")	echo '南東';
	if($tochisetsudohouko1_data=="5")	echo '南';
	if($tochisetsudohouko1_data=="6")	echo '南西';
	if($tochisetsudohouko1_data=="7")	echo '西';
	if($tochisetsudohouko1_data=="8")	echo '北西';
	//text
	if( $tochisetsudohouko1_data !='' && !is_numeric($tochisetsudohouko1_data) ) echo $tochisetsudohouko1_data.'';
}

//55土地	接道種別2	1:公道 2:私道
function my_custom_tochisetsudoshurui1_print($post_id) {
	$tochisetsudoshurui1_data = get_post_meta($post_id,'tochisetsudoshurui1',true);
	if($tochisetsudoshurui1_data=="1")	echo '公道';
	if($tochisetsudoshurui1_data=="2")	echo '私道';
	//text
	if( $tochisetsudoshurui1_data !='' && !is_numeric($tochisetsudoshurui1_data) ) echo $tochisetsudoshurui1_data.'';
}

//57土地	位置指定道路	1:位置指定道路
function my_custom_tochisetsudoichishitei1_print($post_id) {
	$tochisetsudoichishitei1_data = get_post_meta($post_id,'tochisetsudoichishitei1',true);
	if($tochisetsudoichishitei1_data=="1")	echo '位置指定道路';
	//text
	if( $tochisetsudoichishitei1_data !='' && !is_numeric($tochisetsudoichishitei1_data) ) echo $tochisetsudoichishitei1_data .'';
}

//53土地	接道方向2	1:北 2:北東 3:東 4:南東 5:南 6:南西 7:西 8:北西
function my_custom_tochisetsudohouko2_print($post_id) {
	$tochisetsudohouko2_data = get_post_meta($post_id,'tochisetsudohouko2',true);
	if($tochisetsudohouko2_data=="1")	echo '北';
	if($tochisetsudohouko2_data=="2")	echo '北東';
	if($tochisetsudohouko2_data=="3")	echo '東';
	if($tochisetsudohouko2_data=="4")	echo '南東';
	if($tochisetsudohouko2_data=="5")	echo '南';
	if($tochisetsudohouko2_data=="6")	echo '南西';
	if($tochisetsudohouko2_data=="7")	echo '西';
	if($tochisetsudohouko2_data=="8")	echo '北西';
	//text
	if( $tochisetsudohouko2_data !='' && !is_numeric($tochisetsudohouko2_data) ) echo $tochisetsudohouko2_data.'';
}

//55土地	接道種別2	1:公道 2:私道
function my_custom_tochisetsudoshurui2_print($post_id) {
	$tochisetsudoshurui2_data = get_post_meta($post_id,'tochisetsudoshurui2',true);
	if($tochisetsudoshurui2_data=="1")	echo '公道';
	if($tochisetsudoshurui2_data=="2")	echo '私道';
	//text
	if( $tochisetsudoshurui2_data !='' && !is_numeric($tochisetsudoshurui2_data) ) echo $tochisetsudoshurui2_data.'';
}

//57土地	位置指定道路2	1:位置指定道路
function my_custom_tochisetsudoichishitei2_print($post_id) {
	$tochisetsudoichishitei2_data = get_post_meta($post_id,'tochisetsudoichishitei2',true);
	if($tochisetsudoichishitei2_data=="1")	echo '位置指定道路';
	//text
	if( $tochisetsudoichishitei2_data !='' && !is_numeric($tochisetsudoichishitei2_data) ) echo $tochisetsudoichishitei2_data .'';
}

//68土地	土地権利(借地権種類) 
function my_custom_tochikenri_print($post_id) {
	$tochikenri_data = get_post_meta($post_id,'tochikenri',true);
	if($tochikenri_data=="1")	echo '所有権';
	if($tochikenri_data=="2")	echo '旧法地上';
	if($tochikenri_data=="3")	echo '旧法賃借';
	if($tochikenri_data=="4")	echo '普通地上';
	if($tochikenri_data=="5")	echo '定期地上';
	if($tochikenri_data=="6")	echo '普通賃借';
	if($tochikenri_data=="7")	echo '定期賃借';
	if($tochikenri_data=="8")	echo '一時使用';
	if($tochikenri_data=="21")	echo '地上権';
	if($tochikenri_data=="22")	echo '定期借地';
	if($tochikenri_data=="23")	echo '賃借権';
	if($tochikenri_data=="99")	echo 'その他';
	//text
	if( $tochikenri_data !='' && !is_numeric($tochikenri_data) ) echo $tochikenri_data.'';
}

//69土地	国土法届出	【REINS】1:要 2: 届出中 3:不要
function my_custom_tochikokudohou_print($post_id) {
	$tochikokudohou_data = get_post_meta($post_id,'tochikokudohou',true);
	if($tochikokudohou_data=="1")	echo '要';
	if($tochikokudohou_data=="2")	echo '届出中';
	if($tochikokudohou_data=="3")	echo '不要';
	//text
	if( $tochikokudohou_data !='' && !is_numeric($tochikokudohou_data) ) echo $tochikokudohou_data.'';
}

//250	設備・条件
function my_custom_setsubi_print($post_id) {
	global $work_setsubi;

	$setsubi_data = get_post_meta($post_id, 'setsubi', true);
	foreach($work_setsubi as $meta_box){
		if( strpos($setsubi_data, $meta_box['code']) ){	echo "<li>".$meta_box['name']."</li>"; }
	}
	echo '<div class="clear">'.get_post_meta($post_id,'setsubisonota',true).'</div>';
}

// 225物件画像タイプ
function my_custom_fudoimgtype_print($imgtype) {

	switch ($imgtype) {
		case "1" :
			$imgtype = "(間取)"; break;
		case "2" :
			$imgtype = "(外観)"; break;
		case "3" :
			$imgtype = "(地図)"; break;
		case "4" :
			$imgtype = "(周辺)"; break;
		case "5" :
			$imgtype = "(内装)"; break;
		case "9" :
			$imgtype = ""; break;	//(その他画像)
		case "10" :
			$imgtype = "(玄関)"; break;
		case "11" :
			$imgtype = "(居間)"; break;
		case "12" :
			$imgtype = "(キッチン)"; break;
		case "13" :
			$imgtype = "(寝室)"; break;
		case "14" :
			$imgtype = "(子供部屋)"; break;
		case "15" :
			$imgtype = "(風呂)"; break;
	}

	return $imgtype;
}
//print_r($post);
//$meta = get_post_meta(get_the_ID());

//echo '<br>meta:';
//print_r($meta);

?>