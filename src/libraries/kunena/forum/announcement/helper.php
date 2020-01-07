<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Announcement
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena\Libraries\Forum\Announcement;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\Exception\ExecutionFailureException;
use function defined;

/**
 * Class AnnouncementHelper
 *
 * @since   Kunena 1.0
 */
abstract class Helper
{
	/**
	 * @var     Announcement[]
	 * @since   Kunena 6.0
	 */
	public static $_instances = false;

	/**
	 * Returns the global Announcement object, only creating it if it doesn't already exist.
	 *
	 * @param   int   $identifier  Announcement to load - Can be only an integer.
	 * @param   bool  $reload      reload
	 *
	 * @return  Announcement
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function get($identifier = null, $reload = false)
	{
		if ($identifier instanceof Announcement)
		{
			return $identifier;
		}

		if (!is_numeric($identifier))
		{
			return new Announcement;
		}

		$id = intval($identifier);

		if (empty(self::$_instances [$id]))
		{
			self::$_instances [$id] = new Announcement(['id' => $id]);
			self::$_instances [$id]->load();
		}
		elseif ($reload)
		{
			self::$_instances [$id]->load();
		}

		return self::$_instances [$id];
	}

	/**
	 * Get url
	 *
	 * @param   string  $layout  layout
	 * @param   bool    $xhtml   xhtml
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public static function getUrl($layout = null, $xhtml = true)
	{
		$uri = self::getUri($layout);

		return \Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * Get uri
	 *
	 * @param   string  $layout  layout
	 *
	 * @return  Uri
	 *
	 * @since   Kunena 6.0
	 */
	public static function getUri($layout = null)
	{
		$uri = new Uri('index.php?option=com_kunena&view=announcement');

		if ($layout)
		{
			$uri->setVar('layout', $layout);
		}

		return $uri;
	}

	/**
	 * Get Announcements
	 *
	 * @param   int   $start   start
	 * @param   int   $limit   limit
	 * @param   bool  $filter  filter
	 *
	 * @return  Announcement[]
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getAnnouncements($start = 0, $limit = 1, $filter = true)
	{
		$db      = Factory::getDBO();
		$nowDate = $db->quote(Factory::getDate()->toSql());

		if ($filter)
		{
			$query = $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__kunena_announcement'))
				->where($db->quoteName('published') . ' = 1')
				->andWhere($db->quoteName('publish_up') . ' <= ' . $nowDate)
				->andWhere($db->quoteName('publish_down') . ' =' . $db->quote('1000-01-01 00:00:00') . ' OR ' . $db->quoteName('publish_down') . ' <= ' . $nowDate)
				->order($db->quoteName('id') . ' DESC');
		}
		else
		{
			$query = $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__kunena_announcement'))
				->order($db->quoteName('id') . ' DESC');
		}

		$query->setLimit($limit, $start);
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadAssocList();
		}
		catch (ExecutionFailureException $e)
		{
			\Joomla\Component\Kunena\Libraries\Error::displayDatabaseError($e);
		}

		self::$_instances = [];
		$list             = [];

		foreach ($results as $announcement)
		{
			if (isset(self::$_instances [$announcement['id']]))
			{
				continue;
			}

			$instance = new Announcement($announcement);
			$instance->exists(true);
			self::$_instances [$instance->id] = $instance;
			$list[]                           = $instance;
		}

		unset($results);

		return $list;
	}

	/**
	 * Get Count
	 *
	 * @param   bool  $filter  filter
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getCount($filter = true)
	{
		$db       = Factory::getDBO();
		$nullDate = $db->getNullDate() ? $db->quote($db->getNullDate()) : 'NULL';
		$nowDate  = $db->quote(Factory::getDate()->toSql());

		if ($filter)
		{
			$query = $db->getQuery(true)
				->select('COUNT(*)')
				->from($db->quoteName('#__kunena_announcement'))
				->where($db->quoteName('published') . ' = 1')
				->andWhere($db->quoteName('publish_up') . '  = ' . $nullDate . ' OR ' . $db->quoteName('publish_up') . ' <= ' . $nowDate)
				->andWhere($db->quoteName('publish_down') . ' = ' . $nullDate . ' OR ' . $db->quoteName('publish_down') . ' >= ' . $nowDate)
				->order('id DESC');
		}
		else
		{
			$query = $db->getQuery(true)
				->select('COUNT(*)')
				->from($db->quoteName('#__kunena_announcement'))
				->order('id DESC');
		}

		$db->setQuery($query);

		try
		{
			$total = (int) $db->loadResult();
		}
		catch (ExecutionFailureException $e)
		{
			\Joomla\Component\Kunena\Libraries\Error::displayDatabaseError($e);
		}

		return $total;
	}

	/**
	 * Free up memory by cleaning up all cached items.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public static function cleanup()
	{
		self::$_instances = [];
	}
}
