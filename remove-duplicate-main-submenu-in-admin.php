if(is_admin()){

 add_action('admin_menu',function(){
	$v='Database';
	$k='manage_database';
	$i='wl_dbm/home.php';

	if(function_exists('add_menu_page')){add_menu_page($v,$v,$k,$i,'','dashicons-archive');remove_submenu_page($i,$i);};

	if(function_exists('add_submenu_page')){



	 $v='Info';
	 add_submenu_page($i,$v,$v,$k,$i);
	 $v='Tables';
	 add_submenu_page($i,$v,$v,$k,'wl_dbm/tables.php');
	 $v='Backup';
	 add_submenu_page($i,$v,$v,$k,'wl_dbm/backup.php');
	 $v='Manage';
	 add_submenu_page($i,$v,$v,$k,'wl_dbm/manage.php');
	 $v='Optimize ';
	 add_submenu_page($i,$v,$v,$k,'wl_dbm/optimize.php');
	 $v='Repair ';
	 add_submenu_page($i,$v,$v,$k,'wl_dbm/repair.php');
	 $v='Erase';
	 add_submenu_page($i,$v,$v,$k,'wl_dbm/empty.php');
	 $v='Run SQL Query';
	 add_submenu_page($i,$v,$v,$k,'wl_dbm/run.php');
	 $v='Options';
	 add_submenu_page($i,$v,$v,$k,'wl_dbm/wl_dbm.php','dbm_options');
	};

 });
