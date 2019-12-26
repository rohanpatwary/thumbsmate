<?php 
get_header();?>
<section>
<div id="main_container">
<div id="main_section">
<article>
<div id="main_content">

<?php get_template_part('fudosingle');?>

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