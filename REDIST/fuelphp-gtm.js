$(function(){
	
	/* GTM product clicks */
	$('.product-click').click(function(event) {
		event.preventDefault();

		if (this.id) {
			if ($.inArray(this.id, GTM_products)) {
				
				url = $(this).attr('href');
				productObj = GTM_products[this.id];

				dataLayer.push({
					'event': 'productClick',
					'ecommerce': {
						'click': {
							'actionField': {'list': GTM_products['list'] || null},
							'products': productObj
						}
					},
					'eventCallback': function() {
						document.location = url
					}
				});
				
			} else {
				if (window.console) {
					console.log('GTM: The product link ID must be a valid product in "GTM_products". Click tracking has been disabled for this link.');
				}
				document.location = $(this).attr('href');
			}
		} else {
			if (window.console) {
				console.log('GTM: The product link must have an ID. Click tracking has been disabled for this link.');
			}
		}
	});
	
	/* GTM promo clicks */
	$('.promo-click').click(function(event) {
		event.preventDefault();

		if (this.id) {
			if ($.inArray(this.id, GTM_promos)) {
				
				url = $(this).attr('href');
				promoObj = GTM_promos[this.id];

				dataLayer.push({
					'event': 'promotionClick',
					'ecommerce': {
						'promoClick': {
							'promotions': promoObj
						}
					},
					'eventCallback': function() {
						document.location = url
					}
				});
				
			} else {
				if (window.console) {
					console.log('GTM: The promo link ID must be a valid promo in "GTM_promos". Click tracking has been disabled for this link.');
				}
				document.location = $(this).attr('href');
			}
		} else {
			if (window.console) {
				console.log('GTM: The promo link must have an ID. Click tracking has been disabled for this link.');
			}
		}
	});
	
	/* GTM checkout clicks */
	$('.checkout-step').click(function(event) {
		event.preventDefault();
		
		if (GTM_checkout) {
			
			url = $(this).attr('href');
			
			dataLayer.push({
				'event': 'checkout',
				'ecommerce': {
					'checkout': {
						'actionField': {'step': GTM_checkout.step, 'option': GTM_checkout.option, 'list': GTM_checkout.list},
						'products': GTM_checkout.products
					}
				},
				'eventCallback': function() {
					document.location = url
				}
			});
			
		} else {
			if (window.console) {
				console.log('GTM: The GTM_checkout variable doesn\'t exist. Click tracking has been disabled for this link.');
			}
			document.location = $(this).attr('href');
		}
	});
	
	/* GTM checkout option clicks */
	$('.checkout-option').click(function(event) {
		event.preventDefault();

		if (GTM_checkout) {

			dataLayer.push({
				'event': 'checkoutOption',
				'ecommerce': {
					'checkout_option': {
						'actionField': {'step': GTM_checkout.step, 'option': $(this).attr('data-gtm-option')}
					}
				}
			});
			
		} else {
			if (window.console) {
				console.log('GTM: The GTM_checkout variable doesn\'t exist. Click tracking has been disabled for this link.');
			}
		}
	});
	
});