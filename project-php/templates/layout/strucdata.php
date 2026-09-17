<?php
$schemaProductInfo = (!empty($aiProduct) && is_array($aiProduct)) ? $aiProduct : array();
$schemaProductVisible = !isset($schemaProductInfo['enabled']) || (string)$schemaProductInfo['enabled'] !== '0';
$isAiProductSchema = ($template == 'product/product_detail' && !empty($config['website']['ai_product']) && $schemaProductVisible && !empty($rowDetail['id']));
$schemaArticleInfo = (!empty($aiArticle) && is_array($aiArticle)) ? $aiArticle : array();
$schemaArticleVisible = !isset($schemaArticleInfo['enabled']) || (string)$schemaArticleInfo['enabled'] !== '0';
$isAiArticleSchema = in_array($template, array('news/news_detail', 'project/project_detail', 'recruitment/recruitment_detail', 'service/service_detail', 'solution/solution_detail'), true)
    && !empty($config['website']['ai_article'])
    && $schemaArticleVisible
    && !empty($rowDetail['id']);
if (!$isAiProductSchema && !$isAiArticleSchema && !empty(@$seoDB['schema' . $seolang])) {
    $legacySchemaRaw = htmlspecialchars_decode($seoDB['schema' . $seolang]);
    $legacySchema = json_decode($legacySchemaRaw, true);
    if ($template == 'product/product_detail' && is_array($legacySchema)) {
        // Do not publish stale or manually fabricated review data from old schemas.
        unset($legacySchema['review'], $legacySchema['aggregateRating']);
        $legacyProductType = !empty($rowDetail['type']) ? $rowDetail['type'] : ($type ?? 'san-pham');
        if (!empty($config['product'][$legacyProductType]['comment']) && !empty($productRatingTotal)) {
            $legacySchema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => (float)$productRatingAverage,
                'reviewCount' => (int)$productRatingTotal
            );
        }
        $legacySchemaRaw = json_encode($legacySchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
?>
    <script type="application/ld+json">
        <?= $legacySchemaRaw ?>
    </script>
<?php }
if ($isAiProductSchema) {
    $schemaAi = (!empty($aiProduct) && is_array($aiProduct)) ? $aiProduct : array();
    $schemaText = function ($value) {
        return trim(htmlspecialchars_decode(strip_tags((string)$value), ENT_QUOTES));
    };
    $schemaProductName = $schemaText($rowDetail['name' . $lang] ?? '');
    $schemaProductUrl = $func->getCurrentPageURL_CANO();
    $schemaImages = array();
    foreach (array_merge(array(array('photo' => $rowDetail['photo'])), !empty($rowDetailPhoto) ? $rowDetailPhoto : array()) as $schemaImage) {
        if (!empty($schemaImage['photo'])) $schemaImages[] = $configBase . UPLOAD_PRODUCT_L . $schemaImage['photo'];
    }
    $schemaImages = array_values(array_unique($schemaImages));
    $schemaBrand = !empty($schemaAi['brand'])
        ? $schemaText($schemaAi['brand'])
        : (!empty($productBrand['name' . $lang]) ? $schemaText($productBrand['name' . $lang]) : $schemaText($setting['name' . $lang] ?? ''));
    $schemaAvailability = !empty($schemaAi['availability']) ? (string)$schemaAi['availability'] : 'InStock';
    $schemaCondition = !empty($schemaAi['condition']) ? (string)$schemaAi['condition'] : 'NewCondition';
    $schemaAvailabilityUrl = 'https://schema.org/' . (in_array($schemaAvailability, array('InStock', 'OutOfStock', 'PreOrder', 'BackOrder'), true) ? $schemaAvailability : 'InStock');
    $schemaConditionUrl = 'https://schema.org/' . (in_array($schemaCondition, array('NewCondition', 'UsedCondition', 'RefurbishedCondition'), true) ? $schemaCondition : 'NewCondition');
    $schemaPrice = (float)($rowDetail['sale_price'] > 0 ? $rowDetail['sale_price'] : $rowDetail['regular_price']);
    $schemaProduct = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        '@id' => $schemaProductUrl . '#product',
        'name' => $schemaProductName,
        'url' => $schemaProductUrl,
        'image' => $schemaImages,
        'description' => $schemaText($seo->get('description')),
        'sku' => !empty($rowDetail['code']) ? $schemaText($rowDetail['code']) : 'SP' . (int)$rowDetail['id'],
        'brand' => array('@type' => 'Brand', 'name' => $schemaBrand),
    );
    if ($schemaPrice > 0) {
        $schemaProduct['offers'] = array(
            '@type' => 'Offer',
            'url' => $schemaProductUrl,
            'priceCurrency' => 'VND',
            'price' => $schemaPrice,
            'availability' => $schemaAvailabilityUrl,
            'itemCondition' => $schemaConditionUrl,
            'seller' => array('@type' => 'Organization', 'name' => $schemaText($setting['name' . $lang] ?? ''))
        );
    }
    if ($schemaImages === array()) unset($schemaProduct['image']);
    if (!empty($schemaAi['mpn'])) $schemaProduct['mpn'] = $schemaText($schemaAi['mpn']);
    if (!empty($schemaAi['gtin'])) {
        $schemaGtin = preg_replace('/\D+/', '', (string)$schemaAi['gtin']);
        $schemaGtinField = array(8 => 'gtin8', 12 => 'gtin12', 13 => 'gtin13', 14 => 'gtin14');
        if (isset($schemaGtinField[strlen($schemaGtin)])) $schemaProduct[$schemaGtinField[strlen($schemaGtin)]] = $schemaGtin;
    }
    $schemaProductType = !empty($rowDetail['type']) ? $rowDetail['type'] : ($type ?? 'san-pham');
    if (!empty($config['product'][$schemaProductType]['comment']) && !empty($productRatingTotal)) {
        $schemaProduct['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => (float)$productRatingAverage,
            'reviewCount' => (int)$productRatingTotal
        );
    }
    $schemaPropertyLabels = array(
        'purchase_note' => 'Thông tin mua sản phẩm',
        'shipping' => 'Giao hàng',
        'return_policy' => 'Đổi trả',
        'warranty' => 'Bảo hành',
        'origin' => 'Xuất xứ'
    );
    foreach ($schemaPropertyLabels as $schemaKey => $schemaLabel) {
        if (!empty($schemaAi[$schemaKey])) {
            $schemaProduct['additionalProperty'][] = array(
                '@type' => 'PropertyValue',
                'name' => $schemaLabel,
                'value' => $schemaText($schemaAi[$schemaKey])
            );
        }
    }
?>
    <script type="application/ld+json">
        <?= json_encode($schemaProduct, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
    </script>
<?php } ?>
<?php if ($isAiArticleSchema) {
    $schemaArticleAi = (!empty($aiArticle) && is_array($aiArticle)) ? $aiArticle : array();
    $schemaArticleText = function ($value) {
        return trim(htmlspecialchars_decode(strip_tags((string)$value), ENT_QUOTES));
    };
    $schemaArticleName = $schemaArticleText($rowDetail['name' . $lang] ?? '');
    $schemaArticleUrl = $func->getCurrentPageURL_CANO();
    $schemaArticleType = !empty($schemaArticleAi['article_type']) && in_array($schemaArticleAi['article_type'], array('Article', 'NewsArticle', 'BlogPosting'), true)
        ? $schemaArticleAi['article_type']
        : 'Article';
    $schemaArticleImage = !empty($rowDetail['photo']) ? array($configBase . UPLOAD_NEWS_L . $rowDetail['photo']) : array();
    $schemaArticleDescription = !empty($schemaArticleAi['summary'])
        ? $schemaArticleText($schemaArticleAi['summary'])
        : $schemaArticleText($seo->get('description'));
    $schemaArticleAuthorName = !empty($schemaArticleAi['author_name'])
        ? $schemaArticleText($schemaArticleAi['author_name'])
        : $schemaArticleText($setting['name' . $lang] ?? '');
    $schemaArticle = array(
        '@context' => 'https://schema.org',
        '@type' => $schemaArticleType,
        '@id' => $schemaArticleUrl . '#article',
        'mainEntityOfPage' => array('@type' => 'WebPage', '@id' => $schemaArticleUrl),
        'headline' => $schemaArticleName,
        'url' => $schemaArticleUrl,
        'description' => $schemaArticleDescription,
        'datePublished' => !empty($rowDetail['date_created']) ? date(DATE_ATOM, (int)$rowDetail['date_created']) : date(DATE_ATOM),
        'dateModified' => !empty($rowDetail['date_updated']) ? date(DATE_ATOM, (int)$rowDetail['date_updated']) : (!empty($rowDetail['date_created']) ? date(DATE_ATOM, (int)$rowDetail['date_created']) : date(DATE_ATOM)),
        'author' => array('@type' => 'Person', 'name' => $schemaArticleAuthorName),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => $schemaArticleText($setting['name' . $lang] ?? ''),
            'logo' => array('@type' => 'ImageObject', 'url' => $configBase . UPLOAD_PHOTO_L . (@$logo['photo'] ?? ''))
        )
    );
    if ($schemaArticleImage) $schemaArticle['image'] = $schemaArticleImage;
    if (!empty($schemaArticleAi['author_role'])) $schemaArticle['author']['jobTitle'] = $schemaArticleText($schemaArticleAi['author_role']);
    if (!empty($schemaArticleAi['main_topic'])) $schemaArticle['about'] = array('@type' => 'Thing', 'name' => $schemaArticleText($schemaArticleAi['main_topic']));
    if (!empty($schemaArticleAi['audience'])) $schemaArticle['audience'] = array('@type' => 'Audience', 'audienceType' => $schemaArticleText($schemaArticleAi['audience']));
    if (!empty($schemaArticleAi['entities'])) {
        $schemaArticleKeywords = preg_split('/[,\n]+/', (string)$schemaArticleAi['entities']);
        $schemaArticleKeywords = array_values(array_filter(array_map($schemaArticleText, $schemaArticleKeywords)));
        if ($schemaArticleKeywords) $schemaArticle['keywords'] = implode(', ', $schemaArticleKeywords);
    }
    if (!empty($newsList['name' . $lang])) $schemaArticle['articleSection'] = $schemaArticleText($newsList['name' . $lang]);
?>
    <script type="application/ld+json">
        <?= json_encode($schemaArticle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
    </script>
<?php } ?>
<?php if ($template == 'static/static') { ?>
    <!-- Static -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "NewsArticle",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "https://google.com/article"
            },
            "headline": "<?= @$static['name' . $lang] ?>",
            "image": [
                "<?= $configBase . UPLOAD_NEWS_L . @$static['photo'] ?>"
            ],
            "datePublished": "<?= date('Y-m-d', @$static['date_created']) ?>",
            "dateModified": "<?= date('Y-m-d', @$static['date_updated']) ?>",
            "author": {
                "@type": "Person",
                "name": "<?= @$setting['name' . $lang] ?>"
            },
            "publisher": {
                "@type": "Organization",
                "name": "Google",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?= $configBase . UPLOAD_PHOTO_L . @$logo['photo'] ?>"
                }
            },
            "description": "<?= $seo->get('description') ?>"
        }
    </script>
<?php } ?>
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?= @$setting['name' . $lang] ?>",
        "url": "<?= $configBase ?>",
        "sameAs": [
            <?php if (isset($social) && count($social) > 0) {
                $sum_social = count($social);
                foreach ($social as $key => $value) { ?> "<?= @$value['link'] ?>"
                    <?= (($key + 1) < $sum_social) ? ',' : '' ?>
            <?php }
            } ?>
        ],
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<?= $setting['address' . $lang] ?>",
            "addressRegion": "Ho Chi Minh",
            "postalCode": "70000",
            "addressCountry": "vi"
        }
    }
</script>
