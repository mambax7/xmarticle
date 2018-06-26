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

use XoopsModules\Xmarticle;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class Article
 */
class Article extends \XoopsObject
{
    
    // constructor
    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->initVar('article_id', XOBJ_DTYPE_INT, null);
        $this->initVar('article_cid', XOBJ_DTYPE_INT, null);
        $this->initVar('article_reference', XOBJ_DTYPE_TXTBOX, null);
        $this->initVar('article_name', XOBJ_DTYPE_TXTBOX, null);
        $this->initVar('article_description', XOBJ_DTYPE_TXTAREA);
        $this->initVar('article_logo', XOBJ_DTYPE_TXTBOX, null);
        $this->initVar('article_userid', XOBJ_DTYPE_INT, 0);
        $this->initVar('article_date', XOBJ_DTYPE_INT, 0);
        $this->initVar('article_mdate', XOBJ_DTYPE_INT, 0);
        $this->initVar('article_status', XOBJ_DTYPE_INT, 0);
        $this->initVar('category_name', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('category_reference', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('category_fields', XOBJ_DTYPE_ARRAY, []);
    }

    /**
     * @param bool $action
     * @return XoopsThemeForm
     */
    public function getFormCategory($action = false)
    {
        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        require_once dirname(__DIR__) . '/include/common.php';

        // Get Permission to submit
        $submitPermissionCat = Xmarticle\Utility::getPermissionCat('xmarticle_submit');
        
        $form = new \XoopsThemeForm(_MA_XMARTICLE_ADD, 'form', $action, 'post', true);
        // type
        $field_cat = new \XoopsFormSelect(_MA_XMARTICLE_ARTICLE_CATEGORY, 'article_cid', $this->getVar('article_cid'));
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('category_status', 1));
        $criteria->setSort('category_weight ASC, category_name');
        $criteria->setOrder('ASC');
        if (!empty($submitPermissionCat)) {
            $criteria->add(new \Criteria('category_id', '(' . implode(',', $submitPermissionCat) . ')', 'IN'));
        }
        $category_arr = $categoryHandler->getAll($criteria);
        if (0 == count($category_arr) || empty($submitPermissionCat)) {
            redirect_header($action, 3, _MA_XMARTICLE_ERROR_NOACESSCATEGORY);
        }
        foreach (array_keys($category_arr) as $i) {
            $field_cat->addOption($category_arr[$i]->getVar('category_id'), $category_arr[$i]->getVar('category_name'));
        }
        $form->addElement($field_cat, true);
        $form->addElement(new \XoopsFormHidden('op', 'loadarticle'));
        // submit
        $form->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }

