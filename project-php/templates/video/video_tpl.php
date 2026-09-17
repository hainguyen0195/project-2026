<div class="title-main"><span><?= @$titleMain ?></span></div>
<?php if (isset($video) && count($video) > 0) { ?>
    <div class="row-video row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
        <?php foreach ($video as $k => $v) { ?>
            <div class="col col-video">
                <div class="video">
                    <div class="pic-video">
                        <a class="scale-img" data-fancybox="video" data-src="<?= $func->get_youtube_shorts($v['link_video']) ?>" title="<?= $v['name' . $lang] ?>">
                            <?= $func->getImage(['class' => 'lazy w-100', 'size-error' => '480x360x1', 'url' => 'https://img.youtube.com/vi/' . $func->getYoutube($v['link_video']) . '/0.jpg', 'alt' => $v['name' . $lang]]) ?>
                        </a>
                    </div>
                    <h3 class="name-video">
                        <a class="text-split" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                    </h3>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
<?php } else { ?>
    <div class="alert alert-warning" role="alert">
        <strong><?= khongtimthayketqua ?></strong>
    </div>
<?php } ?>