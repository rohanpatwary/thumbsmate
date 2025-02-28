<?php
/*
 * 不動産プラグインウィジェット
 * @package WordPress3.6
 * @subpackage Fudousan Plugin
 * Version: 1.3.5
*/


// 物件条件検索
add_action('widgets_init', 'fudo_widgetInit_b_k');
function fudo_widgetInit_b_k() {
	register_widget('fudo_widget_b_k');
}


// 更新情報(Twitter) Ver1.1
add_action('widgets_init', 'fudo_widgetInit_twitter2');
function fudo_widgetInit_twitter2() {
	register_widget('fudo_widget_twitter2');
}



// 物件条件検索
class fudo_widget_b_k extends WP_Widget {


	/** option */
	function fudo_widget_b_k_option($name,$value) {

		$stored_value = get_option($name);

		if ($stored_value === false) {
			add_option($name, $value);
		} else {
			update_option($name, $value);
		}
	}

	/** constructor */
	function fudo_widget_b_k() {
		$widget_ops = array( 'description' => '物件条件検索フォームを設置します。(１箇所のみ)' );
		parent::WP_Widget( false , $name = '物件条件検索' , $widget_ops );
	}

	/** @see WP_Widget::form */	
	function form($instance) {
		$title  = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$view1  = isset($instance['view1']) ? esc_attr($instance['view1']) : '';
		$r_view = isset($instance['r_view']) ? esc_attr($instance['r_view']) : '';
		$c_view = isset($instance['c_view']) ? esc_attr($instance['c_view']) : '';


		$this->fudo_widget_b_k_option('widget_fudo_b_k_title',$title);
		$this->fudo_widget_b_k_option('widget_fudo_b_k_view1',$view1);
		$this->fudo_widget_b_k_option('widget_fudo_b_k_r_view',$r_view);
		$this->fudo_widget_b_k_option('widget_fudo_b_k_c_view',$c_view);


?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">
		<?php _e('title'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>


		<p><label for="<?php echo $this->get_field_id('view1'); ?>">
		表示種類 <select class="widefat" id="<?php echo $this->get_field_id('view1'); ?>" name="<?php echo $this->get_field_name('view1'); ?>">
			<option value="0"<?php if($view1 == 0){echo ' selected="selected"';} ?>>売買・賃貸両方</option>
			<option value="1"<?php if($view1 == 1){echo ' selected="selected"';} ?>>売買のみ</option>
			<option value="2"<?php if($view1 == 2){echo ' selected="selected"';} ?>>賃貸のみ</option>
		</select></label></p>



		<p><label for="<?php echo $this->get_field_id('r_view'); ?>">
		路線表示 <select class="widefat" id="<?php echo $this->get_field_id('r_view'); ?>" name="<?php echo $this->get_field_name('r_view'); ?>">
			<option value="0"<?php if($r_view == 0){echo ' selected="selected"';} ?>>表示する</option>
			<option value="1"<?php if($r_view == 1){echo ' selected="selected"';} ?>>表示しない</option>
		</select></label></p>

<!--
		//駅歩分
		//間取り
		//築年数
		//面積
-->

		<p><label for="<?php echo $this->get_field_id('c_view'); ?>">
		地域表示 <select class="widefat" id="<?php echo $this->get_field_id('c_view'); ?>" name="<?php echo $this->get_field_name('c_view'); ?>">
			<option value="0"<?php if($c_view == 0){echo ' selected="selected"';} ?>>表示する</option>
			<option value="1"<?php if($c_view == 1){echo ' selected="selected"';} ?>>表示しない</option>
		</select></label></p>


		<p>設備表示設定<label for="set">
		<a href="tools.php?page=fudou/admin_fudou.php#set" target="_blank">設備表示設定</a></label></p>

<?php 

	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $wpdb;
		global $work_bukkenshubetsu;
		global $work_setsubi;
		global $work_madori;


		$site = home_url( '/' ); 

		// outputs the content of the widget

		echo "\n";
		echo '<style type="text/css">';
		echo '	<!--';
		echo '	div.kakaku_b { display:none; }';
		echo '	div.kakaku_c { display:none; }';
		echo '	// -->';
		echo '	</style>';
		echo "\n";


	        extract( $args );
	        $title = apply_filters('widget_title', $instance['title']);
		$view1 = $instance['view1'];
		$r_view = $instance['r_view'];
		$c_view = $instance['c_view'];


		echo $before_widget;

		if ( $title ){
			echo $before_title . $title . $after_title; 
		}else{
			echo $before_title . '物件検索' . $after_title; 
		}

		//種別
		$shu_id = isset($_GET['shu']) ? $_GET['shu'] : '';
		$shu_data = '';
		if($shu_id == '1') $shu_data = '< 3000' ;
		if($shu_id == '2') $shu_data = '> 3000' ;
		if(intval($shu_id) > 3) $shu_data = '= ' .$shu_id ;


		$ros_id = isset($_GET['ros']) ? $_GET['ros'] : ''; 		//路線
		$eki_id = isset($_GET['eki']) ? $_GET['eki'] : ''; 		//駅

		$ken_id = isset($_GET['ken']) ? $_GET['ken'] : ''; 		//県
		$sik_id = isset($_GET['sik']) ? $_GET['sik'] : ''; 		//市区

		$set_id = isset($_GET['set']) ? $_GET['set'] : ''; 		//設備
		$madori_id = isset($_GET['mad']) ? $_GET['mad'] : ''; 		//間取り

		$kalb_data = isset($_GET['kalb']) ? $_GET['kalb'] : ''; 	//価格下限
		$kahb_data = isset($_GET['kahb']) ? $_GET['kahb'] : ''; 	//価格上限

		$kalc_data = isset($_GET['kalc']) ? $_GET['kalc'] : ''; 	//価格下限
		$kahc_data = isset($_GET['kahc']) ? $_GET['kahc'] : ''; 	//価格上限

		$tik_data = isset($_GET['tik']) ? $_GET['tik'] : '';  		//築年数

		$hof_data = isset($_GET['hof']) ? $_GET['hof'] : '';  		//歩分
		$mel_data = isset($_GET['mel']) ? $_GET['mel'] : '';  		//面積下限
		$meh_data = isset($_GET['meh']) ? $_GET['meh'] : '';  		//面積上限



		//価格切り替え
		if($shu_id == 1 || ($shu_id > 999 && $shu_id < 3000) ) {
			echo '<style type="text/css">';
			echo '	<!--';
			echo '	div.kakaku_b { display:block; }';
			echo '	div.kakaku_c { display:none; }';
			echo '	// -->';
			echo '	</style>';
		}
		if($shu_id == 2 || $shu_id > 3000 ) {
			echo '<style type="text/css">';
			echo '	<!--';
			echo '	div.kakaku_b { display:none; }';
			echo '	div.kakaku_c { display:block; }';
			echo '	// -->';
			echo '	</style>';
		}



		//カテゴリ
		$bukken = isset($_GET['bukken']) ? $_GET['bukken'] : '';  		//カテゴリ

		if( $bukken == 'rosen' || $bukken == 'station'){
			$ros_id = isset($_GET['mid']) ? $_GET['mid'] : ''; 		//路線
			$eki_id = isset($_GET['nor']) ? $_GET['nor'] : ''; 		//駅
		}

		if( $bukken == 'ken' || $bukken  == 'shiku'){
			$ken_id = isset($_GET['mid']) ? $_GET['mid'] : ''; 		//県
			$sik_id = isset($_GET['nor']) ? $_GET['nor'] : ''; 		//市区
		}



		//非同期 遅延読み込み
		echo "\n";
		echo '<script type="text/javascript">';
			echo "\n";
			$madori_ar_txt = 'var madori_ar = new Array("0"';
			if(is_array($madori_id)) {
				foreach($madori_id as $meta_box4){
					$madori_ar_txt .= ',"'.$meta_box4.'"';
				}
			}
			$madori_ar_txt .= ');';
			echo $madori_ar_txt;
			echo "\n";
			$set_ar_txt = 'var set_ar = new Array("0"';
			if(is_array($set_id)) {
				foreach($set_id as $meta_box5){
					$set_ar_txt .= ',"'.$meta_box5.'"';
				}
			}
			$set_ar_txt .= ');';
			echo $set_ar_txt;
			echo "\n";
			if($shu_id != ''){
			?>
				addOnload_jyoken(function() { setTimeout(SShu2,500); });
				function addOnload_jyoken(func){
					try {
						window.addEventListener("load", func, false);
					} catch (e) {   
						window.attachEvent("onload", func);   	// IE用
					}
				}
			<?php
			}


		echo '</script>';
		echo "\n";




		echo '<form method="get" id="searchitem" name="searchitem" action="'.$site.'" >';
		echo '<input type="hidden" name="bukken" value="jsearch" >';


		echo "\n";

		echo '<span class="jsearch_caution1">ご希望の種別を選択して下さい</span><br />';

		//種別選択
		echo '<div id="shubetsu" class="shubetsu">';
		echo '<select name="shu" id="shu" onchange="SShu(this)">';
		echo '<option value="0">種別選択</option>';

		if($view1 < 2 ){

			$sql  =  " SELECT DISTINCT PM.meta_value AS bukkenshubetsu";
			$sql .=  " FROM $wpdb->posts as P ";
			$sql .=  " INNER JOIN $wpdb->postmeta as PM ON P.ID = PM.post_id ";
			$sql .=  " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo' ";
			$sql .=  " AND PM.meta_key='bukkenshubetsu' ";
			$sql .=  " AND CAST( PM.meta_value AS SIGNED ) < 3000 ";
			$sql .=  " ORDER BY PM.meta_value";

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );

			if(!empty($metas)) {

				echo '<option value="1"';
				if($shu_id == '1' )
					echo ' selected="selected"';
				echo '>売買　全て</option>';

				foreach ( $metas as $meta ) {
					$bukkenshubetsu_id = $meta['bukkenshubetsu'];

					foreach($work_bukkenshubetsu as $meta_box){
						if( $bukkenshubetsu_id ==  $meta_box['id'] ){
							echo '<option value="'.$meta_box['id'].'"';
							if($shu_id == $meta_box['id'] )
								echo ' selected="selected"';
							echo '>'.$meta_box['name'].'</option>';
						}
					}
				}
			}
		}

		if($view1 != 1 ){

			$sql  =  " SELECT DISTINCT PM.meta_value AS bukkenshubetsu";
			$sql .=  " FROM $wpdb->posts as P ";
			$sql .=  " INNER JOIN $wpdb->postmeta as PM ON P.ID = PM.post_id ";
			$sql .=  " WHERE P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo' ";
			$sql .=  " AND PM.meta_key='bukkenshubetsu' ";
			$sql .=  " AND CAST( PM.meta_value AS SIGNED ) > 3000 ";
			$sql .=  " ORDER BY PM.meta_value";

			$sql = $wpdb->prepare($sql,'');
			$metas = $wpdb->get_results( $sql,  ARRAY_A );

			if(!empty($metas)) {
				echo '<option value="2"';
				if($shu_id == '2' )
					echo ' selected="selected"';
				echo '>賃貸　全て</option>';

				foreach ( $metas as $meta ) {
					$bukkenshubetsu_id = $meta['bukkenshubetsu'];

					foreach($work_bukkenshubetsu as $meta_box){
						if( $bukkenshubetsu_id ==  $meta_box['id'] ){
							echo '<option value="'.$meta_box['id'].'"';
							if($shu_id == $meta_box['id'] )
								echo ' selected="selected"';
							echo '>'.$meta_box['name'].'</option>';
						}
					}
				}
			}
		}

		echo '</select>';
		echo '</div>';


		echo '<span class="jsearch_caution2">以下ご希望の条件を選択して物件検索ボタンを押して下さい</span><br />';


		




		//県選択
		if($c_view != 1){

			echo '<div id="chiiki" class="chiiki">';
			echo '<span class="jsearch_chiiki">市区選択</span><br />';
			echo '<select name="ken" id="ken" onchange="SSik(this)">';
			echo '<option value="0">県選択</option>';

			if( $shu_data !=''){
				$sql  =  "SELECT DISTINCT MA.middle_area_name, PM.meta_value AS middle_area_id";
				$sql .=  " FROM (($wpdb->posts as P";
				$sql .=  " INNER JOIN $wpdb->postmeta AS PM   ON P.ID = PM.post_id) ";
				$sql .=  " INNER JOIN $wpdb->postmeta AS PM_1 ON P.ID = PM_1.post_id) ";
				$sql .=  " INNER JOIN ".$wpdb->prefix."area_middle_area as MA ON CAST( PM.meta_value AS SIGNED ) = MA.middle_area_id";
				$sql .=  " WHERE PM.meta_key='shozaichiken' ";
				$sql .=  " AND P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo' ";
				$sql .=  " AND PM_1.meta_key='bukkenshubetsu'";
				$sql .=  " AND CAST( PM_1.meta_value AS SIGNED ) ".$shu_data."";
				$sql .=  " ORDER BY CAST( PM.meta_value AS SIGNED )";

				$sql = $wpdb->prepare($sql,'');
				$metas = $wpdb->get_results( $sql,  ARRAY_A );
				if(!empty($metas)) {

					foreach ( $metas as $meta ) {
						echo '<option value="'.$meta['middle_area_id'].'"';
						if($ken_id == $meta['middle_area_id'] )
							echo ' selected="selected"';
						echo '>'.$meta['middle_area_name'].'</option>';
					}
				}
			}
			echo '</select><br />';

			//echo $sql;


			//市区選択
			echo '<select name="sik" id="sik">';
			echo '<option value="0">市区選択</option>';

			if( $shu_data !='' && $ken_id !='' ){

				$sql  =  "SELECT DISTINCT NA.narrow_area_name, CAST( RIGHT(LEFT(PM.meta_value,5),3) AS SIGNED ) as narrow_area_id";
				$sql .=  " FROM (($wpdb->posts as P";
				$sql .=  " INNER JOIN $wpdb->postmeta as PM   ON P.ID = PM.post_id) ";
				$sql .=  " INNER JOIN $wpdb->postmeta AS PM_1 ON P.ID = PM_1.post_id) ";
				$sql .=  " INNER JOIN ".$wpdb->prefix."area_narrow_area as NA ON CAST( RIGHT(LEFT(PM.meta_value,5),3) AS SIGNED ) = NA.narrow_area_id";
				$sql .=  " WHERE PM.meta_key='shozaichicode' ";
				$sql .=  " AND P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo' ";
				$sql .=  " AND PM_1.meta_key='bukkenshubetsu'";
				$sql .=  " AND CAST( PM_1.meta_value AS SIGNED ) ".$shu_data."";
				$sql .=  " AND CAST( LEFT(PM.meta_value,2) AS SIGNED ) = ". $ken_id;
				$sql .=  " AND NA.middle_area_id = ". $ken_id;
			//	$sql .=  " GROUP BY NA.narrow_area_name, PM.meta_value";
				$sql .=  " ORDER BY CAST( PM.meta_value AS SIGNED )";
				$sql = $wpdb->prepare($sql,'');
				$metas = $wpdb->get_results( $sql,  ARRAY_A );
				if(!empty($metas)) {
					foreach ( $metas as $meta ) {
						echo '<option value="'.$meta['narrow_area_id'].'"';
						if($sik_id == $meta['narrow_area_id'] )
							echo ' selected="selected"';
						echo '>'.$meta['narrow_area_name'].'</option>';
					}
				}
			}

			echo '</select>';
			echo '</div>';
		}


		//価格選択

		echo '<div id="kakaku_c" class="kakaku_c">';
		echo '<span class="jsearch_kakaku">賃料</span><br />';
		echo '<select name="kalc" id="kalc">';
		echo '<option value="0">下限なし</option>';
		echo '<option value="3"'; 			if ($kalc_data == '3') echo ' selected="selected"';			echo '>3万円</option>';
		echo '<option value="4"';			if ($kalc_data == '4') echo ' selected="selected"';			echo '>4万円</option>';
		echo '<option value="5"';			if ($kalc_data == '5') echo ' selected="selected"';			echo '>5万円</option>';
		echo '<option value="6"';			if ($kalc_data == '6') echo ' selected="selected"';			echo '>6万円</option>';
		echo '<option value="7"';			if ($kalc_data == '7') echo ' selected="selected"';			echo '>7万円</option>';
		echo '<option value="8"';			if ($kalc_data == '8') echo ' selected="selected"';			echo '>8万円</option>';
		echo '<option value="9"';			if ($kalc_data == '9') echo ' selected="selected"';			echo '>9万円</option>';
		echo '<option value="10"';			if ($kalc_data == '10') echo ' selected="selected"';			echo '>10万円</option>';
		echo '<option value="11"';			if ($kalc_data == '11') echo ' selected="selected"';			echo '>11万円</option>';
		echo '<option value="12"';			if ($kalc_data == '12') echo ' selected="selected"';			echo '>12万円</option>';
		echo '<option value="13"';			if ($kalc_data == '13') echo ' selected="selected"';			echo '>13万円</option>';
		echo '<option value="14"';			if ($kalc_data == '14') echo ' selected="selected"';			echo '>14万円</option>';
		echo '<option value="15"';			if ($kalc_data == '15') echo ' selected="selected"';			echo '>15万円</option>';
		echo '<option value="16"';			if ($kalc_data == '16') echo ' selected="selected"';			echo '>16万円</option>';
		echo '<option value="17"';			if ($kalc_data == '17') echo ' selected="selected"';			echo '>17万円</option>';
		echo '<option value="18"';			if ($kalc_data == '18') echo ' selected="selected"';			echo '>18万円</option>';
		echo '<option value="19"';			if ($kalc_data == '19') echo ' selected="selected"';			echo '>19万円</option>';
		echo '<option value="20"';			if ($kalc_data == '20') echo ' selected="selected"';			echo '>20万円</option>';
		echo '<option value="30"';			if ($kalc_data == '30') echo ' selected="selected"';			echo '>30万円</option>';
		echo '<option value="50"';			if ($kalc_data == '50') echo ' selected="selected"';			echo '>50万円</option>';
		echo '<option value="100"';			if ($kalc_data == '100') echo ' selected="selected"';			echo '>100万円</option>';
		echo '</select>～';

		echo '<select name="kahc" id="kahc">';
		echo '<option value="3"';			if ($kahc_data == '3') echo ' selected="selected"';			echo '>3万円</option>';
		echo '<option value="4"';			if ($kahc_data == '4') echo ' selected="selected"';			echo '>4万円</option>';
		echo '<option value="5"';			if ($kahc_data == '5') echo ' selected="selected"';			echo '>5万円</option>';
		echo '<option value="6"';			if ($kahc_data == '6') echo ' selected="selected"';			echo '>6万円</option>';
		echo '<option value="7"';			if ($kahc_data == '7') echo ' selected="selected"';			echo '>7万円</option>';
		echo '<option value="8"';			if ($kahc_data == '8') echo ' selected="selected"';			echo '>8万円</option>';
		echo '<option value="9"';			if ($kahc_data == '9') echo ' selected="selected"';			echo '>9万円</option>';
		echo '<option value="10"';			if ($kahc_data == '10') echo ' selected="selected"';			echo '>10万円</option>';
		echo '<option value="11"';			if ($kahc_data == '11') echo ' selected="selected"';			echo '>11万円</option>';
		echo '<option value="12"';			if ($kahc_data == '12') echo ' selected="selected"';			echo '>12万円</option>';
		echo '<option value="13"';			if ($kahc_data == '13') echo ' selected="selected"';			echo '>13万円</option>';
		echo '<option value="14"';			if ($kahc_data == '14') echo ' selected="selected"';			echo '>14万円</option>';
		echo '<option value="15"';			if ($kahc_data == '15') echo ' selected="selected"';			echo '>15万円</option>';
		echo '<option value="16"';			if ($kahc_data == '16') echo ' selected="selected"';			echo '>16万円</option>';
		echo '<option value="17"';			if ($kahc_data == '17') echo ' selected="selected"';			echo '>17万円</option>';
		echo '<option value="18"';			if ($kahc_data == '18') echo ' selected="selected"';			echo '>18万円</option>';
		echo '<option value="19"';			if ($kahc_data == '19') echo ' selected="selected"';			echo '>19万円</option>';
		echo '<option value="20"';			if ($kahc_data == '20') echo ' selected="selected"';			echo '>20万円</option>';
		echo '<option value="30"';			if ($kahc_data == '30') echo ' selected="selected"';			echo '>30万円</option>';
		echo '<option value="50"';			if ($kahc_data == '50') echo ' selected="selected"';			echo '>50万円</option>';
		echo '<option value="100"';			if ($kahc_data == '100') echo ' selected="selected"';			echo '>100万円</option>';
		echo '<option value="0"';			if ($kahc_data == '0' ||$kahc_data == '' ) echo ' selected="selected"';			echo '>上限なし</option>';
		echo '</select>';
		echo '</div>';



		echo '<div id="kakaku_b" class="kakaku_b">';
		echo '<span class="jsearch_kakaku">価格</span><br />';
		echo '<select name="kalb" id="kalb">';
		echo '<option value="0">下限なし</option>';
		echo '<option value="300"'; 			if ($kalb_data == '300') echo ' selected="selected"';			echo '>300万円</option>';
		echo '<option value="400"';			if ($kalb_data == '400') echo ' selected="selected"';			echo '>400万円</option>';
		echo '<option value="500"';			if ($kalb_data == '500') echo ' selected="selected"';			echo '>500万円</option>';
		echo '<option value="600"';			if ($kalb_data == '600') echo ' selected="selected"';			echo '>600万円</option>';
		echo '<option value="700"';			if ($kalb_data == '700') echo ' selected="selected"';			echo '>700万円</option>';
		echo '<option value="800"';			if ($kalb_data == '800') echo ' selected="selected"';			echo '>800万円</option>';
		echo '<option value="900"';			if ($kalb_data == '900') echo ' selected="selected"';			echo '>900万円</option>';
		echo '<option value="1000"';			if ($kalb_data == '1000') echo ' selected="selected"';			echo '>1000万円</option>';
		echo '<option value="1100"';			if ($kalb_data == '1100') echo ' selected="selected"';			echo '>1100万円</option>';
		echo '<option value="1200"';			if ($kalb_data == '1200') echo ' selected="selected"';			echo '>1200万円</option>';
		echo '<option value="1300"';			if ($kalb_data == '1300') echo ' selected="selected"';			echo '>1300万円</option>';
		echo '<option value="1400"';			if ($kalb_data == '1400') echo ' selected="selected"';			echo '>1400万円</option>';
		echo '<option value="1500"';			if ($kalb_data == '1500') echo ' selected="selected"';			echo '>1500万円</option>';
		echo '<option value="1600"';			if ($kalb_data == '1600') echo ' selected="selected"';			echo '>1600万円</option>';
		echo '<option value="1700"';			if ($kalb_data == '1700') echo ' selected="selected"';			echo '>1700万円</option>';
		echo '<option value="1800"';			if ($kalb_data == '1800') echo ' selected="selected"';			echo '>1800万円</option>';
		echo '<option value="1900"';			if ($kalb_data == '1900') echo ' selected="selected"';			echo '>1900万円</option>';
		echo '<option value="2000"';			if ($kalb_data == '2000') echo ' selected="selected"';			echo '>2000万円</option>';
		echo '<option value="3000"';			if ($kalb_data == '3000') echo ' selected="selected"';			echo '>3000万円</option>';
		echo '<option value="5000"';			if ($kalb_data == '5000') echo ' selected="selected"';			echo '>5000万円</option>';
		echo '<option value="7000"';			if ($kalb_data == '7000') echo ' selected="selected"';			echo '>7000万円</option>';
		echo '<option value="10000"';			if ($kalb_data == '10000') echo ' selected="selected"';			echo '>1億円</option>';
		echo '</select>～';

		echo '<select name="kahb" id="kahb">';
		echo '<option value="300"';			if ($kahb_data == '300') echo ' selected="selected"';			echo '>300万円</option>';
		echo '<option value="400"';			if ($kahb_data == '400') echo ' selected="selected"';			echo '>400万円</option>';
		echo '<option value="500"';			if ($kahb_data == '500') echo ' selected="selected"';			echo '>500万円</option>';
		echo '<option value="600"';			if ($kahb_data == '600') echo ' selected="selected"';			echo '>600万円</option>';
		echo '<option value="700"';			if ($kahb_data == '700') echo ' selected="selected"';			echo '>700万円</option>';
		echo '<option value="800"';			if ($kahb_data == '800') echo ' selected="selected"';			echo '>800万円</option>';
		echo '<option value="900"';			if ($kahb_data == '900') echo ' selected="selected"';			echo '>900万円</option>';
		echo '<option value="1000"';			if ($kahb_data == '1000') echo ' selected="selected"';			echo '>1000万円</option>';
		echo '<option value="1100"';			if ($kahb_data == '1100') echo ' selected="selected"';			echo '>1100万円</option>';
		echo '<option value="1200"';			if ($kahb_data == '1200') echo ' selected="selected"';			echo '>1200万円</option>';
		echo '<option value="1300"';			if ($kahb_data == '1300') echo ' selected="selected"';			echo '>1300万円</option>';
		echo '<option value="1400"';			if ($kahb_data == '1400') echo ' selected="selected"';			echo '>1400万円</option>';
		echo '<option value="1500"';			if ($kahb_data == '1500') echo ' selected="selected"';			echo '>1500万円</option>';
		echo '<option value="1600"';			if ($kahb_data == '1600') echo ' selected="selected"';			echo '>1600万円</option>';
		echo '<option value="1700"';			if ($kahb_data == '1700') echo ' selected="selected"';			echo '>1700万円</option>';
		echo '<option value="1800"';			if ($kahb_data == '1800') echo ' selected="selected"';			echo '>1800万円</option>';
		echo '<option value="1900"';			if ($kahb_data == '1900') echo ' selected="selected"';			echo '>1900万円</option>';
		echo '<option value="2000"';			if ($kahb_data == '2000') echo ' selected="selected"';			echo '>2000万円</option>';
		echo '<option value="3000"';			if ($kahb_data == '3000') echo ' selected="selected"';			echo '>3000万円</option>';
		echo '<option value="5000"';			if ($kahb_data == '5000') echo ' selected="selected"';			echo '>5000万円</option>';
		echo '<option value="7000"';			if ($kahb_data == '7000') echo ' selected="selected"';			echo '>7000万円</option>';
		echo '<option value="10000"';			if ($kahb_data == '10000') echo ' selected="selected"';			echo '>1億円</option>';
		echo '<option value="0"';			if ($kahb_data == '0' ||$kahb_data == '' ) echo ' selected="selected"';			echo '>上限なし</option>';
		echo '</select>';
		echo '</div>';


		//駅歩分
		echo '<div id="hofun" class="hofun">';
		echo '<span class="jsearch_hofun">駅歩分</span><br />';
		echo '<select name="hof" id="hof">';
		echo '<option value="0">指定なし</option>';
		echo '<option value="1"';
			if ($hof_data == '1') echo ' selected="selected"';
			echo '>1分以内</option>';
		echo '<option value="3"';
			if ($hof_data == '3') echo ' selected="selected"';
			echo '>3分以内</option>';
		echo '<option value="5"';
			if ($hof_data == '5') echo ' selected="selected"';
			echo '>5分以内</option>';
		echo '<option value="10"';
			if ($hof_data == '10') echo ' selected="selected"';
			echo '>10分以内</option>';
		echo '<option value="15"';
			if ($hof_data == '15') echo ' selected="selected"';
			echo '>15分以内</option>';
		echo '</select>';
		echo '</div>';




		//間取り
		echo '<div id="madori_cb" class="madori_cb"></div>';


		//築年数
		echo '<div id="chikunen" class="chikunen">';
		echo '<span class="jsearch_chikunen">築年数</span><br />';
		echo '<select name="tik" id="tik">';
		echo '<option value="0">指定なし</option>';
		echo '<option value="1"';			if ($tik_data == '1') echo ' selected="selected"';			echo '>1年以内</option>';
		echo '<option value="3"';			if ($tik_data == '3') echo ' selected="selected"';			echo '>3年以内</option>';
		echo '<option value="5"';			if ($tik_data == '5') echo ' selected="selected"';			echo '>5年以内</option>';
		echo '<option value="10"';			if ($tik_data == '10') echo ' selected="selected"';			echo '>10年以内</option>';
		echo '<option value="15"';			if ($tik_data == '15') echo ' selected="selected"';			echo '>15年以内</option>';
		echo '<option value="20"';			if ($tik_data == '20') echo ' selected="selected"';			echo '>20年以内</option>';
		echo '</select>';
		echo '</div>';


		//面積
		echo '<div id="memseki" class="memseki">';
		echo '<span class="jsearch_memseki">面積</span><br />';
		echo '<select name="mel" id="mel">';
		echo '<option value="0">下限なし</option>';
		echo '<option value="10"';			if ($mel_data == '10') echo ' selected="selected"';			echo '>10m&sup2;</option>';
		echo '<option value="15"';			if ($mel_data == '15') echo ' selected="selected"';			echo '>15m&sup2;</option>';
		echo '<option value="20"';			if ($mel_data == '20') echo ' selected="selected"';			echo '>20m&sup2;</option>';
		echo '<option value="25"';			if ($mel_data == '25') echo ' selected="selected"';			echo '>25m&sup2;</option>';
		echo '<option value="30"';			if ($mel_data == '30') echo ' selected="selected"';			echo '>30m&sup2;</option>';
		echo '<option value="35"';			if ($mel_data == '35') echo ' selected="selected"';			echo '>35m&sup2;</option>';
		echo '<option value="40"';			if ($mel_data == '40') echo ' selected="selected"';			echo '>40m&sup2;</option>';
		echo '<option value="50"';			if ($mel_data == '50') echo ' selected="selected"';			echo '>50m&sup2;</option>';
		echo '<option value="60"';			if ($mel_data == '60') echo ' selected="selected"';			echo '>60m&sup2;</option>';
		echo '<option value="70"';			if ($mel_data == '70') echo ' selected="selected"';			echo '>70m&sup2;</option>';
		echo '<option value="80"';			if ($mel_data == '80') echo ' selected="selected"';			echo '>80m&sup2;</option>';
		echo '<option value="90"';			if ($mel_data == '90') echo ' selected="selected"';			echo '>90m&sup2;</option>';
		echo '<option value="100"';			if ($mel_data == '100') echo ' selected="selected"';			echo '>100m&sup2;</option>';
		echo '<option value="200"';			if ($mel_data == '200') echo ' selected="selected"';			echo '>200m&sup2;</option>';
		echo '<option value="300"';			if ($mel_data == '300') echo ' selected="selected"';			echo '>300m&sup2;</option>';
		echo '<option value="400"';			if ($mel_data == '400') echo ' selected="selected"';			echo '>400m&sup2;</option>';
		echo '<option value="500"';			if ($mel_data == '500') echo ' selected="selected"';			echo '>500m&sup2;</option>';
		echo '<option value="600"';			if ($mel_data == '600') echo ' selected="selected"';			echo '>600m&sup2;</option>';
		echo '<option value="700"';			if ($mel_data == '700') echo ' selected="selected"';			echo '>700m&sup2;</option>';
		echo '<option value="800"';			if ($mel_data == '800') echo ' selected="selected"';			echo '>800m&sup2;</option>';
		echo '<option value="900"';			if ($mel_data == '900') echo ' selected="selected"';			echo '>900m&sup2;</option>';
		echo '<option value="1000"';			if ($mel_data == '1000') echo ' selected="selected"';			echo '>1000m&sup2;</option>';
		echo '</select>～';

		echo '<select name="meh" id="meh">';
		echo '<option value="10"';			if ($meh_data == '10') echo ' selected="selected"';			echo '>10m&sup2;</option>';
		echo '<option value="15"';			if ($meh_data == '15') echo ' selected="selected"';			echo '>15m&sup2;</option>';
		echo '<option value="20"';			if ($meh_data == '20') echo ' selected="selected"';			echo '>20m&sup2;</option>';
		echo '<option value="25"';			if ($meh_data == '25') echo ' selected="selected"';			echo '>25m&sup2;</option>';
		echo '<option value="30"';			if ($meh_data == '30') echo ' selected="selected"';			echo '>30m&sup2;</option>';
		echo '<option value="35"';			if ($meh_data == '35') echo ' selected="selected"';			echo '>35m&sup2;</option>';
		echo '<option value="40"';			if ($meh_data == '40') echo ' selected="selected"';			echo '>40m&sup2;</option>';
		echo '<option value="50"';			if ($meh_data == '50') echo ' selected="selected"';			echo '>50m&sup2;</option>';
		echo '<option value="60"';			if ($meh_data == '60') echo ' selected="selected"';			echo '>60m&sup2;</option>';
		echo '<option value="70"';			if ($meh_data == '70') echo ' selected="selected"';			echo '>70m&sup2;</option>';
		echo '<option value="80"';			if ($meh_data == '80') echo ' selected="selected"';			echo '>80m&sup2;</option>';
		echo '<option value="90"';			if ($meh_data == '90') echo ' selected="selected"';			echo '>90m&sup2;</option>';
		echo '<option value="100"';			if ($meh_data == '100') echo ' selected="selected"';			echo '>100m&sup2;</option>';
		echo '<option value="200"';			if ($meh_data == '200') echo ' selected="selected"';			echo '>200m&sup2;</option>';
		echo '<option value="300"';			if ($meh_data == '300') echo ' selected="selected"';			echo '>300m&sup2;</option>';
		echo '<option value="400"';			if ($meh_data == '400') echo ' selected="selected"';			echo '>400m&sup2;</option>';
		echo '<option value="500"';			if ($meh_data == '500') echo ' selected="selected"';			echo '>500m&sup2;</option>';
		echo '<option value="600"';			if ($meh_data == '600') echo ' selected="selected"';			echo '>600m&sup2;</option>';
		echo '<option value="700"';			if ($meh_data == '700') echo ' selected="selected"';			echo '>700m&sup2;</option>';
		echo '<option value="800"';			if ($meh_data == '800') echo ' selected="selected"';			echo '>800m&sup2;</option>';
		echo '<option value="900"';			if ($meh_data == '900') echo ' selected="selected"';			echo '>900m&sup2;</option>';
		echo '<option value="1000"';			if ($meh_data == '1000') echo ' selected="selected"';			echo '>1000m&sup2;</option>';
		echo '<option value="0"';			if ($meh_data == '0' ||$meh_data == '' ) echo ' selected="selected"';			echo '>上限なし</option>';
		echo '</select>';
		echo '</div>';


		//設備
		echo '<div id="setsubi_cb" class="setsubi_cb"></div>';

//路線選択
		if($r_view != 1){

			echo '<div id="roseneki" class="roseneki">';
			echo '<span class="jsearch_roseneki">駅選択</span><br />';
			echo '<select name="ros" id="ros" onchange="SEki(this)">';
			echo '<option value="0">路線選択</option>';

			if( $shu_data !=''){

				$sql  =  "SELECT DISTINCT DTR.rosen_name, PM.meta_value AS rosen_id";
				$sql .=  " FROM (($wpdb->posts as P";
				$sql .=  " INNER JOIN $wpdb->postmeta as PM   ON P.ID = PM.post_id) ";
				$sql .=  " INNER JOIN $wpdb->postmeta AS PM_1 ON P.ID = PM_1.post_id) ";
				$sql .=  " INNER JOIN ".$wpdb->prefix."train_rosen as DTR ON CAST( PM.meta_value AS SIGNED ) = DTR.rosen_id";
				$sql .=  " WHERE (PM.meta_key='koutsurosen1' Or PM.meta_key='koutsurosen2') ";
				$sql .=  " AND P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo' AND PM_1.meta_key='bukkenshubetsu'";
				$sql .=  " AND CAST( PM_1.meta_value AS SIGNED ) " . $shu_data;
				$sql = $wpdb->prepare($sql,'');
				$metas = $wpdb->get_results( $sql,  ARRAY_A );

				if(!empty($metas)) {

					arsort($metas);

					foreach ( $metas as $meta ) {
						echo '<option value="'.$meta['rosen_id'].'"';
						if($ros_id == $meta['rosen_id'] )
							echo ' selected="selected"';
						echo '>'.$meta['rosen_name'].'</option>';
					}
				}
			}

			echo '</select><br />';


			//駅選択
			echo '<select name="eki" id="eki">';
			echo '<option value="0">駅選択</option>';

			if( $shu_data !='' && $ros_id !='' ){

				$sql  =  " SELECT DISTINCT PM.meta_value AS station_id ";
				$sql .=  " FROM ((( $wpdb->posts as P ";
				$sql .=  " INNER JOIN $wpdb->postmeta as PM ON P.ID = PM.post_id )";
				$sql .=  " INNER JOIN $wpdb->postmeta AS PM_1 ON P.ID = PM_1.post_id )";
				$sql .=  " INNER JOIN $wpdb->postmeta AS PM_2 ON P.ID = PM_2.post_id )";
				$sql .=  " WHERE";
				$sql .=  " (";
				$sql .=  " 	P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo' ";
				$sql .=  " 	AND PM.meta_key='koutsueki1'";
				$sql .=  " 	AND PM_1.meta_key='koutsurosen1' AND PM_1.meta_value = ".$ros_id."";
				$sql .=  " 	AND PM_2.meta_key='bukkenshubetsu' AND PM_2.meta_value ".$shu_data."";
				$sql .=  " )";
				$sql .=  " or";
				$sql .=  " (";
				$sql .=  " 	P.post_status='publish' AND P.post_password = '' AND P.post_type ='fudo' ";
				$sql .=  " 	AND PM.meta_key='koutsueki2'";
				$sql .=  " 	AND PM_1.meta_key='koutsurosen2' AND PM_1.meta_value = ".$ros_id."";
				$sql .=  " 	AND PM_2.meta_key='bukkenshubetsu' AND PM_2.meta_value ".$shu_data."";
				$sql .=  " )";

				$sql = $wpdb->prepare($sql,'');
				$metas = $wpdb->get_results( $sql,  ARRAY_A );

				$tmp_eki = '';
				if(!empty($metas)) {
					$tmp_eki = '0';
					foreach ( $metas as $meta ) {
						if( $meta['station_id'] != '')
							$tmp_eki .= ','. $meta['station_id'];
					}
				}

				if( $tmp_eki != ''){
					$sql  =  " SELECT DISTINCT DTS.station_name , DTS.station_id ";
					$sql .=  " FROM ".$wpdb->prefix."train_station as DTS";
					$sql .=  " WHERE DTS.rosen_id=".$ros_id." AND DTS.station_id in (".$tmp_eki.") ";
					$sql .=  " ORDER BY DTS.station_ranking";
					$sql = $wpdb->prepare($sql,'');
					$metas = $wpdb->get_results( $sql,  ARRAY_A );
					if(!empty($metas)) {

						foreach ( $metas as $meta ) {
							echo '<option value="'.$meta['station_id'].'"';
							if($eki_id == $meta['station_id'] )
								echo ' selected="selected"';
							echo '>'.$meta['station_name'].'</option>';
						}
					}
				}

			}

			echo '</select>';
			echo '</div>';
		}


		echo '<input type="submit" id="btn" value="物件検索" />';
		echo '</form>';

		echo $after_widget;

		//SSL
		$fudou_ssl_site_url = get_option('fudou_ssl_site_url');
		if( $fudou_ssl_site_url !='' && false === empty($_SERVER['HTTPS'])  &&  'off' !== $_SERVER['HTTPS'] ){
			$tmp_url = $fudou_ssl_site_url.'/wp-content/plugins';
		}else{
			$tmp_url = WP_PLUGIN_URL;
		}

		echo '<script type="text/javascript">';
		echo 'var getsite="' . $tmp_url . '/fudou/json/";';
		echo 'var r_view="'.$r_view.'";';
		echo 'var c_view="'.$c_view.'";';
		echo '</script>';
		echo '<script type="text/javascript" src="'.$tmp_url.'/fudou/js/util.js"></script>';
		echo '<script type="text/javascript" src="'.$tmp_url.'/fudou/js/jsearch.js"></script>';


		if( get_option('template')=='twentythirteen' ){
			?>
			<script type="text/javascript">
				function reload_twentyThirteen(){
					jQuery.noConflict();
					var j$ = jQuery;

					j$( function() {
						var sidebar   = j$( '#secondary .widget-area' ),
							secondary = ( 0 == sidebar.length ) ? -40 : sidebar.height(),
							margin    = j$( '#tertiary .widget-area' ).height() - j$( '#content' ).height() - secondary;

							if ( margin > 0 && window.innerWidth > 999 )
								j$( '#colophon' ).css( 'margin-top', margin + 'px' );
						} );
				}

				addOnload_twentyThirteen(function() { setTimeout(reload_twentyThirteen,3000); });
				function addOnload_twentyThirteen(func){
					try {
						window.addEventListener("load", func, false);
					} catch (e) {   
						window.attachEvent("onload", func);   	// IE用
					}
				}
			</script>
			<?php
		}

	}

} // Class fudo_widget_b_k




