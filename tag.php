<?php
/**
 * 标签文章列表
 */
require_once __DIR__ . '/includes/common.php';

Plugin::do('before_page_render', 'tag');
track_visit();

$tagSlug = input('slug', '', 'GET');
$db = Database::getInstance();
$tag = $db->getRow("SELECT * FROM blog_tags WHERE tag_slug = ?", [$tagSlug]);

if (!$tag) { header('Location: /'); exit; }

$page = input_int('page', 1, 'GET');
$perPage = (int)get_option('posts_per_page', 10);
$offset = ($page - 1) * $perPage;

$totalPosts = (int)$db->getValue(
    "SELECT COUNT(*) FROM blog_posts p INNER JOIN blog_post_tags pt ON p.post_id = pt.post_id WHERE pt.tag_id = ? AND p.status = 'publish' AND p.deleted_at IS NULL",
    [$tag['tag_id']]
);

$posts = $db->getAll(
    "SELECT p.*, u.nickname as author_name FROM blog_posts p INNER JOIN blog_post_tags pt ON p.post_id = pt.post_id LEFT JOIN blog_users u ON p.author_id = u.user_id WHERE pt.tag_id = ? AND p.status = 'publish' AND p.deleted_at IS NULL ORDER BY p.created_at DESC LIMIT {$offset}, {$perPage}",
    [$tag['tag_id']]
);

$pageTitle = '标签: ' . $tag['tag_name'];
$theme = get_option('current_theme', 'default');
$themePath = ROOT_PATH . "/themes/{$theme}";

include $themePath . '/header.php';
echo '<main class="site-main"><div class="content">';
echo '<div style="background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow);padding:24px;margin-bottom:24px;">';
echo '<h1 style="font-size:24px;font-weight:700;"><i class="fas fa-tag" style="color:var(--primary);margin-right:8px;"></i>' . clean($tag['tag_name']) . '</h1>';
echo '<p style="color:var(--text-light);font-size:13px;margin-top:8px;">共 ' . $totalPosts . ' 篇文章</p></div>';
include $themePath . '/index.php';
echo '</div>';
include $themePath . '/sidebar.php';
echo '</main>';
include $themePath . '/footer.php';
