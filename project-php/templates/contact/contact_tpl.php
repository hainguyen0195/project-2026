<?= $flash->getMessages("frontend") ?>

<div class="content-main contact-page">
    <div class="contact-consultation">
        <aside class="contact-consultation-intro">
            <span class="contact-consultation-badge"><?= $lienhe['text1' . $lang] ?></span>
            <h2><?= $lienhe['text2' . $lang] ?></h2>
            <h3><?= $lienhe['text3' . $lang] ?></h3>
            <div class="contact-consultation-text"><?= $func->getHtmlChars($lienhe['content' . $lang]) ?></div>
            <ul class="contact-consultation-benefits">
                <?php if (!empty($tieuchicontact)) {
                    foreach ($tieuchicontact as $tieuchi) {
                        $tieuchiLabel = is_array($tieuchi) ? ($tieuchi['label'] ?? '') : $tieuchi;
                        if ($tieuchiLabel === '') continue;
                ?>
                    <li><i class="fas fa-check-circle"></i><span><?= htmlspecialchars($tieuchiLabel) ?></span></li>
                <?php }
                } ?>
            </ul>
            <?php if (!empty($optsetting['hotline'])) { ?>
                <div class="contact-consultation-hotline">
                    <small>Hotline hỗ trợ</small>
                    <a href="tel:<?= $func->parsePhone($optsetting['hotline']) ?>"><i class="fas fa-phone-alt"></i><?= $optsetting['hotline'] ?></a>
                </div>
            <?php } ?>
        </aside>
        <form id="FormContact" class="contact-form contact-consultation-form validation-contact" novalidate method="post" action="" enctype="multipart/form-data">
            <div class="contact-consultation-heading">
                <span><i class="far fa-calendar-alt"></i> Đặt lịch ngay</span>
                <h1>Đặt lịch tư vấn 1:1 ngay</h1>
                <p>Nhận ngay bảng dự toán chi phí tự động dành riêng cho bạn!</p>
            </div>
            <div class="row-20 row">
                <div class="contact-input col-sm-6 col-20">
                    <div class="form-floating form-floating-cus">
                        <input type="text" name="dataContact[fullname]" class="form-control text-sm" id="fullname-contact" placeholder="Nguyễn Văn A" value="<?= $flash->get('fullname') ?>" required>
                        <label for="fullname-contact">Họ và tên <em>*</em></label>
                    </div>
                    <div class="invalid-feedback"><?= vuilongnhaphoten ?></div>
                </div>
                <div class="contact-input col-sm-6 col-20">
                    <div class="form-floating form-floating-cus">
                        <input type="tel" inputmode="numeric" name="dataContact[phone]" class="form-control text-sm" id="phone-contact" placeholder="0912 345 678" value="<?= $flash->get('phone') ?>" required>
                        <label for="phone-contact">Số điện thoại <em>*</em></label>
                    </div>
                    <div class="invalid-feedback"><?= vuilongnhapsodienthoai ?></div>
                </div>
                <div class="contact-input col-sm-6 col-20">
                    <div class="form-floating form-floating-cus">
                        <input type="text" class="form-control text-sm" id="subject-contact" name="dataContact[subject]" placeholder="Ví dụ: Nhà phố, biệt thự, văn phòng..." value="<?= $flash->get('subject') ?>" required>
                        <label for="subject-contact">Loại công trình</label>
                    </div>
                    <div class="invalid-feedback"><?= vuilongnhapchude ?></div>
                </div>
                <div class="contact-input col-sm-6 col-20">
                    <div class="form-floating form-floating-cus">
                        <input type="text" class="form-control text-sm" id="address-contact" name="dataContact[address]" placeholder="VD: Quận Bình Thạnh, TP.HCM" value="<?= $flash->get('address') ?>" required>
                        <label for="address-contact">Khu vực dự kiến</label>
                    </div>
                    <div class="invalid-feedback"><?= vuilongnhapdiachi ?></div>
                </div>
            </div>
            <div class="contact-question-grid">
                <fieldset class="contact-question">
                    <legend class="contact-field-label">Vai trò của Anh/Chị là gì? <em>*</em></legend>
                    <div class="contact-choice-list">
                        <?php foreach (['Chủ nhà', 'Chủ thầu - Đơn vị thiết kế', 'Khác'] as $roleOption) { ?>
                            <label class="contact-choice">
                                <input type="radio" name="dataContact[customer_role]" value="<?= $roleOption ?>" <?= $flash->get('customer_role') === $roleOption ? 'checked' : '' ?> required>
                                <span><?= $roleOption ?></span>
                            </label>
                        <?php } ?>
                    </div>
                    <div class="invalid-feedback">Vui lòng chọn vai trò của Anh/Chị.</div>
                </fieldset>

                <fieldset class="contact-question">
                    <legend class="contact-field-label">Anh/Chị đã có bản vẽ công trình chưa? <em>*</em></legend>
                    <div class="contact-choice-list">
                        <?php foreach (['Đã có', 'Chưa có'] as $drawingOption) { ?>
                            <label class="contact-choice">
                                <input type="radio" name="dataContact[has_drawing]" value="<?= $drawingOption ?>" <?= $flash->get('has_drawing') === $drawingOption ? 'checked' : '' ?> required>
                                <span><?= $drawingOption ?></span>
                            </label>
                        <?php } ?>
                    </div>
                    <div class="invalid-feedback">Vui lòng chọn tình trạng bản vẽ.</div>
                </fieldset>

                <div class="contact-question">
                    <label class="contact-field-label" for="budget-contact">Anh/Chị đã có ngân sách dự kiến cho dự án chưa? <em>*</em></label>
                    <input type="text" class="form-control text-sm contact-question-input" id="budget-contact" name="dataContact[project_budget]" placeholder="Ví dụ: Khoảng 500 triệu hoặc chưa xác định" value="<?= htmlspecialchars($flash->get('project_budget') ?? '') ?>" required>
                    <div class="invalid-feedback">Vui lòng nhập ngân sách dự kiến.</div>
                </div>

                <div class="contact-question">
                    <label class="contact-field-label" for="aluminum-system-contact">Anh/Chị muốn tìm hiểu hệ nhôm nào? <em>*</em></label>
                    <select class="form-control text-sm contact-select" id="aluminum-system-contact" name="dataContact[aluminum_system]" required>
                        <option value="">Chọn hệ nhôm quan tâm</option>
                        <?php foreach (['Technal', 'Maxpro JP', 'PMI', 'Xingfa', 'Phụ kiện Cmech', 'Phụ kiện Fapim', 'Khác'] as $systemOption) { ?>
                            <option value="<?= $systemOption ?>" <?= $flash->get('aluminum_system') === $systemOption ? 'selected' : '' ?>><?= $systemOption ?></option>
                        <?php } ?>
                    </select>
                    <div class="invalid-feedback">Vui lòng chọn hệ nhôm Anh/Chị quan tâm.</div>
                </div>
            </div>
            <div class="contact-input contact-needs">
                <label class="contact-field-label" for="content-contact">Nhu cầu của anh/chị</label>
                <p class="contact-field-description">Cung cấp thêm thông tin kích thước, bản vẽ, phong cách yêu thích hoặc yêu cầu riêng...</p>
                <textarea class="form-control text-sm" id="content-contact" name="dataContact[content]" placeholder="Mô tả sơ bộ về công trình, phong cách yêu thích..." required><?= $flash->get('content') ?></textarea>
                <div class="invalid-feedback"><?= vuilongnhapnoidung ?></div>
            </div>
            <label class="contact-quote-request">
                <input type="checkbox" name="dataContact[quote_request]" value="1" <?= $flash->get('quote_request') !== '0' ? 'checked' : '' ?>>
                <span>Tôi muốn được gửi bảng báo giá tham khảo</span>
            </label>
            <input type="hidden" name="submit-contact" value="submit-contact">
            <button class="contact-submit" type="submit"><i class="fas fa-paper-plane"></i> Gửi yêu cầu tư vấn</button>
            <p class="contact-privacy"><i class="fas fa-lock"></i> <strong>Ghi chú:</strong> Cam kết bảo mật thông tin 100% - chỉ sử dụng để tư vấn cá nhân hóa.</p>
            <input type="hidden" name="recaptcha_response_contact" id="recaptchaResponseContact">
        </form>
    </div>
</div>