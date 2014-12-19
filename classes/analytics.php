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
 *  Generate analytics data to sent to GTM
 *  @link https://developers.google.com/tag-manager/enhanced-ecommerce
 *  @link https://github.com/BitAPIHub/FuelPHP-GTM
 */

namespace GTM;

class Analytics
{
	/** 
	 * @var array $_cart_contents The current contents of the cart
	 */
	public $cart_contents = array();
	
	/** 
	 * @var object $instances The Analytics object instances
	 * @access protected
	 */
	protected static $instances = array();
	
	/** 
	 * @var array $_impressions All product impressions for the page
	 * @access private
	 */
	private $_impressions = array();
	
	/** 
	 * @var array $_product_view All product detail views for the page
	 * @access private
	 */
	private $_product_view = array();
	
	/** 
	 * @var array $_cart The list of items being added to or removed from the cart
	 * @access private
	 */
	private $_cart = array();
	
	/** 
	 * @var integer $_step The step number of the checkout process
	 * @access private
	 */
	private $_step = 0;
	
	/** 
	 * @var string $_step_option A user selected option
	 * @access private
	 */
	private $_step_option = null;
	
	/** 
	 * @var array $_checkout_options A user selected option
	 * @access private
	 */
	private $_checkout_options = array();
	
	/** 
	 * @var array $_transaction The array of transaction data
	 * @access private
	 */
	private $_transaction = array();
	
	/** 
	 * @var array $_refunded_products The array of products receiving a refund, formatted for GTM
	 * @access private
	 */
	private $_refunded_products = array();
	
	/** 
	 * @var array $_refunds The array of refund data for GTM
	 * @access private
	 */
	private $_refund = array();
	
	/** 
	 * @var array $_promo_view The list of promo view items on the page
	 * @access private
	 */
	private $_promo_view = array();

	/**
	 * @var string $_events The composed custom events ready for display
	 * @access private
	 */
	private $_events = null;
	
	/**
	 * @var array $_variables The list of variables for GTM to rely on for tag firing and such
	 * @access private
	 */
	private $_variables = array();
	
	/**
	 * @var array $_non_events The list of events to fire with the gtm.js event
	 * @access private
	 */
	private $_non_events = array();
	
	/**
	 * @var string $_list The name to use for the GTM list properties
	 * @access private
	 */
	private $_list = null;
	
	/**
	 * Pull an instance out of thin air. If the named instance does not exist, it will be created and returned.
	 * 
	 * @param string $name The name of the instance to return
	 * @return object The requested instance or a new instance if the desired one does not exist.
	 */
	public static function instance($name = '_default_')
	{
		if (!array_key_exists($name, static::$instances)) {
			
			static::$instances[$name] = static::forge(); 
			
		}
		
		return static::$instances[$name];
	}
	
	/**
	 * Generate a new instance.
	 * 
	 * @return \GTM\Analytics
	 */
	public static function forge()
	{
		return new static;
	}
	
	/**
	 * Load the default configuration settings
	 */
	public function __construct()
	{
		\Config::load('gtm', true);
		
		// Set the list field for any analytics data for this page
		$this->_list = $this->_get_list();
	}
	
	/**
	 * Generates a list of impression data as specified by
	 * @link https://developers.google.com/tag-manager/enhanced-ecommerce#product-impressions
	 * 
	 * @throws Exception If the product name or ID is missing
	 * @param array $product	The product to add to the list
	 * 							(Must have at least the name or id of the product)
	 */
	public function set_impression(array $product)
	{
		// Check the product
		if (empty($product['id']) && empty($product['name'])) {
		
			throw new \Exception('The product "name" or "id" key is required for the set_impression() method.');
		
		}
		
		// Create the wrapping if we don't yet have an impression.
		if (empty($this->_impressions)) {
			
			$this->_impressions = array(
				
				'ecommerce'	=> array(

					'currencyCode'	=> \Config::get('gtm.currency_code')
					
				)
				
			);
			 
		}
		
		// Add the impression and merge any data from the config that's common to all of them.
		$impression = array_merge(
			
			\Config::get('gtm.defaults.impressions'),
			$product
			
		);
		
		// The page on the site where the impression was set
		$impression['list'] = $this->_list;
		
		$this->_impressions['ecommerce']['impressions'][] = $impression;
	}
	
