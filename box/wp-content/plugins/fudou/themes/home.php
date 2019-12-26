<?php 
/*
Template Name: トップページ
*/
get_header();?>
<section>
<div id="cover_wrapper">
<div id="top_slide">
<div id="slide">
<div class="slide00">
<a class="slide_btn" href="<?php echo home_url();?>/?bukken=osusume&shu=2&ros=0&eki=0&ken=0&sik=0&kalc=0&kahc=0&kalb=0&kahb=0&hof=0&tik=0&mel=0&meh=0">おすすめ物件</a>
</div>
<div class="slide01">
<a class="slide_btn" href="<?php echo home_url();?>/?page_id=23">ピッタリのお部屋を探す</a>
<p class="none">サムズメイトなら<strong>あなたにぴったりなお部屋</strong>が見つかる</p>
</div>
<div class="slide02">
<a class="slide_btn" href="<?php echo home_url();?>/?bukken=jsearch&shu=0&set[]=10801">高齢者向け住宅一覧</a>
<p class="none"><strong>快適なシニアライフ</strong>を送るための住宅選びをお手伝い</p>
<a href="#"></a>
</div>
<div class="slide03">
<a class="slide_btn" href="<?php echo home_url();?>/#top_kodawari_list">カテゴリー一覧</a>
<p class="none">ライフスタイルに合わせた物件選びも、サムズメイトにお任せ！</p>
<a href="#"></a>
</div>
</div><div id="cover"></div><div id="cover_r"></div>
</div>

</div>
</section>
<section>
<div id="main_container">
<div id="main_section">
<article>
<div id="main_content">
<ul id="center_nav">
<li id="top_touroku" class="opa"><a href="<?php echo home_url();?>/?page_id=35">物件登録</a></li>
<li id="top_kodawari" class="opa"><a href="<?php echo home_url();?>/?page_id=23">　こだわり物件検索</a></li>
</ul>
<div class="top_bukken_box">
<h3>おすすめ物件</h3>
<ul><?php dynamic_sidebar(4);?></ul>
<div class="clear"></div>
</div>
<div class="top_bukken_box">
<h3>新着物件</h3>
<ul>
<?php dynamic_sidebar(3);?>
</ul>
<div class="clear"></div>
</div>
<div class="top_bukken_box">
<h3>こだわりで探す</h3>
<ul id="top_kodawari_list">
<li><a href="<?php echo home_url();?>/?bukken=jsearch&shu=0&set[]=10901">ペット可</a></li>
<li><a href="<?php echo home_url();?>/?bukken=jsearch&shu=0&set[]=10802">シニアライフ</a></li>
<li><a href="<?php echo home_url();?>/?bukken=jsearch&shu=0&set[]=10801">シニア限定</a></li>
<li><a href="<?php echo home_url();?>/?bukken=jsearch&shu=0&set[]=10702">学生</a></li>
<li><a href="<?php echo home_url();?>/?bukken=jsearch&amp;shu=0&amp;set[]=10402">女性限定</a></li>
<li><a href="<?php echo home_url();?>/?bukken=jsearch&amp;shu=0&amp;set[]=23901">二世帯住宅</a></li>
<li><a href="<?php echo home_url();?>/?bukken=jsearch&amp;shu=0&amp;set[]=33002">ファミリー向け</a></li>
<li><a href="<?php echo home_url();?>/?bukken=jsearch&amp;shu=0&amp;set[]=33001">敷金礼金0</a></li>
</ul>
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