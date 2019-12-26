<?php 
get_header();?>
<section>
<div id="main_container">
<div id="main_section">
<article>
<div id="main_content">
<h1 class="page_title"><?php the_post();the_title();?></h1>
<ul id="sitemap">
<li><a class="sitemap_h" href="<?php echo home_url(); ?>"><?php bloginfo("name"); ?></a>
<ul>
<?php wp_list_pages('title_li'); ?>
<li><a href="<?php echo home_url(); ?>/infos">お知らせ</a>（各カテゴリ新着10件）
<ul>
<?php $map = new Wp_query('post_type=infos&posts_per_page=10');while($map -> have_posts()):$map ->the_post();?>
<li><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
<?php endwhile;wp_reset_postdata();?>
</ul>
</li>
<li><?php sitemap_q(true,false,false); ?></li>
</ul>
</li>
</ul>




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