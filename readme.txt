=== Monthly Subscription ===
Contributors: cookersdev
Tags: woocommerce subscription, monthly plan, monthly subscription
Requires at least: 3.1
Tested up to: 5.8
Requires PHP: 7.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is used for creating a monthly subscription using the WooCommerce shop and offering content with that subscription.


== Description ==
You can add a product to your WooCommerce shop to offer a subscription. Then, to provide your exclusive content, there is a shortcode to check the subscription.

For the product, choose the "Monthly subscription" type and provide your desired subscription duration. The duration of the subscription is set in months and calculated from today, or added to active subscription if applicable. There can be up to one subscription per user on the WordPress site.

For the content, there is a shortcode to check the subscription. It uses an opening tag and a closing tag. You will need both – the content in between the tags will be shown (or not shown respectively). There are four options to display content („active“, „ended“, „never“ and „inactive“). The options are passed in the opening tag using the subscription parameter.

Further information is available in the [documentation](https://cookers.at/development/monthly-subscription/).


== Installation ==
1. Download and install the plugin from WordPress dashboard. You can also upload the entire “monthly_subscription” folder to the “/wp-content/plugins/” directory.
2. Activate the plugin through the “Plugins” menu in WordPress.
3.a) Create a subscription product (using type “Monthly Subscription”).
3.b) Use the shortcodes to check the subscription in your content and display it respectively.


== Frequently Asked Questions ==

= Do users need an account for this plugin? =

You have to make sure that there is an account connected to the order at the time of payment. Users should either have an account at the time of purchasing, or create one during checkout.

There must be an account to credit the subscription to!


= Is the overlay working on every device and browser? =

Yes, it should work on every system.


= Can I use the plugin with multiple users? =

Yes, every user has his own subscription (saved in the user meta).


= Can I use multiple subscriptions on my page? =

No, there is only one subscription saved per user (at this point). You can, however, offer different subscription amounts in multiple products.


= Can I set the subscription duration freely? =

Yes, you can set the subscription duration in the respective product in the shop. You can only set months as subscription durations.


== Changelog ==
= 0.9.3 =
* Fix behavior on voucher usage: change order hooks.
* Fix namespace of DateTime class.

= 0.9.2 =
* Fix calculation for multiple subscriptions (product quantity).

= 0.9.1 =
* Add AJAX cart support for archive pages.

= 0.9.0 =
* Initial commit.