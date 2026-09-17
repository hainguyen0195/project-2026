<?php
if (!defined('SOURCES')) die("Error");

$action = htmlspecialchars($match['params']['action']);

switch ($action) {
    case 'dang-nhap':
        $titleMain = dangnhap;
        $template = "account/login";
        if (!empty($_SESSION[$loginMember]['active'])) $func->transfer(trangkhongtontai, $configBase, false);
        if (!empty($_POST['login-user'])) loginMember();
        break;

    case 'dang-ky':
        $titleMain = dangky;
        $template = "account/registration";
        if (!empty($_SESSION[$loginMember]['active'])) $func->transfer(trangkhongtontai, $configBase, false);
        if (!empty($_POST['registration-user'])) signupMember();
        break;

    case 'quen-mat-khau':
        $titleMain = quenmatkhau;
        $template = "account/forgot_password";
        if (!empty($_SESSION[$loginMember]['active'])) $func->transfer(trangkhongtontai, $configBase, false);
        if (!empty($_POST['forgot-password-user'])) forgotPasswordMember();
        break;

    case 'kich-hoat':
        $titleMain = kichhoat;
        $template = "account/activation";
        if (!empty($_SESSION[$loginMember]['active'])) $func->transfer(trangkhongtontai, $configBase, false);
        checkActivationMember();
        break;

    case 'thong-tin':
        $titleMain = capnhatthongtin;
        $template = "account/info";
        if (empty($_SESSION[$loginMember]['active'])) $func->transfer(trangkhongtontai, $configBase, false);
        infoMember();
        break;

    case 'lich-su-mua-hang':
        $titleMain = lichsumuahang;
        $template = "account/order";
        if (empty($_SESSION[$loginMember]['active'])) $func->transfer(trangkhongtontai, $configBase, false);
        if (!empty($_GET['code'])) orderMemberDetail($_GET['code']);
        else orderMember();
        break;

    case 'dang-xuat':
        if (empty($_SESSION[$loginMember]['active'])) $func->transfer(trangkhongtontai, $configBase, false);
        logoutMember();

    default:
        header('HTTP/1.0 404 Not Found', true, 404);
        include("404.php");
        exit();
}

/* SEO */
$seo->set('title', $titleMain);

/* breadCrumbs */
if (!empty($titleMain)) $breadcr->set('', $titleMain);
$breadcrumbs = $breadcr->get();

