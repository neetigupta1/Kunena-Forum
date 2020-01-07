<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Search
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena\Site;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use function defined;

/**
 * Class ComponentKunenaControllerSearchResultsDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerSearchResultsDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Search/Results';

	/**
	 * @var     KunenaModelSearch
	 * @since   Kunena 6.0
	 */
	public $model;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $total;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $data = [];

	/**
	 * Prepare search results display.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/search.php';
		$this->model = new KunenaModelSearch([], $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state = $this->model->getState();

		$this->me               = \Joomla\Component\Kunena\Libraries\User\Helper::getMyself();
		$this->message_ordering = $this->me->getMessageOrdering();

		$this->searchwords = $this->model->getSearchWords();
		$this->isModerator = ($this->me->isAdmin() || \Joomla\Component\Kunena\Libraries\Access::getInstance()->getModeratorStatus());

		$this->results = [];
		$this->total   = $this->model->getTotal();
		$this->results = $this->model->getResults();

		$doc = Factory::getApplication()->getDocument();
		$doc->setMetaData('robots', 'follow, noindex');

		foreach ($doc->_links as $key => $value)
		{
			if (is_array($value))
			{
				if (array_key_exists('relation', $value))
				{
					if ($value['relation'] == 'canonical')
					{
						$canonicalUrl               = \Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_('index.php?option=com_kunena&view=search');
						$doc->_links[$canonicalUrl] = $value;
						unset($doc->_links[$key]);
						break;
					}
				}
			}
		}

		$this->pagination = new KunenaPagination(
			$this->total,
			$this->state->get('list.start'),
			$this->state->get('list.limit')
		);

		$this->error = $this->model->getError();
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareDocument()
	{
		$menu_item = $this->app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle(Text::_('COM_KUNENA_SEARCH_ADVSEARCH'));
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title . ', ' . Text::_('COM_KUNENA_SEARCH_ADVSEARCH') . ', ' . $this->searchwords;
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = Text::_('COM_KUNENA_SEARCH_ADVSEARCH') . ': ' . $this->config->board_title;
				$this->setDescription($description);
			}
		}
	}
}
