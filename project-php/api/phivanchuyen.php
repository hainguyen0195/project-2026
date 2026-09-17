<?php
	error_reporting(0);
	session_start();
	$session=session_id();
	if(!isset($_SESSION['lang']))
	{
	$_SESSION['lang']='vi';
	}
	$lang=$_SESSION['lang'];
	
	@define ( '_lib' , '../libraries/');
	@define ( '_source' , '../sources/');
    
	include_once _lib."config.php";
	include_once _lib."constant.php";;
	include_once _lib."functions.php";
	include_once _lib."functions_giohang.php";
	include_once _lib."class.database.php";
	include_once _source."lang_$lang.php";
	
	$d = new database($config['database']);
	
	$id_tinh = (int)$_GET['id_tinh'];
	
?>
 Phí vận chuyển : <span><?php if(phivanchuyen($id_tinh)==0){ echo 'Miển Phí';} else { echo number_format(phivanchuyen($id_tinh),0, ',', '.').' VND ';}?> </span> 
<input name="phivanchuyen" type="hidden" value="<?=phivanchuyen($id_tinh)?>">