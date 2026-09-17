<?php
	session_start();
	@define ( '_template' , './templates/');
	@define ( '_source' , './sources/');
	@define ( '_lib' , './libraries/');
	
	if(!isset($_SESSION['lang']))
	{
	$_SESSION['lang']='vi';
	}
	$lang='vi';
	
	include_once _lib."config.php";
	include_once _lib."constant.php";
	include_once _lib."functions.php";
	include_once _lib."functions_share.php";
	include_once _lib."class.database.php";
	include_once _source."lang_$lang.php";
	include_once _lib."functions_giohang.php";
	include_once _lib."file_requick.php";
	include_once _lib."counter.php"; 
	
	if($_REQUEST['command']=='add' && $_REQUEST['productid']>0){
		$pid=$_REQUEST['productid'];
		$soluong=1;
		addtocart($pid,$soluong);
		redirect("thanh-toan.html");
	}
	
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<base href="http://<?=$config_url?>/">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link id="favicon" rel="shortcut icon" href="<?=_upload_hinhanh_l.$favicon['thumb_'.$lang]?>" type="image/x-icon" />

<title><?php if($title_bar!='') echo $title_bar; else echo $row_setting['title']; ?></title>
<meta name="description" content="<?php if($description_bar!='') echo $description_bar; else echo $row_setting['description']; ?>">
<meta name="keywords" content="<?php if($keywords_bar!='') echo $keywords_bar; else echo $row_setting['keywords']; ?>">

<meta name="robots" content="noodp,index,follow" />
<meta name="google" content="notranslate" />
<meta name='revisit-after' content='1 days' />
<meta name="ICBM" content="<?=$row_setting['toado']?>">
<meta name="geo.position" content="<?=$row_setting['toado']?>">
<meta name="geo.placename" content="<?=$row_setting['diachi_'.$lang]?>">
<meta name="author" content="<?=$row_setting['ten_'.$lang]?>">
<?=$share_facebook?>

<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/intro.css">
<link rel="stylesheet" type="text/css" href="css/animate.css">

<script language="javascript" type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
<script type='text/javascript' src='js/jquery.cookie.js'></script>
<script type="text/javascript" src="js/jquery.animate-colors-min.js"></script>


<script src="js/wow.min.js"></script>
<script>
 new WOW().init();
</script>

</head>

<body>
<div class="body-intro">
	<img src="images/intro-bg.png">
	<div class="center-box">
		<div id="center-box">
			<div class="items-pages">
				<div class="left-cat">
					<img class="wow fadeInRight" src="images/left-cat.png">					
				</div>
				<div class="logo-center">
					<h3 id="left-center"><a href="index.php?com=hinhthuc&value=2">Sản phẩm cho thuê</a></h3>
					<img class="wow fadeInDown" src="images/logo-intro.png">
					<h3 id="right-center"><a href="index.php?com=hinhthuc&value=1">Sản phẩm bán</a></h3>
				</div>
				<div class="right-cat">
					<img class="wow fadeInLeft" src="images/right-cat.png">					
				</div>
			</div>			
		</div>
	</div>
</div>
</body>
</html>
