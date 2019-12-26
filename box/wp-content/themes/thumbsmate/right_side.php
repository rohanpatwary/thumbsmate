<section>
<div id="right_side">
<?php if(!is_user_logged_in()){?>
<div id="side_match">
<p class="ms">条件にぴったりの<br>物件情報をお届け</p>
<h3>メールマッチング</h3>
<div class="exc">ご登録のメールアドレスに、条件にあった物件情報を配信。チャンスを見逃しません！まずは無料会員登録を</div>
<p class="join_btn"><a class="thickbox opa" href="/box/wp-content/plugins/fudoukaiin/wp-login.php?action=register&KeepThis=true&TB_iframe=true&height=500&width=400">無料会員登録</a></p>
</div><?php }else{?>
<div id="meruma">
<a class="thickbox" href="/box/wp-content/plugins/fudoumail/fudou_user.php?KeepThis=true&TB_iframe=true&height=500&width=680"><img src="<?php bloginfo('template_url'); ?>/img/mailsetting.jpg" width="210" height="223" alt="あなたにぴったりの物件情報をいち早くお届け！" /></a>
</div>
<?php }?>
<div id="side_login">
<h3 class="side_h"><img src="<?php bloginfo('template_url'); ?>/img/member.png" width="15" height="19" alt="" class="mid"> 会員ログイン <img src="<?php bloginfo('template_url'); ?>/img/star_nav.png" width="24" height="21" alt="" class="mid"></h3>
<ul class="wp_mem">
<?php dynamic_sidebar(6);?>
</ul>
</div>
<?php if(!is_user_logged_in()){?>
<div id="side_maruhi">
<h3 class="side_h"><img src="<?php bloginfo('template_url'); ?>/img/maruhi.png" width="27" height="27" alt="㊙" class="mid"> 非公開情報物件</h3>
<p>家主様などの意向によりホームページでの一般公開をされていない物件が見れます。人気すぎる物件、掘り出し物や訳あり格安物件など。無料会員登録をすると見ることができます。</p>
<p class="join_btn"><a class="opa thickbox" href="/box/wp-content/plugins/fudoukaiin/wp-login.php?action=register&KeepThis=true&TB_iframe=true&height=500&width=400">無料会員登録</a></p>
</div>
<?php }?>
<p><a href="<?php echo home_url();?>/?page_id=234"><img src="<?php bloginfo('template_url'); ?>/img/owners.png" width="210" height="79" alt="物件を掲載されたい方へ" /></a></p>
<div id="side_contact">
<a href="<?php echo home_url();?>/?page_id=7"></a>
<p class="none">内見・相談・来店予約はお問い合わせフォームへ。<br>
電話番号：072-252-2025　受付時間：9：00～19：00　定休日：日、祝</p>
</div>
<div class="banners">
<p><a href="<?php echo home_url();?>?page_id=74"><img src="<?php bloginfo('template_url'); ?>/img/blog01.png" width="210" height="79" alt="物件周辺情報ブログ"></a></p>
<p><a href="<?php echo home_url();?>/?page_id=80"><img src="<?php bloginfo('template_url'); ?>/img/thumbsmateblog.png" width="210" height="79" alt="ダイレクト賃貸ブログ"></a></p>
</div>
<div id="side_map">
<h3 class="side_h"><img src="<?php bloginfo('template_url'); ?>/img/car.png" width="26" height="13" alt=""> アクセス地図</h3>
<p>〒591-8032<br>
堺市北区百舌鳥梅町1丁目30-1</p>
<iframe width="210" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.co.jp/maps?f=q&amp;source=s_q&amp;hl=ja&amp;geocode=&amp;q=%E5%A0%BA%E5%B8%82%E5%8C%97%E5%8C%BA%E7%99%BE%E8%88%8C%E9%B3%A5%E6%A2%85%E7%94%BA1%E4%B8%81%E7%9B%AE30-1&amp;aq=&amp;sll=34.551352,135.496917&amp;sspn=0.001977,0.004128&amp;brcurrent=3,0x6000dbbb14d4a405:0xdfc443350892596f,0&amp;ie=UTF8&amp;hq=&amp;hnear=%E5%A4%A7%E9%98%AA%E5%BA%9C%E5%A0%BA%E5%B8%82%E5%8C%97%E5%8C%BA%E7%99%BE%E8%88%8C%E9%B3%A5%E6%A2%85%E7%94%BA%EF%BC%91%E4%B8%81%EF%BC%93%EF%BC%90%E2%88%92%EF%BC%91&amp;t=m&amp;ll=34.551317,135.496874&amp;spn=0.017673,0.017939&amp;z=14&amp;iwloc=A&amp;output=embed&iwloc=B"></iframe><br /><small><a href="https://www.google.co.jp/maps?f=q&amp;source=embed&amp;hl=ja&amp;geocode=&amp;q=%E5%A0%BA%E5%B8%82%E5%8C%97%E5%8C%BA%E7%99%BE%E8%88%8C%E9%B3%A5%E6%A2%85%E7%94%BA1%E4%B8%81%E7%9B%AE30-1&amp;aq=&amp;sll=34.551352,135.496917&amp;sspn=0.001977,0.004128&amp;brcurrent=3,0x6000dbbb14d4a405:0xdfc443350892596f,0&amp;ie=UTF8&amp;hq=&amp;hnear=%E5%A4%A7%E9%98%AA%E5%BA%9C%E5%A0%BA%E5%B8%82%E5%8C%97%E5%8C%BA%E7%99%BE%E8%88%8C%E9%B3%A5%E6%A2%85%E7%94%BA%EF%BC%91%E4%B8%81%EF%BC%93%EF%BC%90%E2%88%92%EF%BC%91&amp;t=m&amp;ll=34.551317,135.496874&amp;spn=0.017673,0.017939&amp;z=14&amp;iwloc=A" style="color:#0000FF;text-align:left">大きな地図で見る</a></small>
</div>
</div>
</section>