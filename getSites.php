//#getSites exist?
if(function_exists('getSites')){
	//exist!
}else{
	if(function_exists('get_sites')){
		function getSites(){return get_sites();}
	}elseif(function_exists('wp_get_sites')){
		function getSites(){return wp_get_sites();}
	}else{
		function getSites(){return null;}
	};
};

		register_activation_hook( __FILE__,function($network_wide){
			if(is_multisite() && $network_wide){
				$m=getSites();
				if(0<sizeof($m)){
					foreach($m as $ite){
						switch_to_blog(SiteBlogIdOf($ite));
						//activated();
						restore_current_blog();
					}
				}
			} else {
				//activated();
			}
		});