function infoMember()
{
    global $d, $func, $flash, $rowDetail, $configBase, $loginMember;

    $iduser = $_SESSION[$loginMember]['id'];

    if ($iduser) {
        $rowDetail = $d->rawQueryOne("select fullname, username, gender, birthday, email, phone, address from #_member where id = ? limit 0,1", array($iduser));

        if (!empty($_POST['info-user'])) {
            $message = '';
            $response = array();
            $old_password = (!empty($_POST['old-password'])) ? $_POST['old-password'] : '';
            $old_passwordMD5 = md5($old_password);
            $new_password = (!empty($_POST['new-password'])) ? $_POST['new-password'] : '';
            $new_passwordMD5 = md5($new_password);
            $new_password_confirm = (!empty($_POST['new-password-confirm'])) ? $_POST['new-password-confirm'] : '';
            $fullname = (!empty($_POST['fullname'])) ? htmlspecialchars($_POST['fullname']) : '';
            $email = (!empty($_POST['email'])) ? htmlspecialchars($_POST['email']) : '';
            $phone = (!empty($_POST['phone'])) ? htmlspecialchars($_POST['phone']) : 0;
            $address = (!empty($_POST['address'])) ? htmlspecialchars($_POST['address']) : '';
            $gender = (!empty($_POST['gender'])) ? htmlspecialchars($_POST['gender']) : 0;
            $birthday = (!empty($_POST['birthday'])) ? htmlspecialchars($_POST['birthday']) : '';

            /* Valid data */
            if (empty($fullname)) {
                $response['messages'][] = hotenkhongduoctrong;
            }

            if (!empty($old_password)) {
                $isWrongPass = false;
                $row = $d->rawQueryOne("select id from #_member where id = ? and password = ? limit 0,1", array($iduser, $old_passwordMD5));

                if (empty($row['id'])) {
                    $isWrongPass = true;
                    $response['messages'][] = matkhaucukhongchinhxac;
                } else if (empty($new_password)) {
                    $isWrongPass = true;
                    $response['messages'][] = matkhaumoikhongduoctrong;
                } else if (!empty($new_password) && empty($new_password_confirm)) {
                    $isWrongPass = true;
                    $response['messages'][] = xacnhanmatkhaumoikhongduoctrong;
                } else if ($new_password != $new_password_confirm) {
                    $isWrongPass = true;
                    $response['messages'][] = matkhaumoivaxacnhankhongchinhxac;
                }
            }

            if (empty($gender)) {
                $response['messages'][] = chuachongioitinh;
            }

            if (empty($birthday)) {
                $response['messages'][] = ngaysinhkhongduoctrong;
            }

            if (!empty($birthday) && !$func->isDate($birthday)) {
                $response['messages'][] = ngaysinhkhonghople;
            }

            if (empty($email)) {
                $response['messages'][] = emailkhongduoctrong;
            }

            if (!empty($email)) {
                if (!$func->isEmail($email)) {
                    $response['messages'][] = emailkhonghople;
                }

                if ($func->checkAccount($email, 'email', 'member', $iduser)) {
                    $response['messages'][] = emaildatontai;
                }
            }

            if (!empty($phone) && !$func->isPhone($phone)) {
                $response['messages'][] = sodienthoaikhonghople;
            }

            if (empty($address)) {
                $response['messages'][] = diachikhongduoctrong;
            }

            if (!empty($response)) {
                /* Flash data */
                $flash->set('fullname', $fullname);
                $flash->set('gender', $gender);
                $flash->set('birthday', $birthday);
                $flash->set('email', $email);
                $flash->set('phone', $phone);
                $flash->set('address', $address);

                /* Errors */
                $response['status'] = 'danger';
                $message = base64_encode(json_encode($response));
                $flash->set('message', $message);
                $func->redirect($configBase . "account/thong-tin");
            }

            if (!empty($old_password) && empty($isWrongPass)) {
                $data['password'] = $new_passwordMD5;
            }

            $data['fullname'] = $fullname;
            $data['email'] = $email;
            $data['phone'] = $phone;
            $data['address'] = $address;
            $data['gender'] = $gender;
            $data['birthday'] = strtotime(str_replace("/", "-", $birthday));

            $d->where('id', $iduser);
            if ($d->update('member', $data)) {
                if ($old_password) {
                    unset($_SESSION[$loginMember]);
                    setcookie('login_member_id', "", -1, '/');
                    setcookie('login_member_session', "", -1, '/');
                    $func->transfer(capnhatthongtinthanhcong, $configBase . "account/dang-nhap");
                } else {
                    $func->transfer(capnhatthongtinthanhcong, $configBase . "account/thong-tin");
                }
            } else {
                $func->transfer(capnhatthongtinthatbai, $configBase . "account/thong-tin", false);
            }
        }
    } else {
        $func->transfer(trangkhongtontai, $configBase, false);
    }
}

