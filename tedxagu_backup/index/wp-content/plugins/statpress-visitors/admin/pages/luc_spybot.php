<?php
function luc_spybot()
{	
	global $wpdb, $StatPressV_Option;
	$action = "spybot";
	$table_name = STATPRESS_V_TABLE_NAME;

	$LIMIT = $StatPressV_Option['StatPressV_SpyBot_Bots_Per_Page'];
	$LIMIT_PROOF = $StatPressV_Option['StatPressV_SpyBot_Visits_Per_Bot'];
	if ($LIMIT == 0)
		$LIMIT = 10;
	if ($LIMIT_PROOF == 0)
		$LIMIT_PROOF = 30;
	$pa = luc_page_posts();
	$LimitValue = ($pa * $LIMIT) - $LIMIT;

	// limit the search 7 days ago
	$day_ago = gmdate('Ymd', current_time('timestamp') - 7 * 86400);
	$MinId = $wpdb->get_var("SELECT min(id) as MinId
				FROM $table_name
				WHERE date > $day_ago;");

	// Number of distinct spiders after $day_ago
	$Num = $wpdb->get_var("SELECT count(distinct spider)
				FROM $table_name
				WHERE spider<>''
					AND id >$MinId;");
	$NA = ceil($Num / $LIMIT);

	echo "<div class='wrap'><h2>" . __('Bot Spy', 'statpress') . "</h2>";

	// selection of spider, group by spider, order by most recently visit (last id in the table)
	$sql = "SELECT *
					FROM $table_name as T1
					JOIN
						(SELECT spider,max(id) as MaxId
							FROM $table_name
							WHERE spider<>''
							GROUP BY spider
							ORDER BY MaxId
							DESC LIMIT $LimitValue, $LIMIT
						) as T2
					ON T1.spider = T2.spider
					WHERE T1.id > $MinId
					ORDER BY MaxId DESC, id DESC;
				";

	$qry = $wpdb->get_results($sql);
	echo '<div align="center">';
	luc_print_pp_pa_link(0, 0, $action, $NA, $pa);
	echo '</div><div align="left">';
?>
<script>
function ttogle(thediv){
if (document.getElementById(thediv).style.display=="inline") {
document.getElementById(thediv).style.display="none"
} else {document.getElementById(thediv).style.display="inline"}
}
</script>
<table class="widefat" id="mainspytab" name="mainspytab">
	<div align='left'>
		<?php

	$spider = "robot";
	$num_row = 0;
	foreach ($qry as $rk)
	{ // Bot Spy
		if ($robot <> $rk->spider)
		{
			echo "<div align='left'>
							<thead>
							<tr><th scope='colgroup' colspan='2'>";
			$img = str_replace(" ", "_", strtolower($rk->spider));
			$img = str_replace('.', '', $img) . ".png";
			$lines = file(STATPRESS_V_PLUGIN_PATH  . '/def/spider.dat');
			foreach ($lines as $line_num => $spider) //seeks the tooltip corresponding to the photo
			{
				list ($title, $id) = explode("|", $spider);
				if ($title == $rk->spider)
					break; // break, the tooltip ($title) is found
			}
			echo "<IMG style='border:0px;height:16px;align:left;' alt='" . $title . "' title='" . $title . "' SRC='" . STATPRESS_V_PLUGIN_URL . '/images/spider/' . $img . "'>
									<span style='color:#006dca;cursor:pointer;border-bottom:1px dotted #AFD5F9;font-size:8pt;' onClick=ttogle('" . $img . "');>(more information)</span>
									<div id='" . $img . "' name='" . $img . "'><br /><small>" . $rk->ip . "</small><br><small>" . $rk->agent . "<br /></small></div>
									<script>document.getElementById('" . $img . "').style.display='none';</script>
									</th></tr></thead><tbody>
									<tr><td>" . luc_hdate($rk->date) . " " . $rk->time . "</td>
									<td>".((isset($rk->post_title)) ? $rk->post_title : luc_post_title_Decode(urldecode($rk->urlrequested)))."</td></tr>";
										
			$robot = $rk->spider;
			$num_row = 1;
		}

		else
			if ($num_row < $LIMIT_PROOF)
			{
				echo "<tr>
						<td>" . luc_hdate($rk->date) . " " . $rk->time . "</td>
						<td>".((isset($rk->post_title)) ? $rk->post_title : luc_post_title_Decode(urldecode($rk->urlrequested)))."</td></tr>";
				$num_row += 1;
			}
		echo "</div></td></tr>\n";
	}
	echo "</tbody></table>";
	luc_print_pp_pa_link(0, 0, $action, $NA, $pa);
	echo "</div>";
	luc_StatPressV_load_time();
}
?>