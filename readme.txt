=== delicious tagroll shortcode ===
Contributors: PerS
Donate link: http://soderlind.no/donate/
Tags: delicious, tagroll, shortcode, wpmu
Requires at least: 2.8.6
Tested up to: 3.5
Stable tag: trunk

The delicious tagroll for WordPress plugin adds a new shortcode to WordPress, the [delicious_tagroll] shortcode.

== Description ==

The delicious tagroll for WordPress plugin adds a new shortcode to WordPress, the `[delicious_tagroll]` shortcode.  The shortcode creates a [tag cloud](http://wordpress.org/extend/plugins/delicious-tagroll-shortcode/screenshots/) from your public [delicious](http://delicious.com/) [tags](http://delicious.com/tag/).

Live demo: [http://soderlind.no/bookmarks/](http://soderlind.no/bookmarks/)

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
* count="number of tags" (default="100″)
* sort="alpha or freq" (default = “alpha")
* showcounts="true or false" (default = “false", show tag counts)
* mincount (default mincount="10"), eg. show only tags with 10 or more links
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
= 2.1.2 =
* Bugfix, You need this version if you are using PHP prior to version 5.3
= 2.1.1 =
* Replaced LastRSS with WordPress built in methods and set/get transient for caching. See function ps_delicious_tagroll_get_tags() in ps_delicious_tagroll.php
* Added a new optional attribute:
     * tags (default empty), used to filter which tags you'd like to display eg. tags="javascript,jquery,nodejs"


= 2.0 =
* Delicious removed their javascript feed so I had to do a [total rewrite](http://plugins.trac.wordpress.org/changeset/455721/delicious-tagroll-shortcode/trunk/ps_delicious_tagroll.php)
     * Creates the tag cloud server-side (good for SEO) and lets you change the look and feel using the included style sheet.
     * Uses the [Delicious RSS feed API](http://delicious.com/help/feeds)
     * Caches the feed for one hour. Delicious might block you if you access their feed API too often.
* Some attributes are removed from the shortcode (you can change these using the ps_delicious_tagroll.css style sheet in the plugin directory):
     * mincolor
     * maxcolor
     * flow
* Also removed the attributes
     * showname
     * showadd
     * icon
* Added a new attribute:
     * mincount (default mincount="10"), eg. show only tags with 10 or more links 
= 1.1 =
* changed parameter name="true" to showname="true"
* added missing parameter showcounts="false"

= 1.0 = 
* initial release