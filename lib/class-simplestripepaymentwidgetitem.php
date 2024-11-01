<?php
/**
 * Simple Stripe Payment
 *
 * @package    SimpleStripePayment
 * @subpackage SimpleStripePayment Widget
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

add_action(
	'widgets_init',
	function() {
		register_widget( 'SimpleStripePaymentWidgetItem' );
	}
);

/** ==================================================
 * Widget
 *
 * @since 1.00
 */
class SimpleStripePaymentWidgetItem extends WP_Widget {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {
		parent::__construct(
			'SimpleStripePaymentWidgetItem', /* Base ID */
			__( 'Stripe Button', 'simple-stripe-payment' ), /* Name */
			array( 'description' => __( 'Button from Simple Stripe Payment.', 'simple-stripe-payment' ) ) /* Args */
		);
		if ( is_active_widget( false, false, $this->id_base, true ) ) {
			wp_enqueue_style( 'stripe', plugin_dir_url( __DIR__ ) . 'css/stripebutton.css', array(), '1.0.0' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'stripe', 'https://checkout.stripe.com/checkout.js', array(), '1.0.0', false );
		}
	}

	/** ==================================================
	 * Widget
	 *
	 * @param array $args args.
	 * @param array $instance instance.
	 * @since 1.00
	 */
	public function widget( $args, $instance ) {

		if ( ! is_singular() ) {

			$before_widget = $args['before_widget'];
			$before_title  = $args['before_title'];
			$after_title   = $args['after_title'];
			$after_widget  = $args['after_widget'];

			$title = apply_filters( 'widget_title', $instance['title'] );

			echo wp_kses_post( $before_widget );
			echo wp_kses_post( $before_title . esc_html( $title ) . $after_title );
			$simplestripepayment_settings = get_option( 'simplestripepayment_settings' );
			$before_text                  = $simplestripepayment_settings['before'];
			$label                        = $simplestripepayment_settings['label'];
			$content                      = '<span class="simple_stripe_payment_before">' . esc_html( $before_text ) . '</span><span class="simple_stripe_payment_after"></span><button id="SPPcustomButton">' . esc_html( $label ) . '</button>';
			echo wp_kses_post( $content );
			echo wp_kses_post( $after_widget );

		}

	}

	/** ==================================================
	 * Update
	 *
	 * @param array $new_instance new_instance.
	 * @param array $old_instance old_instance.
	 * @since 1.00
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = wp_strip_all_tags( $new_instance['title'] );
		return $instance;
	}

	/** ==================================================
	 * Form
	 *
	 * @param array $instance instance.
	 * @since 1.00
	 */
	public function form( $instance ) {

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = null;
		}

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

}


