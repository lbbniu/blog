<?php  
/* 
Template Name:guestbook
*/  
?> 


<?php get_header(); ?>


<div class="container global" >


	<div class="container">
		<div class="row">
			
			<div class="col-md-9">
			<div class="guestbook">
				<div class="title"><p>有问题请再下面留言</p></div>			
				<div id="comments"><?php comments_template(); ?></div>
			</div>
			</div>
			<div class="col-md-3 hidden-xs">
				<?php get_sidebar(); ?>
			</div>
		</div>
	</div>
	<div class="top"  style="height:150px;margin-top:10px;">
	</div>
	
</div>

<?php get_footer(); ?>