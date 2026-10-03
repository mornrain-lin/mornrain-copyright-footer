<?php
/**
 * Main plugin class.
 *
 * @package Mornrain_Copyright_Footer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Mornrain_Copyright_Footer' ) ) :
	/**
	 * Appends the copyright notice and owns the settings screen.
	 *
	 * @since 1.0.0
	 */
	final class Mornrain_Copyright_Footer {

		/**
		 * Settings page slug.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		const PAGE_SLUG = 'mornrain-copyright-footer';

		/**
		 * Shared instance.
		 *
		 * @since 1.0.0
		 * @var Mornrain_Copyright_Footer|null
		 */
		private static $instance = null;

		/**
		 * Posts already annotated in this request.
		 *
		 * @since 1.0.0
		 * @var array<int, bool>
		 */
		private $rendered = array();

		/**
		 * Retrieve the shared instance, creating it on first call.
		 *
		 * @since 1.0.0
		 * @return Mornrain_Copyright_Footer
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Wire up the plugin.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			$this->includes();
			$this->hooks();
		}

		/**
		 * Load module files.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function includes() {
			require_once MORNRAIN_COPYRIGHT_FOOTER_PATH . 'includes/class-mornrain-copyright-footer-settings.php';
			require_once MORNRAIN_COPYRIGHT_FOOTER_PATH . 'includes/class-mornrain-copyright-footer-shortcode.php';
		}

		/**
		 * Register WordPress hooks.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function hooks() {
			add_action( 'init', array( $this, 'load_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_filter( 'the_content', array( $this, 'append_notice' ), 20 );

			$settings = new Mornrain_Copyright_Footer_Settings();
			$settings->hooks();

			$shortcode = new Mornrain_Copyright_Footer_Shortcode();
			$shortcode->hooks();
		}

		/**
		 * Load translations.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain(
				'mornrain-copyright-footer',
				false,
				dirname( plugin_basename( MORNRAIN_COPYRIGHT_FOOTER_FILE ) ) . '/languages'
			);
		}

		/**
		 * Load the front-end stylesheet on singular views.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function enqueue_styles() {
			if ( ! is_singular() ) {
				return;
			}

			wp_enqueue_style(
				'mornrain-copyright-footer',
				MORNRAIN_COPYRIGHT_FOOTER_URL . 'assets/css/copyright-footer.css',
				array(),
				MORNRAIN_COPYRIGHT_FOOTER_VERSION
			);
		}

		/**
		 * Append the notice to the content of the configured post types.
		 *
		 * @since 1.0.0
		 * @param string $content Post content.
		 * @return string
		 */
		public function append_notice( $content ) {
			if ( is_admin() || is_feed() || ! is_singular() || ! in_the_loop() ) {
				return $content;
			}

			$post = get_post();

			if ( ! $post instanceof WP_Post ) {
				return $content;
			}

			$settings = mornrain_copyright_footer_get_settings();
			$types    = (array) $settings['post_types'];

			if ( ! in_array( (string) $post->post_type, $types, true ) ) {
				return $content;
			}

			if ( isset( $this->rendered[ (int) $post->ID ] ) ) {
				return $content;
			}

			$notice = mornrain_copyright_footer_get_html( $post );

			if ( '' === $notice ) {
				return $content;
			}

			$this->rendered[ (int) $post->ID ] = true;

			/**
			 * Filter where the notice is placed relative to the content.
			 *
			 * @since 1.0.0
			 * @param string $position Either 'before' or 'after'.
			 * @param int    $post_id  Related post ID.
			 */
			$position = (string) apply_filters( 'mornrain_copyright_footer_position', 'after', (int) $post->ID );

			if ( 'before' === $position ) {
				return $notice . $content;
			}

			return $content . $notice;
		}

		/**
		 * Seed the default options on activation without clobbering saved ones.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public static function on_activate() {
			if ( false === get_option( MORNRAIN_COPYRIGHT_FOOTER_OPTION, false ) ) {
				add_option( MORNRAIN_COPYRIGHT_FOOTER_OPTION, mornrain_copyright_footer_defaults() );
			}
		}

		/**
		 * Remove every trace of the plugin. Bound to register_uninstall_hook().
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public static function on_uninstall() {
			delete_option( MORNRAIN_COPYRIGHT_FOOTER_OPTION );

			if ( is_multisite() ) {
				$site_ids = get_sites( array( 'fields' => 'ids' ) );

				foreach ( $site_ids as $site_id ) {
					switch_to_blog( (int) $site_id );
					delete_option( MORNRAIN_COPYRIGHT_FOOTER_OPTION );
					restore_current_blog();
				}
			}
		}
	}
endif;
