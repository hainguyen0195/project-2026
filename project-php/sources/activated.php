<?php if(!defined('_source')) die("Error");
		
		$kichhoat = magic_quote($_GET['id_kichhoat']);
		$d->reset();
		$sql = "select * from #_thanhvien where mathanhvien='".$kichhoat."'";
		$d->query($sql);
		$row= $d->fetch_array();
		
		$sqlUPDATE_ORDER = "UPDATE table_thanhvien SET hienthi=1 WHERE mathanhvien='$kichhoat'";
		$resultUPDATE_ORDER = mysql_query($sqlUPDATE_ORDER) or die("Not query sqlUPDATE_ORDER");

		$_SESSION[$name_log]= $row;
		
		$title_bar = _kichhoattaikhoan;
	
?>