<?php
/*

******************************************************************************

Plugin Name:  WP_MFEN
Plugin URI:   http://wedemandhtml.com/blog/mfen/
Description:  Allows one to easily create images of chess positions through the use of FEN strings. See <<a href="http://en.wikipedia.org/wiki/Forsyth%E2%80%93Edwards_Notation">http://en.wikipedia.org/wiki/Forsyth&ndash;Edwards Notation</a>> for more information on FEN strings.
Version:      0.4.1
Author:       Michiel Sikma
Author URI:   http://wedemandhtml.com/

******************************************************************************

//////////////////////////////////////////////////////////////////////////////
//                                                                          //
//  MFEN - My FEN Rendering Script - WordPress hooks                        //
//  Version 0.4.1                                                           //
//                                                                          //
//     Copyright (C) 2008-2009, Michiel Sikma <michiel@sikma.org>           //
//                                                                          //
//  Permission is hereby granted, free of charge, to any person obtaining   //
//  a copy of this software and associated documentation files (the         //
//  "Software"), to deal in the Software without restriction, including     //
//  without limitation the rights to use, copy, modify, merge, publish,     //
//  distribute, sublicense, and/or sell copies of the Software, and to      //
//  permit persons to whom the Software is furnished to do so, subject to   //
//  the following conditions:                                               //
//                                                                          //
//  The above copyright notice and this permission notice shall be          //
//  included in all copies or substantial portions of the Software.         //
//                                                                          //
//  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,         //
//  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF      //
//  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  //
//  IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY    //
//  CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,    //
//  TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE       //
//  SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.                  //
//                                                                          //
//////////////////////////////////////////////////////////////////////////////

// Changelog
// ---------
//
// 0.4.1  2009-05-26  Michiel Sikma  michiel@sikma.org
//
//         * fixed: now refers to correct directory (wp-mfen instead
//           of wp_mfen). (Thanks, Kevin Gisi)
//
// 0.4    2009-05-24  Michiel Sikma  michiel@sikma.org
//
//		   Initial version.

*/


/* Include the user-defined settings. */
include_once('config.php');


/* Include the file containing the MFEN class. Note that there is no
WordPress-specific code in this script at all. It's agnostic to its
environment and can be used standalone as well. */
require_once('mfen.php');


/* We construct the WP_MFEN class primarily to refer to the settings.
What it does is very limited. It just makes an MFEN object and sets it up to
conform to WordPress's specific needs. */

class WP_MFEN {
	
	/* These settings can be overridden in a shortcode. */
	private $allowed_arguments = array('SIZE', 'CAPTION', 'COLOR_LIGHT', 'COLOR_DARK', 'NO_FENTEXT', 'NO_ANCHOR');
	
	function WP_MFEN() {
		if (!function_exists('add_filter')) {
			/* Whatever zombie version of WordPress this is: it's too old! */
			return;
		}
		
		/* Create the MFEN object and set it up. */
		global $MFEN;
		global $WP_MFEN_CONFIG;
		$MFEN = new MFEN();
		
		/* Note: the 'CACHE_FOLDER' and 'PIECE_FOLDER' must
		remain unchanged. They are needed for WordPress. */
		$MFEN->change_settings(array(
			'CACHE_FOLDER' => WP_PLUGIN_DIR.'/wp-mfen/cache/',
			'CACHE_URI' => WP_PLUGIN_URL.'/wp-mfen/cache/',
			'PIECE_FOLDER' => $WP_MFEN_CONFIG['pieces_directory'],
			'COLOR_LIGHT' => $WP_MFEN_CONFIG['color_light'],
			'COLOR_DARK' => $WP_MFEN_CONFIG['color_dark'],
		));
		$MFEN->save_defaults();
		
		/* Add the filters that will apply the translation function. */
		add_filter('the_content', 'parse_post_mfen');
		add_filter('the_excerpt', 'parse_post_mfen');
		add_filter('get_comment_excerpt', 'parse_comment_mfen');
		add_filter('get_comment_text', 'parse_comment_mfen');
		add_filter('comment_text_rss', 'parse_comment_mfen');
		add_filter('the_content_rss', 'parse_post_mfen');
		add_filter('the_excerpt_rss', 'parse_post_mfen');
		
		/* Add some basic CSS, if needed. */
		if ($WP_MFEN_CONFIG['print_default_css'] != false) {
			add_action('wp_head', 'print_mfen_css');
		}
	}
	
