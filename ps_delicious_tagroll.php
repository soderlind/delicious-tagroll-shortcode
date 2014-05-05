<?php
/*
Plugin Name: delicious tagroll shortcode
Version: 2.1.3
Plugin URI: http://www.soderlind.no/archives/2009/11/18/delicious-tagroll-for-wordpress/
Description: Adds shortcode "[delicious_tagroll username='username']" which displays a delicious tagroll similar to <a href="http://delicious.com/help/tagrolls">http://delicious.com/help/tagrolls</a>
Author: Per Soderlind (aka PerS)
Author URI: http://www.soderlind.no
*/
/*
Pre-req: PHP 5.x

Installation:

	save the plugin in wp-content/plugins (and activate from Plugins) or wp-content/mu-plugins

Change log
2.1.3
- Tested in WordPress 3.9
- The plugin no longer supports PHP 4.x
2.1.2
- Bugfix, You need this version if you are using PHP prior to version 5.3
2.1.1
- Replaced LastRSS with WordPress built in methods and set/get transient for caching. See function ps_delicious_tagroll_get_tags().
- Added a new optional attribute:
     * tags (default empty), used to filter which tags you'd like to display eg. tags="javascript,jquery,nodejs"
2.0
- Delicious removed their javascript feed so I had to do a total rewrite.
- Some attributes are removed from the shortcode (you can change these using the ps_delicious_tagroll.css style sheet):
     * mincolor
     * maxcolor
     * flow
- Also removed
     * showname
     * showadd
 - Added a new attribute:
     * mincount (default mincount="10"), eg. show only tags with 10 or more links
1.1
- changed parameter name="true" to showname="true"
- added missing parameter showcounts="false"

1.0
- initial release

*/

/*
Credits:
	This template is based on the template at http://pressography.com/plugins/wordpress-plugin-template/
	My changes are documented at http://soderlind.no/archives/2010/03/04/wordpress-plugin-template/
*/


