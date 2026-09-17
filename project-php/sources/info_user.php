<?php  if(!defined('_source')) die("Error");
if($_POST){
	if($_POST['password']){
		$d->reset();
		$sql = "select * from #_thanhvien where password='".md5($_POST['password'])."' and id=".$_SESSION['login_member']['id']."";
		$d->query($sql);
		if($d->num_rows() == 1){
			if($_POST['password_new']){
				$data['password']=md5($_POST['password_new']);
			}
			$data['dienthoai']=magic_quote($_POST['dienthoai']);
			$data['ten']=magic_quote($_POST['hoten']);
			$data['diachi']=magic_quote($_POST['diachi']);
			$d->setTable('thanhvien');
			$d->setWhere('id',$_SESSION['login_member']['id']);
			if($d->update($data)){
				$d->reset();
				$d->query("select * from #_thanhvien where id=".$_SESSION['login_member']['id']." limit 0,1");
				$row=$d->fetch_array();
				$_SESSION['login_member']=$row;
				transfer(_capnhatthongtinthanhcong,$_SESSION['links']);
			}
		}
		else{
			transfer(_matkhauchuachinhxac,$_SESSION['links']);
		}
	}
	else{
		transfer(_banchuanhapmatkhaucu,$_SESSION['links']);
	}
}