<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

	defined( '_JEXEC' ) or die( 'Restricted access' );
	J4SchemaHelperBridge::mootools();

	F0FTemplateUtils::addCSS('com_j4schema/css/editor.css');

	F0FTemplateUtils::addJS('site://components/com_jce/editor/tiny_mce/tiny_mce_popup.js');

	F0FTemplateUtils::addJS('com_j4schema/js/phpjs/get_html_translation_table.js');
	F0FTemplateUtils::addJS('com_j4schema/js/phpjs/htmlentities.js');

    $published = 'JPUBLISHED';
    F0FTemplateUtils::addJS('com_j4schema/js/helper.js');
    F0FTemplateUtils::addJS('com_j4schema/js/editor_helper.js');
    F0FTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.js');
    F0FTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Node.js');
    F0FTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Draw.js');
    F0FTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Hover.js');
    F0FTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Load.js');
    F0FTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Selection.js');
    F0FTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.CookieStorage.js');

	if(J4SCHEMA_PRO) F0FTemplateUtils::addJS('com_j4schema/js/pro.js');

	F0FTemplateUtils::addJS('com_j4schema/js/tree/editor.js');

?>
<div id="j4schema" style="padding:10px;">
	<form autocomplete="off" style="margin-bottom:10px">
		<div id="textareaHolder">
			<textarea id="html_code" style="height:50px;width:98%">&nbsp;</textarea>
		</div>
		<div class="clr"></div>
		<div id="j4sSettings" style="margin-top:15px">
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
	<!-- 							<input type="radio" name="values" id="metaProp" />
									<label for="metaProp" class="pointer">Insert as meta tag</label><br /> -->
								<span id="proprPlusTypeHolder">
									<input type="radio" name="values" id="proprPlusType"/>
									<label for="proprPlusType" class="pointer"><?php echo JText::_('COM_J4SCHEMA_INSERT_NEW')?></label>
								</span>
								<div id="values_list" class="hidden" style="margin-left:40px"></div>
							</div>
						</div>
					</fieldset>
					<fieldset style="margin-bottom:5px; padding:5px">
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
		</div>
	</form>
	<div>
		<div class="sx" style="width:150px">
			<span id="toggleEditor"><?php echo JText::_('COM_J4SCHEMA_EXPAND_EDITOR')?></span>
			<div class="center" style="margin-top:34px">
				<input type="button" class="cancel"  id="remove_schemas" name="remove_schemas" value="<?php echo JText::_('COM_J4SCHEMA_CLEAN_ALL')?>"/>
			</div>
		</div>
		<fieldset class="sx" style="width:600px;min-height:50px;border:1px solid #C8C8C8">
			<legend><?php echo JText::_('COM_J4SCHEMA_CURRENT_SEL')?></legend>
			<div id="currSelection" class="italic"></div>
		</fieldset>
		<div class="dx">
			<input type="button" class="button"  id="add_type" 	 name="add_type" value="<?php echo JText::_('COM_J4SCHEMA_ADD_TYPE')?>"/><br />
			<input type="button" class="button"  id="add_attribute" name="add_attribute" value="<?php echo JText::_('COM_J4SCHEMA_ADD_ATTR')?>" /><br />
			<input type="button" class="insert"  id="paste_editor"  name="paste_editor" value="<?php echo JText::_('COM_J4SCHEMA_PASTE_BACK')?>" />
		</div>
		<div class="clr"></div>
	</div>
</div>