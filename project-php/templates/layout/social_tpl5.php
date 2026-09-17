<div class="social-template-5">
    <?php global $lang; foreach ($social as $k => $v) { ?>
        <a href="<?= $v['link'] ?>" target="_blank" class="social-sidebar-right-item" title="<?= $v['name' . $lang] ?>">
            <img src="<?= THUMBS ?>/45x45x2/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" onerror="this.src='assets/images/noimage.png';">
            <span class="social-tooltip"><?= $v['name' . $lang] ?></span>
        </a>
    <?php } ?>
</div>

<?php
$side_offset = !empty($social_template_offset_side) ? $social_template_offset_side : '0';
?>
<style>
.social-template-5 {
    position: fixed;
    <?php if (!empty($social_template_offset_bottom)) { ?>
    bottom: <?= $social_template_offset_bottom ?>;
    <?php } else { ?>
    top: 50%;
    transform: translateY(-50%);
    <?php } ?>
    <?= ($social_template_position == 'left') ? 'left: '.$side_offset.';' : 'right: '.$side_offset.';' ?>
    z-index: 99999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 10px;
}
.social-sidebar-right-item {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
}
.social-sidebar-right-item img {
    width: 45px;
    height: 45px;
    object-fit: contain;
}
.social-sidebar-right-item:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}
.social-sidebar-right-item .social-tooltip {
    position: absolute;
    <?php if ($social_template_position == 'left') { ?>
    left: 55px;
    <?php } else { ?>
    right: 55px;
    <?php } ?>
    background: #333;
    color: #fff;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    pointer-events: none;
}
.social-sidebar-right-item .social-tooltip::after {
    content: '';
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    <?php if ($social_template_position == 'left') { ?>
    left: -4px;
    border-width: 5px 5px 5px 0;
    border-style: solid;
    border-color: transparent #333 transparent transparent;
    <?php } else { ?>
    right: -4px;
    border-width: 5px 0 5px 5px;
    border-style: solid;
    border-color: transparent transparent transparent #333;
    <?php } ?>
}
.social-sidebar-right-item:hover .social-tooltip {
    opacity: 1;
    visibility: visible;
    <?php if ($social_template_position == 'left') { ?>
    left: 60px;
    <?php } else { ?>
    right: 60px;
    <?php } ?>
}
</style>
