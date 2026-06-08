<?php
/**
 * 友情链接页面
 */
require_once __DIR__ . '/includes/common.php';
Plugin::do('before_page_render', 'links');
track_visit();

$db = Database::getInstance();

$links = $db->getAll(
    "SELECT * FROM blog_links WHERE is_visible = 1 ORDER BY link_group ASC, sort_order ASC, link_id ASC"
);

$groups = [];
foreach ($links as $link) {
    $group = $link['link_group'] ?: '友情链接';
    $groups[$group][] = $link;
}

$pageTitle = '友情链接';
$theme = get_option('current_theme', 'default');
$themePath = ROOT_PATH . "/themes/{$theme}";
include $themePath . '/header.php';
?>
<style>
.friend-link{transition:all .3s;}
.friend-link:hover{transform:translateY(-2px);box-shadow:var(--shadow-lg);}
</style>
<main class="site-main">
    <div class="content">
        <div style="background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow);padding:32px;margin-bottom:24px;">
            <h1 style="font-size:26px;font-weight:800;margin-bottom:8px;"><i class="fas fa-link" style="color:var(--primary);margin-right:8px;"></i>友情链接</h1>
            <p style="color:var(--text-light);font-size:14px;">与优秀的朋友们一起成长</p>
        </div>

        <?php if (empty($groups)): ?>
        <div style="text-align:center;padding:60px 20px;background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow);color:var(--text-light);">
            <i class="fas fa-link" style="font-size:48px;color:#d0d0d0;"></i>
            <p style="margin-top:16px;">暂无友情链接</p>
        </div>
        <?php else: ?>
            <?php foreach ($groups as $groupName => $groupLinks): ?>
            <div style="margin-bottom:24px;">
                <h2 style="font-size:18px;font-weight:700;color:var(--text);margin:0 0 16px;padding-left:12px;border-left:3px solid var(--primary);">
                    <?= clean($groupName) ?>
                </h2>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;">
                    <?php foreach ($groupLinks as $link): ?>
                    <a href="<?= clean($link['link_url']) ?>" target="_blank" rel="noopener noreferrer" class="friend-link"
                       style="display:flex;align-items:center;gap:12px;padding:16px;background:var(--card-bg);border-radius:var(--radius);border:1px solid var(--border);text-decoration:none;box-shadow:var(--shadow);">
                        <?php if ($link['link_logo']): ?>
                        <img src="<?= clean($link['link_logo']) ?>" alt="<?= clean($link['link_name']) ?>"
                             style="width:48px;height:48px;border-radius:8px;object-fit:cover;flex-shrink:0;">
                        <?php else: ?>
                        <div style="width:48px;height:48px;border-radius:8px;background:linear-gradient(135deg,var(--primary, #2563eb),#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;font-weight:700;flex-shrink:0;">
                            <?= mb_substr($link['link_name'], 0, 1) ?>
                        </div>
                        <?php endif; ?>
                        <div style="min-width:0;">
                            <div style="font-size:15px;font-weight:600;color:var(--text);margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <?= clean($link['link_name']) ?>
                            </div>
                            <?php if ($link['link_desc']): ?>
                            <div style="font-size:13px;color:var(--text-light);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <?= clean($link['link_desc']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- 申请友链 -->
        <div style="margin-top:24px;padding:28px;background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow);">
            <h3 style="font-size:16px;font-weight:700;margin-bottom:12px;"><i class="fas fa-handshake" style="color:var(--primary);margin-right:6px;"></i>申请友链</h3>
            <p style="color:var(--text-light);font-size:14px;line-height:1.8;">
                如果您希望与本站交换友情链接，请确保您的网站内容健康、有原创内容，并已添加本站链接。
                然后通过评论或邮件联系站长，注明：站点名称、URL、Logo地址、一句话描述。
            </p>
        </div>
    </div>
    <?php include $themePath . '/sidebar.php'; ?>
</main>
<?php include $themePath . '/footer.php'; ?>
