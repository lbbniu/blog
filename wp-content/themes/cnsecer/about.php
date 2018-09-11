<?php  
/* 
Template Name:about
*/  
?> 


<?php get_header(); ?>


<div class="container" style="width:1010px;">
	
	<div class="about">

 	<p><span style="color:#008000;"><strong>关于本主题<strong></span></p>
      <br>
      <p>
        主题名字:不挂科三代<br>
        版本号:2.0 <br>
        开发日期:2013.11.1<br>
        发布日期:2013.11.4 <br>
		注意:主题暂时不支持移动终端,前两代的主题已经发布到本站WP主题下载分类下。
      </p>
      <br>
      <br>
      <p><span style="color:#008000;"><strong>关于博主</strong></span></p>
      <br>
      <p>PHP业余爱好者，计算机专业，大三。喜欢WP模板制作，欢迎志同道合的朋友加我QQ</p>
      <br>
      <p><span style="color:#008000;"><strong>关于本站</strong></span></p>
      <br>
      <p>
      1、里面的大部分文章都是自己遇到过的问题，方便以后查找。不过我比较懒，里面90%的文章都是我拷贝过来的.<br>
      2、本站为非营利性站点，希望里面的内容能为你提供帮助!<br>
      </p>
    
      <br>
      <p><span style="color:#008000;"><strong>投稿</strong></span></p>
      <br>
      <p>投稿请在本站注册会员，然后在后台发表文章，经过审核后即可在前台显示。或者发邮件至root@cnsecer.com</p>
      <br>
      <br>
      <p><span style="color:#008000;"><strong>联系方式</strong></span></p>
      <br>
      <p>
        <p>Q Q:156420012</p>
        <p>E-mail：root@cnsecer.om</p>
        <br>
        <br>
      <div class="progress progress-striped active">
        <div class="progress-bar"  role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
          <span >主题开发进度:80% Complete</span>
        </div>
      </div>
        <br>

    
    <!--下面的PHP代码含义就是从后台单页内容里面获取用户输入的数据，我这里直接在上面写死了，所以没有用到下面的代码-->

		<?php //if (have_posts()) : while (have_posts()) : the_post(); ?>
        <!-- <p> -->
          <?//php the_content(); ?>          
        <!-- </p> -->
    <?php// endwhile; endif; ?>
	</div>
</div>
<?php get_footer(); ?>