    /**
     * @param int  $article_cid
     * @param int  $old_article_cid
     * @param bool $action
     * @return XoopsThemeForm
     */
    public function getForm($article_cid = 0, $old_article_cid = 0, $action = false)
    {
        global $xoopsUser;
        
        $upload_size = 500000;
        /** @var \XoopsModules\Xmarticle\Helper $helper */
        $helper = \XoopsModules\Xmarticle\Helper::getInstance();
        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        require_once dirname(__DIR__) . '/include/common.php';
        
        //form title
        $title = $this->isNew() ? sprintf(_MA_XMARTICLE_ADD) : sprintf(_MA_XMARTICLE_EDIT);
        
        $form = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        
        if (!$this->isNew()) {
            $form->addElement(new \XoopsFormHidden('article_id', $this->getVar('article_id')));
            $status = $this->getVar('article_status');
            $article_cid = $this->getVar('article_cid');
            $article_cid_fielddata = $this->getVar('article_id');
        } else {
            $status = 1;
            if (0 != $old_article_cid) {
                $article_cid_fielddata = $old_article_cid;
            } else {
                $article_cid_fielddata = 0;
                //echo 'ici';
            }
        }
        // category
        $category = $categoryHandler->get($article_cid);
        $form->addElement(new xoopsFormLabel(_MA_XMARTICLE_ARTICLE_CATEGORY, $category->getVar('category_name')));
        $form->addElement(new \XoopsFormHidden('article_cid', $article_cid));

        // title
        $form->addElement(new \XoopsFormText(_MA_XMARTICLE_ARTICLE_NAME, 'article_name', 50, 255, $this->getVar('article_name')), true);
        
        // reference
        $reference = new \XoopsFormElementTray(_MA_XMARTICLE_ARTICLE_REFERENCE);
        $category_reference = new xoopsFormLabel($category->getVar('category_reference'));
        $reference->addElement($category_reference);
        $reference->addElement(new \XoopsFormText('', 'article_reference', 20, 50, $this->getVar('article_reference')));
        $form->addElement($reference, true);

        // description
        $editor_configs           = [];
        $editor_configs['name']   = 'article_description';
        $editor_configs['value']  = $this->getVar('article_description', 'e');
        $editor_configs['rows']   = 20;
        $editor_configs['cols']   = 160;
        $editor_configs['width']  = '100%';
        $editor_configs['height'] = '400px';
        $editor_configs['editor'] = $helper->getConfig('admin_editor', 'Plain Text');
        $form->addElement(new \XoopsFormEditor(_MA_XMARTICLE_ARTICLE_DESC, 'article_description', $editor_configs), false);
        // logo
        $blank_img = $this->getVar('article_logo') ?: 'blank.gif';
        $uploadirectory='/uploads/xmarticle/images/article';
        $imgtray_img     = new \XoopsFormElementTray(_MA_XMARTICLE_ARTICLE_LOGOFILE . '<br><br>' . sprintf(_MA_XMARTICLE_ARTICLE_UPLOADSIZE, $upload_size / 1000), '<br>');
        $imgpath_img     = sprintf(_MA_XMARTICLE_ARTICLE_FORMPATH, $uploadirectory);
        $imageselect_img = new \XoopsFormSelect($imgpath_img, 'article_logo', $blank_img);
        $image_array_img = \XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . $uploadirectory);
        $imageselect_img->addOption($blank_img, $blank_img);
        foreach ($image_array_img as $image_img) {
            $imageselect_img->addOption((string)$image_img, $image_img);
        }
        $imageselect_img->setExtra("onchange='showImgSelected(\"image_img2\", \"article_logo\", \"" . $uploadirectory . '", "", "' . XOOPS_URL . "\")'");
        $imgtray_img->addElement($imageselect_img, false);
        $imgtray_img->addElement(new \XoopsFormLabel('', "<br><img src='" . XOOPS_URL . '/' . $uploadirectory . '/' . $blank_img . "' name='image_img2' id='image_img2' alt=''>"));
        $fileseltray_img = new \XoopsFormElementTray('<br>', '<br><br>');
        $fileseltray_img->addElement(new \XoopsFormFile(_MA_XMARTICLE_ARTICLE_UPLOAD, 'article_logo', $upload_size), false);
        $fileseltray_img->addElement(new \XoopsFormLabel(''), false);
        $imgtray_img->addElement($fileseltray_img);
        $form->addElement($imgtray_img);
        
        //xmdoc
        if (xoops_isActiveModule('xmdoc') && 1 == $helper->getConfig('general_xmdoc', 0)) {
            xoops_load('utility', 'xmdoc');
            XmdocUtility::renderDocForm($form, 'xmarticle', $this->getVar('category_id'));
        }
        
