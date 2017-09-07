//remove_update_notification
add_filter('site_transient_update_plugins',function($x){unset($x->response[plugin_basename(__FILE__)]);return $x;});
