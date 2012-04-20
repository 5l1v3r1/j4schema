<?php
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
		if(is_null($tpl)) $tpl = 'descr';
		$result = $this->loadTemplate($tpl);
		JError::setErrorHandling(E_WARNING,'callback');

		if($result instanceof JException) {
			// Default JSON behaviour in case the template isn't there!
			echo json_encode($items);
			return false;
		}
	}
}