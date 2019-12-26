<?php 
get_header();?>
<section>
<div id="main_container">
<div id="main_section">
<article>
<div id="main_content">
<h1 class="page_title"><?php the_post();the_title();?></h1>
<time datetime="<?php the_time("Y-m-d"); ?>" pubdate="pubdate"><?php the_time("Y.m.d"); ?></time>
<div class="content_post">
<?php the_content();?>
<div class="clear"></div>
</div>
<?php
if(get_post_type()=='areablog'){
echo '<ul class="post-categories">';
$terms = get_the_terms( $post->ID, 'areablog_type' );
	if($terms){
	foreach ( $terms as $term ) {
		$draught_links = $term->name;
		$draught_link = $term->slug;
		echo '<li><a href="'.home_url().'/?areablog_type='.$draught_link.'">'.$draught_links.'</a></li>';}
		echo '</ul>';
	}
}elseif(get_post_type()=='post'){
the_category();
}?>
<div id="page_link">
<span class="previous">
<?php previous_post_link( '%link', '&lt; %title'); ?>
</span>
<span class="next">
<?php next_post_link( '%link', '%title &gt;'); ?>
</span>
</div>
<?php if(get_post_type()=='areablog'){?>
<p class="areablog_top_link"><a href="<?php echo home_url();?>?page_id=74">物件周辺情報ブログトップ</a></p>
<?php }elseif(get_post_type()=='blogs'){?>
<p class="areablog_top_link"><a href="<?php echo home_url();?>?page_id=80">ダイレクト賃貸ブログトップ</a></p>
<?php }?>
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