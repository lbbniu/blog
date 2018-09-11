<?php
# Theme Name: Yeti
# Author: Blueria
function setConfigValue($var, $value)
{
    $file = TEMPLATEPATH . '/config.php';
    $config = file_get_contents($file);
    if (!is_serialized($value)) {
        $value = maybe_serialize($value);
    }
    $new = preg_replace(('/' . preg_quote("\${$var} = <<<VALUE")) . '.*VALUE;/sU', "\${$var} = <<<VALUE\n{$value}\nVALUE;", $config);
    if ($new == $config) {
        $f = fopen($file, 'a');
        $ok = fwrite($f, "\n\${$var} = <<<VALUE\n{$value}\nVALUE;\n");
        fclose($f);
        return $ok ? true : false;
    } else {
        $ok = file_put_contents($file, $new);
        return $ok ? true : false;
    }
}
function blueria_get_option($option, $default = false)
{
    $option = isset($GLOBALS[$option]) ? $GLOBALS[$option] : $default;
    $option = maybe_unserialize($option);
    return $option;
}
function get_term_taxonomy_id_by_post_format($format)
{
    $term = get_term_by('slug', 'post-format-' . $format, 'post_format');
    return $term->term_taxonomy_id;
}
function template_url()
{
    $HOST = $_SERVER['HTTP_HOST'];
    $template_url = get_bloginfo('template_url');
    echo preg_replace('#http\\:\\/\\/[\\w\\-\\.]+\\/#', "http://{$HOST}/", $template_url);
}
function blueria_comment_author_link($comment_ID = 0)
{
    $url = get_comment_author_url($comment_ID);
    $author = get_comment_author($comment_ID);
    if (empty($url) || 'http://' == $url) {
        $return = $author;
    } else {
        $return = "<a href='{$url}' rel='nofollow external' target='_blank'>{$author}</a>";
    }
    return apply_filters('get_comment_author_link', $return);
}
function yeti_theme_comment($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    extract($args, EXTR_SKIP);
    global $commentcount;
    if (!$commentcount) {
        $page = get_query_var('cpage') - 1;
        $cpp = get_query_var('comments_per_page');
        $commentcount = $cpp * $page;
    }
    echo "<li class='comment_bar clearfix' id='comment-{$comment->comment_ID}'>";
    echo '<div class=\'floor\'>';
    echo ++$commentcount . ' 楼';
    echo '</div>';
    $avatar_url = get_comment_meta($comment->comment_ID, 'avatar', true);
    if (!empty($avatar_url)) {
        $avatar = ('<img class="avatar avatar-20 photo" width="20" height="20" src="' . $avatar_url) . '" alt="">';
    } else {
        $avatar = get_avatar($comment, $args['avatar_size']);
    }
    echo "<div class='avatar_wrap'>{$avatar}</div>";
    echo ('<div class=\'comment_author\'>' . blueria_comment_author_link($comment->comment_ID)) . '</div>';
    echo $comment->comment_approved == '0' ? '<div class=\'moderation\'>你的评论正在等待审核 ...</div>' : get_comment_text();
    edit_comment_link('[编辑]', '　', '');
    echo '</li>';
}
if (function_exists('register_sidebar')) {
    register_sidebar(array('name' => '首页侧边栏', 'id' => 'home_sidebar'));
    register_sidebar(array('name' => '文章侧边栏', 'id' => 'post_sidebar'));
    register_sidebar(array('name' => '页面侧边栏', 'id' => 'page_sidebar'));
    register_sidebar(array('name' => '文章主体左侧栏', 'id' => 'post_left_column', 'description' => '显示在文章页里 紧跟在视频/图片的下方'));
    if ($theme_tpl == 'nice') {
        register_sidebar(array('name' => '文章主体右侧栏(不可用)', 'id' => 'post_right_column', 'description' => '当前布局不可用'));
        register_sidebar(array('name' => '首页主体右侧栏(不可用)', 'id' => 'home_right_column', 'description' => '当前布局不可用'));
    } else {
        register_sidebar(array('name' => '文章主体右侧栏', 'id' => 'post_right_column', 'description' => '显示在文章页里 在视频/图片的右侧显示'));
        register_sidebar(array('name' => '首页主体右侧栏', 'id' => 'home_right_column'));
    }
    register_sidebar(array('name' => '脚本栏位', 'id' => 'script_column', 'description' => 'JS脚本的引入/自定义脚本等都可以在这里添加 这里的内容将会放在网页的最后面并隐藏'));
}
class Blueria_Widget_Rising extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_posts_rising', 'description' => '显示近期人气有所攀升的文章');
        $control_ops = array('width' => 250, 'height' => 300);
        parent::__construct(false, '人气趋势[Yeti]', $widget_ops, $control_ops);
    }
    private $last_day = 0;
    public function filter_where($where = '')
    {
        $where .= " AND TO_DAYS('{$this->last_day}') - TO_DAYS(post_date) <= 7 ";
        return $where;
    }
    public function widget($args, $instance)
    {
        extract($args);
        $num = isset($instance['number']) ? $instance['number'] : 5;
        $title = apply_filters('widget_title', empty($instance['title']) ? '人气趋势' : $instance['title'], $instance, $this->id_base);
        global $wpdb;
        $last_day = $wpdb->get_var("SELECT post_date FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' AND post_password = '' ORDER BY post_date DESC LIMIT 1");
        $this->last_day = $last_day ? $last_day : date('Y-m-d H:i:s');
        $tax_query = array(array('taxonomy' => 'post_format', 'field' => 'slug', 'terms' => array('post-format-image', 'post-format-video'), 'operator' => 'IN'));
        $query_array = array('paged' => 1, 'posts_per_page' => $num, 'tax_query' => $tax_query, 'orderby' => 'meta_value_num', 'meta_key' => 'views');
        add_filter('posts_where', array($this, 'filter_where'));
        $most_viewed_query = new WP_Query($query_array);
        $most_viewed = $most_viewed_query->posts;
        remove_filter('posts_where', array($this, 'filter_where'));
        if ($most_viewed) {
            shuffle($most_viewed);
            echo $before_widget;
            echo ($before_title . $title) . $after_title;
            echo '<ul>';
            foreach ($most_viewed as $post) {
                $media_type = get_post_format($post->ID);
                if ($media_type == 'video') {
                    $thumb = get_post_meta($post->ID, 'img_thumb', true);
                } elseif ($media_type == 'image') {
                    $imgurl = get_post_meta($post->ID, 'imgurl', true);
                    if (!empty($imgurl)) {
                        $thumb = (get_bloginfo('template_url') . '/TimThumb.php?w=80&h=60&src=') . $imgurl;
                    } else {
                        $img = wp_get_attachment_image_src(get_post_meta($post->ID, 'pic', true));
                        $thumb = $img[0];
                    }
                }
                $content = strip_tags($post->post_content);
                $chars = 25;
                if (mb_strlen($content) > $chars) {
                    $content = mb_substr($content, 0, $chars, 'UTF-8') . '(更多)';
                }
                $view = get_post_meta($post->ID, 'views', true);
                $view = $view ? $view : 0;
                $vote = get_post_meta($post->ID, 'blueria_vote', true);
                $vote = $vote ? $vote : 0;
                $url = get_permalink($post);
                echo "<li><div class='post_item clearfix'><a href='{$url}'>";
                echo "<div class='thumb'><img src='{$thumb}' width='80' height='60' /></div>";
                echo "<div class='right'><div class='excerpt'>{$content}</div><div class='data'><span class='vote'>{$vote}</span></div></div>";
                echo '</a></div></li>';
            }
            echo '</ul>';
            echo $after_widget;
        }
    }
    public function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $title_field_id = $this->get_field_id('title');
        $title_field_name = $this->get_field_name('title');
        $number_field_id = $this->get_field_id('number');
        $number_field_name = $this->get_field_name('number');
        echo "<p><label for='{$title_field_id}'>标题: <input type='text' id='{$title_field_id}' name='{$title_field_name}' value='{$title}' style='width:172px;' /></label></p>";
        echo "<p><label for='{$number_field_id}'>显示文章数: <input type='text' id='{$number_field_id}' name='{$number_field_name}' value='{$number}' /></label></p>";
    }
}
class Blueria_Widget_Recommend extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_posts_recommend', 'description' => '推荐1个月内的几篇文章给访客');
        parent::__construct(false, '诚意推荐[Yeti]', $widget_ops);
    }
    public function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $title_field_id = $this->get_field_id('title');
        $title_field_name = $this->get_field_name('title');
        $number_field_id = $this->get_field_id('number');
        $number_field_name = $this->get_field_name('number');
        echo "<p><label for='{$title_field_id}'>标题: <input type='text' id='{$title_field_id}' name='{$title_field_name}' value='{$title}' style='width:172px;' /></label></p>";
        echo "<p><label for='{$number_field_id}'>显示文章数: <input type='text' id='{$number_field_id}' name='{$number_field_name}' value='{$number}' style='width:95px;' /> ≤15篇</label></p>";
    }
    public function filter_where($where = '')
    {
        $where .= ' AND TO_DAYS(NOW()) - TO_DAYS(post_date) <= 30 ';
        return $where;
    }
    public function widget($args, $instance)
    {
        extract($args);
        $num = isset($instance['number']) ? $instance['number'] : 5;
        $title = apply_filters('widget_title', empty($instance['title']) ? '诚意推荐' : $instance['title'], $instance, $this->id_base);
        $tax_query = array(array('taxonomy' => 'post_format', 'field' => 'slug', 'terms' => array('post-format-image', 'post-format-video'), 'operator' => 'IN'));
        $query_array = array('paged' => 1, 'posts_per_page' => 30, 'tax_query' => $tax_query, 'orderby' => 'meta_value_num', 'meta_key' => 'blueria_vote');
        add_filter('posts_where', array($this, 'filter_where'));
        $most_up_query = new WP_Query($query_array);
        $most_up = $most_up_query->posts;
        remove_filter('posts_where', array($this, 'filter_where'));
        if ($most_up) {
            shuffle($most_up);
            echo $before_widget;
            echo ($before_title . $title) . $after_title;
            echo '<ul>';
            $count = 1;
            foreach ($most_up as $post) {
                $media_type = get_post_format($post->ID);
                if ($media_type == 'video') {
                    $thumb = get_post_meta($post->ID, 'img_thumb', true);
                } elseif ($media_type == 'image') {
                    $imgurl = get_post_meta($post->ID, 'imgurl', true);
                    if (!empty($imgurl)) {
                        $thumb = (get_bloginfo('template_url') . '/TimThumb.php?w=80&h=60&src=') . $imgurl;
                    } else {
                        $img = wp_get_attachment_image_src(get_post_meta($post->ID, 'pic', true));
                        $thumb = $img[0];
                    }
                }
                $content = strip_tags($post->post_content);
                $chars = 25;
                if (mb_strlen($content) > $chars) {
                    $content = mb_substr($content, 0, $chars, 'UTF-8') . '(更多)';
                }
                $view = get_post_meta($post->ID, 'views', true);
                $view = $view ? $view : 0;
                $vote = get_post_meta($post->ID, 'blueria_vote', true);
                $vote = $vote ? $vote : 0;
                $url = get_permalink($post);
                echo "<li><div class='post_item clearfix'><a href='{$url}'>";
                echo "<div class='thumb'><img src='{$thumb}' width='80' height='60' /></div>";
                echo "<div class='right'><div class='excerpt'>{$content}</div><div class='data'><span class='vote'>{$vote}</span></div></div>";
                echo '</a></div></li>';
                $count++;
                if ($count > $num) {
                    break;
                }
            }
            echo '</ul>';
            echo $after_widget;
        }
    }
}
class Blueria_Widget_OldPosts extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_posts_oldposts', 'description' => '随机显示过去的文章推荐给访客');
        parent::__construct(false, '随机推荐[Yeti]', $widget_ops);
    }
    public function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 6;
        $ago = isset($instance['ago']) ? absint($instance['ago']) : 7;
        $title_field_id = $this->get_field_id('title');
        $title_field_name = $this->get_field_name('title');
        $number_field_id = $this->get_field_id('number');
        $number_field_name = $this->get_field_name('number');
        $ago_field_id = $this->get_field_id('ago');
        $ago_field_name = $this->get_field_name('ago');
        echo "<p><label for='{$title_field_id}'>标题: <input type='text' id='{$title_field_id}' name='{$title_field_name}' value='{$title}' style='width:172px;' /></label></p>";
        echo "<p><label for='{$ago_field_id}'>随机显示<input type='text' id='{$ago_field_id}' name='{$ago_field_name}' value='{$ago}' style='width:25px;' />天前的文章</label></p>";
        echo "<p><label for='{$number_field_id}'>显示文章数: <input type='text' id='{$number_field_id}' name='{$number_field_name}' value='{$number}' style='width:95px;' /></label></p>";
    }
    private $ago = 0;
    public function filter_where($where = '')
    {
        $where .= " AND TO_DAYS(NOW()) - TO_DAYS(post_date) > {$this->ago} ";
        return $where;
    }
    public function widget($args, $instance)
    {
        extract($args);
        $title = isset($instance['title']) && !empty($instance['title']) ? $instance['title'] : '随机推荐';
        $num = isset($instance['number']) ? $instance['number'] : 6;
        $ago = isset($instance['ago']) ? $instance['ago'] : 7;
        $this->ago = $ago;
        $tax_query = array(array('taxonomy' => 'post_format', 'field' => 'slug', 'terms' => array('post-format-image', 'post-format-video'), 'operator' => 'IN'));
        $query_array = array('paged' => 1, 'posts_per_page' => $num, 'tax_query' => $tax_query, 'orderby' => 'rand');
        add_filter('posts_where', array($this, 'filter_where'));
        $old_posts_query = new WP_Query($query_array);
        $old_posts = $old_posts_query->posts;
        remove_filter('posts_where', array($this, 'filter_where'));
        if ($old_posts) {
            echo $before_widget;
            echo ($before_title . $title) . $after_title;
            echo '<ul>';
            foreach ($old_posts as $post) {
                $media_type = get_post_format($post->ID);
                if ($media_type == 'video') {
                    $thumb = get_post_meta($post->ID, 'img_thumb', true);
                } elseif ($media_type == 'image') {
                    $imgurl = get_post_meta($post->ID, 'imgurl', true);
                    if (!empty($imgurl)) {
                        $thumb = (get_bloginfo('template_url') . '/TimThumb.php?w=80&h=60&src=') . $imgurl;
                    } else {
                        $img = wp_get_attachment_image_src(get_post_meta($post->ID, 'pic', true));
                        $thumb = $img[0];
                    }
                }
                $content = strip_tags($post->post_content);
                $chars = 25;
                if (mb_strlen($content) > $chars) {
                    $content = mb_substr($content, 0, $chars, 'UTF-8') . '(更多)';
                }
                $view = get_post_meta($post->ID, 'views', true);
                $view = $view ? $view : 0;
                $vote = get_post_meta($post->ID, 'blueria_vote', true);
                $vote = $vote ? $vote : 0;
                $url = get_permalink($post);
                echo "<li><div class='post_item clearfix'><a href='{$url}'>";
                echo "<div class='thumb'><img src='{$thumb}' width='80' height='60' /></div>";
                echo "<div class='right'><div class='excerpt'>{$content}</div><div class='data'><span class='vote'>{$vote}</span></div></div>";
                echo '</a></div></li>';
            }
            echo '</ul>';
            echo $after_widget;
        }
    }
}
class Blueria_Widget_Float extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_float_wrap', 'description' => '在你要浮动的侧边栏区块前后各插入一个这个 就可以使你的区块浮动起来 一个[开始] 一个[结束] 请正确设置');
        parent::__construct(false, '区块浮动[Yeti]', $widget_ops);
    }
    public function form($instance)
    {
        $state = isset($instance['state']) ? esc_attr($instance['state']) : 'start';
        if ($state == 'start') {
            $title = '浮动包裹开始';
        }
        if ($state == 'end') {
            $title = '浮动包裹结束';
        }
        echo ((('<input id=\'' . $this->get_field_id('title')) . '\' type=\'hidden\' value=\'') . $title) . '\' />';
        echo ((((((('<p>浮动包裹: <input name=\'' . $this->get_field_name('state')) . '\' type=\'radio\' value=\'start\'') . checked('start', $state, 0)) . ' />开始 <input name=\'') . $this->get_field_name('state')) . '\' type=\'radio\' value=\'end\'') . checked('end', $state, 0)) . ' />结束</p>';
    }
    public function widget($args, $instance)
    {
        extract($args);
        if ($instance['state'] == 'start') {
            echo '<li class=\'floatStart\'><ul>';
        }
        if ($instance['state'] == 'end') {
            echo '</ul></li>';
        }
    }
}
class Blueria_widget_RichText extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_rt', 'description' => '强化原来的[文本]小工具');
        $control_ops = array('width' => 400, 'height' => 300);
        parent::__construct(false, '富文本[Yeti]', $widget_ops, $control_ops);
    }
    public function form($instance)
    {
        $instance = wp_parse_args((array) $instance, array('title' => '此处留名', 'showtitle' => '', 'code' => '', 'width' => '0', 'height' => '250', 'class' => '', 'nobg' => '', 'style' => ''));
        $title = strip_tags($instance['title']);
        $showtitle = strip_tags($instance['showtitle']);
        $code = esc_textarea($instance['code']);
        $width = absint($instance['width']);
        $height = absint($instance['height']);
        $class = strip_tags($instance['class']);
        $nobg = strip_tags($instance['nobg']);
        $style = strip_tags($instance['style']);
        echo ('<p><label for=' . $this->get_field_id('title')) . '>工具名：</label>';
        echo ((('<input class=\'widefat\' id=' . $this->get_field_id('title')) . ' name=') . $this->get_field_name('title')) . " type='text' value='{$title}' /></p>";
        echo ('<p><label for=' . $this->get_field_id('showtitle')) . '>显示标题：</label>';
        echo ((('<input class=\'widefat\' id=' . $this->get_field_id('showtitle')) . ' name=') . $this->get_field_name('showtitle')) . " type='text' value='{$showtitle}' /></p>";
        $editor_settings = array('textarea_name' => $this->get_field_name('code'), 'tinymce' => 0, 'media_buttons' => 0, 'textarea_rows' => 12, 'editor_class' => 'textareastyle', 'quicktags' => array('buttons' => true));
        wp_editor($code, $this->get_field_id('code'), $editor_settings);
        echo '<p style=\'margin-top:5px;\'>';
        echo ('<label for=' . $this->get_field_id('width')) . '>宽度：</label>';
        echo ((('<input class=\'widefat\' id=' . $this->get_field_id('width')) . ' name=') . $this->get_field_name('width')) . " type='text' value='{$width}' style='width:50px' />";
        echo ('<label for=' . $this->get_field_id('height')) . '> 高度：</label>';
        echo ((('<input class=\'widefat\' id=' . $this->get_field_id('height')) . ' name=') . $this->get_field_name('height')) . " type='text' value='{$height}' style='width:50px' />";
        echo ('<label for=' . $this->get_field_id('class')) . '> class：</label>';
        echo ((('<input class=\'widefat\' id=' . $this->get_field_id('class')) . ' name=') . $this->get_field_name('class')) . " type='text' value='{$class}' style='width:100px' />";
        echo '</p>';
        echo ((((((('<p><input type=\'checkbox\' id=' . $this->get_field_id('nobg')) . ' name=') . $this->get_field_name('nobg')) . ' ') . checked('on', $nobg, false)) . ' /><label for=') . $this->get_field_id('nobg')) . '> 无背景和边框</label></p>';
        echo ('<p><label for=' . $this->get_field_id('style')) . '>style = </label>';
        echo ((('<input class=\'widefat\' id=' . $this->get_field_id('style')) . ' name=') . $this->get_field_name('style')) . " type='text' value='{$style}' style='width:80%' placeholder='CSS行样式' /></p>";
    }
    public function widget($args, $instance)
    {
        extract($args);
        $title = isset($instance['showtitle']) ? $instance['showtitle'] : '';
        $code = $instance['code'] ? $instance['code'] : '';
        $width = isset($instance['width']) ? (int) $instance['width'] : '';
        $height = isset($instance['height']) ? (int) $instance['height'] : '';
        $class = isset($instance['class']) ? $instance['class'] : '';
        $nobg = isset($instance['nobg']) ? $instance['nobg'] : '';
        $style = isset($instance['style']) ? $instance['style'] : '';
        $code = do_shortcode($code);
        if (!empty($title)) {
            echo (($before_widget . $before_title) . $title) . $after_title;
            echo "<div class='textwidget'>{$code}</div>";
            echo $after_widget;
        } else {
            $width = $width ? "width:{$width}px;" : '';
            $height = $height ? "height:{$height}px;" : '';
            $nobg = $nobg == 'on' ? 'background:0 none;box-shadow:none;border-radius:0;' : '';
            $style = $style ? "{$style}" : '';
            $class = !empty($class) ? ' ' . $class : '';
            echo "<li class='richtext{$class}' style='{$width}{$height}{$nobg}{$style}'>{$code}</li>";
        }
    }
}
class Blueria_Widget_Visitors extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_visitors', 'description' => '最近访客(按行设置 每行显示5个访客)');
        parent::__construct(false, '最近访客[Yeti]', $widget_ops);
    }
    public function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $row = isset($instance['row']) ? absint($instance['row']) : 1;
        $title_field_id = $this->get_field_id('title');
        $title_field_name = $this->get_field_name('title');
        echo "<p><label for='{$title_field_id}'>显示标题:<label><input id='{$title_field_id}' name='{$title_field_name}' type='text' value='{$title}' style='width:100%;' /></p>";
        echo ('<p>显示<input name=\'' . $this->get_field_name('row')) . "' type='text' value='{$row}' style='width:25px;' />行最近访客</p>";
    }
    public function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '最近访客' : $instance['title'], $instance, $this->id_base);
        $row = $instance['row'] ? $instance['row'] : 1;
        global $theme_tpl;
        if ($theme_tpl == 'default') {
            $visitor_num = $row * 5;
            $div_height = $row * 45;
        } else {
            $visitor_num = $row * 6;
            $div_height = $row * 46;
        }
        echo $before_widget;
        echo ($before_title . $title) . $after_title;
        echo "<ul class='ds-recent-visitors clearfix' data-num-items='{$visitor_num}' data-avatar-size='32' style='height:{$div_height}px;'></ul>";
        echo $after_widget;
    }
}
add_action('widgets_init', 'blueria_widget_init');
function blueria_widget_init()
{
    register_widget('Blueria_Widget_Rising');
    register_widget('Blueria_Widget_Recommend');
    register_widget('Blueria_Widget_OldPosts');
    register_widget('Blueria_Widget_Float');
    register_widget('Blueria_widget_RichText');
    register_widget('Blueria_Widget_Visitors');
    unregister_widget('Duoshuo_Widget_Recent_Visitors');
    unregister_widget('WP_Widget_RSS');
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Archives');
    unregister_widget('WP_Widget_Categories');
    unregister_widget('WP_Widget_Recent_Posts');
    unregister_widget('WP_Widget_Recent_Comments');
    unregister_widget('WP_Widget_Tag_Cloud');
    unregister_widget('WP_Nav_Menu_Widget');
    unregister_widget('WP_Widget_Meta');
    unregister_widget('WP_Widget_Text');
}
add_action('load-widgets.php', 'blueria_quicktags');
function blueria_quicktags()
{
    wp_enqueue_script('blueria_quicktags', get_bloginfo('template_url') . '/js/quicktags.js', array('quicktags'));
}
add_shortcode('php', 'phpcode');
function phpcode($atts = null, $content = null)
{
    ob_start();
    eval($content);
    $html = ob_get_contents();
    ob_clean();
    return $html;
}
function blueria_php($content)
{
    preg_match_all('!\\[php[^\\]]*\\](.*?)\\[/php[^\\]]*\\]!is', $content, $matches);
    $num_matches = count($matches[0]);
    for ($i = 0; $i < $num_matches; $i++) {
        ob_start();
        eval($matches[1][$i]);
        $replacement = ob_get_contents();
        ob_clean();
        $search = quotemeta($matches[0][$i]);
        $search = str_replace('/', '\\' . '/', $search);
        $content = preg_replace("/{$search}/", $replacement, $content, 1);
    }
    return $content;
}
if (!function_exists('media_handle_upload')) {
    require_once ABSPATH . '/wp-admin/includes/media.php';
    require_once ABSPATH . '/wp-admin/includes/file.php';
    require_once ABSPATH . '/wp-admin/includes/image.php';
}
add_action('parse_request', 'createPost');
function createPost()
{
	//if (((isset($_POST['addpost_form']) && $_POST['addpost_form'] == 'send') && !empty($_POST['user-title'])) && !empty($_POST['user-content'])) {   Back_by 晨风 20140324
    if (((isset($_POST['addpost_form']) && $_POST['addpost_form'] == 'send')) && !empty($_POST['user-content'])) { // 删除 && !empty($_POST['user-title'])
        $name = isset($_POST['user-name']) ? trim(htmlspecialchars($_POST['user-name'], ENT_QUOTES)) : '';
        $url = isset($_POST['user-url']) ? trim(htmlspecialchars($_POST['user-url'], ENT_QUOTES)) : '';
        $avatar = isset($_POST['user-avatar']) ? trim(htmlspecialchars($_POST['user-avatar'], ENT_QUOTES)) : '';
        $duoshuo = isset($_POST['user-duoshuo']) ? trim(htmlspecialchars($_POST['user-duoshuo'], ENT_QUOTES)) : '';
        $ip = $_SERVER['REMOTE_ADDR'];
        if ((empty($name) || empty($avatar)) || empty($duoshuo)) {
            die('没有登陆,不能投稿!');
        }
        $title = isset($_POST['user-title']) ? trim(htmlspecialchars($_POST['user-title'], ENT_QUOTES)) : '';
        $content = isset($_POST['user-content']) ? trim(htmlspecialchars($_POST['user-content'], ENT_QUOTES)) : '';
        $media_type = isset($_POST['user-type']) ? trim(htmlspecialchars($_POST['user-type'], ENT_QUOTES)) : 'image';
        $video = isset($_POST['user-video']) ? trim(htmlspecialchars($_POST['user-video'], ENT_QUOTES)) : '';
        $imgurl = isset($_POST['user-image2']) ? trim(htmlspecialchars($_POST['user-image2'], ENT_QUOTES)) : '';
        $multi_image_name = isset($_POST['user-image3']) ? trim(htmlspecialchars($_POST['user-image3'], ENT_QUOTES)) : '';
        if ($media_type == 'video' && empty($video)) {
            urlStatus(false, 2);
        }
        if ('1' == get_option('stop_imageup') && $_FILES['user-image']['size']) {
            urlStatus(false, 3);
        }
        $post_audit = get_option('post_audit');
        if ($post_audit == '1') {
            $post_status = 'pending';
        }
        if ($post_audit == '0') {
            $post_status = 'publish';
        }
        if ($media_type == 'image') {
            if ((!$imgurl && !$multi_image_name) && !$_FILES['user-image']['size']) {
                $post_format = 'post-format-aside';
                $media_cid = blueria_cat_id('纯文字', 'aside');
            } else {
                $post_format = 'post-format-image';
                $media_cid = blueria_cat_id('图片', 'image');
            }
        }
        if ($media_type == 'video') {
            $post_format = 'post-format-video';
            $media_cid = blueria_cat_id('视频', 'video');
        }
        $cats = @$_POST['cats'] ? (array) $_POST['cats'] : false;
        $tags = @$_POST['tags'] ? (array) $_POST['tags'] : false;
        $tougao = array('post_title' => $title, 'post_content' => $content, 'post_author' => 0, 'post_status' => $post_status, 'post_category' => array($media_cid));
        $postID = wp_insert_post($tougao);
        if ($postID) {
            wp_set_object_terms($postID, $post_format, 'post_format');
            function arrayItemToInt(&$value, $key)
            {
                $value = (int) $value;
            }
            if ($cats) {
                array_walk($cats, 'arrayItemToInt');
                wp_set_object_terms($postID, $cats, 'category');
            }
            if ($tags) {
                array_walk($tags, 'arrayItemToInt');
                wp_set_object_terms($postID, $tags, 'post_tag');
            }
            if ($media_type == 'image' && (($imgurl || $multi_image_name) || $_FILES['user-image']['size'])) {
                $wp_upload_dir = wp_upload_dir();
                $multi_image_path = !empty($multi_image_name) ? ($wp_upload_dir['path'] . '/') . $multi_image_name : '';
                if ($imgurl) {
                    add_post_meta($postID, 'imgurl', $imgurl);
                    $img = getImgMeta($imgurl);
                    add_post_meta($postID, 'img_w', $img[0]);
                    add_post_meta($postID, 'img_h', $img[1]);
                    add_post_meta($postID, 'img_type', $img[2]);
                    blueria_del_file($multi_image_path);
                } else {
                    if (!empty($multi_image_path)) {
                        $attachmentId = wp_insert_img($multi_image_name, $postID);
                    } else {
                        $attachmentId = media_handle_upload('user-image', $postID);
                        blueria_del_file($multi_image_path);
                    }
                    if (!is_wp_error($attachmentId) && wp_attachment_is_image($attachmentId)) {
                        add_post_meta($postID, 'pic', $attachmentId);
                    } else {
                        wp_delete_attachment($attachmentId);
                    }
                }
            } elseif ($media_type == 'video' && $video != '') {
                $vdo = new VideoUrlParser($video);
                add_post_meta($postID, 'videourl', $vdo->swf);
                if ($vdo->img) {
                    add_post_meta($postID, 'img', $vdo->img);
                    add_post_meta($postID, 'img_thumb', $vdo->img_thumb);
                }
            }
            add_post_meta($postID, 'username', $name);
            add_post_meta($postID, 'url', $url);
            add_post_meta($postID, 'avatar', $avatar);
            add_post_meta($postID, 'duoshuo', $duoshuo);
            add_post_meta($postID, 'ip', $ip);
            if ($post_audit == '0') {
                $success = 1;
            }
            if ($post_audit == '1') {
                $success = 2;
            }
            urlStatus(true, $success);
        } else {
            urlStatus(false, 1);
        }
    } else {
		//if (isset($_POST['addpost_form']) && (empty($_POST['user-title']) || empty($_POST['user-content']))) {  Back_by 晨风 20140324
        if (isset($_POST['addpost_form']) && (empty($_POST['user-content']))) { // 删除 empty($_POST['user-title']) ||
            urlStatus(false, 1);
        }
    }
}
function urlStatus($is_success, $status_code)
{
    $redirect = stripslashes($_SERVER['REQUEST_URI']);
    if ($is_success) {
        $redirect = remove_query_arg('error', $redirect);
        $redirect = add_query_arg(array('success' => $status_code), $redirect);
    } else {
        $redirect = remove_query_arg('success', $redirect);
        $redirect = add_query_arg(array('error' => $status_code), $redirect);
    }
    wp_redirect($redirect);
    die;
}
function wp_insert_img($file_name, $post_id)
{
    $wp_upload_dir = wp_upload_dir();
    $file_path = ($wp_upload_dir['path'] . '/') . $file_name;
    $file_url = ($wp_upload_dir['url'] . '/') . $file_name;
    $info = pathinfo($file_name);
    $title = $info['filename'];
    $attachment = array('post_mime_type' => 'image/jpeg', 'guid' => $file_url, 'post_parent' => $post_id, 'post_title' => $title, 'post_content' => '');
    $id = wp_insert_attachment($attachment, $file_path, $post_id);
    if (!is_wp_error($id)) {
        wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $file_path));
    }
    return $id;
}
add_action('wp_ajax_blueria_delTempImg', 'delTempImg');
add_action('wp_ajax_nopriv_blueria_delTempImg', 'delTempImg');
function delTempImg()
{
    global $wpdb;
    if (!isset($_POST['file_name']) || empty($_POST['file_name'])) {
        wp_die(0);
    }
    $file_name = trim(htmlspecialchars($_POST['file_name'], ENT_QUOTES));
    $info = pathinfo($file_name);
    $title = $info['filename'];
    $id_exist = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_title = '{$title}' AND post_type = 'attachment';");
    if ($id_exist) {
        wp_die(0);
    }
    $wp_upload_dir = wp_upload_dir();
    $file_path = ($wp_upload_dir['path'] . '/') . $file_name;
    blueria_del_file($file_path);
    echo $file_name;
    wp_die();
}
add_action('the_content', 'original_content', 0);
function original_content($content)
{
    global $post;
    if (is_page()) {
        return $content;
    }
    $src = blueria_get_post_thumb_src();
    $alt = $post->post_title;
    $thumb = "<img src='{$src}' alt='{$alt}' class='hidden' />\n";
    $link = get_permalink($post->ID);
    $chars = 400;
    $content2 = strip_tags($content);
    if (mb_strlen($content2) > $chars) {
        if (!is_single()) {
            $content = mb_substr($content2, 0, $chars, 'UTF-8') . "<a class='more' href='{$link}' target='_blank'>......(查看更多)</a>";
        }
    }
    $content = wpautop($content);
    return (($thumb . '<div class=\'article\'>') . $content) . '</div>';
}
function pagination($query_array)
{
    global $posts_per_page, $paged;
    $query_array = array_merge($query_array, array('posts_per_page' => -1));
    $my_query = new WP_Query($query_array);
    $total_posts = $my_query->post_count;
    if (empty($paged)) {
        $paged = 1;
    }
    $prev = $paged - 1;
    $next = $paged + 1;
    $range = 2;
    $showitems = $range * 2 + 1;
    $pages = ceil($total_posts / $posts_per_page);
    if (1 != $pages) {
        echo '<div class=\'pagination clearfix\'>';
        echo $paged > 1 && $pages > $showitems ? ('<a href=\'' . get_pagenum_link($prev)) . '\'>上一页</a>' : '';
        echo $paged > $range + 1 ? ('<a href=\'' . get_pagenum_link(1)) . '\'>1</a>' : '';
        echo $paged > $range + 2 ? '<span>...</span>' : '';
        for ($i = 1; $i <= $pages; $i++) {
            if (1 != $pages && (!($i >= ($paged + $range) + 1 || $i <= ($paged - $range) - 1) || $pages <= $showitems)) {
                echo $paged == $i ? ('<span class=\'current\'>' . $i) . '</span>' : ((('<a href=\'' . get_pagenum_link($i)) . '\' >') . $i) . '</a>';
            }
        }
        echo $paged < ($pages - $range) - 1 && $pages > $showitems ? '<span>...</span>' : '';
        echo $paged < $pages - $range && $pages > $showitems ? ('<a href=\'' . get_pagenum_link($pages)) . "'>{$pages}</a>" : '';
        echo $paged < $pages && $pages > $showitems ? ('<a href=\'' . get_pagenum_link($next)) . '\'>下一页</a>' : '';
        echo '</div>
';
    }
}
add_action('wp_ajax_blueria_sittings', 'blueria_sittings');
function blueria_sittings()
{
    global $wpdb;
    if (!isset($_POST['type']) || !isset($_POST['key'])) {
        wp_die(0);
    }
    $type =& $_POST['type'] ? $_POST['type'] : 0;
    if (!$type) {
        wp_die(-1);
    }
    $key =& $_POST['key'];
    if ($type == 'post_audit') {
        $value = !(!$key) ? '1' : '0';
        $post_audit = get_option('post_audit', 'no_exists');
        if ($post_audit == 'no_exists') {
            add_option('post_audit', $value);
        } else {
            update_option('post_audit', $value);
        }
        $post_audit = get_option('post_audit');
        if ($post_audit == '1') {
            echo 'yes';
        }
        if ($post_audit == '0') {
            echo 'no';
        }
    }
    if ($type == 'stop_external_link') {
        $value = !(!$key) ? '1' : '0';
        setConfigValue('stop_external_link', $value);
        $stop_external_link = $value;
        if ($stop_external_link == '1') {
            echo 'yes';
        }
        if ($stop_external_link == '0') {
            echo 'no';
        }
    }
    if ($type == 'stop_imageup') {
        $value = !(!$key) ? '1' : '0';
        $stop_imageup = get_option('stop_imageup', 'no_exists');
        if ($stop_imageup == 'no_exists') {
            add_option('stop_imageup', $value);
        } else {
            update_option('stop_imageup', $value);
        }
        $stop_imageup = get_option('stop_imageup');
        if ($stop_imageup == '1') {
            echo 'yes';
        }
        if ($stop_imageup == '0') {
            echo 'no';
        }
    }
    if ($type == 'as_radio') {
        $value = !(!$key) ? '1' : '0';
        $as_radio = get_option('as_radio', 'no_exists');
        if ($as_radio == 'no_exists') {
            add_option('as_radio', $value);
        } else {
            update_option('as_radio', $value);
        }
        $as_radio = get_option('as_radio');
        if ($as_radio == '1') {
            echo 'yes';
        }
        if ($as_radio == '0') {
            echo 'no';
        }
    }
    if ($type == 'stop_ietips') {
        $value = !(!$key) ? '1' : '0';
        setConfigValue('stop_ietips', $value);
        $stop_ietips = $value;
        if ($stop_ietips == '1') {
            echo 'yes';
        }
        if ($stop_ietips == '0') {
            echo 'no';
        }
    }
    if ($type == 'show_links') {
        $value = !(!$key) ? '1' : '0';
        setConfigValue('show_links', $value);
        $show_links = $value;
        if ($show_links == '1') {
            echo 'yes';
        }
        if ($show_links == '0') {
            echo 'no';
        }
    }
    if ($type == 'option_save') {
        parse_str($key, $data);
        setConfigValue('site_description', strip_tags($data['site_description']));
        setConfigValue('site_keywords', strip_tags($data['site_keywords']));
        setConfigValue('site_copyright', stripslashes($data['site_copyright']));
        setConfigValue('site_statistics', stripslashes($data['site_statistics']));
        setConfigValue('site_notice', stripslashes($data['site_notice']));
        echo 'ok';
    }
    if ($type == 'theme_tpl_save') {
        parse_str($key, $data);
        setConfigValue('theme_tpl', $data['theme_tpl']);
        echo 'ok';
    }
    if ($type == 'bdshare_options_save') {
        parse_str($key, $data);
        $options = array();
        $options['id'] = trim(strip_tags($data['bdshare_id']));
        $options['tsina'] = trim(strip_tags($data['bdshare_tsina']));
        $options['tqq'] = trim(strip_tags($data['bdshare_tqq']));
        $options['t163'] = trim(strip_tags($data['bdshare_t163']));
        $options['tsohu'] = trim(strip_tags($data['bdshare_tsohu']));
        setConfigValue('bdshare_options', $options);
        echo 'ok';
    }
    if ($type == 'related_options_save') {
        parse_str($key, $data);
        setConfigValue('union56id', trim($data['union56id']));
        setConfigValue('comment_notice', trim($data['comment_notice']));
        setConfigValue('weixin_original_id', trim($data['weixin_original_id']));
        echo 'ok';
    }
    if ($type == 'mobi_ads_settings') {
        parse_str($key, $data);
        setConfigValue('mobi_home_fixed_ad', stripslashes($data['mobi_home_fixed_ad']));
        setConfigValue('mobi_post_top_ad', stripslashes($data['mobi_post_top_ad']));
        setConfigValue('mobi_post_bottom_ad', stripslashes($data['mobi_post_bottom_ad']));
        echo 'ok';
    }
    if ($type == 'tougao_option_save') {
        parse_str($key, $data);
        $tougao_option = get_option('tougao_option', 'no_exists');
        if ($tougao_option == 'no_exists') {
            add_option('tougao_option', $data);
        } else {
            update_option('tougao_option', $data);
        }
        echo 'ok';
    }
    if ($type == 'sitetop_options_save') {
        parse_str($key, $data);
        $options = array();
        $options['height'] = trim(strip_tags($data['sitetop_height']));
        $options['bgimg'] = trim(strip_tags($data['sitetop_bgimg']));
        $options['type'] = trim(strip_tags($data['sitetop_bg_type']));
        $options['bg_color'] = trim(strip_tags($data['sitetop_bg_color']));
        $options['bg_image'] = trim(strip_tags($data['sitetop_bg_image']));
        $options['logo_top'] = trim(strip_tags($data['sitetop_logo_top']));
        $options['logo_left'] = trim(strip_tags($data['sitetop_logo_left']));
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $options['bg_color'])) {
            wp_die(-2);
        }
        setConfigValue('sitetop_options', $options);
        echo 'ok';
    }
    if ($type == 'bcs_option_save') {
        parse_str($key, $data);
        $save_to_bcs_option = strip_tags(@$data['save_to_bcs']) == 'on' ? 1 : 0;
        $save_to_bcs = get_option('save_to_bcs', 'no_exists');
        if ($save_to_bcs == 'no_exists') {
            add_option('save_to_bcs', $save_to_bcs_option);
        } else {
            update_option('save_to_bcs', $save_to_bcs_option);
        }
        $options = array();
        $options['bucket'] = trim(strip_tags($data['bucket']));
        $options['ak'] = trim(strip_tags($data['ak']));
        $options['sk'] = trim(strip_tags($data['sk']));
        $bcs_options = get_option('bcs_options', 'no_exists');
        if ($bcs_options == 'no_exists') {
            add_option('bcs_options', $options);
        } else {
            update_option('bcs_options', $options);
        }
        echo 'ok';
    }
    if ($type == 'image_cut_option') {
        parse_str($key, $data);
        setConfigValue('home_image_cut', $data['home_image_cut']);
        setConfigValue('home_image_cut_height', $data['home_image_cut_height']);
        echo 'ok';
    }
    if ($type == 'wechat_options_save') {
        parse_str($key, $data);
        $options = array();
        $options['welcome'] = trim(strip_tags($data['wechat_welcome']));
        $options['help'] = trim(strip_tags($data['wechat_help']));
        $wechat_options = get_option('wechat_options', 'no_exists');
        if ($wechat_options == 'no_exists') {
            add_option('wechat_options', $options);
        } else {
            update_option('wechat_options', $options);
        }
        echo 'ok';
    }
    if ($type == 'watermark_options_save') {
        parse_str($key, $data);
        $options = array();
        $options['enabled'] = strip_tags(@$data['watermark_enabled']) == 'on' ? 1 : 0;
        $options['type'] = trim(strip_tags($data['watermark_type']));
        $options['filename'] = trim(strip_tags($data['watermark_filename']));
        $options['text'] = trim(strip_tags($data['watermark_text']));
        $options['font'] = trim(strip_tags($data['watermark_font']));
        $options['size'] = trim(strip_tags($data['watermark_size']));
        $options['pos'] = trim(strip_tags($data['watermark_pos']));
        $options['color'] = trim(strip_tags($data['watermark_color']));
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $options['color'])) {
            wp_die(-2);
        }
        $watermark_options = get_option('watermark_options', 'no_exists');
        if ($watermark_options == 'no_exists') {
            add_option('watermark_options', $options);
        } else {
            update_option('watermark_options', $options);
        }
        echo 'ok';
    }
    if ($type == 'init_theme') {
        if ($key != '1') {
            wp_die(-1);
        }
        Init_Sittings::init();
        echo 'ok';
    }
    wp_die();
}
add_action('wp_ajax_blueria_parser_video', 'blueria_parser_video');
function blueria_parser_video()
{
    global $wpdb;
    if (!isset($_POST['url'])) {
        wp_die(0);
    }
    $url = trim(htmlspecialchars($_POST['url'], ENT_QUOTES));
    $vdo = new VideoUrlParser($url);
    if ($vdo->success != 1) {
        echo $vdo->success;
    } else {
        $img = $vdo->img;
        $img_t = $vdo->img_thumb;
        $swf = $vdo->swf;
        echo "['{$img}','{$img_t}','{$swf}']";
    }
    wp_die();
}
add_action('wp_ajax_blueria_parser_image', 'blueria_parser_image');
function blueria_parser_image()
{
    if (!isset($_POST['url'])) {
        wp_die(0);
    }
    $url = trim(htmlspecialchars($_POST['url'], ENT_QUOTES));
    $img = getImgMeta($url);
    if (!$img) {
        echo '-1';
    } else {
        echo "['{$img[0]}','{$img[1]}']";
    }
    wp_die();
}