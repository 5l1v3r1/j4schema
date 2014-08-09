<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

	defined( '_JEXEC' ) or die( 'Restricted access' );

	$warn_image = F0FTemplateUtils::parsePath('com_j4schema/images/warning_32.png');
?>
<div class="">
	<div style="font-family:Helvetica;font-size:14px;margin-bottom:15px">
		<img class="sx" style="margin-right:5px" src="<?php echo $warn_image?>" />
		<?php echo JText::_('COM_J4SCHEMA_WARNINGS')?>
	</div>
<?php echo implode('', $this->warnings); ?>
</div>