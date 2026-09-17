<?php  if(!defined('_source')) die("Error");
		
		$per_page = 20; // Set how many records do you want to display per page.
		$startpoint = ($page * $per_page) - $per_page;
		$limit = ' limit '.$startpoint.','.$per_page;
		
		$where = " #_hoidap where hienthi=1 order by id desc";

		$sql = "select * from $where $limit";
		$d->query($sql);
		$tintuc = $d->result_array();

		$url = getCurrentPageURL();
		$paging = pagination($where,$per_page,$page,$url);
		
		if(!empty($_POST)){

		require_once LIBRARIES . "PHPMailer/Exception.php";
		require_once LIBRARIES . "PHPMailer/PHPMailer.php";
		require_once LIBRARIES . "PHPMailer/SMTP.php";
		$mail = new PHPMailer\PHPMailer\PHPMailer();
		$mail->isSMTP(); // Gọi đến class xử lý SMTP
		$mail->Host       = $config_ip; // tên SMTP server
		$mail->SMTPAuth   = true;                  // Sử dụng đăng nhập vào account
		$mail->Username   = $config_email; // SMTP account username
		$mail->Password   = $config_pass;  

		//Thiet lap thong tin nguoi gui va email nguoi gui
		$mail->setFrom($config_email,$row_setting['ten_'.$lang]);
		
		$mail->addAddress($row_setting['email'],$row_setting['ten_'.$lang]);
		
		/*=====================================
		 * THIET LAP NOI DUNG EMAIL
 		*=====================================*/

		//Thiết lập tiêu đề
		$mail->Subject    = '['.$_POST['ten'].']';
		$mail->isHTML(true);
		//Thiết lập định dạng font chữ
		$mail->CharSet = "utf-8";	
			$body = '<table>';
			$body .= '
				<tr> 
					<th colspan="2">&nbsp;</th>
				</tr>

				<tr>
					<th colspan="2">Thông tin hỏi đáp từ website <a href="http://'.$config_url.'">www.'.$config_url.'</a></th>
				</tr>

				<tr>
					<th colspan="2">&nbsp;</th>
				</tr>

				<tr>
					<th>Họ tên :</th><td>'.$_POST['ten'].'</td>
				</tr>

				<tr>
					<th>Điện thoại :</th><td>'.$_POST['dienthoai'].'</td>
				</tr>

				<tr>
					<th>Email :</th><td>'.$_POST['email'].'</td>
				</tr>
				
				<tr>
					<th>Tiêu đề :</th><td>'.$_POST['tieude'].'</td>
				</tr>
			
				<tr>
					<th>Câu hỏi :</th><td>'.$_POST['cauhoi'].'</td>
				</tr>';
			$body .= '</table>';

			$data1['ten'] = magic_quote($_POST['ten']);
			$data1['diachi'] = magic_quote($_POST['diachi']);
			$data1['dienthoai'] = magic_quote($_POST['dienthoai']);
			$data1['email'] = magic_quote($_POST['email']);
			$data1['tieude'] = magic_quote($_POST['tieude']);
			$data1['cauhoi'] = magic_quote($_POST['cauhoi']);
			$data1['stt'] = (int)$_POST['stt'];
			$data1['hienthi'] = 0;

			$data1['ngaytao'] = time();
			$d->setTable('hoidap');

			$d->insert($data1);
			
				
			$mail->Body = $body;

			
			
			if($mail->send())
			{	
				transfer("Câu hỏi đã được gửi . Cảm ơn.", "y-kien-khach-hang.html");
			} else {
				transfer("Xin lỗi quý khách.<br>Hệ thống bị lỗi, xin quý khách thử lại.", "y-kien-khach-hang.html",false);
			}
		}
			
	
?>