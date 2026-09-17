<?php
	session_start();
	@define ( '_template' , '../templates/');
	@define ( '_lib' , '../libraries/');
	@define ( '_source' , '../sources/');	
	if(!isset($_SESSION['lang']))
	{
	$_SESSION['lang']='vi';
	}
	$lang=$_SESSION['lang'];

	include_once _lib."config.php";
	include_once _lib."constant.php";
	include_once _lib."functions.php";
	include_once _lib."class.database.php";
	$d = new database($config['database']);
	
	$id=(int)$_POST['id'];
	$table=magic_quote($_POST['table']);
	$type=magic_quote($_POST['type']);

	$d->reset();
	if($table=='info'){
		$sql = "select noidung_$lang,ten_$lang from #_$table where type='".$type."' ";

	} else {
		$sql = "select noidung_$lang,ten_$lang from #_$table where type='".$type."' and id='".$id."' ";
	}
	$d->query($sql);
	$dichvu_cty = $d->fetch_array();

?>
<div class="noidung_load">
	<h2><?php if($table=='info'){ echo "Áo cưới" ; } else { echo $dichvu_cty['ten_'.$lang]; }?></h2>
	<p><?=$dichvu_cty['noidung_'.$lang]?></p>
</div>



