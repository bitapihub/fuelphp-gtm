<?php
/**
 *  @license
 *  Copyright 2014 Bit API Hub
 *  
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *  
 *  http://www.apache.org/licenses/LICENSE-2.0
 *  
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *  
 *  Generate analytics data to sent to GTM. (These are just templates.)
 *  @link https://developers.google.com/tag-manager/enhanced-ecommerce
 *  @link https://github.com/BitAPIHub/FuelPHP-GTM
 */

/**
 * Copy this file to your APPPATH/config directory and modify it there.
 */
return array(
		
	/**
	 * General configuration
	 */
	
	/**
	 * Your GTM ID from Google
	 */
	'ID'	=> '[ENTER YOUR GTM ID]',
	
	/**
	 * Enhanced Ecommerce
	 */
	
	/**
	 * For all those currency code fields :)
	 */
	'currency_code'	=> 'USD',
	
	/**
	 *  Set the page URI to a human readable page name. This key is used for EVERYTHING that uses the "list"
	 *  property. This property is always the name of the page. If a page is not specified here, the script
	 *  will use the URL path for this field.
	 *  
	 *  @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#impression-data
	 */
	'list'	=> array(
	
		'/'	=> 'Home Page'
	
	),
	
	/**
	 * If the page isn't listed with the 'list' key above, the script will use the URL path for the 'list' option.
	 * Should we remove a portion of the URL when doing so? If so, set the portion to remove. If the URL result is
	 * empty, we will use "/".
	 */
	'remove_from_url'	=> '',
	
	/**
	 * Should we include the query string in the output for URLs not listed with 'list'?
	 */
	'include_query_string'	=> false,
		
	/**
	 * The script manages what items are in the cart based on cart additions and removals. In order to keep
	 * a running list, it must use either the "name" of the product, or its "id" field. Set this to "id" or
	 * "name" to tell the script what to use to track your cart's contents. The contents of this key must be
	 * unique to each product and it must be present.
	 * 
	 * @link https://developers.google.com/tag-manager/enhanced-ecommerce#checkout
	 */
	'cart_products_identifier'	=> 'id',
	
	/**
	 * Default values for every type of analytical data
	 *
	 * @link https://developers.google.com/tag-manager/enhanced-ecommerce
	 * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data
	 */
	'defaults'	=> array(
	
		/**
		 * Only items common to every impression. These are merged with each "impressions" section.
		 * 
		 * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#impression-data
		 */
		'impressions'	=> array(
				
			// 'category'	=> 'Core Products',
				
		),
	
		/**
		 * Only items common to every product view. These are merged with each "products" section.
		 * 
		 * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-data
		 */
		'product_view'	=> array(
				
			// 'category'	=> 'Core Products',
				
		),
	
		/**
		 * Only items common to every cart change. These are merged with each "products" section.
		 * 
		 * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-data
		 */
		'cart'	=> array(
				
			// 'category'	=> 'Core Products',
				
		),
	
		/**
		 * All possible values for productFieldObject (List of your products) You may set the $config property of the
		 * \GTM\Analytics class to change this value in realtime.
		 * Ex. $analytics->config['defaults']['product_click'] = array()
		 * 
		 * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-data
		 */
		'product_click'	=> array(
			
			/*
			'product-free'	=> array(
	
				'name'		=> 'Free for Life',
				'category'	=> 'Core Products',
				'price'		=> 0.00
	
			),
			'product-tier1'	=> array(
	
				'name'		=> 'Tier 1',
				'category'	=> 'Core Products',
				'price'		=> 0.00
	
			),
			'product-tier2'	=> array(
	
				'name'		=> 'Tier 2',
				'category'	=> 'Core Products',
				'price'		=> 0.00
	
			)
			*/
				
		),
	
		/**
		 * All possible values for promoFieldObject (List of your promos) You may set the $config property of the
		 * \GTM\Analytics class to change this value in realtime.
		 * Ex. $analytics->config['defaults']['product_click'] = array()
		 * 
		 * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#promotion-data
		 */
		'promo_click'	=> array(
			
			/*
			'promo-test'	=> array(
	
				'id'		=> 'TEST_TOP',
				'name'		=> 'Test Top',
				'creative'	=> 'test_banner',
				'position'	=> 'header'
	
			),
				
			'promo-test2'	=> array(
	
				'id'		=> 'TEST_TOP2',
				'name'		=> 'Test Top2',
				'creative'	=> 'test_banner2',
				'position'	=> 'footer'
	
			),
				
			'promo-test3'	=> array(
	
				'id'		=> 'TEST_TOP3',
				'name'		=> 'Test Top3',
				'creative'	=> 'test_banner3',
				'position'	=> 'sidebar'
	
			),
			*/
		),
		
		/**
		 * Any variables you'd like to set for your page before the GTM code loads. Don't add events here. The non-js
		 * version uses this key and cannot use events. Use the non_events key or set_non_event() method for non_events
		 * or use the events key or the set_event() method for custom events.
		 * 
		 * @link https://developers.google.com/tag-manager/devguide#multipush
		 */
		'variables'	=> array(
			
			// 'testvar'	=> 'testval'
			
		),
		
		/**
		 * All calls that do not have a specified event (or are specified with 'event': 'gtm.js') must be added to
		 * this key or they will never be used by GTM. You may of course omit any non-event code handled by this
		 * package.
		 * 
		 * @link https://productforums.google.com/d/msg/tag-manager/albJCkoKhFk/gVx23nIqatYJ
		 * @link https://productforums.google.com/d/msg/tag-manager/Y4-0cizIosw/knE7u86VwHcJ
		 */
		'non_events'	=> array(
			
			/*
			'test'	=> array(
			
				'test1'	=> 'test2'
				
			),
			
			'ecommerce'	=> array(
				
				'my_ecommerce'	=> 'my_val'
				
			)
			*/
			
		),
		
		'events'	=> array(
		
			/*
			'my_event_name'	=> array(
				
				'ecommerce'	=> array(
	
					'testevent'	=> 'testval'
					
				)
				
			)
			*/
		
		)
	
	)
	
);