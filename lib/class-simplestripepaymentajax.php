<?php
/**
 * Simple Stripe Payment
 *
 * @package    SimpleStripePayment
 * @subpackage Simple Stripe Payment Ajax
/*  Copyright (c) 2019- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$simplestripepaymentajax = new SimpleStripePaymentAjax();

/** ==================================================
 * Payment Ajax
 */
class SimpleStripePaymentAjax {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		$action1 = 'simple-stripe-payment-charge-ajax-action';
		add_action( 'wp_ajax_' . $action1, array( $this, 'simplestripepayment_charge_callback' ) );
		add_action( 'wp_ajax_nopriv_' . $action1, array( $this, 'simplestripepayment_charge_callback' ) );

	}

	/** ==================================================
	 * Charge Callback
	 *
	 * @param string $token token.
	 * @since 1.00
	 */
	public function simplestripepayment_charge_callback( $token = null ) {

		$action1 = 'simple-stripe-payment-charge-ajax-action';
		if ( check_ajax_referer( $action1, 'nonce', false ) ) {

			$stripe_settings = get_option( 'simplestripepayment_ids' );
			require_once dirname( __FILE__ ) . '/class-simplestripepaymentadmin.php';
			$simplestripepaymentadmin = new SimpleStripePaymentAdmin();
			if ( $stripe_settings['test'] ) {
				$secret_key = $simplestripepaymentadmin->decrypt( $stripe_settings['test_api_key'] );
			} else {
				$secret_key = $simplestripepaymentadmin->decrypt( $stripe_settings['api_key'] );
			}

			require_once plugin_dir_path( __DIR__ ) . 'stripe-php-master/init.php';

			/*	Set your secret key: remember to change this to your live secret key in production. See your keys here: https://dashboard.stripe.com/account/apikeys	*/
			\Stripe\Stripe::setApiKey( $secret_key );
			$charge_id = null;

			try {
				/* Token is created using Stripe.js or Checkout! Get the payment token submitted by the form: */
				if ( isset( $_POST['token_id'] ) && ! empty( $_POST['token_id'] ) ) {
					$token_id = sanitize_text_field( wp_unslash( $_POST['token_id'] ) );
				}
				if ( isset( $_POST['token_json'] ) && ! empty( $_POST['token_json'] ) ) {
					$token = sanitize_text_field( wp_unslash( $_POST['token_json'] ) );
				}
				if ( isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ) {
					$email = sanitize_email( wp_unslash( $_POST['email'] ) );
				}
				if ( isset( $_POST['amount'] ) && ! empty( $_POST['amount'] ) ) {
					$amount = intval( $_POST['amount'] );
				}
				if ( isset( $_POST['currency'] ) && ! empty( $_POST['currency'] ) ) {
					$currency = sanitize_text_field( wp_unslash( $_POST['currency'] ) );
				}
				if ( isset( $_POST['name'] ) && ! empty( $_POST['name'] ) ) {
					$name = sanitize_text_field( wp_unslash( $_POST['name'] ) );
				}
				if ( isset( $_POST['description'] ) && ! empty( $_POST['description'] ) ) {
					$description = sanitize_text_field( wp_unslash( $_POST['description'] ) );
				}
				if ( isset( $_POST['payname'] ) && ! empty( $_POST['payname'] ) ) {
					$payname = sanitize_text_field( wp_unslash( $_POST['payname'] ) );
				}
				$charge    = \Stripe\Charge::create(
					array(
						'amount'      => $amount,
						'currency'    => $currency,
						'source'      => $token_id,
						'description' => $description,
						'capture'     => false,
					)
				);
				$charge_id = $charge['id'];
				$charge->capture();
			} catch ( \Stripe\Error\Card $e ) {
				if ( null !== $charge_id ) {
					/* Cancel authorization */
					\Stripe\Refund::create(
						array(
							'charge' => $charge_id,
						)
					);
				}
				wp_die( esc_html__( 'Payment was not completed.', 'simple-stripe-payment' ) );
			}
			/* Payment */
			if ( ! is_null( $token ) ) {
				$token = apply_filters( 'simple_stripe_payment_charge', $token, $email, $amount, $currency, $name, $description, $payname );
				if ( is_wp_error( $token ) ) {
					return $token;
				}
			}
		} else {
			status_header( '403' );
			echo 'Forbidden';
		}

		wp_die();

	}

}


