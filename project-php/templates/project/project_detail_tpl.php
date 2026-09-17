<?php
// Lấy tác giả nếu có
$authorName = !empty($rowDetail['author']) ? $rowDetail['author'] : (!empty($setting['namevi']) ? $setting['namevi'] : '');
?>
<!-- ===== CỘT TRÁI: NỘI DUNG BÀI VIẾT ===== -->
<div class="">
  <!-- Tiêu đề -->
  <h1 class="nd-title"><?= $rowDetail['name' . $lang] ?></h1>
  <!-- Meta: ngày + tác giả + lượt xem -->
  <div class="nd-meta">
    <?php if (!empty($authorName)): ?>
      <span class="nd-meta-item">
        <i class="bi bi-person-fill"></i> <?= htmlspecialchars($authorName) ?>
      </span>
    <?php endif; ?>
    <span class="nd-meta-item">
      <i class="bi bi-calendar-check-fill"></i> <?= date("d/m/Y", $rowDetail['date_created']) ?>
    </span>
    <span class="nd-meta-item">
      <i class="bi bi-eye-fill"></i> <?= $rowDetail['view'] ?> <?= luotxem ?>
    </span>
    <?php if (!empty($newsList['name' . $lang])): ?>
      <span class="nd-meta-item nd-meta-cat">
        <i class="bi bi-tag-fill"></i>
        <a href="<?= $newsList[$sluglang] ?>"><?= $newsList['name' . $lang] ?></a>
      </span>
    <?php endif; ?>
  </div>

  <!-- Nội dung bài viết -->
  <?php if (!empty($rowDetail['content' . $lang])): ?>
    <!-- Mục lục -->
    <div class="meta-toc">
      <a class="mucluc-dropdown-list_button"></a>
      <div class="box-readmore">
        <ul class="toc-list" data-toc="article" data-toc-headings="h1, h2, h3"></ul>
      </div>
    </div>
    <div class="content-main content-ck" id="toc-content"><?= $func->autoLinkKeyword($func->getHtmlChars($rowDetail['content' . $lang])) ?></div>
    <!-- Chia sẻ -->
    <div class="nd-share">
      <span class="nd-share-label"><i class="bi bi-share-fill"></i> <?= chiase ?>:</span>
      <div class="social-plugin w-clear">
        <?php
        $params = array();
        $params['oaid'] = $optsetting['oaidzalo'];
        echo $func->markdown('social/share', $params);
        ?>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warning w-100" role="alert">
      <strong><?= noidungdangcapnhat ?></strong>
    </div>
  <?php endif; ?>
  <?php include TEMPLATE . "news/article_ai_facts.php"; ?>

  <?php if (!empty($rowDetailPhoto)): ?>
    <div class=" mt-4">
      <div class="row-news-photos row">
        <?php foreach ($rowDetailPhoto as $k => $v) { ?>
          <div class="news d-flex flex-wrap col-md-4 col-sm-6 col-12 pb-3" data-aos="fade-up" data-aos-duration="1000">
            <a data-fancybox="gallery" href="<?= UPLOAD_NEWS_L . $v['photo'] ?>">
              <img onerror="this.src='<?= THUMBS ?>/700x700x1/assets/images/noimage.png';" src="<?= THUMBS ?>/700x700x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>">
            </a>
          </div>
        <?php } ?>
      </div>
    </div>
  <?php endif; ?>

</div><!-- /.nd-main -->


<?php if (isset($news) && count($news) > 0) { ?>
  <div class="title-main mt-4" data-aos="fade-in" data-aos-duration="1000"><span>Dự án liên quan</span></div>
  <div class="row-news row">
    <?php foreach ($news as $k => $v) { ?>
      <div class="news d-flex flex-wrap col-md-4 col-sm-6 col-12 pb-3" data-aos="fade-up" data-aos-duration="1000">
        <a class="duan-card duan-card-<?= $k + 1 ?>" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
          <img onerror="this.src='<?= THUMBS ?>/700x700x1/assets/images/noimage.png';" src="<?= THUMBS ?>/700x700x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>">
          <span class="duan-number"><?= str_pad($k + 1, 2, '0', STR_PAD_LEFT) ?></span>
          <span class="duan-card-info">
            <small><?= !empty($v['address' . $lang]) ? strip_tags($v['address' . $lang]) : '' ?></small>
            <strong><?= $v['name' . $lang] ?></strong>
          </span>
        </a>
      </div>
    <?php } ?>
  </div>
<?php } else { ?>
  <div class="alert alert-warning w-100" role="alert">
    <strong><?= khongtimthayketqua ?></strong>
  </div>
<?php } ?>
<div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>