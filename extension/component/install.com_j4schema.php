<?php
/**
 * @package    	J4Schema
 * @author     	Davide Tampellini
 * @copyright 	Copyright (c)2011-2012 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

if(function_exists('xdebug_break')) xdebug_break();
$installation_queue = array(
	// modules => { (folder) => { (module) => { (position), (published) } }* }*
	'modules' => array(
		'admin' => array(
		),
		'site' => array(
			'mod_j4srichtools' => array('left', 0)
		)
	),
	// plugins => { (folder) => { (element) => (published) }* }*
	'plugins' => array(
		'system' => array(
			'j4schema_jintegration'	=> 0
		)
	)
);

$_delete_on_pro_files = array('admin' => array(
									'views/author/skip.xml',
									'views/authors/skip.xml',
									'views/overrides/skip.xml',
									'views/token/skip.xml',
									'views/tokens/skip.xml'
									)
							);

jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.utilities.date');
jimport('joomla.plugin.helper');
jimport('joomla.filesystem.archive');

$db		= JFactory::getDbo();
$params = JComponentHelper::getParams('com_j4schema');

$version 	  = $this->manifest->get('version');
$new_version  = $version[0]->data();
$prev_version = $params->get('lastSchemaUpdate', '3.2.0');

// Schema updates -- BEGIN
$sqlUpdate = JPATH_ROOT.'/administrator/components/com_j4schema/install/updates';
$files = str_replace('.sql', '', JFolder::files($sqlUpdate, '\.sql$'));
usort($files, 'version_compare');

foreach ($files as $file)
{
	if (version_compare($file, $prev_version) > 0)
	{
		$buffer  = file_get_contents($sqlUpdate.'/'.$file.'.sql');
		$queries = JInstallerHelper::splitSql($buffer);

		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query)
		{
			$query = trim($query);
			if ($query != '' && $query{0} != '#') {
				$db->setQuery($query);
				if (!$db->query()) {
					JError::raiseWarning(1, 'JInstaller::install: '.JText::_('SQL Error')." ".$db->stderr(true));
					return false;
				}
			}
		}
	}
}

$params->set('lastSchemaUpdate', $new_version);
$db->setQuery('SELECT id FROM #__components WHERE option = '.$db->quote('com_j4schema'));
$extension_id = $db->loadResult();

$query = 'UPDATE #__components SET params = '.$db->quote($params->toString()).' WHERE id = '.$extension_id;
$db->setQuery($query);
$db->query();
// Schema updates -- END

$src = $this->parent->getPath('source');

if(file_exists($src.'/media/js/pro.js'))	define('J4SCHEMA_PRO', 1);
else										define('J4SCHEMA_PRO', 0);

$source = $src.'/zzz_fof';
$target = JPATH_ROOT.'/libraries/fof';

$haveToInstallFOF = false;
$rawData = JFile::read($source.'/version.txt');
$info = explode("\n", $rawData);
$fofVersion['package'] = array(
	'version'	=> trim($info[0]),
	'date'		=> new JDate(trim($info[1]))
);

if(!JFolder::exists($target))
{
	JFolder::create($target);
	$haveToInstallFOF = true;
}
else
{
	if(JFile::exists($target.'/version.txt'))
	{
		$rawData = JFile::read($target.'/version.txt');
		$info = explode("\n", $rawData);
		$fofVersion['installed'] = array(
			'version'	=> trim($info[0]),
			'date'		=> new JDate(trim($info[1]))
		);
	}
	else
	{
		$fofVersion['installed'] = array(
			'version'	=> '0.0',
			'date'		=> new JDate('2011-01-01')
		);
	}

	$haveToInstallFOF = $fofVersion['package']['date']->toUNIX() > $fofVersion['installed']['date']->toUNIX();
}

if($haveToInstallFOF)
{
	$installedFOF = true;
	$files = JFolder::files($source);
	$versionSource = 'package';
	if(!empty($files)) {
		foreach($files as $file) {
			$installedFOF = $installedFOF && JFile::copy($source.'/'.$file, $target.'/'.$file);
		}
	}
}
else
{
	$versionSource = 'installed';
}

if(!isset($fofVersion))
{
	$fofVersion = array();
	if(JFile::exists($target.'/version.txt')) {
		$rawData = JFile::read($target.'/version.txt');
		$info = explode("\n", $rawData);
		$fofVersion['installed'] = array(
			'version'	=> trim($info[0]),
			'date'		=> new JDate(trim($info[1]))
		);
	} else {
		$fofVersion['installed'] = array(
			'version'	=> '0.0',
			'date'		=> new JDate('2011-01-01')
		);
	}

	$versionSource = 'installed';
}

$fofStatus =  array(
			'required'	=> $haveToInstallFOF,
			'installed'	=> $installedFOF,
			'version'	=> $fofVersion[$versionSource]['version'],
			'date'		=> $fofVersion[$versionSource]['date']->toFormat('%Y-%m-%d'),
		);

// It's a pro version, let's check if I have to delete skip files coming from the base one
if(file_exists(JPATH_ROOT.'/media/com_j4schema/js/pro.js'))
{
	foreach($_delete_on_pro_files['admin'] as $file)
	{
		$filename = JPATH_ROOT.'/administrator/components/com_j4schema/'.$file;
		if(file_exists($filename)) @unlink($filename);
	}
}

// Module installation - BEGIN
if(count($installation_queue['modules'])) {
	foreach($installation_queue['modules'] as $folder => $modules) {
		if(count($modules)) foreach($modules as $module => $modulePreferences) {
			// Install the module
			if(empty($folder)) $folder = 'site';
			$path = "$src/modules/$folder/$module";
			if(!is_dir($path)) {
				$path = "$src/modules/$folder/mod_$module";
			}
			if(!is_dir($path)) {
				$path = "$src/modules/$module";
			}
			if(!is_dir($path)) {
				$path = "$src/modules/mod_$module";
			}
			if(!is_dir($path)) continue;

			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->modules[] = array('name'=>$module, 'client'=>$folder, 'result'=>$result);
		}
	}
}
// Module installation - END

// Plugins installation - BEGIN
if(count($installation_queue['plugins'])) {
	foreach($installation_queue['plugins'] as $folder => $plugins) {
		if(count($plugins)) foreach($plugins as $plugin => $published) {
			$path = "$src/plugins/$folder/$plugin";
			if(!is_dir($path)) {
				$path = "$src/plugins/$folder/plg_$plugin";
			}
			if(!is_dir($path)) {
				$path = "$src/plugins/$plugin";
			}
			if(!is_dir($path)) {
				$path = "$src/plugins/plg_$plugin";
			}
			if(!is_dir($path)) continue;

			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->plugins[] = array('name'=>'plg_'.$plugin,'group'=>$folder, 'result'=>$result);
		}
	}
}

// Plugin installation - END

$jce = JPluginHelper::isEnabled('editors', 'jce');

// let's copy the JCE plugin, so users can re-install it
JFolder::copy($src.'/plugins/jce/j4schema', JPATH_ROOT.'/administrator/components/com_j4schema/jce/j4schema', '', true);

//JCE is not installed, let's stop here
if(!$jce)
{
	$jceStatus['error'] = 'JCE plugin editor not installed. Install it and then reinstall the plugin from J4Schema control panel';
}
else
{
	if(!JFolder::copy($src.'/plugins/jce/j4schema' , JPATH_ROOT.'/components/com_jce/editor/tiny_mce/plugins/j4schema', '', true))
	{
		$jceStatus['error'] = 'There was an error extracting the JCE package. Please re-install J4Schema';
	}
	else
	{
		//automatically add the plugin to the "Default" JCE profile
		$db = JFactory::getDbo();
		$query = "SELECT * FROM #__wf_profiles WHERE name = 'default'";
		$db->setQuery($query);
		$profile = $db->loadObject();

		if(!$profile)
		{
			$jceStatus['notice'] = 'JCE default profile not found. You have to manually the J4Schema button to the toolbar';
		}
		else
		{
			//check if J4Schema JCE plugin is already configurated
			if(stripos($profile->rows, 'j4schema') === false && stripos($profile->plugins, 'j4schema') === false)
			{
				$query = 'UPDATE #__wf_profiles '.
						' SET rows = '.$db->quote($profile->rows.',j4schema').','.
						' plugins = '.$db->quote($profile->plugins.',j4schema').
						' WHERE id = '.$profile->id;
				$db->setQuery($query);


				if(!$db->query())
				{
					$jceStatus['notice'] = 'There was an error while adding J4Schema button to JCE toolbar, you have to do that manually.';
				}
				else
				{
					$jceStatus['ok'] = 'Installed';
				}
			}
		}
	}
}

?>
	<img src="../media/com_j4schema/images/j4schema_48.png" width="48" height="48" alt="J4Schema" align="right" />

	<h2>Welcome to J4Schema!</h2>

	<p>Congratulations! Now you can start using J4Schema!</p>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="title" colspan="2">Extension</th>
				<th width="30%">Status</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3"></td>
			</tr>
		</tfoot>
		<tbody>
			<tr class="row0">
				<td class="key" colspan="2">J4Schema component</td>
				<td><strong style="color: green">Installed</strong></td>
			</tr>
			<tr class="row1">
				<td class="key" colspan="2">
					<strong>Framework on Framework (FOF) <?php echo $fofStatus['version']?></strong> [<?php echo $fofStatus['date'] ?>]
				</td>
				<td><strong>
					<span style="color: <?php echo $fofStatus['required'] ? ($fofStatus['installed']?'green':'red') : '#660' ?>; font-weight: bold;">
						<?php echo $fofStatus['required'] ? ($fofStatus['installed'] ?'Installed':'Not Installed') : 'Already up-to-date'; ?>
					</span>
				</strong></td>
			</tr>
			<?php if (count($status->modules)) : ?>
			<tr>
				<th>Module</th>
				<th>Client</th>
				<th></th>
			</tr>
			<?php foreach ($status->modules as $module) :
					$color = $module['result'] == 'Installed' ? 'green' : 'red';
			?>
			<tr class="row<?php echo (++ $rows % 2); ?>">
				<td class="key"><?php echo $module['name']; ?></td>
				<td class="key"><?php echo ucfirst($module['client']); ?></td>
				<td><strong style="color:<?php echo $color?>"><?php echo ($module['result'])?'Installed':'Not installed'; ?></strong></td>
			</tr>
			<?php endforeach;?>
			<?php endif;?>
			<tr class="row<?php echo (++ $rows % 2); ?>">
				<td class="key">JCE plugin</td>
				<td class="key">JCE editor</td>
				<td>
					<?php
						if    (isset($jceStatus['error']))  $color = 'red';
						elseif(isset($jceStatus['notice'])) $color = '#660';
						else								$color = 'green';
					?>
					<strong style="color:<?php echo $color?>"><?php echo array_pop($jceStatus)?></strong>
				</td>
			</tr>
			<?php if (count($status->plugins)) : ?>
			<tr>
				<th>Plugin</th>
				<th>Group</th>
				<th></th>
			</tr>
			<?php foreach ($status->plugins as $plugin) :
					$color = $plugin['result'] == 'Installed' ? 'green' : 'red';
			?>
			<tr class="row<?php echo (++ $rows % 2); ?>">
				<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
				<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
				<td><strong style="color:<?php echo $color?>"><?php echo ($plugin['result'])?'Installed':'Not installed'; ?></strong></td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>