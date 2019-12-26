<aside>
<div id="aside">
<?php if(!is_page(231)){?><p>
<a href="<?php echo home_url();?>?page_id=231"><img src="<?php bloginfo('template_url'); ?>/img/intohome.png" width="210" height="79" alt="入居者の方へ" /></a>
</p><?php }?>
<div id="side_bukken">
<ul>
<?php dynamic_sidebar(5);?>
</ul>
<div id="side_info">
<h3 class="side_h"><a href="<?php echo home_url();?>?cat=1">お知らせ</a></h3>
<dl>
<?php
wp_reset_query();
$s_post = get_posts(array('numberposts'=>5,'post_type'=>'post'));
foreach($s_post as $s){
//print_r($s);
?>
<dt><?php echo date('Y年m月d日',strtotime($s->post_date));
//print_r($s); ?></dt>
<dd><a href="<?php echo home_url().'/?p='.$s->ID;?>" title="<?php the_title(); ?>"><?php echo $s->post_title; ?></a></dd>
<?php };?>
</dl>
<p class="txr small"><a href="<?php echo home_url();?>/?cat=1">お知らせ一覧</a></p>
</div>
</div>
</div>
</aside>