function checkActivationMember()
{
    global $d, $func, $flash, $rowDetail, $configBase;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (!empty($_POST['activation-user'])) {
        /* Data */
        $message = '';
        $response = array();
        $confirm_code = (!empty($_POST['confirm_code'])) ? htmlspecialchars($_POST['confirm_code']) : '';

        /* Valid data */
        if (empty($id)) {
            $response['messages'][] = nguoidungkhongtontai;
        } else {
            $rowDetail = $d->rawQueryOne("select status, confirm_code, id from #_member where id = ? limit 0,1", array($id));

            if (empty($rowDetail)) {
                $response['messages'][] = taikhoancuabankhongtontai;
            } else if (!empty($rowDetail['status']) && strpos($rowDetail['status'], 'hienthi') !== false) {
                $func->transfer(trangkhongtontai, $configBase, false);
            } else {
                if (empty($confirm_code)) {
                    $response['messages'][] = maxacnhankhongduoctrong;
                }

                if (!empty($confirm_code) && ($rowDetail['confirm_code'] != $confirm_code)) {
                    $response['messages'][] = maxacnhankhongdung;
                }
            }
        }

        if (!empty($response)) {
            $response['status'] = 'danger';
            $message = base64_encode(json_encode($response));
            $flash->set("message", $message);
            $func->redirect($configBase . "account/kich-hoat?id=" . $id);
        }

        /* Save data */
        $data['status'] = 'hienthi';
        $data['confirm_code'] = '';
        $d->where('id', $id);
        if ($d->update('member', $data)) {
            $func->transfer(kichhoattaikhoanthanhcong, $configBase . "account/dang-nhap");
        }
    } else {
        /* Valid data */
        if (empty($id)) {
            $func->transfer(trangkhongtontai, $configBase, false);
        } else {
            $rowDetail = $d->rawQueryOne("select status, confirm_code, id from #_member where id = ? limit 0,1", array($id));

            if (empty($rowDetail) || (!empty($rowDetail['status']) && strpos($rowDetail['status'], 'hienthi') !== false)) {
                $func->transfer(trangkhongtontai, $configBase, false);
            }
        }
    }
}

function loginMember()
{
    global $d, $func, $flash, $loginMember, $configBase;

    /* Data */
    $username = (!empty($_POST['username'])) ? htmlspecialchars($_POST['username']) : '';
    $password = (!empty($_POST['password'])) ? $_POST['password'] : '';
    $passwordMD5 = md5($password);
    $remember = (!empty($_POST['remember-user'])) ? htmlspecialchars($_POST['remember-user']) : false;

    /* Valid data */
    if (empty($username)) {
        $response['messages'][] = tendangnhapkhongduoctrong;
    }

    if (empty($password)) {
        $response['messages'][] = matkhaukhongduoctrong;
    }

    if (!empty($response)) {
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set("message", $message);
        $func->redirect($configBase . "account/dang-nhap");
    }

    $row = $d->rawQueryOne("select id, password, username, phone, address, email, fullname from #_member where username = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) limit 0,1", array($username));

    if (!empty($row)) {
        if ($row['password'] == $passwordMD5) {
            /* Tạo login session */
            $id_user = $row['id'];
            $lastlogin = time();
            $login_session = md5($row['password'] . $lastlogin);
            $d->rawQuery("update #_member set login_session = ?, lastlogin = ? where id = ?", array($login_session, $lastlogin, $id_user));

            /* Lưu session login */
            $_SESSION[$loginMember]['active'] = true;
            $_SESSION[$loginMember]['id'] = $row['id'];
            $_SESSION[$loginMember]['username'] = $row['username'];
            $_SESSION[$loginMember]['phone'] = $row['phone'];
            $_SESSION[$loginMember]['address'] = $row['address'];
            $_SESSION[$loginMember]['email'] = $row['email'];
            $_SESSION[$loginMember]['fullname'] = $row['fullname'];
            $_SESSION[$loginMember]['login_session'] = $login_session;

            /* Nhớ mật khẩu */
            setcookie('login_member_id', "", -1, '/');
            setcookie('login_member_session', "", -1, '/');
            if ($remember) {
                $time_expiry = time() + 3600 * 24;
                setcookie('login_member_id', $row['id'], $time_expiry, '/');
                setcookie('login_member_session', $login_session, $time_expiry, '/');
            }

            $func->transfer("Đăng nhập thành công", $configBase);
        } else {
            $response['messages'][] = tendangnhaphoacmatkhaukhongchinhxac;
        }
    } else {
        $response['messages'][] = tendangnhaphoacmatkhaukhongchinhxac;
    }

    /* Response error */
    if (!empty($response)) {
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set("message", $message);
        $func->redirect($configBase . "account/dang-nhap");
    }
}

