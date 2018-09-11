<TD vAlign=top width="23%">
              <TABLE height=697 cellSpacing=0 cellPadding=0 width="24%" align=left 
            border=0>
                <TBODY>
                  <TR>
                    <TD height=26 vAlign=bottom class="STYLE54"><IMG height=26 
                  src="<?php bloginfo('template_url');?>/images/dbzx.jpg" width=178 
                  tppabs="<?php bloginfo('template_url');?>/images/dbzx.jpg"></TD></TR>
                  <TR>
                    <TD vAlign=top height=238>
                      <TABLE class=sy style="PADDING-LEFT: 4px; TEXT-ALIGN: left" 
                  cellSpacing=0 cellPadding=0 width=178 align=center 
                  background=<?php bloginfo('template_url');?>/images/daohangbg2.jpg 
                  border=0 
                  tppabs="<?php bloginfo('template_url');?>/images/daohangbg2.jpg">
                        <TBODY>
						
						<?php query_posts(array('posts_per_page'=>9,'ignore_sticky_posts'=>true)); while(have_posts()): the_post();?>
						 
                          <TR>
                            <TD height=24><SPAN class=STYLE1><STRONG>·</STRONG><a href="<?php the_permalink(); ?>" target="_blank"><?php echo mb_strimwidth(get_the_title(),0,24); ?>..</a></SPAN> </TD></TR>
                        <?php endwhile; wp_reset_query(); ?>  
							 
                      </TBODY></TABLE></TD></TR>
					  
                  <TR>
                    <TD></TD></TR>

                  <TR>
                    <TD vAlign=bottom height=26><IMG height=26 
                  src="<?php bloginfo('template_url');?>/images/daohang.jpg" width=179 
                  tppabs="<?php bloginfo('template_url');?>/images/daohang.jpg"></TD></TR>
                  
				  <?php 
				  	$cats=get_categories();
					if($cats):
						foreach($cats as $cat):
							if(!empty($cat->parent)) continue;
				  ?>
				  <TR>
                    <TD class=ds0 
                background=<?php bloginfo('template_url');?>/images/daohangbg.jpg 
                height=25 
                  tppabs="<?php bloginfo('template_url');?>/images/daohangbg.jpg"><DIV 
                  align=center><A href="<?php echo get_category_link($cat->term_id); ?>"><?php echo $cat->name; ?></A></DIV></TD></TR>               
				  <?php endforeach; endif; ?>
				  
                  <TR>
                    <TD style="height:10px;"><IMG height=10 
                  src="<?php bloginfo('template_url');?>/images/daohangbg1.jpg" width=179 
                  tppabs="<?php bloginfo('template_url');?>/images/daohangbg1.jpg"></TD></TR>
                  <TR>
                    <TD><IMG height=95 src="<?php bloginfo('template_url');?>/images/1.gif" 
                  width=179 tppabs="<?php bloginfo('template_url');?>/images/1.gif"></TD></TR>
                  <TR>
            <TD height=109><IMG height=95 
                  src="<?php bloginfo('template_url');?>/images/3.gif" width=179 
                  tppabs="<?php bloginfo('template_url');?>/images/3.gif"></TD></TR></TBODY></TABLE></TD>