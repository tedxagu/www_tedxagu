<?php

function luc_options()
{	
	global $wpdb, $StatPressV_Option;
	$table_name = STATPRESS_V_TABLE_NAME;

	if ($_POST['saveit'] == 'yes')
	{	// General Tab
	$StatPressV_Option['StatPressV_Hide_Page_Feeds'] = (isset($_POST['StatPressV_Hide_Page_Feeds']) ? $_POST['StatPressV_Hide_Page_Feeds'] : '');
	$StatPressV_Option['StatPressV_Hide_Page_Referrer'] = (isset($_POST['StatPressV_Hide_Page_Referrer']) ? $_POST['StatPressV_Hide_Page_Referrer'] : '');
	$StatPressV_Option['StatPressV_Hide_Page_SpyBot'] = (isset($_POST['StatPressV_Hide_Page_SpyBot']) ? $_POST['StatPressV_Hide_Page_SpyBot'] : '');
	$StatPressV_Option['StatPressV_Hide_Page_SpyVisitors'] = (isset($_POST['StatPressV_Hide_Page_SpyVisitors']) ? $_POST['StatPressV_Hide_Page_SpyVisitors'] : '');
	$StatPressV_Option['StatPressV_Hide_Page_Stats'] = (isset($_POST['StatPressV_Hide_Page_Stats']) ? $_POST['StatPressV_Hide_Page_Stats'] : '');
	$StatPressV_Option['StatPressV_Hide_Page_Update'] = (isset($_POST['StatPressV_Hide_Page_Update']) ? $_POST['StatPressV_Hide_Page_Update'] : '');
	$StatPressV_Option['StatPressV_Hide_Page_View'] = (isset($_POST['StatPressV_Hide_Page_View']) ? $_POST['StatPressV_Hide_Page_View'] : '');
	$StatPressV_Option['StatPressV_Hide_Page_Visitors'] = (isset($_POST['StatPressV_Hide_Page_Visitors']) ? $_POST['StatPressV_Hide_Page_Visitors'] : '');
	$StatPressV_Option['StatPressV_Hide_Page_Posts_Pages'] = (isset($_POST['StatPressV_Hide_Page_Posts_Pages']) ? $_POST['StatPressV_Hide_Page_Posts_Pages'] : '');

	$StatPressV_Option['StatPressV_Use_Widget_Dashboard'] = (isset($_POST['StatPressV_Use_Widget_Dashboard']) ? $_POST['StatPressV_Use_Widget_Dashboard'] : '');
	$StatPressV_Option['StatPressV_MinPermit'] = (isset($_POST['StatPressV_MinPermit']) ? $_POST['StatPressV_MinPermit'] : $StatPressV_Option['StatPressV_MinPermit']);

	// Data Collection and Retention
	$StatPressV_Option['StatPressV_Dont_Collect_Logged_User'] = (isset($_POST['StatPressV_Dont_Collect_Logged_User']) ? $_POST['StatPressV_Dont_Collect_Logged_User'] : '');
	$StatPressV_Option['StatPressV_Dont_Collect_Logged_User_MinPermit'] = (isset($_POST['StatPressV_Dont_Collect_Logged_User_MinPermit']) ? $_POST['StatPressV_Dont_Collect_Logged_User_MinPermit'] : '');
	$StatPressV_Option['StatPressV_Dont_Collect_Spider'] = (isset($_POST['StatPressV_Dont_Collect_Spider']) ? $_POST['StatPressV_Dont_Collect_Spider'] : '');

	$StatPressV_Option['StatPressV_AutoDelete'] = (isset($_POST['StatPressV_AutoDelete']) ? $_POST['StatPressV_AutoDelete'] : '');
	$StatPressV_Option['StatPressV_AutoDelete_Spider'] = (isset($_POST['StatPressV_AutoDelete_Spider']) ? $_POST['StatPressV_AutoDelete_Spider'] : '');

	// Pages Options
	$StatPressV_Option['StatPressV_Rows_Per_Latest'] = (isset($_POST['StatPressV_Rows_Per_Latest']) ? $_POST['StatPressV_Rows_Per_Latest'] : $StatPressV_Option['StatPressV_Rows_Per_Latest']);
	$StatPressV_Option['StatPressV_Graph_Days'] = (isset($_POST['StatPressV_Graph_Days']) ? $_POST['StatPressV_Graph_Days'] : $StatPressV_Option['StatPressV_Graph_Days']);
	$StatPressV_Option['StatPressV_Graph_Height_Overview'] = (isset($_POST['StatPressV_Graph_Height_Overview']) ? $_POST['StatPressV_Graph_Height_Overview'] : $StatPressV_Option['StatPressV_Graph_Height_Overview']);
	$StatPressV_Option['StatPressV_Graph_Height_Subpages'] = (isset($_POST['StatPressV_Graph_Height_Subpages']) ? $_POST['StatPressV_Graph_Height_Subpages'] : $StatPressV_Option['StatPressV_Graph_Height_Subpages']);
	$StatPressV_Option['StatPressV_Graphs_Per_Page'] = (isset($_POST['StatPressV_Graphs_Per_Page']) ? $_POST['StatPressV_Graphs_Per_Page'] : $StatPressV_Option['StatPressV_Graphs_Per_Page']);
	$StatPressV_Option['StatPressV_SpyVisitor_IP_Per_Page'] = (isset($_POST['StatPressV_SpyVisitor_IP_Per_Page']) ? $_POST['StatPressV_SpyVisitor_IP_Per_Page'] : $StatPressV_Option['StatPressV_SpyVisitor_IP_Per_Page']);
	$StatPressV_Option['StatPressV_SpyVisitor_Visits_Per_IP'] = (isset($_POST['StatPressV_SpyVisitor_Visits_Per_IP']) ? $_POST['StatPressV_SpyVisitor_Visits_Per_IP'] : $StatPressV_Option['StatPressV_SpyVisitor_Visits_Per_IP']);
	$StatPressV_Option['StatPressV_SpyBot_Bots_Per_Page'] = (isset($_POST['StatPressV_SpyBot_Bots_Per_Page']) ? $_POST['StatPressV_SpyBot_Bots_Per_Page'] : $StatPressV_Option['StatPressV_SpyBot_Bots_Per_Page']);
	$StatPressV_Option['StatPressV_SpyBot_Visits_Per_Bot'] = (isset($_POST['StatPressV_SpyBot_Visits_Per_Bot']) ? $_POST['StatPressV_SpyBot_Visits_Per_Bot'] : $StatPressV_Option['StatPressV_SpyBot_Visits_Per_Bot']);
	$StatPressV_Option['StatPressV_Dont_Show_Browser_name']= (isset($_POST['StatPressV_Dont_Show_Browser_name']) ? $_POST['StatPressV_Dont_Show_Browser_name'] : '');
	$StatPressV_Option['StatPressV_Dont_Show_OS_name']= (isset($_POST['StatPressV_Dont_Show_OS_name']) ? $_POST['StatPressV_Dont_Show_OS_name'] : '');
			
	// GeoIP
	$StatPressV_Option['StatPressV_Use_GeoIP'] = (isset($_POST['StatPressV_Use_GeoIP']) ? $_POST['StatPressV_Use_GeoIP'] : '');
	$StatPressV_Option['StatPressV_locate_IP']= (isset($_POST['StatPressV_locate_IP']) ? $_POST['StatPressV_locate_IP'] : $StatPressV_Option['StatPressV_locate_IP']);
		
	update_option('StatPressV_Option', $StatPressV_Option);
	echo "<div class='updated fade'><p>Options saved.</p></div>";
	}

	?>
<div class='wrap'>
	<h2>Options</h2>
	<form method=post>
		<div class="tabbed">
		<!-- The tabs -->
			<ul>
				<li><a href="#tabs-1">General</a></li>
				<li><a href="#tabs-2">Data Collection and Retention</a></li>
				<li><a href="#tabs-3">Pages Options</a></li>
				<li><a href="#tabs-4">GeoIP</a></li>
			</ul>

<!-- tab 1 -->
			<div id="tabs-1">
				<table class=widefat>
					<thead><tr>
						<th scope='col' colspan='2'>Hide pages in admin interface (reduces memory use)</th>
					</tr></thead>
					<tbody>
					<tr>
					<td>
						<input type=checkbox name='StatPressV_Hide_Page_Posts_Pages' id='StatPressV_Hide_Page_Posts_Pages' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Hide_Page_Posts_Pages'] ?> />
							<label for='StatPressV_Hide_Page_Posts_Pages'>Yesterday </label><br />
						<input type=checkbox name='StatPressV_Hide_Page_SpyVisitors' id='StatPressV_Hide_Page_SpyVisitors' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Hide_Page_SpyVisitors'] ?> />
							<label for='StatPressV_Hide_Page_SpyVisitors'>Visitor Spy </label><br />
						<input type=checkbox name='StatPressV_Hide_Page_SpyBot' id='StatPressV_Hide_Page_SpyBot' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Hide_Page_SpyBot'] ?> />
							<label for='StatPressV_Hide_Page_SpyBot'>Bot Spy  </label><br />
						<input type=checkbox name='StatPressV_Hide_Page_Visitors' id='StatPressV_Hide_Page_Visitors' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Hide_Page_Visitors'] ?> />
							<label for='StatPressV_Hide_Page_Visitors'>Visitors </label><br />
						<input type=checkbox name='StatPressV_Hide_Page_View' id='StatPressV_Hide_Page_View' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Hide_Page_View'] ?> />
							<label for='StatPressV_Hide_Page_View'>View  </label><br />
						<input type=checkbox name='StatPressV_Hide_Page_Feeds' id='StatPressV_Hide_Page_Feeds' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Hide_Page_Feeds'] ?> />
							<label for='StatPressV_Hide_Page_Feeds'>Feeds  </label><br />
						<input type=checkbox name='StatPressV_Hide_Page_Referrer' id='StatPressV_Hide_Page_Referrer' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Hide_Page_Referrer'] ?> />
							<label for='StatPressV_Hide_Page_Referrer'>Referrer </label><br />
						<input type=checkbox name='StatPressV_Hide_Page_Stats' id='StatPressV_Hide_Page_Stats' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Hide_Page_Stats'] ?> />
							<label for='StatPressV_Hide_Page_Stats'>Statistics </label><br />
						<input type=checkbox name='StatPressV_Hide_Page_Update' id='StatPressV_Hide_Page_Update' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Hide_Page_Update'] ?> />
							<label for='StatPressV_Hide_Page_Update'>Update database </label><br />
					</td>
					<td>&nbsp;</td>
					</tr>
					</tbody>

					<thead><tr>
						<th scope='col' colspan='2'>Dashboard widget</th>
					</tr></thead>
					<tbody>
					<tr>
					<td>
						<input type=checkbox name='StatPressV_Use_Widget_Dashboard' id='StatPressV_Use_Widget_Dashboard' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Use_Widget_Dashboard'] ?> />
							<label for='StatPressV_Use_Widget_Dashboard'>Enable</label><br />
					</td>
					<td>&nbsp;</td>
					</tr>
					</tbody>


					<thead><tr>
						<th scope='col' colspan='2'>Access Control</th>
					</tr></thead>
					<tbody>
					<tr>
					<td width=200px>Minimum capability to view statistics</td>
						<td>
							<select name="StatPressV_MinPermit">
								<?php
								luc_dropdown_caps($StatPressV_Option['StatPressV_MinPermit']);
								?>
							</select>
							<a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">more info</a>
						</td>
					</tr>
				</table>
			</div>

<!-- tab 2 -->
			<div id="tabs-2">
				<table class=widefat>
					<thead><tr>
						<th scope='col' colspan='2'>Data Collection</th>
					</tr></thead>
					<tbody>
					<tr>
					<td colspan='2'>
						<input type=checkbox name='StatPressV_Dont_Collect_Logged_User' id='StatPressV_Dont_Collect_Logged_User' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Dont_Collect_Logged_User'] ?> >
							<label for='StatPressV_Dont_Collect_Logged_User'>Do not collect data about logged users who have at least these capability</label>
							<select name="StatPressV_Dont_Collect_Logged_User_MinPermit"><?php luc_dropdown_caps($StatPressV_Option['StatPressV_Dont_Collect_Logged_User_MinPermit']); ?></select>
							<a href="http://codex.wordpress.org/Roles_and_Capabilities" target='_blank'>more info</a>
							<br>
						<input type=checkbox name='StatPressV_Dont_Collect_Spider' id='StatPressV_Dont_Collect_Spider' value='checked'
							<?php echo $StatPressV_Option['StatPressV_Dont_Collect_Spider'] ?> >
							<label for='StatPressV_Dont_Collect_Spider'>Do not collect spiders visits</label>
					</td>
					</tr>
					</tbody>

					<thead><tr>
						<th scope='col' colspan='2'>Data Retention</th>
					</tr></thead>
					<tbody>
					<tr>
					<td width=200px>Automatically delete visits older than </td>
					<td>
						<select name="StatPressV_AutoDelete">
							<option value=""
							<?php if ($StatPressV_Option['StatPressV_AutoDelete'] == '')
								print "selected"; ?>>Never delete !</option>
							<option value="1 week"
							<?php if($StatPressV_Option['StatPressV_AutoDelete'] == "1 week") 
								print "selected"; ?>>1 week</option>
							<option value="2 week"
							<?php if($StatPressV_Option['StatPressV_AutoDelete'] == "2 weeks") 
								print "selected"; ?>>2 weeks</option>
							<option value="1 month"
							<?php if ($StatPressV_Option['StatPressV_AutoDelete'] == "1 month")
								print "selected"; ?>>1 month</option>
							<option value="3 months"
							<?php if ($StatPressV_Option['StatPressV_AutoDelete'] == "3 months")
								print "selected"; ?>>3 months</option>
							<option value="6 months"
							<?php if ($StatPressV_Option['StatPressV_AutoDelete'] == "6 months")
								print "selected"; ?>>6 months</option>
							<option value="1 year"
							<?php if ($StatPressV_Option['StatPressV_AutoDelete'] == "1 year")
								print "selected"; ?>>1 year</option>
						</select>
					</td>
					</tr>
				<td width=200px>Automatically delete spider visits older than </td>
				<td>
					<select name="StatPressV_AutoDelete_spider">
						<option value=""
						<?php if($StatPressV_Option['StatPressV_AutoDelete_Spider'] =='' ) 
							print "selected"; ?>>Never delete !</option>
						<option value="1 day"
						<?php if($StatPressV_Option['StatPressV_AutoDelete_Spider'] == "1 day") 
							print "selected"; ?>>1 day</option>
						<option value="1 week"
						<?php if($StatPressV_Option['StatPressV_AutoDelete_Spider'] == "1 week") 
							print "selected"; ?>>1 week</option>
						<option value="1 month"
						<?php if($StatPressV_Option['StatPressV_AutoDelete_Spider'] == "1 month") 
							print "selected"; ?>>1 month</option>
						<option value="1 year"
						<?php if($StatPressV_Option['StatPressV_AutoDelete_Spider'] == "1 year") 
							print "selected"; ?>>1 year</option>
					</select>
				</td>
				</tr>
				</tbody>
				</table>
			</div>

<!-- tab 3 -->
			<div id="tabs-3">
				<table class=widefat>
					<thead><tr>
						<th scope='col' colspan='3'>Main page :</th>
					</tr>
			</thead>
			<tbody>		
		<tr><td colspan='2'> 
		<input type=checkbox name='StatPressV_Dont_Show_OS_name' id='StatPressV_Dont_Show_OS_name' value='checked' 
		<?php echo $StatPressV_Option['StatPressV_Dont_Show_OS_name'] ?> >
		<label for='StatPressV_Dont_Show_OS_name'>Dont show the name of OS</label>
		<br>
		<input type=checkbox name='StatPressV_Dont_Show_Browser_name' id='StatPressV_Dont_Show_Browser_name' value='checked' 
		<?php echo $StatPressV_Option['StatPressV_Dont_Show_Browser_name'] ?> >
		<label for='StatPressV_Dont_Show_Browser_name'>Dont show the name of browsers</label>
		<br>
		</td><td></td></tr>
					<tr>
						 <td >'Latest' Reports : Default number of rows</td>
						 <td>
						 <select name="StatPressV_Rows_Per_Latest">
								<option value="5"
								<?php if ($StatPressV_Option['StatPressV_Rows_Per_Latest'] == 5)
									echo "selected"; ?>>5</option>
								<option value="10"
								<?php if ($StatPressV_Option['StatPressV_Rows_Per_Latest'] == 10)
									echo "selected"; ?>>10</option>
								<option value="25"
								<?php if ($StatPressV_Option['StatPressV_Rows_Per_Latest'] == 25)
									echo "selected"; ?>>25</option>
								<option value="50"
								<?php if ($StatPressV_Option['StatPressV_Rows_Per_Latest'] == 50)
									echo "selected"; ?>>50</option>
						</select>
						</td><td></td>
					</tr>
					</tbody>

					<thead><tr>
						<th scope='col' colspan='2'>Overview, Visitors, Views, Feeds and Referrer graphs</th>
					</tr></thead>
					<tbody>
					<tr>
						 <td width=200px>Days in graphs</td>
						 <td>
						 <select name="StatPressV_Graph_Days">
								<option value="7"
								<?php if ($StatPressV_Option['StatPressV_Graph_Days'] == 7)
									echo "selected"; ?>>7</option>
								<option value="15"
								<?php if ($StatPressV_Option['StatPressV_Graph_Days'] == 15)
									echo "selected"; ?>>15</option>
								<option value="21"
								<?php if ($StatPressV_Option['StatPressV_Graph_Days'] == 21)
									echo "selected"; ?>>21</option>
								<option value="31"
								<?php if ($StatPressV_Option['StatPressV_Graph_Days'] == 31)
									echo "selected"; ?>>31</option>
								<option value="62"
								<?php if ($StatPressV_Option['StatPressV_Graph_Days'] == 62)
									echo "selected"; ?>>62</option>
						</select>
						</td>
					</tr>
					</tbody>

					<thead><tr>
						<th scope='col' colspan='2'>Visitors, views, feeds and referrer graphs</th>
					</tr></thead>
					<tbody>
					<tr>
						<td width=200px>Graphs per page</td>
						<td>
						<select name="StatPressV_Graphs_Per_Page">
							<option value="20"
							<?php if ($StatPressV_Option['StatPressV_Graphs_Per_Page'] == 20)
								echo "selected"; ?>>20</option>
							<option value="50"
							<?php if ($StatPressV_Option['StatPressV_Graphs_Per_Page'] == 50)
								echo "selected"; ?>>50</option>
							<option value="100"
							<?php if ($StatPressV_Option['StatPressV_Graphs_Per_Page'] == 100)
								echo "selected"; ?>>100</option>
						</select>
						</td>
					</tr>
					</tbody>

					<?php
					if (($StatPressV_Option['StatPressV_Hide_Page_SpyVisitors'] <>'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_spyvisitors.php')))
					{ ?>
					<thead><tr>
						<th scope='col' colspan='2'>Visitor Spy</th>
					</tr></thead>
					<tbody>
					<tr>
						<td width=200px>Visitors per page</td>
						<td>
						<select name="StatPressV_SpyVisitor_IP_Per_Page">
							<option value="20"
							<?php if ($StatPressV_Option['StatPressV_SpyVisitor_IP_Per_Page'] == 20)
								print "selected"; ?>>20</option>
							<option value="50"
							<?php if ($StatPressV_Option['StatPressV_SpyVisitor_IP_Per_Page'] == 50)
								print "selected"; ?>>50</option>
							<option value="100"
							<?php if ($StatPressV_Option['StatPressV_SpyVisitor_IP_Per_Page'] == 100)
								print "selected"; ?>>100</option>
						</select>
						</td>
					</tr>
					<tr>
						<td width=200px>Visits per visitor</td>
						<td>
						<select name="StatPressV_SpyVisitor_Visits_Per_IP">
							<option value="20"
							<?php if ($StatPressV_Option['StatPressV_SpyVisitor_Visits_Per_IP'] == 20)
								print "selected"; ?>>20</option>
							<option value="50"
							<?php if ($StatPressV_Option['StatPressV_SpyVisitor_Visits_Per_IP'] == 50)
								print "selected"; ?>>50</option>
							<option value="100"
							<?php if ($StatPressV_Option['StatPressV_SpyVisitor_Visits_Per_IP'] == 100)
								print "selected"; ?>>100</option>
						</select>
						</td>
					</tr>

					</tbody>
					<?php
					}?>

					<?php
					if (($StatPressV_Option['StatPressV_Hide_Page_SpyBot'] <>'checked') AND (file_exists(STATPRESS_V_PLUGIN_PATH . 'admin/pages/luc_spybot.php')))
					{ ?>
					<thead><tr>
						<th scope='col' colspan='2'>Bots Spy</th>
					</tr></thead>
					<tbody>
					<tr>
						<td width=200px>Bots per page</td>
						<td>
						<select name="StatPressV_SpyBot_Bots_Per_Page">
							<option value="5"
							<?php if ($StatPressV_Option['StatPressV_SpyBot_Bots_Per_Page'] == 5)
								print "selected"; ?>>5</option>
							<option value="10"
							<?php if ($StatPressV_Option['StatPressV_SpyBot_Bots_Per_Page'] == 10)
								print "selected"; ?>>10</option>
							<option value="20"
							<?php if ($StatPressV_Option['StatPressV_SpyBot_Bots_Per_Page'] == 20)
								print "selected"; ?>>20</option>
						</select>
						</td>
					</tr>
					<tr>
						<td width=200px>Visits per bot</td>
						<td>
						<select name="StatPressV_SpyBot_Visits_Per_Bot">
							<option value="20"
							<?php if ($StatPressV_Option['StatPressV_SpyBot_Visits_Per_Bot'] == 20)
								print "selected"; ?>>20</option>
							<option value="50"
							<?php if ($StatPressV_Option['StatPressV_SpyBot_Visits_Per_Bot'] == 50)
								print "selected"; ?>>50</option>
							<option value="100"
							<?php if ($StatPressV_Option['StatPressV_SpyBot_Visits_Per_Bot'] == 100)
								print "selected"; ?>>100</option>
						</select>
						</td>
					</tr>
					</tbody>
					<?php
					}?>
				</table>
			</div>

