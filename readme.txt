=== delicious tagroll shortcode ===
Contributors: PerS
Tags: delicious, tagroll, shortcode, wpmu
Requires at least: 2.8.6
Tested up to: 2.8.6
Stable tag: trunk

The delicious tagroll for WordPress plugin adds a new shortcode to WordPress, the `[delicious_tagroll]` shortcode

== Description ==

The delicious tagroll for WordPress plugin adds a new shortcode to WordPress, the `[delicious_tagroll]` shortcode

For more information, please see the [plugin home page](http://www.soderlind.no/archives/2009/11/18/delicious-tagroll-for-wordpress/)

== Installation ==

= Requirement =
* PHP: 5.2.x or newer

= Manual Installation =
* Upload the files to wp-content/plugins/delicious-tagroll-shortcode/
* Activate the plugin

= Automatic Installation =
* On your WordPress blog, open the Dashboard
* Go to Plugins->Install New
* Search for "delicious tagroll"
* Click on install to install the delicious tagroll shortcode

= WPMU Installation =
* If you want to change the shortcode defaults, edit the `ps_delicious_tagroll.php` file
* Upload the file to wp-content/mu-plugins/

= Usage = 
* Add the `[delicious_tagroll]` shortcode to a post or page, see [screenshot](http://www.soderlind.no/wp-content/uploads/2009/11/ps_delicious_tagroll_edit_page.png)

Mandatory parameter

* username=”delicious username” (if you forget it, my tagroll is displayed instead ;) )

Optional parameters

* title=”tagroll title” (default =”My Delicious Tags”, use ” ” if you don’t want a tagroll title)
* icon=”true or false” (default=”true”)
* count=”number of tags” (default=”100″)
* sort=”alpha or freq” (default = “alpha”)
* flow=”cloud or list” (default = “cloud”)
* showname=”true or false”(default = “true”, show your delicious name)
* showadd=”true or false”  (default = “true”, show add to network)
* showcounts=”true or false” (default = “false”, show tag counts)
* mincolor=”73adff”
* maxcolor=”3274d0″
* minfont=”12″
* maxfont=”35″

== Screenshots ==

1. Demo
2. Adding the `[delicious_tagroll]` shortcode to a page

== Changelog ==

= 1.1 =
* changed parameter name="true" to showname="true"
* added missing parameter showcounts="false"

= 1.0 = 
* initial release