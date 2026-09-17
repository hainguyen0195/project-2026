<?php
if (!defined('SOURCES')) die("Error");

if (!empty($_POST['submit-contact'])) {
    $responseCaptcha = $_POST['recaptcha_response_contact'];
    $resultCaptcha = $func->checkRecaptcha($responseCaptcha);
    $scoreCaptcha = (!empty($resultCaptcha['score'])) ? $resultCaptcha['score'] : 0;
    $actionCaptcha = (!empty($resultCaptcha['action'])) ? $resultCaptcha['action'] : '';
    $testCaptcha = (!empty($resultCaptcha['test'])) ? $resultCaptcha['test'] : false;
    $dataContact = (!empty($_POST['dataContact']) && is_array($_POST['dataContact'])) ? $_POST['dataContact'] : array();

    $roleOptions = array('Chủ nhà', 'Chủ thầu - Đơn vị thiết kế', 'Khác');
    $aluminumSystemOptions = array('Technal', 'Maxpro JP', 'PMI', 'Xingfa', 'Phụ kiện Cmech', 'Phụ kiện Fapim', 'Khác');
    $drawingOptions = array('Đã có', 'Chưa có');
    $customerRole = (!empty($dataContact['customer_role']) && is_string($dataContact['customer_role'])) ? trim($dataContact['customer_role']) : '';
    $aluminumSystem = (!empty($dataContact['aluminum_system']) && is_string($dataContact['aluminum_system'])) ? trim($dataContact['aluminum_system']) : '';
    $projectBudget = (!empty($dataContact['project_budget']) && is_string($dataContact['project_budget'])) ? trim($dataContact['project_budget']) : '';
    $hasDrawing = (!empty($dataContact['has_drawing']) && is_string($dataContact['has_drawing'])) ? trim($dataContact['has_drawing']) : '';

    /* Valid data */
    if (empty($dataContact['fullname'])) {
        $response['messages'][] = hotenkhongduoctrong;
    }

    if (empty($dataContact['phone'])) {
        $response['messages'][] = sodienthoaikhongduoctrong;
    }

    if (!empty($dataContact['phone']) && !$func->isPhone($dataContact['phone'])) {
        $response['messages'][] = sodienthoaikhonghople;
    }

    if (empty($dataContact['address'])) {
        $response['messages'][] = diachikhongduoctrong;
    }

    if (empty($dataContact['subject'])) {
        $response['messages'][] = chudekhongduoctrong;
    }

    if (empty($dataContact['content'])) {
        $response['messages'][] = noidungkhongduoctrong;
    }

    if (!in_array($customerRole, $roleOptions, true)) {
        $response['messages'][] = 'Vui lòng chọn vai trò của Anh/Chị.';
    }

    if (!in_array($aluminumSystem, $aluminumSystemOptions, true)) {
        $response['messages'][] = 'Vui lòng chọn hệ nhôm Anh/Chị quan tâm.';
    }

    if ($projectBudget === '') {
        $response['messages'][] = 'Vui lòng nhập ngân sách dự kiến.';
    }

    if (!in_array($hasDrawing, $drawingOptions, true)) {
        $response['messages'][] = 'Vui lòng chọn tình trạng bản vẽ công trình.';
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($dataContact)) {
            foreach ($dataContact as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }
        $flash->set('quote_request', !empty($dataContact['quote_request']) ? '1' : '0');

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set("message", $message);
        $func->redirect("lien-he");
    }

    /* Save data */
    if (($scoreCaptcha >= 0.5 && $actionCaptcha == 'contact') || $testCaptcha == true) {
        $quoteRequest = !empty($dataContact['quote_request']) ? 1 : 0;

        $data = array();
        $data['fullname'] = htmlspecialchars($dataContact['fullname']);
        $data['email'] = !empty($dataContact['email']) ? htmlspecialchars($dataContact['email']) : '';
        $data['phone'] = htmlspecialchars($dataContact['phone']);
        $data['address'] = htmlspecialchars($dataContact['address']);
        $data['subject'] = htmlspecialchars($dataContact['subject']);
        $data['content'] = htmlspecialchars($dataContact['content']);
        $data['customer_role'] = htmlspecialchars($customerRole);
        $data['aluminum_system'] = htmlspecialchars($aluminumSystem);
        $data['project_budget'] = htmlspecialchars($projectBudget);
        $data['has_drawing'] = htmlspecialchars($hasDrawing);
        $data['quote_request'] = $quoteRequest;
        $data['date_created'] = time();
        $data['numb'] = 1;

        if ($d->insert('contact', $data)) {
            $id_insert = $d->getLastInsertId();

            if ($func->hasFile("file_attach")) {
                $fileUpdate = array();
                $file_name = $func->uploadName($_FILES['file_attach']["name"]);

                if ($file_attach = $func->uploadImage("file_attach", '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.DOC|.DOCX|.PDF|.RAR|.ZIP|.PPT|.PPTX|.xls|.xlsx|.jpg|.png|.gif|.JPG|.PNG|.GIF', UPLOAD_FILE_L, $file_name)) {
                    $fileUpdate['file_attach'] = $file_attach;
                    $d->where('id', $id_insert);
                    $d->update('contact', $fileUpdate);
                    unset($fileUpdate);
                }
            }
        } else {
            $func->transfer(guilienhethatbai, $configBase, false);
        }

        /* Gán giá trị gửi email */
        $strThongtin = '';
        $emailer->set('tennguoigui', $data['fullname']);
        $emailer->set('emailnguoigui', $data['email']);
        $emailer->set('dienthoainguoigui', $data['phone']);
        $emailer->set('diachinguoigui', $data['address']);
        $emailer->set('tieudelienhe', $data['subject']);
        $emailer->set('noidunglienhe', htmlspecialchars($dataContact['content']));
        $emailer->set('yeucaubaogia', $quoteRequest ? 'Có' : 'Không');

        $strThongtin .= '<strong>Họ và tên:</strong> <span style="text-transform:capitalize">' . $emailer->get('tennguoigui') . '</span><br>';
        $strThongtin .= '<strong>Số điện thoại:</strong> ' . $emailer->get('dienthoainguoigui') . '<br>';
        if ($emailer->get('emailnguoigui')) {
            $strThongtin .= '<strong>Email:</strong> <a href="mailto:' . $emailer->get('emailnguoigui') . '" target="_blank">' . $emailer->get('emailnguoigui') . '</a><br>';
        }
        $strThongtin .= '<strong>Khu vực dự kiến:</strong> ' . $emailer->get('diachinguoigui') . '<br>';
        $strThongtin .= '<strong>Vai trò:</strong> ' . htmlspecialchars($customerRole) . '<br>';
        $strThongtin .= '<strong>Hệ nhôm quan tâm:</strong> ' . htmlspecialchars($aluminumSystem) . '<br>';
        $strThongtin .= '<strong>Ngân sách dự kiến:</strong> ' . htmlspecialchars($projectBudget) . '<br>';
        $strThongtin .= '<strong>Bản vẽ công trình:</strong> ' . htmlspecialchars($hasDrawing) . '<br>';
        $strThongtin .= '<strong>Yêu cầu bảng báo giá:</strong> ' . $emailer->get('yeucaubaogia');
        $emailer->set('thongtin', $strThongtin);

        /* Defaults attributes email */
        $emailDefaultAttrs = $emailer->defaultAttrs();

        /* Variables email */
        $emailVars = array(
            '{emailTitleSender}',
            '{emailInfoSender}',
            '{emailSubjectSender}',
            '{emailContentSender}'
        );
        $emailVars = $emailer->addAttrs($emailVars, $emailDefaultAttrs['vars']);

        /* Values email */
        $emailVals = array(
            $emailer->get('tennguoigui'),
            $emailer->get('thongtin'),
            $emailer->get('tieudelienhe'),
            $emailer->get('noidunglienhe')
        );
        $emailVals = $emailer->addAttrs($emailVals, $emailDefaultAttrs['vals']);

        /* Send email admin */
        $arrayEmail = null;
        $subject = thulienhetu . ' ' . $setting['name' . $lang];
        $message = str_replace($emailVars, $emailVals, $emailer->markdown('contact/admin'));
        $file = 'file_attach';

        if ($emailer->send("admin", $arrayEmail, $subject, $message, $file)) {
            /* Send email customer */
            if (!empty($data['email'])) {
                $arrayEmail = array(
                    "dataEmail" => array(
                        "name" => $emailer->get('tennguoigui'),
                        "email" => $emailer->get('emailnguoigui')
                    )
                );
                /* Defaults attributes email */
                $emailDefaultAttrs = $emailer->defaultAttrs($lang);

                /* Variables email */
                $emailVars = array(
                    '{emailTitleSender}',
                    '{emailInfoSender}',
                    '{emailSubjectSender}',
                    '{emailContentSender}'
                );
                $emailVars = $emailer->addAttrs($emailVars, $emailDefaultAttrs['vars']);

                /* Values email */
                $emailVals = array(
                    $emailer->get('tennguoigui'),
                    $emailer->get('thongtin'),
                    $emailer->get('tieudelienhe'),
                    $emailer->get('noidunglienhe')
                );
                $emailVals = $emailer->addAttrs($emailVals, $emailDefaultAttrs['vals']);
                $subject = thulienhetu . ' ' . $setting['name' . $lang];
                $message = str_replace($emailVars, $emailVals, $emailer->markdown('contact/customer_' . $lang));
                $file = 'file_attach';
                $emailer->send("customer", $arrayEmail, $subject, $message, $file, $lang);
            }
            $func->transfer(guilienhethanhcong, $configBase);
        } else $func->transfer(guilienhethatbai, $configBase, false);
    } else {
        $func->transfer(guilienhethatbai, $configBase, false);
    }
}

