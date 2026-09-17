<div class="social-template-3">
    <div class="social-fab-wrapper">
        <input id="socialFabCheckbox" type="checkbox" class="social-fab-checkbox" />
        <label class="social-fab-button" for="socialFabCheckbox" title="Kết nối với chúng tôi">
            <i class="fas fa-share-alt social-fab-icon-share"></i>
            <i class="fas fa-times social-fab-icon-close"></i>
        </label>
        <div class="social-fab-wheel">
            <?php 
            global $lang;
            $i = 1;
            foreach ($social as $k => $v) { ?>
                <a href="<?= $v['link'] ?>" target="_blank" class="social-fab-action social-item-<?= $i ?>" title="<?= $v['name' . $lang] ?>">
                    <img src="<?= THUMBS ?>/30x30x2/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" style="width: 100%; height: 100%; border-radius: 50%;" onerror="this.src='assets/images/noimage.png';">
                </a>
            <?php 
                $i++;
            } ?>
        </div>
    </div>
</div>

<?php
$side_offset = !empty($social_template_offset_side) ? $social_template_offset_side : '20px';
$bottom_offset = !empty($social_template_offset_bottom) ? $social_template_offset_bottom : '20px';
?>
<style>
.social-template-3 {
    position: fixed;
    bottom: <?= $bottom_offset ?>;
    <?= ($social_template_position == 'right') ? 'right: '.$side_offset.';' : 'left: '.$side_offset.';' ?>
    z-index: 99999;
}
.social-fab-wrapper {
    position: relative;
}
.social-fab-checkbox {
    display: none;
}
.social-fab-button {
    width: 55px;
    height: 55px;
    background: var(--primary, #007bff);
    border-radius: 50%;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
    z-index: 2;
    position: relative;
    animation: bounceFab 2s infinite;
}
.social-fab-icon-close {
    display: none;
}
.social-fab-checkbox:checked + .social-fab-button {
    transform: rotate(90deg);
    background: #555;
    animation: none;
}
.social-fab-checkbox:checked + .social-fab-button .social-fab-icon-share {
    display: none;
}
.social-fab-checkbox:checked + .social-fab-button .social-fab-icon-close {
    display: block;
    transform: rotate(-90deg);
}

.social-fab-wheel {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 55px;
    height: 55px;
    z-index: 1;
}
.social-fab-action {
    position: absolute;
    width: 40px;
    height: 40px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    opacity: 0;
    bottom: 7.5px;
    left: 7.5px;
    transform: scale(0);
}

.social-fab-action:hover {
    transform: scale(1.15) !important;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

/* Define positions when open. Assumes up to 5 icons */
.social-fab-checkbox:checked ~ .social-fab-wheel .social-item-1 {
    bottom: 70px; left: 7.5px; opacity: 1; transform: scale(1);
}
.social-fab-checkbox:checked ~ .social-fab-wheel .social-item-2 {
    bottom: 120px; left: 7.5px; opacity: 1; transform: scale(1);
}
.social-fab-checkbox:checked ~ .social-fab-wheel .social-item-3 {
    bottom: 170px; left: 7.5px; opacity: 1; transform: scale(1);
}
.social-fab-checkbox:checked ~ .social-fab-wheel .social-item-4 {
    bottom: 220px; left: 7.5px; opacity: 1; transform: scale(1);
}
.social-fab-checkbox:checked ~ .social-fab-wheel .social-item-5 {
    bottom: 270px; left: 7.5px; opacity: 1; transform: scale(1);
}

@keyframes bounceFab {
    0%, 20%, 50%, 80%, 100% {transform: translateY(0);} 
    40% {transform: translateY(-10px);} 
    60% {transform: translateY(-5px);} 
}

@media (max-width: 767px) {
    .social-template-3 {
        bottom: 20px;
        left: 20px;
    }
}
</style>
