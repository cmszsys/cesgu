<?php
/**
 * 独立页面
 */
require_once __DIR__ . '/includes/common.php';

Plugin::do('before_page_render', 'page');
track_visit();

$slug = input('slug', '', 'GET');
$pageId = input_int('id', 0, 'GET');
$db = Database::getInstance();

if ($slug) {
    $page = $db->getRow(
        "SELECT p.*, u.nickname as author_name FROM blog_posts p LEFT JOIN blog_users u ON p.author_id = u.user_id WHERE p.slug = ? AND p.post_type = 'page' AND p.status = 'publish' AND p.deleted_at IS NULL",
        [$slug]
    );
} else {
    $page = $db->getRow(
        "SELECT p.*, u.nickname as author_name FROM blog_posts p LEFT JOIN blog_users u ON p.author_id = u.user_id WHERE p.post_id = ? AND p.post_type = 'page' AND p.status = 'publish' AND p.deleted_at IS NULL",
        [$pageId]
    );
}

if (!$page) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

increment_view_count($page['post_id']);

$pageTitle = $page['meta_title'] ?: $page['title'];
$pageDescription = $page['meta_description'] ?: (strip_tags(mb_substr($page['content'], 0, 150)));
$pageKeywords = $page['meta_keywords'] ?? '';

$theme = get_option('current_theme', 'default');
$themePath = ROOT_PATH . "/themes/{$theme}";

include $themePath . '/header.php';
?>
<main class="site-main full-width">
    <div style="background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow);padding:48px;">
        <h1 style="font-size:30px;font-weight:800;margin-bottom:24px;"><?= clean($page['title']) ?></h1>
        
        <?php if (!empty($page['cover_image'])): ?>
        <img src="<?= clean($page['cover_image']) ?>" alt="<?= clean($page['title']) ?>" style="width:100%;border-radius:10px;margin-bottom:32px;max-height:400px;object-fit:cover;">
        <?php endif; ?>
        
        <div class="post-body">
            <?= $page['content'] ?>
        </div>
        
        <div style="margin-top:32px;padding-top:20px;border-top:1px solid var(--border);font-size:13px;color:var(--text-light);">
            最后更新：<?= date('Y-m-d', strtotime($page['updated_at'] ?? $page['created_at'])) ?>
        </div>
    </div>
    
    <?php
    $post = $page; // 复用评论组件
    $commentResult = get_comments($page['post_id']);
    $comments = $commentResult['data'] ?? [];
    $commentCount = (int)$page['comment_count'];
    require $themePath . '/comments.php';
    ?>
</main>
<?php include $themePath . '/footer.php'; ?>
