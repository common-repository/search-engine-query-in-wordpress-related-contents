=== Plugin Name ===
Contributors: devu
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2797439
Tags: search engine, bounce rate, similar posts, related posts, internal search, referer, similar articles, related articles, widget, search engine keyword, widget
Requires at least: 2.7
Tested up to: 2.8.4
Stable tag: 1.2.5

If the visitor comes from a known search engine, the widget grabs the used search query and shows internal blog posts that match that query.

== Description ==

The widget aim is trying to reduce the **bounce rate** of your blog and provide the visitor a **better navigation experience**.   

When a visitor lands on your site from a search engine result page, he is in need of a certain information / service your page might satisfy. It often happens the visitor go away just after reading that page, for various reasons. Why not to try suggesting him more posts on the topic he is interested in at that moment, so that he might visit more pages?   
    
The **Search Engine Query in Wordpress** widget grabs the query the visitor used on the search engine, executes it in the blog search and displays results, suggesting the visitor further reading about what he is actually looking for.   
    
If the visitor does not come from a search engine and the current page is the single post template, the widget can display the **most recent posts in the current post category**. There is an option in the widget control panel to turn this feature down. In that case, if the user does not come from the search engine the widget box simply doesn't appear.   
    
The widget is compatible with **WP Super Cache**, just enable the ajax technology on the widget settings.    
    
**The plugin has been tested only on Wordpress 2.7 but should not cause errors on previous versions (it might not work indeed).**    

**The plugin requires Php5 or higher.**     
    
**Widget Features**   
*   If the visitor comes from a search engine result page, the widget grabs the search engine query, executes it in the internal search (with the WP\_Query object) and shows results   
*   If the visitor is browsing a post detail (is\_single()) and the visit source was not a search engine result page, the widget get the current post category and shows the most recent posts in that category   
*   If the "Track clicks" option is enabled in the widget control panel, the plugin adds campaign dimension tags to links in the widget box.   
If your web analytics tool supports them, you'll find a "Search engine query in WP widget" campaign that will allow you to understand how many clicks where produced by the widget box and if they were made from the category search or the search engine query search   
*   The widget layout can be customized by editing the seq\_in\_wp.css css file located in the plugin directory   
*   The widget can execute the search as the main page loads, or after the page has loaded with Ajax technology. **If you choose the ajax search, the widget becomes compatible with WP Super Cache plugin**    
    
**The widget contrtrol panel lets you choose:**   
1.   The widget box title   
2.   The number of related posts to display   
3.   To search by category if the search engine query is not available   
4.   To track clicks with campaign link tags   
5.   To use standard mode or Ajax mode    
6.   To receive log emails when a user lands on the blog and the widget appear (to be used carefully)    
    
**List of supported search engines** (international and any local version):   
Google, Yahoo!, Msn, Ask, Live Search, Lycos, Aol, Dmoz, Altavista, Gigablast, AllTheWeb, HotBot    
    
Please report any bug (or enhancements requests) on the plugin main page: [Search engine query in Wordpress](http://www.francesco-castaldo.com/plugins-and-widgets/search-engine-query-in-wordpress/ "Search Engine Query in wordpress"). I can't monitor the Internet to know if something doesn't work on a certain environment :)     

**Available languages**     
English, Italian, French by [Jerome Rigaud](http://jeromerigaud.com "Jerome Rigaud"), Dutch by Bas Bruinekool.    
    
Send me (fcastaldo[at]gmail.com) the .mo file in your language and I'll add it to the project (with credit of course)!
    
    
== Changelog ==    
= 1.2.5 =     
* jQuery not loading bug solved    
* French and Dutch translations added    
    
     
== Installation ==

1.   Unzip and upload 'search-engine-query-in-wp' directory to your '/wp-content/plugins/' directory   
2.   Activate the plugin through the 'Plugins' menu in the Admin panel. The plugin performs some environment checks and if it's not supported by your server / wordpress version, it automatically deactivates itself explaining what the problem is   
3.   Add the widget to your sidebar in the 'Appearance -> Widgets' section of the Admin panel   
4.   Edit widget settings in the 'Appearance -> Widgets -> Search Engine Query in WP' for fine tuning (Title, number of posts, ajax, email etc.)       
5.   Edit the seq\_in\_wp.css to change the box layout so that it fits your blog theme (not mandatory)   

== Frequently Asked Questions ==

= Will this plugin help me with Search Engine Optimization? =    
    
Nope. It could just improve your internal linking structure, and only if you don't use the Ajax version but it's not what the plugin has been designed for.    
    
= Is this plugin compatible with WP Super Cache? =    
    
Sure! When activating the widget in the sidebar, be sure to check the "Use Ajax Technology" checkbox and save. The plugin will appear in NON cached pages and will stay there also after pages are cached. If you want to see the plugin immediately on all your blog, refresh the cache.    
    
= If my theme does not support widget, or I want related posts displayed in a different position than the sidebar, how can I do? =    

Just paste this snippet in your theme where you want the box to appear:  
   
    <?php if (function_exists('SEQ_in_wp')) SEQ_in_wp(); ?>  
   
Remember you can edit the external css to change the box layout.   
If your theme does not support widgets at all, you might want to wrap the snippet this way:   
   
    <div id="search-engine-query-in-wp">   
    <?php if (function_exists('SEQ_in_wp')) SEQ_in_wp(); ?>   
    </div>    


== Screenshots ==

1.   The widget suggesting more posts depending on the query executed by the search engine that brought to the post   
2.   The widget suggesting more posts in the same category of the current one   
3.   The widget control panel   
4.   Google Analytics view of the widget campaign (note the bouce rate under site avarage). By choosing "Dimension: Source" you can see how many links have been clicked by the "category" results or the "search engine query" results. By choosing "Dimension: Keywords" you can see categories or search phrases clicked. Stats refer to a brand new sites with a few visits.


== Arbitrary section ==
More information: http://www.francesco-castaldo.com/plugins-and-widgets/search-engine-query-in-wordpress/