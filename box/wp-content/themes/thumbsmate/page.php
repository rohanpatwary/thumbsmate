<?php 
/*
Template Name: トップページ
*/
get_header();?>
<section>
<div id="main_container">
<div id="main_section">
<article>
<div id="main_content">
<h1 class="page_title"><?php the_post();the_title();?></h1>
<div class="content_post">
<?php the_content();?>

<div class="clear"></div>
</div>



</div>
</article>
<?php get_sidebar();?>
<div class="clear"></div>
</div>
<?php get_template_part('right_side');?>
<div class="clear"></div>
</div>
</section>
<?php get_footer();?>