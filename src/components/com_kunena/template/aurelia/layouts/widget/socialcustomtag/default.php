<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Joomla\Component\Kunena\Site;

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use function defined;

if (\Joomla\Component\Kunena\Libraries\User\Helper::getMyself()->socialshare == 0 && \Joomla\Component\Kunena\Libraries\User\Helper::getMyself()->exists())
{
	return false;
}

$this->ktemplate = \Joomla\Component\Kunena\Libraries\KunenaFactory::getTemplate();
$socialsharetag  = $this->ktemplate->params->get('socialsharetag');

echo HTMLHelper::_('content.prepare', $socialsharetag);