/* SEO */
$seopage = $d->rawQueryOne("select * from #_seopage where type = ? limit 0,1", array('lien-he'));
$seo->set('h1', $titleMain);
if (!empty($seopage['title' . $seolang])) $seo->set('title', $seopage['title' . $seolang]);
else $seo->set('title', $titleMain);
if (!empty($seopage['keywords' . $seolang])) $seo->set('keywords', $seopage['keywords' . $seolang]);
if (!empty($seopage['description' . $seolang])) $seo->set('description', $seopage['description' . $seolang]);
$seo->set('url', $func->getPageURL());
$imgJson = (!empty($seopage['options'])) ? json_decode($seopage['options'], true) : null;
if (!empty($seopage['photo'])) {
    if (empty($imgJson) || ($imgJson['p'] != $seopage['photo'])) {
        $imgJson = $func->getImgSize($seopage['photo'], UPLOAD_SEOPAGE_L . $seopage['photo']);
        $seo->updateSeoDB(json_encode($imgJson), 'seopage', $seopage['id']);
    }
    if (!empty($imgJson)) {
        $seo->set('photo', $configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . UPLOAD_SEOPAGE_L . $seopage['photo']);
        $seo->set('photo:width', $imgJson['w']);
        $seo->set('photo:height', $imgJson['h']);
        $seo->set('photo:type', $imgJson['m']);
    }
}

$lienhe = $d->rawQueryOne("select content$lang,tieuchi,text1$lang,text2$lang,text3$lang from #_static where type = ? limit 0,1", array('lienhe'));
$tieuchicontact = json_decode($lienhe['tieuchi'], true);

/* breadCrumbs */
if (!empty($titleMain)) $breadcr->set($com, $titleMain);
$breadcrumbs = $breadcr->get();
