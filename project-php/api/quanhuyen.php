<?php
	session_start();
	error_reporting(E_ALL & ~E_NOTICE & ~8192);

	if(!isset($_SESSION['lang']))
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
	
	$id_tinh = $_POST['id_tinh'];
		
	$d->reset();
	$sql = "select ten_vi,id from #_chinhanh_cat where type='chinhanh' and hienthi=1 and id_list ='".$id_tinh."' order by ten_vi,id desc ";
	$d->query($sql);
	$quanhuyen = $d->result_array();
	
?>
<option value="">Chọn quận/huyện</option>
<?php foreach ($quanhuyen as $key => $value){ ?>
	<option value="<?=$value['id']?>"><?=$value['ten_vi']?></option>
<?php } ?>
