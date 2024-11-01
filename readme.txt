=== Plugin Name ===
Contributors: geilt, sudravirodhin
Donate link: http://www.esotech.org/
Tags: facebook, feed, widget, facebook page, posts, fbook
Requires at least: 3.4
Tested up to: 3.4.2
Stable tag: trunk

== Description ==

Enables a widget that will pull a facebook page or personal feed via JSON by Facebook ID and display text and/or images in posts. Supports Images, Image Galleries (as Slider), Links, Image Links, Image Descriptions and more. Fully customizable widget. 

[More Details](http://www.esotech.org/plugins/simpul/simpul-facebook/)

== Installation ==

1. Upload the plugin folder into to the `/wp-content/plugins/` directory or search "Simpul Youtube by Esotech" and install.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Appearance -> Widgets
4. Drag the "Youtube" Widget into the Sidebar you want to use.
5. Use http://findmyfacebookid.com/ to find your Facebook ID.
5. Choose the options as you need them.

== Frequently Asked Questions ==

= How do I get my videos to Display? =

Look for "Facebook" under Appearance -> Widgets. Drag it into a sidebar, type your Youtube name ex:109122432465903 into "Facebook Account ID" then choose the number of posts you wan't to display. Click Save.

= How do I find my Facebook ID? =

There are many methods to find your Facebook ID. You can either Google the methods or use this easy method at http://findmyfacebookid.com/ to find your Facebook ID.

= How does Caching Work? =

Caching keeps a local copy of your posts at the interval specified so you don't have to constantly contact Facebook. This saves on site load speed because you don't have to make an external connection.

= I am updating my widget settings, but nothing is changing, why? =

Try disabling the cache, saving, refreshing the page where you widget displays, then turning cache back on. Cache saves data into wordpress, so updating settings won't reflect until the next cache update. 

== Changelog ==
 
= 2.2.6 =
* Class name is now simpul-facebook and id is simpul_facebook
* Added link to http://findmyfacebookid.com/ to find your Facebook ID.
= 2.2.5 =
* Derped and didn't use wordpress standard for before_widget and after widget. Sorry. Updated. 
= 2.2.4 =
* Found and removed reference to SimpulEvents static function. 
= 2.2.3 =
* Added check for slider gallery to prevent inappropriate height for non-slider galleries.
= 2.2.2 =
* Javascript not a reliable method to set gallery height. PHP solution found and implemented.
= 2.2.1 =
* Galleries were not the correct height, causing style problems. Fixed this by having it match the height of the images therein.
= 2.2 =
* Removed upscale, fixed bug that prevented it from happening as it did before.
* General code cleanup, added a div around \<a\> tags again.
= 2.1.2 =
* Added a checkbox to upscale images. (BETA, still testing)
= 2.1.1 =
* Facebook thumbnail size fix.
= 2.0.1 =
* Fixed a cache bug that appeared after the overhaul.
= 2.0 =
* Massive overhaul. Performance increased and all data should be reliably displayed. Empty/deleted posts are now skipped.
* Please reconfigure your widgets, option variable names have changed and their text has been reworded for clearer understanding.
= 1.5.4 =
* More code cleanup.
= 1.5.3 =
* Cleaned up cache code.
= 1.5.2 =
* Fixed PEBKAC programming error.
= 1.5.1 =
* Fixed nasty bug with caching.
= 1.5 =
* Added caching capability.
= 1.2.3 =
* Updated script queuing for efficiency and to prevent any conflict with other plugins, including other simpul plugins.
= 1.2.2 =
* Added Position: Relative to image galleries to ensure height and width wont break layouts. 
= 1.2.1 =
* Added condition to jQuery Loading possibly causing backend problems for Widgets on some installs. 
= 1.2 =
* Cleanups + Added Thumbnail Resizer
= 1.1 =
* Cleanups + Added Slider Capability.
= 1.0 =
* First Upload
