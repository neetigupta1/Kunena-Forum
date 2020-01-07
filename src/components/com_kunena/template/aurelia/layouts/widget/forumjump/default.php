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

use Joomla\CMS\Factory;
use function defined;

$catid = Factory::getApplication()->input->getInt('catid', 0);
?>
<script>
	jQuery(function ($) {
		$("#jumpto option[value=<?php echo $catid;?>]").prop("selected", "selected");
	})
</script>
<form action="<?php echo \Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_('index.php?option=com_kunena'); ?>" id="jumpto" name="jumpto" method="post"
	  target="_self">
	<input type="hidden" name="view" value="category"/>
	<input type="hidden" name="task" value="jump"/>
	<span><?php echo $this->categorylist; ?></span>
</form>
