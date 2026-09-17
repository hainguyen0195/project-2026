<?php
$cartQty = (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) ? count($_SESSION['cart']) : 0;
$brandName = htmlspecialchars($setting['name' . $lang] ?? ($setting['namevi'] ?? 'Logo'), ENT_QUOTES, 'UTF-8');
$logoSrc = UPLOAD_PHOTO_L . $logo['photo'];
$hotlineTel = preg_replace('/\s+/', '', (string)($optsetting['hotline'] ?? ''));
$isHome = (!empty($source) && $source === 'index');
$sloganItem = (!empty($sloganheader[0])) ? $sloganheader[0] : null;
?>
<header class="hd<?= $isHome ? ' hd-home' : '' ?>">
    <div class="container">
        <div class="hd-wrap">
            <?php if (!empty($sloganItem)) { ?>
                <div class="hd-top">
                    <div class="t-gr">
                        <a href="" class="t-text">
                            <?= htmlspecialchars($sloganItem['name' . $lang] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($sloganItem['text' . $lang])) { ?>
                                <span class="fw-7"><?= htmlspecialchars($sloganItem['text' . $lang], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php } ?>
                        </a>
                    </div>
                </div>
            <?php } ?>
            <div class="hd-bot">
                <div class="hd-group">
                    <a class="hd-burger" id="hamburger" href="#menu" title="Menu" aria-controls="menu" aria-expanded="false">
                        <div class="burger">
                            <div class="hamburger">
                                <div class="line"></div>
                                <div class="line"></div>
                                <div class="line"></div>
                            </div>
                        </div>
                    </a>

                    <div class="hd-nav">
                        <?php include TEMPLATE . LAYOUT . "menu.php"; ?>
                    </div>

                    <div class="hd-logo">
                        <a href="" title="<?= trangchu ?>">
                            <img
                                src="<?= $logoSrc ?>"
                                onerror="this.src='<?= THUMBS ?>/205x95x2/assets/images/noimage.png';"
                                alt="<?= $brandName ?>"
                                title="<?= $brandName ?>" />
                        </a>
                    </div>

                    <div class="hd-last">
                        <div class="hd-control">
                            <div class="b-control hd-open-search" title="<?= timkiem ?>">
                                <span class="i-ctr i-search">
                                    <img src="assets/images/hd/icon-search.svg" alt="<?= timkiem ?>" />
                                </span>
                            </div>
                            <button type="button" class="b-control i-cart side-open no-style" title="<?= giohang ?>" aria-label="<?= giohang ?>" aria-controls="side-mini-cart" aria-expanded="false">
                                <div class="t-pos">
                                    <div class="mona-cart-qty">
                                        <span class="t-num count-cart"><?= (int)$cartQty ?></span>
                                    </div>
                                </div>
                                <span class="i-ctr">
                                    <img src="assets/images/hd/icon-cart.svg" alt="<?= giohang ?>" />
                                </span>
                            </button>
                        </div>
                        <?php if (!empty($hotlineTel)) { ?>
                            <div class="hd-btn">
                                <a class="btn" href="tel:<?= htmlspecialchars($hotlineTel, ENT_QUOTES, 'UTF-8') ?>">
                                    <img src="assets/images/hd/hot.png" alt="icon">
                                    <span class="text"><?= lienhenhay ?></span>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="hd-search-wrap">
        <div class="hd-search-close">
            <span class="t-text"><?= donglai ?></span>
        </div>
        <div class="hd-search-form">
            <form method="get" class="searchform" action="" onsubmit="return false;">
                <div class="hd-search-ip">
                    <input
                        type="text"
                        name="keyword"
                        id="keyword-header"
                        value="<?= (!empty($_GET['keyword'])) ? htmlspecialchars($_GET['keyword'], ENT_QUOTES, 'UTF-8') : '' ?>"
                        placeholder="<?= htmlspecialchars($setting['keysearch' . $lang] ?? nhaptukhoatimkiem, ENT_QUOTES, 'UTF-8') ?>"
                        onkeypress="doEnter(event,'keyword-header');" />
                    <button class="hd-search-voice" type="button" aria-label="Tìm kiếm bằng giọng nói" aria-pressed="false" aria-describedby="header-voice-status" title="Tìm kiếm bằng giọng nói">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true">
                            <rect x="9" y="2" width="6" height="12" rx="3" />
                            <path d="M5 10v2a7 7 0 0 0 14 0v-2M12 19v3M8 22h8" />
                        </svg>
                    </button>
                    <button class="hd-search-btn" type="button" onclick="onSearch('keyword-header');" title="<?= timkiem ?>">
                        <span class="icon">
                            <img src="assets/images/hd/icon-search.svg" alt="<?= timkiem ?>" />
                        </span>
                    </button>
                </div>
                <p id="header-voice-status" class="hd-voice-status" role="status" aria-live="polite"></p>
            </form>
        </div>
    </div>

    <?php include TEMPLATE . LAYOUT . "mmenu.php"; ?>
</header>
