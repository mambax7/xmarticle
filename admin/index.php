<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * xmarticle module
 *
 * @copyright       XOOPS Project (http://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author          Mage Gregory (AKA Mage)
 */
require __DIR__ . '/header.php';

// header
xoops_cp_header();

$xoopsTpl->assign('navigation', $admin_class->addNavigation('index.php'));
$xoopsTpl->assign('renderindex', $admin_class->renderIndex());

// Call template file
$xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/xmarticle/templates/admin/xmarticle_admin_index.tpl');

xoops_cp_footer();
