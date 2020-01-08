<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena\Site\Controller\Widget\Statistics;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\Component\Kunena\Libraries\Controller\Display;
use Joomla\Component\Kunena\Libraries\Exception\Authorise;
use Joomla\Component\Kunena\Libraries\Forum\Statistics;
use Joomla\Component\Kunena\Libraries\KunenaFactory;
use function defined;

/**
 * Class ComponentKunenaControllerWidgetStatisticsDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerWidgetStatisticsDisplay extends Display
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Widget/Statistics';

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $config;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $latestMemberLink;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $statisticsUrl;

	/**
	 * Prepare statistics box display.
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	protected function before()
	{
		parent::before();

		$this->config = Config::getInstance();

		if (!$this->config->get('showstats'))
		{
			throw new Authorise(Text::_('COM_KUNENA_NO_ACCESS'), '404');
		}

		$statistics = Statistics::getInstance();
		$statistics->loadGeneral();
		$this->setProperties($statistics);

		$this->latestMemberLink = KunenaFactory::getUser(intval($this->lastUserId))->getLink(null, null, '');
		$this->statisticsUrl    = KunenaFactory::getProfile()->getStatisticsURL();

		return true;
	}
}
