<?php
/**
 * Simple Stripe Payment
 *
 * @package    Simple Stripe Payment
 * @subpackage SimpleStripePaymentRegist registered in the database
	Copyright (c) 2019- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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

$simplestripepaymentregist = new SimpleStripePaymentRegist();

/** ==================================================
 * Registered in the database
 */
class SimpleStripePaymentRegist {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_settings' ) );

	}

	/** ==================================================
	 * Settings register
	 *
	 * @since 1.00
	 */
	public function register_settings() {

		if ( ! get_option( 'simplestripepayment_ids' ) ) {
			$stripe_tbl = array(
				'test'          => 1,
				'test_data_key' => null,
				'test_api_key'  => null,
				'data_key'      => null,
				'api_key'       => null,
			);
			update_option( 'simplestripepayment_ids', $stripe_tbl );
		}

		if ( ! get_option( 'simplestripepayment_settings' ) ) {
			$settings_tbl = array(
				'amount'      => 10,
				'name'        => null,
				'description' => null,
				'currency'    => 'USD',
				'label'       => __( 'Pay with card using the Stripe', 'simple-stripe-payment' ),
				'before'      => null,
				'after'       => null,
				'remove'      => null,
			);
			update_option( 'simplestripepayment_settings', $settings_tbl );
		}

	}

}


