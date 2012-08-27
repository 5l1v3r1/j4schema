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

// Install the FOF framework
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.utilities.date');
jimport('joomla.plugin.helper');
jimport('joomla.filesystem.archive');

$src = $this->parent->getPath('source');

$source = $src.'/zzz_fof';
if(!defined('JPATH_LIBRARIES')) {
	$target = JPATH_ROOT.'/libraries/fof';
} else {
	$target = JPATH_LIBRARIES.'/fof';
}
$haveToInstallFOF = false;
if(!JFolder::exists($target)) {
	JFolder::create($target);
	$haveToInstallFOF = true;
} else {
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

	$haveToInstallFOF = $fofVersion['package']['date']->toUNIX() > $fofVersion['installed']['date']->toUNIX();
}

if($haveToInstallFOF) {
	$installedFOF = true;
	$files = JFolder::files($source);
	if(!empty($files)) {
		foreach($files as $file) {
			$installedFOF = $installedFOF && JFile::copy($source.'/'.$file, $target.'/'.$file);
		}
	}
}

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
$lang = JFactory::getLanguage();
$lang->load('com_j4schema', JPATH_ADMINISTRATOR, 'en-GB', true);
$lang->load('com_j4schema', JPATH_ADMINISTRATOR, null, true);

$jce = JPluginHelper::isEnabled('editors', 'jce');

//JCE is not installed, let's stop here
if(!$jce)
{
	$html[] = '<h2>'.JText::_('COM_J4SCHEMA_NOT_INSTALLED').'</h2>';
	$html[] = '<p><em>'.JText::_('COM_J4SCHEMA_JCE_NOT_INSTALLED').'</em></p>';
	$html[] = '<p>'.JText::_('COM_J4SCHEMA_INSTALL_JCE').'</p>';
	$html[] = '<p>'.JText::_('COM_J4SCHEMA_DOWNLOAD_PLUGIN').'</p>';

	$this->output($html, false);
	return true;
}

$pkg_path = realpath(dirname(__FILE__).'/packages');
$files = JFolder::files($pkg_path);

for($i = 0; $i < count($files); $i++)
{
	if(stripos($files[$i], 'JCE_') !== false)
	{
		$found = true;
		break;
	}
}

//if i didn't find the JCE plugin, let's stop here
if(!$found)
{
	$html[] = '<h2>'.JText::_('COM_J4SCHEMA_NOT_INSTALLED').'</h2>';
	$html[] = '<p><em>'.JText::_('COM_J4SCHEMA_PLUGIN_NOT_FOUND').'</em></p>';
	$html[] = '<p>'.JText::_('COM_J4SCHEMA_DOWNLOAD_PLUGIN').'</p>';

	$this->output($html, false);
	return true;
}

if(!JArchive::extract($pkg_path.'/'.$files[$i], JPATH_ROOT.'/components/com_jce/editor/tiny_mce/plugins'))
{
	$html[] = '<h2>'.JText::_('COM_J4SCHEMA_NOT_INSTALLED').'</h2>';
	$html[] = '<p><em>'.JText::_('COM_J4SCHEMA_ERROR_EXTRACT').'</em></p>';
	$html[] = '<p>'.JText::_('COM_J4SCHEMA_DOWNLOAD_PLUGIN').'</p>';
	return true;
}

//automatically add the plugin to the "Default" JCE profile
$db = JFactory::getDbo();
$query = $db->getQuery(true)
			->select('*')
			->from('#__wf_profiles')
			->where("name = 'default'");
$profile = $db->setQuery($query)->loadObject();

if(!$profile)
{
	$html[] = '<h2>'.JText::_('COM_J4SCHEMA_NOT_CONFIGURATED').'</h2>';
	$html[] = '<p><em>'.JText::_('COM_J4SCHEMA_JCE_DEFAULT_NOT_FOUND').'</em></p>';
	$html[] = '<p>'.JText::_('COM_J4SCHEMA_TOOLBAR_MANUAL').'</p>';

	$this->output($html, false);
	return true;
}

//check if J4Schema JCE plugin is already configurated
if(stripos($profile->rows, 'j4schema') === false && stripos($profile->plugins, 'j4schema') === false)
{
	$query = $db->getQuery(true)
				->update('#__wf_profiles')
				->set('rows = '.$db->quote($profile->rows.',j4schema'))
				->set('plugins = '.$db->quote($profile->plugins.',j4schema'))
				->where('id = '.$profile->id);

	if(!$db->setQuery($query)->query())
	{
		$html[] = '<h2>'.JText::_('COM_J4SCHEMA_NOT_CONFIGURATED').'</h2>';
		$html[] = '<p><em>'.JText::_('COM_J4SCHEMA_JCE_PARAMS_ERROR').'</em></p>';
		$html[] = '<p>'.JText::_('COM_J4SCHEMA_TOOLBAR_MANUAL').'</p>';

		$this->output($html, false);
		return true;
	}
}

$html[] = '<h2>'.JText::_('COM_J4SCHEMA_TITLE').'</h2>';
$html[] = '<p>'.JText::_('COM_J4SCHEMA_SUBTITLE').'</p>';

$this->output($html, true);