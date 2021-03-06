<?php






//增强编辑器开始
function add_editor_buttons($buttons) {
$buttons[] = 'fontselect';
$buttons[] = 'fontsizeselect';
$buttons[] = 'backcolor';
$buttons[] = 'underline';
$buttons[] = 'hr';
$buttons[] = 'sub';
$buttons[] = 'sup';
$buttons[] = 'cut';
$buttons[] = 'copy';
$buttons[] = 'paste';
$buttons[] = 'cleanup';
$buttons[] = 'wp_page';
$buttons[] = 'newdocument';
return $buttons;
}
add_filter("mce_buttons_3", "add_editor_buttons");

/**
 * [colorCloud description]
 * @param  [type] $text [彩色标签云]
 * @return [type]       [description]
 */
function colorCloud($text) {
$text = preg_replace_callback('|<a (.+?)>|i', 'colorCloudCallback', $text);
return $text;
}
function colorCloudCallback($matches) {
$text = $matches[1];
$color = dechex(rand(0,16777215));
$pattern = '/style=(\'|\")(.*)(\'|\")/i';
$text = preg_replace($pattern, "style=\"color:#{$color};$2;\"", $text);
return "<a $text>";
}
add_filter('wp_tag_cloud', 'colorCloud', 1);

//移除WordPress版本号
function wpbeginner_remove_version() {
return '';
}
add_filter('the_generator', 'wpbeginner_remove_version');
/**
 * [cnsecer_get_most_viewed description]
 * @param  integer $posts_num [获取热评文章]
 * @param  integer $days      [description]
 * @return [type]             [description]
 */
function cnsecer_get_most_viewed($posts_num=13, $days=180){
    global $wpdb;
    $sql = "SELECT ID , post_title , comment_count FROM $wpdb->posts WHERE post_type = 'post' AND TO_DAYS(now()) - TO_DAYS(post_date) < $days AND ($wpdb->posts.`post_status` = 'publish' OR $wpdb->posts.`post_status` = 'inherit') ORDER BY comment_count DESC LIMIT 0 , $posts_num ";
    $posts = $wpdb->get_results($sql);
    $output = "";
    $temp =1;
    
    foreach ($posts as $post){
        $sb =0; $str="<span class=\"label label-info\">";
        if($temp>9){
          $sb ="";
        }else{
          $sb=0;
        }
        if($temp>3){
          $str="<span class=\"label label-default\">";
        }else{
          $str="<span class=\"label label-info\">";
        }
        $output .= "\n<li>".$str.$sb.$temp++."</span><span><a href= \"".get_permalink($post->ID)."\" title=\"".$post->post_title."\" >".$post->post_title."</a></span></li>";
    }
    echo $output;
}
//随机输出以下作者
function rand_user(){
  $input=array('cnsecer','xiaoshi','admin','粑粑','安全者','毛泽西','习远平','安全者','匿名');
  $rand_keys = array_rand($input,1);
  echo $input[$rand_keys];
}
//随机输出标签
function rand_span(){
  $span=array(
          '<span class="label label-default">推荐</span>',
          '<span class="label label-info">热门</span>',
          '<span class="label label-warning">极品</span>',
          '<span class="label label-primary">精华</span>',
            );
  $rand_keys = array_rand($span,1);
   return $span[$rand_keys];
}
/**
 * [pagination 分页导航函数，BootStrap专用]
 * @param  [type] $query_string [description]
 * @return [type]               [description]
 */
function pagination($query_string){
global $posts_per_page, $paged;
$my_query = new WP_Query($query_string ."&posts_per_page=-1");
$total_posts = $my_query->post_count;
if(empty($paged))$paged = 1;
$prev = $paged - 1;             
$next = $paged + 1; 
$range = 3; // 分页数设置
$showitems = ($range * 2)+1;
$pages = ceil($total_posts/$posts_per_page);
if(1 != $pages){
  echo "<div class='page-nav'> <ul class=\"pagination\">";
    if($paged != 1){
    echo "<li><a href='" . get_pagenum_link(1) . "' class='extend'  title='跳转到首页'> 首页 </a></li>";
  }
    
  for ($i=1; $i <= $pages; $i++){
    if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )){
      echo ($paged == $i)? "<li><span class='current'>".$i."</span></li>":"<li><a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a></li>"; 
    }
  }

  if($paged != $pages){
    echo "<li><a href='" . get_pagenum_link($pages) . "' class='extend' title='跳转到最后一页'> 末页 </a></li>";
  }
  echo "</ul></div>\n";
}
}



