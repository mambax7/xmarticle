﻿<?php
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

if (!defined('XOOPS_ROOT_PATH')) {
    die('XOOPS root path not defined');
}

/**
 * Class xmarticle_article
 */
class xmarticle_article extends XoopsObject
{   
    
    // constructor
    /**
     * xmarticle_article constructor.
     */
    public function __construct()
    {
        $this->initVar('article_id', XOBJ_DTYPE_INT, null);
        $this->initVar('article_cid', XOBJ_DTYPE_INT, null);
        $this->initVar('article_reference', XOBJ_DTYPE_TXTBOX, null);
        $this->initVar('article_name', XOBJ_DTYPE_TXTBOX, null);        
        $this->initVar('article_description', XOBJ_DTYPE_TXTAREA);
        $this->initVar('article_logo', XOBJ_DTYPE_TXTBOX, null);
        $this->initVar('article_status', XOBJ_DTYPE_INT, 0);
        $this->initVar('category_name',XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('category_fields', XOBJ_DTYPE_ARRAY, array());
    }
    /**
     * @param bool $action
     * @return XoopsThemeForm
     */
    public function getFormCategory($action = false)
    {
        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }
        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';   
        
        $form = new XoopsThemeForm(_MA_XMARTICLE_ADD, 'form', $action, 'post', true);
        // type        
        $field_type = new XoopsFormSelect(_MA_XMARTICLE_ARTICLE_CATEGORY, 'article_cid', $this->getVar('article_cid'));
        
        $categoryHandler = xoops_getModuleHandler('xmarticle_category', 'xmarticle');
        $criteria = new CriteriaCompo();
        $criteria->setSort('category_weight ASC, category_name');
        $criteria->setOrder('ASC');
        $category_arr = $categoryHandler->getall($criteria);
        foreach (array_keys($category_arr) as $i) {
            $field_type->addOption($category_arr[$i]->getVar('category_id'), $category_arr[$i]->getVar('category_name'));
        }
        $form->addElement($field_type, true);   
        $form->addElement(new XoopsFormHidden('op', 'loadarticle'));        
        // submit
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        return $form;
    } 
    /**
    * @param bool $action
    * @return XoopsThemeForm
    */
    public function getForm($article_cid = 0, $action = false)
    {
        $upload_size = 500000;
        $helper = \Xmf\Module\Helper::getHelper('xmarticle');
        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }
        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
		$fieldHandler = xoops_getModuleHandler('xmarticle_field', 'xmarticle');
        $categoryHandler = xoops_getModuleHandler('xmarticle_category', 'xmarticle');
        
        //form title
        $title = $this->isNew() ? sprintf(_MA_XMARTICLE_ADD) : sprintf(_MA_XMARTICLE_EDIT);
        
        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        
        if (!$this->isNew()) {
            $form->addElement(new XoopsFormHidden('article_id', $this->getVar('article_id')));
            $status = $this->getVar('article_status');
            $article_cid = $this->getVar('article_cid');
        } else {
            $status = 1;            
        }
        // category        
        $category = $categoryHandler->get($article_cid);
        $form->addElement(new xoopsFormLabel (_MA_XMARTICLE_ARTICLE_CATEGORY, $category->getVar('category_name')));
        $form->addElement(new XoopsFormHidden('article_cid', $article_cid));

        // title
        $form->addElement(new XoopsFormText(_MA_XMARTICLE_ARTICLE_NAME, 'article_name', 50, 255, $this->getVar('article_name')), true);
        
        // reference
        $form->addElement(new XoopsFormText(_MA_XMARTICLE_ARTICLE_REFERENCE, 'article_reference', 20, 50, $this->getVar('article_reference')), true);

