<?php
class Functions
{
    private $d;
    private $hash;
    private $cache;

    function __construct($d, $cache)
    {
        $this->d = $d;
        $this->cache = $cache;
    }

    /**
     * Render menu by category levels or directly from news/product records.
     *
     * @param string $table     Base table: product or news.
     * @param string $type      Data type.
     * @param int    $level     Number of category levels (1-4).
     * @param bool   $showItems True: show records from the base table only.
     */
    public function showMenu($table = 'product', $type = 'san-pham', $level = 1, $showItems = false)
    {
        global $lang, $sluglang;

        if (!in_array($table, array('news', 'product'), true)) {
            return;
        }

        $level = max(1, min(4, (int) $level));
        $nameField = 'name' . $lang;
        $escape = static function ($value) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        };

        if ($showItems) {
            $items = $this->d->rawQuery(
                "SELECT name$lang, slugvi, slugen, id FROM #_" . $table . " WHERE type = ? AND find_in_set('hienthi',status) ORDER BY numb,id DESC",
                array($type)
            );

            if (empty($items)) {
                return;
            }

            echo '<ul>';
            foreach ($items as $item) {
                $name = $escape($item[$nameField] ?? '');
                $slug = $escape($item[$sluglang] ?? '');
                echo '<li><a class="transition" title="' . $name . '" href="' . $slug . '">' . $name . '</a></li>';
            }
            echo '</ul>';
            return;
        }

        $lists = $this->d->rawQuery(
            "SELECT name$lang, slugvi, slugen, id FROM #_" . $table . "_list WHERE type = ? AND find_in_set('hienthi',status) ORDER BY numb,id DESC",
            array($type)
        );

        if (empty($lists)) {
            return;
        }

