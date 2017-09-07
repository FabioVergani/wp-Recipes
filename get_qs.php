function get_qs(){
 $x='http';
 if($_SERVER['HTTPS'] === 'on'){$x .= 's';};
 $x .= '://';
 $p=$_SERVER['SERVER_PORT'];
 if($p !== '80'){
  $n=$_SERVER['SERVER_NAME'];
  $x .= $n.':'.$p.$_SERVER['REQUEST_URI'];
 }else{
  $x .= $n.$_SERVER['REQUEST_URI'];
 };
 $qs=explode('&',explode('?',$x)[1]);
 $x=[];
 foreach($qs as $nv){
	$m=explode('=',$nv);
	$x[$m[0]]=$m[1];
 };
 return $x;
}
//
print_r(get_qs());
