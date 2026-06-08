<?php
/**
 * 文章归档 - 时间线样式
 */
require_once __DIR__ . '/includes/common.php';

Plugin::do('before_page_render', 'archive');
track_visit();

$db = Database::getInstance();

$allPosts = $db->getAll(
    "SELECT post_id, title, slug, excerpt, cover_image, view_count, comment_count,
            COALESCE(publish_time, created_at) as pub_date 
     FROM blog_posts 
     WHERE status = 'publish' AND deleted_at IS NULL AND post_type = 'post' 
     ORDER BY pub_date DESC"
);

$grouped = [];
$totalCount = count($allPosts);
foreach ($allPosts as $p) {
    $year = date('Y', strtotime($p['pub_date']));
    $month = date('m', strtotime($p['pub_date']));
    $grouped[$year][$month][] = $p;
}

$pageTitle = '文章归档';
$theme = get_option('current_theme', 'default');
$themePath = ROOT_PATH . "/themes/{$theme}";
include $themePath . '/header.php';
?>
<style>
.archive-link{transition:all .2s;}
.archive-link:hover{transform:translateX(4px);border-color:var(--primary, #2563eb)!important;box-shadow:var(--shadow-lg);}
</style>
<main class="site-main full-width">
    <div style="max-width:860px;margin:0 auto;">
        <div style="background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow);padding:32px 40px;margin-bottom:32px;">
            <h1 style="font-size:28px;font-weight:800;margin-bottom:8px;">
                <i class="fas fa-archive" style="color:var(--primary);margin-right:8px;"></i>文章归档
            </h1>
            <p style="color:var(--text-light);font-size:14px;">
                目前共有 <strong style="color:var(--primary);"><?= $totalCount ?></strong> 篇文章
            </p>
        </div>

        <?php if (empty($grouped)): ?>
        <div style="text-align:center;padding:80px 20px;background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow);color:var(--text-light);">
            <i class="fas fa-feather-alt" style="font-size:48px;color:#d0d0d0;"></i>
            <p style="margin-top:16px;">还没有发布任何文章</p>
        </div>
        <?php else: ?>
        <div style="position:relative;">
            <div style="position:absolute;left:28px;top:0;bottom:0;width:2px;background:linear-gradient(to bottom,var(--primary),var(--border));z-index:0;"></div>

            <?php foreach ($grouped as $year => $months): ?>
            <div style="position:relative;display:flex;align-items:center;margin-bottom:24px;z-index:1;">
                <div style="width:58px;height:58px;border-radius:50%;background:var(--primary, #2563eb);color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;box-shadow:0 4px 12px rgba(var(--primary-rgb, 37,99,235),0.3);flex-shrink:0;">
                    <?= $year ?>
                </div>
                <div style="margin-left:20px;font-size:13px;color:var(--text-light);">
                    <?php $yearCount = 0; foreach ($months as $mp) $yearCount += count($mp); ?>
                    共 <?= $yearCount ?> 篇
                </div>
            </div>

            <?php foreach ($months as $monthNum => $monthPosts): ?>
            <div style="position:relative;margin-left:0;padding-left:72px;margin-bottom:28px;z-index:1;">
                <div style="position:absolute;left:21px;top:6px;width:16px;height:16px;border-radius:50%;background:var(--card-bg);border:3px solid var(--primary);z-index:2;"></div>
                <div style="margin-bottom:12px;">
                    <span style="font-size:16px;font-weight:700;color:var(--text);"><?= (int)$monthNum ?> 月</span>
                    <span style="font-size:13px;color:var(--text-light);margin-left:8px;"><?= count($monthPosts) ?> 篇</span>
                </div>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <?php foreach ($monthPosts as $post): ?>
                    <a href="<?= post_url($post['post_id'], $post['slug']) ?>" class="archive-link"
                       style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:var(--card-bg);border-radius:var(--radius);border:1px solid var(--border);text-decoration:none;box-shadow:var(--shadow);">
                        <?php if ($post['cover_image']): ?>
                        <img src="<?= clean($post['cover_image']) ?>" alt="" loading="lazy"
                             style="width:52px;height:52px;border-radius:6px;object-fit:cover;flex-shrink:0;">
                        <?php endif; ?>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:15px;font-weight:500;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <?= clean($post['title']) ?>
                            </div>
                            <div style="display:flex;gap:12px;margin-top:4px;font-size:12px;color:var(--text-light);">
                                <span><?= date('m月d日', strtotime($post['pub_date'])) ?></span>
                                <?php if ($post['view_count'] > 0): ?>
                                <span><i class="fas fa-eye"></i> <?= format_number($post['view_count']) ?></span>
                                <?php endif; ?>
                                <?php if ($post['comment_count'] > 0): ?>
                                <span><i class="fas fa-comment"></i> <?= $post['comment_count'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right" style="color:var(--text-light);font-size:12px;flex-shrink:0;"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php include $themePath . '/footer.php'; ?>