        // description
        $editor_configs           =array();
        $editor_configs['name']   = 'article_description';
        $editor_configs['value']  = $this->getVar('article_description', 'e');
        $editor_configs['rows']   = 20;
        $editor_configs['cols']   = 160;
        $editor_configs['width']  = '100%';
        $editor_configs['height'] = '400px';
        $editor_configs['editor'] = $helper->getConfig('admin_editor', 'Plain Text');
        $form->addElement(new XoopsFormEditor(_MA_XMARTICLE_ARTICLE_DESC, 'article_description', $editor_configs), false);
        // logo
        $blank_img = $this->getVar('article_logo') ? $this->getVar('article_logo') : 'blank.gif';
        $uploadirectory='/uploads/xmarticle/images/article';
        $imgtray_img     = new XoopsFormElementTray(_MA_XMARTICLE_ARTICLE_LOGOFILE  . '<br /><br />' . sprintf(_MA_XMARTICLE_ARTICLE_UPLOADSIZE, $upload_size/1000), '<br />');
        $imgpath_img     = sprintf(_MA_XMARTICLE_ARTICLE_FORMPATH, $uploadirectory);
        $imageselect_img = new XoopsFormSelect($imgpath_img, 'article_logo', $blank_img);
        $image_array_img = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . $uploadirectory);
        $imageselect_img->addOption("$blank_img", $blank_img);
        foreach ($image_array_img as $image_img) {
            $imageselect_img->addOption("$image_img", $image_img);
        }
        $imageselect_img->setExtra("onchange='showImgSelected(\"image_img2\", \"article_logo\", \"" . $uploadirectory . "\", \"\", \"" . XOOPS_URL . "\")'");
        $imgtray_img->addElement($imageselect_img, false);
        $imgtray_img->addElement(new XoopsFormLabel('', "<br /><img src='" . XOOPS_URL . '/' . $uploadirectory . '/' . $blank_img . "' name='image_img2' id='image_img2' alt='' />"));
        $fileseltray_img = new XoopsFormElementTray('<br />', '<br /><br />');
        $fileseltray_img->addElement(new XoopsFormFile(_MA_XMARTICLE_ARTICLE_UPLOAD, 'article_logo', $upload_size), false);
        $fileseltray_img->addElement(new XoopsFormLabel(''), false);
        $imgtray_img->addElement($fileseltray_img);
        $form->addElement($imgtray_img);
		
		// field		
		/*$criteria = new CriteriaCompo();
        $criteria->setSort('field_weight ASC, field_name');
        $criteria->setOrder('ASC');
		$field_arr = $fieldHandler->getall($criteria);
		$sel_option = '<option value=""> </option>';
		foreach (array_keys($field_arr) as $i) {
			$sel_option .= '<option value="' . $field_arr[$i]->getVar('field_id') . '">' . $field_arr[$i]->getVar('field_name') .'</option>';
		}		
		$field_text = "<table  cellspacing='1'><tr><td width='50%'>" . _MA_XMARTICLE_ARTICLE_FIELD . "</td><td width='50%'>" . _MA_XMARTICLE_ARTICLE_FIELD . "</td></tr>";
		$sel_id = 0;
		for ($i = 0; $i < 5; ++$i) {
			$field_text .= "<tr><td><select class='form-control' name='addField[{$sel_id}]' id='addField[{$sel_id}]'>" . $sel_option . "</select></td>";
			$sel_id++;
			$field_text .= "<td><select class='form-control' name='addField[{$sel_id}]' id='addField[{$sel_id}]'>" . $sel_option . "</select><td></tr>";
			$sel_id++;
			$field_text .= "<tr height='3px'><td colspan='2'></td></tr>";
		}
		$field_text .= "</table>";
		$field_text .= "<label><input type='checkbox' name='addmorefields' value='True'>" . _MA_XMARTICLE_FIELD_ADDMOREFIELDS . "</label>";
		$form->addElement(new XoopsFormLabel(_MA_XMARTICLE_FIELD_ADDFIELD, $field_text), true);*/

		// status
        $form_status = new XoopsFormRadio(_MA_XMARTICLE_STATUS, 'article_status', $status);
        $options = array(1 => _MA_XMARTICLE_STATUS_A, 0 =>_MA_XMARTICLE_STATUS_NA,);
        $form_status->addOptionArray($options);
        $form->addElement($form_status);
		
        $form->addElement(new XoopsFormHidden('op', 'save'));
        // submit
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }

    /**
     * @return mixed
     */
    public function saveArticle($articleHandler, $action = false)
    {
        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }
        $error_message = '';      
        //logo
        $uploadirectory = '/xmarticle/images/article';
        if ($_FILES['article_logo']['error'] != UPLOAD_ERR_NO_FILE) {
            include_once XOOPS_ROOT_PATH . '/class/uploader.php';
            $uploader_article_img = new XoopsMediaUploader(XOOPS_UPLOAD_PATH . $uploadirectory, array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png'), $upload_size, null, null);
            if ($uploader_article_img->fetchMedia('article_logo')) {
                $uploader_article_img->setPrefix('article_');
                if (!$uploader_article_img->upload()) {
                    $error_message .= $uploader_article_img->getErrors() . '<br />';
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
        $this->setVar('article_reference',  Xmf\Request::getString('article_reference', ''));
        $this->setVar('article_description',  Xmf\Request::getText('article_description', ''));
        $this->setVar('article_cid', Xmf\Request::getInt('article_cid', 0));
        $this->setVar('article_cid', Xmf\Request::getInt('article_cid', 0));
        $this->setVar('article_status', Xmf\Request::getInt('article_status', 1));
		/*$fields = $this->getVar('article_fields');
		// remove field
		if (isset($_REQUEST['removeFields']) && is_array($_REQUEST['removeFields'])) {
			foreach ($_REQUEST['removeFields'] as $index) {
				unset($fields[$index]);
			}
		}
		
		// add fields	
		if (!empty($_REQUEST['addField'])) {
			$i = 0;
			foreach ($_REQUEST['addField'] as $field) {
				if ($field == '') {
					continue;
				}
				$fields[$field] = $field;
			}                       
		}
		$this->setVar('article_fields', $fields);*/
        if ($error_message == '') {
            if ($articleHandler->insert($this)) {
                redirect_header($action, 2, _MA_XMARTICLE_REDIRECT_SAVE);
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
        $new_enreg = $xoopsDB->getInsertId();
        return $new_enreg;
    }
}

/**
 * Class xmarticlexmarticle_articleHandler
 */
class xmarticlexmarticle_articleHandler extends XoopsPersistableObjectHandler
{
    /**
     * xmarticlexmarticle_articleHandler constructor.
     * @param null|XoopsDatabase $db
     */
    public function __construct(&$db)
    {
        parent::__construct($db, 'xmarticle_article', 'xmarticle_article', 'article_id', 'article_name');
    }
}