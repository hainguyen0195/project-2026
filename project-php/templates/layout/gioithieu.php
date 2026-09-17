<?php
$about = $d->rawQueryOne(
    "select name$lang, desc$lang, content$lang, tieuchi from #_static where type = ? limit 0,1",
    array('gioi-thieu')
);

$aboutCriteria = !empty($about['tieuchi']) ? json_decode($about['tieuchi'], true) : array();
if (!is_array($aboutCriteria)) $aboutCriteria = array();

$aboutTitles = array();
foreach ($aboutCriteria as $criterion) {
    $criterionText = is_array($criterion) ? ($criterion['label'] ?? '') : $criterion;
    $criterionText = trim((string) $criterionText);
    if ($criterionText !== '') $aboutTitles[] = $criterionText;
}

$aboutDescription = !empty($about['desc' . $lang])
    ? $about['desc' . $lang]
    : ($about['content' . $lang] ?? '');
?>

<?php if (!empty($about)) { ?>
    <section class="sec-abi ss-pd">
        <div class="container">
            <div class="abi">
                <div class="abi-wrap">
                    <div class="abi-row row">
                        <div class="col col-6">
                            <?php if (!empty($aboutTitles)) { ?>
                                <div class="abi-title">
                                    <?php foreach ($aboutTitles as $titleIndex => $titleText) { ?>
                                        <div class="abi-ani-text linex<?= $titleIndex + 1 ?>">
                                            <div class="abi-text">
                                                <span class="t-text abi-text-ani"><?= htmlspecialchars($titleText, ENT_QUOTES, 'UTF-8') ?></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="col col-6">
                            <div class="abi-content">
                                <?php if (!empty($aboutDescription)) { ?>
                                    <div class="t-des" data-aos="fade-up">
                                        <?= htmlspecialchars_decode($aboutDescription) ?>
                                    </div>
                                <?php } ?>

                                <a class="trans btn abi-btn" href="gioi-thieu">
                                    <span class="text"><?= xemthem ?></span>
                                    <i class="fas fa-arrow-right icon"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
