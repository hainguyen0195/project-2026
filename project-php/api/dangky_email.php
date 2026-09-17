<?php
	session_start();
	@define ( '_template' , '../templates/');
	@define ( '_lib' , '../libraries/');
	@define ( '_source' , '../sources/');	
	include_once _lib."config.php";
	include_once _lib."constant.php";
	include_once _lib."functions.php";
	include_once _lib."class.database.php";
	$d = new database($config['database']);
	$ten=magic_quote($_POST['ten']);
	$dienthoai=magic_quote($_POST['dienthoai']);
	$email=magic_quote($_POST['email']);
	$d->reset();
	$sql = "select id from #_newsletter where email='".$email."'";
	$d->query($sql);
	$maill = $d->result_array();
	if(count($maill)!=0){
		echo 1;
	} else {
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptchaResponse'])) {
			$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
			$recaptcha_secret = $secretkey;
			$recaptcha_response = $_POST['recaptchaResponse'];

			$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
			$recaptcha = json_decode($recaptcha);

			if ($recaptcha->score >= 0.5) {
				if(isset($email)){
					$data['ten'] = $ten;
					$data['dienthoai'] = $dienthoai;
					$data['email'] = $email;
					$data['ngaytao'] = time();
					$d->setTable('newsletter');
					if($d->insert($data))
						echo 0;
					else
						echo 2;
				}
			} else {
				echo 3;
			}
		}
	}
?>
