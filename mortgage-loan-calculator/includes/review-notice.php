<?php
/**
 * "Rate this plugin" admin notice: manage_options users only, on the plugins screen and this plugin's settings,
 * 14 days after it was first seen; dismissible for good or snoozed for 30 days. No external requests, no tracking.
 */

defined( 'ABSPATH' ) || exit;

define( 'MLCLC_REVIEW_DELAY', 14 * DAY_IN_SECONDS );
define( 'MLCLC_REVIEW_SNOOZE', 30 * DAY_IN_SECONDS );

/**
 * Remembers when the plugin was first seen; existing installs start counting from the update.
 */
function mlclc_review_since() {
	$since = (int) get_option( 'mlclc_review_since', 0 );
	if ( ! $since ) {
		$since = time();
		update_option( 'mlclc_review_since', $since, false );
	}
	return $since;
}

/**
 * Whether the current user should see the notice on the current screen.
 */
function mlclc_review_due() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( $screen->id, array( 'plugins' ), true ) ) {
		return false;
	}
	$state = get_user_meta( get_current_user_id(), 'mlclc_review_state', true );
	if ( 'done' === $state ) {
		return false;
	}
	if ( is_numeric( $state ) && time() < (int) $state ) {
		return false;
	}
	return time() >= mlclc_review_since() + MLCLC_REVIEW_DELAY;
}

function mlclc_review_notice() {
	if ( ! mlclc_review_due() ) {
		return;
	}
	$rate  = 'https://wordpress.org/support/plugin/mortgage-loan-calculator/reviews/#new-post';
	$later = wp_nonce_url( admin_url( 'admin-post.php?action=mlclc_review&do=later' ), 'mlclc_review' );
	$done  = wp_nonce_url( admin_url( 'admin-post.php?action=mlclc_review&do=done' ), 'mlclc_review' );
	?>
	<div class="notice notice-info">
		<p>
			<?php
			/* translators: %s: plugin name */
			echo esc_html( sprintf( __( 'Thank you for using %s! If it helps your site, would you rate it on WordPress.org? It takes a minute and helps other people find it.', 'mortgage-loan-calculator' ), 'Mortgage Calculator' ) );
			?>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $rate ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Rate it', 'mortgage-loan-calculator' ); ?></a>
			<a class="button" href="<?php echo esc_url( $later ); ?>"><?php esc_html_e( 'Maybe later', 'mortgage-loan-calculator' ); ?></a>
			<a class="button-link" href="<?php echo esc_url( $done ); ?>"><?php esc_html_e( 'I already did', 'mortgage-loan-calculator' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'mlclc_review_notice' );

function mlclc_review_action() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'mortgage-loan-calculator' ), 403 );
	}
	check_admin_referer( 'mlclc_review' );
	$do = isset( $_GET['do'] ) ? sanitize_key( wp_unslash( $_GET['do'] ) ) : '';
	update_user_meta( get_current_user_id(), 'mlclc_review_state', 'done' === $do ? 'done' : time() + MLCLC_REVIEW_SNOOZE );
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'plugins.php' ) );
	exit;
}
add_action( 'admin_post_mlclc_review', 'mlclc_review_action' );

add_action( 'admin_init', 'mlclc_review_since' );
