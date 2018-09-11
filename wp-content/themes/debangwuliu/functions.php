<?php 

//theme option
include_once('admin-option/theme-option.php');

//register nav menu
register_nav_menus(array(
	'top'=>'头部导航'
));

class Db_Walker_Nav_Menu extends Walker_Nav_Menu {

	function start_el(&$output, $item, $depth, $args) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<TD width="80" ' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function end_el(&$output, $item, $depth) {
		$output .= "</TD>\n";
	}
}


//thumbanil
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
}

function web589_first_thumbnail($id,$size="thumbnail"){
	global $post;
	$args=array('post_type'=>'attachment','post_mime_type'=>'image','post_parent'=>$post->ID,'order'=>'asc');
	$images=get_children($args);
	if(has_post_thumbnail()) return get_the_post_thumbnail($post->ID,$size);
	else if($images){
		$attachment_id=key($images);
		return wp_get_attachment_image($attachment_id,$size);
	}
	else {
		$preg="/(<img )([^>]*)(>)/"; 
		$content=$post->post_content;
		preg_match($preg,$content,$img);
		return $img[0];
	}
}

function db_page_thumbnail($id,$size='medium'){
	$args=array('post_type'=>'attachment','post_mime_type'=>'image','post_parent'=>$id,'order'=>'asc');
	$images=get_children($args);
	if($images){
		$attachment_id=key($images);
		return wp_get_attachment_image($attachment_id,$size);		
	}
}


//crumbs
function web589_crumbs($sep='&gt;&gt;',$home='首页'){
	$par=web589_get_parrents($sep);
	if(!empty($par)){
		$num=count($par);
		$m=1;
		echo '<a href="'.get_bloginfo('url').'">'.$home.'</a>'.$sep;
		foreach($par as $link=>$name){
			if($m==$num) continue;
			else echo '<a href="'.$link.'">'.$name.'</a>'.$sep;
			$m++;
		}
	}
}
function web589_get_parrents($sep){
	if(is_category()){
		$par=get_ancestors(get_query_var('cat'),'category');
		$num=count($par);
		for($i=$num;$i>=1;$i--){
			$j=$i-1;
			$id=$par[$j];
			$array[get_category_link($id)]=get_cat_name($id);
		}
		$array[get_category_link(get_query_var('cat'))]=get_cat_name(get_query_var('cat'));
	}
	if(is_page()){
		$par=get_ancestors(get_the_ID(),'page');
		$num=count($par);
		for($i=$num;$i>=1;$i--){
			$j=$i-1;
			$id=$par[$j];
			$page=get_page($id);
			$array[get_page_link($id)]=$page->post_title;
		}
		$cur_page=get_page(get_the_ID());
		$array[get_page_link(get_the_ID())]=$cur_page->post_title;		
	}
	if(is_single()){
		$cats=get_the_category();
		foreach($cats as $cat){
			foreach($cats as $child){
				if(!cat_is_ancestor_of($cat,$child)) $id=$cat->cat_ID;
			}
		}		
		$par=get_ancestors($id,'category');
		$num=count($par);
		for($i=$num;$i>=1;$i--){
			$j=$i-1;
			$p_id=$par[$j];
			$array[get_category_link($p_id)]=get_cat_name($p_id);
		};
		$array[get_category_link($id)]=get_cat_name($id);						
		$array[get_permalink()]=get_the_title();
	}
	if(is_tag()){
		$tag=get_tag(get_query_var('tag_id'));
		$array[]=$tag->name;
	}
	if(is_day() ||is_month() ||is_year()){
		$array[]=wp_title('',false);
	}	
	return $array;
}


//paging
function web589_paging_data(){
	$page['sum']=web589_count_posts_num();
	$page['pages']=ceil($page['sum']/get_option('posts_per_page'));
	$page['cur']=get_query_var('paged') ? get_query_var('paged'):1;
	for($i=1;$i<=$page['pages'];$i++){
		$page[$i]=get_pagenum_link($i);
	}
	return $page;
}

//count posts num
function web589_count_posts_num(){
	global $wpdb;
	if( is_home() || is_front_page() ){
		query_posts(array('ignore_sticky_posts'=>true,'posts_per_page'=>-1));	
	}
	if(is_category()){
		query_posts(array('cat'=>get_query_var('cat'),'ignore_sticky_posts'=>true,'posts_per_page'=>-1));
	}
	if(is_tag()){
		query_posts(array('tag_id'=>get_query_var('tag_id'),'ignore_sticky_posts'=>true,'posts_per_page'=>-1));	
	}
	if(is_date()){
		query_posts(array('year'=>get_query_var('year'),'monthnum'=>get_query_var('monthnum'),'w'=>get_query_var('w'),'day'=>get_query_var('day'),'hour'=>get_query_var('hout'),'minute'=>get_query_var('minute'),'second'=>get_query_var('second'),'ignore_sticky_posts'=>true,'posts_per_page'=>-1));		
	}
	if(is_author()){
		query_posts(array('author'=>get_query_var('author'),'ignore_sticky_posts'=>true,'posts_per_page'=>-1));	
	}
	if(is_search()){
		query_posts(array('s'=>get_query_var('s'),'ignore_sticky_posts'=>true,'posts_per_page'=>-1));			
	}
	$i=0;
	while(have_posts()){
		the_post();
		$i++;
	}		
	wp_reset_query();
	return $i;
}