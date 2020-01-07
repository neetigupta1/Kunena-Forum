<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena\Administrator;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use function defined;

/**
 * Kunena Trash Controller
 *
 * @since   Kunena 2.0
 */
class KunenaAdminControllerTrash extends KunenaController
{
	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $baseurl = null;

	/**
	 * Construct
	 *
	 * @param   array  $config  config
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=trash';
	}

	/**
	 * Purge
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function purge()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		$type = $this->input->getCmd('type', 'topics');
		$md5  = $this->input->getString('md5', null);

		if (!empty($cid))
		{
			$this->app->setUserState('com_kunena.purge', $cid);
			$this->app->setUserState('com_kunena.type', $type);
		}
		elseif ($md5)
		{
			$ids  = (array) $this->app->getUserState('com_kunena.purge');
			$type = (string) $this->app->getUserState('com_kunena.type');

			if ($md5 == md5(serialize($ids)))
			{
				if ($type == 'topics')
				{
					$topics = \Joomla\Component\Kunena\Libraries\Forum\Topic\Helper::getTopics($ids, 'none');

					foreach ($topics as $topic)
					{
						$success = $topic->delete();

						if (!$success)
						{
							$this->app->enqueueMessage($topic->getError());
						}
					}

					if ($success)
					{
						\Joomla\Component\Kunena\Libraries\Forum\Topic\Helper::recount($ids);
						\Joomla\Component\Kunena\Libraries\Forum\Category\Helper::recount($topic->getCategory()->id);
						$this->app->enqueueMessage(Text::_('COM_KUNENA_TRASH_DELETE_TOPICS_DONE'));
					}
				}
				elseif ($type == 'messages')
				{
					$messages = KunenaForumMessageHelper::getMessages($ids, 'none');

					foreach ($messages as $message)
					{
						$success = $message->delete();
						$target  = KunenaForumMessageHelper::get($message->id);
						$topic   = \Joomla\Component\Kunena\Libraries\Forum\Topic\Helper::get($target->getTopic());

						if ($topic->attachments > 0)
						{
							$topic->attachments = $topic->attachments - 1;
							$topic->save(false);
						}

						if (!$success)
						{
							$this->app->enqueueMessage($message->getError());
						}
					}

					if ($success)
					{
						\Joomla\Component\Kunena\Libraries\Forum\Topic\Helper::recount($ids);
						\Joomla\Component\Kunena\Libraries\Forum\Category\Helper::recount($topic->getCategory()->id);
						$this->app->enqueueMessage(Text::_('COM_KUNENA_TRASH_DELETE_MESSAGES_DONE'));
					}
				}
			}
			else
			{
				// Error...
			}

			$this->app->setUserState('com_kunena.purge', null);
			$this->app->setUserState('com_kunena.type', null);

			if ($type == 'messages')
			{
				$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($this->baseurl . "&layout=messages", false));
			}
			else
			{
				$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($this->baseurl, false));
			}

			return;
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_MESSAGES_SELECTED'), 'notice');
			$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($this->baseurl . "&layout=purge", false));
	}

	/**
	 * Restore
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function restore()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		$type = $this->input->getCmd('type', 'topics');

		if (empty($cid))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_MESSAGES_SELECTED'), 'notice');
			$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($this->baseurl, false));

			return;
		}

		$nb_items = 0;

		if ($type == 'messages')
		{
			$messages = KunenaForumMessageHelper::getMessages($cid, 'none');

			foreach ($messages as $target)
			{
				if ($target->publish(\Joomla\Component\Kunena\Libraries\Forum\Forum::PUBLISHED))
				{
					$nb_items++;
				}
				else
				{
					$this->app->enqueueMessage($target->getError(), 'notice');
				}
			}
		}
		elseif ($type == 'topics')
		{
			$topics = \Joomla\Component\Kunena\Libraries\Forum\Topic\Helper::getTopics($cid, 'none');

			foreach ($topics as $target)
			{
				if ($target->getState() == \Joomla\Component\Kunena\Libraries\Forum\Forum::UNAPPROVED)
				{
					$status = \Joomla\Component\Kunena\Libraries\Forum\Forum::UNAPPROVED;
				}
				else
				{
					$status = \Joomla\Component\Kunena\Libraries\Forum\Forum::PUBLISHED;
				}

				if ($target->publish($status))
				{
					$nb_items++;
				}
				else
				{
					$this->app->enqueueMessage($target->getError(), 'notice');
				}
			}
		}
		else
		{
			// Error...
		}

		if ($nb_items > 0)
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_TRASH_ITEMS_RESTORE_DONE', $nb_items));
		}

		\Joomla\Component\Kunena\Libraries\User\Helper::recount();
		\Joomla\Component\Kunena\Libraries\Forum\Topic\Helper::recount();
		\Joomla\Component\Kunena\Libraries\Forum\Category\Helper::recount();

		$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to redirect user on cancel on purge page
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function cancel()
	{
		$type = $this->app->getUserState('com_kunena.type');

		if ($type == 'messages')
		{
			$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($this->baseurl . "&layout=messages", false));
		}
		else
		{
			$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_($this->baseurl, false));
		}
	}
}
