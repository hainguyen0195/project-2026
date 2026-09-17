<?php  if(!defined('_source')) die("Error");

	
	$id =  magic_quote($_GET['id']);
	$arr_parent=array('id_list'=>'album_list');
	if ($id!='') {
		$sql = "select * from #_album where hienthi=1 and tenkhongdau='".$id."'";
		$d->query($sql);
		$album_detail = $d->fetch_array();
		$kiemtra404=$d->num_rows();
		if($kiemtra404==0){
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
			include('404.php');
			exit();
		}

		$title_detail = $album_detail['ten_'.$lang];

		if($album_detail['title']!=''){
			$title_bar = $album_detail['title'];
		}else{
			$title_bar = $album_detail['ten_'.$lang];
		}
		$keyword_bar .= $album_detail['keywords'];
		$description_bar .= $album_detail['description'];
		$title_h1 = $album_detail['ten_'.$lang];

		$breadcrumb.='<li class="breadcrumb-item"><a href="'.$com.'">'.$title_detail.'</a></li>';

		foreach ($arr_parent as $key => $value) { 
			if($album_detail[$key]){
				$id_list=get_all_info($album_detail[$key],$value);
				if($id_list){
					$breadcrumb.='<li class="breadcrumb-item"><a href="'.$id_list['tenkhongdau'].'">'.$id_list['ten_'.$lang].'</a></li>';
				}
			}
		}
		$breadcrumb.='<li class="breadcrumb-item active" aria-current="page">'.$album_detail['ten_'.$lang].'</li>';

		$share_facebook = '<meta property="og:url" content="'.getCurrentPageURL().'" />';
		$share_facebook .= '<meta property="og:type" content="website" />';
		$share_facebook .= '<meta property="og:title" content="'.$album_detail['ten_'.$lang].'" />';
		$share_facebook .= '<meta property="og:description" content="'.strip_tags(htmlentities($row_detail['mota_'.$lang])).'" />';
		$share_facebook .= '<meta property="og:locale" content="vi" />';
		$share_facebook .= '<meta property="og:image" content="'.$http.$config_url.'/'.thumb($row_detail["photo"],_upload_album_l,$row_detail["ten_".$lang],400,0,3).'" />';

		
		#các tin cu hon
		$sql_khac = "select * from #_album_photo where hienthi=1 and id_album ='".$album_detail['id']."' order by id desc";
		$d->query($sql_khac);
		$album_images = $d->result_array();

	} else {
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
		$per_page = 15; // Set how many records do you want to display per page.
		$startpoint = ($page * $per_page) - $per_page;
		$limit = ' limit '.$startpoint.','.$per_page;
		
		$where = " #_album where hienthi=1 and type='$type_bar' order by id desc";

		$sql = "select * from $where $limit";
		$d->query($sql);
		$album = $d->result_array();

		$url = getCurrentPageURL();
		$paging = pagination_home($where,$per_page,$page,$url);
		
		

	}
?>