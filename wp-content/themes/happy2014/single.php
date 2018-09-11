<?php get_header(); ?>
<!--内容开始-->
<div class="container global" >
<div class="row">
<div class="col-md-9">
  <!-- single begin -->
  <div class="single">
  	<div class="top">
	    <h2 class="title"><a href="<?php the_permalink() ?>" target="_blank"><?php the_title(); ?></a></h2>
	    <div class="info">
	        <span class="glyphicon glyphicon-user"><b><?php rand_user(); ?> </b></span>
	        <span class="glyphicon glyphicon-calendar"><b><?php the_time('20y-m-d H:i:s')?></b> </span>
	        <span class="glyphicon glyphicon-comment"><b><?php comments_popup_link ('暂无评论','1条评论','%条评论'); ?></b> </span>
	        <span class="glyphicon glyphicon-wrench"><b><?php edit_post_link('编辑');?></b></span>
    			<div class="share hidden-xs">
    				<div class="bdsharebuttonbox"><a href="#" class="bds_more" data-cmd="more"></a><a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a></div>
    				<script>window._bd_share_config={"common":{"bdSnsKey":{"tsina":"3121219033","tqq":"801334400"},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{},"image":{"viewList":["qzone","tsina","tqq","renren","weixin","sqq"],"viewText":"分享到：","viewSize":"16"},"selectShare":{"bdContainerClass":null,"bdSelectMiniList":["qzone","tsina","tqq","renren","weixin","sqq"]}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89343201.js?cdnversion='+~(-new Date()/36e5)];</script>
    			</div>
	    </div>
    </div>

	<div class="ads hidden-xs hidden-sm">
		<?php echo stripslashes(get_option('cnsecer_ads-750')); ?> 
    </div>

    <div class="article">
      <p>   
 		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<p>
			<?php the_content(); ?>
			<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>
		</p>
		<?php endwhile; endif; ?>
      </p>
      <div class="clear"></div>
      <div class="article-footer">
  	    <div class="copyright">
  	    	如文中未特别声明转载请注明出自：<a href="http://www.consecer.com">CNSECER.COM </a>
  	    </div>
        <div class="tags"><?php the_tags('<span class="glyphicon glyphicon-tags"></span>', ', ', ','); ?></div> 
      </div>
    </div>


	 <!-- 相关文章 -->
	 
	 <?php include(TEMPLATEPATH . '/relatedPosts.php'); ?>
	 <!-- 相关文章 -->
  </div>

  
  <div class="clear"></div>
   <!-- 上下页开始 -->
  <div class="post_link ">
    <div class="prev hidden-xs">
		<?php previous_post_link('<span class="glyphicon glyphicon-chevron-left"></span> %link') ?>
    </div>
    <div class="next hidden-xs">
		<?php next_post_link('%link <span class="glyphicon glyphicon-chevron-right"></span> ') ?>
    </div>
  </div>
  <!-- 上下页结束 -->
  <!-- 评论开始 -->
  <div class="tit01"><h1>最新评论</h1></div>
  <div id="comments"><?php comments_template(); ?></div>  
  <!-- 评论结束 -->
  </div>
  <!-- single end -->
  <!-- sidebar begin -->
  <div class="col-md-3 hidden-xs">
  	<?php get_sidebar(); ?>
  </div>
  <!-- sidebar end -->
 </div> 

</div>
<!--内容结束-->


<?php get_footer(); ?>