<?php

/*
 Plugin Name: Statpress Visitors
 Plugin URI: http://additifstabac.webuda.com/index.php/statpress-visitors-new-statistics-wordpress-plugin/
 Description: Improved real time stats for your blog
 Version: 1.5.4
 Author: luciole135, gawain 
 Author URI: http://additifstabac.webuda.com/index.php/statpress-visitors-new-statistics-wordpress-plugin/
*/

// Initialise options variable
global $wpdb, $StatPressV_Option;
$StatPressV_Option = get_option('StatPressV_Option');

define("STATPRESS_V_VERSION", "1.5");
define("STATPRESS_V_DB_VERSION", "1.5");
define("STATPRESS_V_PLUGIN_URL", plugin_dir_url( __FILE__ ));
define("STATPRESS_V_PLUGIN_PATH", plugin_dir_path( __FILE__ ));
define("STATPRESS_V_TABLE_NAME", $wpdb->prefix . "statpress");

$table_name = STATPRESS_V_TABLE_NAME;

register_activation_hook( __FILE__,'StatPressV_activate');
register_deactivation_hook( __FILE__,'StatPressV_deactivate');
register_uninstall_hook( __FILE__,'StatPressV_uninstall');

// call the custom function on the init hook
add_action('plugins_loaded', 'StatPressV_Widget_init');
add_action('send_headers', 'luc_StatAppend');

if (is_admin())
{
	include STATPRESS_V_PLUGIN_PATH . 'admin/luc_admin.php';

	add_action('init', 'StatPressV_load_textdomain');
	add_action('admin_menu', 'luc_add_pages');
	add_action('admin_footer', 'StatPressV_admin_footer');
	add_action('admin_init', 'StatPressV_admin_init');
	
	if ($StatPressV_Option['StatPressV_Use_Widget_Dashboard'] == 'checked')
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/luc_admin_dashboard_widget.php';
		add_action('wp_dashboard_setup', 'luc_StatPressV_Add_Dashboard_Widget');
	}
}

// Use GeoIP? http://geolite.maxmind.com/download/geoip/api/php/
if ($StatPressV_Option['StatPressV_Use_GeoIP'] == 'checked' && (!class_exists('geoiprecord')))
	include_once STATPRESS_V_PLUGIN_PATH . 'GeoIP/geoipcity.inc';
	
if ($StatPressV_Option['StatPressV_activate'] != 'installed_activation')
     add_action('admin_notices', 'StatPressV_message');

function StatPressV_activate() 
	{
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	$old_Install = false; 
	if ( $StatPressV_Option['StatPressV_activate'] == 'installed_activation')
        $old_Install = true; 
	elseif ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name)
		 $old_Install = true; 
		
    if ($StatPressV_Option['StatPressV_DB_Version'] <> STATPRESS_V_DB_VERSION)
				{	
				   $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE COLLATE utf8_bin '%statpres%'");
				   $wpdb->query('OPTIMIZE TABLE ' . $wpdb->options);
				   luc_StatPressV_CreateTable();
				};
		
	if ($old_Install == true)
	   $StatPressV_Option['StatPressV_activate'] = 'update_activation';
	else
	    $StatPressV_Option['StatPressV_activate'] = 'install_activation';
	update_option('StatPressV_Option', $StatPressV_Option);
	}
	
function StatPressV_message() 
	{global $StatPressV_Option;
     $opt = $StatPressV_Option['StatPressV_activate'];
	 if ( $opt == 'install_activation' || $opt == 'update_activation')
	 {
    $StatPressV_Option['StatPressV_activate'] = 'installed_activation'; 
	update_option('StatPressV_Option', $StatPressV_Option);
    $msg = __('Settings activated: ', 'StatPressV_domain' );
    $msg .= ($opt == 'install_activation') ? __('StatPress Visitors database created', 'StatPressV_domain' ) : __('Statpress Vistors database updated', 'StatPressV_domain' );
    echo "<div class='updated'><p><strong>$msg</strong></p></div>";
     }
	}
	
function StatPressV_deactivate()
    {
		$StatPressV_Option['StatPressV_DB_Version'] = '1.1';
		update_option('StatPressV_Option', $StatPressV_Option);
		remove_action('init', 'StatPressV_load_textdomain');
		remove_action('admin_menu', 'luc_add_pages');
		remove_action('admin_footer', 'StatPressV_admin_footer');
		remove_action('admin_init', 'statpressV_admin_init');
		remove_action('wp_dashboard_setup','luc_StatPressV_Add_Dashboard_Widget');
		remove_action('plugins_loaded', 'StatPressV_Widget_init');
		remove_action('send_headers', 'luc_StatAppend');
		remove_action('wp_ajax_table_latest_hits', 'luc_main_table_latest_hits');
		remove_action('wp_ajax_table_latest_search', 'luc_main_table_latest_search');
		remove_action('wp_ajax_table_latest_referrers', 'luc_main_table_latest_referrers');
		remove_action('wp_ajax_table_latest_feeds', 'luc_main_table_latest_feeds');
		remove_action('wp_ajax_table_latest_spiders', 'luc_main_table_latest_spiders');
		remove_action('wp_ajax_table_latest_spambots', 'luc_main_table_latest_spambots');
		remove_action('wp_ajax_table_latest_undefagents', 'luc_main_table_latest_undefagents');
		remove_action('wp_ajax_geoipdbupdate', 'luc_GeoIP_update_db');
    }

