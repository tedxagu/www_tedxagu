<?php
function luc_posts_pages()
{
	$start = microtime(true);
	?>
	<div class='wrap'>
		<form id=postspagesdateForm>
			<table style="width:100%">
				<tr>
					<td>
						<div id="postspagestitle">
							<h2><?php _e(__('Post & Page Summary', 'statpressV')) ?></h2>
						</div>
					</td>
					<td align="right">
						<img src="<?php _e(STATPRESS_V_PLUGIN_URL) ?>/images/ajax-loader.gif" id="postspagesdateLoader" style="display: none;" />
						<input type=text name=postspagesdate id=postspagesdate class='datepicker'>
						<input type="hidden" name="action" value="table_posts_pages" />
					</td>
				</tr>
			</table>
		</form>
		<div id="postspages"> <?php luc_table_posts_pages() ?> </div>
		<?php _e(luc_StatPressV_load_time($start)) ?>
	</div>
	<?php
}

function luc_table_posts_pages()
{	
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;
	// action part of the slug
	$action = "postpage";
	
	// colors
	$visitors_color = "#114477";
	$rss_visitors_color = "#FFF168";
	$pageviews_color = "#3377B6";
	$rss_pageviews_color = "#f38f36";
	$spider_color = "#83b4d8";
	
	$pa = luc_page_posts();
	$permalink = luc_permalink();

	$collect_spider = $StatPressV_Option['StatPressV_Dont_Collect_Spider'];
	$day = (isset ($_POST['postspagesdate']) ? gmdate('Ymd', strtotime($_POST['postspagesdate'])) : gmdate('Ymd', current_time('timestamp') - 86400));
			
	// All WordPress posts and pages
	$sql_posts_pages = "SELECT post_date_gmt,post_title,post_name,post_type 
			FROM $wpdb->posts 
			WHERE post_status = 'publish' 
				AND (post_type = 'page' OR post_type = 'post')
				AND DATE_FORMAT(post_date_gmt, '%Y%m%d') <= $day;";
	
	// Get all posts and pages
	$qry_posts_pages = $wpdb->get_results($sql_posts_pages);	
	$total_posts_pages = $wpdb->num_rows;
					
	$NumberDisplayPost = 100;
	$NA = ceil($total_posts_pages / $NumberDisplayPost);
	$LimitValueArticles = ($pa-1) * $NumberDisplayPost;

	
	foreach ($qry_posts_pages as $p)
	{	$posts[$p->post_name]['post_name'] = $p->post_name;
		$posts[$p->post_name]['post_title'] = $p->post_title;
		$posts[$p->post_name]['post_type'] = $p->post_type;
		$posts[$p->post_name]['visitors'] = NULL;
		$posts[$p->post_name]['visitors_feeds'] = NULL;
		$posts[$p->post_name]['pageviews'] = NULL;
		$posts[$p->post_name]['pageviews_feeds'] = NULL;
		$posts[$p->post_name]['spiders'] = NULL;
	}	
		$posts['page_accueil']['post_name'] = 'page_accueil';
		$posts['page_accueil']['post_title'] = 'Home';
		$posts['page_accueil']['post_type'] = 'page';
		$posts['page_accueil']['visitors'] = NULL;
		$posts['page_accueil']['visitors_feeds'] = NULL;
		$posts['page_accueil']['pageviews'] = NULL;
		$posts['page_accueil']['pageviews_feeds'] = NULL;
		$posts['page_accueil']['spiders'] = NULL;
		
	
	$qry_visitors = requete_day("DISTINCT ip", "urlrequested = ''", "spider = '' AND feed = ''", $day);
	foreach ($qry_visitors as $p)
	{
		$posts[$p->post_name]['visitors'] = $p->total;
		$total_visitors += $p->total;
	}
	
	$qry_visitors_feeds = requete_day("DISTINCT ip", "(urlrequested LIKE '%" . $permalink . "feed%' OR urlrequested LIKE '%" . $permalink . "comment%') ", "spider='' AND feed<>''", $day);
	foreach ($qry_visitors_feeds as $p)
	{
		$posts[$p->post_name]['visitors_feeds'] = $p->total;
		$total_visitors_feeds += $p->total;
	}
	$qry_pageviews = requete_day("ip", "urlrequested = ''", "spider = '' AND feed = ''", $day);
	
	foreach ($qry_pageviews as $p)
	{
		$posts[$p->post_name]['pageviews'] = $p->total;
		$total_pageviews += $p->total;
	}

	$qry_pageviews_feeds = requete_day("ip", "(urlrequested LIKE '%" . $permalink . "feed%' OR urlrequested LIKE '%" . $permalink . "comment%')", " spider='' AND feed<>''", $day);
	foreach ($qry_pageviews_feeds as $p)
	{	
		$posts[$p->post_name]['pageviews_feeds'] = $p->total;
		$total_pageviews_feeds += $p->total;
	}

	$spider = $StatPressV_Option['StatPressV_Dont_Collect_Spider'];
	if ($spider == '')
	{
		$qry_spiders = requete_day("ip", "urlrequested=''", "spider<>'' AND feed=''", $day);
		foreach ($qry_spiders as $p)
		{	
			$posts[$p->post_name]['spiders'] = $p->total;
			$total_spiders += $p->total;
		}
	}

	$total_visitors = $wpdb->get_var("SELECT count(DISTINCT ip) AS total
			FROM $table_name
			WHERE feed=''
				AND spider=''
				AND date = $day ;");
			
	$total_visitors_feeds = $wpdb->get_var("SELECT count(DISTINCT ip) as total
			FROM $table_name
			WHERE feed<>''
				AND spider=''
				AND date = $day ;");
				
	echo "<strong>"; _e("Displaying report for " . gmdate('d M, Y', strtotime($day)) . " (" . $total_posts_pages . " posts/pages)");echo" </strong>";

	luc_print_pp_pa_link(0, 0, $action, $NA, $pa);
	
    // Sort the results by total
	usort($posts, "luc_posts_pages_custom_sort");
	
	echo "<table class='widefat' >
	<thead><tr>
	<th scope='col'>" . __('URL', 'statpressV') . "</th>
	<th scope='col'><div style='background:$visitors_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Visitors', 'statpressV') . "<br /><font size=1></font></th>
	<th scope='col'><div style='background:$rss_visitors_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Visitors Feeds', 'statpressV') . "<br /><font size=1></font></th>
	<th scope='col'><div style='background:$pageviews_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Views', 'statpressV') . "<br /><font size=1></font></th>
	<th scope='col'><div style='background:$rss_pageviews_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Views Feeds', 'statpressV') . "<br /><font size=1></font></th>";
	if ($spider == '')
		echo "<th scope='col'><div style='background:$spider_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Spider', 'statpressV') . "<br /><font size=1></font></th>";
	echo "</tr></thead>";

	echo "<tr>
	<th scope='col'>All URL</th>
	<th scope='col'>" . __($total_visitors, 'statpressV') . "</th>
	<th scope='col'>" . __($total_visitors_feeds, 'statpressV') . "</th>
	<th scope='col'>" . __($total_pageviews, 'statpressV') . "</th>
	<th scope='col'>" . __($total_pageviews_feeds, 'statpressV') . "</th>";
	if ($spider == '')
		echo "<th scope='col'>" . __($total_spiders, 'statpressV') . "</th>
			</tr>";
	$i = 0;

	foreach ($posts as $p)
	{ if (($i >= $LimitValueArticles) and ($i < $LimitValueArticles+$NumberDisplayPost))
		{
		echo "<td>" . ($p[post_type]=='page' ? "[page]&#58;&nbsp;".$p['post_title'] : $p['post_title'])."</td>
			<td>" . $p['visitors']. "</td>
			<td>" . $p['visitors_feeds'] . "</td>
			<td>" . $p['pageviews']. "</td>
			<td>" . $p['pageviews_feeds'] . "</td>";
		if ($spider == '')
			echo "<td>" . $p['spiders'] . "</td>";
		echo "</tr>";
		}
		$i++;
	};
	echo "</table>";
	luc_print_pp_pa_link(0, 0, $action, $NA, $pa);
	
	if (isset ($_POST['postspagesdate']))
		die();
		
}

function requete_day($count,$where_one,$where_two,$day)
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;
	$qry = $wpdb->get_results("SELECT post_name, total
			FROM (
			(SELECT 'page_accueil' AS post_name, count($count) AS total
				FROM $table_name
				WHERE date = $day 
					AND $where_one
					AND $where_two
				GROUP BY post_name)
			UNION ALL
			(SELECT post_name, count($count) AS total
				FROM $wpdb->posts AS p
				JOIN $table_name AS t
				ON t.urlrequested LIKE CONCAT('%',p.post_name,'%')
				WHERE t.date = $day 
					AND p.post_status = 'publish'
					AND (p.post_type = 'page' OR p.post_type = 'post')
					AND $where_two
				GROUP BY p.post_name)
			) req
			GROUP BY post_name");

	return $qry;
}

// Define the custom sort function
function luc_posts_pages_custom_sort($a, $b) 
{
	return $a['visitors'] < $b['visitors'];
}

?>