<!-- tab 4 -->
			<div id="tabs-4">
				<?php // Use GeoIP? http://geolite.maxmind.com/download/geoip/api/php/
						if (($StatPressV_Option['StatPressV_Use_GeoIP'] == 'checked')
							AND (!function_exists('geoip_open')))
							include STATPRESS_V_PLUGIN_PATH . 'GeoIP/geoipcity.inc';
						luc_GeoIP(); 
					?>
			</div>
		</div><!-- tabbed -->

		<br>
		<input type=submit value='Save options' class="button-primary">

		<input type=hidden name=saveit value=yes> 
		<input type=hidden name=page value=statpressV>
		<input type=hidden name=statpressV_action value=options>
	</form>
</div>
<?php
	luc_StatPressV_load_time();
}

function luc_GeoIP()
{	global $StatPressV_Option;
	$ipAddress = htmlentities($_SERVER['REMOTE_ADDR']);
	$geoip = luc_GeoIP_get_data($ipAddress);

	if (file_exists(luc_GeoIP_dbname('country')))
	{
		$stat = stat(luc_GeoIP_dbname('country'));
		$dbsize = number_format_i18n($stat['size']);
		$dbmtime = date_i18n('r', $stat['mtime']);
	}
	else 
		{	
		$StatPressV_Option['StatPressV_Use_GeoIP'] = '';
		update_option('StatPressV_Option', $StatPressV_Option);
		}
	if (file_exists(luc_GeoIP_dbname('city')))
	{
		$statcity = stat(luc_GeoIP_dbname('city'));
		$dbcitysize = number_format_i18n($statcity['size']);
		$dbcitymtime = date_i18n('r', $statcity['mtime']);
	}
	else 
		{	
		$StatPressV_Option['StatPressV_Use_GeoIP'] = '';
		update_option('StatPressV_Option', $StatPressV_Option);
		}
	// Draw page
	?>
	<table class='widefat'>
		<thead>
			<tr>
				<th scope='col' colspan='2'>GeoIP Lookup</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					
					<strong>Warning:</strong> GeoIP consumes lot of CPU time, its use is discouraged, do not use it or have permission from your host before activating.
					</br></br><input type=checkbox name='StatPressV_Use_GeoIP' id='StatPressV_Use_GeoIP' value='checked'/>
					<?php echo $StatPressV_Option['StatPressV_Use_GeoIP'] ?> <label for='StatPressV_Use_GeoIP'>Enable (requires MaxMind GeoIP database files to be installed first)</label>
					
					
				</td>
				<td>
					<?php
					if ($StatPressV_Option['StatPressV_Use_GeoIP'] == 'checked')
					{
						$geoipdb = luc_GeoIP_dbname('country');
						if (file_exists($geoipdb))
							echo "<span style='color:green'>Database OK</span>";
						else
						{
							echo "<span style='color:red'>Database NOT found.  Please download and install databases first. Disabling Option! </span>";
							$StatPressV_Option['StatPressV_Use_GeoIP'] = '';
							update_option('StatPressV_Option', $StatPressV_Option);
						}
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<table class="form-table" width="100%">
						<tr>
							<td>
							<input type='button' id='dogeoipdbupdate' value='Download/update database from MaxMind' class='button-secondary'>
							<img src="<?php echo STATPRESS_V_PLUGIN_URL ?>/images/ajax-loader.gif" id="geoipupdatedbLoader" style="display: none;" />
							<br /><br />
							<font size=2><b>WARNING!</b>  Downloading database updates from MaxMind <b>more than once per day</b> will get your <b>IP address banned!</b></font></td>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
		<thead><tr>
						<th scope='col' colspan='2'>Indicate the preferred method to locate the country of visitors :</th>
					</tr>
			</thead>
			<tbody>
			<form method="post" >
   <tr><td>
       <input type="radio" name="StatPressV_locate_IP" value="browser" id="browser" <?php if ($StatPressV_Option['StatPressV_locate_IP']== 'browser')  echo'checked'; ?>
	   /> <label for="browser">Store the country provided by the browser first, otherwise use GeoIP (default, recommended)</label><br />
       <input type="radio" name="StatPressV_locate_IP" value="GeoIP" id="GeoIP" <?php if ($StatPressV_Option['StatPressV_locate_IP']== 'GeoIP') echo'checked'; ?>
	   /> <label for="GeoIP">Always use GeoIP (accuracy 95%)</label><br />
   </td></tr>
</form>
</tbody>
		<thead><tr><th scope='col' colspan='2'>Status</th></tr></thead>
		<tbody>
		<tr>
			<td><strong>StatPressV GeoIP status:</strong></td>
	<?php
	if ($StatPressV_Option['StatPressV_Use_GeoIP'] == 'checked')
		echo "<td><span style='color:green'>Enabled</span></td>";
	else
		echo "<td><span style='color:red'>Disabled" . $geoipdb . "</span></td>";
	?>
		</tbody>
		<thead><tr><th scope='col' colspan='2'>Country database</th></tr></thead>
		<tbody>
		<tr>
			<td><strong>File status:</strong></td>
	<?php
	if (! file_exists(luc_GeoIP_dbname('country')))
		{echo "<td><span style='color:red'>Database NOT found" . $geoipdb . "</span></td></tr>";
		$StatPressV_Option['StatPressV_Use_GeoIP'] = '';
		update_option('StatPressV_Option', $StatPressV_Option);
		}
	else
	{
		echo "<td><span style='color:green'>Country database file exists</span></td>
		</tr>
		<tr><td><strong>File location:</strong></td>
			<td>" . luc_GeoIP_dbname('country') . "</td>
		</tr>
		<tr>
			<td><strong>File date (mtime):</strong></td>
			<td> $dbmtime </td>
		</tr>
		<tr>
			<td><strong>File size:</strong></td>
			<td> $dbsize bytes </td>
		</tr>";
	}
	?>
		</tbody>

		<thead><tr><th scope='col' colspan='2'>City database</th></tr></thead>
		<tbody>
		<tr>
			<td><strong>File status:</strong></td>
	<?php
	if (! file_exists(luc_GeoIP_dbname('city')))
		{echo "<td><span style='color:red'>City database NOT found" . $geoipcitydb . "</span></td>";
		$StatPressV_Option['StatPressV_Use_GeoIP'] = '';
		update_option('StatPressV_Option', $StatPressV_Option);
		}
	else
	{
		echo "<td><span style='color:green'>City database file exists</span></td>
		<tr>
			<td><strong>File location:</strong></td>
			<td>" . luc_GeoIP_dbname('city') . "</td>
		</tr>
		<tr>
			<td><strong>File date (mtime):</strong></td>
			<td> $dbcitymtime </td>
		</tr>
		<tr>
			<td><strong>File size:</strong></td>
			<td> $dbcitysize bytes</td>
		</tr>";
	}
	?>
		</tbody>
	<?php
	if ($geoip !== false)
	{
		echo "
			<table class='widefat'>
				<thead><tr><th scope='col' colspan='4'>Your GeoIP Information ($ipAddress)</th></tr></thead>
				<tbody>
				<tr>
					<td><strong>Country:</strong></td>
					<td>" . $geoip['cn'] . " (" . $geoip['cc'] . ")
						<IMG style='border:0px;height:16px;' alt='$cn' title='$cn' SRC='" . STATPRESS_V_PLUGIN_URL . '/images/domain/' . strtolower($geoip['cc']).'.png' . "'></td>
					<td><strong>Continent Code:</strong></td>
					<td>" . $geoip['continent_code'] . "</td>
				</tr>
				<tr>
					<td><strong>Region:</strong></td>
					<td>" . $geoip['region'] . "</td>
					<td><strong>Area Code (USA Only):</strong></td>
					<td>" . $geoip['area_code'] . "</td>
				</tr>
				<tr>
					<td><strong>City:</strong></td>
					<td>" . $geoip['city'] . "</td>
					<td><strong>Postal Code (USA Only):</strong></td>
					<td>" . $geoip['postal_code'] . "</td>
				</tr>
				<tr>
					<td><strong>Latitude/Longitude:</strong></td>
					<td>" . $geoip['latitude'] . " " . $geoip['longitude'] . "</td>
					<td><strong>Metro Code (USA Only):</strong></td>
					<td>" . $geoip['metro_code'] . "</td>
				</tr>
				</tbody>
			</table>
		";
	}

	?>
	</table>
	<div id='geoipupdatedbResultCountry'></div>
	<div id='geoipupdatedbResultCity'></div>
	<?php
//  End of GeoIP page

}


?>