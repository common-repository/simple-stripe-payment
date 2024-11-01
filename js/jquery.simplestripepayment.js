/**
 * Simple Stripe Payment
 *
 * @package    Simple Simple Stripe Payment
 * @subpackage jquery.simplestripepayment.js
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

jQuery(
	function($) {

		var handler = StripeCheckout.configure(
			{
				key: SIMPLESTRIPEPAYMENTCHARGE.public_key,
				image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
				locale: 'auto',
				token: function(token) {
					/* You can access the token ID with `token.id`. */
					/* Get the token ID to your server-side code for use. */
					/* charge */
					chargeServer( token );
					/* remove html */
					$( ".simple_stripe_payment_before" ).remove();
					$( "#SPPcustomButton" ).remove();
					$( SIMPLESTRIPEPAYMENTCHARGE.remove ).remove();
					$( SIMPLESTRIPEPAYMENTCHARGE.remove2 ).remove();
					$( ".simple_stripe_payment_after" ).append( SIMPLESTRIPEPAYMENTCHARGE.after );
				}
			}
		);

		var btn = document.getElementById( 'SPPcustomButton' );
		if ( btn !== null ) {
			btn.addEventListener(
				'click',
				function(e) {
					/* Open Checkout with further options: */
					handler.open(
						{
							name: SIMPLESTRIPEPAYMENTCHARGE.name,
							description: SIMPLESTRIPEPAYMENTCHARGE.description,
							currency: SIMPLESTRIPEPAYMENTCHARGE.currency,
							amount: parseInt( SIMPLESTRIPEPAYMENTCHARGE.amount ),
							email: SIMPLESTRIPEPAYMENTCHARGE.email
						}
					);
					e.preventDefault();
				}
			);
		}

		/* Close Checkout on page navigation: */
		window.addEventListener(
			'popstate',
			function() {
				handler.close();
			}
		);

		function chargeServer(token) {
			console.log( token.id );
			$.ajax(
				{
					type: 'POST',
					dataType: 'json',
					url: SIMPLESTRIPEPAYMENTCHARGE.ajax_url,
					data: {
						'action': SIMPLESTRIPEPAYMENTCHARGE.action,
						'nonce': SIMPLESTRIPEPAYMENTCHARGE.nonce,
						'token_id': token.id,
						'token_json': JSON.stringify( token ),
						'email': SIMPLESTRIPEPAYMENTCHARGE.email,
						'amount': SIMPLESTRIPEPAYMENTCHARGE.amount,
						'currency': SIMPLESTRIPEPAYMENTCHARGE.currency,
						'name': SIMPLESTRIPEPAYMENTCHARGE.name,
						'description': SIMPLESTRIPEPAYMENTCHARGE.description,
						'payname': SIMPLESTRIPEPAYMENTCHARGE.payname
					}
				}
			).done(
				function(callback){
						/* console.log(JSON.stringify(token)); */
				}
			).fail(
				function(XMLHttpRequest, textStatus, errorThrown){
						/* console.log("XMLHttpRequest : " + XMLHttpRequest.status); */
						/* console.log("textStatus     : " + textStatus); */
						/* console.log("errorThrown    : " + errorThrown.message); */
				}
			);
		}

	}
);
