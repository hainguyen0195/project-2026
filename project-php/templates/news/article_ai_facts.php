<?php
$aiArticle = (!empty($aiArticle) && is_array($aiArticle)) ? $aiArticle : array();
$aiArticleEnabled = !empty($config['website']['ai_article']);
$aiArticleVisible = !isset($aiArticle['enabled']) || (string)$aiArticle['enabled'] !== '0';
$aiArticleText = function ($key) use ($aiArticle) {
    $value = isset($aiArticle[$key]) ? (string)$aiArticle[$key] : '';
    return htmlspecialchars(htmlspecialchars_decode($value, ENT_QUOTES), ENT_QUOTES, 'UTF-8');
};
$aiArticleRows = array(
    'main_topic' => 'Chủ đề chính',
    'author_name' => 'Tác giả',
    'author_role' => 'Chuyên môn',
    'audience' => 'Đối tượng đọc',
    'entities' => 'Thực thể liên quan',
    'source_note' => 'Nguồn / xác minh'
);
$aiArticleHasContent = $aiArticleEnabled && $aiArticleVisible && (!empty($aiArticle['summary']) || !empty($aiArticle['key_points']));
foreach ($aiArticleRows as $aiArticleKey => $aiArticleLabel) if ($aiArticleEnabled && $aiArticleVisible && !empty($aiArticle[$aiArticleKey])) $aiArticleHasContent = true;
if ($aiArticleHasContent) {
    $aiArticlePoints = preg_split('/\r?\n/', (string)($aiArticle['key_points'] ?? ''));
    $aiArticlePoints = array_values(array_filter(array_map('trim', $aiArticlePoints)));
?>
<section class="article-ai-facts" aria-label="Thông tin bài viết cho AI">
    <h2 class="article-ai-facts__title">Thông tin bài viết</h2>
    <?php foreach ($aiArticleRows as $aiArticleKey => $aiArticleLabel) { if (!empty($aiArticle[$aiArticleKey])) { ?>
        <div class="article-ai-facts__row"><strong><?= $aiArticleLabel ?></strong><span><?= $aiArticleText($aiArticleKey) ?></span></div>
    <?php } } ?>
    <?php if (!empty($aiArticle['summary'])) { ?><div class="article-ai-facts__summary"><strong>Tóm tắt</strong><p><?= $aiArticleText('summary') ?></p></div><?php } ?>
    <?php if (!empty($aiArticlePoints)) { ?>
        <div class="article-ai-facts__points"><strong>Ý chính</strong><ul><?php foreach ($aiArticlePoints as $aiArticlePoint) { ?><li><?= htmlspecialchars(htmlspecialchars_decode($aiArticlePoint, ENT_QUOTES), ENT_QUOTES, 'UTF-8') ?></li><?php } ?></ul></div>
    <?php } ?>
    <?php if (($aiArticle['fact_checked'] ?? '') === '1') { ?><div class="article-ai-facts__verified"><i class="fas fa-check-circle"></i> Nội dung đã được kiểm chứng</div><?php } ?>
</section>
<?php } ?>
