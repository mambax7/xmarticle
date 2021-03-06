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
use Xmf\Request;
use  XoopsModules\Xmarticle;

require_once __DIR__ . '/admin_header.php';
require_once XOOPS_ROOT_PATH.'/class/xoopsform/grouppermform.php';
$moduleAdmin = Admin::getInstance();
$moduleAdmin->displayNavigation('permission.php');

// Get permission
$permission = Request::getInt('permission', 1);
$tab_perm   = [1 => _MA_XMARTICLE_PERMISSION_VIEW, 2 => _MA_XMARTICLE_PERMISSION_SUBMIT, 3 => _MA_XMARTICLE_PERMISSION_OTHER];

// Category
$criteria = new \CriteriaCompo();
$category_arr = $categoryHandler->getAll($criteria);
if (count($category_arr) > 0) {
    $tab_perm = [1 => _MA_XMARTICLE_PERMISSION_VIEW, 2 => _MA_XMARTICLE_PERMISSION_SUBMIT, 3 => _MA_XMARTICLE_PERMISSION_OTHER];
} else {
    $tab_perm = [3 => _MA_XMARTICLE_PERMISSION_VIEW];
    $permission = 3;
}
$permission_options = '';
foreach (array_keys($tab_perm) as $i) {
    $permission_options .= '<option value="' . $i . '"' . ($permission == $i ? ' selected="selected"' : '') . '>' . $tab_perm[$i] . '</option>';
}
$xoopsTpl->assign('permission_options', $permission_options);

switch ($permission) {
    case 1:    // View permission
        $formTitle = _MA_XMARTICLE_PERMISSION_VIEW;
        $permissionName = 'xmarticle_view';
        $permissionDescription = _MA_XMARTICLE_PERMISSION_VIEW_DSC;
        foreach (array_keys($category_arr) as $i) {
            $global_perms_array[$i] = $category_arr[$i]->getVar('category_name');
        }
        break;

    case 2:    // Submit permission
        $formTitle = _MA_XMARTICLE_PERMISSION_SUBMIT;
        $permissionName = 'xmarticle_submit';
        $permissionDescription = _MA_XMARTICLE_PERMISSION_SUBMIT_DSC;
        foreach (array_keys($category_arr) as $i) {
            $global_perms_array[$i] = $category_arr[$i]->getVar('category_name');
        }
        break;

    case 3:    // Other permission
        $formTitle = _MA_XMARTICLE_PERMISSION_OTHER;
        $permissionName = 'xmarticle_other';
        $permissionDescription = _MA_XMARTICLE_PERMISSION_OTHER_DSC;
        $global_perms_array    = [
            '4' => _MA_XMARTICLE_PERMISSION_OTHER_4 ,
            '8' => _MA_XMARTICLE_PERMISSION_OTHER_8,
            '16' => _MA_XMARTICLE_PERMISSION_OTHER_16,
            '32' => _MA_XMARTICLE_PERMISSION_OTHER_32
        ];
        break;
}

$permissionsForm = new \XoopsGroupPermForm($formTitle, $helper->getModule()->getVar('mid'), $permissionName, $permissionDescription, 'admin/permission.php?permission=' . $permission);
foreach ($global_perms_array as $perm_id => $permissionName) {
    $permissionsForm->addItem($perm_id, $permissionName) ;
}
$xoopsTpl->assign('form', $permissionsForm->render());

$xoopsTpl->display('db:xmarticle_admin_permission.tpl');

require_once __DIR__ . '/admin_footer.php';
