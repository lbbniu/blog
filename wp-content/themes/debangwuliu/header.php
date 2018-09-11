<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD>
<title><?php

	global $page, $paged;

	wp_title( '|', true, 'right' );

	bloginfo( 'name' );

	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	?></title>
<META http-equiv=Content-Type content="text/html; charset=<?php bloginfo('charset'); ?>">
<meta name="author" content="网络推广,www.houjinzhe.com" />
<LINK href="<?php bloginfo('stylesheet_url'); ?>" type=text/css rel=stylesheet>
<script src="<?php bloginfo('template_url'); ?>/Scripts/swfobject_modified.js" type="text/javascript"></script>
<STYLE type=text/css>
BODY {
	MARGIN: 0px; BACKGROUND-COLOR: #103483
}
a:link {
	color: #5c679f;
}
a:visited {
	color: #0090CF;
}
.STYLE54 {color: #0000CC}
</STYLE>
<?php wp_head(); ?>
</HEAD>
<BODY>
<DIV align=center>
 
<DIV align=center>
<TABLE cellSpacing=0 cellPadding=0 width=1002 bgColor=#ffffff border=0>
  <TBODY>
  <TR>
    <TD>
      <DIV class=syCss>
      <TABLE style="BACKGROUND-POSITION: 50% bottom" cellSpacing=0 cellPadding=0 
      width="100%" background=<?php bloginfo('template_url');?>/images/image1.png border=0 
      tppabs="<?php bloginfo('template_url');?>/images/image1.png">
        <TBODY>
        <TR>
          <TD colSpan=12>
            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
              <TBODY>
              <TR>
                <TD class=STYLE49 height=60><STRONG><A 
                  href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></A><?php bloginfo('description'); ?></STRONG></TD>
                <TD>
                  <P class=STYLE5 
                  align=right>&nbsp;&nbsp;&nbsp; 
                  <?php bloginfo('name'); ?>服务热线：<SPAN class=STYLE50>0731-88852820</SPAN></P>
                 </TD></TR></TBODY></TABLE></TD></TR>
        <TR>
          <TD align=right width=215><IMG height=62 alt="德邦logo" 
            src="<?php echo get_option('db_logo'); ?>" width=182 
            tppabs="<?php echo get_option('db_logo'); ?>"></TD>
			
			<?php 
				wp_nav_menu(array(
					'theme_location'=>'top',
					'fallback_cb'=>'',
					'container'=>'',
					'walker'=>new Db_Walker_Nav_Menu,
					'items_wrap'      => '%3$s',
				));
			?>
		  
		  
          <TD width=10></TD></TR></TBODY></TABLE></DIV></TD></TR>
  <TR>
    <TD background="<?php echo get_option('db_flash'); ?>" height=308 
    tppabs="<?php echo get_option('db_flash'); ?>"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="1002" height="308">
      <param name="movie" value="<?php bloginfo('template_url');?>/images/6.swf"/>
           <param name="wmode" value="transparent">
      <param name="quality" value="high" />
      <param name="wmode" value="opaque" />
      <embed src="<?php bloginfo('template_url');?>/images/6.swf" quality="high" wmode="opaque" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1002" height="308"></embed>
    </object></TD></TR>
  </TBODY></TABLE></DIV>
	  </TD>
	  </TR>
	  </TBODY>
	  </TABLE>
	  </DIV>