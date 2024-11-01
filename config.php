<?php

//////////////////////////////////////////////////////////////////////////////
//																			//
//	MFEN - My FEN Rendering Script - program settings						//
//	Version 0.4.1															//
//																			//
//	   Copyright (C) 2008-2009, Michiel Sikma <michiel@sikma.org>			//
//																			//
//	Permission is hereby granted, free of charge, to any person obtaining	//
//	a copy of this software and associated documentation files (the			//
//	"Software"), to deal in the Software without restriction, including		//
//	without limitation the rights to use, copy, modify, merge, publish,		//
//	distribute, sublicense, and/or sell copies of the Software, and to		//
//	permit persons to whom the Software is furnished to do so, subject to	//
//	the following conditions:												//
//																			//
//	The above copyright notice and this permission notice shall be			//
//	included in all copies or substantial portions of the Software.			//
//																			//
//	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,			//
//	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF		//
//	MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.	//
//	IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY	//
//	CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,	//
//	TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE		//
//	SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.					//
//																			//
//////////////////////////////////////////////////////////////////////////////


/* This config file will allow you to set up the MFEN plugin. It should
work by default; if it doesn't, feel free to contact me. */

global $WP_MFEN_CONFIG;

/* Whether to print the default CSS. Setting this to 'false' will leave the
replaced FEN strings with no default CSS, meaning you have to style them
yourself in your own CSS file. */
$WP_MFEN_CONFIG['print_default_css']	= true;

/* Whether to allow a JavaScript pop-up of a larger version. */
$WP_MFEN_CONFIG['link_to_jspopup']		= true;

/* The size of the enlarged image. */
$WP_MFEN_CONFIG['enlarged_size']		= 512;

/* The location of the pieces. */
$WP_MFEN_CONFIG['pieces_directory']		= WP_PLUGIN_DIR.'/wp-mfen/pieces/';

/* The default colors. */
$WP_MFEN_CONFIG['color_light']			= 'DFE3E8';
$WP_MFEN_CONFIG['color_dark']			= '9DA8BD';




?>