=== Plugin Name ===
Contributors: dbouman
Tags: track the book, google, maps, kml, books, books, locations
Requires at least: 2.5
Tested up to: 2.8.4

Creates a dynamic KML file of book numbers and locations that were entered by visitors.

== Description ==

This plugin gives visitors the option to track their book by entering their location and book number into a form. 
Their location is then turned into coordinates through the use of the Google Maps API and is able to be plotted onto 
a map. A KML file is also dynamically generated and can be displayed in a map using a separate plugin that reads KML 
files (i.e  XML Google Maps).

== Installation ==

1. Download the archive file and uncompress it.
2. Put the "track-the-book" folder in "wp-content/plugins"
3. Enable the plugin by visiting the "Plugins" menu in WordPress and activating "Track The Book".
4. Go to the "Track The Book" settings page under the "Settings" menu and enter your Google API Key. If you do not
have a Google Maps API Key, you can get one for free at http://code.google.com/apis/maps/signup.html.

To display the link to access the Track The Book form, you would include the following code in your post:
`[trackthebook]Register my book[/trackthebook]`

Or to include the link directly inside of your WordPress template, add the following code:
`<?php echo do_shortcode('[trackthebook]Register my book[/trackthebook]'); ?>`

The address to access the dynamic KML file is http://your/wordpress/site/?view=trackthebook.kml

To use your KML file in conjunction with with XML Google Maps, you would add the following code to your post:
`[xmlgm {http://your/wordpress/site/?view=trackthebook.kml}]`

== Frequently Asked Questions ==

= How do I activate this plugin? =

Enable the plugin by visiting the "Plugins" menu in WordPress and activating "Track The Book".

= How do I add a link to the form to let visitors register their book? =

To display the link to access the Track The Book form, you would include the following code in your post:
`[trackthebook]Register my book[/trackthebook]`

Or to include the link directly inside of your WordPress template, add the following code:
`<?php echo do_shortcode('[trackthebook]Register my book[/trackthebook]'); ?>`

= How do I display a map that plots all of the books that visitors have entered? =

You must use a separate plugin to actually display an interactive map. Any plugin that is able
to read KML files will work. 

I have tested it in conjunction with the [XML Google Maps](http://www.matusz.ch/blog/projekte/xml-google-maps-wordpress-plugin-en/) WordPress plugin.

= What is the URL of the dynamic KML file? =

http://your/wordpress/site/?view=trackthebook.kml

= Why does my map not update immediately? =

If you are using Google Maps, you may find that Google will cache your KML file for a period of time. In order to 
prevent the KML file from being cache you need to append a random code to the end of the KML address.

`http://your/wordpress/site/?view=trackthebook.kml&nocache=<?php echo time(); ?>`

== Screenshots ==

1. An example of the Track The Book plugin in action at Howard County Library.

