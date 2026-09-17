<?php
	session_start();
	error_reporting(E_ALL & ~E_NOTICE & ~8192);

	if(!isset($_SESSION['lang']))
	{
	$_SESSION['lang']='vi';
	}
	$lang=$_SESSION['lang'];
	
	@define ( '_lib' , '../libraries/');
	@define ( '_source' , '../sources/');
    
	include_once _lib."config.php";
	include_once _lib."constant.php";;
	include_once _lib."functions.php";
	include_once _lib."functions_giohang.php";
	include_once _lib."class.database.php";
	include_once _source."lang_$lang.php";
    
	$d = new database($config['database']);

	@$pid = (int)$_POST['id'];
	if($_POST['soluong']>0){
		@$soluong = (int)$_POST['soluong'];
	}else {
		@$soluong = 1;
	}
    
    $result_giohang = addtocart($pid,$soluong);

    $d->reset();
	$sql = "select id,thumb,ten_$lang,giaban,tenkhongdau,giacu from #_product where id='".$pid."' ";
	$d->query($sql);
	$product = $d->fetch_array();
		
?>


<div class="cart-info border-grey-bottom line">

		<div class="khung_mua1">
			<h5>1 sản phẩm mới đã được thêm vào giỏ hàng của bạn</h5>
			<a href="/c-nhan-tam-tai-b-n-dale-carnegie-1203134.html?mp=1">
                <img src="<?=_upload_product_l.$product['thumb']?>" alt="<?=$product['ten_'.$lang]?>" height="117" width="117">
            </a>
            <h3><?=$product['ten_'.$lang]?></h3>
            <p class="gia_m">Giá : <?php if($product['giaban']==0) echo _lienhe; else echo number_format ($product['giaban'],0,",",",")." VNĐ";?></p>
            <?php if($product['giacu']!=0){?>
            <p class="gia_g"><span>Giá cũ : <?php if($product['giacu']==0) echo _lienhe; else echo number_format ($product['giacu'],0,",",",")." VNĐ";?> </span> <?=giamgia($product['giacu'],$product['giaban'])?> GIẢM GIÁ</p>
            <?php } ?>
		</div>

		<div class="khung_mua2">
			<p><h5>Giỏ Hàng Của Tôi </h5> ( <?=count($_SESSION['cart'])?> sản phẩm )</p>
			
            <p class="tamtinh">Tạm tính : <span><?=number_format (get_order_total(),0,",",",")." VNĐ";?> </span></p>
            <p class="thanhtien"><b>Thành tiền</b> ( Đã bao gồm VAT ) <span><b><?=number_format (get_order_total(),0,",",",")." VNĐ";?> </b> VND </span></p>
            <p><a href="trang-chu.html" class="tt_mua"> >> Tiếp tục mua hàng</a> <a href="thanh-toan.html" class="tt_dat">Đặt hàng</a></p>
		</div>

</div>