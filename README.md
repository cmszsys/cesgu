# PHPBlog - PHP+MySQL博客系统

一个基于PHP+MySQL+LayuiAdmin的现代化博客系统，功能完善、界面美观、易于部署。

## 环境要求

- PHP >= 7.4
- MySQL >= 5.7
- Apache（需启用mod_rewrite）或 Nginx
- 必需PHP扩展：PDO MySQL、mbstring、GD、JSON、cURL、FileInfo

## 快速安装

1. 上传所有文件到网站根目录
2. 确保 `uploads/`、`cache/`、`logs/`、`backups/` 目录可写
3. 访问 `http://你的域名/install/index.php`
4. 按向导完成安装配置

## 功能特性

### 内容管理
- 文章管理（草稿、定时发布、密码保护、置顶）
- 无限级分类、标签管理（支持合并）
- Markdown/富文本编辑器
- 文章版本历史、自动保存
- SEO优化（自定义URL、Meta信息）

### 互动管理
- 评论系统（多级嵌套、审核机制）
- 评论频率限制、验证码
- 友情链接管理

### 媒体管理
- 媒体库（图片、视频、文档等）
- 拖拽上传、缩略图自动生成

### 用户系统
- 多角色权限（超级管理员、管理员、编辑、作者、订阅者）
- 登录安全（失败限制、IP锁定、记住登录）

### 系统功能
- 访问统计（PV/UV、设备、浏览器）
- RSS订阅
- 主题系统
- 系统日志
- 维护模式

### 安全特性
- CSRF Token保护
- XSS过滤
- SQL注入防护（PDO预处理）
- 密码哈希加密
- 文件上传安全检查

## 目录结构

```
blog/
├── admin/              # 管理后台
│   ├── controllers/    # API控制器
│   ├── views/          # 后台页面
│   └── static/         # 后台静态资源
├── api/                # 前端API接口
├── assets/             # 公共静态资源
├── cache/              # 缓存目录
├── includes/           # 核心文件
│   ├── classes/        # PHP类
│   ├── common.php      # 公共入口
│   ├── functions.php   # 公共函数
│   └── config.sample.php
├── install/            # 安装向导
├── themes/             # 主题目录
│   └── default/        # 默认主题
├── uploads/            # 上传文件
├── logs/               # 日志
├── backups/            # 备份
└── .htaccess           # URL重写规则
```

## 技术栈

- 后端：PHP 7.4+（原生，无框架依赖）
- 数据库：MySQL 5.7+
- 后台UI：LayuiAdmin 2.9.8
- 图表：ECharts 5
- 图标：Font Awesome 6
- 前端：原生HTML/CSS/JavaScript

## 许可证

MIT License
