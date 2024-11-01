<?php
/**
 * Simple Stripe Payment
 *
 * @package    Simple Stripe Payment
 * @subpackage SimpleStripePayment Main Functions
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

$simplestripepayment = new SimpleStripePayment();

/** ==================================================
 * Class Main function
 *
 * @since 1.00
 */
class SimpleStripePayment {

	/** ==================================================
	 * Attributes
	 *
	 * @var $simplepaypalpayment_atts  attributes.
	 */
	private $simplestripepayment_atts;

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		add_action( 'wp_footer', array( $this, 'load_localize_scripts_styles' ) );
		add_action( 'admin_footer', array( $this, 'load_localize_scripts_styles' ) );

		add_shortcode( 'simplestripepayment', array( $this, 'simplestripepayment_func' ) );
		add_action( 'init', array( $this, 'simplestripepayment_block_init' ) );

	}

	/** ==================================================
	 * Attribute block
	 *
	 * @since 1.00
	 */
	public function simplestripepayment_block_init() {

		$asset_file = include( plugin_dir_path( __DIR__ ) . 'block/dist/simplestripepayment-block.asset.php' );

		wp_register_script(
			'simplestripepayment-block',
			plugins_url( 'block/dist/simplestripepayment-block.js', dirname( __FILE__ ) ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		wp_localize_script(
			'simplestripepayment-block',
			'simplestripepayment_text',
			array(
				'amount' => __( 'Price', 'simple-stripe-payment' ),
				'name' => __( 'The name of your company or website', 'simple-stripe-payment' ),
				'description' => __( 'A description of the product or service being purchased', 'simple-stripe-payment' ),
				'currency' => __( 'Currency Codes', 'simple-stripe-payment' ),
				'label' => __( 'The text to be shown on the blue button', 'simple-stripe-payment' ),
				'before' => __( 'Display before payment', 'simple-stripe-payment' ),
				'after' => __( 'Display after payment', 'simple-stripe-payment' ),
				'remove' => __( 'HTML elements to remove after payment', 'simple-stripe-payment' ),
				'check' => __( 'Can check the behavior in "Preview".', 'simple-stripe-payment' ),
				'view' => __( 'View' ),
				'payname' => __( 'Unique name for this payment', 'simple-stripe-payment' ),
			)
		);

		$simplestripepayment_settings = get_option( 'simplestripepayment_settings' );
		register_block_type(
			'simple-stripe-payment/simplestripepayment-block',
			array(
				'editor_script'   => 'simplestripepayment-block',
				'render_callback' => array( $this, 'simplestripepayment_block_func' ),
				'attributes'      => array(
					'amount'      => array(
						'type'    => 'string',
						'default' => $simplestripepayment_settings['amount'],
					),
					'name'        => array(
						'type'    => 'string',
						'default' => $simplestripepayment_settings['name'],
					),
					'description' => array(
						'type'    => 'string',
						'default' => $simplestripepayment_settings['description'],
					),
					'currency'    => array(
						'type'    => 'string',
						'default' => $simplestripepayment_settings['currency'],
					),
					'label'       => array(
						'type'    => 'string',
						'default' => $simplestripepayment_settings['label'],
					),
					'before'      => array(
						'type'    => 'string',
						'default' => html_entity_decode( $simplestripepayment_settings['before'] ),
					),
					'after'       => array(
						'type'    => 'string',
						'default' => html_entity_decode( $simplestripepayment_settings['after'] ),
					),
					'remove'      => array(
						'type'    => 'string',
						'default' => html_entity_decode( $simplestripepayment_settings['remove'] ),
					),
					'payname'      => array(
						'type'    => 'string',
						'default' => null,
					),
				),
			)
		);

	}

	/** ==================================================
	 * Blocks
	 *
	 * @param array  $atts  atts.
	 * @param string $content  content.
	 * @return string $content
	 * @since 1.00
	 */
	public function simplestripepayment_block_func( $atts, $content ) {

		$settings_tbl = get_option( 'simplestripepayment_settings' );

		foreach ( $settings_tbl as $key => $value ) {
			$blockkey = strtolower( $key );
			if ( empty( $atts[ $blockkey ] ) ) {
				$atts[ $blockkey ] = $value;
			} else {
				if ( strtolower( $atts[ $blockkey ] ) === 'false' ) {
					$atts[ $blockkey ] = null;
				}
			}
		}

		$this->simplestripepayment_atts = $atts;

		$content = '<div class="simple_stripe_payment_before">' . $atts['before'] . '</div><div class="simple_stripe_payment_after"></div><div><button id="SPPcustomButton" class="stripe_btn">' . $atts['label'] . '</button></div>';

		if ( is_archive() || is_home() ) {
			$content = null;
		}

		return $content;

	}

	/** ==================================================
	 * Load Localize Script and Style
	 *
	 * @since 1.00
	 */
	public function load_localize_scripts_styles() {

		$localize_ssp_settings = array();
		if ( is_singular() || is_admin() ) {
			$localize_ssp_settings = $this->simplestripepayment_atts;
		} else { /* for widget */
			if ( ( is_archive() || is_home() ) && is_active_widget( false, false, 'simplestripepaymentwidgetitem', true ) ) {
				$localize_ssp_settings = get_option( 'simplestripepayment_settings' );
			} else {
				return;
			}
		}
		if ( empty( $localize_ssp_settings ) ) {
			return;
		}

		/* Zero-decimal currencies */
		$zero_decimal_curr = array( 'MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'VND', 'JPY', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF' );
		if ( ! in_array( $localize_ssp_settings['currency'], $zero_decimal_curr, true ) ) {
			$localize_ssp_settings['amount'] = intval( $localize_ssp_settings['amount'] * 100 );
		}

		wp_enqueue_style( 'stripe', plugin_dir_url( __DIR__ ) . 'css/stripebutton.css', array(), '1.0.0', 'all' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'stripe', 'https://checkout.stripe.com/checkout.js', array(), '1.0.0', false );

		$stripe_settings = get_option( 'simplestripepayment_ids' );
		if ( $stripe_settings['test'] ) {
			$public_key = apply_filters( 'ssp_decrypt', $stripe_settings['test_data_key'] );
		} else {
			$public_key = apply_filters( 'ssp_decrypt', $stripe_settings['data_key'] );
		}
		$public_key_arr = array( 'public_key' => $public_key );
		$localize_ssp_settings = array_merge( $localize_ssp_settings, $public_key_arr );

		$handle  = 'simple-stripe-payment-ajax-script';
		$action1 = 'simple-stripe-payment-charge-ajax-action';
		wp_enqueue_script( $handle, plugin_dir_url( __DIR__ ) . 'js/jquery.simplestripepayment.js', array( 'jquery' ), '1.0.0', false );
		$ajax_arr = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'action'   => $action1,
			'nonce'    => wp_create_nonce( $action1 ),
		);

		$ajax_arr = array_merge( $ajax_arr, $localize_ssp_settings );
		wp_localize_script( $handle, 'SIMPLESTRIPEPAYMENTCHARGE', $ajax_arr );

	}

	/** ==================================================
	 * Short code
	 *
	 * @param  array  $atts    atts.
	 * @param  string $content content.
	 * @return string $content content.
	 * @since 1.00
	 */
	public function simplestripepayment_func( $atts, $content = null ) {

		$a = shortcode_atts(
			array(
				'amount'      => '',
				'name'        => '',
				'description' => '',
				'currency'    => '',
				'label'       => '',
				'before'      => '',
				'after'       => '',
				'remove'      => '',
				'remove2'     => '',
				'email'       => '',
				'payname'     => '',
			),
			$atts
		);

		$settings_tbl = get_option( 'simplestripepayment_settings' );

		foreach ( $settings_tbl as $key => $value ) {
			$shortcodekey = strtolower( $key );
			if ( empty( $a[ $shortcodekey ] ) ) {
				$a[ $shortcodekey ] = $value;
			} else {
				if ( strtolower( $a[ $shortcodekey ] ) === 'false' ) {
					$a[ $shortcodekey ] = null;
				}
			}
		}

		$this->simplestripepayment_atts = $a;

		if ( is_singular() || is_admin() ) {
			if ( ! empty( $a['before'] ) ) {
				$before_text = $a['before'];
			} else {
				$before_text = null;
			}
			$content = '<div class="simple_stripe_payment_before">' . $before_text . '</div><span class="simple_stripe_payment_after"></span><button id="SPPcustomButton" class="stripe_btn">' . $a['label'] . '</button>';
		} else {
			if ( is_archive() || is_home() ) {
				$content = null;
			} else {
				$content = __( 'It is not displayed on the edit screen. Please preview.', 'simple-stripe-payment' );
			}
		}

		return do_shortcode( $content );

	}

}


