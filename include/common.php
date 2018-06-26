<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

use Xmf\Module\Admin;
use XoopsModules\Xmarticle;
/**
 * xmarticle module
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author          Mage Gregory (AKA Mage)
 */

class_exists(Admin::class) || die('XMF is required.');

include dirname(__DIR__) . '/preloads/autoloader.php';

/** @var \XoopsModules\Xmarticle\Helper $helper */
$helper  = \XoopsModules\Xmarticle\Helper::getInstance();

// Get handler
$categoryHandler  = $helper->getHandler('Category');
$fieldHandler     = $helper->getHandler('Field');
$fielddataHandler = $helper->getHandler('Fielddata');
$articleHandler   = $helper->getHandler('Article');
