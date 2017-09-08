<?php
/*
Plugin Name: ! Sitemap
Plugin URI: 
Description: 
Version: 
Author: 
Author URI: 
*/

 function sitemaplistpages($arr,$query_args){
	$map_args=array(
		'title'=>'post_title',
		'date'=>'post_date',
		'author'=>'post_author',
		'modified'=>'post_modified'
	);
	$orderby=array_key_exists($query_args['orderby'],$map_args)?$map_args[$query_args['orderby']]:$query_args['orderby'];
	$r=array(
		'depth'=>$arr['page_depth'],
		'show_date'=>'',
		'date_format'=>get_option('date_format'),
		'child_of'=>0,
		'exclude'=>$arr['exclude'],
		'echo'=>1,
		'authors'=>'',
		'sort_column'=>$orderby,
		'sort_order'=>$query_args['order'],
		'link_before'=>'',
		'link_after'=>'',
        'item_spacing'=>'',
		//'walker'=>'',
	);
	$output='';
	$current_page=0;
	$r['exclude']=preg_replace('/[^0-9,]/','',$r['exclude']); // sanitize,mostly to keep spaces out
	$r['hierarchical']=0;
	$pages=get_pages($r);
	if(!empty($pages)){
		global $wp_query;
		if(is_page()|| is_attachment()|| $wp_query->is_posts_page){
			$current_page=get_queried_object_id();
		}elseif(is_singular()){
			$queried_object=get_queried_object();
			if(is_post_type_hierarchical($queried_object->post_type)){
				$current_page=$queried_object->ID;
			}
		};
		$output .= walk_page_tree($pages,$r['depth'],$current_page,$r);
	};
	if($arr['links'] != 'true'){$output=preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/',"\\2",$output);};
	if($r['echo']){
		echo $output;
	}else{
		return $output;
	}
}



add_shortcode('sitemap',function($args){
	extract(shortcode_atts(array(
		'types'=>'page',
		'show_excerpt'=>'false',
		'title_tag'=>'',
		'excerpt_tag'=>'div',
		'post_type_tag'=>'h2',
		'show_label'=>'true',
		'links'=>'true',
		'page_depth'=>0,
		'order'=>'asc',
		'orderby'=>'title',
		'exclude'=>''
	),$args));
	$title_tag=tag_escape($title_tag);
	$excerpt_tag=tag_escape($excerpt_tag);
	$post_type_tag=tag_escape($post_type_tag);
	$page_depth=intval($page_depth);
	$post_types=$types; // allows the use of the shorter 'types' rather than 'post_types' in the shortcode	
	ob_start();
	$post_types=array_map('trim',explode(',',$post_types)); // convert comma separated string to array
	$exclude=array_map('trim',explode(',',$exclude)); // must be array to work in the post query
	$registered_post_types=get_post_types();
/*
echo "<pre>";
print_r($registered_post_types);
print_r($post_types);
print_r($exclude);
echo "</pre>";
*/
	foreach($post_types as $post_type){
		$ul_class='sitemap-' . $post_type;
		if(!array_key_exists($post_type,$registered_post_types)){break;};
		if(!empty($title_tag)){
			$title_open='<' . $title_tag . '>';
			$title_close='</' . $title_tag . '>';
		}else{
			$title_open=$title_close='';
		};
		if($show_label == 'true'){
			$post_type_obj =get_post_type_object($post_type);
			$post_type_name=$post_type_obj->labels->name;
			echo '<' . $post_type_tag . '>' . esc_html($post_type_name). '</' . $post_type_tag . '>';
		};
		$query_args=array(
			'posts_per_page'=>-1,
			'post_type'=>$post_type,
			'order'=>$order,
			'orderby'=>$orderby,
			'post__not_in'=>$exclude
		);
		if($post_type == 'page'){
			$arr=array(
				'title_tag'=>$title_tag,
				'links'=>$links,
				'title_open'=>$title_open,
				'title_close'=>$title_close,
				'page_depth'=>$page_depth,
				'exclude'=>$exclude
			);
			echo '<ul class="' . esc_attr($ul_class). '">';
			sitemaplistpages($arr,$query_args);
			echo '</ul>';
			continue;
		};
		$sitemap_query=new WP_Query($query_args);
		if($sitemap_query->have_posts()){
			echo '<ul class="'. esc_attr($ul_class).'">';
			while($sitemap_query->have_posts()){
				$sitemap_query->the_post();
				$title=get_the_title();
				if(!empty($title)){
					$title=esc_html($title);
					$title=$title_open.($links!=='true'?$title:('<a href="' . esc_url(get_permalink()). '">' . $title. '</a>')).$title_close;
				}else{
					$title=$title_open.($links!=='true'?'(no title)':('<a href="' . esc_url(get_permalink()). '">' . '(no title)' . '</a>')).$title_close;
				};
				$excerpt=$show_excerpt == 'true' ? '<' . $excerpt_tag . '>' . esc_html(get_the_excerpt()). '</' . $excerpt_tag . '>' : '';
				echo '<li>'.$title.$excerpt. '</li>';
			};
			echo '</ul>';
			wp_reset_postdata();
		}else{
			echo '<p>Sorry,no posts matched your criteria</p>';
		};
	};
	$sitemap=ob_get_contents();
	ob_end_clean();
	return wp_kses_post($sitemap);
});


add_shortcode('sitemap-group',function($args){
	extract(shortcode_atts(array(
		'tax'=>'category',// single taxonomy
		'term_order'=>'asc',
		'term_orderby'=>'name',
		'show_excerpt'=>'false',
		'title_tag'=>'',
		'excerpt_tag'=>'div',
		'post_type_tag'=>'h2',
		'show_label'=>'true',
		'links'=>'true',
		'page_depth'=>0,
		'order'=>'asc',
		'orderby'=>'title',
		'exclude'=>''
	),$args));
	$title_tag=tag_escape($title_tag);
	$excerpt_tag=tag_escape($excerpt_tag);
	$post_type_tag=tag_escape($post_type_tag);
	$page_depth=intval($page_depth);
	$post_type='post';
	ob_start();
	$exclude=array_map('trim',explode(',',$exclude)); // must be array to work in the post query
	$registered_post_types=get_post_types();
/*
echo "<pre>";
print_r($registered_post_types);
print_r($post_types);
print_r($exclude);
echo "</pre>";
*/
	$taxonomy_arr=get_object_taxonomies($post_type);
	if(!empty($tax)&& in_array($tax,$taxonomy_arr)){
		if($show_label === 'true'){
			$post_type_obj=get_post_type_object($post_type);
			$post_type_name=$post_type_obj->labels->name;
			echo '<' . $post_type_tag . '>' . esc_html($post_type_name). '</' . $post_type_tag . '>';
		};
		$term_attr=array(
			'orderby'=> $term_orderby,
			'order'=> $term_order
		);
		$terms=get_terms($tax,$term_attr);
		foreach($terms as $term){
			$ul_class='sitemap-' . $post_type;
			if(!array_key_exists($post_type,$registered_post_types)){break;};
			if(!empty($title_tag)){
				$title_open='<' . $title_tag . '>';
				$title_close='</' . $title_tag . '>';
			}else{
				$title_open=$title_close='';
			};
			$query_args=array(
				'posts_per_page'=>-1,
				'post_type'=>$post_type,
				'order'=>$order,
				'orderby'=>$orderby,
				'post__not_in'=>$exclude,
				'tax_query'=>array(
					array(
						'taxonomy'=>$tax,
						'field'=>'slug',
						'terms'=>$term
					)
				)
			);
			echo '<h4>' . $term->name . '</h4>';
			$sitemap_query=new WP_Query($query_args);
			if($sitemap_query->have_posts()){
				echo '<ul class="' . esc_attr($ul_class). '">';
				while($sitemap_query->have_posts()){
					$sitemap_query->the_post();
					$title_text=get_the_title();
					if(!empty($title_text)){
						if($links == 'true'){
							$title=$title_open . '<a href="' . esc_url(get_permalink()). '">' . esc_html($title_text). '</a>' . $title_close;
						}else{
							$title=$title_open . esc_html($title_text). $title_close;
						}
					}else{
						if($links == 'true'){
							$title=$title_open . '<a href="' . esc_url(get_permalink()). '">' . '(no title)' . '</a>' . $title_close;
						}else{
							$title=$title_open . '(no title)' . $title_close;
						}
					};
					$excerpt=$show_excerpt == 'true' ? '<' . $excerpt_tag . '>' . esc_html(get_the_excerpt()). '</' . $excerpt_tag . '>' : '';
					echo '<li>'.$title.$excerpt.'</li>';
				}; 
				echo '</ul>';
				wp_reset_postdata();
			}else{
				echo '<p>Sorry,no posts matched your criteria</p>';
			};
		}
	}else{
		echo "No posts found.";
	};
	ob_start();
	$sitemap=ob_get_contents();
	ob_end_clean();
	return wp_kses_post($sitemap);
});


add_action('admin_init',function(){
 register_setting(function($m){
  $m['txt_page_ids']=sanitize_text_field($m['txt_page_ids']);// Strip html from textboxes e.g. $m['textbox']= wp_filter_nohtml_kses($m['textbox']);
  return $m;
 });
});


add_action('admin_menu',function(){
	add_options_page('Sitemap Options Page','Sitemap','manage_options',__FILE__,function(){
	?>
	<style>
	a:focus{ box-shadow: none;}
	.pcdm.dashicons{width:32px;height:32px;font-size:32px;}
	.pcdm.dashicons-yes{color:#1cc31c;}
	.pcdm.dashicons-no{color:red;}
	.bbdf{background:#fff;border:1px dashed #ccc;font-size:13px;margin:20px 0 10px 0;padding:5px 0 5px 8px;}
	</style>
		<div class="wrap">
			<h2>Sitemap Options</h2>
			<div class="bbdf">
				shortcode: <code>[sitemap]</code>
			</div>
			<h2>Choose the Post Types to Display</h2>
			Specify post types and order.
			<div class="bbdf">
				<code>e.g. [sitemap types="post,page,testimonial,download"]</code>
			<p>
				<b>default: types="post,page"</b>
				Choose from any of the following registered post types currently available:
			</p>
	<?php
	$registered_post_types=get_post_types();
	$registered_post_types_str=implode(',',$registered_post_types);
	echo '<code>' . $registered_post_types_str . '</code><br><br>';
	?>
			</div>
			<h2>Formatting the Sitemap Output</h2>
			<p>You have various options for controlling how your sitemap displays</p>
			<div class="bbdf">
				Show a heading label for each post type as well as display a list of links or plain text. If you are outputting pages then you can also control page depth too(for page hierarchies).<br>
				For the <code>order</code> attribute specify <code>asc</code> for ascending,or <code>desc</code> for descending post sort order. As for the <code>orderby</code> attribute you can filter posts by any of the <code>orderby</code> paramters used in the <code>WP_Query</code> class such as <code>title</code>,<code>date</code>,<code>author</code>,<code>ID</code>,<code>menu_order</code> etc. See the full list <a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">here</a>. The <code>exclude</code> attribute simply takes a comma separated list of post IDs.
				<code>e.g. [sitemap show_label="true" links="true" page_depth="1" order="asc" orderby="title" exclude="1,2,3"]</code>			
				<h5>defaults:</h5>
				<p>
				show_label="true"<br>
				links="true"<br>
				page_depth="0"<br>
				order="asc"<br>
				orderby="title"<br>
				exclude=""
				</p>
			</div>
		</div>
	<?php
	});
});

//remove_update_notification
add_filter('site_transient_update_plugins',function($x){unset($x->response[plugin_basename(__FILE__)]);return $x;});