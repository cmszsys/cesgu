<?php
/**
 * 博客首页
 */
require_once __DIR__ . '/includes/common.php';

Plugin::do('before_page_render', 'index');

// 记录访问
track_visit();

$page = input_int('page', 1, 'GET');
$perPage = (int)get_option('posts_per_page', 10);
$db = Database::getInstance();

// 获取文章列表
$offset = ($page - 1) * $perPage;
$orderBy = get_option('post_order', 'created_at');
$validOrders = ['created_at', 'updated_at', 'view_count'];
if (!in_array($orderBy, $validOrders)) $orderBy = 'created_at';

$totalPosts = (int)$db->getValue(
    "SELECT COUNT(*) FROM blog_posts WHERE status = 'publish' AND deleted_at IS NULL AND post_type = 'post'"
);

$posts = $db->getAll(
    "SELECT p.*, u.nickname as author_name
     FROM blog_posts p
     LEFT JOIN blog_users u ON p.author_id = u.user_id
     WHERE p.status = 'publish' AND p.deleted_at IS NULL AND p.post_type = 'post'
     ORDER BY p.is_top DESC, p.{$orderBy} DESC
     LIMIT {$offset}, {$perPage}"
);

// 加载主题
$pageTitle = '';
$theme = get_option('current_theme', 'default');
$themePath = ROOT_PATH . "/themes/{$theme}";

include $themePath . '/header.php';
echo '<main class="site-main">';
echo '<div class="content">';
include $themePath . '/index.php';
echo '</div>';
include $themePath . '/sidebar.php';
echo '</main>';
Plugin::do('after_page_render', 'index');
include $themePath . '/footer.php';