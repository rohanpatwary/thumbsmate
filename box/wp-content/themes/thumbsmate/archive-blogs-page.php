<?php 
/*
Template Name: サムズメイトブログ
*/
get_header();?>
<section>
<div id="main_container">
<div id="main_section">
<article>
<div id="main_content">
<h1><img src="<?php bloginfo('template_url'); ?>/img/blogs_title.png" width="560" height="149" alt="<?php the_post();the_title();?>" /></h1>
<ul id="archive_list">
<?php $paged = get_query_var('paged'); query_posts('post_type=blogs&paged='.$paged);
if(have_posts()){
while(have_posts()){the_post();
?>
<li>
<dl><dt><?php if(has_post_thumbnail()){
echo '<a href="'.get_permalink().'" title="'.get_the_title().'">';
the_post_thumbnail('thumbnail');
echo '</a>';
} ?></dt>
<dd>
<h3><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
<div class="excerpt"><?php the_excerpt();?></div>
<p class="txr"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">&gt; 続きを読む</a></p>
</dd></dl>
</li>
<?php }}else{?>
<li class="txc bold">Coming soon!!</li>
<?php }?>
</ul>
<div class="pagelink"><?php wp_pagenavi(); ?></div>
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