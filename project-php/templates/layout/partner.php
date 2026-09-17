<?php
$partner = $d->rawQuery("select name$lang, link, photo from #_photo where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb, id desc", array('doitac'));
if (!empty($partner)) { ?>
    <div class="wrap-partner ">
        <div class="container-custom">
            <div class="wrap-content">
                <div class="by-partner-header text-center mb-4" data-aos="fade-up" data-aos-duration="1000">
                    <div class="title-product"><span>Đối tác chiến lược</span></div>
                </div>

                <div class="by-partner-slick row">
                    <?php foreach ($partner as $k => $v) {
                        $partnerName = trim((string)($v['name' . $lang] ?: $v['namevi'] ?? 'Đối tác'));
                    ?>
                        <div class="padding-col">
                            <a class="by-partner-item" href="<?= $v['link'] ?>" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($partnerName, ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($partnerName, ENT_QUOTES, 'UTF-8') ?>" data-aos="fade-up" data-aos-duration="1000">
                                <img class="lazy w-100" width="150" height="80" onerror="this.src='<?= THUMBS ?>/150x80x2/assets/images/noimage.png';" data-src="<?= THUMBS ?>/150x80x2/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= htmlspecialchars($partnerName, ENT_QUOTES, 'UTF-8') ?>" />
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
