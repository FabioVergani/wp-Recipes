//fabiovergani
function get_qs(){
 $o=&$_SERVER;
 $x='http';
 if($o['HTTPS'] === 'on'){$x .= 's';};
 $x .= '://'.$o['SERVER_NAME'];
 if($p !== '80'){
  $x .= ':'.$o['SERVER_PORT'].$o['REQUEST_URI'];
 }else{
  $x .= $o['REQUEST_URI'];
 };
 $o=explode('&',explode('?',$x)[1]);
 $x=[];
 foreach($o as $p){
	$m=explode('=',$p);
	$x[$m[0]]=$m[1];
 };
 return $x;
}
//test: print_r(get_qs());
