<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

	defined('_JEXEC') or die();
	$this->loadHelper('select');

	$data = $this->item;
?>
<div id="j4schema">
	<form id="adminForm" action="index.php" method="post">

		<div class="fltlft width-70">
			<fieldset>
				<legend><?php echo JText::_('COM_J4SCHEMA_DETAILS')?></legend>
				<label for="to_name" class="main required"><?php echo JText::_('COM_J4SCHEMA_TOKEN_NAME')?></label>
				<input type="text" id="to_name" class="w200" name="to_name" value="<?php echo $data->to_name;?>" />
				<div class="clr"></div>

				<label for="to_integation" class="main required"><?php echo JText::_('COM_J4SCHEMA_INTEGRATION')?></label>
				<?php echo J4schemaHelperSelect::integration('to_integration', $data->to_integration, 'class="w150"')?>
				<div class="clr"></div>

				<label for="enabled" class="main required"><?php echo JText::_('JPUBLISHED')?></label>
				<?php echo JHTML::_('select.booleanlist', 'enabled', '', $data->enabled)?>
				<div class="clr"></div>

				<label for="to_replace" class="main"><?php echo JText::_('COM_J4SCHEMA_REPLACE')?></label>
				<input type="text" id="to_replace" class="w500 input_pre" name="to_replace" value="<?php echo $data->to_replace;?>" />
				<div class="clr"></div>
			</fieldset>
		</div>

		<input type="hidden" name="option" value="com_j4schema" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="token" />
		<input type="hidden" name="id_tokens" value="<?php echo $this->item->id_tokens?>" />
		<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
	</form>
</div>