function StatPressV_uninstall()
    {	
		global $wpdb;
		$table_name = STATPRESS_V_TABLE_NAME;
		delete_option('StatPressV_Option');
        //$wpdb->query("DROP TABLE `$table_name`"); 
    }
	
// a custom function for loading localization
function StatPressV_load_textdomain()
{ //check whether necessary core function exists
	if (function_exists('load_plugin_textdomain'))
	{
		//load the plugin textdomain
		load_plugin_textdomain('statpressV', false, STATPRESS_V_PLUGIN_PATH . 'locale');
	}
}
function luc_permalink()
{ //return personalized permalink if exist
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;
	$permalink = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'permalink_structure';");
	$permalink = explode("%", $permalink);
	return $permalink[0];
}

function luc_StatPressV_CreateTable()
{
	global $wpdb, $wp_db_version, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	$sql_createtable = "CREATE TABLE " . $table_name . " (
		id MEDIUMINT(9)UNSIGNED NOT NULL AUTO_INCREMENT,
		date INT(8) UNSIGNED NOT NULL,
		time CHAR(8),
		ip VARCHAR(39),
		urlrequested TEXT,
		agent TEXT,
		referrer TEXT,
		search TEXT,
		os TINYTEXT,
		browser TINYTEXT,
		searchengine TINYTEXT,
		spider TINYTEXT,
		feed TINYTEXT,
		user TINYTEXT,
		timestamp INT(10) UNSIGNED NOT NULL,
		language VARCHAR(3),
		country VARCHAR(3),
		realpost BOOLEAN,
		post_title TINYTEXT,
		UNIQUE KEY id (id),
		KEY `date` (`date`)
		);";
	if ($wp_db_version >= 5540)
		$page = 'wp-admin/includes/upgrade.php';
	else
		$page = 'wp-admin/upgrade-functions.php';
	require_once (ABSPATH . $page);
	dbDelta($sql_createtable);
	// update the database version
	$StatPressV_Option['StatPressV_DB_Version'] = STATPRESS_V_DB_VERSION;
	update_option('StatPressV_Option', $StatPressV_Option);
	
	// Remove useless column from some statpress
	$wpdb->query("ALTER TABLE $table_name DROP COLUMN threat_score");
	$wpdb->query("ALTER TABLE $table_name DROP COLUMN threat_type"); 
	$wpdb->query("ALTER TABLE $table_name DROP COLUMN nation"); 
	
	// Remove useless column from StatpressCN
	$wpdb->query("ALTER TABLE $table_name DROP COLUMN ptype");
	$wpdb->query("ALTER TABLE $table_name DROP COLUMN pvalue"); 
	$wpdb->query("ALTER TABLE $table_name DROP COLUMN statuscode");
	
    // Remove useless index from NewStatPress 	
	$wpdb->query("ALTER TABLE $table_name DROP INDEX spider_nation");
	$wpdb->query("ALTER TABLE $table_name DROP INDEX ip_date");
	$wpdb->query("ALTER TABLE $table_name DROP INDEX agent");
	$wpdb->query("ALTER TABLE $table_name DROP INDEX search");
	$wpdb->query("ALTER TABLE $table_name DROP INDEX referrer");
	$wpdb->query("ALTER TABLE $table_name DROP INDEX feed_spider_os");
	$wpdb->query("ALTER TABLE $table_name DROP INDEX os");
	$wpdb->query("ALTER TABLE $table_name DROP INDEX date_feed_spider");
	$wpdb->query("ALTER TABLE $table_name DROP INDEX feed_spider_browser");
	$wpdb->query("ALTER TABLE $table_name DROP INDEX browser");
	
	// Remove useless index from Statpress-Seolution
	$wpdb->query("ALTER TABLE $table_name DROP INDEX time_hour");
	$wpdb->query("ALTER TABLE $table_name DROP INDEX date_time");
	$wpdb->query("ALTER TABLE $table_name DROP INDEX spiders");

	// Remove useless Row from Statpress-visitors
	$wpdb->query("DELETE FROM $table_name WHERE ip IS NULL");
	$wpdb->query('OPTIMIZE TABLE ' . $table_name);
}

