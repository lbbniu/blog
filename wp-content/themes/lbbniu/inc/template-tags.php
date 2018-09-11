<?php
/**
 * Custom template tags for Twenty Fourteen
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

if ( ! function_exists( 'lbbniu_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since Twenty Fourteen 1.0
 */
function lbbniu_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	//$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';

	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';
	// Set up paginated links.
	//$links = paginate_links( array(
	//	'base'     => $pagenum_link,
	//	'format'   => $format,
	//	'total'    => $GLOBALS['wp_query']->max_num_pages,
	//	'current'  => $paged,
	//	'mid_size' => 1,
	//	'add_args' => array_map( 'urlencode', $query_args ),
	//	'prev_text' => __( '上一页', 'lbbniu' ),
	//	'next_text' => __( '下一页', 'lbbniu' ),
	//) );
	$args = array(
		'base'     => $pagenum_link,
		'total'    => $GLOBALS['wp_query']->max_num_pages,
		'current'  => $paged,
		'show_all' => false,
		'end_size' => 1,
		'mid_size' => 1,
		'add_args' => array_map( 'urlencode', $query_args ),
		'prev_text' => __( '上一页', 'lbbniu' ),
		'next_text' => __( '下一页', 'lbbniu' ),
	) ;
	extract($args, EXTR_SKIP);
	$links = '';
	if ( $total < 2 ){
		$links .= '';
	}else{
		$dots = false;
		if ( $current && 1 < $current ){
			$num = $current -1;
			$links .= '<li class="prev-page"><a href="'.$pagenum_link.'/page/'.$num.'">'.$prev_text.'</a></li>';
		}else{
			$links .= '<li class="prev-page"><span>'.$prev_text.'</span></li>';
		}
		for ( $n = 1; $n <= $total; $n++ ) :
			if ( $n == $current ) :
				$links .= '<li class="active"><span>'.$n.'</span></li>';
				$dots = true;
			else :
				if ( $show_all || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
					$links .= '<li><a href="'.$pagenum_link.'/page/'.$n.'">'.$n.'</a></li>';
					$dots = true;
				elseif ( $dots && !$show_all ) :
					$links .= '<li><span> ... </span></li>';
					$dots = false;
				endif;
			endif;
		endfor;

		if ( $current && ( $current < $total || -1 == $total ) ) :
			$num = $current + 1;

		/** This filter is documented in wp-includes/general-template.php */
			$links .= '<li class="next-page"><a href="'.$pagenum_link.'/page/'.$num.'" >'.$next_text.'</a></li>';
		endif;
		
	}
	if ( $links ) :

	?>
	<div class="pagination">
		<ul>
			<?php echo $links;?>
		</ul>
	</div>	
	<?php
	endif;
}
endif;

if ( ! function_exists( 'lbbniu_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 *
 * @since Twenty Fourteen 1.0
 */
function lbbniu_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}

	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'lbbniu' ); ?></h1>
		<div class="nav-links">
			<?php
			if ( is_attachment() ) :
				previous_post_link( '%link', __( '<span class="meta-nav">Published In</span>%title', 'lbbniu' ) );
			else :
				previous_post_link( '%link', __( '<span class="meta-nav">Previous Post</span>%title', 'lbbniu' ) );
				next_post_link( '%link', __( '<span class="meta-nav">Next Post</span>%title', 'lbbniu' ) );
			endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'lbbniu_posted_on' ) ) :
/**
 * Print HTML with meta information for the current post-date/time and author.
 *
 * @since Twenty Fourteen 1.0
 */
function lbbniu_posted_on() {
	if ( is_sticky() && is_home() && ! is_paged() ) {
		echo '<span class="featured-post">' . __( 'Sticky', 'lbbniu' ) . '</span>';
	}

	// Set up and print post meta information.
	printf( '<span class="entry-date"><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s">%3$s</time></a></span> <span class="byline"><span class="author vcard"><a class="url fn n" href="%4$s" rel="author">%5$s</a></span></span>',
		esc_url( get_permalink() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		get_the_author()
	);
}
endif;

/**
 * Find out if blog has more than one category.
 *
 * @since Twenty Fourteen 1.0
 *
 * @return boolean true if blog has more than 1 category
 */
function lbbniu_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'lbbniu_category_count' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'lbbniu_category_count', $all_the_cool_cats );
	}

	if ( 1 !== (int) $all_the_cool_cats ) {
		// This blog has more than 1 category so lbbniu_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so lbbniu_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in lbbniu_categorized_blog.
 *
 * @since Twenty Fourteen 1.0
 */
function lbbniu_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'lbbniu_category_count' );
}
add_action( 'edit_category', 'lbbniu_category_transient_flusher' );
add_action( 'save_post',     'lbbniu_category_transient_flusher' );

/**
 * Display an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index
 * views, or a div element when on single views.
 *
 * @since Twenty Fourteen 1.0
 */
function lbbniu_post_thumbnail() {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) :
	?>

	<div class="post-thumbnail">
	<?php
		if ( ( ! is_active_sidebar( 'sidebar-2' ) || is_page_template( 'page-templates/full-width.php' ) ) ) {
			the_post_thumbnail( 'lbbniu-full-width' );
		} else {
			the_post_thumbnail();
		}
	?>
	</div>

	<?php else : ?>

	<a class="post-thumbnail" href="<?php the_permalink(); ?>">
	<?php
		if ( ( ! is_active_sidebar( 'sidebar-2' ) || is_page_template( 'page-templates/full-width.php' ) ) ) {
			the_post_thumbnail( 'lbbniu-full-width' );
		} else {
			the_post_thumbnail();
		}
	?>
	</a>

	<?php endif; // End is_singular()
}
