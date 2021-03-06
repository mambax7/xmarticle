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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author          Mage Gregory (AKA Mage)
 */

use Xmf\Module\Admin;

require_once __DIR__ . '/admin_header.php';

$moduleAdmin = Admin::getInstance();
$moduleAdmin->displayNavigation(basename(__FILE__));
$moduleAdmin->addConfigModuleVersion('system', 212);
// xmdoc
if (is_dir(XOOPS_ROOT_PATH . '/modules/xmdoc')) {
    $moduleAdmin->addConfigModuleVersion('xmdoc', 10);
} elseif (0 == $helper->getConfig('general_xmdoc', 0)) {
    $moduleAdmin->addConfigWarning(_MA_XMARTICLE_INDEXCONFIG_XMDOC_WARNING);
} else {
    $moduleAdmin->addConfigError(_MA_XMARTICLE_INDEXCONFIG_XMDOC_ERROR);
}
$folder[] = $path_logo_category;
$folder[] = $path_logo_article;
foreach (array_keys($folder) as $i) {
    $moduleAdmin->addConfigBoxLine($folder[$i], 'folder');
    $moduleAdmin->addConfigBoxLine([$folder[$i], '777'], 'chmod');
}
$moduleAdmin->displayIndex();

require_once __DIR__ . '/admin_footer.php';
