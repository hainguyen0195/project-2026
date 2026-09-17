<?php

	error_reporting(E_ALL & ~E_NOTICE);
	session_start();
	@define ( '_template' , '../templates/');
	@define ( '_source' , '../sources/');
	@define ( '_lib' , '../libraries/');

	
	
	include_once _lib."config.php";
	include_once _lib."constant.php";
	include_once _lib."functions.php";
	include_once _lib."functions_share.php";
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
	include_once _source."lang_$lang.php";	

	$page=(int)$_GET['page'];
	$per_page=(int)$_GET['per_page'];
	$type_bar=magic_quote($_GET['type_bar']);
	$url=magic_quote($_GET['url']);
	$sql="select * from";
	$startpoint = ($page * $per_page) - $per_page;
	$limit = ' limit '.$startpoint.','.$per_page;
	$where = " #_product where hienthi=1 and type='$type_bar'";
	$sql.=$where." order by stt,ngaytao desc $limit";
	$d->query($sql);
	$product = $d->result_array();
	$paging = pagination_home($where,$per_page,$page,$url);
	$result="";
	if($product){
		foreach ($product as $key => $value) {
			$result.='<div class="col_sanpham col-md-4 col-sm-4 col-xs-6">';
                $result.='<div class="box_sanpham ">';
                    $result.='<div class="img_sanpham">';
                        $result.='<a href="san-pham/'.$value['tenkhongdau'].'-'.$value['id'].'.html" title="'.$value['ten_vi'].'">
                              <img src="'.thumb($value['photo'],_upload_product_l,$value['tenkhongdau'],300,230).'" alt="'.$value['ten_'.$lang].'" class="w100 trans03"/>
                          </a>
                      </div><!--end img sanpham-->
                      <div class="name_sanpham">
                          <a href="san-pham/'.$value['tenkhongdau'].'-'.$value['id'].'.html" title="'.$value['ten_vi'].'">
                              <h2>'.$value['ten_'.$lang].'</h2>
                          </a>
                      </div><!--end ten sanpham-->
                      <div class="gia_sanpham">
                          <?=_gia?> : <span>'.($value['giaban']==0?_lienhe:number_format($value['giaban'],0,',',',')).' VNĐ</span>
                      </div><!--end gia sanpham-->
                      <div class="more_sanpham">
                          <a href="san-pham/'.$value['tenkhongdau'].'-'.$value['id'].'.html" title="'.$value['ten_vi'].'">
                              '._xemthem.'
                          </a>
                      </div><!--end more san pham-->
                      <div class="dathang_sanpham">
                          <a href="javascript:" class="add_to_basket btn_dathang" onclick="addtocart('.$value['id'].',1)">
                              '._dathang.'
                          </a>
                      </div><!--end dat hang san pham-->
                  </div><!--end box san pham-->
              </div><!--end col san pham-->';
		 }
	}

	$arr_json=array(
		'paging' => $paging,
		'result' => $result,
		'url' => $url
	);
	echo json_encode($arr_json);
?>
