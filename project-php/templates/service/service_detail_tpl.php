<?php
// Lấy tác giả nếu có
$authorName = !empty($rowDetail['author']) ? $rowDetail['author'] : (!empty($setting['namevi']) ? $setting['namevi'] : '');
?>
<div class="nd-layout">

  <!-- ===== CỘT TRÁI: NỘI DUNG BÀI VIẾT ===== -->
  <div class="nd-main">

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

  </div><!-- /.nd-main -->

  <!-- ===== CỘT PHẢI: SIDEBAR ===== -->
  <aside class="nd-sidebar">

    <!-- Tiêu đề sidebar -->
    <div class="nd-sidebar-title">
      <i class="bi bi-journal-richtext"></i> <?= baivietkhac ?>
    </div>

    <?php if (!empty($news)): ?>
      <div class="nd-sidebar-list">
        <?php foreach ($news as $k => $v): ?>
          <a class="nd-sidebar-card" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
            <div class="nd-sidebar-card-thumb">
              <img class="lazy w-100 h-100"
                   onerror="this.src='<?= THUMBS ?>/300x200x1/assets/images/noimage.png';"
                   data-src="<?= THUMBS ?>/300x200x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>"
                   alt="<?= $v['name' . $lang] ?>"
                   title="<?= $v['name' . $lang] ?>" />
            </div>
            <div class="nd-sidebar-card-info">
              <div class="nd-sidebar-card-name"><?= $v['name' . $lang] ?></div>
              <div class="nd-sidebar-card-date"><?= date("d/m/Y H:i", $v['date_created']) ?></div>
              <div class="nd-sidebar-card-desc"><?= $v['desc' . $lang] ?></div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      <?php if (!empty($paging)): ?>
        <div class="nd-sidebar-paging"><?= $paging ?></div>
      <?php endif; ?>
    <?php else: ?>
      <p class="nd-sidebar-empty"><i class="bi bi-inbox"></i> <?= khongtimthayketqua ?></p>
    <?php endif; ?>

  </aside><!-- /.nd-sidebar -->

</div><!-- /.nd-layout -->