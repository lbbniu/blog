=== AJAX Comment Pager ===
Contributors: mg12
Donate link: http://www.neoease.com/plugins/
Tags: comments, AJAX, paging
Requires at least: 2.7
Tested up to: 2.7 beta3
Stable tag: 1.0.1

AJAX paging plugin for comment pages in WordPress 2.7 or higher versions.

== Description ==

AJAX paging plugin for comment pages in WordPress 2.7 or higher versions.

为 WordPress 2.7 的评论分页功能增加 AJAX 处理.

**Features:**

* AJAX comment paging
* Without any JavaScript framework

**Demo:**

http://www.neoease.com/themes/

**Supported Languages:**

* US English/en_US (default)
* 简体中文/zh_CN (translate by [mg12](http://www.neoease.com/))

== Installation ==

**The following things are required:**

1. Your WordPress is 2.7 or higher version. (include beta versions)
2. The CUSTOM CALLBACK method has been declared. (Your theme is ready for WordPress 2.7)

**Just follow there simple steps to install this plugin:**

1. Unzip archive to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' tab in WordPress.
3. Append `<span id="cp_post_id"><?php echo $post->ID; ?></span>` to `<?php paginate_comments_links(); ?>` in 'comment.php' file.
4. Goto 'Settings -> AJAX Comment Pager', input and save changes.

**Custom CSS:**

* This plugin will load 'ajax-comment-pager.css' from your theme directory if it exists.
* If it doesn't exists, it will load the default style that comes with this plugin.

**若您要使用该才插件, 请确保满足以下条件:**

1. 您正在使用 WordPress 2.7 或以上版本. (包括 beta 测试版)
2. 您正在使用的主题定义了自定义的评论显示方法. (基本上支持嵌套回复的都会定义该方法)

**您只需以下简单的几个步骤, 就能将插件安装好:**

1. 将压缩包解压到 "/wp-content/plugins/" 目录中.
2. 到 WordPress 后台的 "Plugins" 页面激活该插件.
3. 在 "comments.php" 文件中将 "&lt;span id="cp_post_id"&gt;&lt;?php echo $post-&gt;ID; ?&gt;&lt;/span&gt;" 追加到 "&lt;?php paginate_comments_links(); ?&gt;" 的后面.
4. 到 WordPress 后台的 "Settings -> AJAX Comment Pager" 页面, 输入相关信息和保存设置.

**自定义样式文件**

* 如果主题目录下存在命名为 "ajax-comment-pager.css" 的文件, 插件会它将作为样式文件加载到页面.
* 如果该文件不存在, 主题会将插件自带的作为默认的样式文件, 并加载到页面

== Changelog ==

****

    VERSION DATE       TYPE   CHANGES
    1.0.1   2008/11/28 NEW    Added Simplified Chinese language support.
    1.0     2008/11/25 NEW    Create this plugin.