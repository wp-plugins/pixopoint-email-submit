<?php
/*

	Plugin Name: Email Submit
	Plugin URI: https://geek.hellyer.kiwi/product/email-submit/
	Description: A WordPress plugin which adds an email submit form
	Author: Ryan Hellyer
	Version: 0.2.4
	Author URI: https://geek.hellyer.kiwi/

	Copyright (c) 2009 Ryan Hellyer

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

*/

/**
 * Do not continue processing since file was called directly
 * @since 0.1
 */
if ( !defined( 'ABSPATH' ) )
	return;

/**
 * Specifying locations
 * @since 0.1
 */
define( 'PIXOPOINT_EMAILSUBMIT_AD', "<!-- Email Submit plugin v0.2.4 - https://geek.hellyer.kiwi/products/email-submit/ -->
" );
define( 'PIXOPOINT_EMAILSUBMIT_DIR', plugins_url( '', __FILE__ ) . '/' ); // Plugin folder URL
define( 'PIXOPOINT_EMAILSUBMIT_OPTION', 'pixopoint_emailsubmit_option' );


/**
 * [emailsubmit] shortcode
 * @since 0.1
 */
function pixopoint_emailsubmit_shortcode( $atts ) {

	// Grabbing parameters and setting default values
	extract(
		shortcode_atts(
			array(
				'text' => 'Enter your email address',
				'submit' => 'Submit',
			),
			$atts
		)
	);

	return '
' . PIXOPOINT_EMAILSUBMIT_AD . '
<form class="pixopoint-emailsubmit" method="post" action="">
	<label>Email</label>
	<input type="text" value="' . $text . '" name="pixopoint_emailsubmit" onfocus="if (this.value == \'' . $text . '\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'' . $text . '\';}" /> 
	<input type="submit" value="' . $submit . '" />
</form>
';
}
add_shortcode( 'emailsubmit', 'pixopoint_emailsubmit_shortcode' );

/**
 * Add another email
 * @since 0.1
 */
if ( isset( $_POST['pixopoint_emailsubmit'] ) ) {
	$email_list = get_option( PIXOPOINT_EMAILSUBMIT_OPTION );
	$email_list = $email_list . ',' . sanitize_email( $_POST['pixopoint_emailsubmit'] );
	add_option( PIXOPOINT_EMAILSUBMIT_OPTION, $email_list );
	update_option( PIXOPOINT_EMAILSUBMIT_OPTION, $email_list );
}

/**
 * If requested, display list on screen
 * @since 0.1
 */
function pixopoint_emailsubmit_get() {
	if ( ! isset( $_GET['pixopoint_emailsubmit'] ) )
		return;

	// Security checks (user permissions and nonce protection)
	if ( !current_user_can( 'manage_options' ) OR !wp_verify_nonce( $_GET['_wpnonce'], 'pixopoint_emailnonce') )
		return;

	// Displaying list
	echo pixopoint_emailsubmit_list( $_GET['pixopoint_emailsubmit'] );
}
add_action( 'init', 'pixopoint_emailsubmit_get' );

/**
 * Comma delimited email list
 * @since 0.1
 */
function pixopoint_emailsubmit_list( $format, $headers = 'text' ) {

	// Load appropriate headers
	if ( 'text' == $headers ) {
		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="pixopoint-email-list.txt"' );
	}

	// Work out which seperator to use
	switch ( $format ) {
		case 'comma': $seperator = ','; break;
		case 'tab': $seperator = '	'; break;
		case 'linebreak': $seperator = '<br />'; break;
		case 'carriagereturn': $seperator = "\n"; break;
		case 'space': $seperator = ' '; break;
		case 'none': $seperator = ''; break;
		default: $seperator = ','; break;
	}

	// Split up emails into array
	$email_list = explode( ',', get_option( PIXOPOINT_EMAILSUBMIT_OPTION ) );

	// Churn through displaying each email one by one until they're all done
	foreach( $email_list as $key ) {
		echo sanitize_email( $key ), $seperator;
	}

	// Kill execution before rest of page loads up
	if ( 'text' == $headers )
		exit;
}


/**
 * Load admin page
 * @since 0.1
 */
if ( is_admin() )
	require( 'admin_page.php' );