function luc_StatAppend()
{
	global $wpdb, $StatPressV_Option, $userdata;
	$table_name = STATPRESS_V_TABLE_NAME;

	get_currentuserinfo();
	$feed = '';

	// Time
	$timestamp = current_time('timestamp');
	$vdate = gmdate("Ymd", $timestamp);
	$vtime = gmdate("H:i:s", $timestamp);

	// IP
	if (strnatcmp(phpversion(),'5.2.0') >= 0) 
		$ipAddress = htmlentities(luc_get_ip());
	else $ipAddress = htmlentities($_SERVER['REMOTE_ADDR']);
	if (luc_CheckBanIP($ipAddress) === true)
		return '';

	// URL (requested)
	$urlRequested = luc_StatPressV_URL();
	$post_title=luc_post_title_Decode($urlRequested);
	$real_post=(($post_title==$urlRequested )? 0 : 1);

	$referrer = (isset ($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '');
	$userAgent = (isset ($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : '');
	$spider = luc_GetSpider($userAgent);

	if (($spider != '') and ($StatPressV_Option['StatPressV_Dont_Collect_Spider'] == 'checked'))
		return '';

	if ($spider != '')
	{
		$os = '';
		$browser = '';
	}
	else
	{
		// Trap feeds
		$prsurl = parse_url(get_bloginfo('url'));
		$feed = luc_StatPressV_Is_Feed($prsurl['scheme'] . '://' . $prsurl['host'] . htmlentities($_SERVER['REQUEST_URI']));
		// Get OS and browser
		$os = luc_GetOS($userAgent);
		$browser = luc_GetBrowser($userAgent);
		$refsearch = luc_GetSE($referrer);
		if ($refsearch !== null)
			list ($searchengine, $search_phrase) = explode("|", $refsearch);
		else
		{
			$searchengine = "";
			$search_phrase = "";
		}
	}

	$code = explode(';', htmlentities($_SERVER['HTTP_ACCEPT_LANGUAGE']));
	$code = explode(',', $code[0]);
	$lang = explode('-', $code[0]);
	$language = $lang[0];
	$country = $lang[1];

	if (((!isset($lang[1])) or $StatPressV_Option['StatPressV_locate_IP'] == 'GeoIP') & ($StatPressV_Option['StatPressV_Use_GeoIP'] == 'checked' & function_exists('geoip_open')))
				{	// Use GeoIP? http://geolite.maxmind.com/download/geoip/api/php/
					// Open the database to read and save info
					$gi = geoip_open(luc_GeoIP_dbname('country'), GEOIP_STANDARD);
					$cc = geoip_country_code_by_addr($gi, $ipAddress);
					if ($cc !== false)
						$country = $cc;
					else
						$country = NULL;
				}
	
	// Auto-delete visits if...
	$today = gmdate('Ymd', current_time('timestamp'));
	if ($today <> $StatPressV_Option['StatPressV_Delete_Today'])
	{
		$StatPressV_Option['StatPressV_Delete_Today'] = $today;
		if ($StatPressV_Option['StatPressV_AutoDelete_spider'] != '')
		{
			$t = gmdate("Ymd", strtotime('-' . $StatPressV_Option['StatPressV_AutoDelete_spider']));
			$results = $wpdb->query("DELETE FROM " . $table_name . " WHERE date < '" . $t . "' AND spider <> ''");
			$results = $wpdb->query('OPTIMIZE TABLE ' . $table_name);
		}
		if ($StatPressV_Option['StatPressV_AutoDelete'] != '')
		{
			$t = gmdate("Ymd", strtotime('-' . $StatPressV_Option['StatPressV_AutoDelete']));
			$results = $wpdb->query("DELETE FROM " . $table_name . " WHERE date < '" . $t . "'");
			$results = $wpdb->query('OPTIMIZE TABLE ' . $table_name);
		}
		update_option('StatPressV_Option', $StatPressV_Option);
	}
	if ((!is_user_logged_in()) or (($StatPressV_Option['StatPressV_Dont_Collect_Logged_User'] != 'checked')) or($StatPressV_Option['StatPressV_Dont_Collect_Logged_User'] == 'checked')and (!current_user_can($StatPressV_Option['StatPressV_Dont_Collect_Logged_User_MinPermit'])))
	{
		$result = $wpdb->insert($table_name, array (
			'date' => $vdate,
			'time' => $vtime,
			'ip' => $ipAddress,
			'urlrequested' => mysql_real_escape_string(strip_tags($urlRequested)),
			'agent' => mysql_real_escape_string(strip_tags($userAgent)),
			'referrer' => mysql_real_escape_string(strip_tags($referrer)),
			'search' => mysql_real_escape_string(strip_tags($search_phrase)),
			'os' => mysql_real_escape_string(strip_tags($os)),
			'browser' => mysql_real_escape_string(strip_tags($browser)),
			'searchengine' => mysql_real_escape_string(strip_tags($searchengine)),
			'spider' => mysql_real_escape_string(strip_tags($spider)),
			'feed' => $feed,
			'user' => $userdata->user_login,
			'timestamp' => $timestamp,
			'language' => mysql_real_escape_string(strip_tags($language)),
			'country' => mysql_real_escape_string(strip_tags($country)),
			'realpost' => $real_post,
			'post_title'=>$post_title
			), 
		array ('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s'));
	}
}


function luc_get_ip_lazy($key, $use_getenv = false)
{
	$ip = ""; 

	if(!$use_getenv)
	{
		if (isset($_SERVER[$key]) && luc_ip_not_private($_SERVER[$key]))
			$ip = $_SERVER[$key];
	}
	else
	{
		$val = "";
		$val = @getenv($key); 
		if ($val != "" && luc_ip_not_private($val))
			$ip = $val;
	}

	return $ip;
}

function luc_get_ip()
{
	$DoVars = Array('HTTP_X_REAL_IP', 'HTTP_X_CLIENT', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');

	$use_getenv = false;
	if ($_SERVER)
		$use_getenv = false;
	else
		$use_getenv = true;

	$ip = "";
	foreach($DoVars as $aVar)
	{
		$ip = luc_get_ip_lazy($aVar, $use_getenv);
		if($ip != "") break;
	}

	return $ip;
}

function luc_ip_not_private($ip)
{if (strnatcmp(phpversion(),'5.2.0') >= 0) 
	{if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE))
		return true;
	return false;
	}
}

function StatPress_Print($body = '')
{
	echo luc_StatPressV_Vars($body);
}

function luc_StatPressV_Vars($body)
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;
	$today = gmdate('Ymd', current_time('timestamp'));
	if (strpos(strtolower($body), "%today%") !== false)
		$body = str_replace("%today%", luc_hdate($today), $body);

	if (strpos(strtolower($body), "%since%") !== false)
	{
		$qry = $wpdb->get_results("SELECT date FROM $table_name WHERE ip IS NOT NULL ORDER BY date LIMIT 1;");
		$body = str_replace("%since%", luc_hdate($qry[0]->date), $body);
	}
	if (strpos(strtolower($body), "%totalvisitors%") !== false)
	{
		$qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as pageview FROM $table_name WHERE spider='' and feed='' ;");
		$body = str_replace("%totalvisitors%", $qry[0]->pageview, $body);
	}
	if (strpos(strtolower($body), "%totalpageviews%") !== false)
	{
		$qry = $wpdb->get_results("SELECT count(*) as pageview FROM $table_name WHERE spider='' and feed='' ;");
		$body = str_replace("%totalpageviews%", $qry[0]->pageview, $body);
	}
	if (strpos(strtolower($body), "%todayvisitors%") !== false)
	{
		$qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as visitors FROM $table_name WHERE date = $today and spider='' and feed='';");
		$body = str_replace("%todayvisitors%", $qry[0]->visitors, $body);
	}
	if (strpos(strtolower($body), "%todaypageviews%") !== false)
	{
		$qry = $wpdb->get_results("SELECT count(ip) as pageviews FROM $table_name WHERE date = $today and spider='' and feed='';");
		$body = str_replace("%todaypageviews%", $qry[0]->pageviews, $body);
	}
	if (strpos(strtolower($body), "%thistotalvisitors%") !== false)
	{
		$qry = $wpdb->get_results("SELECT count(distinct ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND urlrequested='" . mysql_real_escape_string(luc_StatPressV_URL()) . "';");
		$body = str_replace("%thistotalvisitors%", $qry[0]->pageviews, $body);
	}
	if (strpos(strtolower($body), "%thistotalpageviews%") !== false)
	{
		$qry = $wpdb->get_results("SELECT count(distinct ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND urlrequested='" . mysql_real_escape_string(luc_StatPressV_URL()) . "';");
		$body = str_replace("%thistotalpageviews%", $qry[0]->pageviews, $body);
	}
	if (strpos(strtolower($body), "%thistodayvisitors%") !== false)
	{
		$qry = $wpdb->get_results("SELECT count(distinct ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND date = $today AND urlrequested='" . mysql_real_escape_string(luc_StatPressV_URL()) . "';");
		$body = str_replace("%thistodayvisitors%", $qry[0]->pageviews, $body);
	}
	if (strpos(strtolower($body), "%thistodaypageviews%") !== false)
	{
		$qry = $wpdb->get_results("SELECT count(ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND date = $today AND urlrequested='" . mysql_real_escape_string(luc_StatPressV_URL()) . "';");
		$body = str_replace("%thistodaypageviews%", $qry[0]->pageviews, $body);
	}
	if (strpos(strtolower($body), "%os%") !== false)
	{
		$userAgent = (isset ($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		$os = luc_GetOS($userAgent);
		$body = str_replace("%os%", $os, $body);
	}
	if (strpos(strtolower($body), "%browser%") !== false)
	{
		$userAgent = (isset ($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		$browser = luc_GetBrowser($userAgent);
		$body = str_replace("%browser%", $browser, $body);
	}
	if (strpos(strtolower($body), "%ip%") !== false)
	{
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$body = str_replace("%ip%", $ipAddress, $body);
	}
	if (strpos(strtolower($body), "%visitorsonline%") !== false)
	{
		$to_time = current_time('timestamp');
		$from_time = strtotime('-4 minutes', $to_time);
		$qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as visitors FROM $table_name WHERE spider='' and feed='' AND timestamp BETWEEN $from_time AND $to_time;");
		$body = str_replace("%visitorsonline%", $qry[0]->visitors, $body);
	}
	if (strpos(strtolower($body), "%usersonline%") !== false)
	{
		$to_time = current_time('timestamp');
		$from_time = strtotime('-4 minutes', $to_time);
		$qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as users FROM $table_name WHERE spider='' and feed='' AND user<>'' AND timestamp BETWEEN $from_time AND $to_time;");
		$body = str_replace("%usersonline%", $qry[0]->users, $body);
	}
	if (strpos(strtolower($body), "%toppost%") !== false)
	{
		$qry = $wpdb->get_results("SELECT urlrequested, count(ip) as totale FROM $table_name WHERE spider='' AND feed='' AND urlrequested <>'' GROUP BY urlrequested ORDER BY totale DESC LIMIT 1;");
		$body = str_replace("%toppost%", luc_post_title_Decode($qry[0]->urlrequested), $body);
	}
	if (strpos(strtolower($body), "%topbrowser%") !== false)
	{
		$qry = $wpdb->get_results("SELECT browser,count(*) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY browser ORDER BY totale DESC LIMIT 1;");
		$body = str_replace("%topbrowser%", luc_post_title_Decode($qry[0]->browser), $body);
	}
	if (strpos(strtolower($body), "%topos%") !== false)
	{
		$qry = $wpdb->get_results("SELECT os,count(id) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY os ORDER BY totale DESC LIMIT 1;");
		$body = str_replace("%topos%", luc_post_title_Decode($qry[0]->os), $body);
	}
	if (strpos(strtolower($body), "%latesthits%") !== false)
	{
		$qry = $wpdb->get_results("SELECT search FROM $table_name WHERE search <> '' ORDER BY id DESC LIMIT 10;");
		$body = str_replace("%latesthits%", urldecode($qry[0]->search), $body);
		for ($counter = 0; $counter < 10; $counter += 1)
		{
			$body .= "<br>" . urldecode($qry[$counter]->search);
		}
	}
	return $body;
}

function StatPressV_Widget_init($args)
{
	
// Multifunctional StatPress pluging
function StatPressV_Widget_control()
{
	global $StatPressV_Option;

	$options = $StatPressV_Option['StatPressV_Widget'];
	if (!is_array($options))
		$options = array (
			'title' => 'Visitor Stats',
			'body' => 'Today : %today%'
		);
	if ($_POST['statpressV-submit'])
	{
		$options['title'] = strip_tags(stripslashes($_POST['statpressV-title']));
		$options['body'] = stripslashes($_POST['statpressV-body']);

		$StatPressV_Option['StatPressV_Widget'] = $options;
		update_option('StatPressV_Option', $StatPressV_Option);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
	$body = htmlspecialchars($options['body'], ENT_QUOTES);
	// the form
	?>
	<p style="text-align:left;">
		<label for="statpressV-title"><?php _e(__('Title:')) ?> <br>
		<input style="width:100%;" id="statpressV-title" name="statpressV-title" type="text" value="<?php _e($title) ?>" />
		</label>
	</p>
	<p style="text-align:right;">
		<label for="statpressV-body"><div><?php _e(__('Body:', 'widgets')) ?> </div> <br>
		<textarea style="width:100%;height:100px;" id="statpressV-body" name="statpressV-body" type="textarea"><?php _e($body) ?></textarea>
		</label>
	</p>
	<input type="hidden" id="statpressV-submit" name="statpressV-submit" value="1" />

	<strong>Available Macros:</strong>
	<div style="font-size:7pt;">
		<br><strong>Visitors today:</strong> %todayvisitors%
		<br><strong>Today:</strong> %today%
		<br><strong>Since:</strong> %since%
		<br><strong>Total Visitors:</strong> %totalvisitors%
		<br><strong>Total Page Views:</strong> %totalpageviews%
		<br><strong>Today Visitors:</strong> %todayvisitors%
		<br><strong>Today Page Views:</strong> %todaypageviews%
		<br><strong>This Total Page Vistors:</strong> %thistotalvisitors%
		<br><strong>This Total Page Views:</strong> %thistotalpageviews%
		<br><strong>This Today Page Vistors:</strong> %thistodayvisitors%
		<br><strong>This Today Page Views:</strong> %thistodaypageviews%
		<br><strong>OS:</strong> %os%
		<br><strong>Browser:</strong> %browser%
		<br><strong>IP:</strong> %ip%
		<br><strong>Visitors Online:</strong> %visitorsonline%
		<br><strong>Users Online:</strong> %usersonline%
		<br><strong>Top Post:</strong> %toppost%
		<br><strong>To Browser:</strong> %topbrowser%
		<br><strong>Top OS:</strong> %topos%
		<br><strong>Latest Hits:</strong> %latesthits%
		</div>
	<?php
}

function StatPressV_Widget($args)
{
	global $StatPressV_Option;

	extract($args);
	$options = $StatPressV_Option['StatPressV_Widget'];
	$title = $options['title'];
	$body = $options['body'];
	echo $before_widget;
	echo ($before_title . $title . $after_title);
	echo luc_StatPressV_Vars($body);
	echo $after_widget;
}


// Top posts
function StatPressV_Widget_TopPosts_control()
{
	global $StatPressV_Option;

	$options = $StatPressV_Option['StatPressV_Widget_TopPosts'];
	if (!is_array($options))
	{
		$options = array (
			'title' => 'StatPress Visitors Top Posts',
			'howmany' => '5',
			'howlong' => '0',
			'showcounts' => 'checked',
			'showpages' => 'checked'
		);
	}
	if ($_POST['statpressVtopposts-submit'])
	{
		$options['title'] = strip_tags(stripslashes($_POST['statpressVtopposts-title']));
		$options['howmany'] = stripslashes($_POST['statpressVtopposts-howmany']);
		$options['howlong'] = stripslashes($_POST['statpressVtopposts-howlong']);
		$options['showcounts'] = stripslashes($_POST['statpressVtopposts-showcounts']);
		if ($options['showcounts'] == "1")
		{
			$options['showcounts'] = 'checked';
		}
		$options['showpages'] = stripslashes($_POST['statpressVtopposts-showpages']);
		if ($options['showpages'] == "1")
		{
			$options['showpages'] = 'checked';
		}
		$StatPressV_Option['StatPressV_Widget_TopPosts'] = $options;
		update_option('StatPressV_Option', $StatPressV_Option);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
	$howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
	$howlong = htmlspecialchars($options['howlong'], ENT_QUOTES);
	$showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);
	$showpages = htmlspecialchars($options['showpages'], ENT_QUOTES);
	// the form
	?>
	<p style="text-align:left;">
		<label for="statpressVtopposts-title"><?php _e(__('Title:', 'statpressV')) ?>
		<input style="width:100%;" id="statpress-title" name="statpressVtopposts-title" type="text" value="<?php _e($title) ?>" />
		</label>
	</p>
	<p style="text-align:left;">
		<label for="statpressVtopposts-howmany"><?php _e(__('Limit results to:', 'statpressV')) ?>
		<input style="width:40px; align:right;" id="statpressVtopposts-howmany" name="statpressVtopposts-howmany" type="text" value="<?php _e($howmany) ?>" />
		</label>
	</p>


	<p style="text-align:left;">
		<label for="statpressVtopposts-howlong"><?php _e(__('Include # days (0 for all):', 'statpressV')) ?>
		<input style="width:40px; align:right" id="statpressVtopposts-howlong" name="statpressVtopposts-howlong" type="text" value="<?php _e($howlong ) ?>" />
		</label>
	</p>
	<p style="text-align:right;">
		<label for="statpressVtopposts-showcounts"><?php _e(__('Visits', 'statpressV')) ?>
		<input id="statpressVtopposts-showcounts" name="statpressVtopposts-showcounts" type=checkbox value="checked" <?php _e($showcounts) ?> />
		</label>
	</p>
	<p style="text-align:right;">
		<label for="statpressVtopposts-showpages"><?php _e(__('Include Pages', 'statpressV')) ?>
		<input id="statpressVtopposts-showpages" name="statpressVtopposts-showpages" type=checkbox value="checked" <?php _e($showpages) ?> />
		</label>
	</p>
	<input type="hidden" id="statpress-submitTopPosts" name="statpressVtopposts-submit" value="1" />
	<?php
}

function StatPressV_Widget_TopPosts($args)
{
	global $StatPressV_Option;

	extract($args);
	$options = $StatPressV_Option['StatPressV_Widget_TopPosts'];
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
	$howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
	$howlong = htmlspecialchars($options['howlong'], ENT_QUOTES);
	$showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);
	$showpages = htmlspecialchars($options['showpages'], ENT_QUOTES);
	echo $before_widget;
	echo ($before_title . $title . $after_title);
	echo luc_StatPressV_TopPosts($howmany, $howlong, $showcounts, $showpages);
	echo $after_widget;
}

function luc_StatPressV_TopPosts($limit = 5, $numdays = 0, $showcounts = 'checked', $showpages = 'checked')
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;
	$res = "\n<ul>\n";
	
	if ($numdays == 0)
	{ // All dates chosen, default to epoch
		$stopdate = date('Ymd', strtotime('1970-01-01'));
	}
	else
		if ($numdays < 0)
		{ // Negative number of days, no change
			$stopdate = date('Ymd', strtotime($numdays . 'days'));
		}
		else
		{ // Invert sign
			$numdays = $numdays * -1;
			$stopdate = date('Ymd', strtotime($numdays . 'days'));
		}

	if (strtolower($showpages) == 'checked')
		$type = "(post_type = 'page' OR post_type = 'post')";
	else
		$type = "post_type = 'post'";

	$qry_s = "SELECT post_name, COUNT(*) as total, urlrequested
						FROM $wpdb->posts as p
						JOIN $table_name as t
						ON urlrequested LIKE CONCAT('%', p.post_name, '_' )
						WHERE post_status = 'publish'
							AND $type
							AND spider=''
							AND feed=''
							AND date >= $stopdate
						GROUP BY post_name
						ORDER BY total DESC LIMIT $limit;";

	$qry = $wpdb->get_results($qry_s);

	foreach ($qry as $rk)
	{
		$res .= "<li><a href='" .
		luc_GetBlogURL() .
		 ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') .
		"'>" . luc_post_title_Decode($rk->post_name) . "</a>";
		if (strtolower($showcounts) == 'checked')
		{
			$res .= " (" . $rk->total . ")</li>";
		}
	}
	return "$res</ul>\n";
}


	wp_register_sidebar_widget('StatPressV', 'StatPress Stats Macros', 'StatPressV_Widget', array('description' => 'Show a off your statistics in a widget'));
	wp_register_widget_control('StatPressV', 'StatPress Stats Macros', 'StatPressV_Widget_control');

	wp_register_sidebar_widget('StatPressVTopPosts', 'StatPress V Top Posts', 'StatPressV_Widget_TopPosts', array('description' => 'Show a configurable list of your most popular posts & pages'));
	wp_register_widget_control('StatPressVTopPosts', 'StatPress V Top Posts', 'StatPressV_Widget_TopPosts_control');
}

function permalinksEnabled()
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;

	$result = $wpdb->get_row("SELECT `option_value` FROM $wpdb->options WHERE `option_name` = 'permalink_structure';");
	if ($result->option_value != '')
		return true;
	else
		return false;
}

function my_substr($str, $x, $y = 0)
{
	if ($y == 0)
		$y = strlen($str) - $x;
	if (function_exists('mb_substr'))
		return mb_substr($str, $x, $y);
	else
		return substr($str, $x, $y);
}


function luc_post_title_Decode($out_url)
{ 
	//fb_xd_fragment is the urlrequested of home page when the referrer is Facebook
	$permalink = luc_permalink();
    $perm = explode('/', $permalink);
	$home_url = array ( '' , '/' . $perm[1] , $permalink , 'fb_xd_fragment') ;
	if (($permalink == '') and ( in_array($out_url,$home_url)))
	    $out_url = '[' . __('Page', 'statpressV') . "]: Home"; 
	else
	{
		$perm = explode('/', $permalink);
		if (($permalink != '') and ( in_array($out_url,$home_url) or 
				(strpos($out_url, $permalink . 'feed') === 0) or 
				(strpos($out_url, $permalink . 'comments') === 0)))
			$out_url = '[' . __('Page', 'statpressV') . "]: Home"; 
		else
		{
			// Convert page URL to a Wordpress Page ID
			$post_id = url_to_postid($out_url);

			if ($post_id == 0)
				return $out_url;
	
			$post_id = get_post($post_id, ARRAY_A);
	
			if ($post_id['post_type'] == 'page')
				$post_t = '[' . __('Page', 'statpressV') . ']: ' . $post_id['post_title'];
			elseif ($post_id['post_type'] == 'attachment')
					$post_t = '[' . __('File', 'statpressV') . ']: ' . $post_id['post_title'];
				elseif ($post_id['post_type'] == 'post')
						$post_t = $post_id['post_title'];
					else
						$post_t = '';
	
			if ($post_t == '')
				$out_url = $out_url;
			else
				$out_url = $post_t;
		}
	}
	return $out_url;
}

function luc_StatPressV_URL()
{   
	$urlRequested = (isset($_SERVER["REQUEST_URI"]) ? esc_url_raw($_SERVER["REQUEST_URI"] ) : '');
	if (my_substr($urlRequested, 0, 2) == '/?')
		$urlRequested = my_substr($urlRequested, 2);
	if ($urlRequested == '/')
		$urlRequested = '';

	return $urlRequested;
}

function luc_GetBlogURL()
{
	$prsurl = parse_url(get_bloginfo('url'));
	return $prsurl['scheme'] . '://' . $prsurl['host'] . ((!permalinksEnabled()) ? $prsurl['path'] . '/?' : '');
}

// Converte da data us to default format di Wordpress
function luc_hdate($dt = "00000000")
{
	return mysql2date(get_option('date_format'), my_substr($dt, 0, 4) . "-" . my_substr($dt, 4, 2) . "-" . my_substr($dt, 6, 2));
}

function luc_GetQueryPairs($url)
{
	$parsed_url = parse_url($url);
	$tab = parse_url($url);
	$host = $tab['host'];
	if (key_exists("query", $tab))
	{
		$query = $tab["query"];
		$query = str_replace("&amp;", "&", $query);
		$query = urldecode($query);
		$query = str_replace("?", "&", $query);
		return explode("&", $query);
	}
	else
	{
		return null;
	}
}

function luc_GetOS($arg)
{
	$arg = str_replace(" ", "", $arg);
	$lines = file(STATPRESS_V_PLUGIN_PATH . 'def/os.dat');
	foreach ($lines as $line_num => $os)
	{
		list ($os_name, $os_id) = explode("|", $os);
		if (stripos($arg, $os_id) === false)
			continue;

		return $os_name;
	}
	return '';
}

function luc_GetBrowser($arg)
{
	$arg = str_replace(" ", "", $arg);
	$lines = file(STATPRESS_V_PLUGIN_PATH . 'def/browser.dat');
	foreach ($lines as $line_num => $browser)
	{
		list ($name, $id) = explode("|", $browser);
		if (stripos($arg, $id) === false)
			continue;

		return $name;
	}
	return '';
}

function luc_CheckBanIP($arg)
{
		$lines = file(STATPRESS_V_PLUGIN_PATH . 'def/banips.dat');

	if ($lines !== false)
	{
		foreach ($lines as $banip)
		{
			if (@ preg_match('/^' . rtrim($banip, "\r\n") . '$/', $arg))
				return true;
		}
	}
	return false;
}

/* function luc_CheckSpamBot($agent = null)
{
	$agent = str_replace(" ", "", $agent);
	$key = null;
	$lines = file(STATPRESS_V_PLUGIN_PATH . 'def/spambot.dat');
	foreach ($lines as $line_num => $spambot)
	{
		list ($name, $key) = explode("|", $spambot);
		if (stripos($agent, $key) === false)
			continue;

		return $name;
	}
	return null;
}
*/

function luc_GetSE($referrer = null)
{
	$key = null;
	$lines = file(STATPRESS_V_PLUGIN_PATH . 'def/searchengine.dat');
	foreach ($lines as $line_num => $se)
	{
		list($name, $url, $key, $stop) = explode("|", $se);
        if (stripos($referrer, $url) === false)
			continue;
		// trovato se
		if (stripos($key,$url) !== false) // detection of searchs engines without URL like Google
		       { $query_search = explode($key,$referrer);
		         $query_search = explode($stop,$query_search[1]);
		         return ($name . "|" . urlencode($query_search[0]));
		       } 
		// detection of search engine with URL like Google	   
		$variables = luc_GetQueryPairs($referrer);
		$i = count($variables);
		while ($i--)
		{
			$tab = explode("=", $variables[$i]);
			if ($tab[0] == $key)
				return ($name . "|" . urlencode($tab[1]));
		}
	}
	return null;
}

function luc_GetSpider($agent = null)
{
	$agent = str_replace(" ", "", $agent);
	$key = null;
	$lines = file(STATPRESS_V_PLUGIN_PATH . 'def/spider.dat');
	foreach ($lines as $line_num => $spider)
	{
		list ($name, $key) = explode("|", $spider);
		if (stripos($agent, $key) === false)
			continue;
		// trovato
		return $name;
	}
	return null;
}

function luc_StatPressV_Is_Feed($url)
{
	if (stristr($url, get_bloginfo('comments_atom_url')) != FALSE)
	{
		return 'COMMENT ATOM';
	}
	elseif (stristr($url, get_bloginfo('comments_rss2_url')) != FALSE)
	{
		return 'COMMENT RSS';
	}
	elseif (stristr($url, get_bloginfo('rdf_url')) != FALSE)
	{
		return 'RDF';
	}
	elseif (stristr($url, get_bloginfo('atom_url')) != FALSE)
	{
		return 'ATOM';
	}
	elseif (stristr($url, get_bloginfo('rss_url')) != FALSE)
	{
		return 'RSS';
	}
	elseif (stristr($url, get_bloginfo('rss2_url')) != FALSE)
	{
		return 'RSS2';
	}
	elseif (stristr($url, 'wp-feed.php') != FALSE)
	{
		return 'RSS2';
	}
	elseif (stristr($url, '/feed') != FALSE)
	{
		return 'RSS2';
	}
	return '';
}

function luc_GeoIP_dbname($edition)
{
	if ('city' == $edition)
		$geoip_db_name = ABSPATH . 'wp-content/GeoIP/GeoLiteCity.dat';
	else
		$geoip_db_name = ABSPATH . 'wp-content/GeoIP/GeoIP.dat';

	return $geoip_db_name;
}

?>