=== Plugin Name ===
Contributors: msikma
Donate link: http://nothing.here/
Tags: chess, image generation, fen
Requires at least: 2.6
Tested up to: 2.7
Stable tag: trunk

This plugin will convert FEN (Forsyth–Edwards Notation) strings contained in your blog's posts and comments and turn them into images.

== Description ==

This plugin will convert FEN (Forsyth–Edwards Notation) strings contained in your blog's posts and comments and turn them into images. This makes it very easy to show and explain various chess board positions simply by typing, for example: `[fen caption="The board after 1.e4."]rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3 0 1[/fen]`.

The script is very simple in concept but supports some minor customization (changing the board colors and piece images). It utilizes its own image cache, and the portion that actually generates the images is platform agnostic (can be used outside of WordPress too). Setup is done in a config file (an admin setup page is planned, though). 

== Installation ==

Installing the plugin should be very easy.

1. Upload the `wp_mfen` directory to `/wp-content/plugins/`
1. Activate the plugin through the "Plugins" menu in WordPress
1. Use the described shortcode syntax in your posts (note: there's no need to have shortcodes enabled as the plugin has its own parser)
1. Doesn't work? Maybe you need to manually create and give permissions to the `cache` directory (the script tries to do this itself)

== Frequently Asked Questions ==

= It's not working! It says something about the cache directory. =

This occurs if the script is unable to make a directory to place its cached images. Just go to where you installed the plugin and make a new directory called `cache` and, if necessary, give it the proper permissions (777) for PHP to save files to it.

= How exactly do I use this in my posts or comments? =

To make a FEN image appear, simply use something like `[fen]rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1[/fen]`. It's recommended that you put it in a paragraph tag of its own (i.e. use two enters before and after that line) for aesthetic reasons. Note that you can drop everything after the pieces information (which is everything before the first space) since that information is not used in the generation of the image.

You may want to add a caption, e.g. `[fen caption="The board's starting position"]rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR[/fen]`.

= Why does it generate images? Wouldn't it be much more efficient to generate an HTML table? =

It's true that HTML tables or divs would be more efficient, but they're also slightly less usable. This way, you can generate chess positions for use in any container. You can save them, send them via e-mail or include them in other documents.

= How do I customize this? I want to use different colors, pieces and custom CSS. =

Customizing the default settings must be done by editing the `config.php` file. An admin page is planned, but I'm not sure exactly when I'll get around to making it. Changing the pieces can be done simply by adding a new directory with images and changing the default, though you may need to empty the `cache` directory afterwards. By default, WP_MFEN includes a bit of CSS to get things working; to change it, it's recommended you copy and paste it into your own document and then disable the default in the config (don't edit the CSS in the plugin file, since it'll be reverted if you ever decide to update it).

= I want to use this thing in my non-WordPress site, too. =

Just use `mfen.php` directly. That file does the actual work, and it does not depend on the presence of WordPress.

= I found a bug or have a feature request! =

Great, let me know and I'll fix or incorporate it! When I find the time, that is. Which may be never. Contributing patches is fine too!

== Screenshots ==

1. Usage of the script in a post under its default settings.
2. When used in a comment, the image is rendered at a smaller size by default.

== Hey look, I'm using WP_MFEN! ==

Are you using this plugin? Send me an e-mail with a link and I'll add it to this page.

== Contact Me ==

You can reach me at michiel AT wedemandhtml DOT com. Feel free to drop me a note, I appreciate it!