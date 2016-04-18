<?php
/**
 * Plugin Name: delicious tagroll shortcode
 * Version: 2.2.0
 * Plugin URI: http://www.soderlind.no/archives/2009/11/18/delicious-tagroll-for-wordpress/
 * Description: Adds shortcode "[delicious_tagroll username='username']" which displays a delicious tagroll similar to <a href="http://delicious.com/help/tagrolls">http://delicious.com/help/tagrolls</a>
 * Author: Per Soderlind
 * Author URI: http://www.soderlind.no
 */

if ( defined( 'ABSPATH' ) ) {
	PS_Delicious_Tagroll::instance();
}

class PS_Delicious_Tagroll {
	/**
	* @var string $url The url to this plugin
	*/
	var $url = '';
	/**
	* @var string $urlpath The path to this plugin
	*/
	var $urlpath = '';

	private $add_script = false;

	private static $instance;
	private $pages_not_in_menu = array();

	public static function instance() {
		if ( self::$instance ) {
			return self::$instance;
		}
		self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		// "Constants" setup
		$this->url = plugins_url( basename( __FILE__ ), __FILE__ );
		$this->urlpath = plugins_url( '', __FILE__ );

		// Actions
		add_action( 'wp_footer', array( &$this, 'ps_delicious_tagroll_style' ) ); // Add styles (located at the end of this file).
		add_shortcode( 'delicious_tagroll', array( &$this, 'delicious_tagroll_func' ) );
	}

	function delicious_tagroll_func( $atts ) {
		$atts = shortcode_atts(array(
			'username'   => 'soderlind', // If you forget to add username="yourusername", my delicious tagroll will be shown ;) .
			'title'      => 'My Delicious Tags',
			'count'      => '100',
			'sort'       => 'alpha',
			'mincount'   => '10',
			'showcounts' => 'false',
			'minfont'    => '12',
			'maxfont'    => '35',
			'tags'       => '',
		), $atts);

		// Do param testing.
		$atts['username']    = urlencode( wp_filter_nohtml_kses( $atts['username'] ) );
		$atts['title']       = wp_filter_nohtml_kses( $atts['title'] );
		$atts['showcounts']  = (strcasecmp( $atts['showcounts'],'true' ) == 0);
		$atts['mincount']    = strval( intval( $atts['mincount'] ) );
		$atts['count']       = strval( intval( $atts['count'] ) );
		$atts['minfont']     = strval( intval( $atts['minfont'] ) );
		$atts['maxfont']     = strval( intval( $atts['maxfont'] ) );
		$atts['sort']        = ('freq' == $atts['sort']) ? 'freq' : 'alpha';
		$arr_tags = (empty( $atts['tags'] )) ? array() : explode( ',',$atts['tags'] );

		$rs = $this->ps_delicious_tagroll_get_tags( $atts['username'],$atts['count'] );

		if ( ! empty( $rs ) ) {
			$tag_cloud = "<div class='ps_delicious_tagroll'>\n";
			if ( '' != $atts['title'] ) {
				$tag_cloud .= sprintf( "<h2>%s</h2>\n",$atts['title'] );
			}

			if ( empty( $rs['item'] ) ) {
				$tag_cloud .= 'Sorry, no items found';
			} else {
				$tag_cloud .= "<ul>\n";
				if ( 'freq' == $atts['sort'] ) {
					usort( $rs['item'], array( &$this, 'ps_delicious_tagroll_custom_sort_numtags_desc' ) );
				} else {
					usort( $rs['item'], array( &$this, 'ps_delicious_tagroll_custom_sort_alpha_asc' ) );
				}

				$max = $this->ps_delicious_tagroll_get_max( $rs['item'] );
				$min = $this->ps_delicious_tagroll_get_min( $rs['item'] );

				foreach ( $rs['item'] as $item ) {
					$btags = ( ! empty( $arr_tags )) ? in_array( $item['title'],$arr_tags ) : true;
					if ( ($item['description'] >= $atts['mincount']) && $btags ) {
						$multiplier = ( $atts['maxfont'] - $atts['minfont'] ) / ( $max - $min );
						$fontsize = $atts['minfont'] + (($max -($max -($item['description'] -$min))) * $multiplier);
						if ( true === $atts['showcounts'] ) {
							$tag_cloud .= sprintf( "<li><a style='font-size: %dpx;' href='%s'>%s</a>(%d)</li>\n", $fontsize , $item['guid'], $item['title'],$item['description'] );
						} else {
							$tag_cloud .= sprintf( "<li><a style='font-size: %dpx;' href='%s'>%s</a></li>\n", $fontsize , $item['guid'], $item['title'] );
						}
					}
				}
				$tag_cloud .= "</ul>\n";
			}
			$tag_cloud .= "</div>\n";
			$this->add_script = true;
			return $tag_cloud;
		} else {
			return "<!-- It's not possible to reach Delicious -->";
		}
	}

	function ps_delicious_tagroll_get_tags( $username = 'soderlind', $count = 10 ) {

		$key = 'delicious_tagroll_' . $username;

		$feed_url = sprintf( 'http://feeds.delicious.com/v2/rss/tags/%s?count=%s',$username,$count );

		$rs = get_transient( $key );									// read "cache"
		if ( false != $rs ) { return $rs; }

		$data  = wp_remote_get( $feed_url.'' );
		if ( is_wp_error( $data ) ) {
			return (array) get_option( $key ); 						// retrieve fallback
		} else {
			$body = wp_remote_retrieve_body( $data );

			$xml = new SimpleXMLElement( $body );

			$rs = json_decode( json_encode( $xml->channel ),true );	// convert xml object to array

			if ( count( $rs['item'] ) > 0 ) {
				set_transient( $key, $rs, 60 * 60 );					//expire after 1 hour
				update_option( $key, $rs ); 							//fallback storage
			} else {
				$rs = (array) get_option( $key ); 						// retrieve fallback
			}
			return $rs;
		}
	}

	function ps_delicious_tagroll_get_max( $arr ) {
		usort( $arr, array( &$this, 'ps_delicious_tagroll_custom_sort_numtags_desc' ) );
		return $arr[0]['description'];
	}

	function ps_delicious_tagroll_get_min( $arr ) {
		usort( $arr, array( &$this, 'ps_delicious_tagroll_custom_sort_numtags_asc' ) );
		return $arr[0]['description'];
	}

	function ps_delicious_tagroll_custom_sort_numtags_desc( $a, $b ) {
		return $a['description'] < $b['description'];
	}

	function ps_delicious_tagroll_custom_sort_numtags_asc( $a, $b ) {
		return $a['description'] > $b['description'];
	}

	function ps_delicious_tagroll_custom_sort_alpha_asc( $a, $b ) {
		return strcasecmp( $a['title'] ,$b['title'] );
	}

	function ps_delicious_tagroll_style() {
		if ( ! $this->add_script ) {
			return false;
		}
		wp_enqueue_style( 'delicious_tagroll_style',  $this->urlpath . '/delicious-tagroll-shortcode.css' );
	}
} //End Class
