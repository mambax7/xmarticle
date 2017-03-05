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
$modversion['dirname']     = basename(__DIR__);
$modversion['name']        = ucfirst(basename(__DIR__));
$modversion['version']     = '0.1';
$modversion['description'] = _MI_XMARTICLE_DESC;
$modversion['author']      = 'Grégory Mage (Mage)';
$modversion['url']         = 'https://github.com/GregMage';
$modversion['credits']     = 'Mage';

$modversion['help']        = 'page=help';
$modversion['license']     = 'GNU GPL 2 or later';
$modversion['license_url'] = 'http://www.gnu.org/licenses/gpl-2.0.html';
$modversion['official']    = 0;
$modversion['image']       = 'assets/images/xmarticle_logo.png';

$modversion['hasMain'] = 1;

// Admin things
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';







// Configs
$modversion['config'] = array();
/*
$modversion['config'][] = array(
    'name'        => 'config1',
    'title'       => '_MI_XMFDEMO_CONFIG1',
    'description' => '_MI_XMFDEMO_CONFIG2_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'this is my test config1 value',
);

$modversion['config'][] = array(
    'name'        => 'config2',
    'title'       => '_MI_XMFDEMO_CONFIG2',
    'description' => '_MI_XMFDEMO_CONFIG2_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'this is my test config2 value',
);*/

// About stuff
$modversion['module_status'] = 'Alpha 1';
$modversion['release_date']  = '2017/03/04';

$modversion['developer_lead']      = 'Mage';
$modversion['module_website_url']  = 'github.com/GregMage';
$modversion['module_website_name'] = 'github.com/GregMage';


$modversion['min_xoops'] = '2.5.9';
$modversion['min_php']   = '5.3.7';
