<?php
/*
Plugin Name: MU
#Author:
*/
$f='__return_false';
add_action('do_feed',$f,1);
add_action('do_feed_rdf',$f,1);
add_action('do_feed_rss',$f,1);
add_action('do_feed_rss2',$f,1);
add_action('do_feed_rss2_comments',$f,1);
add_action('do_feed_atom',$f,1);
add_action('do_feed_atom_comments',$f,1);


add_filter('feed_links_show_comments_feed',$f);
add_filter('xmlrpc_enabled',$f);

unset($f);

add_filter('wp_headers',function($m){
	unset($m['X-Pingback']);
	return $m;
},PHP_INT_MAX);

add_filter('xmlrpc_methods',function(){return [];},PHP_INT_MAX);


//#
if(is_admin()){

 add_image_size('medium',960,540,true);
 
 add_action('admin_init',function(){
	remove_action('init','wp_version_check');
	remove_action('admin_notices','update_nag');
	remove_action('admin_head','wp_admin_canonical_url');
	remove_action('admin_print_styles','print_emoji_styles');
	remove_action('admin_print_scripts','print_emoji_detection_script');
	$f=function(){return true;};
	add_filter('automatic_updater_disabled',$f);
	$f=function(){return false;};
	$t='auto_update_';
	add_filter($t.'core',$f,PHP_INT_MAX);
	add_filter($t.'plugin',$f,PHP_INT_MAX);
	add_filter($t.'theme',$f,PHP_INT_MAX);
	unset($t,$f);
 });

}else{
 
	 add_action('init',function(){
		remove_action('set_comment_cookies','wp_set_comment_cookies',PHP_INT_MAX);
		remove_action('wp_print_styles','print_emoji_styles',PHP_INT_MAX);
		remove_action('wp_head','print_emoji_detection_script',PHP_INT_MAX); 
	});

	add_action('after_setup_theme',function(){
		remove_action('rest_api_init','wp_oembed_register_route',PHP_INT_MAX);
		remove_action('wp_head','wp_generator',PHP_INT_MAX);
		remove_action('wp_head','wp_oembed_add_host_js',PHP_INT_MAX);
		remove_action('wp_head','wp_shortlink_wp_head',PHP_INT_MAX);
		remove_action('wp_head','rsd_link',PHP_INT_MAX);
		remove_action('wp_head','index_rel_link',PHP_INT_MAX);
		remove_action('wp_head','wlwmanifest_link',PHP_INT_MAX);
		remove_action('wp_head','start_post_rel_link',PHP_INT_MAX);
		remove_action('wp_head','adjacent_posts_rel_link',PHP_INT_MAX);	
		remove_action('wp_head','wp_resource_hints',PHP_INT_MAX);//WP 4.9+ dns-prefetch nonsense
		remove_action('wp_head','parent_post_rel_link',PHP_INT_MAX);
		remove_action('wp_head','adjacent_posts_rel_link_wp_head',PHP_INT_MAX);
		remove_action('wp_head','feed_links_extra',PHP_INT_MAX);
		remove_action('wp_head','feed_links',PHP_INT_MAX);
		remove_action('wp_head','wp_oembed_add_discovery_links',PHP_INT_MAX);
		remove_action('wp_head','wp_oembed_add_discovery_links',PHP_INT_MAX);
		remove_action('wp_head','rest_output_link_wp_head',PHP_INT_MAX);
		remove_action('wp_head','rel_canonical',PHP_INT_MAX);

		remove_filter('oembed_dataparse','wp_filter_oembed_result',PHP_INT_MAX);

		$f=function($x){
			if(strpos($x,'ver=')){
				$x=remove_query_arg('ver',$x);
			};
			return $x;
		};
		add_filter('style_loader_src',$f,PHP_INT_MAX,1);
		add_filter('script_loader_src',$f,PHP_INT_MAX,1);

		$f='__return_false';
		add_filter('embed_oembed_discover',$f,PHP_INT_MAX,0);
		add_filter('json_enabled',$f,PHP_INT_MAX,0);
		add_filter('json_jsonp_enabled',$f,PHP_INT_MAX,0);
		add_filter('rest_enabled',$f,PHP_INT_MAX,0);
		add_filter('rest_jsonp_enabled',$f,PHP_INT_MAX,0);

		$f='__return_null';
		add_filter('the_generator',$f,PHP_INT_MAX,0);

		unset($f);

		add_filter('the_content',function($x){
			return preg_replace('/<p>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s','<figure class="'.(apply_filters('get_image_tag_class',$class,$id,$align,$size)+$align).'">$1</figure>',$x);
		},PHP_INT_MAX,1);

		add_filter('get_image_tag_class',function($a,$b,$c,$d){//class,id,align,size
			return 'align'.esc_attr($c);
		},PHP_INT_MAX,4);

		add_filter('get_image_tag',function($a,$b,$c,$d){//html,id,alt,title
			return preg_replace(['/\s+width="\d+"/i','/\s+height="\d+"/i','/alt=""/i'],['','','','alt="'.$d.'"'],$a);
		},PHP_INT_MAX,4);

	});

};
#Done.

	/*
	// Remove default inline style of wp-caption
	$f='fixed_img_caption_shortcode';
	if(!function_exists($f)){
		function fixed_img_caption_shortcode($attr,$content=null){
			if(isset($attr['caption'])){
				//
			}else{
				if(preg_match('#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is',$content,$matches)){
					$content=$matches[1];
					$attr['caption']=trim($matches[2]);
				}
			};
			$output=apply_filters('img_caption_shortcode','',$attr,$content);
			if($output==''){
				extract(shortcode_atts(['id'=>'','align'=>'alignnone','width'=>'','caption'=>''],$attr));
				if(1>(int)$width||empty($caption)){
					return $content
				}else{
					if($id){
						$id='id="'.esc_attr($id).'" ';
					};
					return '<div '.$id.'class="wp-caption '.esc_attr($align).'" style="width: '.$width.'px">'.do_shortcode($content).'<p class="wp-caption-text">'.$caption.'</p></div>';
				};
			}else{
				return $output;
			};
		}
	};
	add_shortcode('wp_caption',$f);
	add_shortcode('caption',$f);
	unset($f);
	
	
if(!function_exists('DisableWpFeed')){
	function DisableWpFeed(){
		wp_die('No feed available');
	}
};


/?feed=rss
/?feed=rss2
/feed/
/feed/rdf/ > download.rdf
	


We are using the htaccess file to protect it from hackers.
# BEGIN protect xmlrpc.php
<files xmlrpc.php>
order allow,deny
deny from all
</files>
# END protect xmlrpc.php
*/

?>
