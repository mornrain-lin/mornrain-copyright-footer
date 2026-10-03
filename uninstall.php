<?php
/**
 * Uninstall routine.
 *
 * Deletes the single option row the plugin creates, on every site of a
 * multisite network. No custom tables, transients or user meta are involved.
 *
 * @package Mornrain_Copyright_Footer
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$mornrain_copyright_footer_option = 'mornrain_copyright_footer_settings';

delete_option( $mornrain_copyright_footer_option );

if ( is_multisite() ) {
	$mornrain_copyright_footer_sites = get_sites( array( 'fields' => 'ids' ) );

	foreach ( $mornrain_copyright_footer_sites as $mornrain_copyright_footer_site ) {
		switch_to_blog( (int) $mornrain_copyright_footer_site );
		delete_option( $mornrain_copyright_footer_option );
		restore_current_blog();
	}
}