        // field
        $criteria = new \CriteriaCompo();
        $criteria->setSort('field_weight ASC, field_name');
        $criteria->setOrder('ASC');
        $criteria->add(new \Criteria('field_id', '(' . implode(',', $category->getVar('category_fields')) . ')', 'IN'));
        $criteria->add(new \Criteria('field_status', 0, '!='));
        $field_arr = $fieldHandler->getAll($criteria);
        foreach (array_keys($field_arr) as $i) {
            $caption = $field_arr[$i]->getVar('field_name') . '<br><span style="font-weight:normal;">' . $field_arr[$i]->getVar('field_description', 'show') . '</span>';
            if (1 == $field_arr[$i]->getVar('field_required')) {
                $required = true;
            } else {
                $required = false;
            }
            $value = Xmarticle\Utility::getFielddata($article_cid_fielddata, $field_arr[$i]->getVar('field_id'));
            if ('' == $value) {
                if ('text' === $field_arr[$i]->getVar('field_type')) {
                    $value = $field_arr[$i]->getVar('field_default', 'e');
                } elseif ('select_multi' === $field_arr[$i]->getVar('field_type') || 'checkbox' === $field_arr[$i]->getVar('field_type')) {
                    if ('' != $field_arr[$i]->getVar('field_default', 'n')) {
                        $value =  implode(',', array_flip(unserialize($field_arr[$i]->getVar('field_default', 'n'))));
                    }
                } else {
                    $value = $field_arr[$i]->getVar('field_default');
                }
            }
            $name = 'field_' . $i;
            switch ($field_arr[$i]->getVar('field_type')) {
                case 'label':
                    $form->addElement(new \XoopsFormLabel($caption, $value, $name), $required);
                    $form->addElement(new \XoopsFormHidden($name, $value));
                    break;
                case 'vs_text':
                    $form->addElement(new \XoopsFormText($caption, $name, 50, 25, $value), $required);
                    break;
                case 's_text':
                    $form->addElement(new \XoopsFormText($caption, $name, 50, 50, $value), $required);
                    break;
                case 'm_text':
                    $form->addElement(new \XoopsFormText($caption, $name, 50, 100, $value), $required);
                    break;
                case 'l_text':
                    $form->addElement(new \XoopsFormText($caption, $name, 50, 255, $value), $required);
                    break;
                case 'text':
                    $editor_configs           = [];
                    $editor_configs['name']   = $name;
                    $editor_configs['value']  = $value;
                    $editor_configs['rows']   = 2;
                    $editor_configs['editor'] = 'Plain Text';
                    $form->addElement(new \XoopsFormEditor($caption, $name, $editor_configs), $required);
                    break;
                case 'select':
                    $select_field = new \XoopsFormSelect($caption, $name, $value);
                    $select_field ->addOptionArray($field_arr[$i]->getVar('field_options'));
                    $form->addElement($select_field, $required);
                    break;
                case 'select_multi':
                    $select_multi_field = new \XoopsFormSelect($caption, $name, explode(',', $value), 5, true);
                    $select_multi_field ->addOptionArray($field_arr[$i]->getVar('field_options'));
                    $form->addElement($select_multi_field, $required);
                    break;
                case 'radio_yn':
                    $form->addElement(new \XoopsFormRadioYN($caption, $name, $value), $required);
                    break;
                case 'radio':
                    $radio_field = new \XoopsFormRadio($caption, $name, $value);
                    $radio_field ->addOptionArray($field_arr[$i]->getVar('field_options'));
                    $form->addElement($radio_field, $required);
                    break;
                case 'checkbox':
                    $checkbox_field = new \XoopsFormCheckBox($caption, $name, explode(',', $value));
                    $checkbox_field ->addOptionArray($field_arr[$i]->getVar('field_options'));
                    $form->addElement($checkbox_field, $required);
                    break;
                case 'number':
                    $form->addElement(new \XoopsFormText($caption, $name, 15, 50, $value), $required);
                    break;
            }
            unset($value);
        }
        if (true === $helper->isUserAdmin()) {
            if ($this->isNew()) {
                $userid = !empty($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
            } else {
                $userid = $this->getVar('article_userid');
            }
            // userid
            $form->addElement(new \XoopsFormSelectUser(_MA_XMARTICLE_USERID, 'article_userid', true, $userid, 1, false), true);
            
            // date and mdate
            if (!$this->isNew()) {
                $selection_date = new \XoopsFormElementTray(_MA_XMARTICLE_DATEUPDATE);
                $date = new \XoopsFormRadio('', 'date_update', 'N');
                $options        = ['N' => _NO . ' (' . formatTimestamp($this->getVar('article_date'), 's') . ')', 'Y' => _YES];
                $date->addOptionArray($options);
                $selection_date->addElement($date);
                $selection_date->addElement(new \XoopsFormTextDateSelect('', 'article_date', '', time()));
                $form->addElement($selection_date);
                if (0 != $this->getVar('article_mdate')) {
                    $selection_mdate = new \XoopsFormElementTray(_MA_XMARTICLE_MDATEUPDATE);
                    $mdate = new \XoopsFormRadio('', 'mdate_update', 'N');
                    $options         = ['N' => _NO . ' (' . formatTimestamp($this->getVar('article_mdate'), 's') . ')', 'R' => _MA_XMARTICLE_RESETMDATE, 'Y' => _YES];
                    $mdate->addOptionArray($options);
                    $selection_mdate->addElement($mdate);
                    $selection_mdate->addElement(new \XoopsFormTextDateSelect('', 'article_mdate', '', time()));
                    $form->addElement($selection_mdate);
                }
            }
        }
        // permission Auto approve submitted article
        $permHelper = new \Xmf\Module\Helper\Permission();
        $permission = $permHelper->checkPermission('xmarticle_other', 8);
        if (true === $permission || true === $helper->isUserAdmin()) {
            // status
            $form_status = new \XoopsFormRadio(_MA_XMARTICLE_STATUS, 'article_status', $status);
            $options     = [1 => _MA_XMARTICLE_STATUS_A, 0 => _MA_XMARTICLE_STATUS_NA, 2 => _MA_XMARTICLE_WFV];
            $form_status->addOptionArray($options);
            $form->addElement($form_status);
        }
        //captcha
        if (1 == $helper->getConfig('general_captcha', 0)) {
            $form->addElement(new \XoopsFormCaptcha(), true);
        }

        $form->addElement(new \XoopsFormHidden('op', 'save'));
        // submit
        $form->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }

