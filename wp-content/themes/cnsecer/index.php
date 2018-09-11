<?php get_header(); ?>

<!-- 内容开始 -->
<div class="container">
	<!--幻灯开始-->
	<?php include(TEMPLATEPATH . '/slider.php'); ?>
	<!--幻灯结束-->

	<!--内容一开始-->
	<div class="main">
		<?php
		$slug = stripslashes(get_option('cnsecer_product'));
		$cat=get_category_by_slug($slug); //获取分类别名为 wordpress 的分类数据
		$cat_links=get_category_link($cat->term_id); // 通过$cat数组里面的分类id获取分类链接
		?>
		<div class="title">
			<div class="left">
				<span>原创模板</span> 发布模板请 <a href="">投稿</a>，立即 <a href="">发布</a>原创作品赚取RMB吧!
			</div>
			<div class="right">
				<a href="<?php echo   $cat_links;?>">View More</a>
			</div>
		</div>
		<div class="content">
			<div class="row">
				<?php 
				$num =4;
				$str ='<span class="label label-danger">原创</span> ';
				include(TEMPLATEPATH . '/thumb250.php');
				 ?>
			</div>
		</div>

	</div>
	<!--内容一结束-->	
	<!--内容二开始-->
	<div class="main">
		<?php
		$slug = stripslashes(get_option('cnsecer_alltheme'));
		$cat=get_category_by_slug($slug); //获取分类别名为 wordpress 的分类数据
		$cat_links=get_category_link($cat->term_id); // 通过$cat数组里面的分类id获取分类链接
		?>
		<div class="title">
			<div class="left">
				<span>最新模板</span>
			</div>
			<div class="right">
				<a href="<?php echo $cat_links;?>">View More</a>
			</div>
		</div>
		<div class="content">
			<div class="row">
				<?php $num = 20;
				$str ='<span class="label label-success">最新</span> ';
				include(TEMPLATEPATH . '/thumb250.php');
				 ?>
			</div>
		</div>
	</div>
	<!--内容二结束-->

	<!--内容三开始-->
	<div class="main">
		<div class="title">
			<div class="left">
				<span>最新文章</span> 
			</div>
		</div>
		<div class="content">
			<div class="row">
				<div class="col-md-4">
					<?php
					$slug = stripslashes(get_option('cnsecer_ral'));
					$cat=get_category_by_slug($slug); //获取分类别名为 wordpress 的分类数据
					$cat_links=get_category_link($cat->term_id); // 通过$cat数组里面的分类id获取分类链接
					?>
					<div class="htitle">经验分享</div>
					<ul class="article-title">
					<?php $posts = get_posts( "category=($cat->term_id&numberposts=13" ); ?>      
					<?php if( $posts ) : ?>                                      
						<?php foreach( $posts as $post ) : setup_postdata( $post ); ?> 
						<!-- 输出开始-->
						<li>
							<span>
								<a href="<?php the_permalink() ?>" target="_blank"><?php echo mb_strimwidth(get_the_title(), 0, 28,"...") ?></a>	
							</span> 
							<em><?php the_time('20y-m-d')?></em>
						</li>
					    <!-- 输出结束 -->
						<?php endforeach; ?>                                          
					<?php endif; ?>
					</ul>
				</div>
				<div class="col-md-4">
					<?php
					$slug = stripslashes(get_option('cnsecer_rac'));
					$cat=get_category_by_slug($slug); //获取分类别名为 wordpress 的分类数据
					$cat_links=get_category_link($cat->term_id); // 通过$cat数组里面的分类id获取分类链接
					?>
					<div class="htitle">建站教程</div>
					<ul class="article-title">
					<?php $posts = get_posts( "category=($cat->term_id&numberposts=13" ); ?>      
					<?php if( $posts ) : ?>                                      
						<?php foreach( $posts as $post ) : setup_postdata( $post ); ?> 
						<!-- 输出开始-->
						<li>
							<span>
								<a href="<?php the_permalink() ?>" target="_blank"><?php echo mb_strimwidth(get_the_title(), 0, 28,"...") ?></a>	
							</span> 
							<em><?php the_time('20y-m-d')?></em>
						</li>
					    <!-- 输出结束 -->
						<?php endforeach; ?>                                          
					<?php endif; ?>
					</ul>					
				</div>
				<div class="col-md-4">
					<?php
					$slug = stripslashes(get_option('cnsecer_rar'));
					$cat=get_category_by_slug($slug); //获取分类别名为 wordpress 的分类数据
					$cat_links=get_category_link($cat->term_id); // 通过$cat数组里面的分类id获取分类链接
					?>
					<div class="row">
						<?php $num = 4;include(TEMPLATEPATH . '/thumb150.php'); ?>
					</div>					
				</div>
			</div>
		</div>
	</div>
	<!--内容三结束-->

	<!--内容四开始-->
	<div class="main">
		<div class="title">
			<div class="left">
				<span>友情链接</span> 
			</div>
		</div>	
		<div class="flinks">
		<? //php get_links_list(); ?> 
			<li><a href="#">链接地址</a></li>
			<li><a href="#">链接地址</a></li>
			<li><a href="#">链接地址</a></li>
			<li><a href="#">链接地址</a></li>
			<li><a href="#">链接地址</a></li>
			<li><a href="#">链接地址</a></li>
		</div>	
	</div>
	<!--内容四结束-->
</div>
<!--内容结束-->	

</div>
<!-- 内容结束 -->

<?php get_footer(); ?>