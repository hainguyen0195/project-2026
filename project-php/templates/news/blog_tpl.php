<div class="blog-page">
    <?php if (!empty($featuredNews)) { ?>
        <section class="blog-featured">
            <h1 class="blog-page__heading">TIN NỔI BẬT</h1>
            <div class="blog-featured__layout">
                <?php $featuredMain = $featuredNews[0]; ?>
                <article class="blog-featured__main">
                    <a class="blog-card__image scale-img" href="<?= $featuredMain[$sluglang] ?>" title="<?= htmlspecialchars($featuredMain['name' . $lang], ENT_QUOTES, 'UTF-8') ?>">
                        <img class="lazy" onerror="this.src='<?= THUMBS ?>/820x600x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/820x600x1/<?= UPLOAD_NEWS_L . $featuredMain['photo'] ?>" alt="<?= htmlspecialchars($featuredMain['name' . $lang], ENT_QUOTES, 'UTF-8') ?>">
                    </a>
                    <h2><a href="<?= $featuredMain[$sluglang] ?>"><?= htmlspecialchars($featuredMain['name' . $lang], ENT_QUOTES, 'UTF-8') ?></a></h2>
                    <p><?= htmlspecialchars(trim(strip_tags(htmlspecialchars_decode($featuredMain['desc' . $lang] ?? ''))), ENT_QUOTES, 'UTF-8') ?></p>
                </article>
                <div class="blog-featured__side">
                    <?php foreach (array_slice($featuredNews, 1, 4) as $featuredItem) { ?>
                        <article class="blog-featured__small">
                            <a class="blog-card__image scale-img" href="<?= $featuredItem[$sluglang] ?>" title="<?= htmlspecialchars($featuredItem['name' . $lang], ENT_QUOTES, 'UTF-8') ?>">
                                <img class="lazy" onerror="this.src='<?= THUMBS ?>/520x330x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/520x330x1/<?= UPLOAD_NEWS_L . $featuredItem['photo'] ?>" alt="<?= htmlspecialchars($featuredItem['name' . $lang], ENT_QUOTES, 'UTF-8') ?>">
                            </a>
                            <h3><a href="<?= $featuredItem[$sluglang] ?>"><?= htmlspecialchars($featuredItem['name' . $lang], ENT_QUOTES, 'UTF-8') ?></a></h3>
                            <p><?= htmlspecialchars(trim(strip_tags(htmlspecialchars_decode($featuredItem['desc' . $lang] ?? ''))), ENT_QUOTES, 'UTF-8') ?></p>
                        </article>
                    <?php } ?>
                </div>
            </div>
        </section>
    <?php } ?>

    <section class="blog-listing">
        <div class="blog-listing__head">
            <h2 class="blog-page__heading"><?= !empty($newsList['name' . $lang]) ? mb_strtoupper($newsList['name' . $lang], 'UTF-8') : 'TẤT CẢ BÀI VIẾT' ?></h2>
            <nav class="blog-tabs" aria-label="Danh mục bài viết">
                <?php foreach ($newsListTabs as $tab) { ?><a class="<?= (!empty($idl) && (int)$idl === (int)$tab['id']) ? 'active' : '' ?>" href="<?= $tab[$sluglang] ?>"><?= htmlspecialchars($tab['name' . $lang], ENT_QUOTES, 'UTF-8') ?></a><?php } ?>
                <a class="<?= empty($idl) ? 'active' : '' ?>" href="blog">Tất cả</a>
            </nav>
        </div>
        <?php if (!empty($news)) { ?>
            <div class="blog-grid">
                <?php foreach ($news as $v) { ?>
                    <article class="blog-card">
                        <a class="blog-card__image scale-img" href="<?= $v[$sluglang] ?>" title="<?= htmlspecialchars($v['name' . $lang], ENT_QUOTES, 'UTF-8') ?>"><img class="lazy" onerror="this.src='<?= THUMBS ?>/520x360x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/520x360x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= htmlspecialchars($v['name' . $lang], ENT_QUOTES, 'UTF-8') ?>"></a>
                        <h3><a href="<?= $v[$sluglang] ?>"><?= htmlspecialchars($v['name' . $lang], ENT_QUOTES, 'UTF-8') ?></a></h3>
                        <p><?= htmlspecialchars(trim(strip_tags(htmlspecialchars_decode($v['desc' . $lang] ?? ''))), ENT_QUOTES, 'UTF-8') ?></p>
                    </article>
                <?php } ?>
            </div>
        <?php } else { ?><div class="alert alert-warning w-100"><strong><?= khongtimthayketqua ?></strong></div><?php } ?>
        <?php if (!empty($paging)) { ?><div class="blog-pagination pagination-home"><?= $paging ?></div><?php } ?>
    </section>
</div>