    /**
     * @param      $articleHandler
     * @param bool $action
     * @return mixed
     */
    public function saveArticle($articleHandler, $action = false)
    {
        global $xoopsUser;
        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }
        require_once dirname(__DIR__) . '/include/common.php';
        $error_message = '';
        //logo
        $uploadirectory = '/xmarticle/images/article';
        if (UPLOAD_ERR_NO_FILE != $_FILES['article_logo']['error']) {
            require_once XOOPS_ROOT_PATH . '/class/uploader.php';
            $uploader_article_img = new \XoopsMediaUploader(XOOPS_UPLOAD_PATH . $uploadirectory, ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png'], $upload_size, null, null);
            if ($uploader_article_img->fetchMedia('article_logo')) {
                $uploader_article_img->setPrefix('article_');
                if (!$uploader_article_img->upload()) {
                    $error_message .= $uploader_article_img->getErrors() . '<br>';
                } else {
                    $this->setVar('article_logo', $uploader_article_img->getSavedFileName());
                }
            } else {
                $error_message .= $uploader_article_img->getErrors();
            }
        } else {
            $this->setVar('article_logo', Xmf\Request::getString('article_logo', ''));
        }
        $this->setVar('article_name', Xmf\Request::getString('article_name', ''));
        $this->setVar('article_reference', Xmf\Request::getString('article_reference', ''));
        $this->setVar('article_description', Xmf\Request::getText('article_description', ''));
        $article_cid = Xmf\Request::getInt('article_cid', 0);
        $this->setVar('article_cid', $article_cid);
        if (\Xmf\Request::hasVar('article_userid', 'POST')) {
            $this->setVar('article_userid', Xmf\Request::getInt('article_userid', 0));
        } else {
            $this->setVar('article_userid', !empty($xoopsUser) ? $xoopsUser->getVar('uid') : 0);
        }
        if (\Xmf\Request::hasVar('article_date', 'POST')) {
            if ('Y' === $_POST['date_update']) {
                $this->setVar('article_date', strtotime(Xmf\Request::getString('article_date', '')));
            }
            $this->setVar('article_mdate', time());
        } else {
            $this->setVar('article_date', time());
        }
        if (\Xmf\Request::hasVar('article_mdate', 'POST')) {
            if ('Y' === $_POST['mdate_update']) {
                $this->setVar('article_mdate', strtotime(Xmf\Request::getString('article_mdate', '')));
            }
            if ('R' === $_POST['mdate_update']) {
                $this->setVar('article_mdate', 0);
            }
        }
        // permission Auto approve submitted article
        $permHelper = new \Xmf\Module\Helper\Permission();
        $permission = $permHelper->checkPermission('xmarticle_other', 8);
        if (false === $permission) {
            $this->setVar('article_status', 2);
        } else {
            $this->setVar('article_status', Xmf\Request::getInt('article_status', 1));
        }
        // Captcha
        if (1 == $helper->getConfig('general_captcha')) {
            xoops_load('xoopscaptcha');
            $xoopsCaptcha = XoopsCaptcha::getInstance();
            if (! $xoopsCaptcha->verify()) {
                $error_message .= $xoopsCaptcha->getMessage();
            }
        }
        if ('' == $error_message) {
            if ($articleHandler->insert($this)) {
                // fields and fielddata
                $category = $categoryHandler->get($article_cid);
                $criteria = new \CriteriaCompo();
                $criteria->setSort('field_weight ASC, field_name');
                $criteria->setOrder('ASC');
                $criteria->add(new \Criteria('field_id', '(' . implode(',', $category->getVar('category_fields')) . ')', 'IN'));
                $criteria->add(new \Criteria('field_status', 0, '!='));
                $field_arr = $fieldHandler->getAll($criteria);
                if (0 == $this->get_new_enreg()) {
                    $fielddata_aid = $this->getVar('article_id');
                } else {
                    $fielddata_aid = $this->get_new_enreg();
                }
                foreach (array_keys($field_arr) as $i) {
                    $error_message .= Xmarticle\Utility::saveFielddata($field_arr[$i]->getVar('field_type'), $field_arr[$i]->getVar('field_id'), $fielddata_aid, $_POST['field_' . $i]);
                }
                //xmdoc
                if (xoops_isActiveModule('xmdoc') && 1 == $helper->getConfig('general_xmdoc', 0)) {
                    xoops_load('utility', 'xmdoc');
                    $error_message .= XmdocUtility::saveDocuments('xmarticle', $fielddata_aid);
                }
                if ('' == $error_message) {
                    if ('viewarticle.php' === $action) {
                        redirect_header('viewarticle.php?category_id=' . $article_cid . '&article_id=' . $fielddata_aid, 2, _MA_XMARTICLE_REDIRECT_SAVE);
                    } else {
                        redirect_header($action, 2, _MA_XMARTICLE_REDIRECT_SAVE);
                    }
                }
            } else {
                $error_message =  $this->getHtmlErrors();
            }
        }

        return $error_message;
    }

    /**
     * @return mixed
     */
    public function get_new_enreg()
    {
        global $xoopsDB;
        $newEnreg = $xoopsDB->getInsertId();

        return $newEnreg;
    }
}
