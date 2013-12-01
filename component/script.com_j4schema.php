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
	protected $delete_on_pro_files = array('admin' => array(
												'views/author/skip.xml',
												'views/authors/skip.xml',
												'views/overrides/skip.xml',
												'views/token/skip.xml',
												'views/tokens/skip.xml'
													)
											);
	protected $installation_queue = array(
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

	protected $status;

	function __construct()
	{
		$this->status = new JObject;
	}

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

		// Only allow to install on Joomla! 2.5.0 (JVERSION < 3.0) or Joomla 3.1+ (JVERSION >= 3.1)
		return version_compare(JVERSION, '3.0', 'lt') || version_compare(JVERSION, '3.1', 'ge');
	}

	/**
	 * Runs after install, update or discover_update
	 * @param string $type install, update or discover_update
	 * @param JInstaller $parent
	 */
	function postflight( $type, $parent )
	{
		$this->src = $parent->getParent()->getPath('source');

		$this->fofStatus     = $this->_installFOF($parent);
        $this->straperStatus = $this->_installStraper($parent);

		$this->installModules();
		$this->installPlugins();
		$this->installJCEPlugin();

        require_once JPATH_LIBRARIES.'/fof/include.php';

        $platform = FOFPlatform::getInstance();

        if (method_exists($platform, 'clearCache'))
        {
            FOFPlatform::getInstance()->clearCache();
        }

		// It's a pro version, let's check if I have to delete skip files coming from the base one
		if(file_exists(JPATH_ROOT.'/media/com_j4schema/js/pro.js'))
		{
			foreach($this->delete_on_pro_files['admin'] as $file)
			{
				$filename = JPATH_ROOT.'/administrator/components/com_j4schema/'.$file;
				if(file_exists($filename)) @unlink($filename);
			}
		}

		$this->renderPostInstallation();
	}

	function uninstall()
	{
		$db = JFactory::getDbo();

		$extension = JTable::getInstance('extension');
		$component_id = $extension->find(array('element' => 'com_j4schema',
											   'type'    => 'component'));

		if($component_id)
		{
			// Clean up schema table
			$query = $db->getQuery(true)
						->delete('#__schemas')
						->where('extension_id = '.$component_id);
			$rc = $db->setQuery($query)->query();
		}

		// Modules uninstallation
		if(count($this->installation_queue['modules']))
		{
			foreach($this->installation_queue['modules'] as $folder => $modules)
			{
				if(count($modules)) foreach($modules as $module => $modulePreferences)
				{
					// Find the module ID
					$db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = '.$db->Quote($module).' AND `type` = "module"');
					$id = $db->loadResult();

					if($id)
					{
						// Uninstall the module
						$installer = new JInstaller;
						$result = $installer->uninstall('module',$id,1);
						$this->status->modules[] = array('name'=>$module,'client'=>$folder, 'result'=>$result);
					}
				}
			}
		}

		// Plugins uninstallation
		if(count($this->installation_queue['plugins']))
		{
			foreach($this->installation_queue['plugins'] as $folder => $plugins)
			{
				if(count($plugins)) foreach($plugins as $plugin => $published)
				{
					$db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = '.$db->Quote($plugin).' AND `folder` = '.$db->Quote($folder));
					$id = $db->loadResult();

					if($id)
					{
						$installer = new JInstaller;
						$result = $installer->uninstall('plugin',$id,1);
						$this->status->plugins[] = array('name'=>'plg_'.$plugin,'group'=>$folder, 'result'=>$result);
					}
				}
			}
		}

		if(JFolder::exists(JPATH_ROOT.'/components/com_jce/editor/tiny_mce/plugins/j4schema'))
		{
			$result = JFolder::delete(JPATH_ROOT.'/components/com_jce/editor/tiny_mce/plugins/j4schema');
			$this->status->plugins[] = array('name' => 'JCE Plugin', 'group' => 'JCE', 'result' => $result);
		}

		$this->renderPostUninstallation();
	}

	protected function installModules()
	{
		if(count($this->installation_queue['modules'])) {
			foreach($this->installation_queue['modules'] as $folder => $modules) {
				if(count($modules)) foreach($modules as $module => $modulePreferences) {
					// Install the module
					if(empty($folder)) $folder = 'site';
					$path = "$this->src/modules/$folder/$module";
					if(!is_dir($path)) {
						$path = "$this->src/modules/$folder/mod_$module";
					}
					if(!is_dir($path)) {
						$path = "$this->src/modules/$module";
					}
					if(!is_dir($path)) {
						$path = "$this->src/modules/mod_$module";
					}
					if(!is_dir($path)) continue;

					$installer = new JInstaller;
					$result = $installer->install($path);
					$this->status->modules[] = array('name'=>$module, 'client'=>$folder, 'result'=>$result);
				}
			}
		}
	}

	protected function installPlugins()
	{
		if(count($this->installation_queue['plugins'])) {
			foreach($this->installation_queue['plugins'] as $folder => $plugins) {
				if(count($plugins)) foreach($plugins as $plugin => $published) {
					$path = "$this->src/plugins/$folder/$plugin";
					if(!is_dir($path)) {
						$path = "$this->src/plugins/$folder/plg_$plugin";
					}
					if(!is_dir($path)) {
						$path = "$this->src/plugins/$plugin";
					}
					if(!is_dir($path)) {
						$path = "$this->src/plugins/plg_$plugin";
					}
					if(!is_dir($path)) continue;

					$installer = new JInstaller;
					$result = $installer->install($path);
					$this->status->plugins[] = array('name'=>'plg_'.$plugin,'group'=>$folder, 'result'=>$result);
				}
			}
		}
	}

	protected function installJCEPlugin()
	{
		$jce = JPluginHelper::isEnabled('editors', 'jce');

		// let's copy the JCE plugin, so users can re-install it
		JFolder::copy($this->src.'/plugins/jce/j4schema', JPATH_ROOT.'/administrator/components/com_j4schema/jce/j4schema', '', true);

		//JCE is not installed, let's stop here
		if(!$jce)
		{
			$this->jceStatus['error'] = 'JCE plugin editor not installed. Install it and then reinstall the plugin from J4Schema control panel';
		}
		else
		{
			if(!JFolder::copy($this->src.'/plugins/jce/j4schema' , JPATH_ROOT.'/components/com_jce/editor/tiny_mce/plugins/j4schema', '', true))
			{
				$this->jceStatus['error'] = 'There was an error extracting the JCE package. Please re-install J4Schema';
			}
			else
			{
				//automatically add the plugin to the "Default" JCE profile
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
							->select('*')
							->from('#__wf_profiles')
							->where('name = '.$db->Quote('default'));
				$profile = $db->setQuery($query)->loadObject();

				if(!$profile){
					$this->jceStatus['notice'] = 'JCE default profile not found. You have to manually the J4Schema button to the toolbar';
				}
				else
				{
					//check if J4Schema JCE plugin is already configurated
					if(stripos($profile->rows, 'j4schema') === false && stripos($profile->plugins, 'j4schema') === false)
					{
						$query = $db->getQuery(true)
									->update('#__wf_profiles')
									->set('rows = '.$db->quote($profile->rows.',j4schema'))
									->set('plugins = '.$db->quote($profile->plugins.',j4schema'))
									->where('id = '.$profile->id);

						if(!$db->setQuery($query)->query()){
							$this->jceStatus['notice'] = 'There was an error while adding J4Schema button to JCE toolbar, you have to do that manually.';
						}
						else{
							$this->jceStatus['ok'] = 'Installed';
						}
					}
					else{
						$this->jceStatus['ok'] = 'Installed';
					}
				}
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
		$source = $src.'/zzz_fof_2';
		$target = JPATH_LIBRARIES.'/fof';

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

            // Do not install FOF on Joomla! 3.2.0 beta 1 or later
            if (version_compare(JVERSION, '3.1.999', 'gt'))
            {
                $haveToInstallFOF = false;
            }
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
			'date'		=> $fofVersion[$versionSource]['date']->format('Y-m-d'),
		);
	}

    private function _installStraper($parent)
    {
        $src = $parent->getParent()->getPath('source');

        // Install the FOF framework
        JLoader::import('joomla.filesystem.folder');
        JLoader::import('joomla.filesystem.file');
        JLoader::import('joomla.utilities.date');

        $source = $src.'/zzz_strapper';
        $target = JPATH_ROOT.'/media/akeeba_strapper';

        $haveToInstallStraper = false;

        if(!JFolder::exists($target))
        {
            $haveToInstallStraper = true;
        }
        else
        {
            $straperVersion = array();

            if(JFile::exists($target.'/version.txt'))
            {
                $rawData = JFile::read($target.'/version.txt');
                $info = explode("\n", $rawData);
                $straperVersion['installed'] = array(
                    'version'	=> trim($info[0]),
                    'date'		=> new JDate(trim($info[1]))
                );
            }
            else
            {
                $straperVersion['installed'] = array(
                    'version'	=> '0.0',
                    'date'		=> new JDate('2011-01-01')
                );
            }

            $rawData = JFile::read($source.'/version.txt');
            $info = explode("\n", $rawData);

            $straperVersion['package'] = array(
                'version'	=> trim($info[0]),
                'date'		=> new JDate(trim($info[1]))
            );

            $haveToInstallStraper = $straperVersion['package']['date']->toUNIX() > $straperVersion['installed']['date']->toUNIX();
        }

        $installedStraper = false;

        if($haveToInstallStraper)
        {
            $versionSource = 'package';
            $installer = new JInstaller;
            $installedStraper = $installer->install($source);
        }
        else
        {
            $versionSource = 'installed';
        }

        if(!isset($straperVersion))
        {
            $straperVersion = array();

            if(JFile::exists($target.'/version.txt'))
            {
                $rawData = JFile::read($target.'/version.txt');
                $info = explode("\n", $rawData);
                $straperVersion['installed'] = array(
                    'version'	=> trim($info[0]),
                    'date'		=> new JDate(trim($info[1]))
                );
            }
            else
            {
                $straperVersion['installed'] = array(
                    'version'	=> '0.0',
                    'date'		=> new JDate('2011-01-01')
                );
            }

            $rawData = JFile::read($source.'/version.txt');
            $info = explode("\n", $rawData);

            $straperVersion['package'] = array(
                'version'	=> trim($info[0]),
                'date'		=> new JDate(trim($info[1]))
            );

            $versionSource = 'installed';
        }

        if(!($straperVersion[$versionSource]['date'] instanceof JDate))
        {
            $straperVersion[$versionSource]['date'] = new JDate();
        }

        return array(
            'required'	=> $haveToInstallStraper,
            'installed'	=> $installedStraper,
            'version'	=> $straperVersion[$versionSource]['version'],
            'date'		=> $straperVersion[$versionSource]['date']->format('Y-m-d'),
        );
    }

	protected function renderPostInstallation()
	{
        $rows = 0;
?>
	<div>
		<img src="../media/com_j4schema/images/j4schema_48.png" width="48" height="48" alt="J4Schema" align="right" />

		<h2>Welcome to J4Schema!</h2>

		<p>Congratulations! Now you can start using J4Schema!</p>

		<table class="adminlist table table-striped">
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
						<strong>Framework on Framework (FOF) <?php echo $this->fofStatus['version']?></strong> [<?php echo $this->fofStatus['date'] ?>]
					</td>
					<td><strong>
						<span style="color: <?php echo $this->fofStatus['required'] ? ($this->fofStatus['installed']?'green':'red') : '#660' ?>; font-weight: bold;">
							<?php echo $this->fofStatus['required'] ? ($this->fofStatus['installed'] ?'Installed':'Not Installed') : 'Already up-to-date'; ?>
						</span>
					</strong></td>
				</tr>
                <tr class="row0">
                    <td class="key" colspan="2">
                        <strong>Akeeba Strapper <?php echo $this->straperStatus['version']?></strong> [<?php echo $this->straperStatus['date'] ?>]
                    </td>
                    <td><strong>
				<span style="color: <?php echo $this->straperStatus['required'] ? ($this->straperStatus['installed']?'green':'red') : '#660' ?>; font-weight: bold;">
					<?php echo $this->straperStatus['required'] ? ($this->straperStatus['installed'] ?'Installed':'Not Installed') : 'Already up-to-date'; ?>
				</span>
                        </strong></td>
                </tr>
				<?php if (count($this->status->modules)) : ?>
				<tr>
					<th>Module</th>
					<th>Client</th>
					<th></th>
				</tr>
				<?php foreach ($this->status->modules as $module) :
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
							if    (isset($this->jceStatus['error']))  $color = 'red';
							elseif(isset($this->jceStatus['notice'])) $color = '#660';
							else									  $color = 'green';
						?>
						<strong style="color:<?php echo $color?>"><?php echo array_pop($this->jceStatus)?></strong>
					</td>
				</tr>
				<?php if (count($this->status->plugins)) : ?>
				<tr>
					<th>Plugin</th>
					<th>Group</th>
					<th></th>
				</tr>
				<?php foreach ($this->status->plugins as $plugin) :
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
	</div>
<?php
	}

	protected function renderPostUninstallation()
	{
		$rows = 0;?>
	<h2><?php echo JText::_('J4Schema Uninstallation Status'); ?></h2>
	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
				<th width="30%"><?php echo JText::_('Status'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3"></td>
			</tr>
		</tfoot>
		<tbody>
			<tr class="row0">
				<td class="key" colspan="2"><?php echo 'J4Schema '.JText::_('Component'); ?></td>
				<td><strong><?php echo JText::_('Removed'); ?></strong></td>
			</tr>
			<?php if (count($this->status->modules)) : ?>
			<tr>
				<th><?php echo JText::_('Module'); ?></th>
				<th><?php echo JText::_('Client'); ?></th>
				<th></th>
			</tr>
			<?php foreach ($this->status->modules as $module) : ?>
			<tr class="row<?php echo (++ $rows % 2); ?>">
				<td class="key"><?php echo $module['name']; ?></td>
				<td class="key"><?php echo ucfirst($module['client']); ?></td>
				<td><strong><?php echo ($module['result'])?JText::_('Removed'):JText::_('Not removed'); ?></strong></td>
			</tr>
			<?php endforeach;?>
			<?php endif;?>
			<?php if (count($this->status->plugins)) : ?>
			<tr>
				<th><?php echo JText::_('Plugin'); ?></th>
				<th><?php echo JText::_('Group'); ?></th>
				<th></th>
			</tr>
			<?php foreach ($this->status->plugins as $plugin) : ?>
			<tr class="row<?php echo (++ $rows % 2); ?>">
				<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
				<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
				<td><strong><?php echo ($plugin['result'])?JText::_('Removed'):JText::_('Not removed'); ?></strong></td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
<?php
	}
}