/**
 * [post_thumbnail_src 获取文章缩略图]
 *  自定义字段为 thumb 的图片>特色缩略图>文章第一张图片>随机图片/默认图片；
 *  
 *  随机图片：请制作10张图片，放在现用主题文件夹下的 images/pic/ 目录，图片为jpg格式，并且使用数字 1-10命名，
 *  比如 1.jpg；如果你不想用随机图片，请将 倒数第5行 前面的“//”去掉，然后给 倒数第7、9行 前面添加“//”注销，
 *  并且在现用主题的 /images/ 目录下添加一张名字为 default_thumb.jpg 的默认图片，这样，就会显示默认图片。
 *
 * @return [type] [description]
 */
function post_thumbnail_src(){
    global $post;
  if( $values = get_post_custom_values("thumb") ) { //输出自定义域图片地址
    $values = get_post_custom_values("thumb");
    $post_thumbnail_src = $values [0];
  } elseif( has_post_thumbnail() ){    //如果有特色缩略图，则输出缩略图地址
        $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),'full');
    $post_thumbnail_src = $thumbnail_src [0];
    } else {
    $post_thumbnail_src = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $post_thumbnail_src = $matches [1] [0];   //获取该图片 src
    if(empty($post_thumbnail_src)){ //如果日志中没有图片，则显示随机图片
      $random = mt_rand(1, 10);
      echo get_bloginfo('template_url');
      echo '/images/pic/'.$random.'.jpg';
      //如果日志中没有图片，则显示默认图片
      //echo '/images/default_thumb.jpg';
    }
  };
  echo $post_thumbnail_src;
}



/**
 * [cnsecer_theme_setup 设置主题后保存并添加]
 * @return [type] [description]
 */
function cnsecer_theme_setup() {
  load_theme_textdomain( 'cnsecer', get_template_directory() . '/languages' );
  
  // 注册菜单
//  register_nav_menu( 'primary', __( '主导航菜单', 'cnsecer' ) );  
  //自定义导航
    register_nav_menus(
      array(
      'header_menu' => __( '头部主导航' ),
      'footer-menu' => __( '底部' ),
      )
    );
  
  // 为文章和评论在 <head> 标签上添加 RSS feed 链接。
  add_theme_support( 'automatic-feed-links' );

  // 主题支持的文章格式形式。
  // add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );

  // 主题为特色图像使用自定义图像尺寸，显示在 '标签' 形式的文章上。
  add_theme_support( 'post-thumbnails' );
  set_post_thumbnail_size( 650, 9999 );  
  }
add_action( 'after_setup_theme', 'cnsecer_theme_setup' );

/**
 * [bootstrap_nav_footer 显示底部显示链接]
 * @return [type] [description]
 */
function  bootstrap_nav_footer(){
  $defaults = array(
  'theme_location'  =>  'footer-menu',
  'menu_class'      =>  'nav navbar-nav',
  'menu_id'    =>  'cnsecer',
  'container'       =>  false,
  'walker'          =>  new BootStrap_Nav_Walker,
  ) ; 
  return wp_nav_menu($defaults);
}


/**
 * [bootstrap_nav 此函数可以使wp_nav_menu()支持BootStrap]
 * @return [type] [description]
 */
function  bootstrap_nav(){
  $defaults = array(
  'theme_location'  =>  'header_menu',
  'menu_class'      =>  'nav navbar-nav',
  'menu_id'    =>  'cnsecer',
  'container'       =>  false,
  'walker'          =>  new BootStrap_Nav_Walker,
  ) ; 
  return wp_nav_menu($defaults);
}

class BootStrap_Nav_Walker extends Walker_Nav_Menu {

     /*
      * @see Walker_Nav_Menu::start_lvl()
      */
     function start_lvl( &$output, $depth ) {
          $output .= "\n<ul class=\"dropdown-menu\">\n";
     }

