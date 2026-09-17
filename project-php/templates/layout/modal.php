<?php if (!empty($popup)) { ?>
    <!-- Modal popup -->
    <div id="popup" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="popupModalLabel"><?= $popup['name' . $lang] ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <a href="<?= $popup['link'] ?>">
                        <?= $func->getImage(['sizes' => '800x530x1', 'upload' => UPLOAD_PHOTO_L, 'image' => $popup['photo'], 'alt' => 'Popup']) ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php } ?>

<!-- Modal quickview -->
<div class="modal fade" id="popup-quickview" tabindex="-1" aria-labelledby="popup-quickviewLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fs-5" id="popup-quickviewLabel"><?= sanpham ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<!-- Mini cart -->
<div class="side-overlay side-cmini-overlay"></div>
<aside class="side-fixed side-cmini" id="side-mini-cart" aria-hidden="true" aria-label="<?= giohangcuaban ?>">
    <div class="side-fixed-wrap">
        <div class="cmini">
            <div class="cmini-wrap">
                <div class="cmini-head">
                    <div class="t-gr">
                        <span class="t-text"><?= giohang ?></span>
                        <div class="mona-cart-qty"><span class="t-num count-cart"><?= (int)$cartQty ?></span></div>
                    </div>
                    <button type="button" class="side-close no-style side-cmini-close" aria-label="<?= donglai ?>">
                        <i class="fas fa-times close icon"></i>
                    </button>
                </div>
                <div class="mini-cart-content"></div>
            </div>
        </div>
    </div>
</aside>

<?php if (!empty($qrzalo['idzalo'])) {
    $link_zalo = $func->checkLinkZalo($qrzalo['idzalo'], $qrzalo['zalo'], $deviceType, $isIOS);
?>
    <!-- Modal qrzalo -->
    <div id="qrzalo" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="qrzaloModalLabel">Zalo: <?= $qrzalo['zalo'] ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-2">
                        <a target="_blank" href="<?= $link_zalo ?>">
                            <?= $func->getImage(['sizes' => '200x200x2', 'upload' => UPLOAD_PHOTO_L, 'image' => $qrzalo['photo'], 'alt' => 'qrzalo']) ?>
                        </a>
                    </div>
                    <a target="_blank" href="<?= $link_zalo ?>" class="btn btn-primary text-sm">Nhắn tin</a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
