=== StatPress Visitors ===

Contributors: gawain, luciole135 
Tags: stats, statistics, widget, admin, sidebar, visits, visitors, pageview, feed, referrer, spy, log, spider, bot, page, post, geoip
Requires at least: 2.8
Tested up to: 3.5
Stable Tag: 1.5.4

A fork of StatPress with 9 convenient OPTIONAL pages, including new Bot Spy, Visitor, Referrer and Yesterday pages.

== Description ==

StatPress Visitors (a highly improved fork of StatPress Reloaded) shows the real-time statistics on your blog. It **corrects** **many** programming **errors** of the 
original StatPress and StatPress Reloaded. It collects informations about visitors, spiders, search keywords, feeds, browsers, OS, etc.

This free plugin will no longer be updated. Too much aggression from people who want a plugin professional quality without paying a single penny convinced me to keep to myself my own work and not offer it for free to a community that does not recognize the value of work done.

= I am very pleased to present the fantastic work of **Gawain** **Lynch** in this new version 1.5 =

StatPress Visitors features include:

* [New in 1.5] GeoIP integration. Great job Gawain!
* [New in 1.5] Dashboard widget. Great job Gawain!
* [New in 1.5] Generate report pages for individual IP addresses with the 
  ability to review and mark the records as a Spam Bot, or add to the banned IP 
  address definition file. Great job Gawain! 
* [New in 1.5] New page "URL Monitoring" which shows all URLs requested that do not correspond to the posts and pages written by an author in order to deny access to intruders or hackers.

* ALL **search** **engines**, **spiders**, **RSS** **feeds**, **browsers** and **OS** are represented by their **logo**. **Internet** **domains** and  **countries**, are represented by their national **flag**. All icons, flags and logo display the correct name via a **tooltip** on mouse-over. 
* Bot Spy page has been added, showing which pages were indexed by search robots
* Referrers page has been added, showing the referrers bringing traffic 
  to your website.
* Yesterday page shows the site traffic as of the previous day
* Report Pages are now optional and can be enabled and disabled in the 
  Options page. The can help busy sites by freeing up some memory as the pages
  aren't generated 
* The plugins administration subpages are no longer stored in RAM when a visitor visits the site, this frees up RAM unnecessarily consumed otherwise. The administration subpages are loaded into memory only if the Dashboard is  visible. Thanks to xknown.
* FULL PHP 5.4 compatibility

* A **new** accounting method that significantly reduces the number
  of SQL queries in the main page by the use of the **Set** **Theory**
	* Graphs are generated in only 5 SQL queries making StatPress Visitors faster 
	  than all others fork of StatPress -- 2 seconds with a database of 45,000 rows, 
	  compared to more than 10 seconds using StatPress alternatives
	* The new accounting method allows the tracking of visitors, page views, search
	  engines and RSS feeds for each page, giving an **accurate** view of traffic 
	  to your website!

* See the number of unique visitors, page views, subscriptions to RSS feeds and 
  search engines for each page and posts of your website for every day saved in 
  the database by graphs of 7, 15, 21, 31 or 62 days depending on the option 
  chosen

* Visitor Spy page (log of visits) has been redesigned and now sorts the display 
  starting with the most recent visit. This corrects an error of StatPress and 
  StatPress Reloaded.

* Options to set the number of IP addresses displayed on each page (20, 50 or 
  100) and the number of visits for each IP address (20, 50 or 100). 

* Visitors Spy and Bot Spy pages now have **optimised** **SQL** **queries**
    * Uses the natural index of the database table
    * They are made in only a **single SQL query** making StatPress Visitors 
      more than 3 times faster than all other forks of StatPress!

* Visitors, Views and Feeds pages show the traffic to your site for each page, 
  on graphs of 7, 15, 21, 31 or 62 days from the largest to the smallest 
  traffic.

* Optionally ignore statistics collection for logged in users and bots


= DB Table maintenance =
StatPress Visitors can automatically delete older records to allowing insertion of newer records when your space is limited. In these case the data table is 
automatically optimised after the purging of old records.


= StatPress Widget / StatPress_Print function =
The widget is customisable. These are the available variables :