     /*
      * @see Walker_Nav_Menu::start_el()
      */
     function start_el( &$output, $item, $depth, $args ) {
          global $wp_query;
         
          $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
          $li_attributes = $class_names = $value = '';
          $classes = empty( $item->classes ) ? array() : (array) $item->classes;
          $classes[] = 'menu-item-' . $item->ID;

          if ( $args->has_children ) {
               $classes[] = ( 1 > $depth) ? 'dropdown': 'dropdown-submenu';
               $li_attributes .= ' data-dropdown="dropdown"';
          }

          $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
          $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

          $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
          $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

          $output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';

          $attributes     =     $item->attr_title     ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
          $attributes     .=     $item->target          ? ' target="' . esc_attr( $item->target     ) .'"' : '';
          $attributes     .=     $item->xfn               ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
          $attributes     .=     $item->url               ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
          $attributes     .=     $args->has_children     ? ' class="dropdown-toggle" data-toggle="dropdown"' : '';

          $item_output     =     $args->before . '<a' . $attributes . '>';
          $item_output     .=     $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
          $item_output     .=     ( $args->has_children AND 1 > $depth ) ? ' <b class="caret"></b>' : '';
          $item_output     .=     '</a>' . $args->after;

          $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
     }

     /*
      * @see Walker::display_element()
      */
     function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
          if ( ! $element )
               return;
          $id_field = $this->db_fields['id'];
          //display this element
          if ( is_array( $args[0] ) )
               $args[0]['has_children'] = (bool) ( ! empty( $children_elements[$element->$id_field] ) AND $depth != $max_depth - 1 );
          elseif ( is_object(  $args[0] ) )
               $args[0]->has_children = (bool) ( ! empty( $children_elements[$element->$id_field] ) AND $depth != $max_depth - 1 );

          $cb_args = array_merge( array( &$output, $element, $depth ), $args );
          call_user_func_array( array( &$this, 'start_el' ), $cb_args );

          $id = $element->$id_field;

          // descend only when the depth is right and there are childrens for this element
          if ( ( $max_depth == 0 OR $max_depth > $depth+1 ) AND isset( $children_elements[$id] ) ) {

               foreach ( $children_elements[ $id ] as $child ) {

                    if ( ! isset( $newlevel ) ) {
                         $newlevel = true;
                         //start the child delimiter
                         $cb_args = array_merge( array( &$output, $depth ), $args );
                         call_user_func_array( array( &$this, 'start_lvl' ), $cb_args );
                    }
                    $this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
               }
               unset( $children_elements[ $id ] );
          }

          if ( isset( $newlevel ) AND $newlevel ) {
               //end the child delimiter
               $cb_args = array_merge( array( &$output, $depth ), $args );
               call_user_func_array( array( &$this, 'end_lvl' ), $cb_args );
          }

          //end this element
          $cb_args = array_merge( array( &$output, $element, $depth ), $args );
          call_user_func_array( array( &$this, 'end_el' ), $cb_args );
     }
}

/*
 * 给激活的导航菜单添加 .active
 */
function cnsecer_nav_menu_css_class( $classes ) {
     if ( in_array('current-menu-item', $classes ) OR in_array( 'current-menu-ancestor', $classes ) )
          $classes[]     =     'active';

     return $classes;
}
add_filter( 'nav_menu_css_class', 'cnsecer_nav_menu_css_class' );


?>


