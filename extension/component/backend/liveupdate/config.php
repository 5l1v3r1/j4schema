<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName			= 'pkg_j4schema';
	var $_extensionTitle		= 'J4Schema package';

	//default settings (standard version)
	var $_updateURL				= 'http://www.fabbricabinaria.it/index.php?option=com_ars&view=update&format=ini&id=3';
	var $_requiresAuthorization	= false;

	var $_versionStrategy		= 'different';

	function __construct()
	{
		//settings for pro version
		if(defined('J4SCHEMA_PRO') && J4SCHEMA_PRO == 1)
		{
			$this->_updateURL = 'http://www.fabbricabinaria.it/index.php?option=com_ars&view=update&format=ini&id=4';
			$this->_requiresAuthorization = true;
		}

		parent::__construct();
	}
}