        echo '<ul>';
        foreach ($lists as $list) {
            $name = $escape($list[$nameField] ?? '');
            $slug = $escape($list[$sluglang] ?? '');
            echo '<li><a class="transition" title="' . $name . '" href="' . $slug . '">' . $name . '</a>';

            if ($level > 1) {
                $cats = $this->d->rawQuery(
                    "SELECT name$lang, slugvi, slugen, id FROM #_" . $table . "_cat WHERE type = ? AND id_list = ? AND find_in_set('hienthi',status) ORDER BY numb,id DESC",
                    array($type, $list['id'])
                );

                if (!empty($cats)) {
                    echo '<ul>';
                    foreach ($cats as $cat) {
                        $name = $escape($cat[$nameField] ?? '');
                        $slug = $escape($cat[$sluglang] ?? '');
                        echo '<li><a class="transition" title="' . $name . '" href="' . $slug . '">' . $name . '</a>';

                        if ($level > 2) {
                            $items = $this->d->rawQuery(
                                "SELECT name$lang, slugvi, slugen, id FROM #_" . $table . "_item WHERE type = ? AND id_cat = ? AND find_in_set('hienthi',status) ORDER BY numb,id DESC",
                                array($type, $cat['id'])
                            );

                            if (!empty($items)) {
                                echo '<ul>';
                                foreach ($items as $item) {
                                    $name = $escape($item[$nameField] ?? '');
                                    $slug = $escape($item[$sluglang] ?? '');
                                    echo '<li><a class="transition" title="' . $name . '" href="' . $slug . '">' . $name . '</a>';

                                    if ($level > 3) {
                                        $subs = $this->d->rawQuery(
                                            "SELECT name$lang, slugvi, slugen, id FROM #_" . $table . "_sub WHERE type = ? AND id_item = ? AND find_in_set('hienthi',status) ORDER BY numb,id DESC",
                                            array($type, $item['id'])
                                        );

                                        if (!empty($subs)) {
                                            echo '<ul>';
                                            foreach ($subs as $sub) {
                                                $name = $escape($sub[$nameField] ?? '');
                                                $slug = $escape($sub[$sluglang] ?? '');
                                                echo '<li><a class="transition" title="' . $name . '" href="' . $slug . '">' . $name . '</a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                    }

                                    echo '</li>';
                                }
                                echo '</ul>';
                            }
                        }

                        echo '</li>';
                    }
                    echo '</ul>';
                }
            }

            echo '</li>';
        }
        echo '</ul>';
    }


    /* Get Product item */
    public function getProductItems($product, $sluglang, $slide = false)
    {
        $lang = $_SESSION['lang'];
        if (!empty($product)) {
            foreach ($product as $key => $value) {
                $productName = trim((string)($value['name' . $lang] ?: $value['namevi'] ?? 'Sản phẩm'));
?>
                <?= $slide ? '' : '<div class="col-product col" data-aos="fade-up" data-aos-duration="1000">' ?>
                <div class="box-product">
                    <?= $this->getFlashSaleBadge((int)$value['id']) ?>
                    <div class="pic-product">
                        <a class="scale-img" href="<?= $value[$sluglang] ?>" aria-label="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>">
                            <img class="<?= $slide ? '' : 'lazy' ?> w-100" width="770" height="770" onerror="this.src='<?= THUMBS ?>/370x370x1/assets/images/noimage.png';" <?= $slide ? '' : 'data-' ?>src="<?= THUMBS ?>/770x770x1/<?= UPLOAD_PRODUCT_L . $value['photo'] ?>" alt="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>" />
                            <!-- <img class="<?= $slide ? '' : 'lazy' ?> w-100" onerror="this.src='<?= THUMBS ?>/370x370x1/assets/images/noimage.png';" <?= $slide ? '' : 'data-' ?>src="<?= THUMBS ?>/770x770x1/<?= UPLOAD_PRODUCT_L . $value['icon'] ?>" alt="<?= $value['name' . $lang] ?>" title="<?= $value['name' . $lang] ?>" /> -->
                        </a>
                        <div class="product-tool d-none d-md-flex align-items-stretch justify-content-between transition mb-0">
                            <a class="product-detail-view text-hover-main transition" href="<?= $value[$sluglang] ?>" title="Xem chi tiêt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="10" cy="10" r="7" />
                                    <line x1="21" y1="21" x2="15" y2="15" />
                                </svg>
                                <span>Chi tiêt</span>
                            </a>
                            <a class="product-quick-view text-hover-main transition" data-slug="<?= $value[$sluglang] ?>" title="Xem nhanh">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="12" cy="12" r="2" />
                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                </svg>
                                <span>Xem nhanh</span>
                            </a>
                        </div>
                    </div>
                    <?= $this->getProductRatingMarkup($value) ?>
                    <h3 class="name-product text-center mb-2"><a class="text-split" href="<?= $value[$sluglang] ?>" aria-label="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?></a></h3>
                    <div class="btn-link product-order-wrap">
                        <button type="button" class="pro-btn-add-to-cart product-order-link addcart"
                            data-id="<?= (int) $value['id'] ?>" data-action="addnow"
                            aria-label="<?= dathang ?> - <?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>">
                            <span class="t-price">
                                <strong class="t-new"><?= $value['sale_price'] > 0 ? $this->formatMoney($value['sale_price']) : (($value['regular_price'] > 0) ? $this->formatMoney($value['regular_price']) : lienhe) ?></strong>
                                <?php if ($value['sale_price'] > 0) { ?><del class="t-old"><?= $this->formatMoney($value['regular_price']) ?></del><?php } ?>
                            </span>
                            <span class="add-group">
                                <i class="far fa-shopping-bag icon"></i>
                                <span class="text"><?= dathang ?></span>
                            </span>
                        </button>
                    </div>
                </div>
                <?= $slide ? '' : '</div>' ?>
            <?php }
        }
    }

    /** Render the stored product rating, preferring visible reviews when enabled. */
    public function getProductRatingMarkup($product = array())
    {
        global $config;

        if (empty($product) || !isset($product['id'])) return '';

        $type = !empty($product['type']) ? (string)$product['type'] : 'san-pham';
        $commentEnabled = !empty($config['product'][$type]['comment']);
        $storedCount = !empty($product['rating_count']) ? (int)$product['rating_count'] : 0;
        $count = ($commentEnabled && $storedCount > 0) ? $storedCount : 0;
        $manual = !empty($product['rating']) ? (float)$product['rating'] : 5;
        $average = ($count > 0 && !empty($product['rating_average']))
            ? (float)$product['rating_average']
            : $manual;
        $average = max(1, min(5, round($average, 1)));
        $width = number_format($average / 5 * 100, 1, '.', '');
        $label = 'Đánh giá ' . number_format($average, 1, '.', '') . ' trên 5';

        return '<div class="product-card-rating" aria-label="' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '">' .
            '<span class="product-card-rating__stars" aria-hidden="true"><span class="product-card-rating__stars-bg">★★★★★</span><span class="product-card-rating__stars-fill" style="width:' . $width . '%">★★★★★</span></span>' .
            '<span class="product-card-rating__value">' . number_format($average, 1, '.', '') . '</span>' .
            ($count > 0 ? '<span class="product-card-rating__count">(' . $count . ')</span>' : '') .
            '</div>';
    }

    /** Render the active Flash Sale countdown for a product. */
    public function getFlashSaleBadge($productId)
    {
        static $sales = null;
        if ($sales === null) {
            $sales = array();
            if ($this->d->tableExists('product_flash_sale')) {
                $rows = $this->d->rawQuery("select product_id,end_at from #_product_flash_sale where start_at <= NOW() and end_at > NOW()");
                foreach ($rows as $row) $sales[(int)$row['product_id']] = $row['end_at'];
            }
        }
        $productId = (int)$productId;
        if (empty($sales[$productId])) return '';
        $endMs = strtotime($sales[$productId]) * 1000;
        return '<div class="product-flash-sale" data-flash-sale-end="' . $endMs . '"><span class="product-flash-sale__label">Flash Sale</span><span class="product-flash-sale__bolt">⚡</span><span class="product-flash-sale__time">00 : 00 : 00 : 00</span></div>';
    }
    /** Render the shared product card used by product lists and sliders. */
    public function renderProductCard($product, $options = array())
    {
        global $lang, $sluglang;
        if (empty($product) || empty($product['id'])) return '';

        $options = array_merge(array(
            'wrapper_class' => 'col reveal-it',
            'heading_tag' => 'h3',
            'image_size' => '770x770x1',
            'flash_end' => !empty($product['end_at']) ? strtotime($product['end_at']) * 1000 : 0,
            'show_flash_badge' => true
        ), $options);
        $escape = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
        $headingTag = in_array($options['heading_tag'], array('h2', 'h3', 'h4'), true) ? $options['heading_tag'] : 'h3';
        $productName = trim((string)($product['name' . $lang] ?? ($product['namevi'] ?? sanpham)));
        $productDesc = trim(strip_tags(htmlspecialchars_decode($product['desc' . $lang] ?? '')));
        $productUrl = $product[$sluglang] ?? '#';
        $regularPrice = (float)($product['regular_price'] ?? 0);
        $salePrice = (float)($product['sale_price'] ?? 0);
        $isSale = $salePrice > 0;
        $flashEnd = (int)$options['flash_end'];
        $wrapperClass = trim((string)$options['wrapper_class']);
        $imageSize = preg_match('/^\d+x\d+x[123]$/', (string)$options['image_size']) ? $options['image_size'] : '770x770x1';

        ob_start(); ?>
        <div class="<?= $escape($wrapperClass) ?>"<?= $flashEnd > 0 ? ' data-flash-sale-item' : '' ?>>
            <article class="prd-it<?= ($isSale || $flashEnd > 0) ? ' is-km is-km-sale' : '' ?>">
                <div class="b-inner">
                    <?php if ($flashEnd > 0) { ?>
                        <div class="product-flash-sale" data-flash-sale-end="<?= $flashEnd ?>"><span class="product-flash-sale__label">Flash Sale</span><span class="product-flash-sale__bolt"></span><span class="product-flash-sale__time">00 : 00 : 00 : 00</span></div>
                    <?php } elseif (!empty($options['show_flash_badge'])) { ?>
                        <?= $this->getFlashSaleBadge((int)$product['id']) ?>
                    <?php } ?>
                    <div class="b-image">
                        <a class="inner" href="<?= $escape($productUrl) ?>" title="<?= $escape($productName) ?>">
                            <img class="lazy" width="770" height="770" onerror="this.src='<?= THUMBS ?>/370x370x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/<?= $imageSize ?>/<?= UPLOAD_PRODUCT_L . $escape($product['photo'] ?? '') ?>" alt="<?= $escape($productName) ?>">
                        </a>
                    </div>
                    <?= $this->getProductRatingMarkup($product) ?>
                    <div class="b-head">
                        <div class="b-btn"><a class="btn" href="<?= $escape($productUrl) ?>" aria-label="Xem <?= $escape($productName) ?>"><i class="far fa-arrow-right"></i></a></div>
                        <div class="b-ctn">
                            <<?= $headingTag ?> class="t-link"><a href="<?= $escape($productUrl) ?>"><?= $escape($productName) ?></a></<?= $headingTag ?>>
                            <?php if ($productDesc !== '') { ?><p class="t-des"><?= $escape($productDesc) ?></p><?php } ?>
                            <div class="btn-link product-order-wrap">
                                <button type="button" class="pro-btn-add-to-cart product-order-link addcart" data-id="<?= (int)$product['id'] ?>" data-action="addnow" aria-label="<?= dathang ?> - <?= $escape($productName) ?>">
                                    <span class="t-price"><?php if ($isSale) { ?><del class="t-old"><?= $this->formatMoney($regularPrice) ?></del><?php } ?><strong class="t-new"><?= $isSale ? $this->formatMoney($salePrice) : ($regularPrice > 0 ? $this->formatMoney($regularPrice) : lienhe) ?></strong></span>
                                    <span class="add-group"><img class="order-cart-icon" src="assets/images/hd/cart.png" alt="<?= dathang ?>"><span class="text"><?= dathang ?></span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
        <?php return ob_get_clean();
    }

    /* Get News item */
    public function getNewsItems($news, $sluglang, $slide = false)
    {
        $lang = $_SESSION['lang'];
        if ($news) {
            foreach ($news as $key => $value) { ?>
                <?= $slide ? '' : '<div class="col-news col" data-aos="fade-up" data-aos-duration="1000">' ?>
                <div class="box-news">
                    <div class="img-news">
                        <a href="<?= $value[$sluglang] ?>" class="scale-img" title="<?= $value['name' . $lang] ?>">
                            <img class="<?= $slide ? '' : 'lazy' ?> w-100" onerror="this.src='<?= THUMBS ?>/275x226x1/assets/images/noimage.png';" <?= $slide ? '' : 'data-' ?>src="<?= THUMBS ?>/275x226x1/<?= UPLOAD_NEWS_L . $value['photo'] ?>" alt="<?= $value['name' . $lang] ?>" title="<?= $value['name' . $lang] ?>" />
                        </a>
                    </div>
                    <h3 class="name-news"><a href="<?= $value[$sluglang] ?>" title="<?= $value['name' . $lang] ?>"><?= $value['name' . $lang] ?></a></h3>
                    <div class="date-news"><?= date("d - m - Y", $value['date_created']) ?></div>
                    <div class="desc-news text-split"><?= $value['desc' . $lang] ?></div>
                </div>
                <?= $slide ? '' : '</div>' ?>
<?php }
        }
    }

    public function getNameFacebook($url = '')
    {
        if (strpos($url, 'facebook.com') !== false) {
            $url = preg_replace('#^(https?://)?(www\.)?#', '', $url);
            $url = rtrim($url, '/');

            if (strpos($url, 'profile.php') !== false) {
                parse_str(parse_url($url, PHP_URL_QUERY), $params);
                return isset($params['id']) ? $params['id'] : null;
            } else {
                $url_parts = explode('?', $url);
                $url_parts = explode('/', $url_parts[0]);
                $page_name = end($url_parts);

                return trim($page_name, '/');
            }
        }
        return null;
    }

    public function checkLinkZalo($url = '', $phone = '', $deviceType = '',  $isIOS = 0)
    {
        $linkZalo = $url;
        if ($deviceType == 'computer') {
            $linkZalo = "zalo://conversation?phone=" . $this->parsePhone($phone);
        } else {
            if (strpos($url, '/qr/p/') !== false) {
                $prefixes = ['http://zaloapp.com/', 'https://zaloapp.com/'];
                if (!empty($isIOS)) {
                    $linkZalo = str_replace($prefixes, 'zalo://', $url);
                }
            }
        }
        return $linkZalo;
    }

    public function convert_utf8_to_iconv($str = '')
    {
        return iconv('UTF-8', 'ISO-8859-1', $str);
    }

    public function get_youtube_shorts($str)
    {
        $char = 'shorts/';
        $pos = strpos($str, $char);
        if ($pos != false) {
            $str = "https://www.youtube.com/watch?v=" . end(explode($char, $str));
        }
        return $str;
    }

    public function isGoogleSpeed()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        return stripos($userAgent, 'Chrome-Lighthouse') !== false
            || stripos($userAgent, 'HeadlessChrome') !== false
            || stripos($userAgent, 'Google-InspectionTool') !== false
            || stripos($userAgent, 'PageSpeed Insights') !== false;
    }
    public function isRecaptchaSkip()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT']) || (stripos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') === false && stripos($_SERVER['HTTP_USER_AGENT'], 'Google-InspectionTool') === false)) {
            return false;
        }
        return true;
    }
    public function checkRedirect()
    {
        global $http, $configBase;
        $getLink = $this->cache->get("select oldlink, newlink, type from #_redirect where find_in_set('hienthi',status) order by numb,id desc", null, 'result', 7200);
        if (!empty($getLink)) {
            $currentUrl = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $currentUri = $_SERVER['REQUEST_URI'];
            foreach ($getLink as $v) {
                if ($currentUrl == $v['oldlink'] || $currentUrl == $configBase . $v['oldlink'] || $currentUri == $v['oldlink'] || $currentUri == '/' . $v['oldlink'] || $currentUri == $configBase . $v['oldlink']) {
                    $this->redirect($v['newlink'], ($v['type'] == '301') ? 301 : 302);
                }
            }
        }
    }
    /* Markdown */
    public function markdown($path = '', $params = array())
    {
        $content = '';

        if (!empty($path)) {
            ob_start();
            include dirname(__DIR__) . "/sample/" . $path . ".php";
            $content = ob_get_contents();
            ob_clean();
        }

        return $content;
    }
    public function webpinfo($file)
    {
        if (!is_file($file)) {
            return false;
        } else {
            $file = realpath($file);
        }
        $fp = fopen($file, 'rb');
        if (!$fp) return false;
        $data = fread($fp, 90);
        fclose($fp);
        unset($fp);
        $header_format = 'A4Riff/' .
            'I1Filesize/' .
            'A4Webp/' .
            'A4Vp/' .
            'A74Chunk';
        $header = unpack($header_format, $data);
        unset($data, $header_format);
        if (!isset($header['Riff']) || strtoupper($header['Riff']) !== 'RIFF') return false;
        if (!isset($header['Webp']) || strtoupper($header['Webp']) !== 'WEBP') return false;
        if (!isset($header['Vp']) || strpos(strtoupper($header['Vp']), 'VP8') === false) return false;
        if (strpos(strtoupper($header['Chunk']), 'ANIM') !== false || strpos(strtoupper($header['Chunk']), 'ANMF') !== false) {
            $header['Animation'] = true;
        } else {
            $header['Animation'] = false;
        }
        if (strpos(strtoupper($header['Chunk']), 'ALPH') !== false) {
            $header['Alpha'] = true;
        } else {
            if (strpos(strtoupper($header['Vp']), 'VP8L') !== false) {
                $header['Alpha'] = true;
            } else {
                $header['Alpha'] = false;
            }
        }
        unset($header['Chunk']);
        return $header;
    }

    public function writeJson($data = array())
    {
        global $configBase;
        if ($data['type'] == 'city') {
            $citys = $this->d->rawQuery("select id, name from #_city where find_in_set('hienthi',status) order by numb,id desc");

            $data = array();
            if (!empty($citys)) {
                foreach ($citys as $k_city => $v_city) {
                    $data['citysCentral'][] = $v_city;
                    unset($citys[$k_city]);
                }
            }
            /* Put data */
            $this->putJson('city-group.json', $data);
        } else if ($data['type'] == 'district') {
            /* Update */
            if (!empty($data['idCity'])) {
                $district = $this->d->rawQuery("select id, id_city, name from #_district where id_city = ? and find_in_set('hienthi',status) order by numb,id desc", array($data['idCity']));
                /* Put data */
                $this->putJson('district-' . $data['idCity'] . '.json', $district);
            } else /* Create */ {
                $citys = $this->d->rawQuery("select id from #_city where find_in_set('hienthi',status)");
                if (!empty($citys)) {
                    $result = array();
                    foreach ($citys as $v_city) {
                        $district = $this->d->rawQuery("select id, id_city, name from #_district where id_city = ? and find_in_set('hienthi',status) order by numb,id desc", array($v_city['id']));
                        /* Put data */
                        if (!empty($district)) {
                            $this->putJson('district-' . $v_city['id'] . '.json', $district);
                        }
                    }
                }
            }
        } else if ($data['type'] == 'wards') {
            /* Update */
            if (!empty($data['idCity']) && !empty($data['idDistrict'])) {
                $ward = $this->d->rawQuery("select id, id_city, id_district, name from #_ward where id_city = ? and id_district = ? and find_in_set('hienthi',status) order by numb,id desc", array($data['idCity'], $data['idDistrict']));
                /* Put data */
                $this->putJson('wards-' . $data['idCity'] . '-' . $data['idDistrict'] . '.json', $ward);
            } else /* Create */ {
                $districts = $this->d->rawQuery("select distinct id_city, id from #_district where find_in_set('hienthi',status)");

                if (!empty($districts)) {
                    $result = array();

                    foreach ($districts as $v_district) {
                        $ward = $this->d->rawQuery("select id, id_city, id_district, name from #_ward where id_city = ? and id_district = ? and find_in_set('hienthi',status) order by numb,id desc", array($v_district['id_city'], $v_district['id']));

                        /* Put data */
                        if (!empty($ward)) {
                            $this->putJson('wards-' . $v_district['id_city'] . '-' . $v_district['id'] . '.json', $ward);
                        }
                    }
                }
            }
        } else if ($data['type'] == 'places') {
            $citys = $this->d->rawQuery("select id, name from #_city where find_in_set('hienthi',status) order by numb,id desc");

            $data = array();
            if (!empty($citys)) {
                foreach ($citys as $k_city => $v_city) {
                    $data['citysCentral'][] = $v_city;
                    $district = $this->d->rawQuery("select id, id_city, name from #_district where id_city = ? and find_in_set('hienthi',status) order by numb,id desc", array($v_city['id']));
                    /* Put data */
                    if (!empty($district)) {
                        $this->putJson('district-' . $v_city['id'] . '.json', $district);
                    }

                    foreach ($district as $v_district) {
                        $ward = $this->d->rawQuery("select id, id_city, id_district, name from #_ward where id_city = ? and id_district = ? and find_in_set('hienthi',status) order by numb,id desc", array($v_district['id_city'], $v_district['id']));

                        /* Put data */
                        if (!empty($ward)) {
                            $this->putJson('wards-' . $v_district['id_city'] . '-' . $v_district['id'] . '.json', $ward);
                        }
                    }
                }
            }
            /* Put data */
            $this->putJson('city-group.json', $data);
        }
        return true;
    }

    /* Put json */
    public function putJson($filename = '', $data = array())
    {

        if (!empty($data)) {
            $data = json_encode($data);
            $file = fopen(JSONS . $filename, "w+");

            if (!empty($file)) {
                fwrite($file, $data);
                fclose($file);
            }
        } else if (file_exists(JSONS . $filename)) {
            $this->deleteFile(JSONS . $filename);
        }
        return true;
    }

    /* Check URL */
    public function checkURL($index = false)
    {
        global $configBase;

        $url = '';
        $urls = array('index', 'index.html', 'trang-chu', 'trang-chu.html');

        if (array_key_exists('REDIRECT_URL', $_SERVER)) {
            $url = explode("/", $_SERVER['REDIRECT_URL']);
        } else {
            $url = explode("/", $_SERVER['REQUEST_URI']);
        }

        if (is_array($url)) {
            $url = $url[count($url) - 1];
            if (strpos($url, "?")) {
                $url = explode("?", $url);
                $url = $url[0];
            }
        }

        if ($index) array_push($urls, "index.php");
        else if (array_search('index.php', $urls)) $urls = array_diff($urls, ["index.php"]);
        if (in_array($url, $urls)) $this->redirect($configBase, 301);
    }

    /* Check Is Ajax Request */
    public function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));
    }

    /* Check HTTP */
    public function checkHTTP($http, $arrayDomain, &$configBase, $configUrl)
    {
        if (count($arrayDomain) == 0 && $http == 'https://') {
            $configBase = 'http://' . $configUrl;
        }
    }

    /* Create sitemap */
    public function createSitemap($com = '', $type = '', $field = '', $table = '', $time = '', $changefreq = '', $priority = '', $lang = 'vi', $orderby = '', $menu = true)
    {
        global $configBase;

        $urlSm = '';
        $sitemap = null;

        if (!empty($type) && !in_array($table, ['photo', 'static'])) {
            $where = "type = ? and find_in_set('hienthi',status)";
            $where .= ($table != 'static') ? 'order by ' . $orderby . ' desc' : '';
            $sitemap = $this->d->rawQuery("select slug$lang, date_created from #_$table where $where", array($type));
        }

        if ($menu == true && $field == 'id') {
            $urlSm = $configBase . $com;
            echo '<url>';
            echo '<loc>' . $urlSm . '</loc>';
            echo '<lastmod>' . date('c', time()) . '</lastmod>';
            echo '<changefreq>' . $changefreq . '</changefreq>';
            echo '<priority>' . $priority . '</priority>';
            echo '</url>';
        }

        if (!empty($sitemap)) {
            foreach ($sitemap as $value) {
                if (!empty($value['slug' . $lang])) {
                    $urlSm = $configBase . $value['slug' . $lang];
                    echo '<url>';
                    echo '<loc>' . $urlSm . '</loc>';
                    echo '<lastmod>' . date('c', $value['date_created']) . '</lastmod>';
                    echo '<changefreq>' . $changefreq . '</changefreq>';
                    echo '<priority>' . $priority . '</priority>';
                    echo '</url>';
                }
            }
        }
    }

    /* Kiểm tra dữ liệu nhập vào */
    public function cleanInput($input = '', $type = '')
    {
        $output = '';

        if ($input != '') {
            /*
					// Loại bỏ HTML tags
					'@<[\/\!]*?[^<>]*?>@si',
*/

            $search = array(
                'script' => '@<script[^>]*?>.*?</script>@si',
                'style' => '@<style[^>]*?>.*?</style>@siU',
                'blank' => '@
        <![\s\S]*?--[ \t\n\r]*>@',
                'iframe' => '/<iframe(.*?)<\/iframe>/is',
                'title' => '/<title(.*?)<\/title>/is',
                'pre' => '/<pre(.*?)<\/pre>/is',
                'frame' => '/<frame(.*?)<\/frame>/is',
                'frameset' => '/<frameset(.*?)<\/frameset>/is',
                'object' => '/<object(.*?)<\/object>/is',
                'embed' => '/<embed(.*?)<\/embed>/is',
                'applet' => '/<applet(.*?)<\/applet>/is',
                'meta' => '/<meta(.*?)<\/meta>/is',
                'doctype' => '/<!doctype(.*?)>/is',
                'link' => '/<link(.*?)>/is',
                'body' => '/<body(.*?)<\/body>/is',
                'html' => '/<html(.*?)<\/html>/is',
                'head' => '/<head(.*?)<\/head>/is',
                'onclick' => '/onclick="(.*?)"/is',
                'ondbclick' => '/ondbclick="(.*?)"/is',
                'onchange' => '/onchange="(.*?)"/is',
                'onmouseover' => '/onmouseover="(.*?)"/is',
                'onmouseout' => '/onmouseout="(.*?)"/is',
                'onmouseenter' => '/onmouseenter="(.*?)"/is',
                'onmouseleave' => '/onmouseleave="(.*?)"/is',
                'onmousemove' => '/onmousemove="(.*?)"/is',
                'onkeydown' => '/onkeydown="(.*?)"/is',
                'onload' => '/onload="(.*?)"/is',
                'onunload' => '/onunload="(.*?)"/is',
                'onkeyup' => '/onkeyup="(.*?)"/is',
                'onkeypress' => '/onkeypress="(.*?)"/is',
                'onblur' => '/onblur="(.*?)"/is',
                'oncopy' => '/oncopy="(.*?)"/is',
                'oncut' => '/oncut="(.*?)"/is',
                'onpaste' => '/onpaste="(.*?)"/is',
                'php-tag' => '/<(\?|\%)\=?(php)?/',
                'php-short-tag' => '/(\%|\?)>/'
            );

            if (!empty($type)) {
                unset($search[$type]);
            }

            $output = preg_replace($search, '', $input);
        }

        return $output;
    }

    /* Kiểm tra dữ liệu nhập vào */
    public function sanitize($input = '', $type = '')
    {
        if (is_array($input)) {
            foreach ($input as $var => $val) {
                $output[$var] = $this->sanitize($val, $type);
            }
        } else {
            $output  = $this->cleanInput($input, $type);
        }

        return $output;
    }

    /* Decode html characters */
    public function decodeHtmlChars($htmlChars)
    {
        return htmlspecialchars_decode($htmlChars ?: '');
    }

    /* Decode html characters show */
    public function getHtmlChars($htmlChars)
    {
        global $config;
        $pageURL = 'http';
        $pageURLs = "";
        if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURLs .= $pageURL . "s";
            $pageURLs .= "://";
            $pageURL .= "://";
            $pageURL .= $config['arrayDomainSSL'][0];
            $pageURLs .= $config['arrayDomainSSL'][0];
            return htmlspecialchars_decode(str_replace($pageURL, $pageURLs, $htmlChars ?: ''));
        } else {
            return htmlspecialchars_decode($htmlChars ?: '');
        }
    }

    /* Kiểm tra đăng nhập */
    public function checkLoginAdmin()
    {
        global $loginAdmin;

        $token = (!empty($_SESSION[$loginAdmin]['token'])) ? $_SESSION[$loginAdmin]['token'] : '';
        $row = $this->d->rawQuery("select secret_key from #_user where secret_key = ? and find_in_set('hienthi',status)", array($token));

        if (count($row) == 1 && $row[0]['secret_key'] != '') {
            return true;
        } else {
            if (!empty($_SESSION[TOKEN])) unset($_SESSION[TOKEN]);
            unset($_SESSION[$loginAdmin]);
            return false;
        }
    }

    /* Mã hóa mật khẩu admin */
    public function encryptPassword($secret = '', $str = '', $salt = '')
    {
        return md5($secret . $str . $salt);
    }

    /* Kiểm tra phân quyền menu */
    public function checkPermission($com = '', $act = '', $type = '', $array = null, $case = '')
    {
        global $loginAdmin;

        $str = $com;

        if ($act) $str .= '_' . $act;

        if ($case == 'phrase-1') {
            if ($type != '') $str .= '_' . $type;
            if (!in_array($str, $_SESSION[$loginAdmin]['permissions'])) return true;
            else return false;
        } else if ($case == 'phrase-2') {
            $count = 0;

            if ($array) {
                foreach ($array as $key => $value) {
                    if (!empty($value['dropdown'])) {
                        unset($array[$key]);
                    }
                }

                foreach ($array as $key => $value) {
                    if (!in_array($str . "_" . $key, $_SESSION[$loginAdmin]['permissions'])) $count++;
                }

                if ($count == count($array)) return true;
            } else return false;
        }

        return false;
    }

    /* Kiểm tra phân quyền */
    public function checkRole()
    {
        global $config, $loginAdmin;

        if ((!empty($_SESSION[$loginAdmin]['role']) && $_SESSION[$loginAdmin]['role'] == 3) || !empty($config['website']['debug-developer'])) return false;
        else return true;
    }

    /* Lấy tình trạng nhận tin */
    public function getStatusNewsletter($confirm_status = 0, $type = '')
    {
        global $config;

        $loai = '';

        if (!empty($config['newsletter'][$type]['confirm_status'])) {
            foreach ($config['newsletter'][$type]['confirm_status'] as $key => $value) {
                if ($key == $confirm_status) {
                    $loai = $value;
                    break;
                }
            }
        }

        if ($loai == '') $loai = "Đang chờ duyệt...";

        return $loai;
    }

    /* Database maintenance */
    public function databaseMaintenance($action = '', $tables = array())
    {
        $result = array();
        $row = array();

        if (!empty($action) && !empty($tables)) {
            foreach ($tables as $k => $v) {
                foreach ($v as $table) {
                    $result = $this->d->rawQuery("$action TABLE $table");

                    if (!empty($result)) {
                        $row[$k]['table'] = $result[0]['Table'];
                        $row[$k]['action'] = $result[0]['Op'];
                        $row[$k]['type'] = $result[0]['Msg_type'];
                        $row[$k]['text'] = $result[0]['Msg_text'];
                    }
                }
            }
        }

        return $row;
    }

    /* Format money */
    public function formatMoney($price = 0, $unit = 'đ', $html = false)
    {
        $str = '';

        if ($price) {
            $str .= number_format($price, 0, ',', '.');
            if ($unit != '') {
                if ($html) {
                    $str .= '<span>' . $unit . '</span>';
                } else {
                    $str .= $unit;
                }
            }
        }

        return $str;
    }

    /* Is phone */
    public function isPhone($number)
    {
        $number = trim($number);
        if (preg_match_all('/^(0|84)(2(0[3-9]|1[0-6|8|9]|2[0-2|5-9]|3[2-9]|4[0-9]|5[1|2|4-9]|6[0-3|9]|7[0-7]|8[0-9]|9[0-4|6|7|9])|3[2-9]|5[5|6|8|9]|7[0|6-9]|8[0-6|8|9]|9[0-4|6-9])([0-9]{7})$/m', $number, $matches, PREG_SET_ORDER, 0)) {
            return true;
        } else {
            return false;
        }
    }

    /* Format phone */
    public function formatPhone($number, $dash = ' ')
    {
        if (preg_match('/^(\d{4})(\d{3})(\d{3})$/', $number, $matches) || preg_match('/^(\d{3})(\d{4})(\d{4})$/', $number, $matches)) {
            return $matches[1] . $dash . $matches[2] . $dash . $matches[3];
        } else {
            return $number;
        }
    }

    /* Parse phone */
    public function parsePhone($number)
    {
        return (!empty($number)) ? preg_replace('/[^0-9]/', '', $number) : '';
    }

    /* Check letters and nums */
    public function isAlphaNum($str)
    {
        if (preg_match('/^[a-z0-9]+$/', $str)) {
            return true;
        } else {
            return false;
        }
    }

    /* Is email */
    public function isEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    /* Is match */
    public function isMatch($value1, $value2)
    {
        if ($value1 == $value2) {
            return true;
        } else {
            return false;
        }
    }

    /* Is decimal */
    public function isDecimal($number)
    {
        if (preg_match('/^\d{1,10}(\.\d{1,4})?$/', $number)) {
            return true;
        } else {
            return false;
        }
    }

    /* Is coordinates */
    public function isCoords($str)
    {
        if (preg_match('/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/', $str)) {
            return true;
        } else {
            return false;
        }
    }

    /* Is url */
    public function isUrl($str)
    {
        if (preg_match('/^(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/', $str)) {
            return true;
        } else {
            return false;
        }
    }

    /* Is url youtube */
    public function isYoutube($str)
    {
        if (preg_match('/https?:\/\/(?:[a-zA_Z]{2,3}.)?(?:youtube\.com\/watch\?)((?:[\w\d\-\_\=]+&amp;(?:amp;)?)*v(?:&lt;[A-Z]+&gt;)?=([0-9a-zA-Z\-\_]+))/i', $str)) {
            return true;
        } else {
            return false;
        }
    }

    /* Is fanpage */
    public function isFanpage($str)
    {
        if (preg_match('/^(https?:\/\/)?(?:www\.)?facebook\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*([\w\-\.]*)/', $str)) {
            return true;
        } else {
            return false;
        }
    }

    /* Is date */
    public function isDate($str)
    {
        if (preg_match('/^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$/', $str)) {
            return true;
        } else {
            return false;
        }
    }

    /* Is date by format */
    public function isDateByFormat($str, $format = 'd/m/Y')
    {
        $dt = DateTime::createFromFormat($format, $str);
        return $dt && $dt->format($format) == $str;
    }

    /* Is number */
    public function isNumber($numbs)
    {
        if (preg_match('/^[0-9]+$/', $numbs)) {
            return true;
        } else {
            return false;
        }
    }

    /* Check account */
    public function checkAccount($data = '', $type = '', $tbl = '', $id = 0)
    {
        $result = false;
        $row = array();

        if (!empty($data) && !empty($type) && !empty($tbl)) {
            $where = (!empty($id)) ? ' and id != ' . $id : '';
            $row = $this->d->rawQueryOne("select id from #_$tbl where $type = ? $where limit 0,1", array($data));

            if (!empty($row)) {
                $result = true;
            }
        }

        return $result;
    }

    /* Check title */
    public function checkTitle($data = array())
    {
        global $config;

        $result = array();

        foreach ($config['website']['lang'] as $k => $v) {
            if (isset($data['name' . $k])) {
                $title = trim($data['name' . $k]);

                if (empty($title)) {
                    $result[] = 'Tiêu đề (' . $v . ') không được trống';
                }
            }
        }

        return $result;
    }

    /* Check slug */
    public function checkSlug($data = array())
    {
        $result = 'valid';

        if (isset($data['slug'])) {
            $slug = trim($data['slug']);

            if (!empty($slug)) {
                $table = array(
                    "#_product_list",
                    "#_product_cat",
                    "#_product_item",
                    "#_product_sub",
                    "#_product_brand",
                    "#_product",
                    "#_news_list",
                    "#_news_cat",
                    "#_news_item",
                    "#_news_sub",
                    "#_news",
                    "#_tags"
                );

                $where = (!empty($data['id']) && empty($data['copy'])) ? "id != " . $data['id'] . " and " : "";

                foreach ($table as $v) {
                    $check = $this->d->rawQueryOne("select id from $v where $where (slugvi = ? or slugen = ?) limit 0,1", array($data['slug'], $data['slug']));

                    if (!empty($check['id'])) {
                        $result = 'exist';
                        break;
                    }
                }
            } else {
                $result = 'empty';
            }
        }

        return $result;
    }

    /* Check recaptcha */
    public function checkRecaptcha($response = '')
    {
        global $config;

        $result = null;
        $active = $config['googleAPI']['recaptcha']['active'];

        if ($active == true && $response != '') {
            $recaptcha = file_get_contents($config['googleAPI']['recaptcha']['urlapi'] . '?secret=' . $config['googleAPI']['recaptcha']['secretkey'] . '&response=' . $response);
            $recaptcha = json_decode($recaptcha);
            $result['score'] = $recaptcha->score;
            $result['action'] = $recaptcha->action;
        } else if (!$active) {
            $result['test'] = true;
        }

        return $result;
    }

    /* Login */
    public function checkLoginMember()
    {
        global $configBase, $loginMember;

        if (!empty($_SESSION[$loginMember]) || !empty($_COOKIE['login_member_id'])) {
            $flag = true;
            $iduser = (!empty($_COOKIE['login_member_id'])) ? $_COOKIE['login_member_id'] : $_SESSION[$loginMember]['id'];

            if ($iduser) {
                $row = $this->d->rawQueryOne("select login_session, id, username, phone, address, email, fullname from #_member where id = ? and find_in_set('hienthi',status)", array($iduser));

                if (!empty($row['id'])) {
                    $login_session = (!empty($_COOKIE['login_member_session'])) ? $_COOKIE['login_member_session'] : $_SESSION[$loginMember]['login_session'];

                    if ($login_session == $row['login_session']) {
                        $_SESSION[$loginMember]['active'] = true;
                        $_SESSION[$loginMember]['id'] = $row['id'];
                        $_SESSION[$loginMember]['username'] = $row['username'];
                        $_SESSION[$loginMember]['phone'] = $row['phone'];
                        $_SESSION[$loginMember]['address'] = $row['address'];
                        $_SESSION[$loginMember]['email'] = $row['email'];
                        $_SESSION[$loginMember]['fullname'] = $row['fullname'];
                    } else $flag = false;
                } else $flag = false;

                if (!$flag) {
                    unset($_SESSION[$loginMember]);
                    setcookie('login_member_id', "", -1, '/');
                    setcookie('login_member_session', "", -1, '/');

                    $this->transfer("Tài khoản của bạn đã hết hạn đăng nhập hoặc đã đăng nhập trên thiết bị khác", $configBase, false);
                }
            }
        }
    }

    /* Lấy youtube */
    public function getYoutube($url = '')
    {
        if ($url != '') {
            $parts = parse_url($url);
            if (isset($parts['query'])) {
                parse_str($parts['query'], $qs);
                if (isset($qs['v'])) return $qs['v'];
                else if ($qs['vi']) return $qs['vi'];
            }

            if (isset($parts['path'])) {
                $path = explode('/', trim($parts['path'], '/'));
                return $path[count($path) - 1];
            }
        }

        return false;
    }

    /* Get image */
    public function getImage($data = array())
    {
        global $config;

        /* Defaults */
        $defaults = [
            'class' => 'lazy',
            'id' => '',
            'isLazy' => true,
            'thumbs' => THUMBS,
            'isWatermark' => false,
            'watermark' => (defined('WATERMARK')) ? WATERMARK : '',
            'prefix' => '',
            'size-error' => '',
            'size-src' => '',
            'sizes' => '',
            'url' => '',
            'upload' => '',
            'image' => '',
            'upload-error' => 'assets/images/',
            'image-error' => 'noimage.png',
            'alt' => ''
        ];

        /* Data */
        $info = array_merge($defaults, $data);

        /* Upload - Image */
        if (empty($info['upload']) || empty($info['image'])) {
            $info['upload'] = $info['upload-error'];
            $info['image'] = $info['image-error'];
        }

        /* Size */
        if (!empty($info['sizes'])) {
            $info['size-error'] = $info['size-src'] = $info['sizes'];
        }

        /* Path origin */
        $info['pathOrigin'] = $info['upload'] . $info['image'];

        /* Path src */
        if (!empty($info['url'])) {
            $info['pathSrc'] = $info['url'];
        } else {
            if (!empty($info['size-src'])) {
                $info['pathSize'] = $info['size-src'] . "/" . $info['upload'] . $info['image'];
                $info['pathSrc'] = (!empty($info['isWatermark']) && !empty($info['prefix'])) ? ASSET . $info['watermark'] . "/" . $info['prefix'] . "/" . $info['pathSize'] : ASSET . $info['thumbs'] . "/" . $info['pathSize'];
            } else {
                $info['pathSrc'] = ASSET . $info['pathOrigin'];
            }
        }

        /* Path error */
        $info['pathError'] = ASSET . $info['thumbs'] . "/" . $info['size-error'] . "/" . $info['upload-error'] . $info['image-error'];

        /* Class */
        $info['class'] = (empty($info['isLazy'])) ? str_replace('lazy', '', $info['class']) : $info['class'];
        $info['class'] = (!empty($info['class'])) ? "class='" . $info['class'] . "'" : "";

        /* Id */
        $info['id'] = (!empty($info['id'])) ? "id='" . $info['id'] . "'" : "";

        /* Check to convert Webp */
        $info['hasURL'] = false;

        if (filter_var(str_replace(ASSET, "", $info['pathSrc']), FILTER_VALIDATE_URL)) {
            $info['hasURL'] = true;
        }

        // if ($config['website']['image']['hasWebp']) {
        //     if (!$info['sizes']) {
        //         if (!$info['hasURL']) {
        //             $this->converWebp($info['pathSrc']);
        //         }
        //     }

        //     if (!$info['hasURL']) {
        //         $info['pathSrc'] .= '.webp';
        //     }
        // }

        /* Src */
        $info['src'] = (!empty($info['isLazy']) && strpos($info['class'], 'lazy') !== false) ? "data-src='" . $info['pathSrc'] . "'" : "src='" . $info['pathSrc'] . "'";

        /* Image */
        /* onerror=\"this.src='" . $info['pathError'] . "';\" */
        $result = "<img " . $info['class'] . " " . $info['id'] . " onerror=\"this.src='" . $info['pathError'] . "';\" " . $info['src'] . " alt='" . $info['alt'] . "'/>";

        return $result;
    }

    /* Get list gallery */
    public function listsGallery($file = '')
    {
        $result = array();

        if (!empty($file) && !empty($_POST['fileuploader-list-' . $file])) {
            $fileLists = '';
            $fileLists = str_replace('"', '', $_POST['fileuploader-list-' . $file]);
            $fileLists = str_replace('[', '', $fileLists);
            $fileLists = str_replace(']', '', $fileLists);
            $fileLists = str_replace('{', '', $fileLists);
            $fileLists = str_replace('}', '', $fileLists);
            $fileLists = str_replace('0:/', '', $fileLists);
            $fileLists = str_replace('file:', '', $fileLists);
            $result = explode(',', $fileLists);
        }

        return $result;
    }

    /* Template gallery */
    public function galleryFiler($numb = 1, $id = 0, $photo = '', $name = '', $folder = '', $col = '')
    {
        /* Params */
        $params = array();
        $params['numb'] = $numb;
        $params['id'] = $id;
        $params['photo'] = $photo;
        $params['name'] = $name;
        $params['folder'] = ($folder == 'static') ? 'news' : $folder;
        $params['col'] = $col;

        /* Get markdown */
        $str = $this->markdown('gallery/admin', $params);

        return $str;
    }

    /* Delete gallery */
    public function deleteGallery()
    {
        $row = $this->d->rawQuery("select id, com, photo from #_gallery where hash != '' and date_created < " . (time() - 3 * 3600));
        $array = array("product" => UPLOAD_PRODUCT, "news" => UPLOAD_NEWS);

        if ($row) {
            foreach ($row as $item) {
                @unlink($array[$item['com']] . $item['photo']);
                $this->d->rawQuery("delete from #_gallery where id = " . $item['id']);
            }
        }
    }

    /* Generate hash */
    public function generateHash()
    {
        if (!$this->hash) {
            $this->hash = $this->stringRandom(10);
        }
        return $this->hash;
    }

    /* Lấy date */
    public function makeDate($time = 0, $dot = '.', $lang = 'vi', $f = false)
    {
        $str = ($lang == 'vi') ? date("d{$dot}m{$dot}Y", $time) : date("m{$dot}d{$dot}Y", $time);

        if ($f == true) {
            $thu['vi'] = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
            $thu['en'] = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
            $str = $thu[$lang][date('w', $time)] . ', ' . $str;
        }

        return $str;
    }

    /* Alert */
    public function alert($notify = '')
    {
        echo '<script language="javascript">alert("' . $notify . '")</script>';
    }

    /* Delete file */
    public function deleteFile($file = '')
    {
        return @unlink($file);
    }

    /* Transfer */
    public function transfer($msg = '', $page = '', $numb = true)
    {
        global $configBase;

        $basehref = $configBase;
        $showtext = $msg;
        $page_transfer = $page;
        $numb = $numb;

        include("./templates/layout/transfer.php");
        exit();
    }

    /* Redirect */
    public function redirect($url = '', $response = null)
    {
        header("location:$url", true, $response);
        exit();
    }

    /* Dump */
    public function dump($value = '', $exit = false)
    {
        echo "<pre>";
        print_r($value);
        echo "</pre>";
        if ($exit) exit();
    }

    /* Pagination */
    public function pagination($totalq = 0, $perPage = 10, $page = 1, $url = '?')
    {
        $urlpos = strpos($url, "?");
        $url = ($urlpos) ? $url . "&" : $url . "?";
        $total = $totalq;
        $adjacents = "2";
        $firstlabel = "First";
        $prevlabel = "Prev";
        $nextlabel = "Next";
        $lastlabel = "Last";
        $page = ($page == 0 ? 1 : $page);
        $start = ($page - 1) * $perPage;
        $prev = $page - 1;
        $next = $page + 1;
        $lastpage = ceil($total / $perPage);
        $lpm1 = $lastpage - 1;
        $pagination = "";

        if ($lastpage > 1) {
            $pagination .= "<ul class='pagination flex-wrap justify-content-center mb-0'>";
            $pagination .= "<li class='page-item'><span class='page-link'>Page {$page} / {$lastpage}</span></li>";

            if ($page > 1) {
                $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>{$firstlabel}</a></li>";
                if ($page == 2) {
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>{$prevlabel}</a></li>";
                } else {
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$prev}'>{$prevlabel}</a></li>";
                }
            }

            if ($lastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page) $pagination .= "<li class='page-item active'><span class='page-link'>{$counter}</span></li>";
                    else if ($counter == 1) $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>{$counter}</a></li>";
                    else $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$counter}'>{$counter}</a></li>";
                }
            } elseif ($lastpage > 5 + ($adjacents * 2)) {
                if ($page < 1 + ($adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $page) $pagination .= "<li class='page-item active'><span class='page-link'>{$counter}</span></li>";
                        else if ($counter == 1) $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>{$counter}</a></li>";
                        else $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$counter}'>{$counter}</a></li>";
                    }

                    $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>...</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$lpm1}'>{$lpm1}</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$lastpage}'>{$lastpage}</a></li>";
                } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>1</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=2'>2</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>...</a></li>";

                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page) $pagination .= "<li class='page-item active'><span class='page-link'>{$counter}</span></li>";
                        else if ($counter == 1) $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>{$counter}</a></li>";
                        else $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$counter}'>{$counter}</a></li>";
                    }

                    $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>...</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$lpm1}'>{$lpm1}</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$lastpage}'>{$lastpage}</a></li>";
                } else {
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>1</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=2'>2</a></li>";
                    $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>...</a></li>";

                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page) $pagination .= "<li class='page-item active'><span class='page-link'>{$counter}</span></li>";
                        else if ($counter == 1) $pagination .= "<li class='page-item'><a class='page-link' href='{$this->getCurrentPageURL()}'>{$counter}</a></li>";
                        else $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$counter}'>{$counter}</a></li>";
                    }
                }
            }

            if ($page < $counter - 1) {
                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p={$next}'>{$nextlabel}</a></li>";
                $pagination .= "<li class='page-item'><a class='page-link' href='{$url}p=$lastpage'>{$lastlabel}</a></li>";
            }

            $pagination .= "</ul>";
        }

        return $pagination;
    }

    /* UTF8 convert */
    public function utf8Convert($str = '')
    {
        if ($str != '') {
            $utf8 = array(
                'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
                'd' => 'đ|Đ',
                'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
                'i' => 'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',
                'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
                'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
                'y' => 'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ',
                '' => '`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\“|\”|\:|\;|_',
            );

            foreach ($utf8 as $ascii => $uni) {
                $str = preg_replace("/($uni)/i", $ascii, $str);
            }
        }

        return $str;
    }

    /* Change title */
    public function changeTitle($text = '')
    {
        if ($text != '') {
            $text = strtolower($this->utf8Convert($text));
            $text = preg_replace("/[^a-z0-9-\s]/", "", $text);
            $text = preg_replace('/([\s]+)/', '-', $text);
            $text = str_replace(array('%20', ' '), '-', $text);
            $text = preg_replace("/\-\-\-\-\-/", "-", $text);
            $text = preg_replace("/\-\-\-\-/", "-", $text);
            $text = preg_replace("/\-\-\-/", "-", $text);
            $text = preg_replace("/\-\-/", "-", $text);
            $text = '@' . $text . '@';
            $text = preg_replace('/\@\-|\-\@|\@/', '', $text);
        }

        return $text;
    }

    public function changeTitle2($text = '')
    {
        if ($text != '') {
            $text = strtolower($this->utf8Convert($text));
            $text = preg_replace("/[^a-z0-9-\s]/", "", $text);
            $text = preg_replace('/([\s]+)/', '', $text);
            $text = str_replace(array('%20', ' '), '', $text);
            $text = preg_replace("/\-\-\-\-\-/", "", $text);
            $text = preg_replace("/\-\-\-\-/", "", $text);
            $text = preg_replace("/\-\-\-/", "", $text);
            $text = preg_replace("/\-\-/", "", $text);
            $text = '@' . $text . '@';
            $text = preg_replace('/\@\-|\-\@|\@/', '', $text);
        }

        return $text;
    }

    /* Lấy IP */
    public function getRealIPAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /* Lấy getPageURL */
    public function getPageURL()
    {
        $pageURL = 'http';
        if (array_key_exists('HTTPS', $_SERVER) && $_SERVER["HTTPS"] == "on") $pageURL .= "s";
        $pageURL .= "://";
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        return $pageURL;
    }

    /* Lấy getCurrentPageURL */
    public function getCurrentPageURL()
    {
        $pageURL = 'http';
        if (array_key_exists('HTTPS', $_SERVER) && $_SERVER["HTTPS"] == "on") $pageURL .= "s";
        $pageURL .= "://";
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        $urlpos = strpos($pageURL, "?p");
        $pageURL = ($urlpos) ? explode("?p=", $pageURL) : explode("&p=", $pageURL);
        return $pageURL[0];
    }

    /* Lấy getCurrentPageURL Cano */
    public function getCurrentPageURL_CANO()
    {
        $pageURL = 'http';
        if (array_key_exists('HTTPS', $_SERVER) && $_SERVER["HTTPS"] == "on") $pageURL .= "s";
        $pageURL .= "://";
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        $pageURL = str_replace("amp/", "", $pageURL);
        $urlpos = strpos($pageURL, "?p");
        $pageURL = ($urlpos) ? explode("?p=", $pageURL) : explode("&p=", $pageURL);
        $pageURL = explode("?", $pageURL[0]);
        $pageURL = explode("#", $pageURL[0]);
        $pageURL = explode("index", $pageURL[0]);
        return $pageURL[0];
    }

    /* Has file */
    public function hasFile($file)
    {
        if (isset($_FILES[$file])) {
            if ($_FILES[$file]['error'] == 4) {
                return false;
            } else if ($_FILES[$file]['error'] == 0) {
                return true;
            }
        } else {
            return false;
        }
    }

    /* Size file */
    public function sizeFile($file)
    {
        if ($this->hasFile($file)) {
            if ($_FILES[$file]['error'] == 0) {
                return $_FILES[$file]['size'];
            }
        } else {
            return 0;
        }
    }

    /* Check file */
    public function checkFile($file)
    {
        global $config;

        $result = true;

        if ($this->hasFile($file)) {
            if ($this->sizeFile($file) > $config['website']['video']['max-size']) {
                $result = false;
            }
        }

        return $result;
    }

    /* Check extension file */
    public function checkExtFile($file)
    {
        global $config;

        $result = true;

        if ($this->hasFile($file)) {
            $ext = $this->infoPath($_FILES[$file]["name"], 'extension');

            if (!in_array($ext, $config['website']['video']['extension'])) {
                $result = false;
            }
        }

        return $result;
    }

    /* Info path */
    public function infoPath($filename = '', $type = '')
    {
        $result = '';

        if (!empty($filename) && !empty($type)) {
            if ($type == 'extension') {
                $result = pathinfo($filename, PATHINFO_EXTENSION);
            } else if ($type == 'filename') {
                $result = pathinfo($filename, PATHINFO_FILENAME);
            }
        }

        return $result;
    }

    /* Format bytes */
    public function formatBytes($size, $precision = 2)
    {
        $result = array();
        $base = log($size, 1024);
        $suffixes = array('', 'Kb', 'Mb', 'Gb', 'Tb');
        $result['numb'] = round(pow(1024, $base - floor($base)), $precision);
        $result['ext'] = $suffixes[floor($base)];

        return $result;
    }

    /* Copy image */
    public function copyImg($photo = '', $constant = '')
    {
        $str = '';

        if ($photo != '' && $constant != '') {
            $rand = rand(1000, 9999);
            $name = pathinfo($photo, PATHINFO_FILENAME);
            $ext = pathinfo($photo, PATHINFO_EXTENSION);
            $photo_new = $name . '-' . $rand . '.' . $ext;
            $oldpath = '../../upload/' . $constant . '/' . $photo;
            $newpath = '../../upload/' . $constant . '/' . $photo_new;

            if (file_exists($oldpath)) {
                if (copy($oldpath, $newpath)) {
                    $str = $photo_new;
                }
            }
        }

        return $str;
    }

    /* Get Img size */
    public function getImgSize($photo = '', $patch = '')
    {
        $array = array();
        if ($photo != '') {
            $x = (file_exists($patch)) ? getimagesize($patch) : null;
            $array = (!empty($x)) ? array("p" => $photo, "w" => $x[0], "h" => $x[1], "m" => $x['mime']) : null;
        }
        return $array;
    }

    /* Upload name */
    public function uploadName($name = '')
    {
        $result = '';

        if ($name != '') {
            $rand = rand(1000, 9999);
            $ten_anh = pathinfo($name, PATHINFO_FILENAME);
            $result = $this->changeTitle($ten_anh) . "-" . $rand;
        }

        return $result;
    }

    /* Resize images */
    public function smartResizeImage($file = '', $string = null, $width = 0, $height = 0, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false, $quality = 100, $grayscale = false)
    {
        if ($height <= 0 && $width <= 0) return false;
        if ($file === null && $string === null) return false;
        $info = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
        $image = '';
        $final_width = 0;
        $final_height = 0;
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;
        if ($proportional) {
            if ($width == 0) $factor = $height / $height_old;
            elseif ($height == 0) $factor = $width / $width_old;
            else $factor = min($width / $width_old, $height / $height_old);
            $final_width = round($width_old * $factor);
            $final_height = round($height_old * $factor);
        } else {
            $final_width = ($width <= 0) ? $width_old : $width;
            $final_height = ($height <= 0) ? $height_old : $height;
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;
            $x = min($widthX, $heightX);
            $cropWidth = ($width_old - $width * $x) / 2;
            $cropHeight = ($height_old - $height * $x) / 2;
        }
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);
                break;
            case IMAGETYPE_GIF:
                $file !== null ? $image = imagecreatefromgif($file) : $image = imagecreatefromstring($string);
                break;
            case IMAGETYPE_PNG:
                $file !== null ? $image = imagecreatefrompng($file) : $image = imagecreatefromstring($string);
                break;
            default:
                return false;
        }
        if ($grayscale) {
            imagefilter($image, IMG_FILTER_GRAYSCALE);
        }
        $image_resized = imagecreatetruecolor($final_width, $final_height);
        if (($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG)) {
            $transparency = imagecolortransparent($image);
            $palletsize = imagecolorstotal($image);
            if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color = imagecolorsforindex($image, $transparency);
                $transparency = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            } elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 255, 255, 255, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);
        if ($delete_original) {
            if ($use_linux_commands) exec('rm ' . $file);
            else @unlink($file);
        }
        switch (strtolower($output)) {
            case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
                break;
            case 'file':
                $output = $file;
                break;
            case 'return':
                return $image_resized;
                break;
            default:
                break;
        }
        switch ($info[2]) {
            case IMAGETYPE_GIF:
                imagegif($image_resized, $output);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($image_resized, $output, $quality);
                break;
            case IMAGETYPE_PNG:
                $quality = 9 - (int)((0.9 * $quality) / 10.0);
                imagepng($image_resized, $output, $quality);
                break;
            default:
                return false;
        }
        return true;
    }

    /* Correct images orientation */
    public function correctImageOrientation($filename)
    {
        ini_set('memory_limit', '1024M');
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($filename);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if ($orientation != 1) {
                    $img = imagecreatefromjpeg($filename);
                    $deg = 0;

                    switch ($orientation) {
                        case 3:
                            $image = imagerotate($img, 180, 0);
                            break;

                        case 6:
                            $image = imagerotate($img, -90, 0);
                            break;

                        case 8:
                            $image = imagerotate($img, 90, 0);
                            break;
                    }

                    imagejpeg($image, $filename, 90);
                }
            }
        }
    }

    /* Upload images */
    public function uploadImage($file = '', $extension = '', $folder = '', $newname = '')
    {
        global $config;

        if (isset($_FILES[$file]) && !$_FILES[$file]['error']) {
            $postMaxSize = ini_get('post_max_size');
            $MaxSize = $this->convertToBytes($postMaxSize);
            if ($_FILES[$file]['size'] > (int)$MaxSize) {
                $this->alert('Dung lượng file không được vượt quá ' . $postMaxSize);
                return false;
            }

            $ext = explode('.', $_FILES[$file]['name']);
            $ext = strtolower($ext[count($ext) - 1]);
            $name = basename($_FILES[$file]['name'], '.' . $ext);

            if (strpos($extension, $ext) === false) {
                $this->alert('Chỉ hỗ trợ upload file dạng ' . $extension);
                return false;
            }

            if ($newname == '' && file_exists($folder . $_FILES[$file]['name']))
                for ($i = 0; $i < 100; $i++) {
                    if (!file_exists($folder . $name . $i . '.' . $ext)) {
                        $_FILES[$file]['name'] = $name . $i . '.' . $ext;
                        break;
                    }
                }
            else {
                $_FILES[$file]['name'] = $newname . '.' . $ext;
            }

            if (!copy($_FILES[$file]["tmp_name"], $folder . $_FILES[$file]['name'])) {
                if (!move_uploaded_file($_FILES[$file]["tmp_name"], $folder . $_FILES[$file]['name'])) {
                    return false;
                }
            }

            /* Only process orientation and resize when the uploaded file is an image. */
            $array = @getimagesize($folder . $_FILES[$file]['name']);
            if ($array !== false) {
                $this->correctImageOrientation($folder . $_FILES[$file]['name']);

                list($image_w, $image_h) = $array;
                $maxWidth = $config['website']['upload']['max-width'];
                $maxHeight = $config['website']['upload']['max-height'];
                if ($image_w > $maxWidth) $this->smartResizeImage($folder . $_FILES[$file]['name'], null, $maxWidth, $maxHeight, true);
            }

            return $_FILES[$file]['name'];
        }
        return false;
    }

    function convertToBytes($inputString)
    {
        // Sử dụng preg_match để kiểm tra mẫu "M" hoặc "G"
        $regexPattern = '/^(\d+)([MG])$/';

        if (preg_match($regexPattern, $inputString, $matches)) {
            $size = (int)$matches[1];
            $unit = $matches[2];

            if ($unit === 'M') {
                return $size * 1024 * 1024; // Chuyển đổi thành bytes
            } elseif ($unit === 'G') {
                return $size * 1024 * 1024 * 1024; // Chuyển đổi thành bytes
            }
        }

        // Trả về -1 nếu chuỗi không hợp lệ
        return -1;
    }

    /* Delete folder */
    public function removeDir($dirname = '')
    {
        if (is_dir($dirname)) $dir_handle = opendir($dirname);
        if (!isset($dir_handle) || $dir_handle == false) return false;
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file)) unlink($dirname . "/" . $file);
                else $this->removeDir($dirname . '/' . $file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    /* Remove Sub folder */
    public function RemoveEmptySubFolders($path = '')
    {
        $empty = true;

        foreach (glob($path . DIRECTORY_SEPARATOR . "*") as $file) {
            if (is_dir($file)) {
                if (!$this->RemoveEmptySubFolders($file)) $empty = false;
            } else {
                $empty = false;
            }
        }

        if ($empty) {
            if (is_dir($path)) {
                rmdir($path);
            }
        }

        return $empty;
    }

    /* Remove files from dir in x seconds */
    public function RemoveFilesFromDirInXSeconds($dir = '', $seconds = 3600)
    {
        $files = glob(rtrim($dir, '/') . "/*");
        $now = time();

        if ($files) {
            foreach ($files as $file) {
                $filename = basename($file);
                if (is_file($file) && $filename != 'index.txt') {
                    if ($now - filemtime($file) >= $seconds) {
                        unlink($file);
                    }
                } else {
                    $this->RemoveFilesFromDirInXSeconds($file, $seconds);
                }
            }
        }
    }

    /* Remove zero bytes */
    public function removeZeroByte($dir)
    {
        $files = glob(rtrim($dir, '/') . "/*");
        if ($files) {
            foreach ($files as $file) {
                $filename = basename($file);
                if (is_file($file) && $filename != 'index.txt') {
                    if (!filesize($file)) {
                        unlink($file);
                    }
                } else {
                    $this->removeZeroByte($file);
                }
            }
        }
    }

    /* Filter opacity */
    public function filterOpacity($img = '', $opacity = 80)
    {
        return true;
        /*
			if(!isset($opacity) || $img == '') return false;

			$opacity /= 100;
			$w = imagesx($img);
			$h = imagesy($img);
			imagealphablending($img, false);
			$minalpha = 127;

			for($x = 0; $x < $w; $x++)
			{
				for($y = 0; $y < $h; $y++)
				{
					$alpha = (imagecolorat($img, $x, $y) >> 24) & 0xFF;
					if($alpha < $minalpha) $minalpha = $alpha;
				}
			}

			for($x = 0; $x < $w; $x++)
			{
				for($y = 0; $y < $h; $y++)
				{
					$colorxy = imagecolorat($img, $x, $y);
					$alpha = ($colorxy >> 24) & 0xFF;
					if($minalpha !== 127) $alpha = 127 + 127 * $opacity * ($alpha - 127) / (127 - $minalpha);
					else $alpha += 127 * $opacity;
					$alphacolorxy = imagecolorallocatealpha($img, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);
					if(!imagesetpixel($img, $x, $y, $alphacolorxy)) return false;
				}
			}

			return true;
			*/
    }

    /* Convert Webp */
    public function converWebp($in)
    {
        global $config;

        $in = $_SERVER['DOCUMENT_ROOT'] . $config['database']['url'] . str_replace(ASSET, "", $in);

        if (!extension_loaded('imagick')) {
            ob_start();

            WebPConvert::serveConverted($in, $in . ".webp", [
                'fail' => 'original',
                //'show-report' => true,
                'serve-image' => [
                    'headers' => [
                        'cache-control' => true,
                        'vary-accept' => true,
                    ],
                    'cache-control-header' => 'max-age=2',
                ],
                'convert' => [
                    "quality" => 100
                ]
            ]);

            file_put_contents($in . ".webp", ob_get_contents());
            ob_end_clean();
        } else {
            WebPConvert::convert($in, $in . ".webp", [
                'fail' => 'original',
                'convert' => [
                    'quality' => 100,
                    'max-quality' => 100,
                ]
            ]);
        }
    }

    public function createThumb($width_thumb = 0, $height_thumb = 0, $zoom_crop = '1', $src = '', $watermark = null, $path = THUMBS, $preview = false, $args = array(), $quality = 100)
    {
        $t = 3600 * 24 * 3;
        $this->RemoveFilesFromDirInXSeconds(UPLOAD_TEMP_L, 1);
        if ($watermark != null) {
            $this->RemoveFilesFromDirInXSeconds(WATERMARK . '/' . $path . "/", $t);
            $this->RemoveEmptySubFolders(WATERMARK . '/' . $path . "/");
        } else {
            $this->RemoveFilesFromDirInXSeconds($path . "/", $t);
            $this->RemoveEmptySubFolders($path . "/");
        }
        $src = str_replace("%20", " ", $src);
        $src = urldecode($src);
        if (!file_exists($src)) die("NO IMAGE $src");
        $image_url = $src;
        $origin_x = 0;
        $origin_y = 0;
        $array = getimagesize($image_url);
        if ($array) list($image_w, $image_h) = $array;
        else die("NO IMAGE $image_url");
        $width = $image_w;
        $height = $image_h;
        $new_width = ($width_thumb > 0) ? $width_thumb : $image_w;
        $new_height = ($height_thumb > 0) ? $height_thumb : $image_h;
        if ($new_width < 10 && $new_height < 10) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            die("Width and height larger than 10px");
        }
        if ($new_width > 2000 || $new_height > 2000) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            die("Width and height less than 2000px");
        }
        $array = getimagesize($image_url);
        if ($array) list($image_w, $image_h) = $array;
        else die("NO IMAGE $image_url");
        $width = $image_w;
        $height = $image_h;
        if ($new_height && !$new_width) $new_width = $width * ($new_height / $height);
        else if ($new_width && !$new_height) $new_height = $height * ($new_width / $width);
        $image_ext = explode('.', $image_url);
        $image_ext = trim(strtolower(end($image_ext)));
        $image_name = explode('/', $image_url);
        $image_name = trim(strtolower(end($image_name)));
        switch (strtoupper($image_ext)) {
            case 'WEBP':
                $image = imagecreatefromwebp($image_url);
                $func = 'imagejpeg';
                $mime_type = 'webp';
                break;
            case 'JPG':
            case 'JPEG':
                $image = imagecreatefromjpeg($image_url);
                $func = 'imagejpeg';
                $mime_type = 'jpeg';
                break;
            case 'PNG':
                $image = imagecreatefrompng($image_url);
                $func = 'imagepng';
                $mime_type = 'png';
                break;
            case 'GIF':
                $image = imagecreatefromgif($image_url);
                $func = 'imagegif';
                $mime_type = 'png';
                break;
            default:
                die("UNKNOWN IMAGE TYPE: $image_url");
        }
        $_new_width = $new_width;
        $_new_height = $new_height;
        if ($zoom_crop == 3) {
            $final_height = $height * ($new_width / $width);
            if ($final_height > $new_height) $new_width = $width * ($new_height / $height);
            else $new_height = $final_height;
        }
        $canvas = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($canvas, false);
        $color = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefill($canvas, 0, 0, $color);
        if ($zoom_crop == 2) {
            $final_height = $height * ($new_width / $width);
            if ($final_height > $new_height) {
                $origin_x = $new_width / 2;
                $new_width = $width * ($new_height / $height);
                $origin_x = round($origin_x - ($new_width / 2));
            } else {
                $origin_y = $new_height / 2;
                $new_height = $final_height;
                $origin_y = round($origin_y - ($new_height / 2));
            }
        }
        imagesavealpha($canvas, true);
        if ($zoom_crop > 0) {
            $align = '';
            $src_x = $src_y = 0;
            $src_w = $width;
            $src_h = $height;
            $cmp_x = $width / $new_width;
            $cmp_y = $height / $new_height;
            if ($cmp_x > $cmp_y) {
                $src_w = round($width / $cmp_x * $cmp_y);
                $src_x = round(($width - ($width / $cmp_x * $cmp_y)) / 2);
            } else if ($cmp_y > $cmp_x) {
                $src_h = round($height / $cmp_y * $cmp_x);
                $src_y = round(($height - ($height / $cmp_y * $cmp_x)) / 2);
            }
            if ($align) {
                if (strpos($align, 't') !== false) {
                    $src_y = 0;
                }
                if (strpos($align, 'b') !== false) {
                    $src_y = $height - $src_h;
                }
                if (strpos($align, 'l') !== false) {
                    $src_x = 0;
                }
                if (strpos($align, 'r') !== false) {
                    $src_x = $width - $src_w;
                }
            }
            imagecopyresampled($canvas, $image, $origin_x, $origin_y, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);
        } else {
            imagecopyresampled($canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        }
        if ($preview) {
            $watermark = array();
            $watermark['status'] = 'hienthi';
            $options = $args;
            $overlay_url = $args['watermark'];
        }
        $upload_dir = '';
        $folder_old = str_replace($image_name, '', $image_url);
        if (!empty($watermark['status']) && strpos('hienthi', $watermark['status']) !== false) {
            $upload_dir = WATERMARK . '/' . $path . '/' . $width_thumb . 'x' . $height_thumb . 'x' . $zoom_crop . '/' . $folder_old;
        } else {
            if ($watermark != null) $upload_dir = WATERMARK . '/' . $path . '/' . $width_thumb . 'x' . $height_thumb . 'x' . $zoom_crop . '/' . $folder_old;
            else $upload_dir = $path . '/' . $width_thumb . 'x' . $height_thumb . 'x' . $zoom_crop . '/' . $folder_old;
        }
        if (!file_exists($upload_dir)) if (!mkdir($upload_dir, 0777, true)) die('Failed to create folders...');
        if (!empty($watermark['status']) && strpos('hienthi', $watermark['status']) !== false) {
            $options = (isset($options)) ? $options : json_decode($watermark['options'], true)['watermark'];
            $per_scale = $options['per'];
            $per_small_scale = $options['small_per'];
            $max_width_w = $options['max'];
            $min_width_w = $options['min'];
            $opacity = @$options['opacity'];
            $overlay_url = (isset($overlay_url)) ? $overlay_url : UPLOAD_PHOTO_L . $watermark['photo'];
            $overlay_ext = explode('.', $overlay_url);
            $overlay_ext = trim(strtolower(end($overlay_ext)));
            switch (strtoupper($overlay_ext)) {
                case 'JPG':
                case 'JPEG':
                    $overlay_image = imagecreatefromjpeg($overlay_url);
                    break;
                case 'PNG':
                    $overlay_image = imagecreatefrompng($overlay_url);
                    break;
                case 'GIF':
                    $overlay_image = imagecreatefromgif($overlay_url);
                    break;
                default:
                    die("UNKNOWN IMAGE TYPE: $overlay_url");
            }
            //$this->filterOpacity($overlay_image,$opacity);
            $overlay_width = imagesx($overlay_image);
            $overlay_height = imagesy($overlay_image);
            $overlay_padding = 5;
            imagealphablending($canvas, true);
            if (min($_new_width, $_new_height) <= 300) $per_scale = $per_small_scale;
            $oz = max($overlay_width, $overlay_height);
            if ($overlay_width > $overlay_height) {
                $scale = $_new_width / $oz;
            } else {
                $scale = $_new_height / $oz;
            }
            if ($_new_height > $_new_width) {
                $scale = $_new_height / $oz;
            }
            $new_overlay_width = (floor($overlay_width * $scale) - $overlay_padding * 2) / $per_scale;
            $new_overlay_height = (floor($overlay_height * $scale) - $overlay_padding * 2) / $per_scale;
            $scale_w = $new_overlay_width / $new_overlay_height;
            $scale_h = $new_overlay_height / $new_overlay_width;
            $new_overlay_height = $new_overlay_width / $scale_w;
            if ($new_overlay_height > $_new_height) {
                $new_overlay_height = $_new_height / $per_scale;
                $new_overlay_width = $new_overlay_height * $scale_w;
            }
            if ($new_overlay_width > $_new_width) {
                $new_overlay_width = $_new_width / $per_scale;
                $new_overlay_height = $new_overlay_width * $scale_h;
            }
            if (($_new_width / $new_overlay_width) < $per_scale) {
                $new_overlay_width = $_new_width / $per_scale;
                $new_overlay_height = $new_overlay_width * $scale_h;
            }
            if ($_new_height < $_new_width && ($_new_height / $new_overlay_height) < $per_scale) {
                $new_overlay_height = $_new_height / $per_scale;
                $new_overlay_width = $new_overlay_height / $scale_h;
            }
            if ($new_overlay_width > $max_width_w && $new_overlay_width) {
                $new_overlay_width = $max_width_w;
                $new_overlay_height = $new_overlay_width * $scale_h;
            }
            if ($new_overlay_width < $min_width_w && $_new_width <= $min_width_w * 3) {
                $new_overlay_width = $min_width_w;
                $new_overlay_height = $new_overlay_width * $scale_h;
            }
            $new_overlay_width = round($new_overlay_width);
            $new_overlay_height = round($new_overlay_height);
            switch ($options['position']) {
                case 1:
                    $khoancachx = $overlay_padding;
                    $khoancachy = $overlay_padding;
                    break;
                case 2:
                    $khoancachx = abs($_new_width - $new_overlay_width) / 2;
                    $khoancachy = $overlay_padding;
                    break;
                case 3:
                    $khoancachx = abs($_new_width - $new_overlay_width) - $overlay_padding;
                    $khoancachy = $overlay_padding;
                    break;
                case 4:
                    $khoancachx = abs($_new_width - $new_overlay_width) - $overlay_padding;
                    $khoancachy = abs($_new_height - $new_overlay_height) / 2;
                    break;
                case 5:
                    $khoancachx = abs($_new_width - $new_overlay_width) - $overlay_padding;
                    $khoancachy = abs($_new_height - $new_overlay_height) - $overlay_padding;
                    break;
                case 6:
                    $khoancachx = abs($_new_width - $new_overlay_width) / 2;
                    $khoancachy = abs($_new_height - $new_overlay_height) - $overlay_padding;
                    break;
                case 7:
                    $khoancachx = $overlay_padding;
                    $khoancachy = abs($_new_height - $new_overlay_height) - $overlay_padding;
                    break;
                case 8:
                    $khoancachx = $overlay_padding;
                    $khoancachy = abs($_new_height - $new_overlay_height) / 2;
                    break;
                case 9:
                    $khoancachx = abs($_new_width - $new_overlay_width) / 2;
                    $khoancachy = abs($_new_height - $new_overlay_height) / 2;
                    break;
                default:
                    $khoancachx = $overlay_padding;
                    $khoancachy = $overlay_padding;
                    break;
            }
            $overlay_new_image = imagecreatetruecolor($new_overlay_width, $new_overlay_height);
            imagealphablending($overlay_new_image, false);
            imagesavealpha($overlay_new_image, true);
            imagecopyresampled($overlay_new_image, $overlay_image, 0, 0, 0, 0, $new_overlay_width, $new_overlay_height, $overlay_width, $overlay_height);
            imagecopy($canvas, $overlay_new_image, $khoancachx, $khoancachy, 0, 0, $new_overlay_width, $new_overlay_height);
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
        }
        if ($preview) {
            $upload_dir = '';
            $this->RemoveEmptySubFolders(WATERMARK . '/' . $path . "/");
        }
        if ($upload_dir) {
            if ($func == 'imagejpeg') $func($canvas, $upload_dir . $image_name, 100);
            else $func($canvas, $upload_dir . $image_name, floor($quality * 0.09));
        }
        header('Content-Type: image/' . $mime_type);
        if ($func == 'imagejpeg') $func($canvas, NULL, 100);
        else $func($canvas, NULL, floor($quality * 0.09));
        imagedestroy($canvas);
    }

    /* String random */
    public function stringRandom($sokytu = 10)
    {
        $str = '';

        if ($sokytu > 0) {
            $chuoi = 'ABCDEFGHIJKLMNOPQRSTUVWXYZWabcdefghijklmnopqrstuvwxyzw0123456789';
            for ($i = 0; $i < $sokytu; $i++) {
                $vitri = mt_rand(0, strlen($chuoi));
                $str = $str . substr($chuoi, $vitri, 1);
            }
        }

        return $str;
    }

    /* Digital random */
    public function digitalRandom($min = 1, $max = 10, $num = 10)
    {
        $result = '';

        if ($num > 0) {
            for ($i = 0; $i < $num; $i++) {
                $result .= rand($min, $max);
            }
        }

        return $result;
    }

    /* Get permission */
    public function getPermission($id_permission = 0)
    {
        $row = $this->cache->get("select * from #_permission_group where find_in_set('hienthi',status) order by numb,id desc", null, "result", 7200);

        $str = '<select id="id_permission" name="data[id_permission]" class="form-control select2"><option value="0">Nhóm quyền</option>';
        foreach ($row as $v) {
            if ($v["id"] == (int)@$id_permission) $selected = "selected";
            else $selected = "";

            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["name"] . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    /* Get status order */
    public function orderStatus($status = 0)
    {
        $row = $this->cache->get("select * from #_order_status order by id", null, "result", 7200);

        $str = '<select id="order_status" name="data[order_status]" class="form-control custom-select text-sm"><option value="0">' . chontinhtrang . '</option>';
        foreach ($row as $v) {
            if (isset($_REQUEST['order_status']) && ($v["id"] == (int)$_REQUEST['order_status']) || ($v["id"] == $status)) $selected = "selected";
            else $selected = "";

            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["namevi"] . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    /* Lấy thông tin chi tiết */
    public function getInfoDetail($cols = '', $table = '', $id = 0)
    {
        $row = array();

        if (!empty($cols) && !empty($table) && !empty($id)) {
            $row = $this->cache->get("select $cols from #_$table where id = ? limit 0,1", array($id), "fetch", 7200);
        }

        return $row;
    }

    /* Join column */
    public function joinCols($array = null, $column = null)
    {
        $str = '';
        $arrayTemp = array();

        if ($array && $column) {
            foreach ($array as $k => $v) {
                if (!empty($v[$column])) {
                    $arrayTemp[] = $v[$column];
                }
            }

            if (!empty($arrayTemp)) {
                $arrayTemp = array_unique($arrayTemp);
                $str = implode(",", $arrayTemp);
            }
        }

        return $str;
    }

    /* Get payments order */
    function orderPayments()
    {
        $row = $this->cache->get("select * from #_news where type='hinh-thuc-thanh-toan' order by numb,id desc", null, "result", 7200);

        $str = '<select id="order_payment" name="order_payment" class="form-control custom-select text-sm"><option value="0">' . chonhinhthucthanhtoan . '</option>';
        foreach ($row as $v) {
            if (isset($_REQUEST['order_payment']) && ($v["id"] == (int)$_REQUEST['order_payment'])) $selected = "selected";
            else $selected = "";
            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["namevi"] . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    /* Get color */
    public function getColor($id = 0)
    {
        global $type;

        if ($id) {
            $temps = $this->d->rawQuery("select id_color from #_product_sale where id_parent = ?", array($id));
            $temps = (!empty($temps)) ? $this->joinCols($temps, 'id_color') : array();
            $temps = (!empty($temps)) ? explode(",", $temps) : array();
        }

        $row_color = $this->d->rawQuery("select namevi, id from #_color where type = ? order by numb,id desc", array($type));

        $str = '<select id="dataColor" name="dataColor[]" class="select multiselect" multiple="multiple" >';
        for ($i = 0; $i < count($row_color); $i++) {
            if (!empty($temps)) {
                if (in_array($row_color[$i]['id'], $temps)) $selected = 'selected="selected"';
                else $selected = '';
            } else {
                $selected = '';
            }
            $str .= '<option value="' . $row_color[$i]["id"] . '" ' . $selected . ' /> ' . $row_color[$i]["namevi"] . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    /* Get size */
    public function getSize($id = 0)
    {
        global $type;

        if ($id) {
            $temps = $this->d->rawQuery("select id_size from #_product_sale where id_parent = ?", array($id));
            $temps = (!empty($temps)) ? $this->joinCols($temps, 'id_size') : array();
            $temps = (!empty($temps)) ? explode(",", $temps) : array();
        }

        $row_size = $this->d->rawQuery("select namevi, id from #_size where type = ? order by numb,id desc", array($type));

        $str = '<div class="product-variant-select2-wrap">';
        $str .= '<div class="product-variant-select2-actions"><button type="button" class="btn-product-variant-select-all">Chọn tất cả</button><button type="button" class="btn-product-variant-clear-all">Bỏ chọn tất cả</button></div>';
        $str .= '<select id="dataSize" name="dataSize[]" class="select2 product-variant-select2" multiple="multiple" data-placeholder="Chọn phân loại" style="width:100%">';
        for ($i = 0; $i < count($row_size); $i++) {
            if (!empty($temps)) {
                if (in_array($row_size[$i]['id'], $temps)) $selected = 'selected="selected"';
                else $selected = '';
            } else {
                $selected = '';
            }
            $str .= '<option value="' . (int)$row_size[$i]['id'] . '" ' . $selected . '>' . htmlspecialchars($row_size[$i]['namevi'], ENT_QUOTES, 'UTF-8') . '</option>';
        }
        $str .= '</select></div>';

        return $str;
    }

    /* Get tags */
    public function getTags($id = 0, $element = '', $table = '', $type = '')
    {
        if ($id) {
            $temps = $this->d->rawQuery("select id_tags from #_" . $table . " where id_parent = ?", array($id));
            $temps = (!empty($temps)) ? $this->joinCols($temps, 'id_tags') : array();
            $temps = (!empty($temps)) ? explode(",", $temps) : array();
        }

        $row_tags = $this->cache->get("select namevi, id from #_tags where type = ? order by numb,id desc", array($type), "result", 7200);

        $str = '<select id="' . $element . '" name="' . $element . '[]" class="select multiselect" multiple="multiple" >';
        for ($i = 0; $i < count($row_tags); $i++) {
            if (!empty($temps)) {
                if (in_array($row_tags[$i]['id'], $temps)) $selected = 'selected="selected"';
                else $selected = '';
            } else {
                $selected = '';
            }
            $str .= '<option value="' . $row_tags[$i]["id"] . '" ' . $selected . ' /> ' . $row_tags[$i]["namevi"] . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    /* Get category by ajax */
    public function getAjaxCategory($table = '', $level = '', $type = '', $title_select = chondanhmuc, $class_select = 'select-category')
    {
        $where = '';
        $params = array($type);
        $id_parent = 'id_' . $level;
        $data_level = '';
        $data_type = 'data-type="' . $type . '"';
        $data_table = '';
        $data_child = '';

        if ($level == 'list') {
            $data_level = 'data-level="0"';
            $data_table = 'data-table="#_' . $table . '_cat"';
            $data_child = 'data-child="id_cat"';
        } else if ($level == 'cat') {
            $data_level = 'data-level="1"';
            $data_table = 'data-table="#_' . $table . '_item"';
            $data_child = 'data-child="id_item"';

            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);
        } else if ($level == 'item') {
            $data_level = 'data-level="2"';
            $data_table = 'data-table="#_' . $table . '_sub"';
            $data_child = 'data-child="id_sub"';

            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);

            $idcat = (isset($_REQUEST['id_cat'])) ? htmlspecialchars($_REQUEST['id_cat']) : 0;
            $where .= ' and id_cat = ?';
            array_push($params, $idcat);
        } else if ($level == 'sub') {
            $data_level = '';
            $data_type = '';
            $class_select = '';

            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);

            $idcat = (isset($_REQUEST['id_cat'])) ? htmlspecialchars($_REQUEST['id_cat']) : 0;
            $where .= ' and id_cat = ?';
            array_push($params, $idcat);

            $iditem = (isset($_REQUEST['id_item'])) ? htmlspecialchars($_REQUEST['id_item']) : 0;
            $where .= ' and id_item = ?';
            array_push($params, $iditem);
        } else if ($level == 'brand') {
            $data_level = '';
            $data_type = '';
            $class_select = '';
        }

        $rows = $this->cache->get("select namevi, id from #_" . $table . "_" . $level . " where type = ? " . $where . " order by numb,id desc", $params, "result", 7200);

        $str = '<select id="' . $id_parent . '" name="data[' . $id_parent . ']" ' . $data_level . ' ' . $data_type . ' ' . $data_table . ' ' . $data_child . ' class="form-control select2 ' . $class_select . '"><option value="0">' . $title_select . '</option>';
        foreach ($rows as $v) {
            if (isset($_REQUEST[$id_parent]) && ($v["id"] == (int)$_REQUEST[$id_parent])) $selected = "selected";
            else $selected = "";

            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["namevi"] . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    /* Get category by link */
    public function getLinkCategory($table = '', $level = '', $type = '', $title_select = chondanhmuc)
    {
        $where = '';
        $params = array($type);
        $id_parent = 'id_' . $level;

        if ($level == 'cat') {
            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);
        } else if ($level == 'item') {
            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);

            $idcat = (isset($_REQUEST['id_cat'])) ? htmlspecialchars($_REQUEST['id_cat']) : 0;
            $where .= ' and id_cat = ?';
            array_push($params, $idcat);
        } else if ($level == 'sub') {
            $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
            $where .= ' and id_list = ?';
            array_push($params, $idlist);

            $idcat = (isset($_REQUEST['id_cat'])) ? htmlspecialchars($_REQUEST['id_cat']) : 0;
            $where .= ' and id_cat = ?';
            array_push($params, $idcat);

            $iditem = (isset($_REQUEST['id_item'])) ? htmlspecialchars($_REQUEST['id_item']) : 0;
            $where .= ' and id_item = ?';
            array_push($params, $iditem);
        }

        $rows = $this->cache->get("select namevi, id from #_" . $table . "_" . $level . " where type = ? " . $where . " order by numb,id desc", $params, "result", 7200);

        $str = '<select id="' . $id_parent . '" name="' . $id_parent . '" onchange="onchangeCategory($(this))" class="form-control filter-category select2"><option value="0">' . $title_select . '</option>';
        foreach ($rows as $v) {
            if (isset($_REQUEST[$id_parent]) && ($v["id"] == (int)$_REQUEST[$id_parent])) $selected = "selected";
            else $selected = "";

            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["namevi"] . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    /* Get place by ajax */
    public function getAjaxPlace($table = '', $title_select = chondanhmuc)
    {
        $where = '';
        $params = array('0');
        $id_parent = 'id_' . $table;
        $data_level = '';
        $data_table = '';
        $data_child = '';

        if ($table == 'city') {
            $data_level = 'data-level="0"';
            $data_table = 'data-table="#_district"';
            $data_child = 'data-child="id_district"';
        } else if ($table == 'district') {
            $data_level = 'data-level="1"';
            $data_table = 'data-table="#_ward"';
            $data_child = 'data-child="id_ward"';

            $idcity = (isset($_REQUEST['id_city'])) ? htmlspecialchars($_REQUEST['id_city']) : 0;
            $where .= ' and id_city = ?';
            array_push($params, $idcity);
        } else if ($table == 'ward') {
            $data_level = '';
            $data_table = '';
            $data_child = '';

            $idcity = (isset($_REQUEST['id_city'])) ? htmlspecialchars($_REQUEST['id_city']) : 0;
            $where .= ' and id_city = ?';
            array_push($params, $idcity);

            $iddistrict = (isset($_REQUEST['id_district'])) ? htmlspecialchars($_REQUEST['id_district']) : 0;
            $where .= ' and id_district = ?';
            array_push($params, $iddistrict);
        }

        $rows = $this->cache->get("select name, id from #_" . $table . " where id <> ? " . $where . " order by id asc", $params, "result", 7200);

        $str = '<select id="' . $id_parent . '" name="data[' . $id_parent . ']" ' . $data_level . ' ' . $data_table . ' ' . $data_child . ' class="form-control select2 select-place"><option value="0">' . $title_select . '</option>';
        foreach ($rows as $v) {
            if (isset($_REQUEST[$id_parent]) && ($v["id"] == (int)$_REQUEST[$id_parent])) $selected = "selected";
            else $selected = "";

            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["name"] . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    /* Get place by link */
    public function getLinkPlace($table = '', $title_select = chondanhmuc)
    {
        $where = '';
        $params = array('0');
        $id_parent = 'id_' . $table;

        if ($table == 'district') {
            $idcity = (isset($_REQUEST['id_city'])) ? htmlspecialchars($_REQUEST['id_city']) : 0;
            $where .= ' and id_city = ?';
            array_push($params, $idcity);
        } else if ($table == 'ward') {
            $idcity = (isset($_REQUEST['id_city'])) ? htmlspecialchars($_REQUEST['id_city']) : 0;
            $where .= ' and id_city = ?';
            array_push($params, $idcity);

            $iddistrict = (isset($_REQUEST['id_district'])) ? htmlspecialchars($_REQUEST['id_district']) : 0;
            $where .= ' and id_district = ?';
            array_push($params, $iddistrict);
        }

        $rows = $this->cache->get("select name, id from #_" . $table . " where id <> ? " . $where . " order by id asc", $params, "result", 7200);

        $str = '<select id="' . $id_parent . '" name="' . $id_parent . '" onchange="onchangeCategory($(this))" class="form-control filter-category select2"><option value="0">' . $title_select . '</option>';
        foreach ($rows as $v) {
            if (isset($_REQUEST[$id_parent]) && ($v["id"] == (int)$_REQUEST[$id_parent])) $selected = "selected";
            else $selected = "";

            $str .= '<option value=' . $v["id"] . ' ' . $selected . '>' . $v["name"] . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    public function buildSchemaProduct($id_pro, $name, $image, $description, $code_pro, $name_brand, $name_author, $url, $price)
    {

        $str = '{';
        $str .= '"@context": "https://schema.org/",';
        $str .= '"@type": "Product",';
        $str .= '"name": "' . $name . '",';
        $str .= '"image":';
        $str .= '[';
        foreach ($image as $k => $v) {
            $str .= '{';
            $str .= '"@context": "https://schema.org/",';
            $str .= '"@type": "ImageObject",';
            $str .= '"contentUrl": "' . $v . '",';
            $str .= '"url": "' . $v . '",';
            $str .= '"license": "' . $url . '",';
            $str .= '"acquireLicensePage": "' . $url . '",';
            $str .= '"creditText": "' . $name . '",';
            $str .= '"copyrightNotice": "' . $name_author . '",';
            $str .= '"creator":';
            $str .= '{';
            $str .= '"@type": "Organization",';
            $str .= '"name": "' . $name_author . '"';
            $str .= '}';
            $str .= '}' . (($k < count($image) - 1) ? ',' : '') . '';
        }
        $str .= '],';
        $str .= '"description": "' . $description . '",';
        $str .= '"sku":"SP0' . $id_pro . '",';
        $str .= '"mpn": "' . $code_pro . '",';
        $str .= '"brand":';
        $str .= '{';
        $str .= '"@type": "Brand",';
        $str .= '"name": "' . $name_brand . '"';
        $str .= '},';
        $str .= '"offers":';
        $str .= '{';
        $str .= '"@type": "Offer",';
        $str .= '"url": "' . $url . '",';
        $str .= '"priceCurrency": "VND",';
        $str .= '"priceValidUntil": "2099-11-20",';
        $str .= '"price": "' . $price . '",';
        $str .= '"itemCondition": "https://schema.org/NewCondition",';
        $str .= '"availability": "https://schema.org/InStock"';
        $str .= '}';
        $str .= '}';

        $str = json_encode(json_decode($str), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $str;
    }
    /* Build Schema */
    public function buildSchemaArticle($id_news, $name, $image, $ngaytao, $ngaysua, $name_author, $url, $logo, $url_author)
    {
        $str = '{';
        $str .= '"@context": "https://schema.org",';
        $str .= '"@type": "NewsArticle",';
        $str .= '"mainEntityOfPage": ';
        $str .= '{';
        $str .= '"@type": "WebPage",';
        $str .= '"@id": "' . $url . '"';
        $str .= '},';
        $str .= '"headline": "' . $name . '",';
        $str .= '"image":';
        $str .= '{';
        $str .= '"@context": "https://schema.org/",';
        $str .= '"@type": "ImageObject",';
        $str .= '"contentUrl": "' . $image . '",';
        $str .= '"url": "' . $image . '",';
        $str .= '"license": "' . $url . '",';
        $str .= '"acquireLicensePage": "' . $url . '",';
        $str .= '"creditText": "' . $name . '",';
        $str .= '"copyrightNotice": "' . $name_author . '",';
        $str .= '"creator":';
        $str .= '{';
        $str .= '"@type": "Organization",';
        $str .= '"name": "' . $name_author . '"';
        $str .= '}';
        $str .= '},';
        $str .= '"datePublished": "' . date('c', $ngaytao) . '",';
        $str .= '"dateModified": "' . date('c', $ngaysua) . '",';
        $str .= '"author":';
        $str .= '{';
        $str .= '"@type": "Organization",';
        $str .= '"name": "' . $name_author . '",';
        $str .= '"url": "' . $url_author . '"';
        $str .= '},';
        $str .= '"publisher": ';
        $str .= '{';
        $str .= '"@type": "Organization",';
        $str .= '"name": "' . $name_author . '",';
        $str .= '"logo": ';
        $str .= '{';
        $str .= '"@type": "ImageObject",';
        $str .= '"url": "' . $logo . '"';
        $str .= '}';
        $str .= '}';
        $str .= '}';

        $str = json_encode(json_decode($str), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $str;
    }
    /* Product Seen */
    public function product_seen_exists($id = 0)
    {
        (!isset($_SESSION['pro_seen'])) ? $_SESSION['pro_seen'] = array() : "";
        if (!in_array($id, $_SESSION['pro_seen']))
            $_SESSION['pro_seen'][count($_SESSION['pro_seen'])] = $id;
    }
    /* End Product Seen */

    /* Auto Link Keyword */
    public function autoLinkKeyword($content = '')
    {
        if (empty($content)) return $content;

        $getKeywords = $this->cache->get("select title, link, target from #_seokeyword where find_in_set('hienthi',status) order by numb,id desc", null, 'result', 7200);

        if (!empty($getKeywords)) {
            foreach ($getKeywords as $v) {
                if (!empty($v['title']) && !empty($v['link'])) {
                    // Prevent replacing inside existing <a> tags, <img...>, etc.
                    $title = preg_quote($v['title'], '/');
                    $content = preg_replace_callback(
                        '/(<a\b[^>]*>.*?<\/a>|<img\b[^>]*>|<[^>]+>)|(' . $title . ')/iu',
                        function ($matches) use ($v) {
                            if (!empty($matches[1])) {
                                return $matches[1];
                            } else {
                                return '<a href="' . htmlspecialchars($v['link']) . '" target="' . htmlspecialchars($v['target']) . '" title="' . htmlspecialchars($v['title']) . '">' . $matches[2] . '</a>';
                            }
                        },
                        $content
                    );
                }
            }
        }
        return $content;
    }
}
?>
