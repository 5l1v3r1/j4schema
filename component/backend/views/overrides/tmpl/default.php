<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

	defined('_JEXEC') or die();
	$this->loadHelper('html');
	$this->loadHelper('filesystem');

	if    (version_compare(JVERSION, '3.0.0', 'ge'))    $version = '3.0';
	elseif(version_compare(JVERSION, '1.6.0', 'ge')) 	$version = '2.5';
	else										     	$version = '1.5';

	$j4s  = J4schemaHelperFilesystem::treeFolder(JPATH_COMPONENT_ADMINISTRATOR.'/overrides/'.$version);
	$tmpl = J4schemaHelperFilesystem::treeFolder(JPATH_ROOT.'/templates/'.J4schemaHelperHtml::getFrontendTemplate().'/html');
?>
<form id="adminForm" name="adminForm" action="index.php" method="post" class="row-fluid">

	<div class="span6">
		<fieldset>
			<legend><?php echo JText::_('COM_J4SCHEMA_CUSTOM_TMPL_OVERRIDES')?></legend>

			<div style="margin:10px">
				<?php echo JText::_('COM_J4SCHEMA_OVERRIDES_LIST')?>
			</div>

			<table class="adminlist table table-striped">
				<thead>
					<th>&nbsp;</th>
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
				<?php
					if($folder['folder'] == 'com_virtuemart' && version_compare(JVERSION, '1.6', 'l')){
						$VM_15 = true;
						$K2    = false;
					} elseif($folder['folder'] == 'com_k2') {
						$VM_15 = false;
						$K2    = true;
					} else {
						$VM_15 = false;
						$K2    = false;
					}
				?>
						<td style="text-align:center">
							<input type="checkbox" class="fltnone" name="folders[]" value="<?php echo $folder['folder']?>" />
						</td>
						<td><?php echo $folder['folder']?></td>
						<td>
						<?php
							if($VM_15){
								echo 'Virtuemart Template';
							}
							elseif($K2){
								echo 'K2 Template';
							}
							else{
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
							}
						?>
						</td>
						<td>
						<?php
							if($VM_15){
								echo JText::_('COM_J4SCHEMA_OVERRIDES_VM_15');
							}
							elseif($K2){
								echo JText::_('COM_J4SCHEMA_OVERRIDES_K2');
							}
							if($layouts) $views[] = array('folder' => '', 'children' => $layouts);
							foreach($views as $layout):
								echo '<div><strong>'.$layout['folder'].'</strong></div>';
								foreach ($layout['children'] as $item)
								{
									if(is_array($item)){
										echo '<em>'.$item['folder'].'</em><br/>';
                                        if(is_array($item['children']))
                                        {
                                            echo '&nbsp;&nbsp;<em>Nested folders</em><br/>';
                                        }
                                        else
                                        {
                                            echo '&nbsp;&nbsp;'.@implode('<br/>&nbsp;&nbsp;',$item['children']).'<br/>';
                                        }
									}
									else{
										echo $item.'<br/>';
									}
								}
								echo '<br/>';
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

	<div class="span6">
		<fieldset>
			<legend><?php echo JText::_('COM_J4SCHEMA_CURRENT_TMPL_OVERRIDES')?></legend>

			<div style="margin:10px">
				<?php echo JText::_('COM_J4SCHEMA_TMPL_OVERRIDES_LIST')?>
			</div>

			<table class="adminlist table table-striped">
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
								foreach ($layout['children'] as $item)
								{
									if(is_array($item)){
										echo '<em>'.$item['folder'].'</em><br/>';
										echo '&nbsp;&nbsp;'.implode('<br/>&nbsp;&nbsp;',$item['children']).'<br/>';
									}
									else{
										echo $item.'<br/>';
									}
								}
								echo '<br/>';
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
	<input type="hidden" name="<?php echo J4SchemaHelperBridge::getToken();?>" value="1" />
</form>