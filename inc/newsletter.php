<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Newsletter signup: sends the email to MailPoet.
 */
function my_store_subscribe() {
	check_ajax_referer( 'ms_subscribe', 'nonce' );

	// Honeypot: bots fill this, humans never see it.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success();
	}

	// Rate limit: 5 attempts per 10 minutes per IP.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'ms_sub_' . md5( $ip );
	$hit = (int) get_transient( $key );

	if ( $hit >= 5 ) {
		wp_send_json_error( array( 'message' => __( 'Too many attempts. Please try again in a few minutes.', 'my-store' ) ), 429 );
	}
	set_transient( $key, $hit + 1, 10 * MINUTE_IN_SECONDS );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'my-store' ) ), 400 );
	}

	if ( ! class_exists( \MailPoet\API\API::class ) ) {
		wp_send_json_error( array( 'message' => __( 'Newsletter signup is not available right now.', 'my-store' ) ), 500 );
	}

	try {
		$api     = \MailPoet\API\API::MP( 'v1' );
		$list_id = isset( $_POST['list'] ) ? absint( $_POST['list'] ) : 0;

		// No list chosen: use the first one.
		if ( ! $list_id ) {
			$lists   = $api->getLists();
			$list_id = $lists ? (int) $lists[0]['id'] : 0;
		}

		$lists_to_join = $list_id ? array( $list_id ) : array();

		try {
			$api->addSubscriber( array( 'email' => $email ), $lists_to_join );
		} catch ( \Exception $e ) {
			// Already a subscriber: just make sure they are on the list.
			if ( 12 === (int) $e->getCode() || false !== stripos( $e->getMessage(), 'already exists' ) ) {
				if ( $lists_to_join ) {
					$api->subscribeToLists( $email, $lists_to_join );
				}
			} else {
				throw $e;
			}
		}
	} catch ( \Exception $e ) {
		wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again.', 'my-store' ) ), 500 );
	}

	wp_send_json_success();
}
add_action( 'wp_ajax_ms_subscribe', 'my_store_subscribe' );
add_action( 'wp_ajax_nopriv_ms_subscribe', 'my_store_subscribe' );