<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena\Site;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use stdClass;
use function defined;

/**
 * KunenaLayoutUserItem
 *
 * @since   Kunena 5.1
 */
class KunenaLayoutUserEdit extends KunenaLayout
{
	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $profile;

	/**
	 * Method to get tabs for edit profile
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getTabsEdit()
	{
		$myProfile = $this->profile->isMyself() || \Joomla\Component\Kunena\Libraries\User\Helper::getMyself()->isAdmin() || \Joomla\Component\Kunena\Libraries\User\Helper::getMyself()->isModerator();

		// Define all tabs.
		$tabs = [];

		if ($myProfile)
		{
			$tab          = new stdClass;
			$tab->title   = Text::_('COM_KUNENA_PROFILE_EDIT_USER');
			$tab->content = $this->subRequest('User/Edit/User');
			$tab->active  = true;
			$tabs['User'] = $tab;
		}

		if ($myProfile)
		{
			$tab             = new stdClass;
			$tab->title      = Text::_('COM_KUNENA_PROFILE_EDIT_PROFILE');
			$tab->content    = $this->subRequest('User/Edit/Profile');
			$tab->active     = false;
			$tabs['profile'] = $tab;
		}

		if ($myProfile)
		{
			if (config::getInstance()->allowavatarupload || Config::getInstance()->allowavatargallery)
			{
				$tab            = new stdClass;
				$tab->title     = Text::_('COM_KUNENA_PROFILE_EDIT_AVATAR');
				$tab->content   = $this->subRequest('User/Edit/Avatar');
				$tab->active    = false;
				$tabs['avatar'] = $tab;
			}
		}

		if ($myProfile)
		{
			$tab              = new stdClass;
			$tab->title       = Text::_('COM_KUNENA_PROFILE_EDIT_SETTINGS');
			$tab->content     = $this->subRequest('User/Edit/Settings');
			$tab->active      = false;
			$tabs['settings'] = $tab;
		}

		PluginHelper::importPlugin('kunena');

		$plugins = Factory::getApplication()->triggerEvent('onKunenaUserTabsEdit', [$tabs]);

		$tabs = $tabs + $plugins;

		return $tabs;
	}
}