if (!class_exists('ps_delicious_tagroll')) {
	class ps_delicious_tagroll {
		/**
		* @var string $url The url to this plugin
		*/
		var $url = '';
		/**
		* @var string $urlpath The path to this plugin
		*/
		var $urlpath = '';

		/**
		* PHP 5 Constructor
		*/
		function __construct(){
			//"Constants" setup
			$this->url = plugins_url(basename(__FILE__), __FILE__);
			$this->urlpath = plugins_url('', __FILE__);

			//Actions
			add_action('wp_enqueue_scripts', array(&$this,'ps_delicious_tagroll_style')); // add styles (located at the end of this file)
			add_shortcode('delicious_tagroll', array(&$this,'delicious_tagroll_func'));
		}

		function delicious_tagroll_func($atts) {
			extract(shortcode_atts(array(
				'username'   => 'soderlind', //if you forget to add username="yourusername", my delicious tagroll will be shown ;)
				'title'      => 'My Delicious Tags',
				'count'      => '100',
				'sort'       => 'alpha',
				'mincount'   => '10',
				'showcounts' => 'false',
				'minfont'    => '12',
				'maxfont'    => '35',
				'tags'       => ''
			), $atts));

			// do param testing
			//string
			$username    = urlencode( wp_filter_nohtml_kses($username));
			$title       = wp_filter_nohtml_kses($title);
			//bool
			$showcounts  = (strcasecmp($showcounts,"true") == 0);
			//int
			$mincount    = strval(intval($mincount));
			$count       = strval(intval($count));
			$minfont     = strval(intval($minfont));
			$maxfont     = strval(intval($maxfont));
			//choice
			$sort        = ($sort == "freq") ? "freq" : "alpha";
			//array
			$arr_tags = (empty($tags)) ? array() : explode(',',$tags);

			$rs = $this->ps_delicious_tagroll_get_tags($username,$count);

			if (!empty($rs)) {
				$tag_cloud = "<div class='ps_delicious_tagroll'>\n";
				if ($title != "") {
					$tag_cloud .= sprintf("<h2>%s</h2>\n",$title);
				}

				if (empty($rs['item'])) {
					$tag_cloud .= "Sorry, no items found";
				} else {
					$tag_cloud .= "<ul>\n";
					if ($sort == "freq") {
						usort($rs['item'], array(&$this,'ps_delicious_tagroll_custom_sort_numtags_desc'));
					} else {
						usort($rs['item'], array(&$this,'ps_delicious_tagroll_custom_sort_alpha_asc'));
					}

					$max = $this->ps_delicious_tagroll_get_max($rs['item']);
					$min = $this->ps_delicious_tagroll_get_min($rs['item']);

					foreach ($rs['item'] as $item) {
						$btags = (!empty($arr_tags)) ? in_array ($item['title'],$arr_tags) : true;
						if (($item['description'] >= $mincount) && $btags) {
							$multiplier = ($maxfont-$minfont)/($max-$min);
							$fontsize = $minfont + (($max-($max-($item['description']-$min)))*$multiplier);
							if ($showcounts == true) {
								$tag_cloud .=  sprintf("<li><a style='font-size: %dpx;' href='%s'>%s</a>(%d)</li>\n", $fontsize , $item['guid'], $item['title'],$item['description']);
							} else {
								$tag_cloud .=  sprintf("<li><a style='font-size: %dpx;' href='%s'>%s</a></li>\n", $fontsize , $item['guid'], $item['title']);
							}
						}
					}
					$tag_cloud .= "</ul>\n";
				}
				$tag_cloud .= "</div>\n";
				return $tag_cloud;
			} else {
				return "<!-- It's not possible to reach Delicious -->";
			}
		}

		function ps_delicious_tagroll_get_tags($username = 'soderlind',$count = 10){

			$key = 'delicious_tagroll_' . $username;

			$feed_url = sprintf("http://feeds.delicious.com/v2/rss/tags/%s?count=%s",$username,$count);

			$rs = get_transient($key);									// read "cache"
			if ($rs != false) return $rs;

			$data  = wp_remote_get($feed_url.'');
			if (is_wp_error($data)) {
				return (array)get_option($key); 						// retrieve fallback
			} else {
				$body = wp_remote_retrieve_body($data);

				$xml = new SimpleXMLElement($body);

				$rs = json_decode(json_encode($xml->channel),TRUE);	// convert xml object to array

				if (count($rs['item']) > 0) {
					set_transient($key, $rs, 60*60);					//expire after 1 hour
					update_option($key, $rs); 							//fallback storage
				} else {
					$rs = (array)get_option($key); 						// retrieve fallback
				}
				return $rs;
			}
		}



		function ps_delicious_tagroll_get_max($arr) {
			usort($arr, array(&$this,'ps_delicious_tagroll_custom_sort_numtags_desc'));
			return $arr[0]['description'];
		}

		function ps_delicious_tagroll_get_min($arr) {
			usort($arr, array(&$this,'ps_delicious_tagroll_custom_sort_numtags_asc'));
			return $arr[0]['description'];
		}


		function ps_delicious_tagroll_custom_sort_numtags_desc($a,$b) {
			return $a['description'] < $b['description'];
		}

		function ps_delicious_tagroll_custom_sort_numtags_asc($a,$b) {
			return $a['description'] > $b['description'];
		}

		function ps_delicious_tagroll_custom_sort_alpha_asc($a,$b) {
			return strcasecmp($a['title'] ,$b['title'] );
		}

		function ps_delicious_tagroll_style() {
			wp_enqueue_style('delicious_tagroll_style',  $this->urlpath . "/ps_delicious_tagroll.css");
		}
	} //End Class
} //End if class exists statement


if (class_exists('ps_delicious_tagroll')) {
	$ps_delicious_tagroll_var = new ps_delicious_tagroll();
}
?>