function signupMember()
{
    global $d, $func, $flash, $configBase;

    /* Data */
    $message = '';
    $response = array();
    $username = (!empty($_POST['username'])) ? htmlspecialchars($_POST['username']) : '';
    $password = (!empty($_POST['password'])) ? $_POST['password'] : '';
    $passwordMD5 = md5($password);
    $repassword = (!empty($_POST['repassword'])) ? $_POST['repassword'] : '';
    $fullname = (!empty($_POST['fullname'])) ? htmlspecialchars($_POST['fullname']) : '';
    $email = (!empty($_POST['email'])) ? htmlspecialchars($_POST['email']) : '';
    $confirm_code = $func->digitalRandom(0, 3, 6);
    $phone = (!empty($_POST['phone'])) ? htmlspecialchars($_POST['phone']) : 0;
    $address = (!empty($_POST['address'])) ? htmlspecialchars($_POST['address']) : '';
    $gender = (!empty($_POST['gender'])) ? htmlspecialchars($_POST['gender']) : 0;
    $birthday = (!empty($_POST['birthday'])) ? htmlspecialchars($_POST['birthday']) : '';

    /* Valid data */
    if (empty($fullname)) {
        $response['messages'][] = hotenkhongduoctrong;
    }

    if (empty($username)) {
        $response['messages'][] = taikhoankhongduoctrong;
    }

    if (!empty($username)) {
        if (!$func->isAlphaNum($username)) {
            $response['messages'][] = taikhoanchiduocnhapchuthuongvaso;
        }

        if ($func->checkAccount($username, 'username', 'member')) {
            $response['messages'][] = taikhoandatontai;
        }
    }

    if (empty($password)) {
        $response['messages'][] = matkhaukhongduoctrong;
    }

    if (!empty($password) && empty($repassword)) {
        $response['messages'][] = xacnhanmatkhaukhongduoctrong;
    }

    if (!empty($password) && !empty($repassword) && !$func->isMatch($password, $repassword)) {
        $response['messages'][] = matkhaukhongtrungkhop;
    }

    if (empty($gender)) {
        $response['messages'][] = chuachongioitinh;
    }

    if (empty($birthday)) {
        $response['messages'][] = ngaysinhkhongduoctrong;
    }

    if (!empty($birthday) && !$func->isDate($birthday)) {
        $response['messages'][] = ngaysinhkhonghople;
    }

    if (empty($email)) {
        $response['messages'][] = emailkhongduoctrong;
    }

    if (!empty($email)) {
        if (!$func->isEmail($email)) {
            $response['messages'][] = emailkhonghople;
        }

        if ($func->checkAccount($email, 'email', 'member')) {
            $response['messages'][] = emaildatontai;
        }
    }

    if (!empty($phone) && !$func->isPhone($phone)) {
        $response['messages'][] = sodienthoaikhonghople;
    }

    if (empty($address)) {
        $response['messages'][] = diachikhongduoctrong;
    }

    if (!empty($response)) {
        /* Flash data */
        $flash->set('fullname', $fullname);
        $flash->set('username', $username);
        $flash->set('gender', $gender);
        $flash->set('birthday', $birthday);
        $flash->set('email', $email);
        $flash->set('phone', $phone);
        $flash->set('address', $address);

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);
        $func->redirect($configBase . "account/dang-ky");
    }

    /* Save data */
    $data['fullname'] = $fullname;
    $data['username'] = $username;
    $data['password'] = md5($password);
    $data['email'] = $email;
    $data['phone'] = $phone;
    $data['address'] = $address;
    $data['gender'] = $gender;
    $data['birthday'] = strtotime(str_replace("/", "-", $birthday));
    $data['confirm_code'] = $confirm_code;
    $data['status'] = '';

    if ($d->insert('member', $data)) {
        sendActivation($username);
        $func->transfer(dangkythanhvienthanhcong . ": " . $data['email'] . dekichhoattaikhoan, $configBase . "account/dang-nhap");
    } else {
        $func->transfer(dangkythanhvienthatbai, $configBase, false);
    }
}

