<?php
/*
Plugin Name: MU
#Author: F
*/

/*
if(!function_exists('DisableWpFeed')){
	function DisableWpFeed(){
		wp_die('No feed available');
	}
};
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
add_filter('the_generator',$f);

unset($f);

remove_action('wp_head','feed_links_extra',3);
remove_action('wp_head','feed_links',2);

remove_action('wp_head','wp_resource_hints',2);//WP 4.9+ dns-prefetch nonsense
remove_action('wp_head','wlwmanifest_link');
remove_action('wp_head','rsd_link');

remove_action('wp_head','start_post_rel_link');
remove_action('wp_head','index_rel_link');
remove_action('wp_head','adjacent_posts_rel_link');
remove_action('wp_head','adjacent_posts_rel_link_wp_head',10);
remove_action('wp_head','wp_shortlink_wp_head');
/*
/?feed=rss
/?feed=rss2
/feed/
/feed/rdf/ > download.rdf
*/
remove_action('set_comment_cookies','wp_set_comment_cookies');
//#
if(is_admin()){

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
	add_filter($t.'core',$f);
	add_filter($t.'plugin',$f);
	add_filter($t.'theme',$f);
	unset($t,$f);
	add_image_size('medium',960,540, true );
 });

}else{

 add_action('init',function(){
	remove_action('wp_print_styles','print_emoji_styles');
	remove_action('wp_head','print_emoji_detection_script',7); 
 });

	add_action('after_setup_theme',function(){
		remove_filter('oembed_dataparse','wp_filter_oembed_result',10);
		remove_action('rest_api_init','wp_oembed_register_route');		
		remove_action('wp_head','wp_oembed_add_discovery_links',10);
		remove_action('wp_head','wp_oembed_add_discovery_links');
		remove_action('wp_head','wp_oembed_add_host_js');
		remove_action('wp_head','rest_output_link_wp_head',10);
		add_filter('rewrite_rules_array','disable_embeds_rewrites');
		$f='__return_false';
		add_filter('embed_oembed_discover',$f);
		add_filter('json_enabled', $f);
		add_filter('json_jsonp_enabled', $f);
		add_filter('rest_enabled', $f);
		add_filter('rest_jsonp_enabled', $f);
	});

};
//

#Done.
?>
