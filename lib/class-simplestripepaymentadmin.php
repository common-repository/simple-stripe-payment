<?php
/**
 * Simple Stripe Payment
 *
 * @package    Simple Stripe Payment
 * @subpackage SimpleStripePaymentAdmin Management screen
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

$simplestripepaymentadmin = new SimpleStripePaymentAdmin();

/** ==================================================
 * Management screen
 */
class SimpleStripePaymentAdmin {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_settings' ) );

		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 2 );

		/* original hook */
		add_filter( 'ssp_decrypt', array( $this, 'decrypt' ), 10, 1 );

	}

	/** ==================================================
	 * Add a "Settings" link to the plugins page
	 *
	 * @param  array  $links  links array.
	 * @param  string $file   file.
	 * @return array  $links  links array.
	 * @since 1.00
	 */
	public function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty( $this_plugin ) ) {
			$this_plugin = 'simple-stripe-payment/simplestripepayment.php';
		}
		if ( $file === $this_plugin ) {
			$links[] = '<a href="' . admin_url( 'options-general.php?page=simplestripepayment' ) . '">' . __( 'Settings' ) . '</a>';
		}
			return $links;
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_menu() {
		add_options_page( 'Simple Stripe Payment Options', 'Simple Stripe Payment', 'manage_options', 'simplestripepayment', array( $this, 'plugin_options' ) );
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_options() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		$this->options_updated();

		$scriptname                   = admin_url( 'options-general.php?page=simplestripepayment' );
		$stripe_settings              = get_option( 'simplestripepayment_ids' );
		$simplestripepayment_settings = get_option( 'simplestripepayment_settings' );

		?>
		<div class="wrap">
		<h2>Simple Stripe Payment</h2>

			<details>
			<summary><strong><?php esc_html_e( 'Various links of this plugin', 'simple-stripe-payment' ); ?></strong></summary>
			<?php $this->credit(); ?>
			</details>

			<form method="post" action="<?php echo esc_url( $scriptname ); ?>">
			<?php wp_nonce_field( 'ssp_set', 'simplestripepayment_set' ); ?>

			<details style="margin-bottom: 5px;">
			<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( 'Stripe Api keys', 'simple-stripe-payment' ); ?></strong></summary>
				<h4><a style="text-decoration: none;" href="https://stripe.com/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Get Stripe Api keys', 'simple-stripe-payment' ); ?></a></h4>
				<div style="display: block;padding:5px 5px"><input type="checkbox" name="testmode" value="1" <?php checked( '1', $stripe_settings['test'] ); ?>><?php esc_html_e( 'Test mode', 'simple-stripe-payment' ); ?></div>
				<div style="display: block;padding:5px 5px"><?php esc_html_e( 'Public key for test', 'simple-stripe-payment' ); ?><input type="text" name="test_datakey" value="<?php echo esc_attr( $this->decrypt( $stripe_settings['test_data_key'] ) ); ?>" style="width: 300px"></div>
				<div style="display: block;padding:5px 5px"><?php esc_html_e( 'Secret key for test', 'simple-stripe-payment' ); ?><input type="password" name="test_apikey" value="<?php echo esc_attr( $this->decrypt( $stripe_settings['test_api_key'] ) ); ?>" style="width: 300px"></div>
				<div style="display: block;padding:5px 5px"><?php esc_html_e( 'Public key', 'simple-stripe-payment' ); ?><input type="text" name="datakey" value="<?php echo esc_attr( $this->decrypt( $stripe_settings['data_key'] ) ); ?>" style="width: 300px"></div>
				<div style="display: block;padding:5px 5px"><?php esc_html_e( 'Secret key', 'simple-stripe-payment' ); ?><input type="password" name="apikey" value="<?php echo esc_attr( $this->decrypt( $stripe_settings['api_key'] ) ); ?>" style="width: 300px"></div>
			</details>

			<details style="margin-bottom: 5px;">
			<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( "Default value of shortcode and block and widget attribute (Widget's only use this value.)", 'simple-stripe-payment' ); ?></strong></summary>
				<table border=1 cellspacing="0" cellpadding="5" bordercolor="#000000" style="border-collapse: collapse">
				<tr>
				<td><strong><?php esc_html_e( 'Attribute', 'simple-stripe-payment' ); ?></strong></td>
				<td><strong><?php esc_html_e( 'Default value', 'simple-stripe-payment' ); ?></strong></td>
				<td><strong><?php esc_html_e( 'Description' ); ?></strong></td>
				</tr>
				<tr>
				<td>amount</td>
				<td>
				<input type="text" name="ssp_amount" value="<?php echo esc_attr( $simplestripepayment_settings['amount'] ); ?>">
				</td>
				<td><?php esc_html_e( 'Price', 'simple-stripe-payment' ); ?></td>
				</tr>
				<tr>
				<td>name</td>
				<td>
				<input type="text" name="ssp_name" value="<?php echo esc_attr( $simplestripepayment_settings['name'] ); ?>">
				</td>
				<td><?php esc_html_e( 'The name of your company or website', 'simple-stripe-payment' ); ?></td>
				</tr>
				<tr>
				<td>description</td>
				<td>
				<input type="text" name="ssp_description" value="<?php echo esc_attr( $simplestripepayment_settings['description'] ); ?>">
				</td>
				<td><?php esc_html_e( 'A description of the product or service being purchased', 'simple-stripe-payment' ); ?></td>
				</tr>
				<tr>
				<td>currency</td>
				<td>
				<select name="ssp_currency">
				<?php
				$ssp_currency_arr = array(
					'USD' => 'United States dollar',
					'AUD' => 'Australian dollar',
					'BRL' => 'Brazilian real',
					'CAD' => 'Canadian dollar',
					'CZK' => 'Czech koruna',
					'DKK' => 'Danish krone',
					'EUR' => 'Euro',
					'HKD' => 'Hong Kong dollar',
					'HUF' => 'Hungarian forint',
					'INR' => 'Indian rupee',
					'ILS' => 'Israeli new shekel',
					'JPY' => 'Japanese yen',
					'MYR' => 'Malaysian ringgit',
					'MXN' => 'Mexican peso',
					'TWD' => 'New Taiwan dollar',
					'NZD' => 'New Zealand dollar',
					'NOK' => 'Norwegian krone',
					'PHP' => 'Philippine peso',
					'PLN' => 'Polish zloty',
					'GBP' => 'Pound sterling',
					'RUB' => 'Russian ruble',
					'SGD' => 'Singapore dollar',
					'SEK' => 'Swedish krona',
					'CHF' => 'Swiss franc',
					'THB' => 'Thai baht',
				);
				foreach ( $ssp_currency_arr as $key => $value ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $simplestripepayment_settings['currency'], $key ); ?>><?php echo esc_html( $value ); ?></option>
					<?php
				}
				unset( $ssp_currency_arr );
				?>
				</select>
				</td>
				<td><a style="text-decoration: none;" href="https://stripe.com/docs/currencies" target="_blank" rel="noopener noreferrer">Currency Codes</a></td>
				</tr>
				<tr>
				<td>label</td>
				<td>
				<input type="text" name="ssp_label" size="40" value="<?php echo esc_attr( $simplestripepayment_settings['label'] ); ?>">
				</td>
				<td><?php esc_html_e( 'The text to be shown on the blue button', 'simple-stripe-payment' ); ?></td>
				</tr>
				<tr>
				<td>before</td>
				<td>
				<textarea name="ssp_before" style="resize: auto; max-width: 500px; max-height: 500px; min-width: 100px; min-height: 100px; width:500px; height:100px"><?php echo esc_html( html_entity_decode( $simplestripepayment_settings['before'] ) ); ?></textarea>
				</td>
				<td><?php esc_html_e( 'Display before payment', 'simple-stripe-payment' ); ?></td>
				</tr>
				<tr>
				<td>after</td>
				<td>
				<textarea name="ssp_after" style="resize: auto; max-width: 500px; max-height: 500px; min-width: 100px; min-height: 100px; width:500px; height:100px"><?php echo esc_html( html_entity_decode( $simplestripepayment_settings['after'] ) ); ?></textarea>
				</td>
				<td><?php esc_html_e( 'Display after payment', 'simple-stripe-payment' ); ?></td>
				</tr>
				<tr>
				<td>remove</td>
				<td>
				<input type="text" name="ssp_remove" value="<?php echo esc_attr( $simplestripepayment_settings['remove'] ); ?>">
				</td>
				<td rowspan="2"><?php esc_html_e( 'HTML elements to remove after payment', 'simple-stripe-payment' ); ?></td>
				</tr>
				<tr>
				<td>remove2</td>
				<td rowspan="3"><?php esc_html_e( 'This is a special attribute. Only shortcode are valid.', 'simple-stripe-payment' ); ?></td>
				</tr>
				<tr>
				<td>email</td>
				<td><?php esc_html_e( 'Email' ); ?></td>
				</tr>
				<tr>
				<td>payname</td>
				<td><?php esc_html_e( 'Unique name for this payment', 'simple-stripe-payment' ); ?></td>
				</tr>
				</table>
			</details>

			<details style="margin-bottom: 5px;">
			<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( 'Apply Filters' ); ?></strong></summary>
				<h3><?php esc_html_e( 'The following filters are provided.', 'simple-stripe-payment' ); ?></h3>
				<h4>simple_stripe_payment_charge</h4>
				<div><?php esc_html_e( 'Processing when charging is successful.', 'simple-stripe-payment' ); ?></div>
				<div style="margin: 5px; padding: 5px;">
				<table border=1 cellspacing="0" cellpadding="5" bordercolor="#000000" style="border-collapse: collapse">
				<tr>
				<td><strong><?php esc_html_e( 'Variable', 'simple-stripe-payment' ); ?></strong></td>
				<td><strong><?php esc_html_e( 'Description' ); ?></strong></td>
				<td><strong><?php esc_html_e( 'From', 'simple-stripe-payment' ); ?></strong></td>
				</tr>
				<tr>
				<td><strong>$token</strong></td>
				<td><strong><?php esc_html_e( 'Payment information by JSON', 'simple-stripe-payment' ); ?></strong></td>
				<td><strong><?php esc_html_e( 'Value of Stripe', 'simple-stripe-payment' ); ?></strong></td>
				</tr>
				<tr>
				<td><strong>$email</strong></td>
				<td><strong><?php esc_html_e( 'Email' ); ?></strong></td>
				<td rowspan="6"><strong><?php esc_html_e( 'Value of Simple Stripe Payment', 'simple-stripe-payment' ); ?></strong></td>
				</tr>
				<tr>
				<td><strong>$amount</strong></td>
				<td><strong><?php esc_html_e( 'Price', 'simple-stripe-payment' ); ?></strong></td>
				</tr>
				<tr>
				<td><strong>$currency</strong></td>
				<td><strong><a style="text-decoration: none;" href="https://stripe.com/docs/currencies" target="_blank" rel="noopener noreferrer">Currency Codes</a></strong></td>
				</tr>
				<tr>
				<td><strong>$name</strong></td>
				<td><strong><?php esc_html_e( 'The name of your company or website', 'simple-stripe-payment' ); ?></strong></td>
				</tr>
				<tr>
				<td><strong>$description</strong></td>
				<td><strong><?php esc_html_e( 'A description of the product or service being purchased', 'simple-stripe-payment' ); ?></strong></td>
				</tr>
				<tr>
				<td><strong>$payname</strong></td>
				<td><strong><?php esc_html_e( 'Unique name for this payment', 'simple-stripe-payment' ); ?></strong></td>
				</tr>
				</table>
				</div>
				<div><strong><?php esc_html_e( 'Sample code', 'simple-stripe-payment' ); ?></strong></div>
