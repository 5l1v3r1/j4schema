<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */
	defined( '_JEXEC' ) or die( 'Restricted access' );
	$this->loadHelper('checks');

	$warnings = J4schemaHelperChecks::fullCheck();
	$warn_img = FOFTemplateUtils::parsePath('com_j4schema/images/warning_32.png');

	// check if JCE plugin is installed
	if(is_dir(JPATH_ROOT.'/administrator/components/com_j4schema/jce/j4schema') &&
	  !is_dir(JPATH_ROOT.'/components/com_jce/editor/tiny_mce/plugins/j4schema')){
		$reinstallJCE = true;
	}

	$reinstall = FOFTemplateUtils::parsePath('com_j4schema/images/clear_48.png');
?>
<div class="j4schema">
	<div class="sx w50_" style="font-family:Helvetica;font-size:14px">
		<?php
			if(!J4SCHEMA_PRO) echo JText::_('COM_J4SCHEMA_BACKEND_MANAGE_FREE');
			else{
				$types 	 = FOFModel::getTmpInstance('Types', 'J4SchemaModel')->getTotal();
				$attribs = FOFModel::getTmpInstance('Attributes', 'J4SchemaModel')->getTotal();
				echo JText::sprintf('COM_J4SCHEMA_BACKEND_MANAGE_PRO', $types, $attribs);
			}
		?>
	</div>

	<div class="dx w45_">
		<div class="cpanel">
		<?php if($reinstallJCE):?>
			<div class="icon-wrapper">
				<div class="icon">
					<a href="index.php?option=com_j4schema&view=cpanel&task=reinstalljce">
						<img src="<?php echo $reinstall?>" />
						<span><?php echo JText::_('COM_J4SCHEMA_JCE_REINSTALL')?></span>
					</a>
				</div>
			</div>
		<?php endif;?>
			<div class="icon-wrapper">
				<?php echo LiveUpdate::getIcon(); ?>
			</div>
		</div>
	<?php if($warnings): ?>
		<div style="font-family:Helvetica;font-size:14px;margin-bottom:15px">
			<img class="sx" style="margin-right:5px" src="<?php echo $warn_img; ?>" />
			<?php echo JText::_('COM_J4SCHEMA_WARNINGS')?>
		</div>
	<?php echo implode('', $warnings); ?>

	<?php endif;?>
	</div>

	<div class="clr"></div>
	<p>
		Copyright &copy;2012 <a href="http://www.fabbricabinaria.it">Davide Tampellini</a>. All rights reserved.<br>
		If you use J4Schema, please post a rating and a review at the
		<a target="_blank" href="http://extensions.joomla.org/component/mtree/site-management/seo-a-metadata/meta-data/19961">
			Joomla! Extension Directory
		</a>.
	</p>
</div>