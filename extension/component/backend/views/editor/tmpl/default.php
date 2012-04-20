<?php
/**
 * @package 	Joomla4Schema
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

	defined( '_JEXEC' ) or die( 'Restricted access' );
	JHTML::_('behavior.mootools');

	FOFTemplateUtils::addJS('site://components/com_jce/editor/tiny_mce/tiny_mce_popup.js');

	FOFTemplateUtils::addJS('com_j4schema/js/phpjs/get_html_translation_table.js');
	FOFTemplateUtils::addJS('com_j4schema/js/phpjs/htmlentities.js');
	FOFTemplateUtils::addJS('com_j4schema/js/helper.js');
	FOFTemplateUtils::addJS('com_j4schema/js/editor_helper.js');
	FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.js');
	FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Node.js');
	FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Draw.js');
	FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Hover.js');
	FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Load.js');
	FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.Selection.js');
	FOFTemplateUtils::addJS('com_j4schema/js/tree/Mif.Tree.CookieStorage.js');

	if(J4SCHEMA_PRO) FOFTemplateUtils::addJS('com_j4schema/js/pro.js');

	FOFTemplateUtils::addJS('com_j4schema/js/tree/editor.js');

?>
<div id="j4schema">
	<form autocomplete="off" style="height:450px">
		<div id="textareaHolder" style="height:50px">
			<textarea id="html_code" class="w100_" style="height:auto;">&nbsp;</textarea>
		</div>
		<div class="clr"></div>
		<div id="j4sSettings" style="margin-top:15px">
			<div class="sx w600">
				<div id="tree_container" class="container" style="clear:none;width:250px"></div>
				<div id="attrib_container" class="container" style="clear:none"></div>

				<div class="clr"></div>

				<fieldset class="sx w230" style="margin:5px 10px 0">
					<legend>Type Description</legend>
					<div id="type_descr" class="italic">Click on a type to see its description</div>
				</fieldset>

				<fieldset class="sx w280" style="margin:5px 10px 0">
					<legend>Attribute Description</legend>
					<div id="attrib_descr" class="italic">Click on an attribute to see its description</div>
				</fieldset>
			</div>
			<div class="sx w250">
				<div style="min-height:313px;margin-top:-16px">
					<fieldset style="margin-bottom:5px; padding:5px">
						<legend>List of possible Values</legend>
						<div id="values_descr" class="italic">Click on an attribute to see its possible values</div>
						<div id="dateTime" class="hidden">
							<span id="calendarHolder" class="hidden"><?php echo JHTML::calendar('', 'calendar', 'calendar', '%Y-%m-%d');?></span>
							<span id="timeHolder" class="hidden">
								<input type="text" id="calendarTime" size="5" maxlength="5" value="" /> <span class="italic">HH:mm</span>
							</span>
						</div>
						<div id="values_details">
							<div id="values_choose" class="hidden">
								<input type="radio" name="values" id="propOnly" checked />
									<label for="propOnly" class="pointer">Insert as plain property</label><br />
	<!-- 							<input type="radio" name="values" id="metaProp" />
									<label for="metaProp" class="pointer">Insert as meta tag</label><br /> -->
								<span id="proprPlusTypeHolder">
									<input type="radio" name="values" id="proprPlusType"/>
									<label for="proprPlusType" class="pointer">Insert property AND create a new itemtype block</label>
								</span>
								<div id="values_list" class="hidden" style="margin-left:40px"></div>
							</div>
						</div>
					</fieldset>
					<fieldset style="margin-bottom:5px; padding:5px">
						<legend>Configuration</legend>
							<input type="radio" name="modeInsert" id="property" checked />
								<label for="property" class="pointer">Add type/attribute as element property</label><br />
							<input type="radio" name="modeInsert" id="wrap" />
								<label for="wrap" class="pointer">Wrap selected text with a new <pre class="inline">&lt;div&gt;</pre> or <pre class="inline">&lt;span&gt;</pre></label>
							<div id="newElement" style="margin-left:85px" class="hidden">
								<input type="radio" name="newElement" id="newDiv" />
									<label for="newDiv">Create a new div</label><br />
								<input type="radio" name="newElement" id="newSpan" checked />
									<label for="newSpan">Create a new span</label>
							</div>
					</fieldset>

				</div>

				<div id="warning">&nbsp;</div>

			</div>
		</div>
	</form>
	<div class="sx" style="width:150px">
		<span id="toggleEditor">Expand editor</span>
		<div class="center" style="margin-top:34px">
			<input type="button" class="cancel"  id="remove_schemas" name="remove_schemas" value="Clean all"/>
		</div>
	</div>
	<fieldset class="sx" style="width:600px;min-height:50px">
		<legend>Current selection</legend>
		<div id="currSelection" class="italic"></div>
	</fieldset>
	<div class="dx">
		<input type="button" class="button"  id="add_type" 	 name="add_type" value="Add type"/><br />
		<input type="button" class="button"  id="add_attribute" name="add_attribute" value="Add attribute" /><br />
		<input type="button" class="insert"  id="paste_editor"  name="paste_editor" value="Paste back" />
	</div>
	<div class="clr"></div>
</div>