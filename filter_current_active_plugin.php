<?php

add_action('pre_current_active_plugins',function(){
  global $wp_list_table;
  $o=&$wp_list_table;
  $p=$o->items;
  if($_REQUEST['plugin_status']!=='_'){
	  foreach($p as $i=>$v){
		if(substr($v['Name'],0,1)==='!') {
			unset($o->items[$i]);
		};
	  };  
  };
});
