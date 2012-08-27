<?php
/**
 * @package    	J4Schema
 * @author     	Davide Tampellini
 * @copyright 	Copyright (c)2011-2012 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

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

$src = $this->parent->getPath('source');

$source = $src.'/zzz_fof';
if(!defined('JPATH_LIBRARIES')) $target = JPATH_ROOT.'/libraries/fof';
else 							$target = JPATH_LIBRARIES.'/fof';

$haveToInstallFOF = false;
if(!JFolder::exists($target))
{
	JFolder::create($target);
	$haveToInstallFOF = true;
}
else
{
	$fofVersion = array();
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
	$rawData = JFile::read($source.'/version.txt');
	$info = explode("\n", $rawData);
	$fofVersion['package'] = array(
		'version'	=> trim($info[0]),
		'date'		=> new JDate(trim($info[1]))
	);
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

return true;


///////////////////////////////
$jce = JPluginHelper::isEnabled('editors', 'jce');

//JCE is not installed, let's stop here
if(!$jce)
{
	$jceStatus['error'] = 'JCE plugin editor not installed. Install it and then reinstall J4Schema';
}
else
{
	if(!JArchive::extract($pkg_path.'/'.$files[$i], JPATH_ROOT.'/components/com_jce/editor/tiny_mce/plugins'))
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
			<tr class="row0">
				<td class="key" colspan="2">JCE plugin</td>
				<td>
					<?php
						if    (isset($jceStatus['error']))  $color = 'red';
						elseif(isset($jceStatus['notice'])) $color = '#660';
						else								$color = 'green';
					?>
					<strong style="color:<?php echo $color?>"><?php echo array_pop($jceStatus)?></strong>
				</td>
			</tr>
		<?php if(J4SCHEMA_PRO):?>
			<tr class="row1">
				<td class="key" colspan="2">Joomla integration plugin</td>
				<td>
					<?php
						if    (isset($jintegration['error']))  	$color = 'red';
						elseif(isset($jintegration['notice'])) 	$color = '#660';
						else									$color = 'green';
					?>
					<strong style="color:<?php echo $color?>"><?php echo array_pop($jintegration)?></strong>
				</td>
			</tr>
		<?php endif;?>
		</tbody>
	</table>