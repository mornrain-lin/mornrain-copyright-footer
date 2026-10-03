=== MornRain Copyright Footer ===
Contributors: mornrain
Donate link: https://github.com/mornrain-lin
Tags: copyright, footer, notice, settings, post
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Appends a configurable copyright notice to single posts, with a Settings API admin screen.

== Description ==

MornRain Copyright Footer prints a copyright line at the end of your posts and
lets you describe it in your own words.

Everything is configured from *Settings > MornRain Copyright*:

* The notice text, with `{year}`, `{site}` and `{author}` placeholders.
* Which public post types receive the notice.
* Whether `{year}` is recalculated from each post's publication date.
* Whether the notice links back to the home page.

The notice is appended to the post content, never to excerpts, feeds or archive
pages, and it is printed exactly once per post. All settings are validated and
sanitised on save, so only users with the `manage_options` capability can change
them, and every printed value is escaped.

This plugin stores nothing you did not explicitly configure, sends
no data to any remote service, and adds no custom database tables.

== Installation ==

1. Upload the `mornrain-copyright-footer` folder to the `/wp-content/plugins/` directory, or
   install the ZIP through *Plugins > Add New > Upload Plugin*.
2. Activate the plugin through the *Plugins* screen in WordPress.
3. Visit *Settings > MornRain Copyright* and adjust the notice.

== Frequently Asked Questions ==

= Which placeholders are supported? =

`{year}`, `{site}` and `{author}`.

= Can I use HTML in the notice? =

Yes, a safe subset. The value is filtered with `wp_kses_post()` on save, so
scripts and event handlers are stripped.

= How do I remove the notice from a single post? =

Use the `mornrain_copyright_footer_enabled` filter:

    add_filter( 'mornrain_copyright_footer_enabled', function ( $enabled, $post ) {
        return 42 === $post->ID ? false : $enabled;
    }, 10, 2 );

= Can I print the notice in a template? =

Yes:

    echo esc_html( mornrain_copyright_footer_get_html( get_the_ID() ) );

Or simply drop `[mornrain_copyright]` into any content.

= What is removed when I delete the plugin? =

Exactly one option row, `mornrain_copyright_footer_settings`, on every site of
the network. Nothing else is created, so nothing else is removed.

= Is it multisite aware? =

Yes. Activation seeds the default option per site, and uninstall clears every
site in the network.

== Screenshots ==

1. The plugin working on the front end.
2. The relevant WordPress admin screen.

== Changelog ==

= 1.0.0 =
* Initial public release.

== Upgrade Notice ==

= 1.0.0 =
Initial public release.
（内容由AI生成，仅供参考）
