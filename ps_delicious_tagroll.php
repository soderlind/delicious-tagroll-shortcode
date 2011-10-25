<?php
/*
Plugin Name: delicious tagroll shortcode
Version: 2.0
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

define(RSSCACHE_TIME, 3600); // one hour, NEVER set this lower than 1200 (20 minutes) - webmasters will hate you otherwise.


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
		* @var string $urlpath The path to this plugin
		*/
		var $rsscachepath = '';

		//Class Functions
		/**
		* PHP 4 Compatible Constructor
		*/
		function ps_delicious_tagroll(){$this->__construct();}

		/**
		* PHP 5 Constructor
		*/		
		function __construct(){
			//"Constants" setup
			$this->url = plugins_url(basename(__FILE__), __FILE__);
			$this->urlpath = plugins_url('', __FILE__);	
			$this->rsscachepath = trailingslashit( ABSPATH . '/wp-content/aggrss-cache');

			//Actions
			add_action("init", array(&$this,"ps_delicious_tagroll_init"));
			add_action('wp_print_styles', array(&$this,'ps_delicious_tagroll_style')); // add styles (located at the end of this file)
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
		), $atts));
		
		// do param testing
		//string
		$username    = urlencode( wp_filter_nohtml_kses($username));
		$title       = urlencode( wp_filter_nohtml_kses($title));
		//bool
		$showcounts  = (strcasecmp($showcounts,"true") == 0);
		//int
		$mincount    = strval(intval($mincount));
		$count       = strval(intval($count));
		$minfont     = strval(intval($minfont));
		$maxfont     = strval(intval($maxfont));
		//choice
		$sort        = ($sort == "freq") ? "freq" : "alpha";
				
		
		$feed_url = sprintf("http://feeds.delicious.com/v2/rss/tags/%s?count=%s",$username,$count);
		
		if ($rs = $this->aggrss($feed_url)) {			
			$tag_cloud = "<div class='ps_delicious_tagroll'>\n";
			if ($title != "") {
				$tag_cloud .= sprintf("<h2>%s</h2>\n",$title);
			}

			if ($rs['items_count'] <= 0) {
				$tag_cloud .= "Sorry, no items found";
			} else {			
				$tag_cloud .= "<ul>\n";
				if ($sort == "freq") {
					usort($rs['items'], array(&$this,'ps_delicious_tagroll_custom_sort_numtags_desc'));
				} else {
					usort($rs['items'], array(&$this,'ps_delicious_tagroll_custom_sort_alpha_asc'));
				}
				
				$max = $this->ps_delicious_tagroll_get_max($rs['items']);
				$min = $this->ps_delicious_tagroll_get_min($rs['items']);
				
				foreach ($rs['items'] as $item) {
					if ($item['description'] >= $mincount) {						
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
    	//return $a['title'] > $b['title'];
    	return strcasecmp($a['title'] ,$b['title'] );
    }

	
	function ps_delicious_tagroll_init() {
		global $ps_delicious_tagroll_lastRSS;
		require_once(dirname(__FILE__) . '/lastRSS/lastRSS.php');
		
		if ( !file_exists($this->rsscachepath) ) :
			if ( is_writable( dirname($this->rsscachepath) ) )
				$dir = mkdir( $this->rsscachepath, 0777);
			else
				die("Your cache directory (<code>" . $this->rsscachepath . "</code>) needs to be writable for this plugin to work. Double-check it. <a href='" . get_settings('siteurl') . "/wp-admin/plugins.php?action=deactivate&amp;plugin=wp-lastRSS.php'>Deactivate the aggrss plugin</a>.");
		endif;
		// create lastRSS object
		$ps_delicious_tagroll_lastRSS = new lastRSS; 
		// setup transparent cache
		$ps_delicious_tagroll_lastRSS->cache_dir = $this->rsscachepath;
		$ps_delicious_tagroll_lastRSS->cache_time = RSSCACHE_TIME; 
		//
		$ps_delicious_tagroll_lastRSS->date_format = 'U';
	}
		

	function aggrss($rssurl,$striptags=false,$num=0) {
		global $ps_delicious_tagroll_lastRSS;
		$ps_delicious_tagroll_lastRSS->CDATA= ($striptags) ? "strip" : "content" ;
		$ps_delicious_tagroll_lastRSS->items_limit=$num;
		if ($aggr = $ps_delicious_tagroll_lastRSS->Get($rssurl))
			return $aggr;
		else
			return 0;
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