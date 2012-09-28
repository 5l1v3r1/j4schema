<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

class J4schemaViewAttributes extends FOFViewJson
{
	function __construct($config = array())
	{
		parent::__construct($config);

		//I add the backend template paths here, so FOF has already did his work
		$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.'/views/attributes/tmpl');
	}

	/**
	 * Custom on event function, since I get data from database in a non-standard way
	 *
	 * @param string $tpl
	 */
	function onGetdescr($tpl = null)
	{
		$model = $this->getModel();

		$items = $model->getDescr();
		$this->assign('items', $items );

		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/json');

		JError::setErrorHandling(E_ALL,'ignore');

		if(version_compare(JVERSION, '1.6.0', 'ge')){
			if(is_null($tpl)) $tpl = 'descr';
		}

		$result = $this->loadTemplate($tpl);
		JError::setErrorHandling(E_WARNING,'callback');

		if($result instanceof JException) {
			// Default JSON behaviour in case the template isn't there!
			echo json_encode($items);
			return false;
		}
	}
}