	/**
	 * Generates a list of product view data as specified by
	 * @link https://developers.google.com/tag-manager/enhanced-ecommerce#details
	 *
	 * @throws Exception If the product name or ID is missing
	 * @param array $product	The product to add to the list
	 * 							(Must have at least the name or id of the product)
	 */
	public function set_product_view(array $product)
	{
		// Check the product
		if (empty($product['id']) && empty($product['name'])) {
		
			throw new \Exception('The product "name" or "id" key is required for the set_product_view() method.');
		
		}
		
		// Create the wrapping if we don't yet have a product view.
		if (empty($this->_product_view)) {
				
			$this->_product_view = array(
		
				'ecommerce'	=> array(
					
					'detail'	=> array(
						
						'actionField'	=> array(

							'list'	=> $this->_list
							
						)
						
					)
						
				)
		
			);
		
		}
		
		// Add the product view and merge any data from the config that's common to all of them.
		$product_view = array_merge(
				
			\Config::get('gtm.defaults.product_view'),
			$product
				
		);
		
		$this->_product_view['ecommerce']['detail']['products'][] = $product_view;
	}
	
	/**
	 * Generates a list of products being added to or removed from the cart
	 * @link https://developers.google.com/tag-manager/enhanced-ecommerce#cart
	 *
	 * @throws Exception If the product name or ID is missing or the gtm.cart_products_identifier is missing
	 * @param array $product	The product to add to the list
	 * 							(Must have at least the name or id of the product)
	 * @param string $direction	Set this parameter to "add" or "remove" (case sensitive)
	 * @param int $quantity		Set this parameter to a numeric quantity.
	 */
	public function set_cart(array $product, $direction = 'add', $quantity = 1)
	{
		// Check the product
		if (empty($product['id']) && empty($product['name'])) {
				
			throw new \Exception('The product "name" or "id" key is required for the set_cart() method.');
				
		}
		
		$session = \Session::instance();
		$cart_id = \Config::get('gtm.cart_products_identifier');
		
		// If the identifier does not exist, we throw an error.
		if (empty($product[$cart_id])) {
				
			throw new \Exception('The product\'s "'.$cart_id.'" key is required for the set_cart() method.
									Config: gtm.cart_products_identifier = "'.$cart_id.'"');
				
		}
		
		// Create the wrapping if we don't yet have a list.
		if (empty($this->_cart[$direction])) {
			
			$this->_cart[$direction] = array(
				
				'event'	=> $direction === 'add' ? 'addToCart' : 'removeFromCart',
		
				'ecommerce'	=> array(
					
					'currencyCode'	=> \Config::get('gtm.currency_code')
						
				)
		
			);

			$this->_cart[$direction]['ecommerce'][$direction]['actionField']['list'] = $this->_list;
		
		}
		
		// Set the quantity
		$product['quantity'] = $quantity;
		
		// Add the product data and merge any data from the config that's common to all of them.
		$cart = array_merge(
				
			\Config::get('gtm.defaults.cart'),
			$product
				
		);
		
		$this->_cart[$direction]['ecommerce'][$direction]['products'][] = $cart;
		
		////////////////////////////////
		// Manage the cart's contents
		
		// Keep the cart contents in the session to keep it persistent.
		if (!empty($session->get('GTM_cart_contents'))) {
			
			$this->cart_contents = $session->get('GTM_cart_contents');
			
		}
		
		// Add items to the cart
		if ($direction === 'add') {
			
			// This is the first occurrence of this product.
			if (empty($this->cart_contents[$product[$cart_id]])) {
		
				$this->cart_contents[$product[$cart_id]] = $product;
		
			// Add the new quantity to the cart.
			} else {
		
				$this->cart_contents[$product[$cart_id]]['quantity'] += $quantity;
		
			}
		
		// Remove items from the cart
		} else {
				
			// If the product exists in the cart, remove it.
			if (!empty($this->cart_contents[$product[$cart_id]])) {
		
				$current_quantity = $this->cart_contents[$product[$cart_id]]['quantity'];
		
				// Did we remove all of the item?
				if ($current_quantity - $quantity <= 0) {
						
					unset($this->cart_contents[$product[$cart_id]]);
				
				// We still have some of this product.
				} else {
						
					$this->cart_contents[$product[$cart_id]]['quantity'] = $current_quantity - $quantity;
						
				}
		
			}
				
		}
		
		// Save the cart contents
		$session->set('GTM_cart_contents', $this->cart_contents);
	}
	
	/**
	 * Generates the code to show for checkout management
	 * @link https://developers.google.com/tag-manager/enhanced-ecommerce#checkout
	 *
	 * @param integer $step		The step number of the checkout process
	 * @param string $option	A user selected option
	 */
	public function set_checkout_step($step = 1, $option = null)
	{
		$this->_step		= $step;
		$this->_step_option	= $option;
	}
	
	/**
	 * Generates the code to show for checkout options
	 * @link https://developers.google.com/tag-manager/enhanced-ecommerce#checkout
	 *
	 * @param string $option	A user selected option
	 */
	public function set_checkout_options($options = array())
	{
		if (is_string($options)) {
			
			$options = array($options);
			
		}
		
		foreach ($options as $option_key => $option_value) {
			
			$this->_checkout_options[] = array(
			
				'event'		=> 'checkoutOption',
				'ecommerce'	=> array(
			
					'checkout_option'	=> array(
			
						'actionField'	=> array(
			
							'step'		=> $this->_step,
							'option'	=> $option_value,
							'list'		=> $this->_list
			
						)
			
					)
			
				)
			
			);
			
		}
	}
	
	/**
	 * Generates the JS code for a transaction (Purchase)
	 * @link https://developers.google.com/tag-manager/enhanced-ecommerce#purchases
	 *
	 * @throws Exception If the transaction ID is missing
	 * @param array $transaction	The transaction data for the purchase
	 * 								(Must have at least the transaction ID)
	 */
	public function set_transaction(array $transaction)
	{
		if (empty($transaction['id'])) {
			
			throw new \Exception('The transaction ID is required for the set_transaction() method.');
			
		}
		
		$products_list			= $this->_format_products_list();
		$transaction['list']	= $this->_list;
				
		$this->_transaction = array(
	
			'ecommerce'	=> array(
				
				'purchase'	=> array(
					
					'actionField'	=> $transaction,
					'products'		=> $products_list
					
				)
					
			)
	
		);
	}
	
	/**
	 * Generates the JS code for a transaction refund (full or partial)
	 * @link https://developers.google.com/tag-manager/enhanced-ecommerce#refunds
	 *
	 * @throws Exception If the product's "id" or "quantity" key is missing and a product was specified
	 * @param mixed $transaction_id	The ID of transaction for the refund
	 * @param array $products		The array of products being refunded.
	 * 								(Must have at least the product ID and quantity.)
	 */
	public function set_refund($transaction_id, array $products = array())
	{
		$transaction_id['list'] = $this->_list;
		
		/////////////////////////
		// Full refunds
		if (empty($products)) {
			
			$this->_refund = array(
			
				'ecommerce'	=> array(
						
					'refund'	=> array(
			
						'actionField'	=> $transaction_id
			
					)
			
				)
			
			);
			
			// This one is a full refund, so we stop here. 
			return;
			
		}
		
		/////////////////////////
		// Partial refunds
		if (empty($products['id']) || empty($products['quantity'])) {
			
			throw new \Exception('The product "id" and "quantity" keys are required for the set_refund() method.');
			
		}
		
		// Create the wrapping if we don't yet have a refund entry
		if (empty($this->_refunded_products)) {
			
			$this->_refunded_products = array(
			
				'ecommerce'	=> array(
						
					'refund'	=> array(
			
						'actionField'	=> $transaction_id
			
					)
			
				)
			
			);
			 
		}
		
		$this->_refunded_products['ecommerce']['refund']['products'][] = $products;
	}
	
	/**
	 * Generates a list of promo views shown on the page
	 * @link https://developers.google.com/tag-manager/enhanced-ecommerce#promo-impressions
	 *
	 * @throws Exception If the promo name or ID is missing
	 * @param array $promo	The promo to add to the list
	 * 						(Must have at least the name or id of the promo)
	 */
	public function set_promo_view(array $promo = array())
	{
		// Check the promo
		if (empty($promo['id']) && empty($promo['name'])) {
		
			throw new \Exception('The promo "name" or "id" key is required for the set_promo_view() method.');
		
		}
		
		$this->_promo_view['ecommerce']['promoView']['promotions'][] = $promo;
	}
	
	/**
	 * Generates a custom event
	 * @link https://developers.google.com/tag-manager/devguide#events
	 * 
	 * @param array $data	The event data to be set in the root of dataLayer.
	 */
	public function set_event(array $data = array())
	{
		// Create the wrapping if we don't yet have an impression.
		if (empty($this->_events)) {
			
			// Load the events from the config if we have any. If it's empty, this assertion will fail.
			if ($config_events = \Config::get('gtm.defaults.events')) {
				
				foreach ($config_events as $event_name	=> $event_code) {
				
					$event_code['event'] = $event_name;
					$this->_events .= 'dataLayer.push('.json_encode($event_code).');';
				
				}
				
			}
			 
		}
		
		// If we aren't setting an event, but we just want the config events, we don't run this.
		if (!empty($data)) {
			
			$this->_events .= 'dataLayer.push('.json_encode($data).');';
			
		}
	}
	
	/**
	 * Generates a list of page variables for GTM to rely on during it's work flow
	 * @link https://developers.google.com/tag-manager/devguide#datalayer
	 *
	 * @param array $variables	One or more key/value pairs for the variable list
	 */
	public function set_variables(array $variables = array())
	{
		$this->_variables = array_replace_recursive(\Config::get('gtm.defaults.variables'), $this->_variables, $variables);
	}
	
	/**
	 * Generates a list of non-events to fire with gtm.js
	 * @link https://developers.google.com/tag-manager/devguide#datalayer
	 *
	 * @param array $non_events	One or more key/value pairs for the non-event list
	 */
	public function set_non_event(array $non_events = array())
	{
		$this->_non_events = array_replace_recursive(\Config::get('gtm.defaults.non_events'), $this->_non_events, $non_events);
	}
	
	/**
	 * Generate all of the GTM dataLayer code from what the script has gathered.
	 * 
	 * @param bool $page_variables	Set to true to render the page variables for GTM to rely on
	 * @param bool $no_js			Set to true to render the non-js version of $page_variables
	 * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#impression-data
	 * 
	 * @return string The entire dataLayer contents rendered as inline JS through @see \Asset::js()
	 */
	public function render($page_variables = false, $no_js = false)
	{
		// Set the stuff from the config.
		$this->set_variables();
		$this->set_non_event();
		$this->set_event();
		
		$session = \Session::instance();
		
		///////////////////////////////////////////////////////
		// Generate the page variables and non-event code when requested.
		if ($page_variables === true) {
			
			// Just the variables for the no JS version
			if ($no_js === true) {
				
				return empty($this->_variables) ? null : '&'.\Uri::build_query_string($this->_variables);
				
			}
			
			///////////////////////////////////////////////////////
			// Page variables and events
			$non_events = empty($this->_variables) ? array() : $this->_variables;
			
			if (!empty($this->_non_events)) {
				
				$non_events = array_replace_recursive($non_events, $this->_non_events);
				
			}
			
			///////////////////////////////////////////////////////
			// If we have impressions, generate that code.
			if (!empty($this->_impressions)) {
				
				$non_events = array_replace_recursive($non_events, $this->_impressions);
				
			}
			
			///////////////////////////////////////////////////////
			// If we have product details views, generate that code.
			if (!empty($this->_product_view)) {
				
				$non_events = array_replace_recursive($non_events, $this->_product_view);
				
			}
			
			///////////////////////////////////////////////////////
			// If we have promo views to process, generate that code.
			if (!empty($this->_promo_view)) {
	
				$non_events = array_replace_recursive($non_events, $this->_promo_view);
				
			}
			
			///////////////////////////////////////////////////////
			// If we have a transaction to process, generate that code.
			if (!empty($this->_transaction)) {
					
				$non_events = array_replace_recursive($non_events, $this->_transaction);
				
				// Remove the cart contents as we've just completed the purchase.
				$this->cart_contents = array();
				$session->set('GTM_cart_contents', array());
					
			}
			
			///////////////////////////////////////////////////////
			// If we have a refund to process, generate that code.
			
			// Partial Refund
			if (!empty($this->_refunded_products)) {
					
				$non_events = array_replace_recursive($non_events, $this->_refunded_products);
			
			// Full Refund
			} elseif (!empty($this->_refund)) {
					
				$non_events = array_replace_recursive($non_events, $this->_refund);
				
			}
			
			return empty($non_events) ? null : 'dataLayer = ['.json_encode($non_events).'];';
			
		}
		
		// No events by default, but if we have custom events, we set those now.
		$events = $this->_events;
		
		///////////////////////
		// Product click values
		$product_click_config = \Config::get('gtm.defaults.product_click');
		
		if (!empty($product_click_config)) {
			
			$product_click_config['list'] = $this->_list;
			
			$events .= 'var GTM_products = '.json_encode($product_click_config).';';
			
		}
		
		///////////////////////////////////////////////////////
		// If we have add/remove cart items to process, generate that code.
		if (!empty($this->_cart)) {
			
			foreach ($this->_cart as $cart_direction => $cart_array) {

				$events .= 'dataLayer.push('.json_encode($this->_cart[$cart_direction]).');';
				
			}
			
		}
		
		///////////////////////
		// Promo click values
		$promo_click_config = \Config::get('gtm.defaults.promo_click');
		
		if (!empty($promo_click_config)) {
			
			$events .= 'var GTM_promos = '.json_encode($promo_click_config).';';
			
		}
		
		///////////////////////
		// Checkout Steps
		
		if (!empty($session->get('GTM_cart_contents'))) {
		
			$this->cart_contents = $session->get('GTM_cart_contents');
		
		}
		
		// If the cart is empty, there's no real point in tracking the checkout. ;)
		if ($this->_step > 0 && !empty($this->cart_contents)) {
			
			// We no longer need the tracking ID data for cart management and we must remove it so
			// that we can format it properly for GTM.
			$cart_products = $this->_format_products_list();
			
			$checkout_vars = array(
				
				'step'		=> $this->_step,
				'option'	=> $this->_step_option,
				'list'		=> $this->_list,
				'products'	=> $cart_products
				
			);
			
			$events .= 'var GTM_checkout = '.json_encode($checkout_vars).';';
			
		}
		
		///////////////////////
		// Checkout Options
		
		// Hammer the GTM server with events... I really wish they would let people group them
		// together in one call. I don't like having to do it this way.
		if (!empty($this->_checkout_options)) {

			foreach ($this->_checkout_options as $opt_key => $checkout_option) {
					
				$events .= 'dataLayer.push('.json_encode($checkout_option).');';
				
			}
			
		}
		
		return empty($events) ? null : '<script type="text/javascript">'.$events.'</script>';
	}
	
	/**
	 * ======================================================================
	 * 								PRIVATES
	 * ======================================================================
	 */
	
	/**
	 * Format the cart contents for GTM compatability.
	 * 
	 * @return array The array of products in GTM format
	 */
	private function _format_products_list()
	{
		$cart_products = array();
		foreach ($this->cart_contents as $cart_identifier => $product_data) {
		
			$cart_products[] = $product_data;
		
		}
		
		return $cart_products;
	}
	
	/**
	 * @return The name for the URI as given in the configuration file, or the URI if it wasn't named
	 */
	private function _get_list()
	{
		$uri = $this->_get_uri(\Config::get('gtm.include_query_string'), \Config::get('gtm.remove_from_url'));
		return array_key_exists($uri, \Config::get('gtm.list')) ? \Config::get('gtm.list.'.$uri) : $uri;
	}
	
	/**
	 * Get the URI without loader/index and optionally including the query string
	 *
	 * @param bool $with_query		Set to true to return the query string as well.
	 * @param string $with_query	Set to any portion of the URL you wish to remove from the result.
	 * @return string The current URI
	 */
	private function _get_uri($with_query = false, $remove = null)
	{
		$query_string = null;
		if ($with_query === true) {
	
			$query_string = '/?'.$_SERVER['QUERY_STRING'];
	
		}
		 
		$uri = str_replace($remove, '', \Uri::string()).$query_string;
		 
		return empty($uri) ? '/' : $uri;
	}
}
