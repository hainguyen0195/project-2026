<?php if (!empty($slider)) { ?>
    <section class="bnh slideshow" aria-label="Slideshow">
        <div class="container">
            <div class="slick-slideshow">
                <?php foreach ($slider as $index => $v) {
                    $title = trim((string) ($v['name' . $lang] ?? ''));
                    $subtitle = trim((string) ($v['text1' . $lang] ?? ''));
                    $thirdText = trim((string) ($v['text2' . $lang] ?? ''));
                    $options = !empty($v['options']) ? json_decode($v['options'], true) : array();
                    $labels = !empty($options['muilti_text']) && is_array($options['muilti_text']) ? $options['muilti_text'] : array();
                    $newsIds = !empty($options['news_ids']) && is_array($options['news_ids'])
                        ? array_unique(array_filter(array_map('intval', $options['news_ids'])))
                        : array();
                    $blogs = array();
                    foreach ($newsIds as $newsId) {
                        if (!empty($slideBlogs[$newsId])) $blogs[] = $slideBlogs[$newsId];
                    }
                ?>
                    <div class="slideshow-slide">
                        <div class="bnh-wrap">
                            <div class="bnh-image">
                                <div class="inner">
                                    <picture>
                                        <source media="(max-width: 767px)" srcset="<?= THUMBS ?>/794x654x1/<?= UPLOAD_PHOTO_L . $v['photo'] ?>">
                                        <img width="1664" height="778"
                                            src="<?= THUMBS ?>/1664x778x1/<?= UPLOAD_PHOTO_L . $v['photo'] ?>"
                                            onerror="this.src='<?= THUMBS ?>/1664x778x2/assets/images/noimage.png';"
                                            alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                                            <?= $index === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                                    </picture>
                                </div>
                            </div>

                            <div class="bnh-ctn">
                                <div class="bnh-flex">
                                    <div class="col-left">
                                        <div class="bnh-group">
                                            <div class="bnh-head">
                                                <h2 class="bnh-tt">
                                                    <?php if ($title !== '') { ?><span class="t-text"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></span><?php } ?>
                                                    <?php if ($subtitle !== '') { ?><span class="t-txt"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?> <?php if ($thirdText !== '') { ?><span class="t-sub"><?= htmlspecialchars($thirdText, ENT_QUOTES, 'UTF-8') ?></span><?php } ?> </span><?php } ?>

                                                </h2>
                                                <a class="btn bnh-btn" href="<?= !empty($v['link']) ? htmlspecialchars($v['link'], ENT_QUOTES, 'UTF-8') : '#' ?>">
                                                    <span class="text">Tìm hiểu ngay</span>
                                                    <i class="fas fa-arrow-right icon"></i>
                                                </a>
                                            </div>

                                            <?php if (!empty($blogs)) { ?>
                                                <div class="bnh-blog">
                                                    <?php foreach ($blogs as $blog) {
                                                        $blogUrl = !empty($blog[$sluglang]) ? $blog[$sluglang] : '';
                                                    ?>
                                                        <article class="blog-it">
                                                            <?php if (!empty($blog['photo'])) { ?>
                                                                <div class="blog-image">
                                                                    <a class="inner" href="<?= $blogUrl ?>">
                                                                        <img src="<?= THUMBS ?>/120x120x1/<?= UPLOAD_NEWS_L . $blog['photo'] ?>"
                                                                            alt="<?= htmlspecialchars($blog['name' . $lang] ?? '', ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                                                                    </a>
                                                                </div>
                                                            <?php } ?>
                                                            <div class="blog-ctn">
                                                                <h3 class="blog-tt"><a href="<?= $blogUrl ?>"><?= htmlspecialchars($blog['name' . $lang] ?? '', ENT_QUOTES, 'UTF-8') ?></a></h3>
                                                                <?php if (!empty($blog['desc' . $lang])) { ?>
                                                                    <div class="blog-des"><?= htmlspecialchars_decode($blog['desc' . $lang]) ?></div>
                                                                <?php } ?>
                                                            </div>
                                                        </article>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <?php if (!empty($labels)) { ?>
                                        <div class="col-right">
                                            <div class="bnh-prd">
                                                <div class="abp-abs">
                                                    <div class="b-list">
                                                        <?php foreach ($labels as $label) {
                                                            $labelText = is_array($label) ? ($label['label'] ?? '') : $label;
                                                            if (trim((string) $labelText) === '') continue;
                                                        ?>
                                                            <div class="b-item"><span class="t-text"><?= htmlspecialchars($labelText, ENT_QUOTES, 'UTF-8') ?></span></div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>