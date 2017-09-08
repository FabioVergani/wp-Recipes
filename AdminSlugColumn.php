	 //#AdminSlugColumn
	 add_action('manage_pages_custom_column',function($x,$id){
		if($x==='slug'){
			echo esc_attr(get_post($id,'string','display')->post_name);
		}
	 });
	 add_filter('manage_edit-page_columns',function($m){
		return array_merge($m,array(
			'slug'=>'Slug',
		));
	 });
