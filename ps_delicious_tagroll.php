<?php
/*
Plugin Name: delicious tagroll shortcode
Version: 1.1
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
1.1
- changed parameter name="true" to showname="true"
- added missing parameter showcounts="false"

1.0 
- initial release

*/


class ps_delicious_tagroll {
	private $url = "";
	private $urlpath = "";
	private $doparamcheck = true;
	function __construct() {
		add_action('init',array(&$this,'ps_delicious_tagroll_init'));
		add_action('wp_print_styles', array(&$this,'ps_delicious_tagroll_style')); // add styles (located at the end of this file)
		add_shortcode('delicious_tagroll', array(&$this,'delicious_tagroll_func'));	
	}

	
	// [delicious_tagroll username="username"]
	// supported parameter values are "documented" at: http://delicious.com/help/tagrolls
	function delicious_tagroll_func($atts) {
		extract(shortcode_atts(array(
			'username'   => 'soderlind', //if you forget to add username="yourusername", my delicious tagroll will be shown ;)
			'title'      => 'My Delicious Tags',
			'icon'       => 'true',
			'count'      => '100',
			'sort'       => 'alpha',
			'flow'       => 'cloud',
			'showname'   => 'true',
			'showadd'    => 'true',
			'showcounts' => 'false',
			'mincolor'   => '73adff',
			'maxcolor'   => '3274d0',
			'minfont'    => '12',
			'maxfont'    => '35',
		), $atts));
		// do param testing
		
		if ($this->doparamcheck) {
		//string
			$username    = urlencode( wp_filter_nohtml_kses($username));
			$title       = urlencode( wp_filter_nohtml_kses($title));
			//bool
			$icon        = (strtolower($icon) == "true");
			$name        = (strtolower($name) == "true");
			$showadd     = (strtolower($showadd) == "true");
			$showcounts  = (strtolower($showcounts) == "true");
			//int
			$count       = strval(intval($count));
			$minfont     = strval(intval($minfont));
			$maxfont     = strval(intval($maxfont));
			//hex
			$mincolor    = ((strlen($mincolor) == 6) && ctype_xdigit($mincolor)) ? $mincolor : "73adff";
			$maxcolor    = ((strlen($maxcolor) == 6) && ctype_xdigit($maxcolor)) ? $maxcolor : "3274d0";
			//choice
			$sort        = ($sort == "freq") ? "freq" : "alpha";
			$flow        = ($flow == "list") ? "list" : "cloud";
		}
		return sprintf("<script type='text/javascript' src='http://feeds.delicious.com/v2/js/tags/%s?title=%s%s&count=%s&sort=%s&flow=%s%s%s%s&color=%s-%s&size=%s-%s'></script>'",
		$username,$title,($icon==true ? "&icon" : ""),$count,$sort,$flow,($name == true ? "&name" : ""),($showadd == true ? "&showadd" : ""),($showcounts == true ? "&totals" : ""), $mincolor,$maxcolor,$minfont,$maxfont);
	}
	
	function ps_delicious_tagroll_init() {
		$this->url = trailingslashit( get_bloginfo('wpurl') ) . substr( __FILE__, strlen($_SERVER['DOCUMENT_ROOT'])+1);
		$this->urlpath = dirname($this->url);
	}
	

	function ps_delicious_tagroll_style() {
		wp_enqueue_style('delicious_tagroll_style',  $this->url . "?ps_delicious_tagroll_style");
	}
	
}

if (isset($_GET['ps_delicious_tagroll_style'])) {
	Header("content-type: text/css");
	echo<<<ENDCSS
/**
* @desc modify delicious tagroll, add css here
* @author Per Soderlind - www.soderlind.no
*/


	
ENDCSS;

} else {
	$ps_tagroll = new ps_delicious_tagroll();
}