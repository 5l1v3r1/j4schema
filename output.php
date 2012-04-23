<?php
	defined('_JEXEC') or die();

	if($success) $jce_icon = 'templates/bluestork/images/admin/tick.png';
	else		 $jce_icon = 'templates/bluestork/images/admin/publish_x.png';
?>
<h2><?php echo JText::_('COM_J4SCHEMA_INSTALLED')?></h2>

<table>
	<tr>
		<td style="width:200px">FrameworkOnFramework Library</td>
		<td><img src="templates/bluestork/images/admin/tick.png" /></td>
	</tr>
	<tr>
		<td>J4Schema component</td>
		<td><img src="templates/bluestork/images/admin/tick.png" /></td>
	</tr>
	<tr>
		<td>J4Schema cleanup plugin</td>
		<td><img src="templates/bluestork/images/admin/tick.png" /></td>
	</tr>
	<tr>
		<td>J4Schema JCE editor plugin</td>
		<td><img src="<?php echo $jce_icon?>" /></td>
	</tr>
</table>

<div>
	<?php echo implode("\n", $html);?>
</div>