<?php
$newsletter = $d->rawQueryOne("select name$lang, desc$lang,text1$lang , tieuchi from #_static where type = ? limit 0,1", array('newsletter'));
$tieuchis = json_decode($newsletter['tieuchi'], true);
?>
<section class="wrap-newsletter">
    <div class="container-custom">
        <div class="wrap-content newsletter-inner">
            <div class="newsletter-left">
                <div class="newsletter-eyebrow" data-aos="fade-right" data-aos-duration="1000">
                    <i class="fas fa-paper-plane"></i>
                    <?= $newsletter['text1' . $lang] ?>
                </div>
                <h2 class="newsletter-title" data-aos="fade-right" data-aos-duration="1000">
                    <?= $newsletter['name' . $lang] ?>
                </h2>
                <p class="newsletter-desc" data-aos="fade-right" data-aos-duration="1000">
                    <?= htmlspecialchars_decode($newsletter['desc' . $lang] ?? '') ?>
                </p>
                <ul class="newsletter-checklist" data-aos="fade-right" data-aos-duration="1000">
                    <?php foreach ($tieuchis as $tieuchi) { ?>
                        <li><i class="fas fa-check"></i> <?= $tieuchi['label'] ?></li>
                    <?php } ?>
                </ul>
            </div>

            <div class="newsletter-right">
                <div class="newsletter-card">
                    <div class="newsletter-card-decor" data-aos="fade-left" data-aos-duration="900"></div>
                    <div class="newsletter-card-note" data-aos="fade-left" data-aos-duration="1000">Miễn phí – Có thể huỷ bất kỳ lúc nào</div>

                    <?= $flash->getMessages("frontend") ?>

                    <form id="FormNewsletter" class="newsletter-form validation-newsletter" novalidate method="post" action="" enctype="multipart/form-data">
                        <div class="newsletter-field" data-aos="fade-left" data-aos-duration="1000">
                            <label for="fullname-newsletter">Họ và tên</label>
                            <div class="newsletter-input">
                                <i class="far fa-user"></i>
                                <input type="text" name="dataNewsletter[fullname]" id="fullname-newsletter" placeholder="Nguyễn Văn A" value="<?= $flash->get('fullname') ?>" required>
                            </div>
                            <div class="invalid-feedback"><?= vuilongnhaphoten ?></div>
                        </div>

                        <div class="newsletter-field" data-aos="fade-left" data-aos-duration="1000">
                            <label for="email-newsletter">Địa chỉ email</label>
                            <div class="newsletter-input">
                                <i class="far fa-envelope"></i>
                                <input type="email" name="dataNewsletter[email]" id="email-newsletter" placeholder="email@example.com" value="<?= $flash->get('email') ?>" required>
                            </div>
                            <div class="invalid-feedback"><?= vuilongnhapdiachiemail ?></div>
                        </div>
                        <div class="newsletter-field" data-aos="fade-left" data-aos-duration="1000">
                            <label for="phone-newsletter">Số điện thoại</label>
                            <div class="newsletter-input">
                                <i class="far fa-phone"></i>
                                <input type="text" name="dataNewsletter[phone]" id="phone-newsletter" placeholder="0909090909" value="<?= $flash->get('phone') ?>" required>
                            </div>
                            <div class="invalid-feedback"><?= vuilongnhapsodienthoai ?></div>
                        </div>
                        <div class="newsletter-field" data-aos="fade-left" data-aos-duration="1000">
                            <label for="address-newsletter">Địa chỉ</label>
                            <div class="newsletter-input">
                                <i class="far fa-map-marker-alt"></i>
                                <input type="text" name="dataNewsletter[address]" id="address-newsletter" placeholder="123 Đường ABC, Quận XYZ, TP. HCM" value="<?= $flash->get('address') ?>" required>
                            </div>
                            <div class="invalid-feedback"><?= vuilongnhapdiachi ?></div>
                        </div>

                        <input type="hidden" name="submit-newsletter" value="submit-newsletter">
                        <input type="hidden" name="recaptcha_response_newsletter" id="recaptchaResponseNewsletter">

                        <button class="newsletter-submit" type="submit" data-aos="fade-left" data-aos-duration="1000">
                            Đăng ký ngay <i class="far fa-arrow-right"></i>
                        </button>
                    </form>

                    <div class="newsletter-privacy" data-aos="fade-up" data-aos-duration="1000">
                        <i class="fas fa-lock"></i>
                        Chúng tôi tôn trọng quyền riêng tư. Không spam, không chia sẻ dữ liệu.
                    </div>


                </div>
            </div>
        </div>
    </div>
</section>