=== delicious tagroll shortcode ===
Contributors: PerS
Donate link: http://soderlind.no/donate/
Tags: delicious, tagroll, shortcode, wpmu
Requires at least: 2.8.6
Tested up to: 2.8.6
Stable tag: trunk

The delicious tagroll for WordPress plugin adds a new shortcode to WordPress, the [delicious_tagroll] shortcode.

== Description ==

The delicious tagroll for WordPress plugin adds a new shortcode to WordPress, the `[delicious_tagroll]` shortcode.  The shortcode creates a [tag cloud](http://wordpress.org/extend/plugins/delicious-tagroll-shortcode/screenshots/) or a list of tags(see [flow parameter](http://wordpress.org/extend/plugins/delicious-tagroll-shortcode/installation/)) from your public [delicious](http://delicious.com/) [tags](http://delicious.com/tag/).

Live demo: *The proof of the pudding* is using it yourself, I am: [http://soderlind.no/bookmarks/](http://soderlind.no/bookmarks/)

For more information, please see the [plugin home page](http://soderlind.no/archives/2009/11/18/delicious-tagroll-for-wordpress/)

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
* Add the `[delicious_tagroll username="delicious username"]` shortcode to a post or page, see [screenshot](http://wordpress.org/extend/plugins/delicious-tagroll-shortcode/screenshots/) and the [plugin home page](http://soderlind.no/archives/2009/11/18/delicious-tagroll-for-wordpress/)

= Parameters =
* username="delicious username" (the only **mandatory** parameter, if you forget this parameter, my tagroll will be displayed)
* title="tagroll title" (default ="My Delicious Tags", use " " if you don’t want a tagroll title)
* icon="true or false" (default="true")
* count="number of tags" (default="100″)
* sort="alpha or freq" (default = “alpha")
* flow="cloud or list" (default = “cloud")
* showname="true or false"(default = “true", show your delicious name)
* showadd="true or false"  (default = “true", show add to network)
* showcounts="true or false" (default = “false", show tag counts)
* mincolor="73adff"
* maxcolor="3274d0″
* minfont="12″
* maxfont="35″

== Screenshots ==

1. Demo
2. Adding the `[delicious_tagroll]` shortcode to a page

== Frequently Asked Questions ==

= What are shortcodes? =

Shortcode, a "shortcut to code", makes it easy to add funtionality to a page or post. When a page with a shortcode is saved, WordPress execute the linked code and embedds the output in the page.

= Writing your own shortcode plugin =

* [Smashing Magazine](http://www.smashingmagazine.com/) has a nice (as allways) article about [Mastering WordPress shortcodes](http://www.smashingmagazine.com/2009/02/02/mastering-wordpress-shortcodes/). The article has several examples you can use as a starting point for writing your own.
* At codplex, you'll find the [Shortcode API documented](http://codex.wordpress.org/Shortcode_API)
* Also, feel free to use this plugin as a template for you own shortcode plugin


== Changelog ==

= 1.1 =
* changed parameter name="true" to showname="true"
* added missing parameter showcounts="false"

= 1.0 = 
* initial release