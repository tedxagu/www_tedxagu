<?php
function luc_update_Browsers($text, $date_from, $date_to)
{	
	echo "<thead><tr><th scope='col' colspan='2'>Updating Browsers</th></tr></thead>
			<tbody>
			<tr>";

	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	
	$wpdb->query("UPDATE $table_name
					SET browser = ''
					WHERE date BETWEEN $date_from AND $date_to");
	$lines = file(STATPRESS_V_PLUGIN_PATH . 'def/browser.dat');
	$lines = (array) $lines;
	$lines = array_reverse($lines); // each lines of $lines is read, then we must begin the update in reverse of function luc_StatAppend() do.
	foreach ($lines as $line_num => $browser)
	{
		list ($nome, $id) = explode("|", $browser);
		$qry = "UPDATE $table_name
						SET browser = '$nome'
						WHERE spider =''
							AND date BETWEEN $date_from AND $date_to
							AND replace(agent,' ','') LIKE '%" . $id . "%'";
		$wpdb->query($qry);
	}

	luc_update_footer($wpdb->num_queries, $date_to, $date_from);

	$text = $text . " Browsers";
	return $text;
}

function luc_update_OS($text, $date_from, $date_to)
{	
	echo "<thead><tr><th scope='col' colspan='2'>Updating Operating Systems</th></tr></thead>
				<tbody>
				<tr>";

	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;

	$wpdb->query("UPDATE $table_name
						SET os = ''
						WHERE date BETWEEN $date_from AND $date_to");
	$lines = file(STATPRESS_V_PLUGIN_PATH . 'def/os.dat');
	$lines = (array) $lines;
	$lines = array_reverse($lines);
	foreach ($lines as $line_num => $os)
	{
		list ($nome_os, $id_os) = explode("|", $os);
		$qry = "UPDATE $table_name
						SET os = '$nome_os'
						WHERE spider =''
							AND date BETWEEN $date_from AND $date_to
							AND replace(agent,' ','') LIKE '%" . $id_os . "%'";
		$wpdb->query($qry);
	}

	luc_update_footer($wpdb->num_queries, $date_to, $date_from);

	if (isset ($text))
		$text .= ',';
	$text = $text . " OS";
	return $text;
}

function luc_update_SearchEngine($text, $date_from, $date_to)
{	
	echo "<thead><tr><th scope='col' colspan='2'>Updating Search Engines</th></tr></thead>
				<tbody>
				<tr>";

	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;

	$wpdb->query("UPDATE $table_name
					SET searchengine = '', search=''
					WHERE date BETWEEN $date_from AND $date_to");
	$qry = $wpdb->get_results("SELECT id, referrer
				FROM $table_name
				WHERE referrer <> ''
					AND feed =''
					AND date BETWEEN $date_from AND $date_to ");
	foreach ($qry as $rk)
	{
		list ($searchengine, $search_phrase) = explode("|", luc_GetSE($rk->referrer));
		if ($searchengine <> '')
		{
			$qry = "UPDATE $table_name
					SET searchengine = '$searchengine', search = '" . addslashes($search_phrase) . "'
					WHERE id= '".$rk->id."'";
			$wpdb->query($qry);
		}
	}

	luc_update_footer($wpdb->num_queries, $date_to, $date_from);

	if (isset ($text))
		$text .= ',';
	$text = $text . " Searchs engines";
	return $text;
}

function luc_update_Spiders($text, $date_from, $date_to)
{	
	echo "<thead><tr><th scope='col' colspan='2'>Updating Spiders</th></tr></thead>
				<tbody>
				<tr>";

	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;

	$wpdb->query("UPDATE $table_name
					SET spider = ''
					WHERE date BETWEEN $date_from
						AND $date_to;");
	$lines = file(STATPRESS_V_PLUGIN_PATH . 'def/spider.dat');
	$lines = (array) $lines;
	$lines = array_reverse($lines);
	foreach ($lines as $line_num => $spider)
	{
		list ($nome, $id) = explode("|", $spider);
		$qry = "UPDATE $table_name
				SET spider = '$nome', browser = '', os = ''
				WHERE date BETWEEN $date_from
					AND $date_to
					AND replace(agent,' ','') LIKE '%" . $id . "%' ;";
		$wpdb->query($qry);
	}

	luc_update_footer($wpdb->num_queries, $date_to, $date_from);

	if (isset ($text))
		$text .= ',';
	$text = $text . " Spiders";
	return $text;
}

function luc_update_GeoIP($text, $date_from, $date_to)
{	
	echo "<thead><tr><th scope='col' colspan='2'>Updating GeoIP country information</th></tr></thead>
				<tbody>
				<tr>";

	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;

	$gi = geoip_open(luc_GeoIP_dbname('country'), GEOIP_STANDARD);
	$qry_s = "SELECT ip
				FROM $table_name
				WHERE date BETWEEN $date_from
					AND $date_to
				GROUP BY ip;";
	$qry = $wpdb->get_results($qry_s);

	foreach ($qry as $rk)
	{
		$ipAddress = $rk->ip;
		$country = geoip_country_code_by_addr($gi, $ipAddress);

		$qry_u = "UPDATE $table_name
					SET country = '$country'
					WHERE date BETWEEN $date_from
						AND $date_to
						AND ip = '$ipAddress' ;";
		$wpdb->query($qry_u);
	}

	luc_update_footer($wpdb->num_queries, $date_to, $date_from);

	if (isset ($text))
		$text .= ',';
	$text = $text . " GeoIP";
	return $text;
}

function luc_update_footer($queries, $date_to, $date_from)
{
	echo "<td>Update status:</td><td>[OK]</td></tr>";
	echo "<tr><td>Date range:</td><td>" . gmdate('d M, Y', strtotime($date_from)) . "  to  " . gmdate('d M, Y', strtotime($date_to)) . "</td></tr>";
	echo "<tr><td>Number of SQL queries:</td><td>" . get_num_queries() . "</td></tr>";
	echo "<tr><td>Time taken:</td><td>" . timer_stop(0,2) . " seconds</td></tr>";
	echo "</tbody>";
}

function luc_update()
{	
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;

	$action = "update";

if ($_POST['updatenow'] == 'Update Now')
	{
		$date_from = gmdate('Ymd', strtotime($_POST['datefrom']));
		$date_to = gmdate('Ymd', strtotime($_POST['dateto']));

		if ($date_to < $date_from)
		{
			$date = $date_from;
			$date_from = $date_to;
			$date_to = $date;
		}

		$wpdb->flush();
		$text = "";

		echo "<table class='widefat'>";

		foreach ($_POST['update'] as $post)
		{
			if ($post == 'Browsers')
				$text=luc_update_Browsers($text, $date_from, $date_to);

			if ($post == 'OS')
				$text=luc_update_OS($text, $date_from, $date_to);

			if ($post == 'SearchEngines')
				$text=luc_update_SearchEngine($text, $date_from, $date_to);

			if (($post == 'Spiders') AND ($StatPressV_Option['StatPressV_Dont_Collect_Spider'] == ''))
				$text=luc_update_Spiders($text, $date_from, $date_to);

			if ($post == 'GeoIP')
				$text=luc_update_GeoIP($text, $date_from, $date_to);
		}
		//		echo "</tbody>";
	}

	$up = "Update StatPress Visitors database from definition files";
	

	echo "<div class='wrap'><h2>" . $up . "</h2>";
?>
<script>
function toggle(source) {
	checkboxes = document.getElementsByName('update[]');
	for(var i in checkboxes)
		checkboxes[i].checked = source.checked;
}
</script>

<form method=post>
	<table class='widefat'>
		<thead>
			<tr>
			<th>Select definitions to update</th>
			</tr>
		</thead>
		<tbody>
			<tr><td>
				<input type="checkbox" name="update[]" id="Browsers" value="Browsers">
					<label for="Browsers">Browsers</label>
				<br>
				<input type="checkbox" name="update[]" id="OS" value="OS">
					<label for="OS">OS</label>
				<br>
				<input type="checkbox" name="update[]" id="SearchEngines" value="SearchEngines">
					<label for="SearchEngines">Search Engines</label>
				<?php if ($StatPressV_Option['StatPressV_Dont_Collect_Spider'] =='')	
				echo '<br>
				<input type="checkbox" name="update[]" id="Spiders" value="Spiders">
					<label for="Spiders">Spiders and Spambots</label>';
					?>
				<?php if ($StatPressV_Option['StatPressV_Use_GeoIP'] == 'checked')	
				echo '<br>
				<input type="checkbox" name="update[]" id="GeoIP" value="GeoIP">
						<label for="GeoIP">GeoIP country information</label>';
				?>
				<br><br><input type="checkbox" onClick="toggle(this)" /> Select All
			</td></tr>
			<tr><td>
				Update records from
					<input type=text name=datefrom value='<?php echo $date_from; ?>' class='datepicker'>
				to
					<input type=text name=dateto value='<?php echo $date_to; ?>' class='datepicker'>
			</td></tr>
			<tr><td>
					<input type=submit
						name=updatenow value='Update Now' class='button-primary'>
			</td></tr>
		</tbody>
	</table>
</form>
<?php
	luc_StatPressV_load_time();
}
?>