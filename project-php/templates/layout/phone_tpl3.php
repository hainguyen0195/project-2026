<div class="phone-template-3">
    <div class="fab-wrapper">
        <input id="fabCheckbox" type="checkbox" class="fab-checkbox" />
        <label class="fab-button" for="fabCheckbox">
            <i class="fas fa-plus fab-icon-plus"></i>
            <i class="fas fa-times fab-icon-close"></i>
        </label>
        <div class="fab-wheel">
            <?php if (!empty($optsetting['hotline'])) { ?>
            <a href="tel:<?= $func->parsePhone($optsetting['hotline']) ?>" class="fab-action fab-action-phone" title="Gọi điện">
                <i class="fas fa-phone-alt"></i>
            </a>
            <?php } ?>

            <?php if (!empty($qrzalo['zalo'])) { 
                $link_zalo = (!empty($qrzalo['idzalo'])) ? $func->checkLinkZalo($qrzalo['idzalo'], $qrzalo['zalo'], $deviceType, $isIOS) : 'https://zalo.me/' . $func->parsePhone($qrzalo['zalo']);
            ?>
            <a href="<?= $link_zalo ?>" target="_blank" class="fab-action fab-action-zalo" title="Zalo">
                <img src="assets/images/zalo-combo.png" alt="Zalo" style="width:20px; filter: brightness(0) invert(1);">
            </a>
            <?php } ?>

            <?php if (!empty($optsetting['fanpage'])) { ?>
            <a href="https://m.me/<?= $func->getNameFacebook($optsetting['fanpage']) ?>" target="_blank" class="fab-action fab-action-messenger" title="Nhắn tin">
                <i class="fab fa-facebook-messenger"></i>
            </a>
            <?php } ?>

            <?php if (!empty($optsetting['link_googlemaps'])) { ?>
            <a href="<?= $optsetting['link_googlemaps'] ?>" target="_blank" class="fab-action fab-action-map" title="Chỉ đường">
                <i class="fas fa-map-marker-alt"></i>
            </a>
            <?php } ?>
        </div>
    </div>
</div>

<style>
.phone-template-3 {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
}
.fab-wrapper {
    position: relative;
}
.fab-checkbox {
    display: none;
}
.fab-button {
    width: 60px;
    height: 60px;
    background: var(--primary, #d9534f);
    border-radius: 50%;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
    z-index: 2;
    position: relative;
    animation: pulseFab 2s infinite;
}
.fab-icon-close {
    display: none;
}
.fab-checkbox:checked + .fab-button {
    transform: rotate(135deg);
    background: #555;
    animation: none;
}
.fab-checkbox:checked + .fab-button .fab-icon-plus {
    display: none;
}
.fab-checkbox:checked + .fab-button .fab-icon-close {
    display: block;
    transform: rotate(-135deg); /* keep it upright */
}

.fab-wheel {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 60px;
    height: 60px;
    z-index: 1;
}
.fab-action {
    position: absolute;
    width: 45px;
    height: 45px;
    background: #aaa;
    border-radius: 50%;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    text-decoration: none;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    opacity: 0;
    bottom: 7.5px;
    right: 7.5px;
    transform: scale(0);
}

.fab-action:hover {
    transform: scale(1.1) !important;
    color: #fff;
}

/* Define positions when open */
.fab-checkbox:checked ~ .fab-wheel .fab-action:nth-child(1) {
    bottom: 80px; right: 7.5px; opacity: 1; transform: scale(1);
}
.fab-checkbox:checked ~ .fab-wheel .fab-action:nth-child(2) {
    bottom: 60px; right: 60px; opacity: 1; transform: scale(1);
}
.fab-checkbox:checked ~ .fab-wheel .fab-action:nth-child(3) {
    bottom: 7.5px; right: 80px; opacity: 1; transform: scale(1);
}
.fab-checkbox:checked ~ .fab-wheel .fab-action:nth-child(4) {
    bottom: -50px; right: 60px; opacity: 1; transform: scale(1);
} /* If there's 4, might need to adjust positions */

/* Colors */
.fab-action-phone { background: #28a745; }
.fab-action-zalo { background: #0088cc; }
.fab-action-messenger { background: #0084ff; }
.fab-action-map { background: #dc3545; }

@keyframes pulseFab {
    0% { box-shadow: 0 0 0 0 rgba(217, 83, 79, 0.7); }
    70% { box-shadow: 0 0 0 15px rgba(217, 83, 79, 0); }
    100% { box-shadow: 0 0 0 0 rgba(217, 83, 79, 0); }
}

@media (max-width: 767px) {
    .phone-template-3 {
        bottom: 20px;
        right: 20px;
    }
}
</style>
