<?php
/**
 * XML Sitemap
 */
require_once __DIR__ . '/includes/common.php';
Plugin::do('before_page_render', 'sitemap');

$db = Database::getInstance();
$siteUrl = rtrim(get_option('site_url', ''), '/');
if (empty($siteUrl)) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $siteUrl = $protocol . '://' . $_SERVER['HTTP_HOST'];
}

$rw = get_option('rewrite_enabled', '0') === '1';

header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= htmlspecialchars($siteUrl) ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <?php
    // 文章
    $posts = $db->getAll(
        "SELECT post_id, slug, updated_at, created_at FROM blog_posts 
         WHERE status = 'publish' AND deleted_at IS NULL AND post_type = 'post'
         ORDER BY created_at DESC LIMIT 5000"
    );
    foreach ($posts as $p):
        $url = post_url($p['post_id'], $p['slug']);
    ?>
    <url>
        <loc><?= htmlspecialchars($url) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($p['updated_at'] ?? $p['created_at'])) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>
    
    <?php
    // 独立页面
    $pages = $db->getAll(
        "SELECT post_id, slug, updated_at, created_at FROM blog_posts 
         WHERE status = 'publish' AND deleted_at IS NULL AND post_type = 'page'
         ORDER BY created_at DESC"
    );
    foreach ($pages as $p):
        $url = ($rw && $p['slug']) ? url('/page/' . $p['slug']) : url('/page.php?id=' . $p['post_id']);
    ?>
    <url>
        <loc><?= htmlspecialchars($url) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($p['updated_at'] ?? $p['created_at'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>
    
    <?php
    // 分类
    $categories = $db->getAll("SELECT cat_id, cat_slug FROM blog_categories WHERE status = 1 AND post_count > 0");
    foreach ($categories as $c):
    ?>
    <url>
        <loc><?= htmlspecialchars(category_url($c['cat_slug'])) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>
    
    <?php
    // 标签
    $tags = $db->getAll("SELECT tag_slug FROM blog_tags WHERE post_count > 0");
    foreach ($tags as $t):
    ?>
    <url>
        <loc><?= htmlspecialchars(tag_url($t['tag_slug'])) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
    <?php endforeach; ?>
    
    <url>
        <loc><?= htmlspecialchars($rw ? url('/archive') : url('/archive.php')) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?= htmlspecialchars($rw ? url('/links') : url('/links.php')) ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>
    <url>
        <loc><?= htmlspecialchars($rw ? url('/tags') : url('/tags.php')) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.4</priority>
    </url>
</urlset>
