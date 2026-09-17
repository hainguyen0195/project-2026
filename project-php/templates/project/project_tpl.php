<?php if (!empty($idl)) { ?>
    <div class="project-category-heading" data-aos="fade-in" data-aos-duration="1000">
        <h1><?= $titleCate ?></h1>
    </div>

    <?php if (!empty($news)) {
        $featuredProject = $news[0];
        $projectSideItems = array_slice($news, 1, 5);
    ?>
        <div class="project-category-showcase">
            <article class="project-category-featured" data-aos="fade-up" data-aos-duration="1000">
                <a class="project-category-featured-image scale-img" href="<?= $featuredProject[$sluglang] ?>" title="<?= $featuredProject['name' . $lang] ?>">
                    <img class="lazy" onerror="this.src='<?= THUMBS ?>/650x520x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/650x520x1/<?= UPLOAD_NEWS_L . $featuredProject['photo'] ?>" alt="<?= $featuredProject['name' . $lang] ?>" />
                </a>
                <?php if (!empty($featuredProject['address' . $lang])) { ?>
                    <div class="project-category-featured-address"><?= strip_tags($featuredProject['address' . $lang]) ?></div>
                <?php } ?>
                <div class="project-category-featured-content">
                    <h2><a href="<?= $featuredProject[$sluglang] ?>" title="<?= $featuredProject['name' . $lang] ?>"><?= $featuredProject['name' . $lang] ?></a></h2>
                    <div class="project-category-featured-desc text-split"><?= $featuredProject['desc' . $lang] ?></div>
                </div>
            </article>

            <?php if (!empty($projectSideItems)) { ?>
                <div class="project-category-list">
                    <?php foreach ($projectSideItems as $projectItem) { ?>
                        <article class="project-category-item" data-aos="fade-up" data-aos-duration="1000">
                            <a class="project-category-item-image scale-img" href="<?= $projectItem[$sluglang] ?>" title="<?= $projectItem['name' . $lang] ?>">
                                <img class="lazy" onerror="this.src='<?= THUMBS ?>/230x135x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/230x135x1/<?= UPLOAD_NEWS_L . $projectItem['photo'] ?>" alt="<?= $projectItem['name' . $lang] ?>" />
                            </a>
                            <div class="project-category-item-content">
                                <h3><a class="text-split" href="<?= $projectItem[$sluglang] ?>" title="<?= $projectItem['name' . $lang] ?>"><?= $projectItem['name' . $lang] ?></a></h3>
                                <div class="project-category-item-desc text-split"><?= $projectItem['desc' . $lang] ?></div>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning w-100" role="alert"><strong><?= khongtimthayketqua ?></strong></div>
    <?php } ?>
<?php } else { ?>
    <div class="title-main" data-aos="fade-in" data-aos-duration="1000"><span><?= (!empty($titleCate)) ? $titleCate : @$titleMain ?></span></div>
    <?php if (!empty($news)) { ?>
        <div class="row-news row">
            <?php foreach ($news as $k => $v) { ?>
                <div class="news d-flex flex-wrap col-md-4 col-sm-6 col-12 pb-3" data-aos="fade-up" data-aos-duration="1000">
                    <a class="duan-card duan-card-<?= $k + 1 ?>" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                        <img onerror="this.src='<?= THUMBS ?>/700x700x1/assets/images/noimage.png';" src="<?= THUMBS ?>/700x700x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>">
                        <span class="duan-number"><?= str_pad($k + 1, 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="duan-card-info">
                            <small><?= !empty($v['address' . $lang]) ? strip_tags($v['address' . $lang]) : '' ?></small>
                            <strong><?= $v['name' . $lang] ?></strong>
                        </span>
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning w-100" role="alert"><strong><?= khongtimthayketqua ?></strong></div>
    <?php } ?>
<?php } ?>

<div class="pagination-home project-category-pagination w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