function sendActivation($username)
{
    global $d, $setting, $emailer, $func, $configBase, $lang;

    /* Lấy thông tin người dùng */
    $row = $d->rawQueryOne("select id, confirm_code, username, password, fullname, email, phone, address from #_member where username = ? limit 0,1", array($username));

    /* Gán giá trị gửi email */
    $iduser = $row['id'];
    $confirm_code = $row['confirm_code'];
    $tendangnhap = $row['username'];
    $matkhau = $row['password'];
    $tennguoidung = $row['fullname'];
    $emailnguoidung = $row['email'];
    $dienthoainguoidung = $row['phone'];
    $diachinguoidung = $row['address'];
    $linkkichhoat = $configBase . "account/kich-hoat?id=" . $iduser;

    /* Thông tin đăng ký */
    $thongtindangky = '<td style="padding:3px 9px 9px 0px;border-top:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal" valign="top"><span style="text-transform:normal">Username: ' . $tendangnhap . '</span><br>' . matkhau . ': *******' . substr($matkhau, -3) . '<br>' . makichhoat . ': ' . $confirm_code . '</td><td style="padding:3px 0px 9px 9px;border-top:0;border-left:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal" valign="top">';
    if ($tennguoidung) {
        $thongtindangky .= '<span style="text-transform:capitalize">' . $tennguoidung . '</span><br>';
    }
    if ($emailnguoidung) {
        $thongtindangky .= '<a href="mailto:' . $emailnguoidung . '" target="_blank">' . $emailnguoidung . '</a><br>';
    }
    if ($diachinguoidung) {
        $thongtindangky .= $diachinguoidung . '<br>';
    }
    if ($dienthoainguoidung) {
        $thongtindangky .= 'Tel: ' . $dienthoainguoidung . '</td>';
    }

    /* Defaults attributes email */
    $emailDefaultAttrs = $emailer->defaultAttrs();

    /* Variables email */
    $emailVars = array(
        '{emailInfoSignupMember}',
        '{emailLinkActiveMember}'
    );
    $emailVars = $emailer->addAttrs($emailVars, $emailDefaultAttrs['vars']);

    /* Values email */
    $emailVals = array(
        $thongtindangky,
        $linkkichhoat
    );
    $emailVals = $emailer->addAttrs($emailVals, $emailDefaultAttrs['vals']);

    /* Send email admin */
    $arrayEmail = array(
        "dataEmail" => array(
            "name" => $row['username'],
            "email" => $row['email']
        )
    );
    $subject = thukichhoattaikhoanthanhvientu . " " . $setting['name' . $lang];
    $message = str_replace($emailVars, $emailVals, $emailer->markdown('member/active_' . $lang));
    $file = '';

    if (!$emailer->send("customer", $arrayEmail, $subject, $message, $file)) {
        $func->transfer(coloixayratrongquatrinhkichhoat, $configBase . "lien-he", false);
    }
}

