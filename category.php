<?php
/**
 * 分类文章列表
 */
require_once __DIR__ . '/includes/common.php';

Plugin::do('before_page_render', 'category');
track_visit();

$catId = input_int('id', 0, 'GET');
$catSlug = input('slug', '', 'GET');
$db = Database::getInstance();

if ($catSlug) {
    $category = $db->getRow("SELECT * FROM blog_categories WHERE cat_slug = ?", [$catSlug]);
} else {
    $category = $db->getRow("SELECT * FROM blog_categories WHERE cat_id = ?", [$catId]);
}

if (!$category) {
    header('Location: /');
    exit;
}

$page = input_int('page', 1, 'GET');
$perPage = (int)get_option('posts_per_page', 10);
$offset = ($page - 1) * $perPage;

$totalPosts = (int)$db->getValue(
    "SELECT COUNT(*) FROM blog_posts p INNER JOIN blog_post_categories pc ON p.post_id = pc.post_id WHERE pc.cat_id = ? AND p.status = 'publish' AND p.deleted_at IS NULL",
    [$category['cat_id']]
);

$posts = $db->getAll(
    "SELECT p.*, u.nickname as author_name
     FROM blog_posts p
     INNER JOIN blog_post_categories pc ON p.post_id = pc.post_id
     LEFT JOIN blog_users u ON p.author_id = u.user_id
     WHERE pc.cat_id = ? AND p.status = 'publish' AND p.deleted_at IS NULL
     ORDER BY p.is_top DESC, p.created_at DESC
     LIMIT {$offset}, {$perPage}",
    [$category['cat_id']]
);

$pageTitle = $category['meta_title'] ?: $category['cat_name'];
$pageDescription = $category['meta_description'] ?: $category['cat_desc'];

$theme = get_option('current_theme', 'default');
$themePath = ROOT_PATH . "/themes/{$theme}";

include $themePath . '/header.php';
echo '<main class="site-main"><div class="content">';
echo '<div style="background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow);padding:24px;margin-bottom:24px;">';
echo '<h1 style="font-size:24px;font-weight:700;"><i class="fas fa-folder" style="color:var(--primary);margin-right:8px;"></i>' . clean($category['cat_name']) . '</h1>';
if ($category['cat_desc']) {
    echo '<p style="color:var(--text-light);margin-top:8px;font-size:14px;">' . clean($category['cat_desc']) . '</p>';
}
echo '<p style="color:var(--text-light);font-size:13px;margin-top:8px;">共 ' . $totalPosts . ' 篇文章</p>';
echo '</div>';
include $themePath . '/index.php';
echo '</div>';
include $themePath . '/sidebar.php';
echo '</main>';
include $themePath . '/footer.php';