<textarea rows="25" cols="120" readonly>
/** ==================================================
 * Show button for shortcode
 */
&lsaquo;?php echo do_shortcode('[simplestripepayment amount=100 currency="USD" name="Test" description="Test Charge" email="test@test.com" payname="testpay"]'; ?&rsaquo;

/** ==================================================
 * Filter of Simple Stripe Payment
 *
 * @param string $token  token.
 * @param string $email  email.
 * @param int    $amount  amount.
 * @param string $currency  currency.
 * @param string $name  name.
 * @param string $description  description.
 * @param string $payname  payname.
 */
function stripe_charge( $token, $email, $amount, $currency, $name, $description, $payname ) {

	/* Please write the process to be done when billing succeeds. */
	if ( 'testpay' === $payname ) {
		update_option( 'testpay_stripe', 'stripe' . $payname . $amount . $currency );
	}

}
add_filter( 'simple_stripe_payment_charge', 'stripe_charge', 10, 7 );
</textarea>
			</details>

			<?php submit_button( __( 'Save Changes' ), 'large', 'Manageset', false ); ?>
			</form>

		</div>
		<?php
	}

	/** ==================================================
	 * Credit
	 *
	 * @since 1.00
	 */
	private function credit() {

		$plugin_name    = null;
		$plugin_ver_num = null;
		$plugin_path    = plugin_dir_path( __DIR__ );
		$plugin_dir     = untrailingslashit( wp_normalize_path( $plugin_path ) );
		$slugs          = explode( '/', $plugin_dir );
		$slug           = end( $slugs );
		$files          = scandir( $plugin_dir );
		foreach ( $files as $file ) {
			if ( '.' === $file || '..' === $file || is_dir( $plugin_path . $file ) ) {
				continue;
			} else {
				$exts = explode( '.', $file );
				$ext  = strtolower( end( $exts ) );
				if ( 'php' === $ext ) {
					$plugin_datas = get_file_data(
						$plugin_path . $file,
						array(
							'name'    => 'Plugin Name',
							'version' => 'Version',
						)
					);
					if ( array_key_exists( 'name', $plugin_datas ) && ! empty( $plugin_datas['name'] ) && array_key_exists( 'version', $plugin_datas ) && ! empty( $plugin_datas['version'] ) ) {
						$plugin_name    = $plugin_datas['name'];
						$plugin_ver_num = $plugin_datas['version'];
						break;
					}
				}
			}
		}
		$plugin_version = __( 'Version:' ) . ' ' . $plugin_ver_num;
		/* translators: FAQ Link & Slug */
		$faq       = sprintf( __( 'https://wordpress.org/plugins/%s/faq', 'simple-stripe-payment' ), $slug );
		$support   = 'https://wordpress.org/support/plugin/' . $slug;
		$review    = 'https://wordpress.org/support/view/plugin-reviews/' . $slug;
		$translate = 'https://translate.wordpress.org/projects/wp-plugins/' . $slug;
		$facebook  = 'https://www.facebook.com/katsushikawamori/';
		$twitter   = 'https://twitter.com/dodesyo312';
		$youtube   = 'https://www.youtube.com/channel/UC5zTLeyROkvZm86OgNRcb_w';
		$donate    = __( 'https://shop.riverforest-wp.info/donate/', 'simple-stripe-payment' );

		?>
		<span style="font-weight: bold;">
		<div>
		<?php echo esc_html( $plugin_version ); ?> | 
		<a style="text-decoration: none;" href="<?php echo esc_url( $faq ); ?>" target="_blank" rel="noopener noreferrer">FAQ</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $support ); ?>" target="_blank" rel="noopener noreferrer">Support Forums</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $review ); ?>" target="_blank" rel="noopener noreferrer">Reviews</a>
		</div>
		<div>
		<a style="text-decoration: none;" href="<?php echo esc_url( $translate ); ?>" target="_blank" rel="noopener noreferrer">
		<?php
		/* translators: Plugin translation link */
		echo esc_html( sprintf( __( 'Translations for %s' ), $plugin_name ) );
		?>
		</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-facebook"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-twitter"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-video-alt3"></span></a>
		</div>
		</span>

		<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
		<h3><?php esc_html_e( 'Please make a donation if you like my work or would like to further the development of this plugin.', 'simple-stripe-payment' ); ?></h3>
		<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
		<button type="button" style="margin: 5px; padding: 5px;" onclick="window.open('<?php echo esc_url( $donate ); ?>')"><?php esc_html_e( 'Donate to this plugin &#187;' ); ?></button>
		</div>

		<?php

	}

	/** ==================================================
	 * Update wp_options table.
	 *
	 * @since 1.00
	 */
	private function options_updated() {

		if ( isset( $_POST['Manageset'] ) && ! empty( $_POST['Manageset'] ) ) {
			if ( check_admin_referer( 'ssp_set', 'simplestripepayment_set' ) ) {
				$stripe_settings = get_option( 'bdc_stripe' );
				if ( isset( $_POST['testmode'] ) && ! empty( $_POST['testmode'] ) ) {
					$stripe_settings['test'] = 1;
				} else {
					$stripe_settings['test'] = false;
				}
				if ( isset( $_POST['test_datakey'] ) && ! empty( $_POST['test_datakey'] ) ) {
					$stripe_settings['test_data_key'] = $this->encrypt( sanitize_text_field( wp_unslash( $_POST['test_datakey'] ) ) );
				} else {
					$stripe_settings['test_data_key'] = null;
				}
				if ( isset( $_POST['test_apikey'] ) && ! empty( $_POST['test_apikey'] ) ) {
					$stripe_settings['test_api_key'] = $this->encrypt( sanitize_text_field( wp_unslash( $_POST['test_apikey'] ) ) );
				} else {
					$stripe_settings['test_api_key'] = null;
				}
				if ( isset( $_POST['datakey'] ) && ! empty( $_POST['datakey'] ) ) {
					$stripe_settings['data_key'] = $this->encrypt( sanitize_text_field( wp_unslash( $_POST['datakey'] ) ) );
				} else {
					$stripe_settings['data_key'] = null;
				}
				if ( isset( $_POST['apikey'] ) && ! empty( $_POST['apikey'] ) ) {
					$stripe_settings['api_key'] = $this->encrypt( sanitize_text_field( wp_unslash( $_POST['apikey'] ) ) );
				} else {
					$stripe_settings['api_key'] = null;
				}
				update_option( 'simplestripepayment_ids', $stripe_settings );
				$simplestripepayment_settings = get_option( 'simplestripepayment_settings' );
				if ( isset( $_POST['ssp_amount'] ) && ! empty( $_POST['ssp_amount'] ) ) {
					$simplestripepayment_settings['amount'] = intval( $_POST['ssp_amount'] );
				}
				if ( isset( $_POST['ssp_name'] ) && ! empty( $_POST['ssp_name'] ) ) {
					$simplestripepayment_settings['name'] = sanitize_text_field( wp_unslash( $_POST['ssp_name'] ) );
				}
				if ( isset( $_POST['ssp_description'] ) && ! empty( $_POST['ssp_description'] ) ) {
					$simplestripepayment_settings['description'] = sanitize_text_field( wp_unslash( $_POST['ssp_description'] ) );
				}
				if ( isset( $_POST['ssp_currency'] ) && ! empty( $_POST['ssp_currency'] ) ) {
					$simplestripepayment_settings['currency'] = sanitize_text_field( wp_unslash( $_POST['ssp_currency'] ) );
				}
				if ( isset( $_POST['ssp_label'] ) && ! empty( $_POST['ssp_label'] ) ) {
					$simplestripepayment_settings['label'] = sanitize_text_field( wp_unslash( $_POST['ssp_label'] ) );
				}
				if ( isset( $_POST['ssp_before'] ) && ! empty( $_POST['ssp_before'] ) ) {
					$simplestripepayment_settings['before'] = htmlentities( sanitize_text_field( wp_unslash( $_POST['ssp_before'] ) ) );
				}
				if ( isset( $_POST['ssp_after'] ) && ! empty( $_POST['ssp_after'] ) ) {
					$simplestripepayment_settings['after'] = htmlentities( sanitize_text_field( wp_unslash( $_POST['ssp_after'] ) ) );
				}
				if ( isset( $_POST['ssp_remove'] ) && ! empty( $_POST['ssp_remove'] ) ) {
					$simplestripepayment_settings['remove'] = sanitize_text_field( wp_unslash( $_POST['ssp_remove'] ) );
				}
				update_option( 'simplestripepayment_settings', $simplestripepayment_settings );
				echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html__( 'Settings' ) . ' --> ' . esc_html__( 'Settings saved.' ) . '</li></ul></div>';
			}
		}

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

	/**
	 * Crypt AES 256
	 * https://blog.ohgaki.net/encrypt-decrypt-using-openssl
	 *
	 * @param data $data  data.
	 * @return base64 encrypted  encrypted.
	 */
	private function encrypt( $data ) {

		$password = 'simple_stripe_payment';

		/* Set a random salt */
		$salt = openssl_random_pseudo_bytes( 16 );

		$salted = '';
		$dx     = '';
		/* Salt the key(32) and iv(16) = 48 */
		$length = '';
		while ( $length < 48 ) {
			$dx      = hash( 'sha256', $dx . $password . $salt, true );
			$salted .= $dx;
			$length  = strlen( $salted );
		}

		$key = substr( $salted, 0, 32 );
		$iv  = substr( $salted, 32, 16 );

		$encrypted_data = openssl_encrypt( $data, 'AES-256-CBC', $key, true, $iv );
		return base64_encode( $salt . $encrypted_data );
	}

	/**
	 * Decrypt AES 256
	 * https://blog.ohgaki.net/encrypt-decrypt-using-openssl
	 *
	 * @param data $edata  edata.
	 * @return decrypted $data  data.
	 */
	public function decrypt( $edata ) {

		$password = 'simple_stripe_payment';

		$data = base64_decode( $edata );
		$salt = substr( $data, 0, 16 );
		$ct   = substr( $data, 16 );

		$rounds  = 3; /* depends on key length */
		$data00  = $password . $salt;
		$hash    = array();
		$hash[0] = hash( 'sha256', $data00, true );
		$result  = $hash[0];
		for ( $i = 1; $i < $rounds; $i++ ) {
			$hash[ $i ] = hash( 'sha256', $hash[ $i - 1 ] . $data00, true );
			$result    .= $hash[ $i ];
		}
		$key = substr( $result, 0, 32 );
		$iv  = substr( $result, 32, 16 );

		return openssl_decrypt( $ct, 'AES-256-CBC', $key, true, $iv );
	}

}


