<?php 
get_header();?>
<section>
<div id="main_container">
<div id="main_section">
<article>
<div id="main_content">
<h1 class="page_title"><?php printf(__('「%s」の検索結果'), '' . get_search_query() . ''); ?>:<?php $my_query =& new WP_Query("s=$s & showposts=-1"); echo $my_query->post_count; ?> 件</h1>
<ul id="archive_list">
<?php if(have_posts()){
while(have_posts()){the_post();
?>
<li>
<dl><dt><a href="'.get_permalink().'" title="'.get_the_title().'"><?php if(has_post_thumbnail()){
the_post_thumbnail('thumbnail');

}else{
echo '<img src="'.get_bloginfo('template_url').'/img/noimage.png" width="150" height="150">';
} ?></a></dt>
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