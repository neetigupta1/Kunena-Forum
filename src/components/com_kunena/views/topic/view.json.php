<?php
/**
 * Kunena Latest Json
 *
 * @package       Kunena.json_kunenalatest
 *
 * @copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Joomla\Component\Kunena\Site;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;

use stdClass;
use function defined;

/**
 *
 * @since   Kunena 6.0
 */
class KunenaViewTopic extends KunenaView
{
	/**
	 * @param   null  $tpl tmpl
	 *
	 * @return  mixed|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$id                        = Factory::getApplication()->input->getInt('id');
		$topic                     = \Joomla\Component\Kunena\Libraries\Forum\Topic\Helper::get($id);
		$topic->subject            = \Joomla\Component\Kunena\Libraries\Html\Parser::parseText($topic->subject);
		$topic->first_post_message = \Joomla\Component\Kunena\Libraries\Html\Parser::stripBBCode($topic->first_post_message);
		$topic->last_post_message  = \Joomla\Component\Kunena\Libraries\Html\Parser::stripBBCode($topic->last_post_message);
		$messages                  = KunenaForumMessageHelper::getMessagesByTopic($topic, 0, $topic->posts);

		$list     = [];
		$template = \Joomla\Component\Kunena\Libraries\KunenaFactory::getTemplate();

		foreach ($messages as $message)
		{
			$user              = \Joomla\Component\Kunena\Libraries\User\Helper::get($message->userid);
			$response          = new stdClass;
			$response->id      = $message->id;
			$response->message = \Joomla\Component\Kunena\Libraries\Html\Parser::stripBBCode(KunenaForumMessageHelper::get($message->id)->message);
			$response->author  = $user->username;
			$response->avatar  = $user->getAvatarImage($template->params->get('avatarType'), 'thumb');
			$response->rank    = $user->getRank($topic->getCategory()->id, 'title');
			$response->time    = KunenaDate::getInstance($message->time)->toKunena('config_post_dateformat');

			$list[] = $response;
		}

		$json2 = [
			'Count'    => $topic,
			'Messages' => $list,
		];

		$json = json_encode($json2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		echo $json;
	}
}
