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

	<table class="adminlist">
		<thead>
			<tr>
				<th class="w50"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" /></th>
				<th class="w70"><?php echo JHTML::_('grid.sort', 'ID&nbsp;','id_tokens' ,$this->lists->order_Dir, $this->lists->order); ?></th>
				<th class="w250"><?php echo JHTML::_('grid.sort', JText::_('COM_J4SCHEMA_TOKEN_NAME').'&nbsp;','to_name' ,$this->lists->order_Dir, $this->lists->order); ?></th>
				<th class="w150"><?php echo JText::_('COM_J4SCHEMA_INTEGRATION')?></th>
				<th><?php echo JText::_('COM_J4SCHEMA_REPLACE')?></th>
				<th class="w70"><?php echo JText::_('JPUBLISHED')?></th>
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
					$link	  = 'index.php?option=com_j4schema&view=token&id='.$row->id_tokens;
					$checkbox = JHTML::_('grid.id', $i, $row->id_tokens);
			?>
				<tr class="<?php echo "row$k"; ?>">
					<td class="center"><?php echo $checkbox; ?></td>
					<td class="center"><?php echo $row->id_tokens?></td>
					<td class="center"><a href="<?php echo $link;?>"><?php echo $row->to_name ?></a></td>
					<td class="center"><?php echo $row->to_integration?></td>
					<td class="faux_pre"><?php echo $row->to_replace?></td>
					<td class="center"><?php echo JHtml::_('grid.published', $row->enabled, $i)?></td>
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
	<input type="hidden" name="view" value="tokens" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />

</form>