<?php
$faqHome = $d->rawQuery("select name$lang, desc$lang from #_news where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('cau-hoi-thuong-gap'));
if (!empty($faqHome)) { ?>
    <div class="wrap-faq py-5">
        <div class="container-custom">
            <div class="wrap-content">

                <div class="title-product"><span>CÁC CÂU HỎI THƯỜNG GẶP</span></div>
                <div class="row">
                    <div class="col-12">
                        <div class="accordion by-faq-accordion" id="faqAccordion">
                            <?php foreach ($faqHome as $k => $v) { ?>
                                <div class="accordion-item by-faq-item">
                                    <h2 class="accordion-header by-faq-header" id="faqHeading<?= $k ?>">
                                        <button class="accordion-button by-faq-button <?= ($k == 0) ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse<?= $k ?>" aria-expanded="<?= ($k == 0) ? 'true' : 'false' ?>" aria-controls="faqCollapse<?= $k ?>">
                                            <?= $v['name' . $lang] ?>
                                        </button>
                                    </h2>
                                    <div id="faqCollapse<?= $k ?>" class="accordion-collapse collapse <?= ($k == 0) ? 'show' : '' ?>" aria-labelledby="faqHeading<?= $k ?>" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body by-faq-body content-ck">
                                            <?= htmlspecialchars_decode($v['desc' . $lang] ?? '') ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>