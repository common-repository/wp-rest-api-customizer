=== WP Rest API Customizer ===
Contributors: amarilindra, ikvaesolutions
Donate link: https://www.paypal.me/geekdashboard/15
Tags: wp rest api, wp rest api 2, rest api customizer, modify wp rest api end points, custom wp rest api routs, rest api, api
Requires at least: 4.7
Tested up to: 6.0.2
Stable tag: 1.1
Version: 1.1
Requires PHP: 5.2.4+
License: GPLv2 or later

WP Rest API Customizer can add or remove objects and customize the rest API endpoints response.

== Description ==

WP Rest API Customizer provides an easy to use interface to modify WP rest API response. Sometimes you may need to remove few unnecessary JSON objects from the response to improver performance of your application. This plugin can do it for you.

Major features in WP Rest API Customizer include:

* Adds Large, Medium and Small featured image URL's to JSON response.
* Enables adding comments from third party clients without user authentication.
* Add's a new route to get post id from post url.
* You can unset any object you don't need from /wp-json/wp/v2/posts end point.

== Installation ==

The quickest method for installing the WP Rest API Customizer is:

1. Visit Plugins -> Add New in the WordPress dashboard.
1. Search for `WP REST API Customizer` without quotes in search box.
1. Click "Install".
1. Finally click `Activate Plugin`.
1. Go to Settings -> WP Rest API Customizer and check the desired fields to modify the defualt WP API response.

If you would prefer to do things manually then follow these instructions:

1. Extract the dowloaded plugin file.
1. Upload the `wp-rest-api-customizer` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Settings -> WP Rest API Customizer and check the desired fields to modify the defualt WP API response.


== Frequently Asked Questions ==

= Will my app performance increase if I uncheck all fields? =
No, you should not uncheck everything without understanding their actual usage. Few of them might be used by your theme, other plugins are even your app.  

= I enabled POST ID from POST URL custom route, how to use it? =
By enabling it, you created a new route. Send a GET request to https://www.geekdashboard.com/wp-json/wp-rest-api-customizer/url-to-id and add your post url in header with key 'url'

= Is enabling comments without authorization is safe? =
Yes, it is completely safe. But make sure you enabled comment moderation in WordPress settings -> Discussion to prevent spam comments. 

= What is the use of adding featured image urls. I'm already getting image id's? =
It depends on your requirements. Assume you're showing 20 recent posts with title and feature image in your Android application. By addig featured image url's to JSON object, you're saving 20 requests and ultimately saving user's data, your site bandwidth and improving app performance. Presto!

= Can I create my own routes with this plugin? =
Currently our plugin didn't support creating own routes. We are planning it in next updates. However if you need a custom router which is useful to others (like gettig post id from post url), ask us via support. We'll add it and update the plugin.

= How can I contribute to WP Rest API Customizer? =
You can get in touch with from our [blog](https://www.geekdashboard.com/) or [contact us](https://www.geekdashboard.com/contact-us) page and we'll have a discussion.

== Screenshots ==
 
1. Enable new routes 
2. Modify existing routes

== Changelog ==

= 1.1 =
*Release Date - 4 September 2017*

* Readme file updated.
* Plugin icon updated.

= 1.0 =
*Release Date - 30 August 2017*

* Initial release.
