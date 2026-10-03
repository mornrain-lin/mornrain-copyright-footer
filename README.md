---
AIGC:
    Label: "1"
    ContentProducer: 001191440300708461136T1XGW3
    ProduceID: cf93d2ba4252e3fc820ac383cb09c649_7e089878be7a11f18019525400248c00
    ReservedCode1: tfH1tNc5RAKd47xnDSaVkEFJmuu6iwYI27WxxKeHusnLNyzXS/1hACG/wVEzYSDTdjYVGaZ4fnZYTPZS08wrvSFubx+LUxnrV33l2uT9ZVMJrkWZO26o5oCFo3fdfZQ01knEymUu6S6c2sy0cjBAsTBh7J/3mVGp62rjUrdwQxsxfPMicu1zBLNfxTw=
    ContentPropagator: 001191440300708461136T1XGW3
    PropagateID: cf93d2ba4252e3fc820ac383cb09c649_7e089878be7a11f18019525400248c00
    ReservedCode2: tfH1tNc5RAKd47xnDSaVkEFJmuu6iwYI27WxxKeHusnLNyzXS/1hACG/wVEzYSDTdjYVGaZ4fnZYTPZS08wrvSFubx+LUxnrV33l2uT9ZVMJrkWZO26o5oCFo3fdfZQ01knEymUu6S6c2sy0cjBAsTBh7J/3mVGp62rjUrdwQxsxfPMicu1zBLNfxTw=
---

# MornRain Copyright Footer

> A configurable copyright notice appended to your posts, driven by the Settings API.

`MornRain Copyright Footer` is a lightweight, self-contained WordPress plugin by **MornRain**.
It makes **no outbound network requests**, loads **no external CDN assets**,
creates **no custom database tables** and touches **no user data** beyond what
the site owner explicitly configures.

| Item | Value |
| --- | --- |
| License | GPL v2 or later |
| Minimum WordPress | 6.0 |
| Minimum PHP | 8.0 |
| Text domain | `mornrain-copyright-footer` |
| Function prefix | `mornrain_copyright_footer_*` |
| Class prefix | `Mornrain_Copyright_Footer` |

---

## Table of contents

