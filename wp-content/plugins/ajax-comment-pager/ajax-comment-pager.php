<?php
/*
Plugin Name: AJAX Comment Pager
Plugin URI: http://wordpress.org/extend/plugins/ajax-comment-pager/
Plugin Description: AJAX paging plugin for comment pages in WordPress 2.7 or higher versions.
Version: 1.0.1
Author: mg12
Author URI: http://www.neoease.com/
*/

/** l10n */
load_plugin_textdomain('ajax-comment-pager', "/wp-content/plugins/ajax-comment-pager/languages/");

/** options */
class AJAXCommentPagerOptions {

	function getOptions() {
		$options = get_option('ajax_comment_pager_options');
		if (!is_array($options)) {
			$options['comments_id'] = '';
			$options['callback'] = '';
			$options['type'] = 1;
			update_option('ajax_comment_pager_options', $options);
		}
		return $options;
	}

	function add() {
		if(isset($_POST['ajax_comment_pager_save'])) {
			$options = AJAXCommentPagerOptions::getOptions();

			$options['comments_id'] = stripslashes($_POST['comments_id']);
			$options['callback'] = stripslashes($_POST['callback']);
			$options['type'] = $_POST['type'];

			update_option('ajax_comment_pager_options', $options);

		} else {
			AJAXCommentPagerOptions::getOptions();
		}

		add_options_page('AJAX Comment Pager', 'AJAX Comment Pager', 10, basename(__FILE__), array('AJAXCommentPagerOptions', 'display'));
	}

	function display() {
		$options = AJAXCommentPagerOptions::getOptions();
?>

<form action="#" method="post" enctype="multipart/form-data" name="ajax_comment_pager_form" id="ajax_comment_pager_form">
	<div class="wrap">
		<h2><?php _e('AJAX Comment Pager', 'ajax-comment-pager'); ?></h2>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Comment list ID', 'ajax-comment-pager'); ?></th>
					<td>
						<input type="text" name="comments_id" id="comments_id" class="code" size="30" value="<?php echo($options['comments_id']); ?>"> *
						<br/>
						<span class="setting-description"><?php _e('This ID come from the parent of comment element nodes.', 'ajax-comment-pager'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Callback method name', 'ajax-comment-pager'); ?></th>
					<td>
						<input type="text" name="callback" id="callback" class="code" size="30" value="<?php echo($options['callback']); ?>"> *
						<br/>
						<span class="setting-description"><?php _e('The callback parameter of wp_list_comments method.', 'ajax-comment-pager'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Type', 'ajax-comment-pager'); ?></th>
					<td>
						<select name="type" size="1">
							<option value="1" <?php if($options['type'] != 2 && $options['type'] != 3) echo ' selected '; ?>><?php _e('both', 'ajax-comment-pager'); ?></option>
							<option value="2" <?php if($options['type'] == 2) echo ' selected '; ?>><?php _e('comment', 'ajax-comment-pager'); ?></option>
							<option value="3" <?php if($options['type'] == 3) echo ' selected '; ?>><?php _e('pings', 'ajax-comment-pager'); ?></option>
						</select>
						<br/>
						<span class="setting-description"><?php _e('Get different types of comments.', 'ajax-comment-pager'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input class="button-primary" type="submit" name="ajax_comment_pager_save" value="<?php _e('Save Changes', 'ajax-comment-pager'); ?>" />
		</p>
	</div>

</form>

<?php
	}
}
add_action('admin_menu', array('AJAXCommentPagerOptions', 'add'));

/** AJAX function */
function cpage_ajax(){
	if ($_GET['action'] == 'cpage_ajax') {

		// global variables
		global $wp_query, $wpdb, $authordata, $comment, $user_ID, $wp_rewrite;

		// options
		$options = get_option('ajax_comment_pager_options');

		// post ID
		$post_id = $_GET["post"];

		// comment page ID
		$page_id = $_GET["page"];

		// callback method name
		$callback = $options['callback'];

		// type
		$type = '';
		if ($options['type'] == 2) {
			$type = '&type=comment';
		} else if ($options['type'] == 3) {
			$type = '&type=pings';
		}

		// set as singular (is_single || is_page || is_attachment)
		$wp_query->is_singular = true;

		// admin data
		$authordata = get_userdata(1);

		// comment author username
		if (isset($_COOKIE['comment_author_'.COOKIEHASH])) {
			$comment_author = apply_filters('pre_comment_author_name', $_COOKIE['comment_author_'.COOKIEHASH]);
			$comment_author = stripslashes($comment_author);
			$comment_author = attribute_escape($comment_author);
		}

		// comment author email
		if (isset($_COOKIE['comment_author_email_'.COOKIEHASH])) {
			$comment_author_email = apply_filters('pre_comment_author_email', $_COOKIE['comment_author_email_'.COOKIEHASH]);
			$comment_author_email = stripslashes($comment_author_email);
			$comment_author_email = attribute_escape($comment_author_email);
		}

		// comments
		if ($user_ID) {
			$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date", $post_id, $user_ID));
		} else if ( empty($comment_author) ) {
			$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = '1' ORDER BY comment_date", $post_id));
		} else {
			$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY comment_date", $post_id, $comment_author, $comment_author_email));
		}

		// base url of page links
		$baseLink = '';
		if ($wp_rewrite->using_permalinks()) {
			$baseLink = '&base=' . user_trailingslashit(get_permalink($post_id) . 'comment-page-%#%', 'commentpaged');
		}

		// response
		wp_list_comments('callback=' . $callback . '&page=' . $page_id . '&per_page=' . get_option('comments_per_page') . $type, $comments);
		echo '<!-- AJAX_COMMENT_PAGER_SEPARATOR_BY_MG12 -->';
		echo '<span id="cp_post_id">' . $post_id . '</span>';
		paginate_comments_links('current=' . $page_id . $baseLink);
		die();
	}
}
add_action('init', 'cpage_ajax');

/** add to WordPress */
function commentpager_head() {
	$options = get_option('ajax_comment_pager_options');
	if (!(is_single() || is_page() || $withcomments) || !get_option('page_comments') || !$options['comments_id'] || !$options['callback']) {
		return;
	}

	$css_url = get_bloginfo("wpurl") . '/wp-content/plugins/ajax-comment-pager/ajax-comment-pager.css';
	if ( file_exists(TEMPLATEPATH . "/ajax-comment-pager.css") ){
		$css_url = get_bloginfo("template_url") . "/ajax-comment-pager.css";
	}
	echo "\n" . '<!-- generated by AJAX Commnets Pager START -->';
	echo "\n" . '<link rel="stylesheet" href="' . $css_url . '" type="text/css" media="screen" />';
	echo "\n" . '<script type="text/javascript">//<![CDATA[';
	echo "\n" . 'var ajaxCommnetsPagerCommentsId = "' . $options['comments_id'] . '"';
	echo "\n" . 'var ajaxCommnetsPagerAjaxLoader = "' . __('Loading...', 'ajax-comment-pager') . '"';
	echo "\n" . '//]]></script>';
	echo "\n" . '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/ajax-comment-pager/ajax-comment-pager.js"></script>';
	echo "\n" . '<!-- generated by AJAX Commnets Pager END -->' . "\n";
}
add_action('wp_head', 'commentpager_head');

?>