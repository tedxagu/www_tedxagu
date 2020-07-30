<?php

/**
 * Based on the plugin StatPress Dashboard Widget Lite originally by Andreas Kaul from
 * http://blog.dunkelwesen.de/download-statpress-dashboard-widget-lite/
 */

/**
 * Dashboard widget
 */
function luc_StatPresV_Dashboard_Widget()
{	
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;

	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
	{
		die('StatPress is not installed');
	}

	# Table properties
	$unique_color = "#114477";
	$web_color = "#3377B6";
	$rss_color = "#f38f36";
	$spider_color = "#83b4d8";

	$lastmonth = luc_StatPress_lastmonth();
	$thismonth = gmdate('Ym', current_time('timestamp'));
	$yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
	$today = gmdate('Ymd', current_time('timestamp'));
	$tlm[0] = substr($lastmonth, 0, 4);
	$tlm[1] = substr($lastmonth, 4, 2);

	print "<table class='widefat'><thead><tr>
			<th scope='col'></th>
			<th scope='col'>" . __('Total', 'statpress') . "</th>
			<th scope='col'>" . __('Yesterday', 'statpress') . "<br /><font size=1>" . gmdate('d M, Y', current_time('timestamp') - 86400) . "</font></th>
			<th scope='col'>" . __('Today', 'statpress') . "<br /><font size=1>" . gmdate('d M, Y', current_time('timestamp')) . "</font></th>
			</tr></thead>
			<tbody id='the-list'>";

	################################################################################################
	# VISITORS ROW
	print "<tr><td>" . __('Visitors', 'statpress') . "</td>";

	#TOTAL
	$qry_total = $wpdb->get_var("
					SELECT count(DISTINCT ip)
					FROM $table_name
					WHERE feed = ''
						AND spider = ''
					");
	echo "<td>" . $qry_total. "</td>\n";

	$qry = $wpdb->get_results("
					SELECT date, COUNT(DISTINCT ip) as total
					FROM $table_name
					WHERE feed = ''
						AND spider = ''
						AND date BETWEEN $yesterday AND $today
					GROUP BY date");

	echo luc_print_dashstats($yesterday, $today, $qry);

	echo "</tr>";

	################################################################################################
	# PAGE VIEWS ROW
	print "<tr><td>" . __('Page Views', 'statpress') . "</td>";

	#TOTAL
	$qry_total = $wpdb->get_var("
					SELECT COUNT(date)
					FROM $table_name
					WHERE feed = ''
						AND spider = ''
					");
	echo "<td>" . $qry_total. "</td>\n";

	$qry = $wpdb->get_results("
					SELECT date, COUNT(date) as total
					FROM $table_name
					WHERE feed = ''
						AND spider = ''
						AND date BETWEEN $yesterday AND $today
					GROUP BY date");

	echo luc_print_dashstats($yesterday, $today, $qry);

	echo "</tr>";

	################################################################################################
	# SPIDERS ROW
	print "<tr><td>Spiders</td>";
	#TOTAL
	$qry_total = $wpdb->get_var("
					SELECT COUNT(date)
					FROM $table_name
					WHERE feed = ''
						AND spider <> ''
					");
	print "<td>" . $qry_total. "</td>\n";

	$qry = $wpdb->get_results("
					SELECT date, COUNT(date) as total
					FROM $table_name
					WHERE feed = ''
						AND spider <> ''
						AND date BETWEEN $yesterday AND $today
					GROUP BY date");

	echo luc_print_dashstats($yesterday, $today, $qry);

	echo "</tr>";

	################################################################################################
	# FEEDS ROW
	print "<tr><td>Feeds</td>";

	#TOTAL
	$qry_total = $wpdb->get_var("
					SELECT count(date)
					FROM $table_name
					WHERE feed <> ''
						AND spider = ''
					");
	print "<td>" . $qry_total. "</td>\n";

	$qry = $wpdb->get_results("
					SELECT date, COUNT(date) as total
					FROM $table_name
					WHERE feed <> ''
						AND spider = ''
						AND date BETWEEN $yesterday AND $today
					GROUP BY date");

	echo luc_print_dashstats($yesterday, $today, $qry);

	echo "</tr>";

	##################################################################################################
	$qry_s = $wpdb->get_var("
					SELECT date
					FROM $table_name
					LIMIT 1;
					");

	$cstart = strtotime($qry_s);
	$cstart = date("d.m.Y", $cstart);

	print "<tr><td><i>Counter Start</i></td>";
	print "<td colspan='3'><i>$cstart</i></td>\n";

	print "</table><br />\n\n";

	// More Details link
	print "<div class='wrap'><a href='admin.php?page=statpress-visitors/admin/luc_admin.php'>" . __('More details', 'statpress') . " &raquo;</a></div>";
	luc_StatPressV_load_time();
}

function luc_StatPressV_Add_Dashboard_Widget()
{
	wp_add_dashboard_widget('luc_StatPreoussV_Dashboard_Widget', 'StatPressV Overview', 'luc_StatPresV_Dashboard_Widget');
}

function luc_print_dashstats($yesterday, $today, $array)
{
	if (empty($array))
		return "<td>0</td><td>0</td>";
	elseif (count($array) == 1)
	{
		if ($array[0]->date == $yesterday)
			return "<td>" . $array[0]->total . "</td><td>0</td>";
		else
			return "<td>0</td><td>" . $array[0]->total . "</td>";
	}
	else
		return "<td>" . $array[0]->total . "</td><td>" . $array[1]->total . "</td>";
}

?>