* %today% - Today's date
* %since% - Date of oldest record in the StatPress database table
* %os% - Operating system of current visitor
* %browser% - Browsers User Agent string of the current visitor
* %ip% - IP address of the current visitor
* %latesthits%  - The 10 last pages read
* %visitorsonline% - Number of visitors currently online 
* %usersonline% - Number of logged in users currently online 
* %todayvisitors% - Total number of visitors for today
* %todaypageviews% - Total number of page views for today
* %thistodayvisitors% - Today's total number of visitors for current page
* %thistodaypageviews% - Today's total number of page views for current page

**Warning**: These variables can cause a break in service when the database is very large (> 200,000 lines) and should be avoided:

* %totalvisitors% - Total number of visitors to date
* %totalpageviews% - Total number of page views to date
* %thistotalvisitors% - Total number of visitors for current page
* %thistotalpageviews% - Total number of page views for current page
* %toppost% - Most read (popular) post
* %topbrowser% - Top browser
* %topos% - Top operating system 

You can add these values everywhere! StatPress offers a new PHP function 
StatPress_Print(). 

Put it in your template where you want the details to be displayed. Remember, 
as this is PHP, it needs to be surrounded by PHP tags!

Example:
	`<?php
	StatPress_Print("This page has been viewed %thistodaypageviews% times today.");
	?>`

	
= Ban IP addresses from StatPress Visitors logging =
You can ban IP addresses, preventing them from being included in your stats 
by editing the file def/banips.dat in the StatPress Visitors plugins directory.


= Update StatPress Definitions =
You can choose the data to update in your database (browsers, OS, search engines
and spiders).  Text matches below are based on part string matches. 

* Browsers

The format for browsers (browser.dat) is:
	[name] | [user agent text to match -without all space caracters-] |

e.g.
	Chromium 15|Chrome/15|

* Operating Systems

The format for operating systems (os.dat) is:
	[name] | [user agent text to match -without all space characters-] |

e.g.
	Linux Android|Android2.2.1|

* Spiders

The format for spiders (spider.dat) is:
	[name] | [user agent text to match] |

e.g.
	Google|googlebot|

* Spam bots

The format for spam bots (spambot.dat) is:
	[name] | [user agent text to match] |

e.g.
	Purebot Spam Bot|Purebot|

* Search engines

The format for search engines (searchengine.dat) is:
	[name] | [domain url part] | [query search key text] | [query search key stop] |
note: there is 4 pipes each line.

e.g.
	Google|www.google.|q||	

So for a search engine who have a URL like Google :
	http://www.google.fr/search?q=statpress+visitors

From this example you can see that the domain part of the URL is "www.google.fr",  
however as a number of search engines use regional domains, you need only enter
the www.google. part.

Secondly, notice the "q=" in the URL, that is the query search key text.  

note: in these case [stop] is empty.

Some few Search engine have a URL not like the Google URL but like this one :
http://fr.eannu.com/benson_platinum.htm
In the case the format for search engine is still:
	[name] | [domain url part] | [query search key text] | [query search key stop] |

e.g.
Eannu|fr.eannu.com|fr.eannu.com/|.htm|

note: the query text is the text betwwen [query search key text] and  [query search key stop]. 
Secondly, [domain url part] must be **include** in [query search key text], there is still 4 pipes | each line.

= Update images =
When image is name.png

1. The **name** of the image is the first part of the corresponding line of the definition file in /daily-stat/def
name|...|etc
1. Write the name of the browser, the OS, the Search engine, the spider with **lowercase**
1. Replace all the characters « space » by « _ » (underscore).
1. Replace all characters « point » by "-" (dash).
 For example, if you added the name Safari Mobile 7.0 in the def/browser file, the filename of the image must be safari_mobile_7-0.png 
 If you add the name Search.com in def/searchengine, the filename of the image must be Search-com.png

e.g. 
the definition of Safari Mobile 7.0 like:
 Safari Mobile 7.0|Version/7.0.0.400MobileSafari/534.11| in def/browser
correspond to the image safari_mobile_7-0.png in images/browser
  
note: the heigth of all the images is fixed to **16** pixels.

== Installation ==

1. Unzip file and Upload "StatPress-visitors" directory in wp-content/plugins/ 
1. Then just activate it on your plugin management page. That's it, you're done! 
1. Note: If you have been using an other compatible StatPress plugin **deactivate it** before enabling StatPress Visitors. Existing data will be used!
1. If enabling GeoIP support, you must either:

- use the download button on the "GeoIP" tab of the StatPress Visitors Option Page

- or manually download the database files to your wp-content/GeoIP/ directory, the database files can be found on MaxMind's website:  

