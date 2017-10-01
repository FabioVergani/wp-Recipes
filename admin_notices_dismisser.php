<?php
//fabio vergani
add_action('admin_notices',function(){
?>
<script>
window.addEventListener('load',function(){
	var $=jQuery,f=setTimeout;
	$('.notice.is-dismissible').each(function(i){
		var e=$(this);
		f(function(){e.find('button.notice-dismiss').click();},(i+1)*900);
	});
});
</script>
<?php
});

//add_action('admin_notices',function(){echo '<script>window.addEventListener('load',function(){var $=jQuery,f=setTimeout;$('.notice.is-dismissible').each(function(i){var e=$(this);f(function(){e.find('button.notice-dismiss').click();},(i+1)*900);});});</script>';});
