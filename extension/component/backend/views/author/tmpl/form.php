<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();
?>
<div id="j4schema">
	<form id="adminForm" name="adminForm" action="index.php" method="post" autocomplete="off">
		<div class="fltlft" style="width:70%">
			<fieldset>
				<legend style="border:none;width:auto;padding:0 10px"><?php echo JText::_('COM_J4SCHEMA_DETAILS')?></legend>

				<label for="at_userid" class="main required"><?php echo JText::_('COM_J4SCHEMA_AUTHOR')?></label>
				<?php echo JHTML::_('list.users', 'at_userid', $this->item->at_userid)?>
				<div class="clr"></div>

				<label for="at_profile" class="main required"><?php echo JText::_('COM_J4SCHEMA_AUTHOR_PROFILE')?></label>
				<input type="text" id="at_profile" class="w200" name="at_profile" value="<?php echo $this->item->at_profile;?>" />
				<div class="clr"></div>
			</fieldset>
		</div>
		<input type="hidden" name="option" value="com_j4schema" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="author" />
		<input type="hidden" name="id_authors" value="<?php echo $this->item->id_authors?>" />
		<input type="hidden" name="<?php echo J4SchemaHelperBridge::getToken();?>" value="1" />
	</form>
</div>