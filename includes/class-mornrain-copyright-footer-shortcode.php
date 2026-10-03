<?php
/**
 * The [mornrain_copyright] shortcode.
 *
 * @package Mornrain_Copyright_Footer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Mornrain_Copyright_Footer_Shortcode' ) ) :
	/**
	 * Registers the copyright shortcode.
	 *
	 * @since 1.0.0
	 */
	final class Mornrain_Copyright_Footer_Shortcode {

		/**
		 * Register WordPress hooks.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function hooks() {
			add_action( 'init', array( $this, 'register' ) );
		}

		/**
		 * Register the shortcode.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function register() {
			add_shortcode( 'mornrain_copyright', array( $this, 'render' ) );
		}

		/**
		 * Render the shortcode.
		 *
		 * @since 1.0.0
		 * @param array<string, mixed>|string $atts Shortcode attributes.
		 * @return string Escaped HTML.
		 */
		public function render( $atts ) {
			$atts = shortcode_atts(
				array(
					'post_id' => 0,
				),
				$atts,
				'mornrain_copyright'
			);

			$post_id = absint( $atts['post_id'] );

			if ( 0 === $post_id ) {
				$post_id = (int) get_the_ID();
			}

			if ( $post_id <= 0 ) {
				return '';
			}

			return mornrain_copyright_footer_get_html( $post_id );
		}
	}
endif;
