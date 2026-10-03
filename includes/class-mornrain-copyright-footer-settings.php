<?php
/**
 * Settings screen built on the WordPress Settings API.
 *
 * @package Mornrain_Copyright_Footer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Mornrain_Copyright_Footer_Settings' ) ) :
	/**
	 * Registers the settings page, sections and fields.
	 *
	 * @since 1.0.0
	 */
	final class Mornrain_Copyright_Footer_Settings {

		/**
		 * Option group name used by settings_fields().
		 *
		 * @since 1.0.0
		 * @var string
		 */
		const GROUP = 'mornrain_copyright_footer_group';

		/**
		 * Register WordPress hooks.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function hooks() {
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( MORNRAIN_COPYRIGHT_FOOTER_FILE ), array( $this, 'action_links' ) );
		}

		/**
		 * Add the settings entry under Settings.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function add_menu() {
			add_options_page(
				__( 'MornRain Copyright Footer', 'mornrain-copyright-footer' ),
				__( 'MornRain Copyright', 'mornrain-copyright-footer' ),
				'manage_options',
				Mornrain_Copyright_Footer::PAGE_SLUG,
				array( $this, 'render_page' )
			);
		}

		/**
		 * Register the option, section and fields.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function register_settings() {
			register_setting(
				self::GROUP,
				MORNRAIN_COPYRIGHT_FOOTER_OPTION,
				array(
					'type'              => 'array',
					'sanitize_callback' => 'mornrain_copyright_footer_sanitize',
					'default'           => mornrain_copyright_footer_defaults(),
					'show_in_rest'      => false,
				)
			);

			add_settings_section(
				'mornrain_copyright_footer_main',
				__( 'Notice content', 'mornrain-copyright-footer' ),
				array( $this, 'render_section_intro' ),
				Mornrain_Copyright_Footer::PAGE_SLUG
			);

			add_settings_field(
				'mornrain_copyright_footer_template',
				__( 'Notice template', 'mornrain-copyright-footer' ),
				array( $this, 'render_template_field' ),
				Mornrain_Copyright_Footer::PAGE_SLUG,
				'mornrain_copyright_footer_main',
				array( 'label_for' => 'mornrain-copyright-footer-template' )
			);

			add_settings_field(
				'mornrain_copyright_footer_post_types',
				__( 'Post types', 'mornrain-copyright-footer' ),
				array( $this, 'render_post_types_field' ),
				Mornrain_Copyright_Footer::PAGE_SLUG,
				'mornrain_copyright_footer_main'
			);

			add_settings_field(
				'mornrain_copyright_footer_show_year',
				__( 'Update the year automatically', 'mornrain-copyright-footer' ),
				array( $this, 'render_show_year_field' ),
				Mornrain_Copyright_Footer::PAGE_SLUG,
				'mornrain_copyright_footer_main'
			);

			add_settings_field(
				'mornrain_copyright_footer_link_home',
				__( 'Link the notice to the home page', 'mornrain-copyright-footer' ),
				array( $this, 'render_link_home_field' ),
				Mornrain_Copyright_Footer::PAGE_SLUG,
				'mornrain_copyright_footer_main'
			);
		}

		/**
		 * Print the section description.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function render_section_intro() {
			echo '<p>';
			esc_html_e( 'The notice is appended to the content of the selected post types. Available placeholders: {year}, {site}, {author}.', 'mornrain-copyright-footer' );
			echo '</p>';
		}

		/**
		 * Print the template field.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function render_template_field() {
			$settings = mornrain_copyright_footer_get_settings();
			?>
			<input
				type="text"
				id="mornrain-copyright-footer-template"
				class="regular-text"
				name="<?php echo esc_attr( MORNRAIN_COPYRIGHT_FOOTER_OPTION ); ?>[template]"
				value="<?php echo esc_attr( (string) $settings['template'] ); ?>"
			/>
			<p class="description">
				<?php esc_html_e( 'Placeholders: {year}, {site}, {author}. Basic HTML such as &lt;strong&gt; is allowed.', 'mornrain-copyright-footer' ); ?>
			</p>
			<?php
		}

		/**
		 * Print the post type checkboxes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function render_post_types_field() {
			$settings  = mornrain_copyright_footer_get_settings();
			$selected  = array_map( 'strval', (array) $settings['post_types'] );
			$post_types = get_post_types( array( 'public' => true ), 'objects' );

			echo '<fieldset>';

			foreach ( $post_types as $type ) {
				if ( 'attachment' === $type->name ) {
					continue;
				}

				printf(
					'<label style="display:block;margin-bottom:4px;"><input type="checkbox" name="%1$s[post_types][]" value="%2$s"%3$s /> %4$s</label>',
					esc_attr( MORNRAIN_COPYRIGHT_FOOTER_OPTION ),
					esc_attr( $type->name ),
					checked( in_array( (string) $type->name, $selected, true ), true, false ),
					esc_html( $type->labels->singular_name )
				);
			}

			echo '</fieldset>';
			echo '<p class="description">';
			esc_html_e( 'Only public post types are listed. Leave everything unchecked to disable the front-end notice.', 'mornrain-copyright-footer' );
			echo '</p>';
		}

		/**
		 * Print the automatic year checkbox.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function render_show_year_field() {
			$settings = mornrain_copyright_footer_get_settings();
			?>
			<label>
				<input
					type="checkbox"
					name="<?php echo esc_attr( MORNRAIN_COPYRIGHT_FOOTER_OPTION ); ?>[show_year]"
					value="1"
					<?php checked( 1, (int) $settings['show_year'] ); ?>
				/>
				<?php esc_html_e( 'Recompute {year} from the publication date of each post.', 'mornrain-copyright-footer' ); ?>
			</label>
			<?php
		}

		/**
		 * Print the home link checkbox.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function render_link_home_field() {
			$settings = mornrain_copyright_footer_get_settings();
			?>
			<label>
				<input
					type="checkbox"
					name="<?php echo esc_attr( MORNRAIN_COPYRIGHT_FOOTER_OPTION ); ?>[link_home]"
					value="1"
					<?php checked( 1, (int) $settings['link_home'] ); ?>
				/>
				<?php esc_html_e( 'Wrap the notice text in a link to the site home page.', 'mornrain-copyright-footer' ); ?>
			</label>
			<?php
		}

		/**
		 * Render the settings page. Capability is enforced by the admin menu.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function render_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'mornrain-copyright-footer' ) );
			}
			?>
			<div class="wrap">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<form action="options.php" method="post">
					<?php
					settings_fields( self::GROUP );
					do_settings_sections( Mornrain_Copyright_Footer::PAGE_SLUG );
					submit_button();
					?>
				</form>
				<p class="description">
					<?php esc_html_e( 'The same notice can be printed anywhere with the [mornrain_copyright] shortcode.', 'mornrain-copyright-footer' ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Load the small admin stylesheet on this screen only.
		 *
		 * @since 1.0.0
		 * @param string $hook_suffix Current admin page hook.
		 * @return void
		 */
		public function enqueue_admin_styles( $hook_suffix ) {
			if ( 'settings_page_' . Mornrain_Copyright_Footer::PAGE_SLUG !== $hook_suffix ) {
				return;
			}

			wp_enqueue_style(
				'mornrain-copyright-footer-admin',
				MORNRAIN_COPYRIGHT_FOOTER_URL . 'assets/css/admin.css',
				array(),
				MORNRAIN_COPYRIGHT_FOOTER_VERSION
			);
		}

		/**
		 * Add a settings shortcut on the Plugins screen.
		 *
		 * @since 1.0.0
		 * @param array<int, string> $links Existing action links.
		 * @return array<int, string>
		 */
		public function action_links( $links ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return $links;
			}

			$url = add_query_arg(
				'page',
				Mornrain_Copyright_Footer::PAGE_SLUG,
				admin_url( 'options-general.php' )
			);

			array_unshift(
				$links,
				sprintf( '<a href="%1$s">%2$s</a>', esc_url( $url ), esc_html__( 'Settings', 'mornrain-copyright-footer' ) )
			);

			return $links;
		}
	}
endif;
