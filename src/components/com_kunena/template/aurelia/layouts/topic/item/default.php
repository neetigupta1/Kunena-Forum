<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

$topic = $this->topic;

$this->addScriptOptions('kunena_topicurl_ajax', "https://kunena.test/j4new/index.php?option=com_kunena&view=topic&catid={$topic->category_id}&id={$topic->id}&format=json");
$this->addScript('assets/js/topic.js');
$this->addScript('assets/js/jquery.animate-colors-min.js');
?>
<div id="ktopic">
	<table class="table table-hover">
		<thead id="tblSomething">
			<tr id="ksubject"></tr>
		</thead>
		<tbody id="tblSomething2">
		</tbody>
	</table>
</div>
<div class="clearfix"></div>
