<?php
/**
 * 404 页面
 */
require_once __DIR__ . '/includes/common.php';

$pageTitle = '页面未找到';
$theme = get_option('current_theme', 'default');
$themePath = ROOT_PATH . "/themes/{$theme}";

http_response_code(404);
include $themePath . '/header.php';
?>
<main class="site-main full-width">
    <div style="text-align:center;padding:80px 20px;background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow);">
        <div style="font-size:120px;font-weight:900;color:var(--border);line-height:1;">404</div>
        <h1 style="font-size:24px;font-weight:700;margin:20px 0 12px;color:var(--text);">页面未找到</h1>
        <p style="color:var(--text-light);font-size:16px;margin-bottom:32px;">您访问的页面不存在或已被删除</p>
        
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="<?= url('/') ?>" style="display:inline-flex;align-items:center;gap:6px;background:var(--primary);color:#fff;padding:12px 28px;border-radius:8px;font-weight:600;transition:all .2s;">
                <i class="fas fa-home"></i> 返回首页
            </a>
            <a href="javascript:history.back()" style="display:inline-flex;align-items:center;gap:6px;background:var(--bg);color:var(--text);padding:12px 28px;border-radius:8px;font-weight:600;transition:all .2s;">
                <i class="fas fa-arrow-left"></i> 返回上页
            </a>
        </div>
        
        <div style="margin-top:40px;">
            <form action="<?= url('/search.php') ?>" method="get" style="display:flex;gap:8px;max-width:400px;margin:0 auto;">
                <input type="text" name="q" placeholder="试试搜索..." style="flex:1;border:1px solid var(--border);border-radius:8px;padding:10px 16px;font-size:14px;outline:none;">
                <button type="submit" style="background:var(--primary);color:#fff;border:none;border-radius:8px;padding:10px 20px;cursor:pointer;">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</main>
<?php include $themePath . '/footer.php'; ?>
