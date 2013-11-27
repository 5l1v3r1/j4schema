<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

	defined('_JEXEC') or die();
	JHTML::_('behavior.keepalive');
	$this->loadHelper('select');

	FOFTemplateUtils::addCSS('com_j4schema/css/editor.css');

	FOFTemplateUtils::addJS('com_j4schema/js/phpjs/get_html_translation_table.js');
	FOFTemplateUtils::addJS('com_j4schema/js/phpjs/htmlentities.js');

    $published = 'JPUBLISHED';
    FOFTemplateUtils::addJS('com_j4schema/js/helper.js');
    FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.js');
    FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Node.js');
    FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Draw.js');
    FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Hover.js');
    FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Load.js');
    FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Selection.js');
    FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.CookieStorage.js');


	if(J4SCHEMA_PRO) FOFTemplateUtils::addJS('com_j4schema/js/pro.js');

	FOFTemplateUtils::addJS('com_j4schema/js/token_edit.js');

	$data = $this->item;
?>
<div id="j4schema">
	<form id="adminForm" name="adminForm" action="index.php" method="post" autocomplete="off">

		<div class="fltlft w70_">
			<fieldset>
				<legend><?php echo JText::_('COM_J4SCHEMA_DETAILS')?></legend>
				<label for="to_name" class="main required"><?php echo JText::_('COM_J4SCHEMA_TOKEN_NAME')?></label>
				<input type="text" id="to_name" class="w200" name="to_name" value="<?php echo $data->to_name;?>" />
				<div class="clr"></div>

				<label for="to_integation" class="main required"><?php echo JText::_('COM_J4SCHEMA_INTEGRATION')?></label>
				<?php echo J4schemaHelperSelect::integration('to_integration', $data->to_integration, 'class="w150"')?>
				<div class="clr"></div>

				<label for="to_type" class="main required"><?php echo JText::_('COM_J4SCHEMA_TOKEN_TYPE')?></label>
				<?php echo J4schemaHelperSelect::tokenType('to_type', $data->to_type, 'class="w150"')?>
				<div class="clr"></div>

				<label for="enabled" class="main required"><?php echo JText::_($published)?></label>
				<div style="float:left">
					<?php echo JHTML::_('select.booleanlist', 'enabled', '', $data->enabled)?>
				</div>
				<div class="clr"></div>

				<label for="to_replace" class="main"><?php echo JText::_('COM_J4SCHEMA_REPLACE')?></label>
				<input type="text" id="to_replace" class="input_pre" style="width:500px" name="to_replace" value='<?php echo $data->to_replace;?>' />
				<input type="text" id="dummy_disabled" value="<?php echo JText::_('COM_J4SCHEMA_DUMMY_DISABLED')?>" style="display:none;width:500px" disabled="disabled" />
				<div class="clr"></div>
			</fieldset>
		</div>

		<div class="clr"></div>

		<div class="w100_">
			<div id="j4sSettings" style="margin:15px auto 0;width:860px">
				<div class="sx w600">
					<div id="tree_container" class="container" style="clear:none;width:250px"></div>
					<div id="attrib_container" class="container" style="clear:none"></div>

					<div class="clr"></div>

					<fieldset class="sx w230" style="margin:5px 10px 0">
						<legend><?php echo JText::_('COM_J4SCHEMA_TYPE_DESCR')?></legend>
						<div id="type_descr" class="italic"><?php echo JText::_('COM_J4SCHEMA_TYPE_DESCR_DESCR')?></div>
					</fieldset>

					<fieldset class="sx w280" style="margin:5px 10px 0">
						<legend><?php echo JText::_('COM_J4SCHEMA_ATTR_DESCR')?></legend>
						<div id="attrib_descr" class="italic"><?php echo JText::_('COM_J4SCHEMA_ATTR_DESCR_DESCR')?></div>
					</fieldset>
				</div>
				<div class="sx w250">
					<div style="margin-top:-16px">
						<fieldset style="margin-bottom:5px; padding:5px">
							<legend><?php echo JText::_('COM_J4SCHEMA_VALUE_LIST')?></legend>
							<div id="values_descr" class="italic"><?php echo JText::_('COM_J4SCHEMA_VALUE_LIST_DESCR')?></div>
							<div id="dateTime" class="hidden">
								<span id="calendarHolder" class="hidden"><?php echo JHTML::calendar('', 'calendar', 'calendar', '%Y-%m-%d');?></span>
								<span id="timeHolder" class="hidden">
									<input type="text" id="calendarTime" size="5" maxlength="5" value="" /> <span class="italic">HH:mm</span>
								</span>
							</div>
							<div id="values_details">
								<div id="values_choose" class="hidden">
									<input type="radio" name="values" id="propOnly" checked />
										<label for="propOnly" class="pointer"><?php echo JText::_('COM_J4SCHEMA_INSERT_PLAIN')?></label><br />
										<span id="proprPlusTypeHolder">
										<input type="radio" name="values" id="proprPlusType"/>
										<label for="proprPlusType" class="pointer"><?php echo JText::_('COM_J4SCHEMA_INSERT_NEW')?></label>
									</span>
									<div id="values_list" class="hidden" style="margin-left:40px"></div>
								</div>
							</div>
						</fieldset>
						<fieldset class="hidden" style="margin-bottom:5px; padding:5px">
							<legend><?php echo JText::_('COM_J4SCHEMA_EDITOR_CONFIG')?></legend>
								<input type="radio" name="modeInsert" id="property" checked />
									<label for="property" class="pointer"><?php echo JText::_('COM_J4SCHEMA_ADD_AS_PROPERTY')?></label><br />
								<input type="radio" name="modeInsert" id="wrap" />
									<label for="wrap" class="pointer"><?php echo JText::_('COM_J4SCHEMA_WRAP_PROPERTY')?></label>
								<div id="newElement" style="margin-left:85px" class="hidden">
									<input type="radio" name="newElement" id="newDiv" />
										<label for="newDiv"><?php echo JText::_('COM_J4SCHEMA_CREATE_DIV')?></label><br />
									<input type="radio" name="newElement" id="newSpan" checked />
										<label for="newSpan"><?php echo JText::_('COM_J4SCHEMA_CREATE_SPAN')?></label>
								</div>
						</fieldset>

					</div>

					<div id="warning">&nbsp;</div>

				</div>
				<div class="clr"></div>

				<div>
					<div class="dx">
						<input type="button" class="button"  id="add_type" 	 name="add_type" value="<?php echo JText::_('COM_J4SCHEMA_ADD_TYPE')?>"/><br />
						<input type="button" class="button"  id="add_attribute" name="add_attribute" value="<?php echo JText::_('COM_J4SCHEMA_ADD_ATTR')?>" /><br />
					</div>
					<div class="clr"></div>
				</div>

			</div>
		</div>

		<input type="hidden" name="option" value="com_j4schema" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="token" />
		<input type="hidden" name="id_tokens" value="<?php echo $this->item->id_tokens?>" />
		<input type="hidden" name="<?php echo J4SchemaHelperBridge::getToken();?>" value="1" />
	</form>
</div>