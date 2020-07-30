<?php
function luc_statistics()
{	
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;

	$querylimit = "LIMIT 10";

	# Top days
	luc_ValueTable("date", "Top days", 5, "", "", "WHERE spider NOT LIKE '%Spam Bot'");

	# O.S.
	luc_ValueTable("os", "O.S.", 10, "", "", "WHERE feed='' AND spider='' AND os<>'' AND agent<>''");

	# Browser
	luc_ValueTable("browser", "Browser", 10, "", "", "WHERE feed='' AND spider='' AND browser<>'' AND agent<>''");

	# Feeds
	luc_ValueTable("feed", "Feeds", 10, "", "", "WHERE feed<>'' AND spider NOT LIKE '%spam bot'");

	# SE
	luc_ValueTable("searchengine", "Search engines", 10, "", "", "WHERE searchengine<>'' AND spider NOT LIKE '%spam bot'");

	# Search terms
	luc_ValueTable("search", "Top search terms", 20, "", "", "WHERE search<>'' AND spider NOT LIKE '%spam bot'");

	# Top referrer
	luc_ValueTable("referrer", "Top referrer", 20, "", "", "WHERE referrer<>'' AND referrer NOT LIKE '%" . get_bloginfo('url') . "%' AND spider NOT LIKE '%spam bot'");

	# Countries
	luc_ValueTable("country", "Countries", 20, "", "", "WHERE country<>'' AND spider='' AND agent<>''");

	# Languages
	luc_ValueTable("language", "Languages", 20, "", "", "WHERE language<>'' AND spider='' AND agent<>''");

	# Spider
	if ($StatPressV_Option['StatPressV_Dont_Collect_Spider'] == '') // chek if collect or not spider
		luc_ValueTable("spider", "Spiders", 10, "", "", "WHERE spider<>'' AND spider NOT LIKE '%spam bot'");

	# Top Pages
	luc_ValueTable("urlrequested", "Top pages", 10, "", "urlrequested", "WHERE feed='' and spider='' AND agent<>''");

	# Top Days - Unique visitors
	luc_ValueTable("date", "Top Days - Unique visitors", 5, "distinct", "ip", "WHERE feed='' and spider='' AND agent<>''"); /* Maddler 04112007: required patching luc_ValueTable */

	# Top Days - Pageviews
	luc_ValueTable("date", "Top Days - Pageviews", 5, "", "urlrequested", "WHERE feed='' AND spider='' AND agent<>''"); /* Maddler 04112007: required patching luc_ValueTable */

	# Top IPs - Pageviews
	luc_ValueTable("ip", "Top IPs - Pageviews", 10, "", "urlrequested", "WHERE feed='' AND spider='' AND agent<>'' "); /* Maddler 04112007: required patching luc_ValueTable */
	luc_StatPressV_load_time();
	}
