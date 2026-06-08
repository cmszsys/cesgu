<?php
/**
 * 标签列表页
 */
require_once __DIR__ . '/includes/common.php';
Plugin::do('before_page_render', 'tags');
track_visit();

$db = Database::getInstance();
$tags = $db->getAll(
    "SELECT t.*, 
     (SELECT COUNT(*) FROM blog_post_tags pt INNER JOIN blog_posts p ON pt.post_id = p.post_id 
      WHERE pt.tag_id = t.tag_id AND p.status = 'publish' AND p.deleted_at IS NULL) as real_count
     FROM blog_tags t HAVING real_count > 0 ORDER BY real_count DESC"
);

$maxCount = 0;
foreach ($tags as $t) $maxCount = max($maxCount, $t['real_count']);

$pageTitle = '全部标签';
$theme = get_option('current_theme', 'default');
$themePath = ROOT_PATH . "/themes/{$theme}";
include $themePath . '/header.php';
?>
<style>
.tag-cloud-item{transition:all .2s;}
.tag-cloud-item:hover{border-color:var(--primary, #2563eb)!important;color:var(--primary, #2563eb)!important;}
</style>
<main class="site-main full-width">
    <div style="max-width:900px;margin:0 auto;">
        <div style="background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow);padding:40px;">
            <h1 style="font-size:28px;font-weight:800;margin-bottom:8px;">
                <i class="fas fa-tags" style="color:var(--primary);margin-right:8px;"></i>全部标签
            </h1>
            <p style="color:var(--text-light);font-size:14px;margin-bottom:32px;">共 <?= count($tags) ?> 个标签</p>
            
            <?php if ($tags): ?>
            <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;">
                <?php foreach ($tags as $tag): 
                    $ratio = $maxCount > 0 ? $tag['real_count'] / $maxCount : 0;
                    $fontSize = 14 + round($ratio * 18);
                    $opacity = 0.5 + $ratio * 0.5;
                ?>
                <a href="<?= tag_url($tag['tag_slug']) ?>" class="tag-cloud-item"
                   style="display:inline-flex;align-items:center;gap:6px;padding:6px 18px;background:rgba(var(--primary-rgb, 37,99,235),<?= round($ratio * 0.15, 2) ?>);color:var(--text);border-radius:20px;font-size:<?= $fontSize ?>px;border:1px solid transparent;">
                    <?= clean($tag['tag_name']) ?>
                    <span style="font-size:12px;color:var(--text-light);"><?= $tag['real_count'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="text-align:center;color:var(--text-light);padding:40px;">暂无标签</p>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php include $themePath . '/footer.php'; ?>
