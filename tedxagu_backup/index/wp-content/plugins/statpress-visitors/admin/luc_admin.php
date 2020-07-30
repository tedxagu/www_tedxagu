<?php
function StatPressV_admin_init()
{
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;

	$mincap = $StatPressV_Option['StatPressV_MinPermit'];
	if ($mincap == '')
		$mincap = 'switch_themes';

		// Add JQuery support
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		
		// jQuery Tabs
		wp_enqueue_script('jquery-ui-tabs');
	
		// jQuery Pagination
		wp_enqueue_script('jquery-pagination', STATPRESS_V_PLUGIN_URL . 'js/jquery.pagination.js', array ('jquery', 'jquery-ui-core'));
	
		// jQuery Datepicker
		wp_enqueue_script('jquery-ui-datepicker', STATPRESS_V_PLUGIN_URL . 'js/jquery.ui.datepicker.min.js', array ('jquery', 'jquery-ui-core'));
		
		//  Add AJAX support
		wp_enqueue_script('luc-ajax-datepicker', STATPRESS_V_PLUGIN_URL . 'js/luc.ajax.datepicker.js', array ('jquery'));
		wp_enqueue_script('luc-ajax-geoip', STATPRESS_V_PLUGIN_URL . 'js/luc.ajax.geoip.js', array ('jquery'));
		wp_enqueue_script('luc-ajax-pagination', STATPRESS_V_PLUGIN_URL . 'js/luc.ajax.pagination.js', array ('jquery'));
		wp_enqueue_script('luc-ajax-tables', STATPRESS_V_PLUGIN_URL . 'js/luc.ajax.tables.js', array ('jquery'));
	
		// jQuery CSS
		wp_enqueue_style('jquery.ui.theme', STATPRESS_V_PLUGIN_URL . 'css/jquery-ui-1.8.16.custom.css');
		wp_enqueue_style('jquery.pagination.theme', STATPRESS_V_PLUGIN_URL . 'css/pagination.css');
		wp_enqueue_style('jquery.overrides', STATPRESS_V_PLUGIN_URL . 'css/jquery.override.css');
		
	// Handlers for AJAX tables in admin
	add_action('wp_ajax_table_latest_hits', 'luc_main_table_latest_hits');
	add_action('wp_ajax_table_latest_search', 'luc_main_table_latest_search');
	add_action('wp_ajax_table_latest_referrers', 'luc_main_table_latest_referrers');
	add_action('wp_ajax_table_latest_feeds', 'luc_main_table_latest_feeds');
	add_action('wp_ajax_table_latest_spiders', 'luc_main_table_latest_spiders');
	add_action('wp_ajax_table_latest_spambots', 'luc_main_table_latest_spambots');
	add_action('wp_ajax_table_latest_undefagents', 'luc_main_table_latest_undefagents');

	// Pagination callbacks
	add_action('wp_ajax_table_bargraph', 'luc_main_table_bargraph');
	
	add_action('wp_ajax_page_views_table', 'luc_callback_page_views_table');
	add_action('wp_ajax_page_spyvisitors_table', 'luc_callback_page_spyvisitors_table');
	add_action('wp_ajax_page_spybot_table', 'luc_callback_page_spybot_table');
	add_action('wp_ajax_page_visitors_table', 'luc_callback_page_visitors_table');
	add_action('wp_ajax_page_feeds_table', 'luc_callback_page_feeds_table');
	add_action('wp_ajax_page_referrer_table', 'luc_callback_page_referrer_table');
	
	// Required for AJAX callback to work
	if (($StatPressV_Option['StatPressV_Hide_Page_Posts_Pages'] <> 'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_posts_pages.php')))
	{
		require_once STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_posts_pages.php';
		add_action('wp_ajax_table_posts_pages', 'luc_table_posts_pages');
	}
	
	// AJAX handler for GeoIP database downloads
	add_action('wp_ajax_geoipdbupdate', 'luc_GeoIP_update_db');
	
	add_action('wp_ajax_page_views_table', 'luc_callback_page_views_table');
}

function luc_add_pages()
{
	// Create table if it doesn't exist
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	
	// add submenu
	$mincap = $StatPressV_Option['StatPressV_MinPermit'];
	if ($mincap == '')
		$mincap = 'switch_themes';

	if (isset($_GET['statpress_action']))
	{
		if ($_GET['statpress_action'] == 'exportnow')
			luc_ExportNow();
		if ($_GET['statpress_action'] == 'updategeoipdat')
			luc_GeoIP_update_db('country');
		if ($_GET['statpress_action'] == 'updategeoipcitydat')
			luc_GeoIP_update_db('city');
	}

	if ($_POST['saveit'] == 'yes')
	{
		$StatPressV_Option['StatPressV_Hide_Page_Feeds'] = (isset($_POST['StatPressV_Hide_Page_Feeds']) ? $_POST['StatPressV_Hide_Page_Feeds'] : '');
		$StatPressV_Option['StatPressV_Hide_Page_Referrer'] = (isset($_POST['StatPressV_Hide_Page_Referrer']) ? $_POST['StatPressV_Hide_Page_Referrer'] : '');
		$StatPressV_Option['StatPressV_Hide_Page_SpyBot'] = (isset($_POST['StatPressV_Hide_Page_SpyBot']) ? $_POST['StatPressV_Hide_Page_SpyBot'] : '');
		$StatPressV_Option['StatPressV_Hide_Page_SpyVisitors'] = (isset($_POST['StatPressV_Hide_Page_SpyVisitors']) ? $_POST['StatPressV_Hide_Page_SpyVisitors'] : '');
		$StatPressV_Option['StatPressV_Hide_Page_Stats'] = (isset($_POST['StatPressV_Hide_Page_Stats']) ? $_POST['StatPressV_Hide_Page_Stats'] : '');
		$StatPressV_Option['StatPressV_Hide_Page_Update'] = (isset($_POST['StatPressV_Hide_Page_Update']) ? $_POST['StatPressV_Hide_Page_Update'] : '');
		$StatPressV_Option['StatPressV_Hide_Page_View'] = (isset($_POST['StatPressV_Hide_Page_View']) ? $_POST['StatPressV_Hide_Page_View'] : '');
		$StatPressV_Option['StatPressV_Hide_Page_Visitors'] = (isset($_POST['StatPressV_Hide_Page_Visitors']) ? $_POST['StatPressV_Hide_Page_Visitors'] : '');
		$StatPressV_Option['StatPressV_Hide_Page_Posts_Pages'] = (isset($_POST['StatPressV_Hide_Page_Posts_Pages']) ? $_POST['StatPressV_Hide_Page_Posts_Pages'] : '');
		
	}
	
	add_menu_page('StatPress V', 'StatPressV', $mincap, __FILE__, 'luc_main', STATPRESS_V_PLUGIN_URL.'images/stat.png');
	// add optionals submenus
	if (($StatPressV_Option['StatPressV_Hide_Page_Posts_Pages'] <> 'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_posts_pages.php')))
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_posts_pages.php';
		add_submenu_page(__FILE__, 'Posts and Pages ', 'Posts and Pages ', $mincap, 'statpress-visitors/action=postpage', 'luc_posts_pages');
	}
	if (($StatPressV_Option['StatPressV_Hide_Page_SpyVisitors'] <> 'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_spyvisitors.php')))
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_spyvisitors.php';
		add_submenu_page(__FILE__, 'Visitor Spy', 'Visitor Spy', $mincap, 'statpress-visitors/action=spyvisitors', 'luc_spyvisitors');
	}
	if (($StatPressV_Option['StatPressV_Hide_Page_SpyBot'] <> 'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_spybot.php')))
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_spybot.php';
		add_submenu_page(__FILE__, 'Bot Spy', 'Bot Spy', $mincap, 'statpress-visitors/action=spybot', 'luc_spybot');
	}
	// non optional page URL Monitoring
	if  (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_url_monitoring.php'))
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_url_monitoring.php';
		add_submenu_page(__FILE__, 'URL Monitoring', 'URL Monitoring', $mincap, 'statpress-visitors/action=urlmonitoring', 'luc_url_monitoring');
	}
	if (($StatPressV_Option['StatPressV_Hide_Page_Visitors'] <> 'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_visitors.php')))
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_visitors.php';
		add_submenu_page(__FILE__, 'Visitors ', 'Visitors ', $mincap, 'statpress-visitors/action=visitors', 'luc_visitors');
	}
	if (($StatPressV_Option['StatPressV_Hide_Page_View'] <> 'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_view.php')))
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_view.php';
		add_submenu_page(__FILE__, 'Views', 'Views ', $mincap, 'statpress-visitors/action=views', 'luc_view');
	}
	if (($StatPressV_Option['StatPressV_Hide_Page_Feeds'] <> 'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_feeds.php')))
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_feeds.php';
		add_submenu_page(__FILE__, 'Feeds', 'Feeds ', $mincap, 'statpress-visitors/action=feeds', 'luc_feeds');
	}
	if (($StatPressV_Option['StatPressV_Hide_Page_Referrer'] <> 'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_referrer.php')))
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_referrer.php';
		add_submenu_page(__FILE__, 'Referrer', 'Referrer', $mincap, 'statpress-visitors/action=referrer', 'luc_referrer');
	}
	if (($StatPressV_Option['StatPressV_Hide_Page_Stats'] <> 'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_statistics.php')))
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_statistics.php';
		add_submenu_page(__FILE__, 'Statistics', 'Statistics', $mincap, 'statpress-visitors/action=details', 'luc_statistics');
	}
	if (($StatPressV_Option['StatPressV_Hide_Page_Update'] <> 'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_update.php')))
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_update.php';
		add_submenu_page(__FILE__, 'Update database', 'Update database', $mincap, 'statpress-visitors/action=update', 'luc_update');
	}
    // add non optionals pages
	
	add_submenu_page(__FILE__, 'Export', 'Export', $mincap, 'statpress-visitors/action=export', 'luc_export');
	
	if  (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_options.php'))
	{
		include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_options.php';
		add_submenu_page(__FILE__, 'Options', 'Options', $mincap, 'statpress-visitors/action=options', 'luc_options');
	}
	

}