function luc_ValueTable($fld, $fldtitle, $limit = 0, $param = "", $queryfld = "", $exclude = "")
{
	global $wpdb;
	$table_name = STATPRESS_V_TABLE_NAME;

	if ($queryfld == '')
		$queryfld = $fld;

	echo "<div class='wrap'>
				<table class='widefat'>
					<thead>
						<tr>
						<th scope='col' style='width:400px;'><h2>$fldtitle</h2></th>
						<th scope='col' style='width:100px;'>" . __('Visits', 'statpress') . "</th>
						<th></th></tr>
					</thead>";
	$rks = $wpdb->get_var("SELECT count($param $queryfld) as rks
								FROM $table_name
								$exclude;");
	if ($rks > 0)
	{
		$sql = "SELECT count($param $queryfld) as pageview, $fld
						FROM $table_name
						$exclude
						GROUP BY $fld ORDER BY pageview DESC";
		if ($limit > 0)
			$sql = $sql .
			" LIMIT $limit";

		$qry = $wpdb->get_results($sql . ";");
		$tdwidth = 450;

		// Collects data
		$data = array ();
		foreach ($qry as $rk)
		{
			$pc = round(($rk->pageview * 100 / $rks), 1);
			if ($fld == 'country')
				$rk-> $fld = strtoupper($rk-> $fld);

			if ($fld == 'date')
				$rk-> $fld = luc_hdate($rk-> $fld);

			if ($fld == 'urlrequested')
				$rk-> $fld = luc_post_title_Decode($rk-> $fld);

			$data[substr($rk-> $fld, 0, 50)] = $rk->pageview;
		}
	}

	// Draw table body
	echo "<tbody id='the-list'>";
	if ($rks > 0)
	{ // Chart!
		if ($fld == 'country')
			$chart = luc_GoogleGeo("", "", $data);
		else
			$chart = luc_GoogleChart("", "500x200", $data);

		echo "<tr><td></td><td></td><td rowspan='" . ($limit +2) . "'>$chart</td></tr>";
		foreach ($data as $key => $value)
		{
			echo "<tr>";
			if ($fld == 'ip')
				echo "	<td style='width:500px;overflow: hidden; white-space: nowrap; text-overflow: ellipsis;'>" . luc_create_href($key, 'ip') . "</td>";
			else
				echo "	<td style='width:500px;overflow: hidden; white-space: nowrap; text-overflow: ellipsis;'>" . $key . "</td>";
			echo "	<td style='width:100px;text-align:center;'>" . $value . "</td>";
			echo "</tr>";
		}
	}
	echo "</tbody></table></div><br>";

}

function luc_GoogleChart($title, $size, $data_array)
{
	if (empty ($data_array))
	{
		return '';
	}
	// get hash
	foreach ($data_array as $key => $value)
	{
		$values[] = $value;
		$labels[] = $key;
	}
	$maxValue = max($values);
	$simpleEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$chartData = "s:";
	for ($i = 0; $i < count($values); $i++)
	{
		$currentValue = $values[$i];
		if ($currentValue > -1)
		{
			$chartData .= substr($simpleEncoding, 61 * ($currentValue / $maxValue), 1);
		}
		else
		{
			$chartData .= '_';
		}
	}
	$data = $chartData . "&chxt=y&chxl=0:|0|" . $maxValue;
	return "<img src=http://chart.apis.google.com/chart?chtt=" . urlencode($title) . "&cht=p3&chs=$size&chd=" . $data . "&chl=" . urlencode(implode("|", $labels)) . ">";
}

function luc_GoogleGeo($title, $size, $data_array)
{
	if (empty ($data_array))
	{
		return '';
	}
	// get hash
	foreach ($data_array as $key => $value)
	{
		$values[] = $value;
		$labels[] = $key;
	}
	return "<img src=http://chart.apis.google.com/chart?chtt=" . urlencode($title) . "&cht=t&chtm=world&chs=440x220&chco=eeeeee,FFffcc,cc3300&chd=t:0," . (implode(",", $values)) . "&chld=XX" . (implode("", $labels)) . ">";
}

class GoogChart
{
	// Constants
	const BASE = 'http://chart.apis.google.com/chart?';

	// Variables
	protected $types = array (
		'pie' => 'p',
		'line' => 'lc',
		'sparkline' => 'ls',
		'bar-horizontal' => 'bhg',
		'bar-vertical' => 'bvg',

	);

	protected $type;
	protected $title;
	protected $data = array ();
	protected $size = array ();
	protected $color = array ();
	protected $fill = array ();
	protected $labelsXY = false;
	protected $legend;
	protected $useLegend = true;
	protected $background = 'a,s,ffffff';

	protected $query = array ();

	// debug
	public $debug = array ();

	// Return string
	public function __toString()
	{
		return $this->display();
	}

	/** Create chart
	 */
	protected function display()
	{
		// Create query
		$this->query = array (
			'cht' => $this->types[strtolower($this->type)], // Type
	'chtt' => $this->title, // Title
	'chd' => 't:' . $this->data['values'], // Data
	'chl' => $this->data['names'], // Data labels
	'chdl' => (($this->useLegend) && (is_array($this->legend))) ? implode('|', $this->legend) : null, // Data legend
	'chs' => $this->size[0] . 'x' . $this->size[1], // Size
	'chco' => preg_replace('/[#]+/', '', implode(',', $this->color)), // Color ( Remove # from string )
	'chm' => preg_replace('/[#]+/', '', implode('|', $this->fill)), // Fill ( Remove # from string )
	'chxt' => ($this->labelsXY == true) ? 'x,y' : null, // X & Y axis labels
	'chf' => preg_replace('/[#]+/', '', $this->background), // Background color ( Remove # from string )

		);

		// Return chart
		return $this->img(GoogChart :: BASE .
		http_build_query($this->query), $this->title);
	}

	/** Set attributes
	 */
	public function setChartAttrs($attrs)
	{
		// debug
		$this->debug[] = $attrs;

		foreach ($attrs as $key => $value)
		{
			$this-> {
				"set$key" }
			($value);
		}
	}

	/** Set type
	 */
	protected function setType($type)
	{
		$this->type = $type;
	}

	/** Set title
	 */
	protected function setTitle($title)
	{
		$this->title = $title;
	}

	/** Set data
	 */
	protected function setData($data)
	{
		// Clear any previous data
		unset ($this->data);

		// Check if multiple data
		if (is_array(reset($data)))
		{
			/** Multiple sets of data
			 */
			foreach ($data as $key => $value)
			{
				// Add data values
				$this->data['values'][] = implode(',', $value);

				// Add data names
				$this->data['names'] = implode('|', array_keys($value));
			}
			/** Implode data correctly
			 */
			$this->data['values'] = implode('|', $this->data['values']);
			/** Create legend
			 */
			$this->legend = array_keys($data);
		}
		else
		{
			/** Single set of data
			 */
			// Add data values
			$this->data['values'] = implode(',', $data);

			// Add data names
			$this->data['names'] = implode('|', array_keys($data));
		}

	}

	/** Set legend
	 */
	protected function setLegend($legend)
	{
		$this->useLegend = $legend;
	}

	/** Set size
	 */
	protected function setSize($width, $height = null)
	{
		// check if width contains multiple params
		if (is_array($width))
		{
			$this->size = $width;
		}
		else
		{
			// set each individually
			$this->size[] = $width;
			$this->size[] = $height;
		}
	}

	/** Set color
	 */
	protected function setColor($color)
	{
		$this->color = $color;
	}

	/** Set labels
	 */
	protected function setLabelsXY($labels)
	{
		$this->labelsXY = $labels;
	}

	/** Set fill
	 */
	protected function setFill($fill)
	{
		// Fill must have atleast 4 parameters
		if (count($fill) < 4)
		{
			// Add remaining params
			$count = count($fill);
			for ($i = 0; $i < $count; ++ $i)
				$fill[$i] = 'b,' . $fill[$i] . ',' . $i . ',' . ($i +1) . ',0';
		}

		$this->fill = $fill;
	}

	/** Set background
	 */
	protected function setBackground($background)
	{
		$this->background = 'bg,s,' . $background;
	}

	/** Create img html tag
	 */
	protected function img($url, $alt = null)
	{
		return sprintf('<img src="%s" alt="%s" style="width:%spx;height:%spx;" />', $url, $alt, $this->size[0], $this->size[1]);
	}
}
?>