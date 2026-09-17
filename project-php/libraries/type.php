<?php
	$type = (isset($_REQUEST['type'])) ? addslashes($_REQUEST['type']) : "";	
	$act = (isset($_REQUEST['act'])) ? addslashes($_REQUEST['act']) : "";
	$act = explode('_',$act);
	if(count($act>1)){
		$act = $act[1];
	} else {
		$act = $act[0];
	}
	switch($type){
		//-------------product------------------
		case 'product':
			switch($act){
				case 'list':
					$title_main = "Danh mục cấp 1";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					$config_noibat= "false";
					$config_menu= "false";
					@define ( _width_thumb , 550 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;
				case 'cat':
					$title_main = "Danh mục cấp 2";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					$config_noibat= "false";
					@define ( _width_thumb , 555 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;
				case 'item':
					$title_main = "Danh mục cấp 3";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					@define ( _width_thumb , 555 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;

				case 'sub':
					$title_main = "Danh mục cấp 4";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					@define ( _width_thumb , 555 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;
				
				default:
					$title_main = "Sản Phẩm";
					$config_images = "true";
					$config_alt = "false";
					$config_mota= "true";
					$config_list = "true";
					$config_cat = "true";
					$config_item = "false";
					$config_sub = "false";
					$config_noibat = "true";
					$config_khuyenmai = "false";
					$config_moi = "false";
					$config_banchay = "false";
					@define ( _width_thumb , 290 );
					@define ( _height_thumb , 290 );
					@define ( _style_thumb , 1 );
					$ratio_ = 3;
					break;
				}
				@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			break;
			
			case 'tintuc':
			switch($act){
				case 'list':
					$title_main = "Danh mục cấp 1";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					@define ( _width_thumb , 550 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;
				case 'cat':
					$title_main = "Danh mục cấp 2";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					@define ( _width_thumb , 555 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;
				case 'item':
					$title_main = "Danh mục cấp 3";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					@define ( _width_thumb , 555 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;

				case 'sub':
					$title_main = "Danh mục cấp 4";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					@define ( _width_thumb , 555 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;
				
				default:
					$title_main = "Tin tức";
					$config_images = "true";
					$config_alt = "false";
					$config_mota= "true";
					$config_noidung= "true";
					$config_seo= "true";
					$config_noibat= "true";
					$config_list = "false";
					$config_cat = "false";
					$config_item = "false";
					$config_sub = "false";
					@define ( _width_thumb , 210 );
					@define ( _height_thumb , 170 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;
				}
				@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			break;

		case 'chinhanh':
		$title_list="Tỉnh thành";
		$title_cat="Quận huyện";
		$title_item="Phường xã";
		$title_sub="Đường phố";
			switch($act){
				case 'list':
					$title_main = "Tỉnh thành";
					$config_ceo = "false";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					@define ( _width_thumb , 550 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;
				case 'cat':
					$title_main = "Quận huyện";
					$config_ceo = "false";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					@define ( _width_thumb , 555 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;
				case 'item':
					$title_main = "Phường xã";
					$config_ceo = "false";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					@define ( _width_thumb , 555 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;

				case 'sub':
					$title_main = "Đường phố";
					$config_ceo = "false";
					$config_images = "false";
					$config_mota= "false";
					$config_noidung= "false";
					@define ( _width_thumb , 555 );
					@define ( _height_thumb , 232 );
					@define ( _style_thumb , 1 );
					$ratio_ = 1;
					break;
				
				default:
					$title_main = "Chi nhánh";
					$config_ceo = "false";
					$config_images = "false";
					$config_mota= "true";
					$config_noidung= "false";
					$config_noibat= "false";
					$config_list = "true";
					$config_cat = "false";
					$config_item = "false";
					$config_sub = "false";
					@define ( _width_thumb , 290 );
					@define ( _height_thumb , 290 );
					@define ( _style_thumb , 1 );
					$ratio_ = 3;
					break;
				}
				@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			break;


		case 'dichvu':
			$title_main = "Dịch vụ";
			$config_images = "true";
			$config_mota= "true";
			$config_noidung= "true";
			$config_seo= "true";
			$config_list = "false";
			$config_cat = "false";
			$config_item = "false";
			$config_sub = "false";
			$config_alt = "false";
			$config_noibat = "false";
			@define ( _width_thumb , 210 );
			@define ( _height_thumb , 170 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;

		case 'chinhsach':
			$title_main = "Chính sách";
			$config_images = "true";
			$config_mota= "true";
			$config_noidung= "true";
			$config_seo= "true";
			$config_list = "false";
			$config_cat = "false";
			$config_item = "false";
			$config_sub = "false";
			$config_alt = "false";
			$config_noibat = "true";
			@define ( _width_thumb , 210 );
			@define ( _height_thumb , 170 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;

		
		case 'download':
			$title_main = "Download File";
			$config_images = "true";
			@define ( _width_thumb , 200 );
			@define ( _height_thumb , 250 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;
		
		case 'album':
			$title_main = "Album";
			$config_images = "true";
			$config_list = "false";
			$config_mota= "true";
			$config_seo= "true";
			$config_noidung= "true";
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			@define ( _width_thumb , 375 );
			@define ( _height_thumb , 260 );
			@define ( _style_thumb , 1 );
			$ratio_ = 2;
			break;

		case 'album_list':
			$title_main = "danh mục album";
			$config_images = "true";
			$config_list = "false";
			@define ( _width_thumb , 200 );
			@define ( _height_thumb , 160 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;
		//-------------info------------------
		case 'gioithieu':
			$title_main = 'Giới thiệu';
			$config_ten = 'false';
			$config_mota = 'false';
			$config_images = 'false';
			$config_background = 'false';
			@define ( _width_thumb , 300 );
			@define ( _height_thumb , 300 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			break;

		case 'bocongthuong':
			$title_main = 'Bộ công thương';
			$config_ten = 'false';
			$config_mota = 'false';
			$config_images = 'true';
			$config_background = 'false';
			$links_ = 'true';
			$config_ck = 'true';
			@define ( _width_thumb , 135 );
			@define ( _height_thumb , 51 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			break;

		case 'tags':
			$title_main = 'tags';
			$config_ten = 'true';
			break;

		case 'httt':
			$title_main = 'Hình thức thanh toán';
			$config_ten = 'true';
			$config_mota = 'true';
			break;
			
		case 'trangchu':
			$title_main = 'Trang chủ';
			$config_images = 'true';
			$config_ten = 'true';
			@define ( _width_thumb , 590 );
			@define ( _height_thumb , 260 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;

		case 'hinhanh':
			$title_main = 'Hình ảnh';
			$config_ten = 'true';
			break;
		case 'chamsoc':
			$title_main = 'chăm sóc khách hàng';
			$config_ten = 'true';
			break;
		case 'lienhe':
			$title_main = 'Liên hệ';
			$config_ten = 'true';
			break;

		case 'logo':
			$title_main = 'Logo';
			@define ( _width_thumb , 609 );
			@define ( _height_thumb , 153 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;
		
		case 'banner':
			$title_main = 'Banner';
			@define ( _width_thumb , 633 );
			@define ( _height_thumb , 99 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;

		case 'br_banner':
			$title_main = 'Background Banner';
			@define ( _width_thumb , 1366 );
			@define ( _height_thumb , 153 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF|swf' );
			$ratio_ = 1;
			break;

		case 'dongdau':
			$title_main = 'Đóng dấu';
			$config_vitri = 'true';
			@define ( _width_thumb , 150 );
			@define ( _height_thumb , 50 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;

		case 'share':
			$title_main = 'Chia sẻ mặc định';
			@define ( _width_thumb , 400 );
			@define ( _height_thumb , 400 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;

			
		case 'popup':
			$title_main = 'Popup';
			$links_ = 'true';
			$config_hienthi = 'true';
			@define ( _width_thumb , 900 );
			@define ( _height_thumb , 500 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;

		case 'bando':
			$title_main = 'Bản đồ';
			$links_ = 'true';
			$config_mota = 'true';
			$config_hienthi = 'true';
			@define ( _width_thumb , 1200 );
			@define ( _height_thumb , 400 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;

		case 'favicon':
			$title_main = 'Favicon';
			@define ( _width_thumb , 40 );
			@define ( _height_thumb , 40 );
			@define ( _style_thumb , 2 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;

		case 'bgweb':
			$title_main = 'background web';
			@define ( _width_thumb , 500 );
			@define ( _height_thumb , 120 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;

		case 'yahoo':
			$title_main = 'background web';
			@define ( _width_thumb , 30 );
			@define ( _height_thumb , 30 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;
		//-------------photo------------------
		case 'slider':
			$title_main = "Hình ảnh slider";
			$config_list = "false";
			@define ( _width_thumb , 1366 );
			@define ( _height_thumb , 512 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			$links_ = "true";
			break;

		case 'doitac':
		    $title_main = "Đối tác";
		    $config_multi_lang = "false";
			@define ( _width_thumb , 150 );
			@define ( _height_thumb , 100 );
			@define ( _style_thumb , 2 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			$links_ = "true";
			break;

		case 'video':
		    $title_main = "Video";
		    $config_images= "true";
		    $config_seo= "false";
		    $config_multi_lang = "false";
			@define ( _width_thumb , 150 );
			@define ( _height_thumb , 100 );
			@define ( _style_thumb , 2 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			$links_ = "true";
			break;

		case 'adsmenu':
		    $title_main = "Quảng cáo theo danh mục";
		    $config_list = "true";
		    $config_multi_lang = "true";
			@define ( _width_thumb , 260 );
			@define ( _height_thumb , 234 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;
		#lkweb
		case 'mxh':
		    $title_main = "Mạng xã hội";
		    $config_link = "true";
		    $config_images= "true";
			$config_ngonngu= "false";
			@define ( _width_thumb , 36 );
			@define ( _height_thumb , 36 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;

		case 'footer_item':
		    $title_main = "Mạng xã hội footer";
		    $config_link = "true";
		    $config_images= "true";
			$config_ngonngu= "false";
			@define ( _width_thumb , 36 );
			@define ( _height_thumb , 36 );
			@define ( _style_thumb , 1 );
			@define ( _img_type , 'jpg|gif|png|jpeg|PNG|JPG|JPEG|GIF' );
			$ratio_ = 1;
			break;
		//--------------defaut---------------
		default: 
			$source = "";
			$template = "index";
			break;
	}

?>