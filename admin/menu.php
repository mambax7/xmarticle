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

use XoopsModules\Xmarticle;

// require_once  dirname(__DIR__) . '/class/Helper.php';
//require_once  dirname(__DIR__) . '/include/common.php';
/** @var Xmarticle\Helper $helper */
$helper = Xmarticle\Helper::getInstance();

$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

// Index
$adminmenu[] = [
    'title' => _MI_XMARTICLE_MENU_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . 'home.png'
];
// Category
$adminmenu[] = [
    'title' => _MI_XMARTICLE_MENU_CATEGORY,
    'link'  => 'admin/category.php',
    'icon'  => $pathIcon32 . 'category.png'
];
// Field
$adminmenu[] = [
    'title' => _MI_XMARTICLE_MENU_FIELD,
    'link'  => 'admin/field.php',
    'icon'  => $pathIcon32 . 'folder_txt.png'
];
// Article
$adminmenu[] = [
    'title' => _MI_XMARTICLE_MENU_ARTICLE,
    'link'  => 'admin/article.php',
    'icon'  => $pathIcon32 . 'view_detailed.png'
];
// Article
$adminmenu[] = [
    'title' => _MI_XMARTICLE_MENU_PERMISSION,
    'link'  => 'admin/permission.php',
    'icon'  => $pathIcon32 . 'permissions.png'
];
// About
$adminmenu[] = [
    'title' => _MI_XMARTICLE_MENU_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . 'about.png'
];
