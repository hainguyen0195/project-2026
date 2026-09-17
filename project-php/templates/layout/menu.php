<div class="menu-nav">
    <ul id="menu-primary" class="menu-list">
        <li class="parent fz16 fw6 menu-item menu-item-has-children dropdown<?= ($com == 'san-pham') ? ' current-menu-item' : '' ?>">
            <a class="menu-link" href="san-pham" title="<?= sanpham ?>"><?= sanpham ?> <i class="fas fa-chevron-down"></i></a>
            <?php if (!empty($productListMenu)) { ?>
                <ul class="menu-list child">
                    <?php foreach ($productListMenu as $v) {
                        $name = htmlspecialchars($v['name' . $lang] ?? '', ENT_QUOTES, 'UTF-8');
                        $slug = htmlspecialchars($v[$sluglang] ?? '', ENT_QUOTES, 'UTF-8');
                    ?>
                        <li class="parent fz16 fw6 menu-item">
                            <a class="menu-link" href="<?= $slug ?>" title="<?= $name ?>"><?= $name ?></a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </li>
        <li class="parent fz16 fw6 menu-item menu-item-has-children dropdown<?= ($com == 'best-saler') ? ' current-menu-item' : '' ?>">
            <a class="menu-link" href="best-saler" title="<?= bestsaler ?>"><?= bestsaler ?></a>
        </li>
        <li class="parent fz16 fw6 menu-item<?= ($com == 'flash-sale') ? ' current-menu-item' : '' ?>">
            <a class="menu-link" href="flash-sale" title="<?= flashsale ?>"><?= flashsale ?></a>
        </li>
        <li class="parent fz16 fw6 menu-item<?= ($com == 've-comi') ? ' current-menu-item' : '' ?>">
            <a class="menu-link" href="ve-comi" title="<?= vecomi ?>"><?= vecomi ?></a>
        </li>
        <li class="parent fz16 fw6 menu-item<?= ($com == 'blog') ? ' current-menu-item' : '' ?>">
            <a class="menu-link" href="blog" title="<?= blog ?>"><?= blog ?></a>
        </li>
    </ul>
</div>