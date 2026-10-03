<?php
/**
 * Settings helpers for MornRain Copyright Footer.
 *
 * @package Mornrain_Copyright_Footer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mornrain_copyright_footer_defaults' ) ) :
	/**
	 * Default settings.
	 *
	 * @since 1.0.0
	 * @return array<string, mixed>
	 */
	function mornrain_copyright_footer_defaults() {
		return array(
			'template'      => '(c) {year} {site}. All rights reserved.',
			'post_types'    => array( 'post' ),
			'show_year'     => 1,
			'link_home'     => 1,
		);
	}
endif;

if ( ! function_exists( 'mornrain_copyright_footer_get_settings' ) ) :
	/**
	 * Read the stored settings merged over the defaults.
	 *
	 * @since 1.0.0
	 * @return array<string, mixed>
	 */
	function mornrain_copyright_footer_get_settings() {
		$stored = get_option( MORNRAIN_COPYRIGHT_FOOTER_OPTION, array() );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$settings = array_merge( mornrain_copyright_footer_defaults(), $stored );

		/**
		 * Filter the effective settings.
		 *
		 * @since 1.0.0
		 * @param array<string, mixed> $settings Effective settings.
		 */
		return (array) apply_filters( 'mornrain_copyright_footer_settings', $settings );
	}
endif;

if ( ! function_exists( 'mornrain_copyright_footer_sanitize' ) ) :
	/**
	 * Sanitise the submitted settings array.
	 *
	 * Never trusts the caller: every key is validated here, so the Settings API
	 * callback can stay thin.
	 *
	 * @since 1.0.0
	 * @param mixed $input Raw submitted value.
	 * @return array<string, mixed>
	 */
	function mornrain_copyright_footer_sanitize( $input ) {
		$defaults = mornrain_copyright_footer_defaults();
		$clean    = $defaults;

		if ( ! is_array( $input ) ) {
			return $clean;
		}

		if ( isset( $input['template'] ) ) {
			$template        = wp_kses_post( (string) $input['template'] );
			$clean['template'] = '' === trim( $template ) ? $defaults['template'] : $template;
		}

		$allowed_types = get_post_types( array( 'public' => true ), 'names' );
		$submitted     = array();

		if ( isset( $input['post_types'] ) && is_array( $input['post_types'] ) ) {
			foreach ( $input['post_types'] as $type ) {
				$type = sanitize_key( (string) $type );

				if ( in_array( $type, $allowed_types, true ) ) {
					$submitted[] = $type;
				}
			}
		}

		$clean['post_types'] = array_values( array_unique( $submitted ) );
		$clean['show_year']  = empty( $input['show_year'] ) ? 0 : 1;
		$clean['link_home']  = empty( $input['link_home'] ) ? 0 : 1;

		return $clean;
	}
endif;

if ( ! function_exists( 'mornrain_copyright_footer_placeholders' ) ) :
	/**
	 * Map every supported placeholder to its replacement value.
	 *
	 * @since 1.0.0
	 * @param int|WP_Post|null $post Post the notice belongs to.
	 * @return array<string, string>
	 */
	function mornrain_copyright_footer_placeholders( $post = null ) {
		$post = get_post( $post );
		$site = wp_specialchars_decode( (string) get_bloginfo( 'name' ), ENT_QUOTES );

		$settings = mornrain_copyright_footer_get_settings();
		$year     = (string) gmdate( 'Y' );

		if ( ! empty( $settings['show_year'] ) && $post instanceof WP_Post && '' !== $post->post_date_gmt && '0000-00-00 00:00:00' !== $post->post_date_gmt ) {
			$stamp = strtotime( $post->post_date_gmt . ' UTC' );

			if ( false !== $stamp ) {
				$year = (string) gmdate( 'Y', $stamp );
			}
		}

		$author = '';

		if ( $post instanceof WP_Post ) {
			$author = (string) get_the_author_meta( 'display_name', (int) $post->post_author );
		}

		/**
		 * Filter the placeholder values before replacement.
		 *
		 * @since 1.0.0
		 * @param array<string, string> $values Placeholder map.
		 * @param WP_Post|null          $post   Related post.
		 */
		return (array) apply_filters(
			'mornrain_copyright_footer_placeholders',
			array(
				'{year}'   => $year,
				'{site}'   => $site,
				'{author}' => $author,
			),
			$post
		);
	}
endif;

if ( ! function_exists( 'mornrain_copyright_footer_get_html' ) ) :
	/**
	 * Build the escaped copyright notice for a post.
	 *
	 * @since 1.0.0
	 * @param int|WP_Post|null $post Post the notice belongs to.
	 * @return string Empty string when the notice is disabled or empty.
	 */
	function mornrain_copyright_footer_get_html( $post = null ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post ) {
			return '';
		}

		$settings = mornrain_copyright_footer_get_settings();

		/**
		 * Filter whether the notice is printed for a given post.
		 *
		 * @since 1.0.0
		 * @param bool    $enabled Whether to print the notice.
		 * @param WP_Post $post    Related post.
		 */
		$enabled = (bool) apply_filters( 'mornrain_copyright_footer_enabled', true, $post );

		if ( ! $enabled ) {
			return '';
		}

		$text = strtr( (string) $settings['template'], mornrain_copyright_footer_placeholders( $post ) );

		/**
		 * Filter the notice text after placeholder replacement.
		 *
		 * @since 1.0.0
		 * @param string  $text Notice text.
		 * @param WP_Post $post Related post.
		 */
		$text = (string) apply_filters( 'mornrain_copyright_footer_text', $text, $post );

		if ( '' === trim( $text ) ) {
			return '';
		}

		if ( 1 === (int) $settings['link_home'] ) {
			$inner = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( home_url( '/' ) ),
				esc_html( $text )
			);
		} else {
			$inner = esc_html( $text );
		}

		$html = sprintf(
			'<footer class="mornrain-copyright"><p class="mornrain-copyright__notice">%s</p></footer>',
			$inner
		);

		/**
		 * Filter the complete notice markup.
		 *
		 * @since 1.0.0
		 * @param string  $html Escaped HTML.
		 * @param WP_Post $post Related post.
		 */
		return (string) apply_filters( 'mornrain_copyright_footer_html', $html, $post );
	}
endif;
