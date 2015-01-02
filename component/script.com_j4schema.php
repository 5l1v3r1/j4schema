<?php
/**
 * @package    	J4Schema
 * @author     	Davide Tampellini
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

// Load FOF if not already loaded
if (!defined('F0F_INCLUDED'))
{
    $paths = array(
        (defined('JPATH_LIBRARIES') ? JPATH_LIBRARIES : JPATH_ROOT . '/libraries') . '/f0f/include.php',
        __DIR__ . '/zzz_fof/include.php',
    );

    foreach ($paths as $filePath)
    {
        if (!defined('F0F_INCLUDED') && file_exists($filePath))
        {
            @include_once $filePath;
        }
    }
}

// Pre-load the installer script class from our own copy of FOF
if (!class_exists('F0FUtilsInstallscript', false))
{
    @include_once __DIR__ . '/zzz_fof/utils/installscript/installscript.php';
}

// Pre-load the database schema installer class from our own copy of FOF
if (!class_exists('F0FDatabaseInstaller', false))
{
    @include_once __DIR__ . '/zzz_fof/database/installer.php';
}

// Pre-load the update utility class from our own copy of FOF
if (!class_exists('F0FUtilsUpdate', false))
{
    @include_once __DIR__ . '/zzz_fof/utils/update/update.php';
}

// Pre-load the cache cleaner utility class from our own copy of FOF
if (!class_exists('F0FUtilsCacheCleaner', false))
{
    @include_once __DIR__ . '/zzz_fof/utils/cache/cleaner.php';
}

class Com_j4schemaInstallerScript extends F0FUtilsInstallscript
{
    protected $componentTitle      = 'J4Schema';
    protected $componentName       = 'com_j4schema';
    protected $fofSourcePath       = 'zzz_fof';
    protected $strapperSourcePath  = 'zzz_strapper';

	/** @var array */
	protected $removeFilesPro = array(
        'folders' => array(
            'administrator/components/com_j4schema/overrides/2.5/virtuemart',
            'administrator/components/com_j4schema/overrides/2.5/com_virtuemart',
            'administrator/components/com_j4schema/overrides/2.5/com_k2',
            'administrator/components/com_j4schema/overrides/3.0/com_content',
            'administrator/components/com_j4schema/overrides/3.0/layouts',
        ),
        'files' => array(
            'administrator/components/com_j4schema/views/author/skip.xml',
            'administrator/components/com_j4schema/views/authors/skip.xml',
            'administrator/components/com_j4schema/views/overrides/skip.xml',
            'administrator/components/com_j4schema/views/token/skip.xml',
            'administrator/components/com_j4schema/views/tokens/skip.xml'
        )
    );

	protected $installation_queue = array(
					// modules => { (folder) => { (module) => { (position), (published) } }* }*
                    // plugins => { (folder) => { (element) => (published) }* }*
					'modules' => array(
						'site' => array(
							'mod_j4srichtools' => array('left', 0)
						)
					),
					'plugins' => array(
                        'installer' => array('j4schema'              => 1),
                        'system'    => array('j4schema_jintegration' => 0)
					)
				);

	/**
	 * Runs after install, update or discover_update
	 * @param string $type install, update or discover_update
	 * @param JInstaller $parent
	 */
	public function postflight( $type, $parent )
	{
        if(file_exists(JPATH_ROOT.'/media/com_j4schema/js/pro.js'))
        {
            $this->isPaid = true;
        }

		parent::postflight($type, $parent);

        // Remove obsolete files on pro version
        if($this->isPaid)
        {
            foreach ($this->removeFilesPro['folders'] as $folder)
            {
                if(JFolder::exists(JPATH_ROOT.'/'.$folder))
                {
                    JFolder::delete(JPATH_ROOT.'/'.$folder);
                }
            }

            foreach ($this->removeFilesPro['files'] as $file)
            {
                if(JFile::exists(JPATH_ROOT.'/'.$file))
                {
                    JFile::delete(JPATH_ROOT.'/'.$file);
                }
            }
        }
	}

	protected function installJCEPlugin($parent)
	{
		$jce = JPluginHelper::isEnabled('editors', 'jce');
        $src = $parent->getParent()->getPath('source');

		// let's copy the JCE plugin, so users can re-install it
		JFolder::copy($src.'/plugins/jce/j4schema', JPATH_ROOT.'/administrator/components/com_j4schema/jce/j4schema', '', true);

		//JCE is not installed, let's stop here
		if(!$jce)
		{
			$this->jceStatus['error'] = 'JCE plugin editor not installed. Install it and then reinstall the plugin from J4Schema control panel';
		}
		else
		{
			if(!JFolder::copy($src.'/plugins/jce/j4schema' , JPATH_ROOT.'/components/com_jce/editor/tiny_mce/plugins/j4schema', '', true))
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

	protected function renderPostInstallation($status, $fofInstallationStatus, $strapperInstallationStatus, $parent)
	{
        $rows = 0;

        $this->installJCEPlugin($parent);
?>
	<div>
		<img src="../media/com_j4schema/images/j4schema_48.png" width="48" height="48" alt="J4Schema" align="right" />

		<h2>Welcome to J4Schema!</h2>

		<p>Congratulations! Now you can start using J4Schema!</p>

        <table class="adminlist table table-striped" width="100%">
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
            <tr class="row<?php echo($rows++ % 2); ?>">
                <td class="key" colspan="2"><?php echo $this->componentTitle ?></td>
                <td><strong style="color: green">Installed</strong></td>
            </tr>
            <?php if ($fofInstallationStatus['required']): ?>
                <tr class="row<?php echo($rows++ % 2); ?>">
                    <td class="key" colspan="2">
                        <strong>Framework on Framework (FOF) <?php echo $fofInstallationStatus['version'] ?></strong>
                        [<?php echo $fofInstallationStatus['date'] ?>]
                    </td>
                    <td><strong>
							<span
                                style="color: <?php echo $fofInstallationStatus['required'] ? ($fofInstallationStatus['installed'] ? 'green' : 'red') : '#660' ?>; font-weight: bold;">
		<?php echo $fofInstallationStatus['required'] ? ($fofInstallationStatus['installed'] ? 'Installed' : 'Not Installed') : 'Already up-to-date'; ?>
							</span>
                        </strong></td>
                </tr>
            <?php endif; ?>
            <?php if ($strapperInstallationStatus['required']): ?>
                <tr class="row<?php echo($rows++ % 2); ?>">
                    <td class="key" colspan="2">
                        <strong>Akeeba Strapper <?php echo $strapperInstallationStatus['version'] ?></strong>
                        [<?php echo $strapperInstallationStatus['date'] ?>]
                    </td>
                    <td><strong>
							<span
                                style="color: <?php echo $strapperInstallationStatus['required'] ? ($strapperInstallationStatus['installed'] ? 'green' : 'red') : '#660' ?>; font-weight: bold;">
				<?php echo $strapperInstallationStatus['required'] ? ($strapperInstallationStatus['installed'] ? 'Installed' : 'Not Installed') : 'Already up-to-date'; ?>
							</span>
                        </strong></td>
                </tr>
            <?php endif; ?>
            <?php if (count($status->modules)) : ?>
                <tr>
                    <th>Module</th>
                    <th>Client</th>
                    <th></th>
                </tr>
                <?php foreach ($status->modules as $module) : ?>
                    <tr class="row<?php echo($rows++ % 2); ?>">
                        <td class="key"><?php echo $module['name']; ?></td>
                        <td class="key"><?php echo ucfirst($module['client']); ?></td>
                        <td><strong
                                style="color: <?php echo ($module['result']) ? "green" : "red" ?>"><?php echo ($module['result']) ? 'Installed' : 'Not installed'; ?></strong>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
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
            <?php if (count($status->plugins)) : ?>
                <tr>
                    <th>Plugin</th>
                    <th>Group</th>
                    <th></th>
                </tr>
                <?php foreach ($status->plugins as $plugin) : ?>
                    <tr class="row<?php echo($rows++ % 2); ?>">
                        <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
                        <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                        <td><strong
                                style="color: <?php echo ($plugin['result']) ? "green" : "red" ?>"><?php echo ($plugin['result']) ? 'Installed' : 'Not installed'; ?></strong>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
	</div>
<?php
	}

}