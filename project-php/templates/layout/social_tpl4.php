<div class="social-template-4">
    <div class="social-topbar">
        <div class="social-topbar-text">
            <span>Kết nối với chúng tôi:</span>
        </div>
        <div class="social-topbar-icons">
            <?php foreach ($social as $k => $v) { ?>
                <a href="<?= $v['link'] ?>" target="_blank" class="social-topbar-item" title="<?= $v['name' . $lang] ?>">
                    <img class="" src="<?= THUMBS ?>/20x20x2/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>">
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<style>
.social-template-4 {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 99999;
    background: rgba(0, 0, 0, 0.8);
    color: #fff;
    font-size: 12px;
}
.social-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 15px;
    max-width: 1200px;
    margin: 0 auto;
}
.social-topbar-text span {
    font-weight: 500;
    letter-spacing: 0.5px;
}
.social-topbar-icons {
    display: flex;
    gap: 10px;
}
.social-topbar-item {
    display: inline-block;
    transition: transform 0.3s;
}
.social-topbar-item img {
    border-radius: 50%;
}
.social-topbar-item:hover {
    transform: scale(1.2);
}

/* Push body down so it doesn't overlap header */
body {
    padding-top: 30px;
}

@media (max-width: 767px) {
    .social-topbar {
        justify-content: center;
    }
    .social-topbar-text {
        display: none;
    }
}
</style>
