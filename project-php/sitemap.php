<?php 
	session_start();
	@define ( '_lib' , './libraries/');
	include_once _lib."config.php";
	// include_once _lib."checkSSL.php";
	include_once _lib."class.database.php";
	$d = new database($config['database']);
	$d->reset();
	$sql_setting = "select * from #_setting limit 0,1";
	$d->query($sql_setting);
	$row_setting= $d->fetch_array();
	
	if(!isset($_SESSION['lang'])){
		$_SESSION['lang']=$row_setting['lang'];
	}
	$lang=$_SESSION['lang'];
	include_once _lib."file_requick.php";

	

	$time_sitemap = time();
	function get_http(){
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		return $pageURL;
	}
	function urlElement($url,$pri,$time) {
		global $config_url; 
		$url = get_http().$config_url.$url;
		$str_sitemap='<url>'; 
		$str_sitemap.='<loc>'.$url.'</loc>'; 
		$str_sitemap.='<lastmod>'.date("c",$time).'</lastmod>';
		$str_sitemap.='<changefreq>weekly</changefreq>'; 
		$str_sitemap.='<priority>'.$pri.'</priority>';
		$str_sitemap.='</url>';
		echo $str_sitemap;
	} 
	function CreateXML2($tbl='',$type='',$priority=1){
		global $d;
		if($tbl=='') return false;
		$d->reset();
		$sql = "SELECT tenkhongdau,ngaytao FROM table_$tbl where type='".$type."' and hienthi=1 order by ngaytao desc";
		$d->query($sql);
		$result_data = $d->result_array();
		foreach ($result_data as $key => $v) { 
			urlElement('/'.$v['tenkhongdau'],$priority,$v['ngaytao']);
		}	
	}
	header("Content-Type: application/xml; charset=utf-8"); 
	echo '<?xml version="1.0" encoding="UTF-8"?>'; 
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'; 
	urlElement('','1.0',$time_sitemap); 
	foreach ($data_tpl as $k => $v) {
		$priority = $v['field']=='id' ? "1.0" : "0.8";
		if($v['field']=='id'){
			urlElement('/'.$v['com'],$priority,$time_sitemap); 
		}
		if($v['tbl']!='info'){
			CreateXML2($v['tbl'],$v['type'],$priority);
		}
	}
	echo '</urlset>'; 
?>