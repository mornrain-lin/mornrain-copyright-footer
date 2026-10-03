<?php
/**
 * Plugin Name: MornRain Copyright Footer
 * Plugin URI: https://github.com/mornrain-lin/mornrain-copyright-footer
 * Description: Appends a configurable copyright notice to the end of single posts, with a Settings API admin screen.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: MornRain
 * Author URI: https://github.com/mornrain-lin
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mornrain-copyright-footer
 * Domain Path: /languages
 *
 * @package Mornrain_Copyright_Footer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'MORNRAIN_COPYRIGHT_FOOTER_VERSION', '1.0.0' );
define( 'MORNRAIN_COPYRIGHT_FOOTER_FILE', __FILE__ );
define( 'MORNRAIN_COPYRIGHT_FOOTER_PATH', plugin_dir_path( __FILE__ ) );
define( 'MORNRAIN_COPYRIGHT_FOOTER_URL', plugin_dir_url( __FILE__ ) );
define( 'MORNRAIN_COPYRIGHT_FOOTER_OPTION', 'mornrain_copyright_footer_settings' );

require_once MORNRAIN_COPYRIGHT_FOOTER_PATH . 'includes/functions-copyright-footer.php';
require_once MORNRAIN_COPYRIGHT_FOOTER_PATH . 'includes/class-mornrain-copyright-footer.php';

if ( ! function_exists( 'mornrain_copyright_footer' ) ) :
	/**
	 * Return the shared plugin instance.
	 *
	 * @since 1.0.0
	 * @return Mornrain_Copyright_Footer
	 */
	function mornrain_copyright_footer() {
		return Mornrain_Copyright_Footer::instance();
	}
endif;

register_activation_hook( __FILE__, array( 'Mornrain_Copyright_Footer', 'on_activate' ) );
register_uninstall_hook( __FILE__, array( 'Mornrain_Copyright_Footer', 'on_uninstall' ) );

mornrain_copyright_footer();
