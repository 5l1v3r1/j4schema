<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();
?>
<form name="adminForm" id="adminForm" action="index.php" method="post">

	<div style="padding-left:20px;font-size:110%">
		<?php echo JText::_('COM_J4SCHEMA_AUTHOR_CONTRIBUTOR')?>
	</div>
	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th class="w50"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" /></th>
				<th class="w70"><?php echo JHTML::_('grid.sort', 'User ID&nbsp;','at_userid' ,$this->lists->order_Dir, $this->lists->order); ?></th>
				<th class="w150"><?php echo JHTML::_('grid.sort', 'Username&nbsp;','username' ,$this->lists->order_Dir, $this->lists->order); ?></th>
				<th class="w150">Author name</th>
				<th>Profile ID</th>
				<th class="w80"><?php echo JHTML::_('grid.sort', JText::_('COM_J4SCHEMA_NUM_ARTICLES').'&nbsp;','articles' ,$this->lists->order_Dir, $this->lists->order); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<div><?php echo $this->pagination->getListFooter(); ?></div>
				</td>
			</tr>
		</tfoot>

		<tbody>
		<?php
			$k = 0;
			$i = 0;
			if(!$this->items){
				echo '<tr class="row0"><td class="center" colspan="6">'.JText::_('COM_J4SCHEMA_NO_DATA').'</td></tr>';}
			else{

				foreach($this->items as $row):
					$link	  = 'index.php?option=com_j4schema&view=author&id='.$row->id_authors;
					$checkbox = JHTML::_('grid.id', $i, $row->id_authors);
			?>
				<tr class="<?php echo "row$k"; ?>">
					<td class="center"><?php echo $checkbox; ?></td>
					<td class="center"><?php echo $row->at_userid?></td>
					<td class=""><a href="<?php echo $link;?>"><?php echo $row->username ?></a></td>
					<td class="center"><?php echo $row->name?></td>
					<td class="center"><?php echo $row->at_profile?></td>
					<td class="center"><?php echo $row->articles?></td>
				</tr>
				<?php
					$k = 1 - $k;
					$i++;
					endforeach; ?>
				<?php } ?>
			<tbody>
	</table>

	<input type="hidden" name="option" value="com_j4schema" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="authors" />
	<input type="hidden" name="<?php echo J4SchemaHelperBridge::getToken();?>" value="1" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
</form>