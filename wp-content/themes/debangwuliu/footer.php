<DIV align=center>
  <TABLE cellSpacing=0 cellPadding=0 width=1002 bgColor=#ffffff border=0>
  <TBODY>
  <TR>      </TR></TBODY></TABLE>
  <DIV align=center style="margin-top:10px;"><span class="STYLE1"><?php echo stripslashes(get_option('db_index_8')); ?></span></DIV>
</DIV>
	  
</DIV>
	  </TD>
	  </TR>
	  </TBODY>
	  </TABLE>
	  </DIV>
<center class="STYLE1" style="width:1002px; margin:0 auto;">友情链接：
  
  	<?php 
		$links=wp_list_bookmarks(array('title_li'=>'','categorize'=>false,'show_images'=>false,'echo'=>false));
		$links=str_replace('<li>','',$links);
		echo str_replace('</li>','',$links);
	?>
  
</center>
	  <p>
	  <?php wp_footer(); ?>
</BODY>
</HTML>