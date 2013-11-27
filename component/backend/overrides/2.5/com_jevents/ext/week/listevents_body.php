<?php
defined('_JEXEC') or die('Restricted access');

$cfg	 = & JEVConfig::getInstance();


$cfg = & JEVConfig::getInstance();
$option = JEV_COM_COMPONENT;
$Itemid = JEVHelper::getItemid();

$compname = JEV_COM_COMPONENT;
$viewname = $this->getViewName();
$viewpath = JURI::root() . "components/$compname/views/".$viewname."/assets";
$viewimages = $viewpath . "/images";

$view =  $this->getViewName();

$this->data = $data = $this->datamodel->getWeekData($this->year, $this->month, $this->day);

// previous and following month names and links
$followingWeek = $this->datamodel->getFollowingWeek($this->year, $this->month, $this->day);
$precedingWeek = $this->datamodel->getPrecedingWeek($this->year, $this->month, $this->day);

?>
<table class="maintable" align="center" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="tableh1" colspan="3">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><h2><?php echo JText::_( 'WEEKLY_VIEW' );?></h2></td>
					<td class="today" align="right"><?php echo  $data['startdate'] . ' - ' . $data['enddate'] ;?></td>
				</tr>
			</table>
	  </td>
	</tr>
		<tr>
			<td  class="previousmonth" align="center" height="22" nowrap="nowrap" valign="middle" width="33%">&nbsp;
<!-- BEGIN previous_month_link_row -->
      	<?php if ($precedingWeek) { echo "<a href='".$precedingWeek."' title='".JText::_("PRECEEDING_Week")."' >"?>
      	<?php echo JText::_("PRECEEDING_Week")."</a>";}?>


<!-- END previous_month_link_row -->
			</td>
			<td  class="currentmonth" style="background-color: rgb(208, 230, 246);" align="center" height="22" nowrap="nowrap" valign="middle">
				<?php echo  $data['startdate'] . ' - ' . $data['enddate'] ;?>
			</td>
			<td  class="nextmonth" align="center" height="22" nowrap="nowrap" valign="middle"  width="33%">
      	<?php if ($followingWeek) {echo "<a href='".$followingWeek."' title='".JText::_("FOLLOWING_Week")."' >"?>
      	<?php echo JText::_("FOLLOWING_Week")."</a>";}?>
			</td>
		</tr>
<?php
for( $d = 0; $d < 7; $d++ ){

	$num_events	= count($data['days'][$d]['rows']);
	if ($num_events==0) continue;

	$day_link = '<a class="ev_link_weekday" href="' . $data['days'][$d]['link'] . '" title="' . JText::_('JEV_CLICK_TOSWITCH_DAY') . '">'
	. JEventsHTML::getDateFormat( $data['days'][$d]['week_year'], $data['days'][$d]['week_month'], $data['days'][$d]['week_day'], 2 ).'</a>'."\n";

	echo '<tr class="tableh2"><td class="tableh2" colspan="3">' . $day_link . '</td></tr>' ;
	echo "<tr>";
	echo '<td class="ev_td_right" colspan="3">' ;

	########################
	# FABBRICABINARIA HACK #
	#       J4Schema       #
	########################
	if ($num_events>0) {
		echo "<ul class='ev_ul'>\n";

		for( $r = 0; $r < $num_events; $r++ ){
			$row = $data['days'][$d]['rows'][$r];

			$listyle = 'style="border-color:'.$row->bgcolor().';"';
			echo "<li {JE_EVENT_WRAPPER} class='ev_td_li' $listyle>\n";

			$j4sTime = date('Y-m-d H:i:s', $row->_unixstarttime);
			echo '<time {JE_EVENT_STARTTIME:'.$j4sTime.'} />';

			echo '<span {JE_EVENT_NAME}>';
			if (!$this->loadedFromTemplate('icalevent.list_row', $row, 0)){
				$this->viewEventRowNew ( $row);
				echo "&nbsp;::&nbsp;";
				$this->viewEventCatRowNew($row);
			}
			echo '</span>';

			$endTime  = date('Y-m-d H:i:s', $row->_unixendtime);
			$duration = $row->_unixendtime - $row->_unixstarttime;

			echo '{JE_EVENT_ENDTIME:'.$endTime.'} ';
			echo '{JE_EVENT_DURATION:P'.$duration.'}';

			echo "</li>\n";
		}
		echo "</ul>\n";
	}
	echo '</td></tr>' . "\n";
} // end for days

?>
</table>