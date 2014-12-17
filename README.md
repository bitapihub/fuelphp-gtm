GTM for FuelPHP
===============

At Bit API Hub we employ both Google Tag Manager (GTM) and FuelPHP as part of our infrastructure, but we're far from the only company who takes that approach. In order to empower your GTM implementation to access critical information like what coupons customers use and what products they have in their cart, you must install code (dataLayer) to send that information to GTM.

GTM for FuelPHP provides the following advantages for your company.
* Your programmers have less code to write. They have one-line access to each type of dataLayer code.
* Your web designers need only use a class and ID on each link they wish to track.
* Use a config file for any repetitive events and variables.

Version
-------

The current version of this software is: Version 0.5 Beta.

Prerequisites
-------------

* PHP 5.3.3+
* FuelPHP 1.7.2 (May work with other versions, but only version 1.7.2 is supported at this time.)
* A [GTM account](https://tagmanager.google.com)
* A [Google Analytics Account](http://www.google.com/analytics/)
* JQuery 1.11.1 (Other versions may work as well, but are unsupported.)

Installation and Configuration
------------------------------

1. Download the sources to your fuel/packages directory. (fuel/packages/gtm)
2. Open your config.php file and add the package 'gtm' to the 'always_load' 'packages' section of the configuration. If you'd prefer load it manually, use \Package::load('gtm');. The latter is not supported.
3. Copy the analytics.php config file to your APPPATH/config directory and configure it to your liking. You must change the GTM ID to match that of your GTM container. For security reasons, consider placing a configuration file with that ID in only the appropriate environment subfolder.
4. Move the REDIST/fuelphp-gtm.js file to assets/js/fuelphp-gtm.js and include it for output. You may also minify it and/or place it within another js file on your site to lower your page load processing time and bandwidth.
5. Place the code below just after the <body> tag. (This code uses Smarty variables. If you do not use Smarty for your template engine, modify the variables. See the list of variables below the code.)

```
<!-- Start Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id={$GTM_ID}{$GTM_variables_no_js}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>{$GTM_variables}(function(w,d,s,l,i){ w[l]=w[l]||[];w[l].push({ 'gtm.start':
new Date().getTime(),event:'gtm.js' });var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$GTM_ID}');</script>{$GTM_dataLayer}
<!-- End Google Tag Manager -->
```

Variables: ``` {$GTM_ID}, {$GTM_variables_no_js}, {$GTM_variables}, {$GTM_dataLayer} ```

6. You need to process the template variables stated above. To do so, run the set_safe() method from the \View or \Theme class while processing the template containing the above code. You may use the set() method of GTM_ID's variable, but due to the nature of the other variables, you must allow the unaltered code to show. Therefore, be sure to properly screen any user input that you pass to these variables.

```
$analytics = \GTM\Analytics::instance();

[DESIRED INSTANCE]->set_safe('GTM_variables', $analytics->render(true))
->set_safe('GTM_variables_no_js', $analytics->render(true, true))
->set('GTM_ID', \Config::get('gtm.ID'))
->set_safe('GTM_dataLayer', $analytics->render());
```

7. Configure your tags as specified in the [Ecommerce Developers Guide](https://developers.google.com/tag-manager/enhanced-ecommerce). Click the 'See the Tag Configuration for this Example'
to figure out how to properly configure each tag. As it was written for the outdated version of GTM which ends/ended January 1st, 2015, take the following into consideration.

* Rules are now called triggers

* Basic Settings - Document Path: {{url path}}
You can create your own {{url path}} variable as an alias for {{page path}} by typing {{url path}} into the "Document Path" box to get the "Add new variable" option. Select URL, then the "Path" component type and name your variable "url path" then click "Create Variable."

* Firing Rule: {{event}} equals gtm.js
As that's the event that automatically fires every time a page loads, you can just set up a Universal Analytics tag that fires on every page load.

* Firing Rule: {{event}} equals productClick
If the event is anything other than gtm.js, you'll need to create a custom event by clicking "More" under the triggers options. For "Add Filters" you need to enter the event's name for the event to match, (Ex. productClick) and then click "Some Events" and set "Event equals [EVENT NAME HERE]" click continue, name your trigger to whatever you wish, and then click "Create Trigger."

Usage
-----

For more information on the various data formats for GTM, check out Google's [Enhanced Ecommerce guide](https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data).

**Product Impressions (Ex. Someone sees a list of product summaries.)**

See Google's [guide on product impressions](https://developers.google.com/tag-manager/enhanced-ecommerce#product-impressions).

``` $analytics->set_impression($array_of_impressionFieldObjects); ```

**Product Clicks**

See Google's [guide on product clicks](https://developers.google.com/tag-manager/enhanced-ecommerce#product-clicks).

1. Configure the "product_click" key of the configuration file's "defaults" key to contain the desired productFieldObjects for every possible product. If you're running a retail outlet or for some other reason you cannot possibly add a full list of products in this location, you may set the $config property of the \GTM\Analytics class to change this value in realtime.

``` \Config::set('gtm.defaults.product_click', array()); ```

2. Change your product links to include the class 'product-click' and set its ID attribute to the key name specified in the 'product_click' array. Leave the link (href) alone and the JS will take care of the rest. Your 'href' attribute is your callback URL.

**Product Detail Impressions (Ex. Someone sees the details of your product instead of a summary.)**

See Google's [guide on product detail impressions](https://developers.google.com/tag-manager/enhanced-ecommerce#details).

``` $analytics->set_product_view($array_of_productFieldObjects); ```

**Add/Remove an Item to/from the Cart**

See Google's [guide on shopping cart operations](https://developers.google.com/tag-manager/enhanced-ecommerce#cart).

The script also tracks the contents of your cart using this function so that you don't need to specify the product list for the checkout operations, including measuring purchases. To help keep things synchronized, the $cart_contents property of \GTM\Analytics is public and it contains the full cart contents. GTM for FuelPHP uses \Session for tracking your cart through your site. (GTM_cart_contents key) The session key holds the same data as the $cart_contents property.

When sending multiple product additions or removals, do not use the "quantity" parameter for GTM's product list. (Ex. In $array_of_productFieldObjects) Instead, pass the quantity through the $quantity parameter on this function so that it can track how many items are being added or removed. In doing this, the product list will have all the proper quantities for each item in the list sent to GTM.

To track a variant of a product, you must give the product a different "id" or "name" value. A simple way to do this is to append text to the id, since GTM takes a string for the "id" field. You may do the same with the "name" field if you're using that as your item identifier. (Ex. You have a moustache kit with the ID of "1001" in your database, but you need to track a kit for a handlebar moustache. You can give it the ID "1001_handlebar" and your stats will stay accurate for that variation.

**Parameters**

| Variable        | Type           | Description  |
| ------------- | ------------- | ----- |
| $array_of_productFieldObjects | array       | The array of product data for the item being added to the cart. |
| $direction                    | string      | "add" or "remove" (Case sensitive) - Default: add |
| $quantity                     | integer     | The number of items you're adding or removing from the cart. Default: 1 |

``` $analytics->set_cart($array_of_productFieldObjects, $direction, $quantity); ```

**Checkout Steps**

See Google's [guide on measuring a checkout](https://developers.google.com/tag-manager/enhanced-ecommerce#checkout).

The checkout process relies on what items are in a customer's shopping cart. (You must use the set_cart() method to set what's in the cart.) If you're selling one product per customer, such as a company marketing different package levels for a service, you don't actually need a shopping cart for customers to collect their items. Therefore, in order to track what your customers are buying from you, let the customer click the "buy now" button, then have your system process the set_cart() method in it's usual workflow, followed by the set_checkout_step() method. That will allow you to send the proper events to GTM without an unnecessary step on your interface.

There are two ways to set your one and only "option" field that you can pass with this function. (See set_checkout_options() to set further options.) First, you can set the option via PHP, by passing the second parameter below as the value for the option field. (The first parameter is the step number.)

``` $analytics->set_checkout_step(1, 'my option'); ```

The second option you have to set that option is to use JS to set it using an "onclick" event or the like. The JS version will override the option set by PHP.

``` <a href="#" onclick="GTM_checkout['option']='my option';">Change the Option</a> ```

**Extra Checkout Options**

See Google's [guide on measuring a checkout](https://developers.google.com/tag-manager/enhanced-ecommerce#checkout).

When you need to send more than one option, you can do so through PHP or though JS. If you only have one option, you may set $options to a string. If you have multiple options to set, create an array of options. (Ex. array("option 1", "option 2");) This method will use the same step number as set_checkout_step() above.

``` $analytics->set_checkout_options($options); ```

To let your users set checkout options as they click on stuff, use the class "checkout-option" with data-gtm-option set to the option value. Please note that the JS version for the main checkout event will only set the option as the user clicks around. The JS version below will send to GTM every option the user clicks on. Plan accordingly.

``` <a href="#" class="checkout-option" data-gtm-option="my option">Send "my option" to GTM</a> ```

**Transactions**

See Google's [guide on measuring purchases](https://developers.google.com/tag-manager/enhanced-ecommerce#purchases).

$array_of_actionFieldObjects must contain at least the transaction id. The list of products is set from the add/remove items method, set_cart().

NOTE: Once you've rendered the JS scripts using the render() method, the cart contents are destroyed.

``` $analytics->set_checkout_options($array_of_actionFieldObjects); ```

**Refunds**

See Google's [guide on measuring refunds](https://developers.google.com/tag-manager/enhanced-ecommerce#refunds).

There are two types of refunds, partial refunds, and full refunds. When you process a full refund, you must only send the $transaction_id for the transaction you're refunding. If you need to issue a partial refund, load each product into the transaction you're refunding by passing $array_of_productFieldObjects in the call for each product. (You'll write the line of code for each product you're refunding.)

Unfortunately you may only process one refund per page load. If you're trying to do bulk refund processing, for example when finalizing your daily transactions at the end of the day, you're going to find it difficult. This issue is a limitation imposed by Google's system. Refunds are not event based and therefore they must be sent immediately to GTM with the gtm.js event. Therefore, if you add multiple refunds, you'll end up overwriting your refund code before GTM even processes the first refund code.

``` $analytics->set_refund($transaction_id, $array_of_productFieldObjects); ```

**Promotion Impressions**

See Google's [guide on promotion impressions](https://developers.google.com/tag-manager/enhanced-ecommerce#promo-impressions).

``` $analytics->set_promo_view($array_of_promoFieldObjects); ```

**Promotion Clicks**

See Google's [guide on promotion clicks](https://developers.google.com/tag-manager/enhanced-ecommerce#promo-clicks).

1. Configure the "promo_click" key of the configuration file's "defaults" key to contain the desired promoFieldObjects for every possible promo. If for some other reason you cannot possibly add a full list of promos in this location, you may set the $config property of the \GTM\Analytics class to change this value in realtime.

``` \Config::set('gtm.defaults.promo_click', array()); ```

Always Required
---------------

After you've set your script to track everything, you must place the generated code in the appropriate location with the render() method. The script is thrice rendered. See the configuration section above for a description of how to use the render() method.

Add Custom Data
----------------

See Google's [guide on page variables and events](https://developers.google.com/tag-manager/devguide#events).

**GTM Page Variables**

You may wish to set variables at the top of the page to aid in your GTM marketing experience. Let's say that you'd like to run a popup for returning customers who have never purchased something from you, but they keep window shopping. You'd like to give them a coupon to help them to decide to make a purchase. To do that, you'll need to use a GTM variable. Let's call this variable JimBob just to do it. Now to set JimBob, we need to run the following code.

``` $analytics->set_variables(array('JimBob` => 1)); ```

Now JimBob is set to an integer value of 1. In GTM, you can now track to see if that variable is set and fire a tag based on JimBob. Isn't JimBob awesome? :) If you only set variables in your config file, you don't need to add any extra code. Do not set events or arrays here! The page variables are also set in the noscript version. See the next section for that.

**GTM events to fire with gtm.js**

GTM only does stuff when an event is fired. Certain code must be added to the dataLayer before the event gtm.js fires. (GTM fires gtm.js automatically.) As soon as gtm.js fires, everything on the dataLayer is sent to GTM for analysis. Impression data and product detail views are some items that can be added before gtm.js fires. You may need to add other "non-events" to GTM at some point and that's where this code comes in. Set the 'non_events' key in the config file to any "non-events" you wish to add by default or use the following code to add them dynamically.

``` $analytics->set_non_event($array_of_data); ```

$array_of_data is placed directly into the root of the dataLayer, not in a sub-key. You can set things on the sub-keys and if they're later changed, they will be merged with array_replace_recursive().

**Custom Events**

When you need to write your own events, that's easy. Try this out. It's set at the root of the dataLayer variable, too.

``` $analytics->set_event(array('event' => 'my_event', 'ecommerce' => array('my_key' => 'my_value'))); ```

The only required part is to have a key named 'event' set to the name of your event. If you have events you'd like called on every page, use the config file's 'event' key.

Troubleshooting
---------------

When your settings aren't taking effect or files can't be found, clear your FuelPHP cache. (APPPATH/cache)

Credits
-------

This package contains [code licensed by Google](https://developers.google.com/tag-manager/enhanced-ecommerce) under the Apache 2.0 License. It also makes reference to code contained within FuelPHP 1.7.2, which is [licensed under the MIT license](http://fuelphp.com/docs/license.html).
