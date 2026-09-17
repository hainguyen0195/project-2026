<?php 
if(!empty($_POST)&& (isset($_POST['username'])||isset($_POST['username_login']))) {
	$username = magic_quote($_POST['username_login']);
	$password = magic_quote($_POST['password_login']);
	$d->reset();
	$sql = "select * from #_thanhvien where email='".$username."'";
	$d->query($sql);
	if($d->num_rows() == 1){
		$row = $d->fetch_array();
		
		if($row['hienthi']!=1){
			transfer(_banphaikichhoat, $_SESSION['links'],false);
		} else { 
			if(($row['password'] == md5($password))){
				//$name_log => in file "file_requick"
				//$name_log ="phanthetuan" => $_SESSION['phanthetuan'] 
				$_SESSION['login_member']= $row;
				if($_POST['rememberpass']){
					setcookie('email_remember', $row['email'], time()+60*60*24*365);
					setcookie('password_remember', $row['password'], time()+60*60*24*365);
				}
				else {
					setcookie('email_remember', '', time()-60*60*24*365);
					setcookie('password_remember', '', time()-60*60*24*365);
				}

				$giohang_user=json_decode($_SESSION['login_member']['giohang'],true);
				for ($i=0; $i < count($giohang_user); $i++) { 
					$_SESSION['cart'][$i]['productid'] = $giohang_user[$i]['productid'];
					$_SESSION['cart'][$i]['qty'] = $giohang_user[$i]['qty'];
				}

				transfer(_dangnhapthanhcong, $_SESSION['links']);
			}
		}
	}
		transfer(_tendangnhaphoacmatkhaukhongchinhxac, $_SESSION['links'],false);
	}
	
	$title_bar .= _dangnhap; 
?>