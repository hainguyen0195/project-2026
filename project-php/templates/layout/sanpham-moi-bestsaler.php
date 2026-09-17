<?php if (!empty($productBestsaler) || !empty($productNew)) { ?>
    <section class="sec-htab ss-pd-b">
        <div class="container-custom">
            <div class="htab tabJS" data-product-tabs>
                <div class="htab-wrap">
                    <div class="htab-head" role="tablist" aria-label="Nhóm sản phẩm">
                        <?php if (!empty($productBestsaler)) { ?><button class="t-text tabBtn" type="button" role="tab">Best Seller</button><?php } ?>
                        <?php if (!empty($productBestsaler) && !empty($productNew)) { ?><span class="htab-switch" aria-hidden="true"></span><?php } ?>
                        <?php if (!empty($productNew)) { ?><button class="t-text tabBtn" type="button" role="tab">Hàng mới về</button><?php } ?>
                    </div>

                    <div class="htab-block">
                        <?php foreach (array_filter(array($productBestsaler, $productNew)) as $products) { ?>
                            <div class="htab-ctn tabPanel" role="tabpanel">
                                <div class="sprd-list">
                                    <?php foreach ($products as $v) echo $func->renderProductCard($v); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.querySelectorAll('[data-product-tabs]').forEach(function(tab) {
            var buttons = tab.querySelectorAll('.tabBtn');
            var panels = tab.querySelectorAll('.tabPanel');
            var stickyHead = tab.querySelector('.htab-head');
            var stickyTop = window.matchMedia('(max-width: 768px)').matches ? 72 : 80;
            function updateStickyState() {
                if (!stickyHead) return;
                var tabRect = tab.getBoundingClientRect();
                var headRect = stickyHead.getBoundingClientRect();
                stickyHead.classList.toggle('is-stuck', tabRect.top <= stickyTop && tabRect.bottom > headRect.height + stickyTop);
            }
            updateStickyState();
            window.addEventListener('scroll', updateStickyState, { passive: true });
            window.addEventListener('resize', function() {
                stickyTop = window.matchMedia('(max-width: 768px)').matches ? 72 : 80;
                updateStickyState();
            }, { passive: true });
            buttons.forEach(function(button, index) {
                button.setAttribute('aria-selected', index === 0 ? 'true' : 'false');
                button.classList.toggle('active', index === 0);
                if (panels[index]) panels[index].classList.toggle('active', index === 0);
                button.addEventListener('click', function() {
                    buttons.forEach(function(item, itemIndex) {
                        var selected = itemIndex === index;
                        item.classList.toggle('active', selected);
                        item.setAttribute('aria-selected', selected ? 'true' : 'false');
                        if (panels[itemIndex]) panels[itemIndex].classList.toggle('active', selected);
                    });
                    tab.classList.toggle('is-new-selected', index === 1);
                });
            });
        });
    </script>
<?php } ?>
