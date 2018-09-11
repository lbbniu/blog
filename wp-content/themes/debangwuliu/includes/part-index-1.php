                  <TR>
                    <TD vAlign=top 
                background=<?php bloginfo('template_url');?>/images/jjbg.jpg height=25 
                tppabs="<?php bloginfo('template_url');?>/images/jjbg.jpg">
                      <TABLE cellSpacing=0 cellPadding=0 width="90%" border=0>
                        <TBODY>
                          <TR>
                            <TD width="6%">&nbsp;</TD>
                            <TD class=zt11 width="94%">
								
                        <DIV align=left><?php echo get_cat_name($cat); ?></DIV></TD></TR></TBODY></TABLE></TD></TR>
                  <TR>
                    <TD vAlign=top height=152><table width="100%" border="0">
                      <tr>
                        <td width="50%"><DIV>
                          <UL class="ZT9">
                          
						  <?php query_posts(array('cat'=>$cat,'posts_per_page'=>8,'ignore_sticky_posts'=>true)); while(have_posts()): the_post(); ?>  
                            <LI style="BORDER-BOTTOM: #cccccc 1px dashed"><a href="<?php the_permalink(); ?>" target="_blank"><?php echo mb_strimwidth(get_the_title(),0,40); ?></a>
                           	</LI>
                         <?php endwhile; ?>   
                            
                            
                              </UL>
                        </DIV></td>
                        <td width="50%"><DIV class=liebiao_1>
                          <UL>
                            
                            <?php query_posts(array('cat'=>$cat,'posts_per_page'=>8,'ignore_sticky_posts'=>true,'offset'=>8)); while(have_posts()): the_post(); ?>  
                            <LI style="BORDER-BOTTOM: #cccccc 1px dashed"><a href="<?php the_permalink(); ?>" target="_blank"><?php echo mb_strimwidth(get_the_title(),0,40); ?></a>
                           	</LI>
                         <?php endwhile; ?>  
                            
                          </UL>
                        </DIV></td>
                      </tr>
                    </table></TD>
                  </TR>