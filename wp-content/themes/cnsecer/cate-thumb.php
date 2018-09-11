<?php get_header(); ?>

<!-- 内容开始 -->
<div class="container">
<div class="category">

  <div class="row">
    <div class="col-md-12">
      <div class="cate-title"><span>分类目录</span></div>
    </div>    
  </div>

  <div class="row">
    <div class="col-md-9">
      <!--cate-main begin-->
      <div class="cate-main">
        <div class="row">
			<?php 
        $str =rand_span();
        include(TEMPLATEPATH . '/thumb175.php');
       ?>
        </div>
      </div>
      <!--cate-main end-->  	

  	</div>
	<div class="col-md-3 hidden-xs">
		<?php get_sidebar(); ?>
	</div>
  </div>
</div>
<div class="navigation"><?php pagination($query_string);  ?></div>
</div>
<?php get_footer(); ?>