function forgotPasswordMember()
{
    global $d, $setting, $emailer, $func, $flash, $loginMember, $configBase, $lang;

    /* Data */
    $message = '';
    $response = array();
    $username = (!empty($_POST['username'])) ? htmlspecialchars($_POST['username']) : '';
    $email = (!empty($_POST['email'])) ? htmlspecialchars($_POST['email']) : '';
    $newpass = substr(md5(rand(0, 999) * time()), 15, 6);
    $newpassMD5 = md5($newpass);

    /* Valid data */
    if (empty($username)) {
        $response['messages'][] = taikhoankhongduoctrong;
    }

    if (!empty($username) && !$func->isAlphaNum($username)) {
        $response['messages'][] = taikhoanchiduocnhapchuthuongvaso;
    }

    if (empty($email)) {
        $response['messages'][] = emailkhongduoctrong;
    }

    if (!empty($email) && !$func->isEmail($email)) {
        $response['messages'][] = emailkhonghople;
    }

    if (!empty($username) && !empty($email)) {
        $row = $d->rawQueryOne("select id from #_member where username = ? and email = ? limit 0,1", array($username, $email));

        if (empty($row)) {
            $response['messages'][] = tendangnhaphoacemailkhongtontai;
        }
    }

    if (!empty($response)) {
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);
        $func->redirect($configBase . "account/quen-mat-khau");
    }

    /* Cập nhật mật khẩu mới */
    $data['password'] = $newpassMD5;
    $d->where('username', $username);
    $d->where('email', $email);
    $d->update('member', $data);

    /* Lấy thông tin người dùng */
    $row = $d->rawQueryOne("select id, username, password, fullname, email, phone, address from #_member where username = ? limit 0,1", array($username));

    /* Gán giá trị gửi email */
    $iduser = $row['id'];
    $tendangnhap = $row['username'];
    $matkhau = $row['password'];
    $tennguoidung = $row['fullname'];
    $emailnguoidung = $row['email'];
    $dienthoainguoidung = $row['phone'];
    $diachinguoidung = $row['address'];

    /* Thông tin đăng ký */
    $thongtindangky = '<td style="padding:3px 9px 9px 0px;border-top:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal" valign="top"><span style="text-transform:normal">Username: ' . $tendangnhap . '</span><br>' . matkhau . ': *******' . substr($matkhau, -3) . '</td><td style="padding:3px 0px 9px 9px;border-top:0;border-left:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal" valign="top">';
    if ($tennguoidung) {
        $thongtindangky .= '<span style="text-transform:capitalize">' . $tennguoidung . '</span><br>';
    }

    if ($emailnguoidung) {
        $thongtindangky .= '<a href="mailto:' . $emailnguoidung . '" target="_blank">' . $emailnguoidung . '</a><br>';
    }

    if ($diachinguoidung) {
        $thongtindangky .= $diachinguoidung . '<br>';
    }

    if ($dienthoainguoidung) {
        $thongtindangky .= 'Tel: ' . $dienthoainguoidung . '</td>';
    }

    /* Defaults attributes email */
    $emailDefaultAttrs = $emailer->defaultAttrs();

    /* Variables email */
    $emailVars = array(
        '{emailInfoSignupMember}',
        '{emailNewPasswordMember}'
    );
    $emailVars = $emailer->addAttrs($emailVars, $emailDefaultAttrs['vars']);

    /* Values email */
    $emailVals = array(
        $thongtindangky,
        $newpass
    );
    $emailVals = $emailer->addAttrs($emailVals, $emailDefaultAttrs['vals']);

    /* Send email admin */
    $arrayEmail = array(
        "dataEmail" => array(
            "name" => $tennguoidung,
            "email" => $email
        )
    );
    $subject = thucaplaimatkhautu . " " . $setting['name' . $lang];
    $message = str_replace($emailVars, $emailVals, $emailer->markdown('member/forgot-password_' . $lang));
    $file = '';

    if ($emailer->send("customer", $arrayEmail, $subject, $message, $file)) {
        unset($_SESSION[$loginMember]);
        setcookie('login_member_id', "", -1, '/');
        setcookie('login_member_session', "", -1, '/');
        $func->transfer(caplaimatkhauthanhcong . ": " . $email, $configBase);
    } else {
        $func->transfer(coloixayratrongquatrinhcaplaimatkhau, $configBase . "account/quen-mat-khau", false);
    }
}

function logoutMember()
{
    global $d, $func, $loginMember, $configBase;

    unset($_SESSION[$loginMember]);
    setcookie('login_member_id', "", -1, '/');
    setcookie('login_member_session', "", -1, '/');

    $func->redirect($configBase);
}

function orderMember()
{
    global $d, $func, $loginMember, $order;
    $order = $d->rawQuery("select * from #_order where id_user = ? order by date_created desc", array($_SESSION[$loginMember]['id']));
}

function orderMemberDetail($codeOrder = "")
{
    global $d, $func, $loginMember, $order, $orderDetail;
    $order = $d->rawQueryOne("select * from #_order where id_user = ? and code = ? order by date_created desc", array($_SESSION[$loginMember]['id'], $codeOrder));
    if (!empty($order)) {
        $orderDetail = $d->rawQuery("select * from #_order_detail where id_order = ? order by id desc", array($order['id']));
    }
}