// 更新情報(Twitter)2
class fudo_widget_twitter2 extends WP_Widget {

	/** constructor */
	function fudo_widget_twitter2() {
		$widget_ops = array( 'description' => 'Twitterのつぶやきを更新情報として表示します。' );
		parent::WP_Widget( false , $name = '更新情報(Twitter)' , $widget_ops );
	}

	/** @see WP_Widget::form */	
	function form($instance) {


		$title  = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$t_user = isset($instance['t_user']) ? esc_attr($instance['t_user']) : '';
		$item   = isset($instance['item']) ? esc_attr($instance['item']) : '';

		$consumerKey = isset($instance['consumerKey']) ? esc_attr($instance['consumerKey']) : '';
		$consumerSecret = isset($instance['consumerSecret']) ? esc_attr($instance['consumerSecret']) : '';
		$accessToken = isset($instance['accessToken']) ? esc_attr($instance['accessToken']) : '';
		$accessTokenSecret = isset($instance['accessTokenSecret']) ? esc_attr($instance['accessTokenSecret']) : '';


		if($item=='') $item = 5;

		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">
		<?php _e('title'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('t_user'); ?>">
		Twitterのユーザ名 <input class="widefat" id="<?php echo $this->get_field_id('t_user'); ?>" name="<?php echo $this->get_field_name('t_user'); ?>" type="text" value="<?php echo $t_user; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('item'); ?>">
		表示数<input class="widefat" id="<?php echo $this->get_field_id('item'); ?>" name="<?php echo $this->get_field_name('item'); ?>" type="text" value="<?php echo $item; ?>" /></label></p>

		Twitter OAuth settings
		<p><label for="<?php echo $this->get_field_id('consumerKey'); ?>">
		Consumer key <input class="widefat" id="<?php echo $this->get_field_id('consumerKey'); ?>" name="<?php echo $this->get_field_name('consumerKey'); ?>" type="text" value="<?php echo $consumerKey; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('consumerSecret'); ?>">
		Consumer secret <input class="widefat" id="<?php echo $this->get_field_id('consumerSecret'); ?>" name="<?php echo $this->get_field_name('consumerSecret'); ?>" type="text" value="<?php echo $consumerSecret; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('accessToken'); ?>">
		Access token <input class="widefat" id="<?php echo $this->get_field_id('accessToken'); ?>" name="<?php echo $this->get_field_name('accessToken'); ?>" type="text" value="<?php echo $accessToken; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('accessTokenSecret'); ?>">
		Access token secret <input class="widefat" id="<?php echo $this->get_field_id('accessTokenSecret'); ?>" name="<?php echo $this->get_field_name('accessTokenSecret'); ?>" type="text" value="<?php echo $accessTokenSecret; ?>" /></label></p>

		<?php 

		if (!function_exists('json_decode')) {	//for php <5.2
			echo '*json_decodeを有効にしてください。<br />';
		}
		if (!function_exists('curl_init')) {	//XAMP
			echo '*php_curl.dllを有効にしてください。<br />';
		}

		/*
		php.ini
		；extension=php_curl.dll
		▼
		extension=php_curl.dll 
		*/

	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {

		//$site = home_url( '/' ); 
		$site = site_url( '/' ); 

	        extract( $args );
	        $title = apply_filters('widget_title', $instance['title']);
		$t_user = isset($instance['t_user']) ? $instance['t_user'] : '' ;
		$item = $instance['item'];
		$consumerKey		= isset($instance['consumerKey'])	? trim($instance['consumerKey']) : '' ;
		$consumerSecret		= isset($instance['consumerSecret'])	? trim($instance['consumerSecret']) : '' ;
		$accessToken		= isset($instance['accessToken'])	? trim($instance['accessToken']) : '' ;
		$accessTokenSecret	= isset($instance['accessTokenSecret']) ? trim($instance['accessTokenSecret']) : '' ;


		if( $consumerKey != '' && $consumerSecret != '' && $accessToken != '' && $accessTokenSecret != '' ){

			echo $before_widget;

			if ( $title =='' ) $title = '更新情報 (Twitter)';
			echo $before_title .'<span style="float: right; margin:-7px 0;"><a href="http://www.twitter.com/'.$t_user.'" target="_blank" rel="nofollow"><img src="'.$site.'wp-content/plugins/fudou/img/twitter-forrow.png" alt="@'.$t_user.'をフォローしましょう" title="@'.$t_user.'をフォローしましょう" /></a></span>' . $title . $after_title; 
			//echo $before_title .'<span style="float: right;"><a href="https://twitter.com/'.$t_user.'" class="twitter-follow-button" data-show-count="false" data-lang="ja">'.$t_user.'</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></span>' . $title . $after_title; 

			if ( function_exists('json_decode') ) {	//for php <5.2

				//cash
				$cash_interval = get_option('cash_interval');
				if($cash_interval == '' ) $cash_interval = 5;	//min
				$cash_time = get_option($args['widget_id']. '_time');
				$cash_data = get_option($args['widget_id']);

				//$cash_interval = 0;	//test

				if( $cash_interval != 0 && !empty( $cash_data ) && strtotime( $cash_time ) > strtotime(date("Y/m/d  H:i:s",strtotime("-$cash_interval min")))){
					echo "\n<!-- cash_data " . $cash_time . " -->\n";
					$decode = $cash_data;
				}else{

					//Twitter
					require_once("twitteroauth/twitteroauth.php");
					$twObj = new TwitterOAuth($consumerKey,$consumerSecret,$accessToken,$accessTokenSecret);
					$json = $twObj->OAuthRequest("https://api.twitter.com/1.1/statuses/user_timeline.json","GET",array("count"=>"$item"));
					$decode = json_decode($json, true);

					if( isset($decode[0]['text']) ){
						if( $cash_interval != 0 ){
							update_option($args['widget_id'], $decode);
							update_option($args['widget_id']. '_time', date("Y/m/d H:i:s") );
						}
					}else{	//取得できない場合
						echo "\n<!-- cash_data " . $cash_time . " -->\n";
						$decode = $cash_data;
					}
				}

				echo '<ul id="twitter_update_list">';
				echo "\n";
				if( isset($decode[0]['text']) ){

					foreach ($decode as $val) {
						$text    = isset($val['text']) ? $val['text'] : '';
						$pattern = array ("/(https?:\/\/[a-zA-Z0-9\-\.\/~_]+)/","/(\s|^)@([a-zA-Z0-9\.\/~\-_]+)\b/",);
						$replace = array (
							"<a href='\\1' target=\"_blank\">\\1</a>",
							"<a href=\"http://twitter.com/\\2\" rel=\"external\" target=\"_blank\">@\\2</a> ",
						);

						$text = preg_replace($pattern, $replace, $text);
						if ( isset($val['entities']['hashtags']) ) {
							foreach ($val['entities']['hashtags'] as $hashtag) {
								$pattern = "/(\s|　|^)#".$hashtag['text']."/";
								$replace = " <a href=\"http://search.twitter.com/search?q=%23".$hashtag['text']."\" rel=\"external\" target=\"_blank\">#".$hashtag['text']."</a>";
								$text = preg_replace($pattern, $replace, $text);
							}
						}
						$tweet_id = isset($val['id_str']) ? $val['id_str'] : '';
						$time     = isset($val['created_at']) ? strtotime($val['created_at']) : '';
						if( $time != '' ){
							$date = date("c", $time);
							$date = date("Y/n/j", $time);
							echo '<li><a class="tw_date" href="http://twitter.com/' .$t_user. '/status/' .$tweet_id. '" rel="external" target="_blank">'.$date.'</a> <span class="tw_status">' .$text. '</span></li>';
							echo "\n";
						}
					}
				}
				echo '</ul>';
				echo "\n";
			}

			echo $after_widget;
		}
	}
} // Class fudo_widget_twitter2



?>