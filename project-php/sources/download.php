<?php  if(!defined('_source')) die("Error");

		@$id=  magic_quote($_GET['id']);
		
		if($id!=''){

			$sql = "select * from #_download where hienthi=1 and tenkhongdau='".$id."'";
			$d->query($sql);
			$row_detail = $d->fetch_array();

			if($row_detail['title']!=''){
				$title_bar = $row_detail['title'];
			}else{
				$title_bar = $row_detail['ten_'.$lang];
			}
			$keywords_bar = $row_detail['keywords'];
			$description_bar = $row_detail['description'];
			$title_h1 = $row_detail['ten_'.$lang];
			
			#các tin cu hon
			$sql_khac = "select * from #_download where hienthi=1 and id !='".$row_detail['id']."' and type='".$type_bar."' order by stt,ngaytao desc limit 0,10";
			$d->query($sql_khac);
			$tintuc = $d->result_array();

		} else {
			$d->reset();
			$d->query("select title,keywords,description,photo from #_title where type='".$type_bar."' limit 0,1");
			$row_detail = $d->fetch_array();
			$title_bar = $row_detail['title'];
			$keywords_bar = $row_detail['keywords'];
			$description_bar = $row_detail['description'];
			$title_h1 = $title_detail;
			
			// cac tin tuc
			$per_page = 10; // Set how many records do you want to display per page.
			$startpoint = ($page * $per_page) - $per_page;
			$limit = ' limit '.$startpoint.','.$per_page;
			
			$where = " #_download where hienthi=1 and type='".$type_bar."' order by id desc";

			$sql = "select * from $where $limit";
			$d->query($sql);
			$tintuc = $d->result_array();

			$url = getCurrentPageURL();
			$paging = pagination($where,$per_page,$page,$url);
		}
	
?>