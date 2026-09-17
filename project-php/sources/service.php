<?php  if(!defined('_source')) die("Error");

		@$string =  magic_quote($id_child);
		@$id_cur= (int)$field;
		@$id=  magic_quote($_GET['id']);

		
		$title_detail = $title_detail;

		$arr_parent=array('id_list'=>'baiviet_list','id_cat'=>'baiviet_cat','id_item'=>'baiviet_item','id_sub'=>'baiviet_sub');



		if($id!=''){
			#title san pham khac
			switch ($type_bar) {
				case 'product':
					$title_other=_baivietkhac;
					break;
				
				default:
					$title_other=_baivietkhac;
					break;
			}

			$sql = "select * from #_baiviet where hienthi=1 and tenkhongdau='".$id."'";
			if($id_cur)
				$sql.=" and id=".$id_cur;
			$d->query($sql);
			$row_detail = $d->fetch_array();
			$kiemtra404=$d->num_rows();
			if($kiemtra404==0){
			//tra loi 404
				header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
				include('404.php');
				exit();
			}

			$breadcrumb.='<li class="breadcrumb-item"><a href="'.$com.'">'.$title_detail.'</a></li>';

			foreach ($arr_parent as $key => $value) { 
				if($row_detail[$key]){
					$id_list=get_all_info($row_detail[$key],$value);
					if($id_list){
						$breadcrumb.='<li class="breadcrumb-item"><a href="'.$id_list['tenkhongdau'].'">'.$id_list['ten_'.$lang].'</a></li>';
					}
				}
			}
			$breadcrumb.='<li class="breadcrumb-item active" aria-current="page">'.$row_detail['ten_'.$lang].'</li>';

			$share_facebook = '<meta property="og:url" content="'.getCurrentPageURL().'" />';
			$share_facebook .= '<meta property="og:type" content="website" />';
			$share_facebook .= '<meta property="og:title" content="'.$row_detail['ten_'.$lang].'" />';
			$share_facebook .= '<meta property="og:description" content="'.strip_tags(htmlentities($row_detail['mota_'.$lang])).'" />';
			$share_facebook .= '<meta property="og:locale" content="vi_VN" />';
			$share_facebook .= '<meta property="og:image" content="'.$http.$config_url.'/'.thumb($row_detail["photo"],_upload_baiviet_l,$row_detail["ten_".$lang],400,0,3).'" />';

			if($row_detail['title']!=''){
				$title_bar = $row_detail['title'];
			}else{
				$title_bar = $row_detail['ten_'.$lang];
			}
			$keywords_bar = $row_detail['keywords'];
			$description_bar = $row_detail['description'];
			$title_h1 = $row_detail['ten_'.$lang];

			$d->reset();
			$d->query("update table_baiviet set luotxem=".($row_detail['luotxem']+1)." where id=".$row_detail['id']);
			
			#các tin khác
			$sql_khac = "select ten_$lang,ngaytao,id,tenkhongdau,photo,mota_$lang from #_baiviet where hienthi=1 and id !='".$row_detail['id']."' and type='$type_bar' order by stt,ngaytao desc limit 0,6";
			$d->query($sql_khac);
			$tintuc = $d->result_array();

		}else if($string){ #check danh muc san pham
		
			$d->reset();
			$sql_cat="select * from #_baiviet_list where tenkhongdau='$string' and type='$type_bar'";
			if($id_cur)
				$sql_cat.=" and id=".$id_cur;

			$d->query($sql_cat);
			$row_detail=$d->fetch_array();
			$idl=$row_detail['id'];
			
			$sql="select * from";
			if($idl){
				$where = " #_baiviet where hienthi=1 and type='$type_bar' and id_list=$idl";
			}
			if(!$idl){
				$d->reset();
				$sql_cat="select * from #_baiviet_cat where tenkhongdau='$string' and type='$type_bar'";
				if($id_cur)
					$sql_cat.=" and id=".$id_cur;
				$d->query($sql_cat);
				$row_detail=$d->fetch_array();
				$idc=$row_detail['id'];
				$where = " #_baiviet where hienthi=1 and type='$type_bar' and id_cat=$idc";
			}

			if(!$idl&&!$idc){
				$d->reset();
				$sql_cat="select * from #_baiviet_item where tenkhongdau='$string' and type='$type_bar'";
				$d->query($sql_cat);
				$row_detail=$d->fetch_array();
				$idi=$row_detail['id'];
				if($id_cur)
					$sql_cat.=" and id=".$id_cur;
				$where = " #_baiviet where hienthi=1 and type='$type_bar' and id_item=$idi";
			}
			if(!$idl&&!$idc&&!$idi){
				$d->reset();
				$sql_cat="select * from #_baiviet_sub where tenkhongdau='$string' and type='$type_bar'";
				$d->query($sql_cat);
				$row_detail=$d->fetch_array();
				$ids=$row_detail['id'];
				if($id_cur)
					$sql_cat.=" and id=".$id_cur;
				$where = " #_baiviet where hienthi=1 and type='$type_bar' and id_sub=$ids";
			}
			if(!$idl&&!$idc&&!$idi&&!$ids){
			//tra loi 404
				header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
				include('404.php');
				exit();
			}
			$per_page = $row_setting['page_tintuc'] > 0?$row_setting['page_tintuc']:12; // Set how many records do you want to display per page.
			$startpoint = ($page * $per_page) - $per_page;
			$limit = ' limit '.$startpoint.','.$per_page;
			$sql.=$where." order by stt,ngaytao desc $limit";
			
			$d->query($sql);
			$tintuc = $d->result_array();
			
			$url = getCurrentPageURL();
			$paging = pagination_home($where,$per_page,$page,$url);

			$breadcrumb.='<li class="breadcrumb-item"><a href="'.$com.'">'.$title_detail.'</a></li>';
			foreach ($arr_parent as $key => $value) { 
				if($row_detail[$key]){
					$id_list=get_all_info($row_detail[$key],$value);
					if($id_list){
						$breadcrumb.='<li class="breadcrumb-item"><a href="'.$id_list['tenkhongdau'].'">'.$id_list['ten_'.$lang].'</a></li>';
					}
				}
			}

			$breadcrumb.='<li class="breadcrumb-item active" aria-current="page">'.$row_detail['ten_'.$lang].'</li>';

			$title_detail = $row_detail['ten_'.$lang];
			if($row_detail['title']!=''){
				$title_bar = $row_detail['title'];
			}else{
				$title_bar = $row_detail['ten_'.$lang];
			}
			$keywords_bar = $row_detail['keywords'];
			$description_bar = $row_detail['description'];
			$title_h1 = $row_detail['ten_'.$lang];
			
			$share_facebook = '<meta property="og:url" content="'.getCurrentPageURL().'" />';
			$share_facebook .= '<meta property="og:type" content="website" />';
			$share_facebook .= '<meta property="og:title" content="'.$row_detail['ten_'.$lang].'" />';
			$share_facebook .= '<meta property="og:description" content="'.$row_detail['mota_'.$lang].'" />';
			$share_facebook .= '<meta property="og:locale" content="vi_VN" />';
			$share_facebook .= '<meta property="og:image" content="'.$http.$config_url.'/'.thumb($row_detail["photo"],_upload_baiviet_l,$row_detail["ten_".$lang],400,0,3).'" />';

		}else{
			$d->reset();
			$d->query("select title,keywords,description,photo from #_title where type='".$type_bar."' limit 0,1");
			$row_detail = $d->fetch_array();
			$title_bar = $row_detail['title'];
			$keywords_bar = $row_detail['keywords'];
			$description_bar = $row_detail['description'];
			$title_h1 = $title_detail;

			$share_facebook = '<meta property="og:url" content="'.getCurrentPageURL().'" />';
			$share_facebook .= '<meta property="og:type" content="website" />';
			$share_facebook .= '<meta property="og:title" content="'.$row_detail['title'].'" />';
			$share_facebook .= '<meta property="og:description" content="'.$row_detail['description'].'" />';
			$share_facebook .= '<meta property="og:locale" content="vi_VN" />';
			$share_facebook .= '<meta property="og:image" content="'.$http.$config_url.'/'.thumb($row_detail["photo"],_upload_seo_l,$row_detail["ten_".$lang],400,0,3).'" />';
			
			$breadcrumb.='<li class="breadcrumb-item active" aria-current="page">'.$title_detail.'</li>';
			$sql="select * from";
			$per_page = $row_setting['page_tintuc'] > 0?$row_setting['page_tintuc']:12; // Set how many records do you want to display per page.
			$startpoint = ($page * $per_page) - $per_page;
			$limit = ' limit '.$startpoint.','.$per_page;
			$where = " #_baiviet where hienthi=1 and type='$type_bar'";
			$sql.=$where." order by stt,ngaytao desc $limit";
			$d->query($sql);
			$tintuc = $d->result_array();
			$url = getCurrentPageURL();
			$paging = pagination_home($where,$per_page,$page,$url);

			
		}
	
?>