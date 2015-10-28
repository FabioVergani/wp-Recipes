	$v='Database';
	$k='manage_database';
	$i='wl_dbm/home.php';

	if(function_exists('add_menu_page')){add_menu_page($v,$v,$k,$i,'','dashicons-archive');};

	if(function_exists('add_submenu_page')){

add_submenu_page( $i, '',     '', $k,   $i,   '__return_null' );
    remove_submenu_page($i,$i);
