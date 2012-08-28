<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

	defined('_JEXEC') or die();
	$this->loadHelper('html');
	$this->loadHelper('filesystem');

	$j4s  = J4schemaHelperFilesystem::treeFolder(JPATH_COMPONENT_ADMINISTRATOR.'/overrides');
	$tmpl = J4schemaHelperFilesystem::treeFolder(JPATH_ROOT.'/templates/'.J4schemaHelperHtml::getFrontendTemplate().'/html');
?>
<form id="adminForm" name="adminForm" action="index.php" method="post">

	<div class="fltlft width-50">
		<fieldset>
			<legend><?php echo JText::_('COM_J4SCHEMA_CUSTOM_TMPL_OVERRIDES')?></legend>

			<div style="margin:10px">
				<?php echo JText::_('COM_J4SCHEMA_OVERRIDES_LIST')?>
			</div>

			<table class="adminlist">
				<thead>
					<th><?php echo JText::_('COM_J4SCHEMA_COMPONENT')?></th>
					<th><?php echo JText::_('COM_J4SCHEMA_VIEW')?></th>
					<th><?php echo JText::_('COM_J4SCHEMA_LAYOUT')?></th>
				</thead>
				<tbody>
				<?php
					$k = 0;
					if(!$j4s) $j4s = array();
					foreach($j4s as $folder)
					{
						$views = array();
						$layouts = array();
			?>
					<tr class="row<?php echo $k?>">
						<td><?php echo $folder['folder']?></td>
						<td>
						<?php
							foreach($folder['children'] as $view):
								if(is_array($view))
								{
									$views[] = $view;
									echo $view['folder'].'<br/>';
								}
								else
								{
									$layouts[] = $view;
								}
							endforeach;
						?>
						</td>
						<td>
						<?php
							if($layouts) $views[] = array('folder' => '', 'children' => $layouts);
							foreach($views as $layout):
								echo '<div><strong>'.$layout['folder'].'</strong></div>';
								echo implode('<br/>', $layout['children']).'<br/><br/>';
							endforeach;
						?>
						</td>
					</tr>
			<?php
						$k = 1 - $k;
					}
				?>
				</tbody>
			</table>
		</fieldset>
	</div>

	<div class="fltrt width-45">
		<fieldset>
			<legend><?php echo JText::_('COM_J4SCHEMA_CURRENT_TMPL_OVERRIDES')?></legend>

			<div style="margin:10px">
				<?php echo JText::_('COM_J4SCHEMA_TMPL_OVERRIDES_LIST')?>
			</div>

			<table class="adminlist">
				<thead>
					<th><?php echo JText::_('COM_J4SCHEMA_COMPONENT')?></th>
					<th><?php echo JText::_('COM_J4SCHEMA_VIEW')?></th>
					<th><?php echo JText::_('COM_J4SCHEMA_LAYOUT')?></th>
				</thead>
				<tbody>
				<?php
					$k = 0;

					if(!$tmpl) $tmpl = array();
					foreach($tmpl as $folder)
					{
						$views = array();
						$layouts = array();
			?>
					<tr class="row<?php echo $k?>">
						<td><?php echo $folder['folder']?></td>
						<td>
						<?php
							foreach($folder['children'] as $view):
								if(is_array($view))
								{
									$views[] = $view;
									echo $view['folder'].'<br/>';
								}
								else
								{
									$layouts[] = $view;
								}
							endforeach;
						?>
						</td>
						<td>
						<?php
							if($layouts) $views[] = array('folder' => '', 'children' => $layouts);
							foreach($views as $layout):
								echo '<div><strong>'.$layout['folder'].'</strong></div>';
								echo implode('<br/>', $layout['children']).'<br/><br/>';
							endforeach;
						?>
						</td>
					</tr>
			<?php
						$k = 1 - $k;
					}
				?>
				</tbody>
			</table>
		</fieldset>
	</div>

	<input type="hidden" name="option" value="com_j4schema" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="overrides" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
</form>