<?php
      /**
       * [description:后台自定义菜单模板,请勿修改。]
       * [Author:cnsecer.com]
       * [Date:2014.01.14]
       */
      $themename = "安全者";
      $shortname = "cnsecer";   //前缀  
      $options = array (
      array(
      "name" => "标题（Title)",
      "id" => $shortname."_title",   /*后缀 ,在前台引用时输入 <?php echo stripslashes(get_option('cnsecer_title')); ?>即可 */
      "type" => "text",
      "std" => "网站标题",
      "explain" => "SEO设置<br>它将显示在网站首页的title标签里，必填项。"
      ),
      array("name" => "描述（Description）",
      "id" => $shortname."_description",
      "type" => "textarea",
      "css" => "class='h60px'",
      "std" => "网站描述",
      "explain" => "SEO设置<br>它将显示在网站首页的meta标签的description属性里"
      ),
      array("name" => "关键字（KeyWords）",
      "id" => $shortname."_keywords",
      "type" => "textarea",
      "css" => "class='h60px'",
      "std" => "网站关键字",
      "explain" => "SEO设置<br>多个关键字请以英文逗号隔开，它将显示在网站首页的meta标签的keywords属性里"
      ),
      array("name" => "主页幻灯片1（Slider one）",
      "id" => $shortname."_slider1",
      "type" => "text",
      "std" => "images/slider/a.jpg",
      "explain" => "显示第一张幻灯片"
      ),
       array("name" => "主页幻灯片2（Slider two）",
      "id" => $shortname."_slider2",
      "type" => "text",
      "std" => "images/slider/b.jpg",
      "explain" => "显示第二张幻灯片"
      ),
      array("name" => "主页幻灯片3（Slider three）",
      "id" => $shortname."_slider3",
      "type" => "text",
      "std" => "images/slider/c.jpg",
      "explain" => "显示第三张幻灯片"
      ),


      array("name" => "主页作品展示(默认显示4篇)",
      "id" => $shortname."_product",
      "type" => "text",
      "std" => "输入分类名",
      "explain" => "它将在网站首页中间显示五个缩略图"
      ),
      array("name" => "主页最新主题(默认显示20篇)",
      "id" => $shortname."_alltheme",
      "type" => "text",
      "std" => "输入分类名",
      "explain" => "显示所有的主题，以缩略图形式显示"
      ),
      array("name" => "主页文章一",
      "id" => $shortname."_ral",
      "type" => "text",
      "std" => "输入分类名",
      "explain" => "主页左边文章"
      ),
      array("name" => "主页文章二",
      "id" => $shortname."_rac",
      "type" => "text",
      "std" => "输入分类名",
      "explain" => "主页右边文章"
      ),
      array("name" => "主页文章三(只显示缩略图)",
      "id" => $shortname."_rar",
      "type" => "text",
      "std" => "输入分类名",
      "explain" => "主页右边文章的右边"
      ),

      array("name" => "版权年份",
      "id" => $shortname."_years",
      "std" => "2012",
      "type" => "text",
      "explain" => "它将显示在页面底部"
      ),
      array("name" => "ICP备案号",
      "id" => $shortname."_icp",
      "type" => "text",
      "explain" => "页面底部可以显示 ICP 备案信息，如果网站已备案，在此输入您的备案号，它将显示在页面底部，如果没有请留空"
      ),
      array("name" => "主页分享代码",
      "id" => $shortname."_share",
      "type" => "textarea",
      "css" => "class='h80px'",
      "explain" => "请在此处输入您的分享代码，来自第三方或者您自己的代码，它将显示在文章的结尾处，如果没有请留空<br>第三方分享工具主要有：百度分享、JiaThis、BShare 等等"
      ),
      array("name" => "底部统计代码",
      "id" => $shortname."_tongji",
      "type" => "textarea",
      "css" => "class='h80px'",
      "explain" => "页面底部可以显示第三方统计<br>您可以放一个或者多个统计代码"
      ),
      array("name" => "文章头部广告(940*80)",
      "id" => $shortname."_single-ads-header",
      "type" => "textarea",
      "css" => "class='h60px'",
      "explain" => "建议使用940*80的广告代码"
      ),      
      array("name" => "文章底部广告(940*80)",
      "id" => $shortname."_single-ads-buttom",
      "type" => "textarea",
      "css" => "class='h60px'",
      "explain" => "建议使用940*80的广告代码"
      ),
      array("name" => "文章页面部760*90页广告",
      "id" => $shortname."_download-ads-header",
      "type" => "textarea",
      "css" => "class='h60px'",
      "explain" => "建议使用760*90的广告代码"
      ),
      array("name" => "文章页面底部760*90广告",
      "id" => $shortname."_download-ads-buttom",
      "type" => "textarea",
      "css" => "class='h60px'",
      "explain" => "建议使用760*90的广告代码"
      ),
      array("name" => "侧栏250*250广告",
      "id" => $shortname."_sidebar-ads",
      "type" => "textarea",
      "css" => "class='h60px'",
      "explain" => "建议使用250*250的广告代码"
      ),

      );
      function mytheme_add_admin() {
        global $themename, $shortname, $options;
        if ( $_GET['page'] == basename(__FILE__) ) {
          if ( 'save' == $_REQUEST['action'] ) {
            foreach ($options as $value) {
            update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
            foreach ($options as $value) {
            if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
            header("Location: themes.php?page=functions.php&saved=true");
            die;   //one 
            } else if( 'reset' == $_REQUEST['action'] ) {
            foreach ($options as $value) {
              delete_option( $value['id'] );
              update_option( $value['id'], $value['std'] );
            }
            header("Location: themes.php?page=functions.php&reset=true");
            die;   //two
          }
        }
        add_theme_page($themename." 设置", "$themename 设置", 'edit_themes', basename(__FILE__), 'mytheme_admin');
      }
      function mytheme_admin() {
        global $themename, $shortname, $options;
        if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' 设置已保存。</strong></p></div>';
        if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' 设置已重置。</strong></p></div>';
      ?>
      
      <style type="text/css">
        .wrap h2 {color:#09C;}
        .themeadmin {border:1px dashed #999;margin-top:20px;width:420px;position:10px;}
        .options {margin-top:20px;}
        .options input,.options textarea {padding:2px;border:1px solid;border-color:#666 #CCC #CCC #666;background:#F9F9F9;color:#333;resize:none;width:400px;}
        .options .h80px {height:80px;}
        .options .h60px {height:60px;}
        .options .setup {border-top:1px dotted #CCC;padding:10px 0 10px 10px;overflow:hidden;}
        .options .setup h3 {font-size:14px;margin:0;padding:0;}
        .options .setup .value {float:left;width:410px;}
        .options .setup .explain {float:left;}
      </style>
      <div class="wrap">
        <h2><b><?php echo $themename; ?>主题设置</b></h2>
        <hr />
        <div>主题作者：<a href="http://www.cnsecer.com/2113.html" target="_blank">安全者</a> ¦ 当前版本：<a href="http://www.cnsecer.com/2113.html" title="安全者 2.0" target="_blank">V1.0</a> ¦ 主题介绍、使用帮助及升级请访问：<a href="http://www.cnsecer.com/guestbook/" title="留言板" target="_blank">留言版</a></div>
        <form method="post">
          <div class="options">
            <?php foreach ($options as $value) {
              if ($value['type'] == "text") { ?>
              <div class="setup">
                <h3><?php echo $value['name']; ?></h3>
                <div class="value"><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?>" /></div>
                <div class="explain"><?php echo $value['explain']; ?></div>
              </div>
              <?php } elseif ($value['type'] == "textarea") { ?>
              <div class="setup">
                <h3><?php echo $value['name']; ?></h3>
                <div class="value"><textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" <?php echo $value['css']; ?> ><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?></textarea></div>
                <div class="explain"><?php echo $value['explain']; ?></div>
              </div>
              <?php } elseif ($value['type'] == "select") { ?>
              <div class="setup">
                <h3><?php echo $value['name']; ?></h3>
                <div class="value">
                  <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php foreach ($value['options'] as $option) { ?>
                    <option value="<?php echo $option;?>" <?php if (get_settings( $value['id'] ) == $option) { echo 'selected="selected"'; } ?>>
                      <?php
                        if ((empty($option) || $option == '' ) && isset($value['option'])) {
                          echo $value['option'];
                          } else {
                          echo $option; 
                        }?></option><?php } ?>
                  </select>
                </div>
                <div class="explain"><?php echo $value['explain']; ?></div>
              </div>
            <?php } ?>
            <?php } ?>
          </div>
          <div class="submit">
            <input style="font-size:12px !important;" name="save" type="submit" value="保存设置" class="button-primary" />
            <input type="hidden" name="action" value="save" />
          </div>
        </form>
        
        <form method="post">
          <div style="margin:50px 0;border-top:1px solid #F00;padding-top:10px;">
            <input style="font-size:12px !important;" name="reset" type="submit" value="还原默认设置" />
            <input type="hidden" name="action" value="reset" />
          </div>
        </form>
        
      </div>
      <?php
      }
      add_action('admin_menu', 'mytheme_add_admin');
      add_filter( 'pre_option_link_manager_enabled', '__return_true' ); 
?>