	function filter_arguments($args) {
		/* Check the arguments to ensure they don't contain settings that
		should not be overwritten. */
		foreach ($args as $k => $v) {
			unset($args[$k]);
			$args[strtoupper($k)] = $v;
			if (!in_array(strtoupper(trim($k)), $this->allowed_arguments)) {
				unset($args[$k]);
			}
		}
		return $args;
	}
}


/* Printed in the header to ensure there's some basic CSS. Can be turned
off in the config.php. */
function print_mfen_css() {
	printf('<style type="text/css">
	.mfen {
		margin: 10px 0;
	}
	.mfen .wrapper {
		color: #0066CC;
		background-color: #fbfcfd;
		border: 1px solid #d9e1ee;
		padding: 10px;
		display: inline-block;
		text-align: center;
	}
	a.mfen:hover .wrapper {
		color: #114477;
		background-color: #ffffff;
	}
	.mfen .wrapper .fenimg {
		
	}
	.mfen .wrapper .fencaption {
		margin: 6px 0 0;
		display: block;
	}
	.mfen .wrapper .fentext {
		/* Hide the FEN text by default. */
		display: none;
	}
</style>');
}


/* Used when parsing a post. */
function parse_post_mfen($content='') {
	return parse_mfen($content, array(
		'SIZE' => 'medium',
	));
}

/* Used when parsing a comment. */
function parse_comment_mfen($content='') {
	return parse_mfen($content, array(
		'SIZE' => 'tiny',
	));
}

/* The function that's called whenever we need to parse a body of text
that may contain a FEN shortcode. The '$settings' array is used to
pass additional standard values, which are overridden by the
shortcode's arguments. */
function parse_mfen($content='', $settings=array()) {
	$pattern = '/\[(fen)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\1\])?/ie';
	$content = preg_replace($pattern, "replace_mfen('\\1', '\\2', '\\4', \$settings)", $content);
	return($content);
}


/* The callback function for FEN shortcode matches. This just calls
generate_mfen and returns its output. */
function replace_mfen($function='', $args='', $fen='', $settings=array()) {
	$data = generate_mfen($function, $args, $fen, $settings);
	if (isset($data['error'])) {
		/* Something went wrong while generating the image! */
		$error = $data['error'];
		$error = $error[1].' ('.$error[0].')';
		return '<span class="mfen error">Sorry; something went wrong while generating this FEN image. The returned error was: "'.htmlentities($error).'".</span>';
	}
	
	global $WP_MFEN_CONFIG;
	
	/* Determine the anchor. */
	$link = WP_PLUGIN_URL.'/wp-mfen/mfen.php?fen='.$data['fen'].'&size='.$WP_MFEN_CONFIG['enlarged_size'];
	foreach (array('color_light', 'color_dark') as $arg) {
		if (isset($data[$arg])) {
			$link .= '&'.$arg.'='.$data[$arg];
		}
	}
	$anchor = $link;
	/* Will we print a JavaScript pop-up link in the anchor? */
	if ($WP_MFEN_CONFIG['link_to_jspopup']) {
		$psize = $WP_MFEN_CONFIG['enlarged_size'] + 16;
		$anchor = 'javascript:x=(screen.width-'.$psize.')*(0.5)<<0;y=(screen.height-'.$psize.')*(0.5)<<0;window.open(\''.$link.'\',\'mfen\',\'toolbar=no,menubar=no,resizable=no,scrollbars=no,status=no,location=no,width='.$psize.',height='.$psize.', top=\'+y+\',left=\'+x);void(0);';
	}
	
	/* Begin constructing the return value. The FEN shortcode will be
	removed in its entirety and overwritten with this string. */
	$fentext = '';
	if (!$data['no_fentext']) {
		$fentext = sprintf('<span class="fentext">%s</span>',
			$data['fen']
		);
	}
	$caption = '';
	if (isset($data['caption'])) {
		$fencaption = sprintf('<span class="fencaption">%s</span>',
			$data['caption']
		);
	}
	if (!$data['no_anchor']) {
		return sprintf('<a href="%s" class="%s"><span class="wrapper"><span class="fenimg"><img src="%s" alt="%s" width="%d" height="%d" /></span>%s%s</span></a>',
			$anchor,
			'mfen '.$data['size_class'],
			$data['filename'],
			htmlentities($data['fen']),
			$data['size'],
			$data['size'],
			$fentext,
			$fencaption
		);
	} else {
		return sprintf('<span class="%s"><span class="wrapper"><span class="fenimg"><img src="%s" alt="%s" width="%d" height="%d" /></span>%s%s</span></span>',
			'mfen '.$data['size_class'],
			$data['filename'],
			htmlentities($data['fen']),
			$data['size'],
			$data['size'],
			$fentext,
			$fencaption
		);
	}
}

/* The function that actually does most of the work. The '$function' should
always be "fen". '$args' will contain extra arguments passed in the command,
such as [fen color_dark="#ff0000"]string[/fen]. '$fen' contains the string to
be rendered and '$settings' contains standard values that the shortcode's
arguments may override. */
function generate_mfen($function='', $args='', $fen='', $settings=array()) {
	global $WP_MFEN;
	global $MFEN;
	
	if (strtolower(trim($function)) != 'fen') {
		/* Wrong shortcode! This shouldn't happen,
		but it's better to be safe. */
		return $content;
	}
	$args = shortcode_parse_atts(stripslashes($args));
	/* Override the standard settings where applicable. */
	foreach ($args as $k => $v) {
		$settings[strtoupper($k)] = $v;
	}
	/* Filter the arguments to ensure there are none that must not
	be overwritten. */
	$args = $WP_MFEN->filter_arguments($settings);
	
	/* Used when constructing the return string (in a different function):
	is an <a> tag required? */
	$no_anchor = false;
	if (isset($args['NO_ANCHOR']) && strtolower(trim($args['NO_ANCHOR'])) == 'true') {
		$no_anchor = true;
	}
	/* Is the FEN text required? */
	$no_fentext = false;
	if (isset($args['NO_FENTEXT']) && strtolower(trim($args['NO_FENTEXT'])) == 'true') {
		$no_fentext = true;
	}
	
	/* Filter the color if necessary. */
	foreach (array('COLOR_LIGHT', 'COLOR_DARK') as $c) {
		if (substr($args[$c], 0, 2) == '0x') {
			$args[$c] = substr($args[$c], 2, 6);
		}
		if (substr($args[$c], 0, 1) == '#') {
			$args[$c] = substr($args[$c], 1, 6);
		}
	}
	
	/* Now simply generate the image (or pull its name from cache). Magic! */
	$fen = trim(strip_tags($fen));
	$MFEN->set_fen($fen);
	$MFEN->reset_settings_to_defaults();
	$MFEN->change_settings($args);
	$filename = $MFEN->render(true);
	if ($MFEN->error_has_occurred()) {
		/* Something went wrong while generating the image! */
		return array('error' => $MFEN->get_last_error());
	}
	/* To help blog owners style the FEN images, some extra classes are
	passed on to the <span> tag. */
	$size_class = $MFEN->get_size();
	$size = $size_class[0];
	$size_class[0] = 'size_'.$size_class[0];
	$size_class = implode(' ', $size_class);
	
	/* We're done. Return the information necessary to construct the string. */
	return array(
		'size_class' => $size_class,
		'filename' => $filename,
		'size' => $size,
		'fen' => $fen,
		'color_light' => @$args['COLOR_LIGHT'],
		'color_dark' => @$args['COLOR_DARK'],
		'caption' => @$args['CAPTION'],
		'no_anchor' => $no_anchor,
		'no_fentext' => $no_fentext,
	);
}


/* Hook to start the plugin. */
function start_mfen() {
	global $WP_MFEN;
	$WP_MFEN = new WP_MFEN();
}
add_action('plugins_loaded', 'start_mfen');

?>