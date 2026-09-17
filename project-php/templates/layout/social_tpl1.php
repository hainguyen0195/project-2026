<?php global $lang; foreach ($social as $k => $v) { ?>
    <a href="<?= $v['link'] ?>" target="_blank" class="me-2 d-inline-block transition-hover">
        <img src="<?= THUMBS ?>/38x37x2/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>" style="border-radius: 5px;" onerror="this.src='assets/images/noimage.png';">
    </a>
<?php } ?>
<style>
.transition-hover {
    transition: transform 0.3s;
}
.transition-hover:hover {
    transform: translateY(-3px);
}
</style>
