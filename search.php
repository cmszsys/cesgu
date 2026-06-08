<?php
/**
 * 搜索页
 */
require_once __DIR__ . '/includes/common.php';

Plugin::do('before_page_render', 'search');
track_visit();

$q = input('q', '', 'GET');
$page = input_int('page', 1, 'GET');
$perPage = (int)get_option('posts_per_page', 10);
$db = Database::getInstance();
$totalPosts = 0;
$posts = [];

if (!empty($q)) {
    $offset = ($page - 1) * $perPage;
    
    // 优先使用FULLTEXT搜索（需要MySQL 5.6+）
    $useFulltext = true;
    try {
        $totalPosts = (int)$db->getValue(
            "SELECT COUNT(*) FROM blog_posts WHERE status = 'publish' AND deleted_at IS NULL AND post_type = 'post' AND MATCH(title, content, excerpt) AGAINST(? IN BOOLEAN MODE)",
            [$q . '*']
        );
        $posts = $db->getAll(
            "SELECT p.*, u.nickname as author_name, MATCH(p.title, p.content, p.excerpt) AGAINST(? IN BOOLEAN MODE) as relevance FROM blog_posts p LEFT JOIN blog_users u ON p.author_id = u.user_id WHERE p.status = 'publish' AND p.deleted_at IS NULL AND p.post_type = 'post' AND MATCH(p.title, p.content, p.excerpt) AGAINST(? IN BOOLEAN MODE) ORDER BY relevance DESC, p.created_at DESC LIMIT {$offset}, {$perPage}",
            [$q . '*', $q . '*']
        );
    } catch (Exception $e) {
        $useFulltext = false;
    }
    
    // FULLTEXT不可用时回退到LIKE搜索
    if (!$useFulltext) {
        $totalPosts = (int)$db->getValue(
            "SELECT COUNT(*) FROM blog_posts WHERE status = 'publish' AND deleted_at IS NULL AND post_type = 'post' AND (title LIKE ? OR content LIKE ? OR excerpt LIKE ?)",
            ["%{$q}%", "%{$q}%", "%{$q}%"]
        );
        $posts = $db->getAll(
            "SELECT p.*, u.nickname as author_name FROM blog_posts p LEFT JOIN blog_users u ON p.author_id = u.user_id WHERE p.status = 'publish' AND p.deleted_at IS NULL AND p.post_type = 'post' AND (p.title LIKE ? OR p.content LIKE ? OR p.excerpt LIKE ?) ORDER BY p.created_at DESC LIMIT {$offset}, {$perPage}",
            ["%{$q}%", "%{$q}%", "%{$q}%"]
        );
    }
    
    // 记录搜索日志
    write_log('search', 'frontend', "搜索: {$q} (结果: {$totalPosts})");
}

$pageTitle = '搜索: ' . $q;
$theme = get_option('current_theme', 'default');
$themePath = ROOT_PATH . "/themes/{$theme}";

include $themePath . '/header.php';
echo '<main class="site-main"><div class="content">';
echo '<div style="background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow);padding:24px;margin-bottom:24px;">';
echo '<h1 style="font-size:24px;font-weight:700;"><i class="fas fa-search" style="color:var(--primary);margin-right:8px;"></i>搜索: ' . clean($q) . '</h1>';
echo '<p style="color:var(--text-light);font-size:13px;margin-top:8px;">找到 ' . $totalPosts . ' 个结果</p></div>';
if (empty($q)) {
    echo '<div style="text-align:center;padding:60px;color:var(--text-light);">请输入搜索关键词</div>';
} else {
    include $themePath . '/index.php';
}
echo '</div>';
include $themePath . '/sidebar.php';
echo '</main>';
include $themePath . '/footer.php';
