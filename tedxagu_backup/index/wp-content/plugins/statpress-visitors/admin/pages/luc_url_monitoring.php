<?php
function luc_url_monitoring()
{	global $wpdb, $StatPressV_Option;
	$table_name =STATPRESS_V_TABLE_NAME;
	$querylimit = 20;
	$pa = luc_page_posts();
	$action = "urlmonitoring";
	
	// Number of distinct "no author post or page URL"
	$Num = $wpdb->get_var("SELECT COUNT(*)
				FROM $table_name
				WHERE realpost=0 AND (spider ='' OR spider LIKE 'Unknown Spam Bot')
				");	
				
	$NumPage = ceil($Num / $querylimit);

	echo "<div class='wrap'><h2>" . __('URL Monitoring', 'statpress') . "</h2>
	       </br> This page is designed to help you secure your website:<div title='Indeed this page shows all URLs that have access to your website or your blog and who are not posts or pages written by an author of your website.Some are legitimate as /category or the robots like Google. Nevertheless, they are all shown so you can secure your blog or your site by selecting the ones you want to block access to your site.'>Learn more</div>";
	luc_print_pp_pa_link(0,0,$action, $NumPage, $pa);
	$LimitValue = ($pa * $querylimit) - $querylimit;
	?>
	<table class='widefat' >
		<thead>
		<tr>
			<th scope='col'>Date</th>
			<th scope='col'>Time</th>
			<th scope='col'>IP</th>
			<th scope='col'>Country</th>
			<th scope='col' width="30%">URL requested</th>
			<th scope='col' width="30%">Agent</th>
			<th scope='col'>Spider</th>
			<th scope='col'>OS</th>
			<th scope='col'>Browser</th>
		</tr>
		</thead>
		<tbody>
	<?php

	$qry = $wpdb->get_results("SELECT date,time,ip,urlrequested,agent,os,browser,spider,country,realpost
			FROM $table_name
			WHERE realpost=0 AND (spider ='' OR spider LIKE 'Unknown Spam Bot')
			ORDER BY id DESC
			LIMIT $LimitValue, $querylimit;");
			
	foreach ($qry as $rk)
	{
		echo "<tr>
			<td>" .luc_hdate($rk->date). "</td>
			<td>" .$rk->time. "</td>
			<td>" .luc_create_href($rk->ip, 'ip'). "</td>
			<td>" .luc_HTML_IMG($rk->country, 'country', false). "</td>
			<td>" .$rk->urlrequested."</td>
			<td><a href='http://www.google.com/search?q=%22User+Agent%22+" . urlencode($rk->agent) . "' target='_blank' title='Search for User Agent string on Google...'> " . $rk->agent . "</a> </td>
			<td>" .luc_HTML_IMG($rk->spider, 'spider', false). "</td>
			<td>" .luc_HTML_IMG($rk->os, 'os', $text_OS). "</td>
			<td>" .luc_HTML_IMG($rk->browser, 'browser', $text_browser). "</td>";
	}
	?>
		</tbody>
	</table>
	
<?php

echo "</div>";
luc_print_pp_pa_link(0,0,$action, $NumPage, $pa);
luc_StatPressV_load_time($start);
}
?>