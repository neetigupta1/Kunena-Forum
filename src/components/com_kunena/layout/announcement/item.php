<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Announcement.List
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena\Site;

defined('_JEXEC') or die;

use Exception;
use function defined;

/**
 * KunenaLayoutAnnouncementItem
 *
 * @since   Kunena 4.0
 */
class KunenaLayoutAnnouncementItem extends KunenaLayout
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $buttons;

	/**
	 * @var     KunenaForumAnnouncement
	 * @since   Kunena 6.0
	 */
	public $announcement;

	/**
	 * Method to get moderation actions for announcements
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getActions()
	{
		$this->buttons = [];

		if ($this->announcement->isAuthorised('edit'))
		{
			$this->buttons['edit'] = $this->getButton($this->announcement->getUri('edit'), 'edit', 'announcement', 'moderation');
		}

		if ($this->announcement->isAuthorised('delete'))
		{
			$this->buttons['delete'] = $this->getButton($this->announcement->getTaskUri('delete'), 'delete', 'announcement', 'permanent');
		}

		if ($this->buttons)
		{
			$this->buttons['cpanel'] = $this->getButton(KunenaForumAnnouncementHelper::getUri('list'), 'list', 'announcement', 'communication');
		}

		return $this->buttons;
	}

	/**
	 * Get button.
	 *
	 * @param   string  $url     Target link (do not route it).
	 * @param   string  $name    Name of the button.
	 * @param   string  $scope   Scope of the button.
	 * @param   string  $type    Type of the button.
	 * @param   int     $id      Id of the button.
	 * @param   bool    $normal  Define if the button will have the class btn or btn-small
	 *
	 * @return  KunenaLayout
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getButton($url, $name, $scope, $type, $id = null, $normal = true)
	{
		return KunenaLayout::factory('Widget/Announcement/Button')
			->setProperties(['url' => \Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($url), 'name' => $name, 'scope' => $scope, 'type' => $type, 'id' => $id, 'normal' => $normal]);
	}
}
