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
                    <td valign="top" background="<?php bloginfo('template_url');?>/images/jjbg.jpg" height="33" >
                      <table cellspacing="0" cellpadding="0" width="90%" border="0">
                        <tbody>
                          <tr>
                            <td width="6%">&nbsp;</td>
                            <td class="zt11" width="94%">
                        <div align="left"><?php web589_crumbs();?></div></td></tr></tbody></table></td></tr>
                  <tr>
                    <td>
					<?php while(have_posts()): the_post();?>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tbody><tr>
                        <td width="17" height="26">&nbsp;</td>
                        <td width="100%" class="hgx">&nbsp;</td>
                        <td width="10">&nbsp;</td>
                      </tr>
                      <tr>
                        <td width="17"></td>
                        <td height="26" align="center" class="p"><font color="#FF0000"><strong><?php the_title(); ?></strong></font></td>
                        <td width="10">&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td height="27" class="hgx"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody><tr>
                              <td width="189">&nbsp;</td>
                              <td width="255" class="p">发布时间：<?php the_time('Y-m-d');?> 文章作者:<a href="http://www.houjinzhe.com/"><strong>长沙后进者网络公司</strong></a></td>
                            </tr>
                        </tbody></table></td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td height="143" valign="top" class="hgx"><table width="100%" border="0" cellpadding="4" cellspacing="1">
                            <tbody><tr>
                              <td class="p" id="post_content"><?php the_content(); ?></td>
                            </tr>
                        </tbody></table></td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td height="23">&nbsp;</td>
                        <td height="23" align="right" background="image/line-1.GIF" class="hgx"><span class="aa">【<a href="javascript:window.print()">打印此页</a>】 
                          【<a href="javascript:close()">关闭</a>】</span></td>
                        <td>&nbsp;</td>
                      </tr>
                    </tbody></table>
					<?php endwhile;?>
					</td></tr></tbody></table></td>
			
			</TR></TBODY></TABLE>
      </TD></TR>
	  
  </TBODY></TABLE> 
      

<?php get_footer(); ?>