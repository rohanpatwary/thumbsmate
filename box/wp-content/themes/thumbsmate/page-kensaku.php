<?php 
/*
Template Name: 物件検索
*/
get_header();?>
<section>
<div id="main_container">
<div id="main_section">
<div id="bukken_content">
<h1 class="page_title">物件検索</h1>
<ul>
<?php dynamic_sidebar(5);?>
</ul>
<div class="clear"></div>
</div>
</div>
<?php get_template_part('right_side');?>
<div class="clear"></div>
</div>
</section>
<?php get_footer();?>