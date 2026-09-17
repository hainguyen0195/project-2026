<?php
	$config_email=$row_setting['email_host'];
	$config_pass=$row_setting['pass_host'];
	$config_ip=$row_setting['ip_host'];
	
	$com = (isset($_REQUEST['com'])) ? magic_quote(addslashes($_REQUEST['com'])) : "";
	$act = (isset($_REQUEST['act'])) ? magic_quote(addslashes($_REQUEST['act'])) : "";
	$breadcrumb='<li class="breadcrumb-item"><a href="" title="'._trangchu.'">'._trangchu.'</a></li>';
	
	$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
	
	if ($page <= 0) $page = 1;
	$data_tpl =
	array(
		array("tbl"=>"product_list","field"=>"idl","type"=>"product","source"=>"product","com"=>"san-pham"),
		array("tbl"=>"product_cat","field"=>"idc","type"=>"product","source"=>"product","com"=>"san-pham"),
		array("tbl"=>"product","field"=>"id","type"=>"product","source"=>"product","com"=>"san-pham"),

		array("tbl"=>"baiviet","field"=>"id","type"=>"tintuc","source"=>"news","com"=>"tin-tuc"),
		
		array("tbl"=>"baiviet","field"=>"id","type"=>"vechungtoi","source"=>"news","com"=>"ve-chung-toi"),
		array("tbl"=>"baiviet","field"=>"id","type"=>"chinhsach","source"=>"news","com"=>"chinh-sach"),
		array("tbl"=>"info","field"=>"id","type"=>"gioigthieu","source"=>"about","com"=>"gioi-thieu"),
		array("tbl"=>"info","field"=>"id","type"=>"catalog","source"=>"about","com"=>"catalog"),
		
		array("tbl"=>"info","field"=>"id","type"=>"contact","source"=>"contact","com"=>"lien-he"),
	);
	if($com!=''){
		foreach($data_tpl as $k=>$v){
			if(isset($com) && $v['tbl']!='info'){
				$d->query("select id,tenkhongdau from #_".$v['tbl']." where tenkhongdau='".$com."' and hienthi=1 and type = '".$v['type']."'" );
				if($d->num_rows()>=1){
					$row = $d->fetch_array();
					$field=$row['id'];
					if($v['field']=='id')
						$_GET['id']=$row['tenkhongdau'];
					else
						$id_child = $row['tenkhongdau'];
					$com = $v['com'];
					break;
				}
			}
		}
	}
	
	//alert($_GET['lang']);
	switch($com){
		

		
		case 'video':
			$source = "video";
			$template = isset($_GET['id']) ? "video_detail" : "video";
			break;

		case 'thu-vien-anh':
			$source = "album";
			$template = isset($_GET['id']) ? "album_detail" : "album";
			$type_bar = 'album';
			$title_detail = "album";
			break;

		case 'catalog':
			$source = "about";
			$template = "about";
			$title_detail = "catalog";
			$type_bar = 'catalog';
			break;


	
			
		case 'gioi-thieu':
			$source = "about";
			$template = "about";
			$title_detail = _gioithieu;
			$type_bar = 'gioithieu';
			break;
	
	
		case 'chinh-sach':
			$source = "news";
			$template = isset($_GET['id']) ? "news_detail" : "news";
			$type_bar = 'chinhsach';
			$title_detail = _chinhsach;
			break;
		
		case 'tin-tuc':
			$source = "news";
			$template = isset($_GET['id']) ? "news_detail" : "news";
			$type_bar = 'tintuc';
			$title_detail = _tintuc;
			break;
		
	
		
		case 'san-pham':
			$source = "product";
			$template =isset($_GET['id']) ? "product_detail" : "product";
			$title_detail = _sanpham;
			$type_bar = 'product';	
			break;
								
		case 'lien-he':
			$source = "contact";
			$template = "contact";
			$title_detail = _lienhe;	
			break;
		
		case 'tim-kiem':
			$source = "search";
			$template = "search";
			break;
		

		case '':
			$source = "index";
			$template = "index";
			break;
		default: 
			$source = "index";
			$template = "index";
			exit();
	}
	
	if($config['index']==1){
		if($_SERVER["REQUEST_URI"]=='/index.php'){
			header("location:".$http.$config_url);
		}
	}
	
	if($source!="") include _source.$source.".php";
	/*==================== dkdn ====================*/
	if($_COOKIE['email_remember']){
		$email_remember=$_COOKIE['email_remember'];
		$d->reset();
		$d->query("select * from #_thanhvien where email='$email_remember' limit 0,1");
		$row=$d->fetch_array();
		$_SESSION['login_member']=$row;
	}
	if($_SESSION['login_member']){
		$thanhvien=$_SESSION['login_member'];
	}
	if($_REQUEST['com']=='logout')
	{
		unset($_SESSION['login_member']);
		setcookie('email_remember', $row['email'], time()-1);
		setcookie('password_remember', $row['password'], time()-1);
		header("Location:index.php");
	}		
	//end dkdn
?>