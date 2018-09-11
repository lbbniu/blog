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
			
            <td valign="top" width="77%">
              <table cellspacing="2" cellpadding="2" width="96%" align="center" border="0">
                <tbody>
                  <tr>
                    <td valign="top" background="<?php bloginfo('template_url');?>/images/jjbg.jpg" height="33">
                      <table cellspacing="0" cellpadding="0" width="90%" border="0">
                        <tbody>
                          <tr>
                            <td width="6%">&nbsp;</td>
                            <td class="zt11" width="94%">
                        <div align="left"><?php web589_crumbs();?><?php single_cat_title(); ?></div></td></tr></tbody></table></td></tr>
                  <tr>
                    <td><table cellspacing="0" cellpadding="0" width="100%" border="0">
                      <tbody>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody><tr>
                                <td width="10">&nbsp;</td>
                                <td width="100%"><br>
                                    <table width="100%" border="0" cellpadding="0" cellspacing="1">
                                      <tbody><tr>
                                        <td>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                              
                                              <tbody>
											  
											  <?php while(have_posts()): the_post();?>
											  <tr>
                                                <td width="6%" align="center" background="image/line-1.GIF"><img src="<?php bloginfo('template_url');?>/images/tubiao_3.jpg" width="8" height="9"></td>
                                                <td width="71%" height="20" align="left" class="p">&nbsp;<a href="<?php the_permalink(); ?>" target="_blank"><?php echo mb_strimwidth(get_the_title(),0,34); ?></a>
                                                    </td>
                                                <td width="23%" class="p"><span class="STYLE3">时间:<?php the_time('Y-m-d'); ?></span></td>
                                              </tr>
											<?php endwhile; ?>
                                              
                                              
                                          </tbody></table></td>
                                      </tr>
                                    </tbody></table>
                                  <table width="100%" height="20" border="0" align="center" cellpadding="0" cellspacing="1">
                                      <tbody><tr>
                                        <?php 
								$page=web589_paging_data();
								$sum=$page['sum'];
								$cur=$page['cur'];
								$pages=$page['pages'];
								$home= $cur==1 ? '' : '<a href="'.$page[1].'" >首页</a>';
								$end= $cur==$pages ? '' : '<a href="'.$page[$pages].'" >尾页</a>';
							?>
                              <td height="50" colspan="4" align="center" valign="middle" class="p">全部-共<?php echo $sum; ?>条信息&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;第<?php echo $cur; ?>页/共<?php echo $pages; ?>页&nbsp;<?php echo $home; ?>&nbsp;&nbsp;<?php previous_posts_link('上一页'); ?>&nbsp;<?php next_posts_link('下一页'); ?>&nbsp;<?php echo $end; ?>&nbsp;转到第<select name="sel_page" onchange="javascript:location=this.options[this.selectedIndex].value;">
							  
							  <?php for($n=1;$n<=$pages;$n++):?>
							  	<?php $selected=($n==$cur) ? 'selected' : ''; ?>
								<option value="<?php echo $page[$n]; ?>" <?php echo $selected; ?>><?php echo $n; ?></option>
							  <?php endfor;?>
							  
							  </select>页</td>
                                      </tr>
                                  </tbody></table></td>
                                <td width="11">&nbsp;</td>
                              </tr>
                            </tbody></table>
                              
                          </td>
                        </tr>
                      </tbody>
                    </table></td></tr></tbody></table></td>
			
			</TR></TBODY></TABLE>
      </TD></TR>
	  
  </TBODY></TABLE> 
      

<?php get_footer(); ?>