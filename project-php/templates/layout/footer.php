<?php
$footerAddress = trim((string)($optsetting['address'] ?? ''));
$footerHotline = trim((string)($optsetting['hotline'] ?? ''));
$footerEmail = trim((string)($optsetting['email'] ?? ''));
$footerWebsite = trim((string)($optsetting['website'] ?? ''));
$footerWebsiteUrl = preg_match('~^https?://~i', $footerWebsite) ? $footerWebsite : 'https://' . $footerWebsite;
?>
<footer class="footer">
    <div class="footer-article">
        <div class="container-custom">
            <div class="footer-grid">
                <div class="footer-column footer-brand">
                    <?php if (!empty($footer['photo'])) { ?>
                        <a class="logo-footer" href="<?= $configBase ?>" title="<?= htmlspecialchars($setting['name' . $lang] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <img width="699" height="284" loading="lazy" src="<?= UPLOAD_NEWS_L . $footer['photo'] ?>" alt="<?= htmlspecialchars($footer['name' . $lang] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </a>
                    <?php } ?>
                </div>
                <div class="footer-column footer-contact">
                    <h3 class="footer-title">Thông Tin Liên Hệ</h3>
                    <ul class="footer-contact-list">
                        <?php if ($footerAddress !== '') { ?><li><i class="fa-solid fa-location-dot"></i><span>Địa Chỉ: <?= $footerAddress ?></span></li><?php } ?>
                        <?php if ($footerHotline !== '') { ?><li><i class="fa-solid fa-phone"></i><span>Hotline: <a href="tel:<?= $func->parsePhone($footerHotline) ?>"><?= $footerHotline ?></a></span></li><?php } ?>
                        <?php if ($footerEmail !== '') { ?><li><i class="fa-solid fa-envelope"></i><span>Email: <a href="mailto:<?= htmlspecialchars($footerEmail, ENT_QUOTES, 'UTF-8') ?>"><?= $footerEmail ?></a></span></li><?php } ?>
                        <?php if ($footerWebsite !== '') { ?><li><i class="fa-solid fa-globe"></i><span>Website: <a href="<?= htmlspecialchars($footerWebsiteUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= $footerWebsite ?></a></span></li><?php } ?>
                    </ul>
                </div>
                <div class="footer-column footer-links">
                    <h3 class="footer-title">Liên Kết Nhanh</h3>
                    <ul class="footer-ul">
                        <li><a href="san-pham" title="<?= sanpham ?>"><?= sanpham ?></a></li>
                        <li><a href="best-saler" title="<?= bestsaler ?>"><?= bestsaler ?></a></li>
                        <li><a href="flash-sale" title="<?= flashsale ?>"><?= flashsale ?></a></li>
                        <li><a href="ve-comi" title="<?= vecomi ?>"><?= vecomi ?></a></li>
                        <li><a href="blog" title="<?= blog ?>"><?= blog ?></a></li>
                    </ul>
                </div>
                <div class="footer-column footer-process">
                    <h3 class="footer-title">Quy Trình Mua Hàng</h3>
                    <ul class="footer-ul">
                        <?php foreach ($purchaseProcess as $v) { ?>
                            <li><a href="<?= $v[$sluglang] ?>" title="<?= htmlspecialchars($v['name' . $lang], ENT_QUOTES, 'UTF-8') ?>"><?= $v['name' . $lang] ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="copyright mb-0"><?= $setting['copyright' . $lang] ?></p>
                <?php if (!empty($socialfooter)) { ?>
                    <div class="social-footer">
                        <?php foreach ($socialfooter as $v) { ?>
                            <a href="<?= htmlspecialchars($v['link'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" title="<?= htmlspecialchars($v['name' . $lang], ENT_QUOTES, 'UTF-8') ?>"><img loading="lazy" src="<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= htmlspecialchars($v['name' . $lang], ENT_QUOTES, 'UTF-8') ?>"></a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</footer>
