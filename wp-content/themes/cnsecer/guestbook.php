<?php  
/* 
Template Name:guestbook
*/  
?> 


<?php get_header(); ?>


<div class="container" style ="width:1010px; background-color:#FFFFFF;margin-top:20px;">
	<div class="top"  style="height:150px;margin-top:10px;">
		 <?php poptip();?>
		<div class="single-ads">
	    	<?php echo stripslashes(get_option('cnsecer_single-ads-buttom')); ?>
	    </div>
	</div>
	<div id="comments"><?php comments_template(); ?></div>
</div>

<?php get_footer(); ?>