<form method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
     <div>
          <input type="text" onfocus="if (this.value == '検索') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search';}"  value="検索" name="s" id="s" />
          <input type="submit" id="searchsubmit" value="検索" />
     </div>
</form>