1. [Features](#features)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Hooks reference](#hooks-reference)
5. [File structure](#file-structure)
6. [Development and quality checks](#development-and-quality-checks)
7. [Frequently asked questions](#frequently-asked-questions)
8. [Changelog](#changelog)
9. [License](#license)

---

## Features

- Appends a copyright notice to the content of selected public post types.
- Text template with `{year}`, `{site}` and `{author}` placeholders.
- Full admin screen built on the WordPress Settings API, under **Settings**.
- Capability checked with `manage_options`; every field validated on save with
  `wp_kses_post()`, `sanitize_key()` and explicit allow-lists.
- Optional home page link and per-post publication year.
- Provides the `[mornrain_copyright]` shortcode for arbitrary placement.
- Removes exactly one option row on uninstall, across a multisite network.

---

## Installation

### Option A - Install from the WordPress admin (recommended)

1. Download or clone this repository.
2. Compress the `mornrain-copyright-footer` folder itself into `mornrain-copyright-footer.zip`. The archive must
   contain the plugin folder, not the repository root.
3. Go to **Plugins > Add New > Upload Plugin**, choose the ZIP, click
   **Install Now**, then **Activate**.

### Option B - Copy the folder over FTP / SSH

1. Copy the whole `mornrain-copyright-footer` folder into `wp-content/plugins/`.
2. Go to **Plugins** and activate `MornRain Copyright Footer`.

### Option C - Git clone (developer workflow)

```bash
cd wp-content/plugins
git clone https://github.com/mornrain-lin/mornrain-copyright-footer.git
```

---

## Configuration

Open **Settings > MornRain Copyright**.

| Field | Default | Description |
| --- | --- | --- |
| Notice template | `(c) {year} {site}. All rights reserved.` | The visible text. |
| Post types | `post` | Which public post types receive the notice. |
| Update the year automatically | on | Recompute `{year}` from each post's publication date. |
| Link the notice to the home page | on | Wrap the text in a link to `home_url( '/' )`. |

The settings screen is restricted to users with `manage_options`:

```php
add_options_page(
    __( 'MornRain Copyright Footer', 'mornrain-copyright-footer' ),
    __( 'MornRain Copyright', 'mornrain-copyright-footer' ),
    'manage_options', // capability required to see and save the screen.
    Mornrain_Copyright_Footer::PAGE_SLUG,
    array( $this, 'render_page' )
);
```

---

## Hooks reference

| Hook | Type | Purpose |
| --- | --- | --- |
| `mornrain_copyright_footer_settings` | filter | Override the effective settings array. |
| `mornrain_copyright_footer_enabled` | filter | `bool $enabled, WP_Post $post` - suppress per post. |
| `mornrain_copyright_footer_text` | filter | `string $text, WP_Post $post` - change the text. |
| `mornrain_copyright_footer_placeholders` | filter | `array $values, WP_Post $post` - add placeholders. |
| `mornrain_copyright_footer_html` | filter | `string $html, WP_Post $post` - replace the markup. |
| `mornrain_copyright_footer_position` | filter | `string $position, int $post_id` - `after` or `before`. |

Public helper functions:

| Function | Returns |
| --- | --- |
| `mornrain_copyright_footer_get_settings()` | `array<string, mixed>` |
| `mornrain_copyright_footer_get_html( $post )` | `string` (escaped HTML) |
| `mornrain_copyright_footer_sanitize( $input )` | Sanitised settings array |
| `mornrain_copyright_footer_placeholders( $post )` | `array<string, string>` |

Shortcode: `[mornrain_copyright]`, or `[mornrain_copyright post_id="12"]`.

---

## File structure

```text
mornrain-copyright-footer/                              # MornRain Copyright Footer 插件根目录：可配置版权声明
|-- .github/                                            # GitHub 仓库配置目录
|   `-- workflows/                                      # GitHub Actions 工作流目录
|       `-- build.yml                                   # CI 工作流：在 PHP 8.1–8.3 上 lint、跑 PHPUnit 并打包 ZIP 构件
|-- assets/                                             # 前端静态资源目录
|   `-- css/                                            # 样式资源目录
|       |-- admin.css                                   # 后台设置页样式
|       `-- copyright-footer.css                        # 前台版权声明样式
|-- includes/                                           # 插件 PHP 源码目录
|   |-- class-mornrain-copyright-footer-settings.php    # 设置类：基于 Settings API 构建后台设置页与字段校验
|   |-- class-mornrain-copyright-footer-shortcode.php   # 短码类：实现 [mornrain_copyright] 短码
|   |-- class-mornrain-copyright-footer.php             # 主类：注册钩子并向选定文章类型内容追加版权声明
|   `-- functions-copyright-footer.php                  # 辅助函数：占位符替换、设置读取与声明 HTML 生成
|-- tests/                                              # PHPUnit 测试目录
|   |-- ScaffoldTest.php                                # 脚手架冒烟测试：断言 README、LICENSE、composer.json 存在
|   `-- bootstrap.php                                   # PHPUnit 引导文件：存在时才加载 Composer 自动加载器
|-- mornrain-copyright-footer.php                       # 插件入口：声明插件头并加载 includes
|-- composer.json                                       # Composer 元数据与 lint/test 脚本
|-- LICENSE                                             # GPL-2.0-or-later 许可证全文
|-- phpunit.xml.dist                                    # PHPUnit 配置，扫描 tests 目录
|-- README.md                                           # 插件说明文档
|-- readme.txt                                          # WordPress 插件目录要求的 readme.txt
`-- uninstall.php                                       # 卸载脚本：删除设置选项（含多站点）
```

---

## Development and quality checks

```bash
composer install
composer validate
composer lint   # runs php -l over every PHP file
composer test   # runs PHPUnit
```

Coding style follows the
[WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/):
tab indentation, Yoda conditions, prefixed global functions, nonce and
capability checks on every write path, and escaped output everywhere.

Continuous integration lives in `.github/workflows/build.yml`. It runs on every
push and pull request across PHP 8.1, 8.2 and 8.3: `composer install`,
`php -l` linting, PHPUnit, and finally packages a release ZIP as a build
artifact.

---

## Frequently asked questions

### Which placeholders can I use?

`{year}`, `{site}` and `{author}`. They are replaced through `strtr()`, which
never re-scans the inserted values, so a site name containing braces is safe.

### Can I use HTML in the notice?

Yes, a safe subset. The template is stored with `wp_kses_post()` so scripts and
inline event handlers are stripped before the value reaches the database.

### How do I hide the notice on one post?

Return `false` from the `mornrain_copyright_footer_enabled` filter for that post
ID.

### Can I print the notice inside a template?

Yes. Call `mornrain_copyright_footer_get_html( get_the_ID() )` and echo it, or
place `[mornrain_copyright]` in the content.

### Who is allowed to change the settings?

Only users with the `manage_options` capability. The plugin also re-checks the
capability in the page callback before rendering the form.

### What happens when I delete the plugin?

`uninstall.php` deletes the single option row on every site of a multisite
network. The plugin creates no custom tables, no transients and no user meta.

---

## Changelog

### 1.0.0

- Initial public release.

---

## License

Released under the **GNU General Public License v2 or later**. See
[LICENSE](LICENSE) for the full text.
