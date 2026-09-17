<div class="social-template-2">
    <?php global $lang;
    foreach ($social as $k => $v) { ?>
        <a class=" btn-frame text-decoration-none" target="_blank" href="<?= $v['link'] ?>">
            <div class="animated infinite zoomIn kenit-alo-circle"></div>
            <div class="animated infinite pulse kenit-alo-circle-fill"></div>
            <div class="hover_bond amination-ring">
                <i class=""><img src="<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>"></i>
            </div>
        </a>
    <?php } ?>
</div>

<?php
$side_offset = !empty($social_template_offset_side) ? $social_template_offset_side : '0';
?>
<style>
    .social-template-2 {
        position: fixed;
        /* <?php if (!empty($social_template_offset_bottom)) { ?>bottom: <?= $social_template_offset_bottom ?>;
        <?php } else { ?>top: 50%;
        transform: translateY(-50%);
        <?php } ?><?= ($social_template_position == 'right') ? 'right: ' . $side_offset . ';' : 'left: ' . $side_offset . ';' ?> */
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 5px;
        bottom: 12%;
        right: 0;
    }

    .social-sidebar-item {
        display: flex;
        width: 45px;
        height: 45px;
        align-items: center;
        justify-content: center;
        background: #fff;
        <?= ($social_template_position == 'right') ? 'border-radius: 5px 0 0 5px;' : 'border-radius: 0 5px 5px 0;' ?>transition: all 0.3s ease;
        padding: 5px;
        position: relative;
    }

    .social-sidebar-item img {
        width: 100%;
        height: auto;
        border-radius: 3px;
    }

    .social-sidebar-item:hover {
        width: 55px;
        padding-right: 15px;
        background: #f8f9fa;
    }
</style>