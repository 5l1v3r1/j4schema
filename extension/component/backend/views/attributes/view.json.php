<?php
defined('_JEXEC') or die();

class J4schemaViewAttributes extends FOFViewJson
{
	/**
	 * Since I'm using more than a JSON layout, I have to override the standard onRead method,
	 * so I can set the correct $tpl to use
	 *
	 * @see FOFViewHtml::onRead()
	 */
	function onRead($tpl = null)
	{
		xdebug_break();

		$layout = FOFInput::getVar('layout');

		switch ($layout)
		{
			case 'default_descr':
				$layout = 'default';
				$tpl = 'descr';
				break;
		}

		return parent::onRead($tpl);
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