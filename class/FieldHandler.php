<?php namespace XoopsModules\Xmarticle;

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

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class FieldHandler
 */
class FieldHandler extends \XoopsPersistableObjectHandler
{
    /**
     * FieldHandler constructor.
     * @param null|\XoopsDatabase $db
     */
    public function __construct($db)
    {
        parent::__construct($db, 'xmarticle_field', Field::class, 'field_id', 'field_name');
    }
}
