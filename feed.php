<?php
/**
 * RSS Feed
 */
require_once __DIR__ . '/includes/common.php';
Plugin::do('before_page_render', 'feed');

$db = Database::getInstance();
$siteName = get_option('site_name', '我的博客');
$siteUrl = get_option('site_url', '');
$siteDesc = get_option('site_description', '');
$rssCount = (int)get_option('rss_count', 20);

$posts = $db->getAll(
    "SELECT p.*, u.nickname as author_name 
     FROM blog_posts p 
     LEFT JOIN blog_users u ON p.author_id = u.user_id 
     WHERE p.status = 'publish' AND p.deleted_at IS NULL AND p.post_type = 'post' 
     ORDER BY p.created_at DESC 
     LIMIT {$rssCount}"
);

header('Content-Type: application/rss+xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title><?= htmlspecialchars($siteName) ?></title>
    <link><?= htmlspecialchars($siteUrl) ?></link>
    <description><?= htmlspecialchars($siteDesc) ?></description>
    <language>zh-CN</language>
    <lastBuildDate><?= date('r') ?></lastBuildDate>
    <generator>PHPBlog</generator>
    <atom:link href="<?= htmlspecialchars(url(get_option('rewrite_enabled','0') === '1' ? '/feed' : '/feed.php')) ?>" rel="self" type="application/rss+xml"/>
    
    <?php 
    $rssContent = get_option('rss_content', 'excerpt');
    foreach ($posts as $post): 
        $postCats = get_post_categories($post['post_id']);
    ?>
    <item>
        <title><?= htmlspecialchars($post['title']) ?></title>
        <link><?= htmlspecialchars(post_url($post['post_id'], $post['slug'])) ?></link>
        <guid isPermaLink="true"><?= htmlspecialchars(post_url($post['post_id'], $post['slug'])) ?></guid>
        <?php if ($rssContent === 'full'): ?>
        <description><![CDATA[<?= $post['content'] ?>]]></description>
        <?php else: ?>
        <description><![CDATA[<?= $post['excerpt'] ?: mb_substr(strip_tags($post['content']), 0, 300) ?>]]></description>
        <?php endif; ?>
        <?php if (!empty($post['author_name'])): ?>
        <author><?= htmlspecialchars($post['author_name']) ?></author>
        <?php endif; ?>
        <?php foreach ($postCats as $cat): ?>
        <category><?= htmlspecialchars($cat['cat_name']) ?></category>
        <?php endforeach; ?>
        <pubDate><?= date('r', strtotime($post['publish_time'] ?? $post['created_at'])) ?></pubDate>
    </item>
    <?php endforeach; ?>
</channel>
</rss>
