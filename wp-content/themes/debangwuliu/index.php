<?php get_header(); ?>


<TABLE width=1002 border=0 align="center" cellPadding=0 cellSpacing=0 bgColor=#ffffff>
  <TBODY>
  <TD vAlign=top>
      <TABLE height=497 cellSpacing=２ cellPadding=2 width="92%" align=center 
      border=0>
        <TBODY>
          <TR>
            <TD vAlign=top height=30></TD>
            <TD></TD></TR>
          <TR>
            
			<?php get_sidebar(); ?>
			
            <TD vAlign=top width="77%">
              <TABLE cellSpacing=2 cellPadding=2 width="96%" align=center 
border=0>
                <TBODY>
                  <TR>
                    <TD vAlign=top 
                background=<?php bloginfo('template_url');?>/images/jjbg.jpg height=33 
                tppabs="<?php bloginfo('template_url');?>/images/jjbg.jpg">
                      <TABLE cellSpacing=0 cellPadding=0 width="90%" border=0>
                        <TBODY>
                          <TR>
                            <TD width="6%">&nbsp;</TD>
                            <TD class=zt11 width="94%">
					
					<?php $page=get_page(get_option('db_index_1')); ?>		
                        <DIV align=left><?php echo $page->post_title; ?></DIV></TD></TR></TBODY></TABLE></TD></TR>
                  <TR>
                    <TD vAlign=top height=214 id="page_tmb"><?php echo db_page_thumbnail($page->ID); ?>  
				  <P style="padding-left:10px;"><?php echo mb_strimwidth(strip_tags($page->post_content),0,620); ?>...
				  
				  <a href='<?php echo get_permalink($page->ID); ?>'  title='点击查看更多内容...'>..[详情].</a></TD></TR></P>
				  <?php ?>
				  
                  <?php 
				  	$cat=get_option('db_index_2'); include('includes/part-index-1.php');
					$cat=get_option('db_index_3'); include('includes/part-index-1.php');
				  ?>				  
				
				
				</TBODY></TABLE></TD></TR></TBODY></TABLE>
      <TABLE width=963 border=0>
        <TBODY>
          <TR>
		  
		  <?php for($m=4;$m<=7;$m++):?>
            <?php $p_id=get_option('db_index_'.$m); $page=get_page($p_id); ?>
			<TD width=68><?php echo db_page_thumbnail($p_id,array(62,62)); ?></TD>
            <TD width=160><STRONG><a href="<?php echo get_permalink($p_id); ?>"><?php echo mb_strimwidth($page->post_title,0,20); ?></a> </STRONG><BR>              
            <?php echo mb_strimwidth(strip_tags($page->post_content),0,48); ?>...</TD>
		<?php endfor;?>	
            
          </TR></TBODY></TABLE></TD></TR>
  <TR>
    <TD class=b bgColor=#ffffff height=30>
      <DIV align=left>
        <p><span class="zt11"><STRONG>长沙德邦物流</STRONG>有限公司 
  订车电话：0731-82055565</span>  </DIV></TD></TR></TBODY></TABLE> 
      

<?php get_footer(); ?>