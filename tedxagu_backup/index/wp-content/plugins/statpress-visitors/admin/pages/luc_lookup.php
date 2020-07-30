<?php
function luc_BanIP_Check($ip)
{
	$lines = file(STATPRESS_V_PLUGIN_PATH . 'def/banips.dat');

	foreach ($lines as $line_num => $record)
	{
		if (strcmp(trim($record), $ip) == 0)
			return true;
	}
	return null;
}

function luc_BanIP_Add($ip)
{
	$fp = fopen(STATPRESS_V_PLUGIN_PATH . 'def/banips.dat', 'a');
	if ($fp)
		fwrite($fp, "\n" . $ip);
	fclose($fp);
}

function luc_BanIP($ip)
{
	if (luc_BanIP_Check($ip))
		echo "IP address " . $ip . " already in ban list<br>";
	else
	{
		luc_BanIP_Add($ip);
		echo "IP address " . $ip . " is now banned!<br>";
	}
}

function luc_BanBot($type, $info)
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;

	if ($type == 'ip')
		$cond = "WHERE ip = '$info'";
	else
		return false; // trap badness

	$qry_u = "UPDATE $table_name
					SET spider = 'Spam Bot'
					$cond ;";

	$wpdb->query($qry_u);

	echo "Existing records in database for IP address " . $info . " now labled as 'Spam Bot' <br>";
}

function luc_print_uas($array)
{
	foreach ($array as $a)
		$ret = $ret . $a->agent . "<br>";

	return $ret;
}

function luc_display_by_IP($ip)
{	
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;

	$qry_s = "SELECT *
				FROM $table_name
				WHERE ip = '$ip'
				ORDER BY id DESC
				";
	$qry = $wpdb->get_results($qry_s);
    $num = $wpdb->num_rows;

	$qry_sa = "SELECT DISTINCT agent
				FROM $table_name
				WHERE ip = '$ip'
				ORDER BY agent ASC ;
				";
	$qrya = $wpdb->get_results($qry_sa);

	if ($_POST['markbot'] == 'Mark as spambot')
		luc_BanBot('ip', $ip);

	if ($_POST['banip'] == 'Ban IP address')
		luc_BanIP($ip);
	$text_OS = (($StatPressV_Option['StatPressV_Dont_Show_OS_name']!='checked') ? true : false);
	$text_browser =(($StatPressV_Option['StatPressV_Dont_Show_Browser_name']!='checked') ? true : false);
	
	$text = "Report for " . $ip . " ";
	?>
	<form method=post>
		<div class='wrap'><table style="width:100%"><tr><td><h2> <?php _e($text) ?> </h2></td>

		<td width=50px align='right'>
			<input type=submit
				name=banip value='Ban IP address' >
		</td>
		</tr>
		</table>
		<table class='widefat'>
			<thead>
				<tr>
				<th scope='col' colspan='2'></th>
			</thead>
			<tbody>
				<tr>
					<td>Records in database:</td>
					<td> <?php _e($num) ?> </td>
				</tr>
				<tr>
					<td>Latest hit:</td>
					<td> <?php _e(luc_hdate($qry[0]->date) . " " . $qry[0]->time) ?> </td>
				</tr>
				<tr>
					<td>First hit:</td>
					<td> <?php _e(luc_hdate($qry[$num - 1]->date) . " " . $qry[$num - 1]->time) ?> </td>
				</tr>
				<tr>
					<td>User agent(s):</td>
					<td> <?php _e(luc_print_uas($qrya)) ?> </td>
				</tr>
			</tbody>
		</table>
	<?php

	$geoip = luc_GeoIP_get_data($ip);
	if ($geoip !== false)
	{
		?>
		<table class='widefat'>
			<thead><tr><th scope='col' colspan='4'>GeoIP Information</th></tr></thead>
			<tbody>
			<tr>
				<td><strong>Country:</strong></td>
				<td> <?php _e($geoip['cn'] . " (" . $geoip['cc'] . ")") ?>
					<IMG style='border:0px;height:16px;' alt='$cn' title='$cn' SRC=' <?php _e(STATPRESS_V_PLUGIN_URL . "/images/domain/" . strtolower($geoip['cc']) . '.png') ?> '></td>
				<td><strong>Continent Code:</strong></td>
				<td> <?php _e($geoip['continent_code']) ?> </td>
			</tr>
			<tr>
				<td><strong>Region:</strong></td>
				<td> <?php _e($geoip['region']) ?> </td>
				<td><strong>Area Code: (USA Only)</strong></td>
				<td> <?php _e($geoip['area_code']) ?> </td>
			</tr>
			<tr>
				<td><strong>City:</strong></td>
				<td> <?php _e($geoip['city']) ?> </td>
				<td><strong>Postal Code: (USA Only)</strong></td>
				<td> <?php _e($geoip['postal_code']) ?> </td>
			</tr>
			<tr>
				<td><strong>Latitude/Longitude</strong></td>
				<td> <a href='http://maps.google.com/maps?q=<?php _e($geoip['latitude'] . "+" . $geoip['longitude']) ?>' target='_blank' title='Lookup latitude/longitude location on Google Maps...'><?php _e($geoip['latitude'] . " " . $geoip['longitude']) ?></a></td>
				<td><strong>Metro Code: (USA Only)</strong></td>
				<td> <?php _e($geoip['metro_code']) ?> </td>
			</tr>
			</tbody>
		</table>
		<?php

	}

	?>
		<table class='widefat'>
			<thead>
				<tr>
				<th scope='col' colspan='6'>URLs Requested</th>
				</tr>
			</thead>
			<thead>
				<tr>
				<th scope='col'>Date</th>
				<th scope='col'>Time</th>
				<th scope='col'>OS</th>
				<th scope='col'>Browser</th>
				<th scope='col'>Agent</th>
				<th scope='col'>Referrer</th>
				<th scope='col'>URL Requested</th>
				</tr>
			</thead>
			<tbody>
	<?php

	foreach ($qry as $rk)
	{
		?>
				<tr>
					<td> <?php _e(luc_hdate($rk->date)) ?> </td>
					<td> <?php _e($rk->time) ?> </td>
					<td> <?php _e(luc_HTML_IMG($rk->os, 'os', $text_OS)) ?> </td>
					<td> <?php  _e(luc_HTML_IMG($rk->browser, 'browser', $text_browser)) ?></td>
					<td> <?php _e($rk->agent) ?> </td>
					<td> <?php _e($rk->referrer) ?> </td>
					<td> <?php _e(luc_post_title_Decode($rk->urlrequested)) ?> </td>
				</tr>
			</tbody>
		<?php
	}
	?>
		</table>
		</div>
	</form>
	<?php
	luc_StatPressV_load_time();
}
?>