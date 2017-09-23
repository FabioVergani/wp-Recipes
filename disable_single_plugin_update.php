
		$PlugInBasenameFile=plugin_basename(__FILE__);

		add_filter('http_request_args',function($a,$b)use($PlugInBasenameFile){
			if(strpos($b,'//api.wordpress.org/plugins/update-check/')){
				$b=&$a['body'];
				$n=&$PlugInBasenameFile;
				$m=json_decode($b['plugins'],true);
				$e=&$m['active'];
				unset($m['plugins'][$n],$e[array_search($n,$e)]);
				$b['plugins']=json_encode($m);
			};
			return$a;
		},1,2);
