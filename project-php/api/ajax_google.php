<?php
	session_start();
	error_reporting(E_ALL & ~E_NOTICE & ~8192);
	
	@define ( '_lib' , '../libraries/');
    
	include_once _lib."config.php";
	include_once _lib."constant.php";;
	include_once _lib."functions.php";
	include_once _lib."class.database.php";
    
	$d = new database($config['database']);
	$email=magic_quote($_POST['email']);
	$ten=magic_quote($_POST['name']);
	$id=(int)$_POST['id'];
	$d->reset();
	$d->query("select * from #_thanhvien where email='".$email."'");
	$item=$d->fetch_array();
	if($item){
		$_SESSION['login_member']=$item;
	}
	else{
		$mathanhvien=ChuoiNgauNhien(6).''.$time;
		$data['email'] = $email;
		$data['ten'] = $ten;
		$data['mathanhvien'] = $mathanhvien;
		$data['google_auth_id'] = $id;
		$data['ngaytao'] = time();
		$data['hienthi'] = 1;
		$d->setTable('thanhvien');
		$d->insert($data);
		$d->reset();
		$d->query("select * from #_thanhvien where email='".$email."'");
		$item=$d->fetch_array();
		$_SESSION['login_member']=$item;
	}
?>