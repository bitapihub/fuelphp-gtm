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
	
});