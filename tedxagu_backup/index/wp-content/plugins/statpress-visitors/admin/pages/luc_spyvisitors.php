<?php
function luc_spyvisitors()
{	
	global $wpdb;
	global $StatPressV_Option;
	$action = "spyvisitors";
	$table_name = STATPRESS_V_TABLE_NAME;
	// number of IP or bot by page
	$LIMIT = ($StatPressV_Option['StatPressV_SpyVisitor_IP_Per_Page'] ? $StatPressV_Option['StatPressV_SpyVisitor_IP_Per_Page'] : 20);
	$LIMIT_PROOF = ($StatPressV_Option['StatPressV_SpyVisitor_Visits_Per_IP'] ? $StatPressV_Option['StatPressV_SpyVisitor_Visits_Per_IP'] : 20);
	
	$pp = luc_page_periode();

	// Number of distinct ip (unique visitors)
	$NumIP = $wpdb->get_var("SELECT count(distinct ip)
									FROM $table_name
									WHERE spider='' ;");
								
	$NP = ceil($NumIP / $LIMIT);
	$LimitValue = ($pp * $LIMIT) - $LIMIT;
	
	$sql = "SELECT *
					FROM $table_name as T1
					JOIN
						(SELECT max(id) as MaxId,ip
							FROM $table_name
							WHERE spider=''
							GROUP BY ip
							ORDER BY MaxId DESC LIMIT $LimitValue, $LIMIT
						) as T2
					ON T1.ip = T2.ip
					ORDER BY MaxId DESC, id DESC;
				";
	$qry = $wpdb->get_results($sql);

	if ($StatPressV_Option['StatPressV_Use_GeoIP'] == 'checked' & function_exists('geoip_open'))
	{ // Open the database to read and save info
		if (file_exists(luc_GeoIP_dbname('city')))
		{
			$gic = geoip_open(luc_GeoIP_dbname('city'), GEOIP_STANDARD);
			$geoip_isok = true;
		}
	}

	echo "<div class='wrap'><h2>" . __('Visitor Spy', 'statpress') . "</h2>";
?>
<script>
	function ttogle(thediv){
	if (document.getElementById(thediv).style.display=="inline") {
	document.getElementById(thediv).style.display="none"
	} else {document.getElementById(thediv).style.display="inline"}
	}
</script>
<?php

	$MaxId = 0;
	$num_row = 0;
	// Add pagination
	luc_insert_pagination_options("spyvisitors", $NumIP, $LIMIT);
			
	luc_print_pp_link($NP, $pp, $action);

	echo '<table class="widefat" id="mainspytab" name="mainspytab" width="99%" border="0" cellspacing="0" cellpadding="4">';
	foreach ($qry as $rk)
	{ // Visitor Spy
		if ($MaxId <> $rk->MaxId) //this is the first time these ip appear, print informations
		{
			if ($geoip_isok === true)
				$gir = GeoIP_record_by_addr($gic, $rk->ip);
			echo "<thead><tr><th scope='colgroup' colspan='2'>";

			if ($rk->country <> '')
				echo "HTTP country " . luc_HTML_IMG($rk->country, 'country', false);
			else
					echo "Hostip country <IMG SRC='http://api.hostip.info/flag.php?ip=" . $rk->ip . "' border=0 width=18 height=12>  ";

			if ($geoip_isok === true)
				$lookupsvc = "GeoIP details";
			else
				$lookupsvc = "Hostip details";

			echo "	<strong><span><font size='2' color='#7b7b7b'> " . $rk->ip . " </font></span></strong>
					<span style='color:#006dca;cursor:pointer;border-bottom:1px dotted #AFD5F9;font-size:8pt;'
						onClick=ttogle('" . $rk->ip . "');>" . $lookupsvc . "</span></div>
					<div id='" . $rk->ip . "' name='" . $rk->ip . "'>";

			if ($geoip_isok === true)
				echo "	<small><br>
							Country: " . utf8_encode($gir->country_name) . " (" . $gir->country_code . ")<br>
							City: " . utf8_encode($gir->city) . "<br>
							Latitude/Longitude: <a href='http://maps.google.com/maps?q=" . $gir->latitude . "+" . $gir->longitude . "' target='_blank' title='Lookup latitude/longitude location on Google Maps...'>" . $gir->latitude . " " . $gir->longitude . "</a>
						</small>";
			else
				echo "	<iframe style='overflow:hide;border:0px;width:100%;height:35px;font-family:helvetica;paddng:0;'
							scrolling='no' marginwidth=0 marginheight=0 src=http://api.hostip.info/get_html.php?ip=" . $rk->ip . ">
						</iframe>";

			echo "	<small>
						<br>" . $rk->os . "
						<br>" . $rk->browser . "
						<br>" . gethostbyaddr($rk->ip) . "
						<br>" . $rk->agent . "
					</small></div></th></tr></thead><tbody>
					<script> document.getElementById('" . $rk->ip . "').style.display='none';</script>
					<tr>
					<td>" . luc_hdate($rk->date) . " " . $rk->time . "</td>
					<td>" . ((isset($rk->post_title)) ? $rk->post_title : luc_post_title_Decode(urldecode($rk->urlrequested))) . "";

			if ($rk->searchengine <> '')
				echo "<br><small>arrived from <b>" . $rk->searchengine . "</b> searching <a target='_blank' href='" . $rk->referrer . "' >" . urldecode($rk->search) . "</a></small>";
			elseif ($rk->referrer <> '' && strpos($rk->referrer, $home) === false)
					echo "<br><small>arrived from <a target='_blank' href='" . $rk->referrer . "' >" . $rk->referrer . "</a></small>";

			echo "</div></td></tr>\n";
			$MaxId = $rk->MaxId;
			$num_row = 1;
		}
		elseif ($num_row < $LIMIT_PROOF)
		{
			echo "<tr><td>" . luc_hdate($rk->date) . " " . $rk->time . "</td>
						<td>" .((isset($rk->post_title)) ? $rk->post_title : luc_post_title_Decode(urldecode($rk->urlrequested))). "";
			if ($rk->searchengine <> '')
				echo "<br><small>arrived from <b>" . $rk->searchengine . "</b> searching <a target='_blank' href='" . $rk->referrer . "' >" . urldecode($rk->search) . "</a></small>";
			elseif ($rk->referrer <> '' && strpos($rk->referrer, $home) === false)
					echo "<br><small>arrived from <a target='_blank' href='" . $rk->referrer . "' >" . $rk->referrer . "</a></small>";

			$num_row += 1;
			echo "</td></tr></tbody>";
		}
	}
	echo "</div></td></tr>\n</table>";
	luc_print_pp_link($NP, $pp, $action);
	echo "</div>";
	luc_StatPressV_load_time();
}
?>