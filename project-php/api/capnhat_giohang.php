<?php
	session_start();
	error_reporting(E_ALL & ~E_NOTICE & ~8192);

	if(!isset($_SESSION['lang'])||$_SESSION['lang']=='')
	{
	$_SESSION['lang']='vi';
	}
	$lang=$_SESSION['lang'];
	
	@define ( '_lib' , '../libraries/');
    
	include_once _lib."config.php";
	include_once _lib."constant.php";;
	include_once _lib."functions.php";
	include_once _lib."functions_giohang.php";
	include_once _lib."class.database.php";
    
	$d = new database($config['database']);

	@$q = (int)$_POST['soluong'];
	@$product = (int)$_POST['product'];

	$max=count($_SESSION['cart']);
	for($i=0;$i<$max;$i++){
		$pid=$_SESSION['cart'][$i]['productid'];
		$thongtin=trim($_SESSION['cart'][$i]['thongtin']);
		if($pid==$product ){
			if($q>0 && $q<=999){
				$soluong = str_replace(",", '.', $q);
				$_SESSION['cart'][$i]['qty']=$soluong;
			}
		}
	}

	$price_item = number_format(get_price($product)*$q,0, ',', '.')." &nbsp;VND ";
	$full_item = number_format(get_order_total(),0, ',', '.')." &nbsp;VND ";

	$get_order_info = array('price_item' => $price_item ,'full_item' => $full_item);

	echo json_encode($get_order_info);
		
?>