* http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
* http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz

You must **unzip** these file before use with the FREE 7-Zip (http://7-zip.org/) or a similar ZIP program

see here : http://www.maxmind.com/app/installation?city=1

== Frequently Asked Questions ==

= What is the difference between "Visitors Feeds" and "Page Views feeds"? =
Quite simply, if a single visitor subscribed to RSS feeds on 5 pages of your website, then "Visitors Feeds" is 1 and  "Pageviews Feeds" is 5.

= Why "Visitors Feeds" and "pageviews feeds" are not the same count in the pages "yesterday" and in the "Main Overview"? =
This is because the calculations are not the same!
On the "Main Overview", all pages are counted, even those that are automatically generated by WordPress (category, etc.).
On the "Yesterday", only the pages you have actually written and that are stored in your database are taken into account, 
those generated by WordPress are not counted.

= An user says: "I’ve use StatPress V on a few sites and noticed that the visitor total is never accurate. =
I’ve put it on a new site just a couple of days ago and already the vistor total is wrong. It says 211 (3 days) but if I add the individual 
day unique visits 147 + 91 + 68 i.e 306. So after 3 days the total is already almost 100 visitors inaccurate".
It’s because the calculation isnt the same. In the main page, the unique visitors of 3 days isnt the total of the unique 
visitor of each day : the same IP is counted one time in 3 day and it’ is counted 1 time each day in the graph !
This way of counting of the main page is carried from the original StatPress, change it made a very long software with more SQL queries.
The SQL query count the DISTINCT IP in the 3 day, not the DISTINCT IP each day.

= Isn’t it possible to make it work with network of sites ? =
You can use http://wordpress.org/extend/plugins/proper-network-activation/

= Is there a way to block my own visits to my blog page? =
Yes, simply add your IP in the def/banips.dat file.

== Screenshots ==
http://www.flickr.com/photos/59604063@N05/sets/72157626522412772/

== Changelog ==
= 1.5.4 =
*Correct a XSS into URL Monitoring page, thanks to aramosf
*new definitions and images

= 1.5.3 =
*Correct a bug on the detection of IP behind PROXY. Thanks to natli. Works only with PHP >= 5.2.0
*News definitions and images.
*Correct bug on the display on table 
*hyphenation long URL

= 1.5.2.1 = 
*deletion of / giving a wrong path with // i.e 
*replace STATPRESS_V_PLUGIN_PATH.'/  by  STATPRESS_V_PLUGIN_PATH.' and STATPRESS_V_PLUGIN_URL.'/ by STATPRESS_V_PLUGIN_URL.' in all files. thanks to petesky

= 1.5.2 =
*News definitions and images

= 1.5.1.1 =
*Same as 1.5.1 problem on repository, sorry

= 1.5.0.5 =
*Correct the displayed number of row when choose 250 in the main page

= 1.5.0.4 =
*correect filter on ip

= 1.5.0.3 =
*Correct bug on widgets

= 1.5.0.2 =
*Correct the JS who deactive HTML edition button

= 1.5.0.1= 
* Renamed the function "message" to avoid incompatibilities between plugins. 
* Add some search engine

= 1.5 =
= I am very pleased to present the beautiful work of Gawain Lynch in this new version, there is =
* Style pagination
* AJAX enable tables in admin interface to allow for dynamic row count changes
* Add initial JQuery support to admin interface.  Tested up to 3.3.x
* Per IP custom report pages
* Ability to mark IP address as spam bot in database from IP Report page
* Ability to ban IP address from IP Report page
* GeoIP integration - Needs database files to be installed via options page!
* Hooked GeoIP City optionally into Visitor Spy page when enabled.  MUCH FASTER!
* Add Google Maps link to lat/long when GeoIP.  You can now stalk your visitors with greater ease!
* Dashboard widget based on StatPress Dashboard Widget Lite by Andreas Kaul
* Export now uses blog URL as basis for the file name
* Undefined agents now grouped by IP address (latest occurrence shown)
* Undefined agents shows count of times it appears in database
* Undefined agents text links to Google query for its name
* Remove /uploads/ from logging stats
* Major redesign of Options page
* Serialize options to minimise the I/O to the database
* Add option to export to different file extensions
* Added option to Top Posts widget to include/remove "Pages"
* Added option to Top Posts widget to only calculate based on X number of days
* Added option to not collect known SpamBot visits
* Added option to treat blank user agents as SpamBots
* Add option to options to select default table rows
* Add initial support for spambot identification
* Lots of performance tweaks
* Initial code clean up
* Fixed WordPress post/page name resolution. Refactored existing function to simplify and make consistent regardless of WP setup.
* [BUG] Database update page: Dont run createtable() every single time.
* [BUG] Strip redirection portion of search URL when rendering href on admin page.
* [BUG] Fixed export of large datasets by breaking query into 500 rows per time
* More sensible graphs and defaults in statistics admin page

= My only job is =
* Add new columns in the database for more efficiency : realpost and post_title
* Creation of the URL Monitoring page
* Add usuals actions on activate, deactivate, uninstall of the plugin
* Combine "yesterday" and "today" queries for efficiency in the main page
* PHP and SQL optimisation of the yesterday page, work 7 times faster.
* Make front page queries more efficient
* Fix URL parsing from referring sites like Facebook
* Combine "yesterday" and "today" queries for efficiency
* Updated browser definitions and images
* Updated domain definitions and images, new definitions work with 2 alpha code, 3 alpha code or 3 digits code
* Updated language definitions
* Updated OS definitions and images
* Updated search engine definitions and images
* Updated spider definitions and images
* correct some minors bugs
* Adding functions to the activation, deactivation and uninstallation of the plugin in order to ensure full compatibility with all other derivatives StatPress.

= We both do =
* Better detection of IP behind PROXY
* Optimize the data type of the database and add an INDEX on the date, it significantly increases the speed of excecution of most pages.
* PHP and SQL optimisation of the yesterday page, work 7 times faster.

= 1.4.3 =
* Replacement of all the WordPress functions deprecated by the new WordPress functions.
* Add a new table in the main page : "Undefined agent", the agent without definition in StatPress Visitors, then you can update it by yourself.
* FULL PHP 5.3 and higher compatibility

= 1.4.2 =
* Put again the correct file of 1.4.1 in the repository systeme of WordPress who dont work very well.
* Add .arpa domain in the domain and image
* new definition of Opera 11.5
* Dont display the name of browsers and OS, u will see their name with Tooltip
* dont made abrevia on the name of page in the main page

= 1.4.1 =
* The tables "last terms search", "Last referrers", "Last Feeds" and "Last spiders" on the main page are more informatives. 
* New update field **domain**
* PHP optimization : StatPress Visitors 1.4 make more with less memory RAM use than the previous versions.
* PHP and MySql optimization, work between 8% and 15% faster in main page. Work 2 times more faster in "Visitors", "Views", "Feeds" and "referrer" pages. Thanks to Guy.
* FULL PHP 5.3 and higher compatibility
* On "Bot spy", "more info" show the agent and ip of the bot.
* **Spam** **Bots** are detected with new definitions.
* Add a version of the database to make a possible upgrade, thanks to kittz.

= 1.4.0.1 =
* correct the variables who use "today"
* Add the IP to the referrer page.
* Do no display the user on the feed table (on main page) if do no collect logged user is checked
* Change the text in initialization of the widget.
* correct the definition of the country GB Great Britain use by some browsers (and not United Kingdom who is uk) 

= 1.4 =
* Optionals pages more convenients, simply click now in the "Options page" on the pages you do not wish to appear. 
* ALL **logos** and **icons** with tooltip.
* Two new informations : the **language** and **country** in addition to the internet domain. 
* On the "spy visitors" page, the flag displayed in the first place is the country given by the visitor's browser (preceded by "http country"), if it is not known then, secondly, it's the flag of the internet domain that is displayed (preceded by "http domain"). If neither is given, then querying the free internet service "hostip.info" (preceded by "hostip country").
* In the main page, the country's flag is displayed only if different from the Internet domain. If the same flag is displayed, then the tooltips do not give the same indication. Indeed, some Internet domains correspond to several countries and some countries have regions with theirs own internet domain.
* The functions of the administration part of the plugin are no longer stored in RAM when a visitor visits the site, this frees up RAM unnecessarily consumed otherwise. The functions and administration pages are stored in memory RAM only if the Dashboard is visible.
* The tables "last terms search", "Last referrers", "Last Feeds" and "Last spiders" on the main page are more informatives. 
* New update field **domain**
* PHP optimization : StatPress Visitors 1.4 make more with less memory RAM use than the previous versions.
* PHP and MySql optimization, work between 8% and 15% faster in main page. Work 2 times more faster in "Visitors", "Views", "Feeds" and "referrer" pages. Thanks Guy.
* FULL PHP 5.3 and higher compatibility
* On "Bot spy", "more info" show the agent and ip of the bot.
* Spam Bots are detected with new definitions.
* correct the variable who use "today", thanks to Markvandark

= 1.3.1 =
* Correct a memory use bug.
* Optimize PHP Overview main page inherited from original StatPress (0,07 MB use less).

= 1.3 =
* New page update replaces the previous two inherited from the original StatPress not working (another mistake). Now it work. You can choose the datas you want to update (Browsers, OS, Searchs engines and spiders)
* Added to the main page, a new table **"Last** **Feeds"** with the columns :  date, time, page, feed, user
* Better design of the page "visitors", "views", "feeds" and "referrer". Now, there is a row above the graphs indicate the page/post/URL, the total number of visits and the average daily visits.
* New design of the general statistics  page inherited from the original StatPress much more enjoyable.
* New color for the "referrer" : green, more readable.
* When you have selected "no collect spiders", the spiders datas are not displayed on all pages
* correct an error, in very few situation, if you change of period day in graph twice in a day, this update is not working
* Correction of errors on widgets and variables. Now it works.

= 1.2 = 
* fix bug on 1.1.2
* All the pages are OPTIONALS (except "main" and "options"). How it works: all the pages in the folder wp-content/plugins/StatPress-visitors/pages are optionals. If you do not want to use a page, delete the file via FTP (with Filezilla, par ex) in the folder and this will free some RAM. If you want to use it again, add it in this same folder, simply.
* the page "yesterday" show all your pages, with or without visits.
* I corrected an error in the page yesterday on account of Feeds and Visitors Pageviews Feeds. 
* added %since% and %totalvisitors% that I deleted by mistake
* update some OS and browsers definitions

= 1.1.2 =
* sorry, the 1.1 and 1.1.1 file wasnt the finals file, this it the final file.

= 1.1 =
* Added a new page "yesterday" with the results of site traffic at the time of yesterday.
* SQL queries optimization in the pages "Visitors Spy" and "Spy Bot" by the use of Set Theory. Now these pages are made in only one SQL query. The previous versions and all others fork of StatPress need as many SQL queries that there is IP or Bot displayed on the page. The speed is 3 times faster than the previous version and than all other fork of StatPress.
* Detection of the referring page when the referrer is Facebook. In this case, in previous versions, all page views were called "fb_xd_fragment", now, their real name is displayed.
* Added a new way to count the RSS feed by IP. Thus, there are two separate counts of RSS: as far as total subscription on every page (pageviews feeds), as far as visitors subscribers(visitors feeds).
* Every day, automatic optimization of the data table "StatPress" when the "autodelete" option is on. The data table is optimize after the removal of olds data. Then, now, the data table 'StatPress' is always optimized.
* New count of the RSS Feeds : Count 1 feed by IP (more realistic way of count).
* Correct the count of the variable %toppost%

= 1.0.10 =
* correct "spy visitor" to work like in version 1.0.5 and lower : display "arrived from...searching..."

= 1.0.9 =
* add these variable  %thistotalpageviews% - this page, total pageviews

= 1.0.8 =
* correct an URL error on 'Spy visitors' and 'Spy bot' page when there are multiple pages.

= 1.0.7 =
* Better URL for StatPress-Visitors pages.
* correct an URL error on 'Overview' page when there are multiple pages.
* New menu icon.

= 1.0.6 =
* Now when selecting one of the StatPress Visitors pages, such as visitor spy, the menu indicates that it is this page who is selected (shaded background & notch on left side).
* The main menu item is now "StatPress V" to keep it on a single line.

= 1.0.5 =
* this version correct some error when dadabase is empty 

= 1.0.4 =
 this version correct minimum capability to view stat

= 1.0.3 =
* This version 1.0.3 optimize some SQL query in "visitor and view" page, then it work a little faster.

= 1.0.2 =
* This version 1.0.2 optimize some SQL query in "feed page".

= 1.0.1 =
* StatPress-visitors 1.0.1 correct a SQL query to work faster in "Overview" main page.
* This version 1.0.1 is much faster in displaying the main "Overview" page.
* add Cityreview spider in def/spider.dat

== Upgrade Notice ==
