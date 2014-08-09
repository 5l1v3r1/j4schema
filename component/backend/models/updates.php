<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die;

/**
 * The updates provisioning Model
 */
class J4schemaModelUpdates extends F0FUtilsUpdate
{
	/**
	 * Public constructor. Initialises the protected members as well.
	 *
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$isPro = defined('J4SCHEMA_PRO') ? J4SCHEMA_PRO : 0;

		JLoader::import('joomla.application.component.helper');
		$params = JComponentHelper::getParams('com_j4schema');
		$dlid = $params->get('downloadid', '');
		$this->extraQuery = null;

		// If I have a valid Download ID I will need to use a non-blank extra_query in Joomla! 3.2+
		if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
		{
			// Even if the user entered a Download ID in the Core version. Let's switch his update channel to Professional
			$isPro = true;

			$this->extraQuery = 'dlid=' . $dlid;
		}

		$this->updateSiteName = 'J4Schema ' . ($isPro ? 'Pro' : 'Free');

        if($isPro)
        {
            $this->updateSite = 'http://www.fabbricabinaria.it/index.php?option=com_ars&view=update&task=stream&format=xml&id=4';
        }
        else
        {
            $this->updateSite = 'http://www.fabbricabinaria.it/index.php?option=com_ars&view=update&task=stream&format=xml&id=3';
        }
	}
}