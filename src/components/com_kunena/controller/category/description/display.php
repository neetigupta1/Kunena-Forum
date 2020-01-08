<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Category
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena\Site\Controller\Category\Description;

defined('_JEXEC') or die();

use Joomla\Component\Kunena\Libraries\Controller\Display;
use Joomla\Component\Kunena\Libraries\Forum\Category\Helper;
use function defined;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerCategoryDescriptionDisplay extends Display
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Category/Description';

	/**
	 * Prepare category display.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/category.php';

		$catid = $this->input->getInt('catid');

		$this->category = Helper::get($catid);
		$this->category->tryAuthorise();
	}
}
