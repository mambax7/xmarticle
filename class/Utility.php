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

use Xmf\Request;
use XoopsModules\Xmarticle;
use XoopsModules\Xmarticle\Common;

/**
 * Class Utility
 */
class Utility
{
    use Common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use Common\ServerStats; // getServerStats Trait

    use Common\FilesManagement; // Files Management Trait

    //--------------- Custom module methods -----------------------------

    /**
     * @return array
     */
    public static function fieldTypes()
    {
        $types = [
            'label'        => _MA_XMARTICLE_FIELDTYPE_LABEL,
            'vs_text'      => _MA_XMARTICLE_FIELDTYPE_VSTEXT,
            's_text'       => _MA_XMARTICLE_FIELDTYPE_STEXT,
            'm_text'       => _MA_XMARTICLE_FIELDTYPE_MTEXT,
            'l_text'       => _MA_XMARTICLE_FIELDTYPE_LTEXT,
            'text'         => _MA_XMARTICLE_FIELDTYPE_TEXT,
            'select'       => _MA_XMARTICLE_FIELDTYPE_SELECT,
            'select_multi' => _MA_XMARTICLE_FIELDTYPE_SELECTMULTI,
            'radio_yn'     => _MA_XMARTICLE_FIELDTYPE_RADIOYN,
            'radio'        => _MA_XMARTICLE_FIELDTYPE_RADIO,
            'checkbox'     => _MA_XMARTICLE_FIELDTYPE_CHECKBOX,
            'number'       => _MA_XMARTICLE_FIELDTYPE_NUMBER
        ];

        return $types;
    }

    /**
     * @param int $article_id
     * @return bool
     */
    public static function delFilddataArticle($article_id = 0)
    {
        require_once dirname(__DIR__) . '/include/common.php';
        if (0 == $article_id) {
            return false;
            exit();
        }
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('fielddata_aid', $article_id));
        $fielddata_count = $fielddataHandler->getCount($criteria);
        if ($fielddata_count > 0) {
            $fielddataHandler->deleteAll($criteria);
        }

