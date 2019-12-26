<!DOCTYPE HTML>
<html <?php language_attributes(); ?> xmlns:fb="http://ogp.me/ns/fb#">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet" media="all" />
<title><?php
if(is_single()||is_page()){
the_title();echo ' - ';
}
if(is_category()||is_archive()){
if(is_category()){single_cat_title();}
elseif ( is_day() ){echo get_the_date();}
elseif ( is_month() ) {echo get_the_date('Y年F');}
elseif ( is_year() ){echo get_the_date('Y年');}
elseif(is_tag()){single_tag_title();}
elseif(is_post_type_archive()){post_type_archive_title();}
elseif(is_tax() ){single_term_title();}
elseif(is_author){the_author();echo 'の';}
else {echo ('新着');}?>記事一覧 - <?php
}
 bloginfo('name'); ?></title>
<link href='http://fonts.googleapis.com/css?family=Quicksand:700' rel='stylesheet' type='text/css'>
<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" />
<link rel="apple-touch-icon" href="<?php bloginfo('template_url'); ?>/screenshot.png" />
<?php
wp_deregister_script('jquery');
wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js', array(), '1.8.0');
wp_enqueue_script('jquery ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js', array(), '1.10.3');

wp_head(); ?>
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
</head>
<body <?php body_class(); ?>>
<header>
<div id="header">
<hgroup>
<h1><?php bloginfo('description');?></h1>
<h2><a href="<?php echo home_url();?>/"><img src="<?php bloginfo('template_url'); ?>/img/direct-logo.png" width="278" height="59" alt="<?php bloginfo('name');?>"></a></h2>
<h3>物件情報の詳細やおすすめポイントをご紹介！<br>
どこよりも詳しく丁寧な賃貸情報サイトです。</h3>
</hgroup>
<address>
<a href="<?php echo home_url();?>/?page_id=7"><img src="<?php bloginfo('template_url'); ?>/img/head_addres.png" width="295" height="77" alt="電話番号：072-252-2025">
</a></address>
</div>
</header>
<nav>
<div id="nav">
<ul>
<li><a href="<?php echo home_url();?>/">ホーム</a></li>
<li id="nav_osusume"><a href="<?php echo home_url();?>/?bukken=osusume&shu=2&ros=0&eki=0&ken=0&sik=0&kalc=0&kahc=0&kalb=0&kahb=0&hof=0&tik=0&mel=0&meh=0">おすすめ物件</a></li>
<li><a href="<?php echo home_url();?>/?page_id=23">物件検索</a></li>
<li><a class="thickbox" href="/box/wp-content/plugins/fudoukaiin/wp-login.php?action=register&KeepThis=true&TB_iframe=true&height=500&width=400">会員登録</a></li>
<li><a href="<?php echo home_url();?>/?page_id=4">会社情報</a></li>
<li><a href="<?php echo home_url();?>/?page_id=7">お問い合わせ</a></li>
</ul>
</div>
</nav>