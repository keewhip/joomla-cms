<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_privacy
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;

/**
 * Privacy Controller
 *
 * @since  __DEPLOY_VERSION__
 */
class PrivacyController extends JControllerLegacy
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $default_view = 'dashboard';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function display($cachable = false, $urlparams = array())
	{
		JLoader::register('PrivacyHelper', JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/privacy.php');

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName   = $this->input->get('view', $this->default_view);
		$vFormat = $document->getType();
		$lName   = $this->input->get('layout', 'default', 'string');

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat))
		{
			$model = $this->getModel($vName);
			$view->setModel($model, true);

			// For the dashboard view, we need to also push the requests model into the view
			if ($vName === 'dashboard')
			{
				$requestsModel = $this->getModel('Requests');

				$view->setModel($requestsModel, false);
			}

			// For the request view, we need to also push the action logs model into the view
			if ($vName === 'request')
			{
				JLoader::register('ActionlogsHelper', JPATH_ADMINISTRATOR . '/components/com_actionlogs/helpers/actionlogs.php');
				JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_actionlogs/models', 'ActionlogsModel');

				$logsModel = $this->getModel('Actionlogs', 'ActionlogsModel');

				// Set default ordering for the context
				$logsModel->setState('list.fullordering', 'a.log_date DESC');

				// And push the model into the view
				$view->setModel($logsModel, false);
			}

			$view->setLayout($lName);

			// Push document object into the view.
			$view->document = $document;

			// Load the submenu.
			PrivacyHelper::addSubmenu($this->input->get('view', $this->default_view));

			$view->display();
		}

		return $this;
	}

	/**
	 * Fetch and report number urgent privacy requests in JSON format, for AJAX requests
	 *
	 * @return void
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function getNumberUrgentRequests()
	{
		$app = Factory::getApplication();

		// Check for a valid token. If invalid, send a 403 with the error message.
		if (!Session::checkToken('get'))
		{
			$app->setHeader('status', 403, true);
			$app->sendHeaders();
			echo new JsonResponse(new \Exception(Text::_('JINVALID_TOKEN'), 403));
			$app->close();
		}

		/** @var PrivacyModelRequests $model */
		$model                = $this->getModel('requests');
		$numberUrgentRequests = $model->getNumberUrgentRequests();

		echo new JResponseJson(array('number_urgent_requests' => $numberUrgentRequests));

		$app->close();
	}
}