        return true;
    }

    /**
     * @param string $permtype
     * @return array
     */
    public static function getPermissionCat($permtype = 'xmarticle_view')
    {
        global $xoopsUser;
        $categories    = [];
        /** @var \XoopsModules\Xmarticle\Helper $helper */
        $helper = \XoopsModules\Xmarticle\Helper::getInstance();
        $moduleHandler = $helper->getModule();
        $groups        = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $grouppermHandler  = xoops_getHandler('groupperm');
        $categories    = $grouppermHandler->getItemIds($permtype, $groups, $moduleHandler->getVar('mid'));

        return $categories;
    }

    /**
     * @param string $field_type
     * @param int    $fielddata_fid
     * @param int    $fielddata_aid
     * @param string $fielddata_value
     * @param bool|string   $action
     * @return string
     */
    public static function saveFielddata($field_type = '', $fielddata_fid = 0, $fielddata_aid = 0, $fielddata_value = '', $action = null)
    {
        if (null === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }
        if (0 == $fielddata_fid || 0 == $fielddata_aid || '' == $field_type) {
            redirect_header($action, 2, _MA_XMARTICLE_ERROR);
        }
        require_once dirname(__DIR__) . '/include/common.php';
        switch ($field_type) {
            case 'vs_text':
            case 's_text':
            case 'm_text':
            case 'l_text':
            case 'select':
            case 'radio_yn':
            case 'radio':
                $fieldname_bdd = 'fielddata_value1';
                break;

            case 'label':
            case 'text':
                $fieldname_bdd = 'fielddata_value2';
                break;

            case 'select_multi':
            case 'checkbox':
                $fieldname_bdd = 'fielddata_value3';
                break;

            case 'number':
                $fieldname_bdd = 'fielddata_value4';
                break;
        }
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('fielddata_fid', $fielddata_fid));
        $criteria->add(new \Criteria('fielddata_aid', $fielddata_aid));
        $error_message = '';
        if ('select_multi' === $field_type || 'checkbox' === $field_type) {
            $fielddataHandler->deleteAll($criteria);
            foreach (array_keys($fielddata_value) as $i) {
                $obj = $fielddataHandler->create();
                $obj->setVar('fielddata_fid', $fielddata_fid);
                $obj->setVar('fielddata_aid', $fielddata_aid);
                $obj->setVar($fieldname_bdd, $fielddata_value[$i]);
                if ('' == $error_message) {
                    if (!$fielddataHandler->insert($obj)) {
                        $error_message = $obj->getHtmlErrors();
                    }
                }
            }
        } else {
            $fielddata_arr = $fielddataHandler->getAll($criteria);
            if (0 == count($fielddata_arr)) {
                $obj = $fielddataHandler->create();
            } else {
                foreach (array_keys($fielddata_arr) as $i) {
                    $obj = $fielddataHandler->get($fielddata_arr[$i]->getVar('fielddata_id'));
                }
            }
            $obj->setVar('fielddata_fid', $fielddata_fid);
            $obj->setVar('fielddata_aid', $fielddata_aid);
            $obj->setVar($fieldname_bdd, $fielddata_value);
            if ('' == $error_message) {
                if (!$fielddataHandler->insert($obj)) {
                    $error_message = $obj->getHtmlErrors();
                }
            }
        }

        return $error_message;
    }

    /**
     * @param int $fielddata_aid
     * @param int $fielddata_fid
     * @return string
     */
    public static function getFielddata($fielddata_aid = 0, $fielddata_fid = 0)
    {
        require_once dirname(__DIR__) . '/include/common.php';
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('fielddata_aid', $fielddata_aid));
        $criteria->add(new \Criteria('fielddata_fid', $fielddata_fid));
        $fielddata_arr = $fielddataHandler->getAll($criteria);
        $value         = '';
        foreach (array_keys($fielddata_arr) as $i) {
            if ('' != $fielddata_arr[$i]->getVar('fielddata_value1')) {
                $value = $fielddata_arr[$i]->getVar('fielddata_value1');
            }
            if ('' != $fielddata_arr[$i]->getVar('fielddata_value2')) {
                $value = $fielddata_arr[$i]->getVar('fielddata_value2', 'e');
            }
            if ('' != $fielddata_arr[$i]->getVar('fielddata_value3')) {
                if ('' == $value) {
                    $seperator = '';
                } else {
                    $seperator = ',';
                }
                $value .= $seperator . $fielddata_arr[$i]->getVar('fielddata_value3');
            }
            if ('' != $fielddata_arr[$i]->getVar('fielddata_value4')) {
                $value = $fielddata_arr[$i]->getVar('fielddata_value4');
            }
        }

        return $value;
    }

    /**
     * @param array $fields
     * @param int   $fielddata_aid
     * @return array
     */
    public static function getArticleFields($fields = [], $fielddata_aid = 0)
    {
        $values = [];
        if (0 != count($fields)) {
            require_once dirname(__DIR__) . '/include/common.php';
            // field
            $criteria = new \CriteriaCompo();
            $criteria->setSort('field_weight ASC, field_name');
            $criteria->setOrder('ASC');
            $criteria->add(new \Criteria('field_id', '(' . implode(',', $fields) . ')', 'IN'));
            $field_arr = $fieldHandler->getAll($criteria);
            foreach (array_keys($field_arr) as $i) {
                $fielddata_value = '';
                // fielddata
                $criteria = new \CriteriaCompo();
                $criteria->add(new \Criteria('fielddata_fid', $field_arr[$i]->getVar('field_id')));
                $criteria->add(new \Criteria('fielddata_aid', $fielddata_aid));
                $fielddata_arr = $fielddataHandler->getAll($criteria);
                foreach (array_keys($fielddata_arr) as $j) {
                    switch ($field_arr[$i]->getVar('field_type')) {
                        case 'vs_text':
                        case 's_text':
                        case 'm_text':
                        case 'l_text':
                            $fielddata_value = $fielddata_arr[$j]->getVar('fielddata_value1');
                            break;

                        case 'radio_yn':
                            if (0 == $fielddata_arr[$j]->getVar('fielddata_value1')) {
                                $fielddata_value = _NO;
                            } else {
                                $fielddata_value = _YES;
                            }
                            break;

                        case 'select':
                        case 'radio':
                            $fielddata_value = $field_arr[$i]->getVar('field_options')[$fielddata_arr[$j]->getVar('fielddata_value1')];
                            break;

                        case 'label':
                        case 'text':
                            $fielddata_value = $fielddata_arr[$j]->getVar('fielddata_value2', 'e');
                            break;

                        case 'select_multi':
                        case 'checkbox':
                            if ('' == $fielddata_value) {
                                $seperator = '';
                            } else {
                                $seperator = $helper->getConfig('general_separator', '-');
                            }
                            $fielddata_value .= $seperator . $field_arr[$i]->getVar('field_options')[$fielddata_arr[$j]->getVar('fielddata_value3')];
                            break;

                        case 'number':
                            $fielddata_value = $fielddata_arr[$j]->getVar('fielddata_value4');
                            break;
                    }
                }
                $values[] = [$field_arr[$i]->getVar('field_name'), $field_arr[$i]->getVar('field_description'), $fielddata_value];
            }
        }

        return $values;
    }

    /**
     * @param $category_id
     * @param $article_arr
     * @return int
     */
    public static function articlePerCat($category_id, $article_arr)
    {
        $count = 0;
        foreach (array_keys($article_arr) as $i) {
            if ($article_arr[$i]->getVar('article_cid') == $category_id) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param $category_id
     * @return string
     */
    public static function articleNamePerCat($category_id)
    {
        require_once dirname(__DIR__) . '/include/common.php';
        $article_name = '';
        $criteria     = new \CriteriaCompo();
        $criteria->setSort('article_name');
        $criteria->setOrder('ASC');
        $criteria->add(new \Criteria('article_cid', $category_id));
        $article_arr = $articleHandler->getAll($criteria);
        if (count($article_arr) > 0) {
            $article_name .= _MA_XMARTICLE_CATEGORY_WARNINGDELARTICLE . '<br>';
            foreach (array_keys($article_arr) as $i) {
                $article_name .= $article_arr[$i]->getVar('article_name') . '<br>';
            }
        }

        return $article_name;
    }

    /**
     * @param             $article_id
     * @param null|string $action
     * @return mixed
     */
    public static function cloneArticle($article_id, $action = null)
    {
        if (null === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }
        require_once dirname(__DIR__) . '/include/common.php';
        $article = $articleHandler->get($article_id);
        if (0 == count($article)) {
            redirect_header($action, 2, _MA_XMARTICLE_ERROR_NOARTICLE);
        }
        $newobj  = $articleHandler->create();
        $rand_id = mt_rand(1, 10000);
        $newobj->setVar('article_name', _MA_XMARTICLE_CLONE_NAME . $rand_id . '- ' . $article->getVar('article_name'));
        $newobj->setVar('article_reference', $article->getVar('article_reference') . '-' . $rand_id);
        $newobj->setVar('article_description', $article->getVar('article_description', 'e'));
        $newobj->setVar('article_cid', $article->getVar('article_cid'));
        $newobj->setVar('article_userid', !empty($xoopsUser) ? $xoopsUser->getVar('uid') : 0);
        $newobj->setVar('article_date', time());
        $newobj->setVar('article_status', 1);

        return $newobj;
    }
}
