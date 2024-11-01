=== Simple Stripe Payment ===
Contributors: Katsushi Kawamori
Donate link: https://shop.riverforest-wp.info/donate/
Tags: block, stripe, shortcode, widget
Requires at least: 4.6
Requires PHP: 5.6
Tested up to: 5.7
Stable tag: 1.19
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrates Stripe checkout into WordPress.

== Description ==

= Integrates Stripe checkout into WordPress. =
* Paste Stripe checkout button to Single Post and Single Page by short code.
* Paste Stripe checkout button to Single Post and Single Page by block.
* Paste Stripe checkout button to Archive Page and Home Page by widget.
* Complete payment without screen transition.
* Can specify the text or html before payment and after payment.
* Can remove html elements to after payment.
* Prepared a filter hook for processing immediately after billing.

= Tutorial Video =
[youtube https://youtu.be/aUvNJNet7jU]

= Sample of how to use the filter hook =
* Show button
~~~
echo do_shortcode('[simplestripepayment amount=100 currency="USD" name="Test" description="Test Charge" email="test@test.com" payname="testpay"]';
~~~
* shortcode
Attribute : Description
amount : Price
name : The name of your company or website
description :A description of the product or service being purchased
currency : Currency Codes
label : The text to be shown on the blue button
before : Display before payment
after : Display after payment
remove : HTML elements to remove after payment
remove2 : HTML elements to remove after payment
email : Email
payname : Unique name for this payment
* Filter hook & Function
~~~
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
~~~
* Filter hook
Variable : Description : From
$token : Payment information by JSON : Value of Stripe
$email : Email : Value of Simple Stripe Payment
$amount : Price : Value of Simple Stripe Payment
$currency : Currency Codes : Value of Simple Stripe Payment
$name : The name of your company or website : Value of Simple Stripe Payment
$description : A description of the product or service being purchased : Value of Simple Stripe Payment
$payname : Unique name for this payment : Value of Simple Stripe Payment

== Installation ==

1. Upload `simple-stripe-payment` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

none

== Screenshots ==

1. Stripe Settings
2. Short code
3. block
4. Insert from block
5. Page view
6. Widget Settings

== Changelog ==

= 1.19 =
Minor change.

= 1.18 =
Update stripe php library.

= 1.17 =
Update stripe php library.

= 1.16 =
Fixed sample code.
Added a "payname" to the block.
Update stripe php library.

= 1.15 =
Update stripe php library.
The block now supports ESNext.

= 1.14 =
Fixed problem shortcode.

= 1.13 =
Fixed a payment issue in the admin screen.

= 1.12 =
Conformed to the WordPress coding standard.

= 1.11 =
Update stripe php library.

= 1.10 =
Fixed script loading error on archive page.
Update stripe php library.

= 1.09 =
Fixed problem of charge.

= 1.08 =
Fixed problem of screen transition.
Update stripe php library.

= 1.07 =
Fixed of filter sample code.
Fixed  problem of widget.
Update stripe php library.

= 1.06 =
Fixed  problem of widget.

= 1.05 =
Add shortcode attribute 'payname'.

= 1.04 =
Fixed loading problem of Javascript.

= 1.03 =
Receive payment information by json.

= 1.02 =
Add shortcode attribute 'remove2'.

= 1.01 =
Add stripe button css.

= 1.00 =
Initial release.

== Upgrade Notice ==

= 1.00 =
Initial release.

