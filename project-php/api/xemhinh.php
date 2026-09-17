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

	$d->reset();
	$sql = "select id,ten_$lang from #_album where type='album' and id='".$id."' ";
	$d->query($sql);
	$album = $d->fetch_array();

	$d->reset();
	$sql = "select * from #_album_photo where id_album='".$id."' ";
	$d->query($sql);
	$album_photo = $d->result_array();

?>
<style type="text/css">
	.item_hinh img{ width:100%; margin:10px 0px 0px 0px;}
	.item_hinh:hover{ opacity:0.8;} 
</style>
<div class="noidung_load">
	<h2><?=$album['ten_'.$lang]?></h2>
<div class="col-md-12 list_hinhanh">

	<?php for($i=0;$i<count($album_photo);$i++){?>
		<div class="item_hinh group2 col-md-4 col-sm-4 col-xx-6 col-xs-12"><a class="phongto" href="<?=_upload_album_l.$album_photo[$i]['photo']?>"><img src="<?=_upload_album_l.$album_photo[$i]['thumb']?>" alt="<?=$album_photo[$i]['ten_'.$lang]?>"></a></div>
	<?php } ?>
</div>
</div>




