<?php
	session_start();
	error_reporting(E_ALL & ~E_NOTICE & ~8192);
	
	@define ( '_lib' , '../libraries/');
    
	include_once _lib."config.php";
	include_once _lib."constant.php";;
	include_once _lib."functions_giohang.php";
	include_once _lib."class.database.php";
    
	$d = new database($config['database']);
	$score=(float)$_GET['score'];
	$id=(int)$_GET['id'];
	$d->reset();
	$d->query("select rate from #_product where id=$id");
	$item=$d->fetch_array();
	if($item['rate'])
		$sqlUPDATE_ORDER = "UPDATE table_product SET rate =".(($score+$item['rate'])/2)." WHERE  id = ".$id."";
	else
		$sqlUPDATE_ORDER = "UPDATE table_product SET rate =".($score)." WHERE  id = ".$id."";
	$resultUPDATE_ORDER = mysql_query($sqlUPDATE_ORDER) or die("Not query sqlUPDATE_ORDER");
?>