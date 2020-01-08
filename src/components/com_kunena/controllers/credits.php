<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controllers;

defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Controller;
use function defined;

/**
 * Kunena Credits Controller
 *
 * @since   Kunena 2.0
 */
class KunenaControllerCredits extends Controller
{
	/**
	 * @param   array  $config  config
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
	}
}
