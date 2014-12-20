<?php
/*
	Registers settings, adds a new submenu in the admin panel and adds options to array
	@since 0.1

	Copyright (c) 2009 Ryan Hellyer

*/


/**
 * Do not continue processing since file was called directly
 * @since 0.1
 */
if( !defined( 'ABSPATH' ) )
	return;

/**
 * Register our settings
 * @since 0.1
 */
function pixopoint_emailsubmit_options_init(){
	register_setting(
		'pixopoint_emailsubmit_options', // Admin HTML callback function
		PIXOPOINT_EMAILSUBMIT_OPTION, // Option name
		'pixopoint_emailsubmit_options_validate' // Validation callback function
	);
}
add_action( 'admin_init', 'pixopoint_emailsubmit_options_init' );

/**
 * Validating admin form callback
 * @since 0.2
 */
function pixopoint_emailsubmit_options_validate( $input ) {

	// Seperate email addresses
	$input = explode( ',', $input );

	// Churn through displaying each email one by one until they're all done
	foreach( $input as $key => $email ) {
		if ( $email )
			$email_list[$key] = sanitize_email( $email ); // Strip out nasties
	}

	// Recombine email addresses
	if ( is_array( $email_list ) ) {
		$input = implode( ',', $email_list );
	} else {
		$input = array();
	}

	return $input;
}

/**
 * Load up the menu page
 * @since 0.1
 */
function pixopoint_emailsubmit_options_add_page() {

	// Add admin page
	$page = add_options_page(
		__( 'Email Submissions' ), 
		__( 'Email submissions' ), // Name in menu
		'administrator', // Who has access
		'pixopoint_emailsubmitoptions', // URL
		'pixopoint_emailsubmit_options' // Callback to admin HTML
	);

	// Add styles (only for this admin page)
	add_action( 'admin_print_styles-' . $page, 'pixopoint_emailsubmit_admin_styles' );

}
add_action( 'admin_menu', 'pixopoint_emailsubmit_options_add_page' );

/**
 * Stuff for between the head tags
 * @since 0.1
 */
function pixopoint_emailsubmit_admin_styles() { ?>
<style type="text/css">
	#icon-pixopoint-emailsubmit {
		background:url(<?php echo PIXOPOINT_EMAILSUBMIT_URL; ?>images/h2_icon.png) no-repeat;
	}
</style>
<?php
}

/**
 * The main admin page content
 * @since 0.1
 */
function pixopoint_emailsubmit_options() {
	?>
	<form method="post" action="options.php" id="options">
	<div class="wrap">
		<?php screen_icon( 'pixopoint-emailsubmit' ); ?>
		<h2><?php _e( 'Email Submissions', 'pixopoint_emailsubmit_lang' ); ?></h2>

		<div style="clear:both;padding-top:20px;"></div>

		<h3><?php _e( 'Download email lists', 'pixopoint_emailsubmit_lang' ); ?></h3>
		<p><?php _e( 'These links will provide your email lists in their corresponding formats', 'pixopoint_emailsubmit_lang' ); ?></p>
		<ul>
			<li><a href="<?php echo wp_nonce_url( home_url() . '/?pixopoint_emailsubmit=carriagereturn', 'pixopoint_emailnonce' ); ?>"><?php _e( 'Carriage return delimited', 'pixopoint_emailsubmit_lang' ); ?></a></li>
			<li><a href="<?php echo wp_nonce_url( home_url() . '/?pixopoint_emailsubmit=tab', 'pixopoint_emailnonce' ); ?>"><?php _e( 'Tab delimited', 'pixopoint_emailsubmit_lang' ); ?></a></li>
			<li><a href="<?php echo wp_nonce_url( home_url() . '/?pixopoint_emailsubmit=comma', 'pixopoint_emailnonce' ); ?>"><?php _e( 'Comma delimited', 'pixopoint_emailsubmit_lang' ); ?></a></li>
			<li><a href="<?php echo wp_nonce_url( home_url() . '/?pixopoint_emailsubmit=space', 'pixopoint_emailnonce' ); ?>"><?php _e( 'Space delimited', 'pixopoint_emailsubmit_lang' ); ?></a></li>
		</ul>

		<h3><?php _e( 'Adding submission form', 'pixopoint_emailsubmit_lang' ); ?></h3>
		<p><?php _e( 'To add an email submit form to your site, use a shortcode with the formmat <code>[emailsubmit text="Enter your email address here" submit="Submit"]</code> in a post. To add it directly to your theme, wrap the shortcode in the do_shortcode() PHP function like so ... <code>&lt;?php echo do_shortcode( \'[emailsubmit text="" submit="test"]\' ); ?&gt;</code>', 'pixopoint_emailsubmit_lang' ); ?></p>

		<?php settings_fields( 'pixopoint_emailsubmit_options' ); ?>
		<h3><?php _e( 'Modify list', 'pixopoint_emailsubmit_lang' ); ?></h3>
		<textarea style="width:100%;height:300px;" name="<?php echo PIXOPOINT_EMAILSUBMIT_OPTION; ?>"><?php echo pixopoint_emailsubmit_list( 'comma', '' ); ?></textarea>
		<input type="hidden" name="action" value="update" />
		<p class="submit"><input type="submit" name="Submit" value="<?php _e( 'Update List' ) ?>" /></p>
	</form>


</div>

<?php
}