function StatPressV_admin_footer()
{
?>

	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('.datepicker').datepicker({
			dateFormat : 'yy-mm-dd',
			maxDate : 0,
			showButtonPanel : true,
			showWeek : true,
			showOn: 'both',
			buttonText : '',
			buttonImage : '<?php echo STATPRESS_V_PLUGIN_URL . 'images/smoothness/calendar.png' ?>'
		});
	});
	</script>

	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('.tabbed').tabs();
	});
	</script>
	<?php

}

function luc_StatPressV_load_time()
{
	echo "<font size='1'><br>StatPressV page generated in " . timer_stop(0,2) . " seconds with ".get_num_queries()." SQL queries.</font>";
}

function luc_main_table_latest_hits()
{
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	$querylimit = (isset ($_POST['hitsrows']) ? $_POST['hitsrows'] : $StatPressV_Option['StatPressV_Rows_Per_Latest']);
	?>
	<table class='widefat'>
		<thead>
		<tr>
			<th scope='col'>Date</th>
			<th scope='col'>Time</th>
			<th scope='col'>IP</th>
			<th scope='col'>Language</th>
			<th scope='col'>Country</th>
			<th scope='col' width="30%">Page</th>
			<th scope='col'>OS</th>
			<th scope='col'>Browser</th>
	<?php
	if (($StatPressV_Option['StatPressV_Dont_Collect_Logged_User'] != 'checked') or (($StatPressV_Option['StatPressV_Dont_Collect_Logged_User'] == 'checked')and (current_user_can($StatPressV_Option['StatPressV_Dont_Collect_Logged_User_MinPermit']))))
		echo "<th scope='col'>User</th>";
	?>
			<th scope='col'>Feed</th>
		</tr>
		</thead>
		<tbody>
	<?php

	$rks = $wpdb->get_results("SELECT date, time, ip,urlrequested, os, browser,feed,user, language, country, post_title
			FROM $table_name
			WHERE (os<>'' OR browser <>'')
				AND `spider` NOT LIKE '%Spam Bot%'
			ORDER BY id DESC LIMIT $querylimit;");
	$text_OS = (($StatPressV_Option['StatPressV_Dont_Show_OS_name']!='checked') ? true : false);
	$text_browser =(($StatPressV_Option['StatPressV_Dont_Show_Browser_name']!='checked') ? true : false);
	
			
	foreach ($rks as $rk)
	{
		echo "<tr>
					<td>" . luc_hdate($rk->date) . "</td>
					<td>" . $rk->time . "</td>
					<td>" . luc_create_href($rk->ip, 'ip') . "</td>
					<td>" . luc_language($rk) . "</td>
					<td>" . luc_HTML_IMG($rk->country, 'country', false) . "</td>
					<td>".((isset($rk->post_title)) ? $rk->post_title :  luc_post_title_Decode(urldecode($rk->urlrequested)) )."</td>
				<td>" . luc_HTML_IMG($rk->os, 'os', $text_OS) . "</td>
					<td>" . luc_HTML_IMG($rk->browser, 'browser', $text_browser) . "</td>";
		if (($StatPressV_Option['StatPressV_Dont_Collect_Logged_User'] != 'checked') or (($StatPressV_Option['StatPressV_Dont_Collect_Logged_User'] == 'checked')and (current_user_can($StatPressV_Option['StatPressV_Dont_Collect_Logged_User_MinPermit']))))
			{echo (($rk->user != '') ? "<td>" .$rk->user. "</td>" : "<td>&nbsp;</td>");}
		echo "	<td>" . luc_HTML_IMG($rk->feed, 'feed', false) . "</td>
				</tr>";
	}
	?>
		</tbody>
	</table>
	<?php

	if (isset ($_POST['hitsrows']))
		die();
}

function luc_main_table_latest_search()
{
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	$querylimit = (isset ($_POST['searchrows']) ? $_POST['searchrows'] : $StatPressV_Option['StatPressV_Rows_Per_Latest']);
	?>
	<table class='widefat' >
		<thead>
		<tr>
			<th scope='col'>Date</th>
			<th scope='col'>Time</th>
			<th scope='col'>IP</th>
		<?php if ($StatPressV_Option['StatPressV_Dont_Show_domain_name']=='checked') echo "<th scope='col'>Domain</th>" ?>
			<th scope='col'>Language</th>
			<th scope='col'>Country</th>
			<th scope='col'>Terms</th>
			<th scope='col' width="30%">Page</th>
			<th scope='col'>Engine</th>
		</tr>
		</thead>
		<tbody>
	<?php

	$qry = $wpdb->get_results("SELECT date, time, ip, urlrequested, referrer, search, searchengine, os, language, country
			FROM $table_name
			WHERE search<>''
			ORDER BY id DESC LIMIT $querylimit;");
	foreach ($qry as $rk)
	{
		echo "<tr>
					<td>" . luc_hdate($rk->date) . "</td>
					<td>" . $rk->time . "</td>
					<td>" . luc_create_href($rk->ip, 'ip') . "</td>";
				echo "
					<td>" . luc_language($rk) . "</td>
					<td>" . luc_HTML_IMG($rk->country, 'country', false) . "</td>
					<td><a target='_blank' href='" . luc_StatPressV_SearchQ($rk->referrer) . "' title='Go to search page...'>" . urldecode($rk->search) . "</a></td>
					<td>".((isset($rk->post_title)) ? $rk->post_title :  luc_post_title_Decode(urldecode($rk->urlrequested)) )."</td>
				<td>" . luc_HTML_IMG($rk->searchengine, 'searchengine', false) . "</td>
				</tr>";
	}
	?>
		</tbody>
	</table>
	<?php

	if (isset ($_POST['searchrows']))
		die();
}

function luc_main_table_latest_referrers()
{
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	$querylimit = (isset ($_POST['referrersrows']) ? $_POST['referrersrows'] : $StatPressV_Option['StatPressV_Rows_Per_Latest']);
	?>
	<table class='widefat' >
		<thead>
		<tr>
			<th scope='col'>Date</th>
			<th scope='col'>Time</th>
			<th scope='col'>IP</th>
		<?php if ($StatPressV_Option['StatPressV_Dont_Show_domain_name']=='checked') echo "<th scope='col'>Domain</th>" ?>
			<th scope='col'>Language</th>
			<th scope='col'>Country</th>
			<th scope='col'>URL</th>
			<th scope='col' width="30%">Page</th>
		</tr>
		</thead>
		<tbody>

	<?php

	$qry = $wpdb->get_results("SELECT date, time, ip, referrer, urlrequested, os, language, country
			FROM $table_name
			WHERE (referrer NOT LIKE '" . get_option('home') . "%')
			    AND spider = ''
				AND referrer <>''
				AND searchengine = ''
			ORDER BY id DESC LIMIT $querylimit;");
	foreach ($qry as $rk)
	{
		echo "<tr>
			<td>" . luc_hdate($rk->date) . "</td>
			<td>" . $rk->time . "</td>
			<td>" . luc_create_href($rk->ip, 'ip') . "</td>";
				echo "
			<td>" . luc_language($rk) . "</td>
			<td>" . luc_HTML_IMG($rk->country, 'country', false) . "</td>
			<td><a target='_blank' href='" . urldecode($rk->referrer) . "'>" . urldecode($rk->referrer) . "</a></td>
			<td>".((isset($rk->post_title)) ? $rk->post_title :  luc_post_title_Decode(urldecode($rk->urlrequested)) )."</td>
				</tr>\n";
	}
	?>
		</tbody>
	</table>
	<?php

	if (isset ($_POST['referrersrows']))
		die();
}

function luc_main_table_latest_feeds()
{
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	$querylimit = (isset ($_POST['feedsrows']) ? $_POST['feedsrows'] : $StatPressV_Option['StatPressV_Rows_Per_Latest']);
	?>
	<table class='widefat' >
		<thead>
		<tr>
			<th scope='col'>Date</th>
			<th scope='col'>Time</th>
			<th scope='col'>IP</th>
		<?php if ($StatPressV_Option['StatPressV_Dont_Show_domain_name']=='checked') echo "<th scope='col'>Domain</th>" ?>
			<th scope='col'>Language</th>
			<th scope='col'>Country</th>
			<th scope='col' width="30%" >Page</th>
			<th scope='col'>Feed</th>
	<?php

	if ($StatPressV_Option['StatPressV_Dont_Collect_Logged_User'] != 'checked')
		echo "<th scope='col'>User</th>";
	?>
		</tr>
		</thead>
		<tbody>
	<?php

	$qry = $wpdb->get_results("SELECT date, time, ip, urlrequested, feed, language, country, post_title
			FROM $table_name
			WHERE feed<>''
			ORDER BY id DESC LIMIT $querylimit;");
	foreach ($qry as $rk)
	{
		echo "<tr>
					<td>" . luc_hdate($rk->date) . "</td>
					<td>" . $rk->time . "</td>
					<td>" . luc_create_href($rk->ip, 'ip') . "</td>";
				echo "
					<td>" . luc_language($rk) . "</td>
					<td>" . luc_HTML_IMG($rk->country, 'country', false) . "</td>
					<td>".((isset($rk->post_title)) ? $rk->post_title :  luc_post_title_Decode(urldecode($rk->urlrequested)) )."</td>
					<td>" . luc_HTML_IMG($rk->feed, 'feed', true) . "</td>";
		if ($StatPressV_Option['StatPressV_Dont_Collect_Logged_User'] != 'checked')
		{
			if ($rk->user != '')
				echo "<td>" . $rk->user . "</td>";
			else
				echo "<td>&nbsp;</td>";
		}
		echo "</tr>";
	}
		?>
		</tbody>
	</table>
	<?php

	if (isset ($_POST['feedsrows']))
		die();
}

function luc_main_table_latest_spiders()
{
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	$querylimit = (isset ($_POST['spidersrows']) ? $_POST['spidersrows'] : $StatPressV_Option['StatPressV_Rows_Per_Latest']);
	?>
	<table class='widefat' >
		<thead>
		<tr>
			<th scope='col'>Date</th>
			<th scope='col'>Time</th>
			<th scope='col'>IP</th>
			<th scope='col'></th>
			<th scope='col' width="30%">Page</th>
			<th scope='col' width="30%">Agent</th>
		</tr>
		</thead>
		<tbody>
	<?php

	$qry = $wpdb->get_results("SELECT date, time, ip, urlrequested, spider, agent
			FROM $table_name
			WHERE spider<>''
				AND spider NOT LIKE '%spam bot'
			ORDER BY id DESC
			LIMIT $querylimit;");
	foreach ($qry as $rk)
	{
		echo "<tr>
					<td>" . luc_hdate($rk->date) . "</td>
					<td>" . $rk->time . "</td>
					<td>" . luc_create_href($rk->ip, 'ip') . "</td>
					<td>" . luc_HTML_IMG($rk->spider, 'spider', false) . "</td>
					<td>".((isset($rk->post_title)) ? $rk->post_title :  luc_post_title_Decode(urldecode($rk->urlrequested)) )."</td>
				<td> " . $rk->agent . "</td>
				</tr>";
	}
	?>
		</tbody>
	</table>
	<?php

	if (isset ($_POST['spidersrows']))
		die();
}

function luc_main_table_latest_spambots()
{
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	$querylimit = (isset ($_POST['spambotsrows']) ? $_POST['spambotsrows'] : $StatPressV_Option['StatPressV_Rows_Per_Latest']);
	?>
	<table class='widefat' >
		<thead>
		<tr>
			<th scope='col'>Date</th>
			<th scope='col'>Time</th>
			<th scope='col'>IP</th>
			<th scope='col'></th>
			<th scope='col' width="30%">Page</th>
			<th scope='col' width="30%">Agent</th>
		</tr>
		</thead>
		<tbody>
	<?php
	$qry = $wpdb->get_results("SELECT date, time, ip, urlrequested, spider, agent
			FROM $table_name
			WHERE spider LIKE '%spam bot'
			ORDER BY id DESC
			LIMIT $querylimit;");

	foreach ($qry as $rk)
	{
		echo "<tr>
					<td>" . luc_hdate($rk->date) . "</td>
					<td>" . $rk->time . "</td>
					<td>" . luc_create_href($rk->ip, 'ip') . "</td>
					<td>" . luc_HTML_IMG($rk->spider, 'spider', false) . "</td>
					<td>".((isset($rk->post_title)) ? $rk->post_title :  luc_post_title_Decode(urldecode($rk->urlrequested)) )."</td>
				<td> " . $rk->agent . "</td>
				</tr>";}
	?>
		</tbody>
	</table>
	<?php

	if (isset ($_POST['spambotsrows']))
		die();
}

function luc_main_table_latest_undefagents()
{
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	$querylimit = (isset ($_POST['undefagentsrows']) ? $_POST['undefagentsrows'] : $StatPressV_Option['StatPressV_Rows_Per_Latest']);
	?>
	<table class='widefat' >
		<thead>
		<tr>
			<th scope='col'>Date</th>
			<th scope='col'>Time</th>
			<th scope='col'>IP</th>
			<th scope='col' width="30%">Agent</th>
			<th scope='col'>Count</th>
		</tr>
		</thead>
		<tbody>
	<?php

	$qry = $wpdb->get_results("SELECT date, time, ip, agent ,COUNT(ip) AS ipcount
			FROM $table_name
			WHERE (os=''
				OR browser='')
				AND searchengine=''
				AND spider=''
			GROUP BY ip,agent
			ORDER BY id DESC
			LIMIT $querylimit;");
	foreach ($qry as $rk)
	{
		echo "<tr>
			<td>" . luc_hdate($rk->date) . "</td>
			<td>" . $rk->time . "</td>
			<td>" . luc_create_href($rk->ip, 'ip') . "</td>
			<td><a target='_blank' href='http://www.google.com/search?q=%22User+Agent%22+" . urlencode($rk->agent) . "' target='_blank' title='Search for &quot;" . urldecode($rk->agent) . "&quot; on Google...'> " . $rk->agent . "</a> </td>
			<td>" . $rk->ipcount . "</td></tr>";
	}
	?>
		</tbody>
	</table>
	<?php

	if (isset ($_POST['undefagentsrows']))
		die();
}

function luc_main_table_overview($total)
{
	global $StatPressV_Option;
	
	$visitors_color = "#114477";
	$rss_visitors_color = "#FFF168";
	$pageviews_color = "#3377B6";
	$rss_pageviews_color = "#f38f36";
	$spider_color = "#83b4d8";
	$action = "overview";
	
	$lastmonth = luc_StatPress_lastmonth();
//	$thismonth = gmdate('Ym', current_time('timestamp'));

	$tlm[0] = my_substr($lastmonth, 0, 4);
	$tlm[1] = my_substr($lastmonth, 4, 2);

	// OVERVIEW table
	
	?>
		<table class='widefat' >
			<thead>
				<tr>
					<th scope='col'></th>
					<th scope='col'>Total</th>
					<th scope='col'>Last month<br /><font size=1><?php _e(gmdate('M, Y', gmmktime(0, 0, 0, $tlm[1], 1, $tlm[0]))) ?></font></th>
					<th scope='col'>This month<br /><font size=1><?php _e(gmdate('M, Y', current_time('timestamp'))) ?></font></th>
					<th scope='col'>Target This month<br /><font size=1><?php _e(gmdate('M, Y', current_time('timestamp'))) ?></font></th>
					<th scope='col'>Yesterday<br /><font size=1><?php _e(gmdate('d M, Y', current_time('timestamp') - 86400)) ?></font></th>
					<th scope='col'>Today<br /><font size=1><?php _e(gmdate('d M, Y', current_time('timestamp'))) ?></font></th>
				</tr>
			</thead>
			<tbody>
	<?php
	//###############################################################################################
	// VISITORS ROW
	luc_Row("DISTINCT ip", "feed=''", "spider=''", "agent<>''", $visitors_color, "Visitors", $total->visitors);

	//###############################################################################################
	// VISITORS FEEDS ROW
	luc_Row("DISTINCT ip", "feed<>''", "spider=''", "agent<>''", $rss_visitors_color, "Visitors RSS Feeds", $total->visitors_feeds);

	//###############################################################################################
	// PAGE VIEWS ROW
	luc_Row("*", "feed=''", "spider=''", "agent<>''", $pageviews_color, "Pageviews", $total->pageviews);

	//###############################################################################################
	// PAGE VIEWS FEEDS ROW
	luc_Row("*", "feed<>''", "spider=''", "agent<>''", $rss_pageviews_color, "Pageviews RSS Feeds", $total->pageviews_feeds);

	//###############################################################################################	
	// SPIDERS ROW
	if ($StatPressV_Option['StatPressV_Dont_Collect_Spider'] == '')
		luc_Row("*", "feed=''", "spider<>''", "agent<>''", $spider_color, "Spiders", $total->spiders);

	echo "</table>";
	//###############################################################################################
	
}

function luc_main_table_bargraph($overview = null)
{
	global $StatPressV_Option;
	
	$visitors_color = "#114477";
	$rss_visitors_color = "#FFF168";
	$pageviews_color = "#3377B6";
	$rss_pageviews_color = "#f38f36";
	$spider_color = "#83b4d8";
	$action = "overview";
	$graphdays = ($StatPressV_Option['StatPressV_Graph_Days'] == 0 ? 7: $StatPressV_Option['StatPressV_Graph_Days']);
	
	if (isset($_POST['page_index']))
		$overview = luc_main_table_overview_array(luc_page_index());

	// Overhead of the graph, display the average by day of the visitors, visitors feeds, pageviews, pageviews feeds and spiders
	?>
	<table class='widefat' >
		<thead>
			<tr>
				<th scope='col'>Average by day : </th>
				<th scope='col'><div style='background:<?php _e($visitors_color) ?>;width:10px;height:10px;float:left;margin-top:2px;margin-right:5px;'></div><?php _e((round($overview['total']->totalvisitors / $graphdays, 1))) ?> Visitors</th>
				<th scope='col'><div style='background:<?php _e($rss_visitors_color) ?>;width:10px;height:10px;float:left;margin-top:2px;margin-right:5px;'></div><?php _e((round($overview['total']->totalvisitors_feeds / $graphdays, 1))) ?> Visitors Feeds</th>
				<th scope='col'><div style='background:<?php _e($pageviews_color) ?>;width:10px;height:10px;float:left;margin-top:2px;margin-right:5px;'></div><?php _e((round($overview['total']->totalpageviews / $graphdays, 1))) ?> Pageviews
				<th scope='col'><div style='background:<?php _e($rss_pageviews_color) ?>;width:10px;height:10px;float:left;margin-top:2px;margin-right:5px;'></div><?php _e((round($overview['total']->totalpageviews_feeds / $graphdays, 1))) ?> Pageviews Feeds</th>
	<?php
	if ($StatPressV_Option['StatPressV_Dont_Collect_Spider'] == '')
		echo "	<th scope='col'><div style='background:$spider_color;width:10px;height:10px;float:left;margin-top:2px;margin-right:5px;'></div>" . (round($overview['total']->totalspiders / $graphdays, 1)) . " Spiders</th>";
	?>
			</tr>
		</thead>
	</table>
	<table class='graph'>
		<tr><?php luc_graph($overview['px'], $overview['total'], $graphdays, $overview['pp'], $action) ?></tr>
	</table>
	<?php
	
	if (isset ($_POST['page_index']))
		die();	
}

function luc_page_index()
{
	if (isset ($_POST['page_index']))
	{ 
		// Get page_index parameter from the POST header
		$index = $_POST['page_index'];
		if ($index <= 0)
			$index = 1;
		else
			$index += 1;
	}
	else
		// URL doesn't have &page_index= parameter
		$index = 1;
	return $index;
}

function luc_insert_pagination_options($element_prefix, $items_total, $items_page)
{
	global $StatPressV_Option;
	?>
	<form id="paginationoptions" name="paginationoptions">
		<input type="hidden" value="<?php _e($element_prefix) ?>" name="element_prefix" id="element_prefix"/>
		<input type="hidden" value="<?php _e($items_total) ?>" name="total_items" id="total_items" class="numeric"/>
		<input type="hidden" value="<?php _e($items_page) ?>" name="items_per_page" id="items_per_page" class="numeric"/>
		<input type="hidden" value="10" name="num_display_entries" id="num_display_entries" class="numeric"/>
		<input type="hidden" value="2" name="num_edge_entries" id="num_edge_entries" class="numeric"/>
		<input type="hidden" value="Prev" name="prev_text" id="prev_text"/>
		<input type="hidden" value="Next" name="next_text" id="next_text"/>
	</form>
	<?php
}

function luc_main()
{
	$start = microtime(true);
	
	if (isset ($_GET['ip']))
	{ // This is a query for an IP address
		luc_Lookup("ip", $_GET['ip']);
		return true;
	}
	if (isset ($_GET['pageid']))
	{ // This is a query for a post or page
		luc_Lookup("pageid", $_GET['pageid']);
		return true;
	}

	global $StatPressV_Option;
	$overview = luc_main_table_overview_array(1);

	?>
	<div class='wrap' >
	<div class="tabbed">
	<!-- The tabs -->
		<ul>
			<li><strong><a href="#tabs-1">Overview</a></strong></li>
			<li><strong><a href="#tabs-2">Latest Hits</a></strong></li>
			<li><strong><a href="#tabs-3">Latest Search Terms</a></strong></li>
			<li><strong><a href="#tabs-4">Latest Referrers</a></strong></li>
			<li><strong><a href="#tabs-5">Latest Feeds</a></strong></li>
	<?php
	if ($StatPressV_Option['StatPressV_Dont_Collect_Spider'] =='')
	{ ?>
			<li><strong><a href="#tabs-6">Latest Spiders</a></strong></li>
			<li><strong><a href="#tabs-7">Latest Spams Bots</a></strong></li>
	<?php
	} ?>		
			<li><strong><a href="#tabs-8">Latest Undefined Agents</a></strong></li>
	
			</ul>

<!-- tab 1 -->
		<div id="tabs-1">
			<div id="overview"> <?php luc_main_table_overview($overview['total']) ?> </div>
			<div id="bargraph"> <?php luc_main_table_bargraph($overview) ?> </div>
			<?php luc_insert_pagination_options("bargraph", $overview['numdays'], ($StatPressV_Option['StatPressV_Graph_Days'] == 0 ? 7: $StatPressV_Option['StatPressV_Graph_Days'])); ?>
			<form id='bargraphForm'>
				<table class='main'>
					<tr>
						<td align='center'>
							<div id="pagination"></div>
						</td>
						<td>
							<img src="<?php echo STATPRESS_V_PLUGIN_URL ?>/images/ajax-loader.gif" id="bargraphLoader" style="display: none;" />
							<input type="hidden" name="action" value="table_bargraph" />
						</td>
					</tr>
				</table>
			</form>
		</div>

<!-- tab 2 -->
		<div id="tabs-2">
			<table>
				<form id='latesthitsForm'>
					<table class='main'>
						<tr>
							<td>Rows:</td>
							<td align='right'>
								<select name='hitsrows' id='hitsrows'>
									<option value='0'>--Select--</option>
									<option value='5'>5</option>
									<option value='10'>10</option>
									<option value='25'>25</option>
									<option value='50'>50</option>
									<option value='100'>100</option>
									<option value='250'>250</option>
									<option value='500'>500</option>
								</select>
								<img src="<?php _e(STATPRESS_V_PLUGIN_URL) ?>/images/ajax-loader.gif" id="latesthitsLoader" style="display: none;" />
								<input type="hidden" name="action" value="table_latest_hits" />
							</td>
						</tr>
					</table>
				</form>
			</table>
			<div id="latesthits"> <?php luc_main_table_latest_hits() ?> </div>
		</div>

<!-- tab 3 -->
		<div id="tabs-3">
			<table>
				<form id='latestsearchForm'>
					<table class='main'>
						<tr>
							<td>Rows:</td>
							<td align='right'>
								<select name='searchrows' id='searchrows'>
									<option value='0'>--Select--</option>
									<option value='5'>5</option>
									<option value='10'>10</option>
									<option value='25'>25</option>
									<option value='50'>50</option>
									<option value='100'>100</option>
									<option value='250'>250</option>
									<option value='500'>500</option>
								</select>
								<img src="<?php _e(STATPRESS_V_PLUGIN_URL) ?>/images/ajax-loader.gif" id="latestsearchLoader" style="display: none;" />
								<input type="hidden" name="action" value="table_latest_search" />
							</td>
						</tr>
					</table>
				</form>
			</table>
			<div id="latestsearch"> <?php luc_main_table_latest_search() ?> </div>
		</div>

<!-- tab 4 -->
		<div id="tabs-4">
			<table>
				<form id='latestreferrersForm'>
					<table class='main'>
						<tr>
							<td>Rows:</td>
							<td align='right'>
								<select name='referrersrows' id='referrersrows'>
									<option value='0'>--Select--</option>
									<option value='5'>5</option>
									<option value='10'>10</option>
									<option value='25'>25</option>
									<option value='50'>50</option>
									<option value='100'>100</option>
									<option value='250'>250</option>
									<option value='500'>500</option>
								</select>
								<img src="<?php _e(STATPRESS_V_PLUGIN_URL) ?>/images/ajax-loader.gif" id="latestreferrersLoader" style="display: none;" />
								<input type="hidden" name="action" value="table_latest_referrers" />
							</td>
						</tr>
					</table>
				</form>
			</table>
			<div id="latestreferrers"> <?php luc_main_table_latest_referrers() ?> </div>
		</div>

<!-- tab 5 -->
		<div id="tabs-5">
			<table>
				<form id='latestfeedsForm'>
					<table class='main'>
						<tr>
							<td>Rows:</td>
							<td align='right'>
								<select name='feedsrows' id='feedsrows'>
									<option value='0'>--Select--</option>
									<option value='5'>5</option>
									<option value='10'>10</option>
									<option value='25'>25</option>
									<option value='50'>50</option>
									<option value='100'>100</option>
									<option value='250'>250</option>
									<option value='500'>500</option>
								</select>
								<img src="<?php _e(STATPRESS_V_PLUGIN_URL) ?>/images/ajax-loader.gif" id="latestfeedsLoader" style="display: none;" />
								<input type="hidden" name="action" value="table_latest_feeds" />
							</td>
						</tr>
					</table>
				</form>
			</table>
			<div id="latestfeeds"> <?php luc_main_table_latest_feeds() ?> </div>
		</div>

<!-- tab 6 -->
	<?php
	if ($StatPressV_Option['StatPressV_Dont_Collect_Spider'] == '')
	{
	?>
		<div id="tabs-6">
			<table>
				<form id='latestspidersForm'>
					<table class='main'>
						<tr>
							<td>Rows:</td>
							<td align='right'>
								<select name='spidersrows' id='spidersrows'>
									<option value='0'>--Select--</option>
									<option value='5'>5</option>
									<option value='10'>10</option>
									<option value='25'>25</option>
									<option value='50'>50</option>
									<option value='100'>100</option>
									<option value='250'>250</option>
									<option value='500'>500</option>
								</select>
								<img src="<?php _e(STATPRESS_V_PLUGIN_URL) ?>/images/ajax-loader.gif" id="latestspidersLoader" style="display: none;" />
								<input type="hidden" name="action" value="table_latest_spiders" />
							</td>
						</tr>
					</table>
				</form>
			</table>
			<div id="latestspiders"> <?php luc_main_table_latest_spiders() ?> </div>
		</div>

<!-- tab 7 -->
		<div id="tabs-7">
			<table>
				<form id='latestspambotsForm'>
					<table class='main'>
						<tr>
							<td width=270px><h2>Latest Spambots</h2></td>
							<td align='right'>
								<select name='spambotsrows' id='spambotsrows'>
									<option value='0'>Rows:</option>
									<option value='5'>5</option>
									<option value='10'>10</option>
									<option value='25'>25</option>
									<option value='50'>50</option>
									<option value='100'>100</option>
								</select>
								<img src="<?php echo STATPRESS_V_PLUGIN_URL ?>/images/ajax-loader.gif" id="latestspambotsLoader" style="display: none;" />
								<input type="hidden" name="action" value="table_latest_spambots" />
							</td>
						</tr>
					</table>
				</form>
			</table>
	<div id="latestspambots"> <?php luc_main_table_latest_spambots() ?> </div>
	</div>
<?php
	}
	?>
<!-- tab 8 -->
		<div id="tabs-8">
			<table>
				<form id='latestundefagentsForm'>
					<table class='main'>
						<tr>
							<td>Rows:</td>
							<td align='right'>
								<select name='undefagentsrows' id='undefagentsrows'>
									<option value='0'>--Select--</option>
									<option value='5'>5</option>
									<option value='10'>10</option>
									<option value='25'>25</option>
									<option value='50'>50</option>
									<option value='100'>100</option>
									<option value='250'>250</option>
									<option value='500'>500</option>
								</select>
								<img src="<?php _e(STATPRESS_V_PLUGIN_URL) ?>/images/ajax-loader.gif" id="latestundefagentsLoader" style="display: none;" />
								<input type="hidden" name="action" value="table_latest_undefagents" />
							</td>
						</tr>
					</table>
				</form>
			</table>
			<div id="latestundefagents"> <?php luc_main_table_latest_undefagents() ?> </div>
		</div>
	
	</div> <!-- End tabbed div -->
		<!-- end of the tab -->
		<br>
	<table style="width:100%;">
		<tbody>
			<tr>
				<td width="200px"><font size="1"><b>StatPress Visitors table size</b></font></td>
				<td><font size="1"><?php _e(luc_tablesize()) ?></font></td>
			</tr>
			<tr>
				<td><font size="1"><b>StatPress Visitors current time</b></font></td>
				<td><font size="1"><?php _e(current_time('mysql')) ?></font></td>
			</tr>
			<tr>
				<td><font size="1"><b>RSS2 URL</b></font></td>
				<td><font size="1"><?php _e(get_bloginfo('rss2_url') . " (" . luc_StatPress_extractfeedreq(get_bloginfo('rss2_url')) . ")") ?></font></td>
			</tr>
			<tr>
				<td><font size="1"><b>ATOM URL</b></font></td>
				<td><font size="1"><?php _e(get_bloginfo('atom_url') . " (" . luc_StatPress_extractfeedreq(get_bloginfo('atom_url')) . ")") ?></font></td>
			</tr>
			<tr>
				<td><font size="1"><b>RSS URL</b></font></td>
				<td><font size="1"><?php _e(get_bloginfo('rss_url') . " (" . luc_StatPress_extractfeedreq(get_bloginfo('rss_url')) . ")") ?></font></td>
			</tr>
			<tr>
				<td><font size="1"><b>RSS2 Comments URL</b></font></td>
				<td><font size="1"><?php _e(get_bloginfo('comments_rss2_url') . " (" . luc_StatPress_extractfeedreq(get_bloginfo('comments_rss2_url')) . ")") ?></font></td>
			</tr>
			<tr>
				<td><font size="1"><b>ATOM Comments URL</b></font></td>
				<td><font size="1"><?php _e(get_bloginfo('comments_atom_url') . " (" . luc_StatPress_extractfeedreq(get_bloginfo('comments_atom_url')) . ")") ?></font></td>
			</tr>
		</tbody>
	</table>
	<?php _e(luc_StatPressV_load_time($start)) ?>
	</div>
	<?php
}

function luc_main_table_overview_array($page_index)
{	
	global $StatPressV_Option, $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;
	
	$action = "overview";
	
	$graphdays = ($StatPressV_Option['StatPressV_Graph_Days'] == 0 ? 7: $StatPressV_Option['StatPressV_Graph_Days']);
	$pp = $page_index;	

	$limitdate = gmdate('Ymd', current_time('timestamp') - 86400 * $graphdays * $pp + 86400); // first date to display on the graphs
	$currentdate = gmdate('Ymd', current_time('timestamp') - 86400 * $graphdays * ($pp -1)); // last date to display on the graphs
	
	$total = luc_count_graph(luc_count_graph_init($graphdays, $pp), $graphdays, $pp, " 1=1 ", " 1=1 ", "feed=''", "feed<>''", $limitdate, $currentdate);
	$maxxday = luc_maxxday($total, $graphdays, $pp); //calculation sum of the maximumn of visitors, pageviews, feeds, and spider for all days of the graph ($graphdays)
	$px = luc_pixel($total, $graphdays, $maxxday, $pp, $action);

	$overview['pp'] = $pp;
	$overview['numdays'] = luc_get_db_num_days("SELECT date FROM $table_name GROUP BY date ORDER BY date ASC LIMIT 1;");
	$overview['numpages'] = $overview['numdays'] / $graphdays;
	$overview['total'] = $total;
	$overview['maxxday'] = $maxxday;
	$overview['px'] = $px;
	
	return $overview;
}

function luc_get_db_num_days($query)
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;
	
	$first = $wpdb->get_var($query);
	
	if (isset ($first))
		$num_days = ceil((current_time('timestamp') - strtotime($first)) / 86400);
	else
		$num_days = 0;
		
	return $num_days;
}

function luc_count_graph_init($graphdays, $pp)
{
	$total = (object) array();
	for ($i = 0; $i < $graphdays; $i++)
	{
		$date = gmdate('Ymd', current_time('timestamp') - 86400 * ($graphdays * $pp - $i -1));
		$total->visitors[$date] = 0;
		$total->visitors_feeds[$date] = 0;
		$total->pageviews[$date] = 0;
		$total->pageviews_feeds[$date] = 0;
		$total->spiders[$date] = 0;
	}
	return $total;
}
function luc_init_count_graph($graphdays, $pp)
{
	for ($i = 0; $i < $graphdays; $i++)
	{
		$date = gmdate('Ymd', current_time('timestamp') - 86400 * ($graphdays * $pp - $i -1));
		$total->visitors[$date] = 0;
		$total->visitors_feeds[$date] = 0;
		$total->pageviews[$date] = 0;
		$total->pageviews_feeds[$date] = 0;
		$total->spiders[$date] = 0;
	}
	return $total;
}

function luc_count_graph($total, $graphdays, $pp, $where1, $where2, $feed1, $feed2, $limitdate, $currentdate)
{ //TOTAL VISITORS
	$qry_visitors = luc_query_graph("DISTINCT ip", $where1 . " AND " . $feed1 . " AND spider='' ", $limitdate, $currentdate); // SQL query count of the uniques visitors for all days of the graph ($graphdays)
	foreach ($qry_visitors as $row)
	{
		$total->visitors[$row->date] = $row->total;
		$total->totalvisitors += $row->total;
	}

	//TOTAL VISITORS FEEDS
	$qry_visitors_feeds = luc_query_graph("DISTINCT ip", $where2 . " AND " . $feed2 . " AND spider='' ", $limitdate, $currentdate); // SQL query count of the visitors feeds for all days of the graph ($graphdays)
	foreach ($qry_visitors_feeds as $row)
	{
		$total->visitors_feeds[$row->date] = $row->total;
		$total->totalvisitors_feeds += $row->total;
	}
	//TOTAL PAGEVIEWS (we do not delete the uniques, this is falsing the info.. uniques are not different visitors!)
	$qry_pageviews = luc_query_graph("*", $where1 . " AND " . $feed1 . " AND spider='' ", $limitdate, $currentdate); // SQL count of the pageviews for all days of the graph ($graphdays)
	foreach ($qry_pageviews as $row)
	{
		$total->pageviews[$row->date] = $row->total;
		$total->totalpageviews += $row->total;
	}

	//TOTAL PAGEVIEWS FEEDS
	$qry_pageviews_feeds = luc_query_graph("*", $where2 . " AND " . $feed2 . " AND spider='' ", $limitdate, $currentdate); // SQL query count of the pageviews feeds for all days of the graph ($graphdays)
	foreach ($qry_pageviews_feeds as $row)
	{
		$total->pageviews_feeds[$row->date] = $row->total;
		$total->totalpageviews_feeds += $row->total;
	}

	//TOTAL SPIDERS
	if ($StatPressV_Option['StatPressV_Dont_Collect_Spider'] == '')
	{
		$qry_spiders = luc_query_graph("*", $where2 . " AND " . $feed1 . " AND spider<>'' ", $limitdate, $currentdate); // SQL query count of the spiders for all days of the graph ($graphdays)
		foreach ($qry_spiders as $row)
		{
			$total->spiders[$row->date] = $row->total;
			$total->totalspiders += $row->total;
		}

	}
	return $total;
}

function luc_graph_calculation($graphdays, $pp)
{	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	// Graph calculations
	$limitdate = gmdate('Ymd', current_time('timestamp') - 86400 * $graphdays * $pp +86400); // first date to display on the graphs
	$currentdate = gmdate('Ymd', current_time('timestamp') - 86400 * $graphdays * ($pp -1)); // last date to display on the graphs
	$NP = luc_count_periode("date","","FROM $table_name","ip IS NOT NULL","date",$graphdays); // total of all display pages link
	
	$total=luc_init_count_graph($graphdays,$pp);
    $total=luc_count_graph ($total,$graphdays,$pp," 1=1 "," 1=1 ","feed=''","feed<>''",$limitdate,$currentdate);	
				
	return $total;
}

function luc_Row($count, $feed, $spider, $agent, $color, $text, $total)
{
	$visitors_color = "#114477";
	$rss_visitors_color = "#FFF168";
	$pageviews_color = "#3377B6";
	$rss_pageviews_color = "#f38f36";
	$spider_color = "#83b4d8";

	$lastmonth = luc_StatPress_lastmonth();
	$thismonth = gmdate('Ym', current_time('timestamp'));
	$yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
	$today = gmdate('Ymd', current_time('timestamp'));

	//TOTAL
	$qry_total = requete_main($count, $feed, $spider, $agent, "1 = 1");
	//LAST MONTH
	$qry_lmonth = requete_main($count, $feed, $spider, $agent, "date LIKE '" . $lastmonth . "%'");
	//THIS MONTH
	$qry_tmonth = requete_main($count, $feed, $spider, $agent, "date LIKE '" . $thismonth . "%'");
	$qry_tmonth_change = pourcent_change($qry_tmonth, $qry_lmonth);
	//TARGET
	$tmonthtarget = round($qry_tmonth / (time() - mktime(0, 0, 0, date('m'), date('1'), date('Y'))) * (86400 * date('t')));
	$tmonthadded = pourcent_change($tmonthtarget, $qry_lmonth);
	//YESTERDAY
	$qry_y = $total[$yesterday];
	//TODAY
	$qry_t = $total[$today];
	echo "<tr><td><div style='background:$color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>$text</td>
		<td>" . number_format_i18n($qry_total) . "</td>\n
		<td>" . number_format_i18n($qry_lmonth) . "</td>\n
		<td>" . number_format_i18n($qry_tmonth) . $qry_tmonth_change . "</td>\n
		<td>" . number_format_i18n($tmonthtarget) . $tmonthadded . "</td>\n
		<td>" . number_format_i18n($qry_y) . "</td>\n
		<td>" . number_format_i18n($qry_t) . "</td>\n</tr>";
}

function luc_HTML_IMG($key, $type, $showname)
{
	if ($key != '')
	{
		$search = strtolower($key);
		$title = $key;
		
		// Look for fields in definition file
		if ($type === "country")
			$lines = file(STATPRESS_V_PLUGIN_PATH . "/def/domain.dat");
		else
			$lines = file(STATPRESS_V_PLUGIN_PATH . "/def/" . $type . ".dat");
		
		foreach ($lines as $line_num => $line)
		{
			$entry = explode("|", strtolower($line));

			if (in_array($search, $entry, true))
			{
				// We have a match
				
					$title = explode('|',$line);
					$title = $title[0];

				if ($type === "country")
					$img = STATPRESS_V_PLUGIN_URL . "images/domain/" . $entry[1] . ".png";
				else
					$img = STATPRESS_V_PLUGIN_URL . "images/" . $type . "/" .  str_replace(" ", "_", str_replace(".", "-", $entry[0])) . ".png";				
				break;
			}
		}
		if ($showname === true)
			return "<IMG style='border:0px;height:16px;' alt='$title' title='$title' SRC='$img'>&nbsp;&nbsp;$title";
		else
			return "<IMG style='border:0px;height:16px;' alt='" . $title . "' title='" . $title . "' SRC='" . $img . "'>";
	}
	else
		return "&nbsp;";
}

function luc_language($rk)
{
	if ($rk->language != '')
	{
		$lines = file(STATPRESS_V_PLUGIN_PATH . 'def/languages.dat');
		foreach ($lines as $line_num => $ligne) //see
		{
			list ($langue, $id) = explode("|", $ligne);
			if ($id == $rk->language)
				break; // break, the language is found
		}
		return $langue;
	}
	else
		return "&nbsp;";
}

function luc_dropdown_caps($default = false)
{
	global $wp_roles;
	$role = get_role('administrator');
	foreach ($role->capabilities as $cap => $grant)
	{
		echo "<option ";
		if ($default == $cap)
			echo "selected ";
		echo ">$cap</option>";
	}
}

function pourcent_change($current, $last)
{
	if ($last <> 0)
	{
		$pt = round(100 * ($current / $last) - 100, 1);
		if ($pt >= 0)
			$pt = "+" .
			$pt;
		$change = "<code> (" . $pt . "%)</code>";
		return $change;
	}
	return '';
}

function requete_main($count, $feed, $spider, $agent, $date)
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;
	$qry = $wpdb->get_var("SELECT count($count)
				FROM $table_name
				WHERE  $feed
					AND $spider
					AND $agent
					AND $date;");
	return $qry;
}

function luc_export()
{
	?>
<div class='wrap'>
	<h2>
		<?php _e('Export stats to text file', 'statpressV'); ?>
		(csv)
	</h2>
	<form method=get>
		<table>
			<tr>
				<td><?php _e('From', 'statpressV'); ?></td>
				<td><input type=text name=from class='datepicker'></td>
			</tr>
			<tr>
				<td><?php _e('To', 'statpressV');?></td>
				<td><input type=text name=to class='datepicker'></td>
			</tr>
			<tr>
				<td><?php _e('Fields delimiter', 'statpressV');?></td>
				<td><select name=del>
						<option>,</option>
						<option>;</option>
						<option>|</option>
				</select>

			</tr>
			<tr>
				<td><?php _e('File extension', 'statpressV');?></td>
				<td><select name=ext>
						<option>.csv</option>
						<option>.txt</option>
						<option>.log</option>
				</select>

			</tr>
			<tr>
				<td></td>
				<td><input type=submit value=<?php _e('Export', 'statpressV');?>></td>
			</tr>
			<tr><td>
				<input type=hidden name=page value=statpress>
				<input type=hidden name=statpress_action value=exportnow>
			</td></tr>
		</table>
	</form>
</div>
<?php
}

function luc_exportNow()
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;
	$from = gmdate('Ymd', strtotime($_GET['from']));
	$to = gmdate('Ymd', strtotime($_GET['to']));
	$filename = str_replace("http://", "", get_bloginfo('url')) . "_statpress_" . $from . "-" . $to . $_GET['ext'];

	// Set headers to download
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=$filename");
	header("Content-Type: application/octet-stream charset=" . get_option('blog_charset'), true);

	// Set the delimiter
	$del = my_substr($_GET['del'], 0, 1);

	// Write out column headings
	echo "date" . $del . "time" . $del . "ip" . $del . "urlrequested" . $del .
	"agent" . $del . "referrer" . $del . "search" . $del . "nation" . $del .
	"os" . $del . "browser" . $del . "searchengine" . $del . "spider" . $del . "feed\n";

	//  Get number of rows that will be exported
	$nr = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name;"));
	$i = 0;

	$qry_s = "SELECT *
			FROM `$table_name`
			WHERE `date` >= '" . (date("Ymd", strtotime(my_substr($from, 0, 8)))) . "'
			AND   `date` <= '" . (date("Ymd", strtotime(my_substr($to, 0, 8)))) . "'
			LIMIT 500 OFFSET YYY;";

	while ($i < $nr)
	{
		$qry = $wpdb->get_results(str_replace('YYY', $i, $qry_s));

		foreach ($qry as $rk)
		{
			echo '"' . $rk->date . '"' . $del . '"' . $rk->time . '"' . $del . '"' . $rk->ip . '"' .
			$del . '"' . $rk->urlrequested . '"' . $del . '"' . $rk->agent . '"' . $del . '"' .
			$rk->referrer . '"' . $del . '"' . urldecode($rk->search) . '"' . $del . '"' .
			$rk->nation . '"' . $del . '"' . $rk->os . '"' . $del . '"' . $rk->browser . '"' .
			$del . '"' . $rk->searchengine . '"' . $del . '"' . $rk->spider . '"' . $del . '"' .
			$rk->feed . '"' . "\n";
		}
		$i = $i +500; // Iterate 500 rows at a time
	}
	die();
}

function luc_graph($px, $total, $graphdays, $pp, $action)
{
	$visitors_color = "#114477";
	$rss_visitors_color = "#FFF168";
	$pageviews_color = "#3377B6";
	$rss_pageviews_color = "#f38f36";
	$spider_color = "#83b4d8";
	$referrer_color = "#419E0C";
	$gd = (90 / $graphdays) . '%';

	if ($action == 'referrer')
		$color = $referrer_color;
	else
		$color = $visitors_color;
	$start_of_week = get_option('start_of_week');
	for ($i = 0; $i < $graphdays; $i++)
	{		$timestamp = current_time('timestamp') - 86400 * ($graphdays * $pp-$i-1 );
			$Date = gmdate('Ymd', $timestamp);
			echo '<td width="' . $gd . '" valign="bottom"';
            if ($start_of_week == gmdate('w', $timestamp))  // week-cut
                      echo ' style="border-left:2px dotted gray;"';		
			echo "><div style='float:left;height: 100%;width:100%;font-family:Helvetica;font-size:7pt;text-align:center;border-right:1px solid white;color:black;'>
				<div style='background:$color;width:100%;height:" . $px->visitors[$Date] . "px;' title='" . $total->visitors[$Date] . " " . __('visitors', 'statpressV') . "'></div>
				<div style='background:$rss_visitors_color;width:100%;height:" . $px->visitors_feeds[$Date] . "px;' title='" . $total->visitors_feeds[$Date] . " " . __('visitors feeds', 'statpressV') . "'></div>
				<div style='background:$pageviews_color;width:100%;height:" . $px->pageviews[$Date] . "px;' title='" . $total->pageviews[$Date] . " " . __('pageviews', 'statpressV') . "'></div>
				<div style='background:$rss_pageviews_color;width:100%;height:" . $px->pageviews_feeds[$Date] . "px;' title='" . $total->pageviews_feeds[$Date] . " " . __('pageviews feeds', 'statpressV') . "'></div>
				<div style='background:$spider_color;width:100%;height:" . $px->spiders[$Date] . "px;' title='" . $total->spiders[$Date] . " " . __('spiders', 'statpressV') . "'></div>
				<div style='background:gray;width:100%;height:1px;'></div>
				<div style='width:100%;height:40px;'>" . gmdate('d', current_time('timestamp') - 86400 * ($graphdays * $pp - $i -1)) . ' ' . gmdate('M', current_time('timestamp') - 86400 * ($graphdays * $pp - $i -1)) . "</div></div></td>\n";
	};
}

function luc_pixel($total, $graphdays, $maxxday, $pp, $action)
{
	if ($action <> 'overview')
		$heigth = 100; // heigth of the graph
	else
		$heigth = 200;
	for ($i = 0; $i < $graphdays; $i++)
	{
		$Date = gmdate('Ymd', current_time('timestamp') - 86400 * ($graphdays * $pp - $i -1));
		$px->visitors[$Date] = round($total->visitors[$Date] * $heigth / $maxxday);
		$px->visitors_feeds[$Date] = round($total->visitors_feeds[$Date] * $heigth / $maxxday);
		$px->pageviews[$Date] = round($total->pageviews[$Date] * $heigth / $maxxday);
		$px->pageviews_feeds[$Date] = round($total->pageviews_feeds[$Date] * $heigth / $maxxday);
		$px->spiders[$Date] = round($total->spiders[$Date] * $heigth / $maxxday);
		$px->white[$Date] = $heigth - $px->visitors[$Date] - $px->visitors_feeds[$Date] - $px->pageviews[$Date] - $px->pageviews_feeds[$Date] - $px->spiders[$Date];
	}
	return $px;
}

function luc_query_graph($select, $where, $limitdate, $currentdate)
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;
	$qry = $wpdb->get_results("SELECT count($select) AS total, date
				FROM $table_name
				WHERE agent <>'' AND realpost = true AND $where AND date BETWEEN $limitdate AND $currentdate
				GROUP BY date;");
	return $qry;
}

function luc_maxxday($total, $graphdays, $pp)
{
	$maxxd = 0;
	for ($i = 0; $i < $graphdays; $i++)
	{
		$Date = gmdate('Ymd', current_time('timestamp') - 86400 * ($graphdays * $pp - $i -1));
		$maxd[$Date] = $total->visitors[$Date] + $total->visitors_feeds[$Date] + $total->pageviews[$Date] + $total->pageviews_feeds[$Date] + $total->spiders[$Date];
		if ($maxd[$Date] > $maxxd)
			$maxxd = $maxd[$Date];
	}

	if ($maxxd == 0)
		$maxxd = 1;
	return $maxxd;
}

function luc_page_periode()
{
	global $wpdb;
	// pp is the display page periode
	if (isset ($_GET['pp']))
	{ // Get Current page periode from URL
		$periode = $_GET['pp'];
		if ($periode <= 0)
			// Periode is less than 0 then set it to 1
			$periode = 1;
	}
	else
		// URL does not show the page set it to 1
		$periode = 1;
	return $periode;
}

function luc_page_posts()
{
	global $wpdb;
	// pa is the display pages Articles
	if (isset ($_GET['pa']))
	{
		$pageA = $_GET['pa']; // Get Current page Articles from URL
		if ($pageA <= 0) // Article is less than 0 then set it to 1
			$pageA = 1;
	}
	else // URL does not show the Article set it to 1
		$pageA = 1;
	return $pageA;
}

function luc_print_pp_link($NP, $pp, $action)
{ // For all pages ($NP) Display first 3 pages, 3 pages before current page($pp), 3 pages after current page , each 25 pages and the 3 last pages for($action)

	$GUIL1 = FALSE;
	$GUIL2 = FALSE; // suspension points  not writed  style='border:0px;width:16px;height:16px;   style="border:0px;width:16px;height:16px;"
	if ($NP > 1)
	{
		echo "<div class='tablenav-pages'>";
		if (($action <> "update") OR ($action <> "overview"))
			echo "<font size='2'>Previous period: </font>";
		for ($i = 1; $i <= $NP; $i++)
		{
			if ($i <= $NP)
			{ // $page is not the last page
				if ($i == $pp)
					echo " [{$i}] "; // $page is current page
				else
				{ // Not the current page Hyperlink them
					if (($i <= 3) or (($i >= $pp -3) and ($i <= $pp +3)) or ($i >= $NP -3) or is_int($i / 1000))
					{
						if ($action == "overview")
							echo '<a class="pagin" href="' . admin_url() .'admin.php?page=statpress-visitors/admin/luc_admin.php&pp=' . $i . '">' . $i . '</a> ';
						else
							echo '<a class="pagin" href="' . admin_url() .'admin.php?page=statpress-visitors/action=' . $action . '/&pp=' . $i . '&pa=1' . '">' . $i . '</a> ';
					}
					else
					{
						if (($GUIL1 == FALSE) OR ($i == $pp +4))
						{
							echo "...";
							$GUIL1 = TRUE;
						}
						if ($i == $pp -4)
							echo "..";
						if (is_int(($i -1) / 1000))
							echo ".";
						if ($i == $NP -4)
						{
							echo "..";
						}
						// suspension points writed
					}
				}
			}
		}
		echo "</div>";
	}
}

function luc_print_pp_pa_link($NP, $pp, $action, $NA, $pa)
{ 	
	if ($NP <> 0)
		luc_print_pp_link($NP, $pp, $action);

	// For all pages ($NP) display first 5 pages, 3 pages before current page($pa), 3 pages after current page , 3 last pages
	$GUIL1 = FALSE; // suspension points not writed
	$GUIL2 = FALSE;
	echo '<table width="100%" border="0"><tr></tr></table>';
	if ($NA > 1)
	{
		echo "<div class='tablenav-pages'>";
		echo "<font size='2'>Pages: </font>";
		for ($j = 1; $j <= $NA; $j++)
		{
			if ($j <= $NA) // $i is not the last Articles page
			{
				if ($j == $pa) // $i is current page
					echo " [{$j}] ";
				else
				{ // Not the current page Hyperlink them
					if (($j <= 5) or (($j >= $pa -2) AND ($j <= $pa +2)) or ($j >= $NA -2))
						echo '<a class="pagin" href="' . admin_url() .'admin.php?page=statpress-visitors/action=' . $action . '&pp=' . $pp . '&pa=' . $j . '">' . $j . '</a> ';
					else
					{
						if ($GUIL1 == FALSE)
							echo "... ";
						$GUIL1 = TRUE;
						if (($j == $pa +4) and ($GUIL2 == FALSE))
						{
							echo " ... ";
							$GUIL2 = TRUE;
						}
						// suspension points writed
					}
				}
			}
		}
		echo "</div>";
	}
}

function luc_count_periode($select, $from, $join = "", $where, $group, $graphdays) // count the number total of day, necessary to count the number of page periode link displayed
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;
	// selection of the older day
	$old_date = $wpdb->get_var("SELECT $select $from $join WHERE $where GROUP BY $group ORDER BY $group ASC LIMIT 1;");
	if (isset ($old_date))
		$nbjours = ceil((current_time('timestamp') - strtotime($old_date)) / 86400);
	else
		$nbjours = 0;
	$Number = ceil($nbjours / $graphdays);
	return $Number;
}

function luc_StatPress_lastmonth()
{
	$ta = getdate(current_time('timestamp'));

	$year = $ta['year'];
	$month = $ta['mon'];

	// go back 1 month;
	$month = $month -1;

	if ($month === 0)
	{
		// if this month is Jan
		// go back a year
		$year = $year -1;
		$month = 12;
	}

	// return in format 'YYYYMM'
	return sprintf($year . '%02d', $month);
}

function luc_StatPress_Abbrevia($s, $c)
{
	$res = "";
	if (strlen($s) > $c)
		$res = "...";
	return my_substr($s, 0, $c) . $res;
}

function luc_StatPress_extractfeedreq($url)
{
	if (!strpos($url, '?') === FALSE)
	{
		list ($null, $q) = explode("?", $url);
		list ($res, $null) = explode("&", $q);
	}
	else
	{
		$prsurl = parse_url($url);
		$res = $prsurl['path'] . $$prsurl['query'];
	}

	return $res;
}

function luc_tablesize()
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;
	$res = $wpdb->get_results("SHOW TABLE STATUS LIKE '$table_name'");
	foreach ($res as $fstatus)
	{
		$data_length = $fstatus->Data_length;
		$data_rows = $fstatus->Rows;
	}
	return number_format_i18n($data_length / 1024 / 1024, 2) . " MB (" . number_format_i18n($data_rows) . " records)";
}

function luc_gunzip($srcName, $dstName)
{
	$sfp = gzopen($srcName, "rb");
	if ($sfp !== false)
	{
		$fp = fopen($dstName, "w");
		if ($fp === false)
			return false;
		
		while ($string = gzread($sfp, 4096))
		{
			fwrite($fp, $string, strlen($string));
		}
		gzclose($sfp);
		fclose($fp);
		return true;
	}
	return false;
}

function luc_GeoIP_get_data($ipAddress)
{	global $StatPressV_Option;
	$StatPressV_Use_GeoIP = $StatPressV_Option['StatPressV_Use_GeoIP'];

	if (file_exists(luc_GeoIP_dbname('country')))
	{
		if ($StatPressV_Use_GeoIP == 'checked')
		{
			$gi = geoip_open(luc_GeoIP_dbname('country'), GEOIP_STANDARD);
			$array['cc'] = geoip_country_code_by_addr($gi, $ipAddress);
			$array['cn'] = utf8_encode(geoip_country_name_by_addr($gi, $ipAddress));
		}
	}

	if (file_exists(luc_GeoIP_dbname('city')))
	{
		if ($StatPressV_Use_GeoIP == 'checked')
		{
			$gic = geoip_open(luc_GeoIP_dbname('city'), GEOIP_STANDARD);
			$gir = GeoIP_record_by_addr($gic, $ipAddress);
			$array['region'] = utf8_encode($gir->region);
			$array['city'] = utf8_encode($gir->city);
			$array['postal_code'] = $gir->postal_code;
			$array['latitude'] = $gir->latitude;
			$array['longitude'] = $gir->longitude;
			$array['area_code'] = $gir->area_code;
			$array['metro_code'] = $gir->metro_code;
			$array['continent_code'] = $gir->continent_code;
		}
	}
	if (count($array) > 0)
		return $array;
	else
		return false;
}

function luc_StatPressV_SearchQ($surl)
{
	$url = parse_url($surl);
	if (strpos($url['host'], "google") > 0)
	{
		$p = strpos($surl, 'url=');
		if ($p === false)
			return $surl;
		$surl = substr($surl, 0, $p);
	}
	return $surl;
}

function luc_create_href($str, $type)
{
	if ($type == 'ip')
	{
		$qrys = "admin.php?page=statpress-visitors/admin/luc_admin.php&ip=";
		$href = "<a target='_blank' href='" . admin_url() . $qrys . $str . "'target='_self' title='Generate report for " . $str . "'>" . $str . "</a>";
	}
	return $href;
}

function luc_Client_Lookup_IP($ip)
{
	//include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_lookup.php';
	luc_display_by_IP($ip);
}

function luc_Lookup($type, $frag)
{	
	include STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_lookup.php';
	
	if ($type == "ip")
		luc_display_by_IP($frag);
	if ($type == "pageid")
		luc_display_by_PageID($frag);
}

function luc_GeoIP_update_db($edition = null)
{
	$edition = (isset ($_POST['edition']) ? $_POST['edition'] : $edition);

	$db = luc_GeoIP_dbname($edition);
	$db_dir = dirname($db);
	$db_gz = $db . '.gz';
	if ('city' == $edition)
		$db_url = "http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz";
	else
		if ('country' == $edition)
			$db_url = "http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz";
		else
			return false;

	if (is_dir($db_dir) === false)
		mkdir($db_dir);

	$db_gz_f = fopen($db_gz, "w");
	$host = parse_url($db_url, PHP_URL_HOST);

	?>
		<table class='widefat'>
			<thead>
				<tr>
				<th scope='col'>Action</th>
				<th scope='col'>Information</th>
				<th scope='col' width="50px">Result</th>
				</tr>
			</thead>
			<tbody>
				<tr><td width="20%">Resolving hostname: </td><td><?php _e($host) ?></td>
	<?php
	if (gethostbyname($host) === $host)
		echo "<td><span style='color:red'>[FAILED]</span></td></tr>";
	else
	{
		if (function_exists('curl_init'))
		{
			echo "<td><span style='color:green'>[OK]</span></td></tr>";
			echo "<tr><td>Requesting: </td><td>" . $db_url . "</td>";
	
			$ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9) Gecko/2008052906 Firefox/3.0';
			$ch = curl_init($db_url);
			curl_setopt($ch, CURLOPT_USERAGENT, $ua);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_FILE, $db_gz_f);
	
			$execute = curl_exec($ch);
	
			// Check if any error occured
			if (curl_errno($ch))
			{
				curl_close($ch);
				echo "<td><span style='color:red'>[FAILED]</span></td></tr>";
			}
			else
			{
				fclose($db_gz_f);
				$info = curl_getinfo($ch);
				curl_close($ch);
				clearstatcache();
				?>
				<td><span style='color:green'>[OK]</span></td></tr>";
				<tr><td>Server response: </td><td><?php _e($info['http_code']) ?></td><td><span style='color:green'>[OK]</span></td></tr>
				<tr><td>Content type: </td><td><?php _e($info['content_type']) ?></td><td><span style='color:green'>[OK]</span></td></tr>
				<tr><td>Remote file time: </td><td><?php _e($info['filetime']) ?></td><td><span style='color:green'>[OK]</span></td></tr>
				<tr><td>Bytes transfered: </td><td><?php _e(number_format_i18n($info['size_download'])) ?> bytes</td><td><span style='color:green'>[OK]</span></td></tr>
				<tr><td>Avg download speed: </td><td><?php _e(number_format_i18n($info['speed_download'])) ?> bytes/second</td><td><span style='color:green'>[OK]</span></td></tr>
				<tr><td>Time taken: </td><td><?php _e($info['total_time']) ?></td><td><span style='color:green'>[OK]</span></td></tr>
				<?php
				//  Check that the file is a plausable size
				if (filesize($db_gz) > 500000)
				{
					// Remove old backup
					if (file_exists($db . ".bak"))
						unlink($db . ".bak");
	
					// Move exisiting database to backup
					if (file_exists($db))
						rename($db, $db . ".bak");
					echo "<tr><td>Backing up old database:</td><td></td><td><span style='color:green'>[OK]</span></td></tr>";
	
					// Unpack new database
					if (luc_gunzip($db_gz, $db . ".new") !== true)
					{
						echo "<tr><td>Unpacking archive:</td><td></td><td><span style='color:red'>[FAILED]</span></td></tr>";
						
						// Restore backup file	
						if (file_exists($db))
							rename($db . ".bak", $db);
						echo "<tr><td>Restoring backup database:</td><td></td><td><span style='color:green'>[OK]</span></td></tr>";
					}
					else
					{
						echo "<tr><td>Unpacking archive:</td><td></td><td><span style='color:green'>[OK]</span></td></tr>";
		
						// Rename new database
						if (file_exists($db . ".new"))
							rename($db . ".new", $db);
		
						// Remove gzip file
						if (file_exists($db_gz))
							unlink($db_gz);
					}
				}
				else
					echo "<tr><td>Downloaded file size too small.  Aborted.</td><td></td><td><span style='color:red'>[FAILED]</span></td></tr>";
			}
		}
		else
		{
			echo "<tr><td>Requesting: </td><td>PHP not built with cURL support: Manual install required</td><td><span style='color:red'>[FAILED]</span></td></tr>";
		}
		echo "</tbody></table>";
	}
	if (isset ($_POST['edition']))
		die();
}
?>