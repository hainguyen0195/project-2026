<?php if(!defined('_source')) die("Error");
		
		$title_bar .= " - Dang ky";

	
		if(!empty($_POST)&&isset($_POST['nhanmail'])){

  			
			
		
		$data['email'] = magic_quote($_POST['email']);
		$data['ten'] = magic_quote($_POST['tieude']);
		$data['noidung'] = magic_quote($_POST['noidung']);
		$data['ngaytao'] = time();
		

		$d->setTable('nhanmail');
		if($d->insert($data))
			transfer("Bạn đã đăng ký thành công<br/>Cảm ơn", $http.$config_url."/");
		else
			transfer("Lưu dữ liệu bị lỗi", "index.php?com=register");
		}
		
	
		
	
?>