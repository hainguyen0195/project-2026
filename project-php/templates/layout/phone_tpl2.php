<div class="phone-template-2">
    <div class="phone-bar">
        <?php if (!empty($optsetting['hotline'])) { ?>
        <a href="tel:<?= $func->parsePhone($optsetting['hotline']) ?>" class="pb-item phone-item">
            <div class="pb-icon"><i class="fas fa-phone-alt"></i></div>
            <div class="pb-text">Gọi điện</div>
        </a>
        <?php } ?>

        <?php if (!empty($qrzalo['zalo'])) { 
            $link_zalo = (!empty($qrzalo['idzalo'])) ? $func->checkLinkZalo($qrzalo['idzalo'], $qrzalo['zalo'], $deviceType, $isIOS) : 'https://zalo.me/' . $func->parsePhone($qrzalo['zalo']);
        ?>
        <a href="<?= $link_zalo ?>" target="_blank" class="pb-item zalo-item">
            <div class="pb-icon">
                <img src="assets/images/zalo-combo.png" alt="Zalo" style="width:24px; filter: brightness(0) invert(1);">
            </div>
            <div class="pb-text">Zalo</div>
        </a>
        <?php } ?>

        <?php if (!empty($optsetting['fanpage'])) { ?>
        <a href="https://m.me/<?= $func->getNameFacebook($optsetting['fanpage']) ?>" target="_blank" class="pb-item messenger-item">
            <div class="pb-icon"><i class="fab fa-facebook-messenger"></i></div>
            <div class="pb-text">Nhắn tin</div>
        </a>
        <?php } ?>

        <?php if (!empty($optsetting['link_googlemaps'])) { ?>
        <a href="<?= $optsetting['link_googlemaps'] ?>" target="_blank" class="pb-item map-item">
            <div class="pb-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div class="pb-text">Chỉ đường</div>
        </a>
        <?php } ?>
    </div>
</div>

<style>
.phone-template-2 {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 9999;
    background: #fff;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}
.phone-bar {
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 8px 0;
    margin: 0 auto;
    max-width: 600px; /* Optional constraint on desktop */
}
.pb-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #555;
    flex: 1;
    transition: all 0.3s;
}
.pb-item:hover {
    color: var(--primary);
    transform: translateY(-2px);
}
.pb-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    margin-bottom: 4px;
    color: #fff;
}
.pb-text {
    font-size: 11px;
    font-weight: 500;
}

/* Colors for specific items */
.phone-item .pb-icon { background: #28a745; animation: phoneRing 1.5s infinite ease-in-out; }
.zalo-item .pb-icon { background: #0088cc; }
.messenger-item .pb-icon { background: #0084ff; }
.map-item .pb-icon { background: #dc3545; }

@keyframes phoneRing {
    0% { transform: rotate(0) scale(1) skew(1deg); }
    10% { transform: rotate(-25deg) scale(1) skew(1deg); }
    20% { transform: rotate(25deg) scale(1) skew(1deg); }
    30% { transform: rotate(-25deg) scale(1) skew(1deg); }
    40% { transform: rotate(25deg) scale(1) skew(1deg); }
    50% { transform: rotate(0) scale(1) skew(1deg); }
    100% { transform: rotate(0) scale(1) skew(1deg); }
}

/* Hide scroll to top if template 2 is active so it doesn't overlap too badly, 
   or push scroll-to-top up */
#scrollToTop {
    bottom: 80px !important;
}
</style>
