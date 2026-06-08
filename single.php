<?php
/**
 * 文章详情页
 */
require_once __DIR__ . '/includes/common.php';

Plugin::do('before_page_render', 'single');

$postId = input_int('id', 0, 'GET');
$slug = input('slug', '', 'GET');

$db = Database::getInstance();

if ($slug) {
    $post = $db->getRow(
        "SELECT p.*, u.nickname as author_name FROM blog_posts p LEFT JOIN blog_users u ON p.author_id = u.user_id WHERE p.slug = ? AND p.status = 'publish' AND p.deleted_at IS NULL",
        [$slug]
    );
} else {
    $post = $db->getRow(
        "SELECT p.*, u.nickname as author_name FROM blog_posts p LEFT JOIN blog_users u ON p.author_id = u.user_id WHERE p.post_id = ? AND p.deleted_at IS NULL",
        [$postId]
    );
}

if (!$post || ($post['status'] !== 'publish' && !Auth::hasRole(['super_admin', 'admin', 'editor']))) {
    http_response_code(404);
    $pageTitle = '页面未找到';
    $theme = get_option('current_theme', 'default');
    include ROOT_PATH . "/themes/{$theme}/header.php";
    echo '<main class="site-main full-width"><div style="text-align:center;padding:100px 0;"><h1 style="font-size:72px;color:#e2e8f0;">404</h1><p style="color:var(--text-light);margin-top:16px;">页面未找到</p><a href="/" style="display:inline-block;margin-top:24px;padding:10px 24px;background:var(--primary);color:#fff;border-radius:8px;">返回首页</a></div></main>';
    include ROOT_PATH . "/themes/{$theme}/footer.php";
    exit;
}

// 密码保护文章
$needPassword = false;
if ($post['visibility'] === 'password' && !empty($post['password'])) {
    $enteredPwd = $_POST['post_password'] ?? $_COOKIE['post_pwd_' . $post['post_id']] ?? '';
    if ($enteredPwd !== $post['password'] && !Auth::hasRole(['super_admin', 'admin', 'editor'])) {
        if (is_post() && !empty($_POST['post_password'])) {
            $pwdError = '密码不正确';
        }
        $needPassword = true;
    } elseif ($enteredPwd === $post['password']) {
        // 记住密码1小时
        setcookie('post_pwd_' . $post['post_id'], $enteredPwd, time() + 3600, '/');
    }
}

// 增加浏览量
increment_view_count($post['post_id']);
track_visit();

$pageTitle = $post['meta_title'] ?: $post['title'];
$pageDescription = $post['meta_description'] ?: $post['excerpt'];
$pageKeywords = $post['meta_keywords'] ?: '';

$theme = get_option('current_theme', 'default');
$themePath = ROOT_PATH . "/themes/{$theme}";

include $themePath . '/header.php';
echo '<main class="site-main">';
echo '<div class="content">';
include $themePath . '/single.php';
echo '</div>';
include $themePath . '/sidebar.php';
echo '</main>';
include $themePath . '/footer.php';
