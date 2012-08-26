<?php
/**
 * @package    	J4Schema
 * @author     	Davide Tampellini
 * @copyright 	Copyright (c)2011-2012 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class Com_j4schemaInstallerScript
{
	/** @var string The component's name */
	protected $_fabbrica_extension = 'com_j4schema';

	/** @var array */
	protected $_delete_on_pro_files = array('admin' => array(
												'views/author/skip.xml',
												'views/authors/skip.xml',
												'views/overrides/skip.xml',
												'views/token/skip.xml',
												'views/tokens/skip.xml'
													)
											);

	/**
	 * Joomla! pre-flight event
	 *
	 * @param string $type Installation type (install, update, discover_install)
	 * @param JInstaller $parent Parent object
	 */
	public function preflight($type, $parent)
	{
		// Bugfix for "Can not build admin menus"
		if(in_array($type, array('install','discover_install'))) {
			$this->_bugfixDBFunctionReturnedNoError();
		} else {
			$this->_bugfixCantBuildAdminMenus();
		}

		// Only allow to install on Joomla! 2.5.0 or later
		return version_compare(JVERSION, '2.5.0', 'ge');
	}

	function update($parent)
	{
		$db = JFactory::getDBO();
		if(method_exists($parent, 'extension_root')) {
			$sqlfile = $parent->getPath('extension_root').DS.'install/install.sql';
		} else {
			$sqlfile = $parent->getParent()->getPath('extension_root').DS.'install/install.sql';
		}

		$buffer = file_get_contents($sqlfile);
		if ($buffer !== false) {
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (count($queries) != 0) {
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
							return false;
						}
					}
				}
			}
		}
	}

	/**
	 * Runs after install, update or discover_update
	 * @param string $type install, update or discover_update
	 * @param JInstaller $parent
	 */
	function postflight( $type, $parent )
	{
		$fofStatus = $this->_installFOF($parent);

		// It's a pro version, let's check if I have to delete skip files coming from the base one
		if(file_exists(JPATH_ROOT.'/media/com_j4schema/js/pro.js'))
		{
			foreach($this->_delete_on_pro_files['admin'] as $file)
			{
				$filename = JPATH_ROOT.'/administrator/components/com_j4schema/'.$file;
				if(file_exists($filename)) @unlink($filename);
			}
		}
	}

	/**
	 * Joomla! 1.6+ bugfix for "DB function returned no error"
	 */
	private function _bugfixDBFunctionReturnedNoError()
	{
		$db = JFactory::getDbo();

		// Fix broken #__assets records
		$query = $db->getQuery(true);
		$query->select('id')
			  ->from('#__assets')
			  ->where($db->qn('name').' = '.$db->q($this->_fabbrica_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__assets')
				  ->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}

		// Fix broken #__extensions records
		$query = $db->getQuery(true);
		$query->select('extension_id')
			  ->from('#__extensions')
			  ->where($db->qn('element').' = '.$db->q($this->_fabbrica_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__extensions')
				  ->where($db->qn('extension_id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}

		// Fix broken #__menu records
		$query = $db->getQuery(true);
		$query->select('id')
			  ->from('#__menu')
			  ->where($db->qn('type').' = '.$db->q('component'))
			  ->where($db->qn('menutype').' = '.$db->q('main'))
			  ->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_fabbrica_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
				  ->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Joomla! 1.6+ bugfix for "Can not build admin menus"
	 */
	private function _bugfixCantBuildAdminMenus()
	{
		$db = JFactory::getDbo();

		// If there are multiple #__extensions record, keep one of them
		$query = $db->getQuery(true);
		$query->select('extension_id')
			  ->from('#__extensions')
			  ->where($db->qn('element').' = '.$db->q($this->_fabbrica_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(count($ids) > 1) {
			asort($ids);
			$extension_id = array_shift($ids); // Keep the oldest id

			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__extensions')
					  ->where($db->qn('extension_id').' = '.$db->q($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// @todo

		// If there are multiple assets records, delete all except the oldest one
		$query = $db->getQuery(true);
		$query->select('id')
			  ->from('#__assets')
			  ->where($db->qn('name').' = '.$db->q($this->_fabbrica_extension));
		$db->setQuery($query);
		$ids = $db->loadObjectList();
		if(count($ids) > 1) {
			asort($ids);
			$asset_id = array_shift($ids); // Keep the oldest id

			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__assets')
					  ->where($db->qn('id').' = '.$db->q($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// Remove #__menu records for good measure!
		$query = $db->getQuery(true);
		$query->select('id')
			  ->from('#__menu')
			  ->where($db->qn('type').' = '.$db->q('component'))
			  ->where($db->qn('menutype').' = '.$db->q('main'))
			  ->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_fabbrica_extension));
		$db->setQuery($query);
		$ids1 = $db->loadColumn();
		if(empty($ids1)) $ids1 = array();
		$query = $db->getQuery(true);
		$query->select('id')
			  ->from('#__menu')
			  ->where($db->qn('type').' = '.$db->q('component'))
			  ->where($db->qn('menutype').' = '.$db->q('main'))
			  ->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_fabbrica_extension.'&%'));
		$db->setQuery($query);
		$ids2 = $db->loadColumn();
		if(empty($ids2)) $ids2 = array();
		$ids = array_merge($ids1, $ids2);
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
				  ->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}
	}

	private function _installFOF($parent)
	{
		$src = $parent->getParent()->getPath('source');

		// Install the FOF framework
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.date');
		$source = $src.'/zzz_fof';
		if(!defined('JPATH_LIBRARIES')) {
			$target = JPATH_ROOT.'/libraries/fof';
		} else {
			$target = JPATH_LIBRARIES.'/fof';
		}
		$haveToInstallFOF = false;
		if(!JFolder::exists($target)) {
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

		$installedFOF = false;
		if($haveToInstallFOF) {
			$versionSource = 'package';
			$installer = new JInstaller;
			$installedFOF = $installer->install($source);
		} else {
			$versionSource = 'installed';
		}

		if(!isset($fofVersion)) {
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

		if(!($fofVersion[$versionSource]['date'] instanceof JDate)) {
			$fofVersion[$versionSource]['date'] = new JDate();
		}

		return array(
				'required'	=> $haveToInstallFOF,
				'installed'	=> $installedFOF,
				'version'	=> $fofVersion[$versionSource]['version'],
				'date'		=> $fofVersion[$versionSource]['date']->toFormat('%Y-%m-%d'),
		);
	}
}