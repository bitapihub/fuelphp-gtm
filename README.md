GTM for FuelPHP
===============

**NOTE: This project is currently a WIP and will be made available shortly. (Winin a few days.)**

At Bit API Hub we employ both Google Tag Manager (GTM) and FuelPHP as part of our infrastructure, but we're far from the only company who takes that approach. In order to empower your GTM implementation to access critical information like what coupons customers use and what products they have in their cart, you must install code (dataLayer) to send that information to GTM.

GTM for FuelPHP provides the following advantages for your company.
* Your programmers have less code to write. They have one-line access to each type of dataLayer code.
* Your web designers need only use a class and ID on each link they wish to track.
* Use a config file for any repetitive events and variables.

Prerequisites
-------------

* PHP 5.3.3+
* FuelPHP 1.7.2 (May work with other versions, but only version 1.7.2 is supported at this time.)
* A [GTM account](https://tagmanager.google.com)
* A [Google Analytics Account](http://www.google.com/analytics/)
* JQuery 1.11.1 (Other versions may work as well, but are unsupported.)

Installation and Configuration
------------------------------

1. Download the sources to your fuel/packages directory.
2. Copy the analytics.php config file to your APPPATH/config directory and configure it to your liking. You must change the GTM ID to match that of your GTM container. For security reasons, consider placing a configuration file with that ID in only the appropriate environment subfolder.
3. Move the REDIST/fuelphp-gtm.js file to assets/js/fuelphp-gtm.js and include it for output. You may also minify it and/or place it within another js file on your site to lower your page load processing time and bandwidth.
4. Place the code below just after the <body> tag. (This code uses Smarty variables. If you do not use Smarty for your template engine, modify the variables. See the list of variables below the code.)

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

5. You need to process the template variables stated above. To do so, run the set_safe() method from the \View or \Theme class while processing the template containing the above code. You may use the set() method of GTM_ID's variable, but due to the nature of the other variables, you must allow the unaltered code to show. Therefore, be sure to properly screen any user input that you pass to these variables.

```
$analytics = \GTM\Analytics::instance();

[DESIRED INSTANCE]->set_safe('GTM_variables', $analytics->render(true))
->set_safe('GTM_variables_no_js', $analytics->render(true, true))
->set('GTM_ID', $analytics->config['ID'])
->set_safe('GTM_dataLayer', $analytics->render());
```

6. Configure your tags as specified in the [Ecommerce Developers Guide](https://developers.google.com/tag-manager/enhanced-ecommerce). Click the 'See the Tag Configuration for this Example'
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

``` $analytics->config['defaults']['product_click'] = array() ```

2. Change your product links to include the class 'product-click' and set its ID attribute to the key name specified in the 'product_click' array. Leave the link (href) alone and the JS will take care of the rest. Your 'href' attribute is your callback URL.

**Product Detail Impressions (Ex. Someone sees the details of your product instead of a summary.)**

See Google's [guide on product detail impressions](https://developers.google.com/tag-manager/enhanced-ecommerce#details).

``` $analytics->set_product_view($array_of_productFieldObjects); ```

**Add an Item to the Cart**

See Google's [guide on shopping cart operations](https://developers.google.com/tag-manager/enhanced-ecommerce#cart).

``` $analytics->set_cart($array_of_productFieldObjects); ```

**Remove an Item from the Cart**

See Google's [guide on shopping cart operations](https://developers.google.com/tag-manager/enhanced-ecommerce#cart).

NOTE: The 'remove' parameter is case-sensitive.

``` $analytics->set_cart($array_of_productFieldObjects, 'remove'); ```

**Promotion Impressions**

See Google's [guide on promotion impressions](https://developers.google.com/tag-manager/enhanced-ecommerce#promo-impressions).

``` $analytics->set_promo_view($array_of_promoFieldObjects); ```

**Promotion Clicks**

See Google's [guide on promotion clicks](https://developers.google.com/tag-manager/enhanced-ecommerce#promo-clicks).

1. Configure the "promo_click" key of the configuration file's "defaults" key to contain the desired promoFieldObjects for every possible promo. If for some other reason you cannot possibly add a full list of promos in this location, you may set the $config property of the \GTM\Analytics class to change this value in realtime.

``` $analytics->config['defaults']['promo_click'] = array(); ```

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

Credits
-------

This package contains [code licensed by Google](https://developers.google.com/tag-manager/enhanced-ecommerce) under the Apache 2.0 License. It also makes reference to code contained within FuelPHP 1.7.2, which is [licensed under the MIT license](http://fuelphp.com/docs/license.html).
