<?php
	session_start();
	@define ( '_template' , '../templates/');
	@define ( '_lib' , '../libraries/');
	@define ( '_source' , '../sources/');	
	include_once _lib."config.php";
	include_once _lib."constant.php";
	include_once _lib."functions.php";
	include_once _lib."class.database.php";
	$d = new database($config['database']);
	
	$links=magic_quote($_POST['links']);

?>
<link rel="stylesheet" type="text/css" href="zoom/cloud-zoom.css" />
	<a href="<?=$links?>" class="group2 cloud-zoom" rev="group1" rel="zoomHeight:300, zoomWidth:445, adjustX: 10, adjustY:-4, position:'body'" ><img src="<?=$links?>" alt="hình ảnh" width="350" /></a>
    
<script type="text/javascript" src="zoom/cloud-zoom.1.0.2.js"></script>
