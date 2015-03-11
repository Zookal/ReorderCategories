<?php

/**
 * NOTICE OF LICENSE
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright  Copyright (c) Zookal Services Pte Ltd
 * @author     Cyrill Schumacher @schumacherfm, Chris Zaharia @chrisjz
 * @license    See LICENSE.txt
 */
class Zookal_ReorderCategories_Adminhtml_CategoriesReorderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/categories/zookal_reordercategories');
    }

    /**
     * @param Zookal_ReorderCategories_Model_Reorder $reorder
     * @param                                        $msg
     */
    protected function _doIterate(Zookal_ReorderCategories_Model_Reorder $reorder, $msg)
    {
        if ($reorder->checkCount()) {
            $this->getResponse()->sendResponse();
            $reorder->iterate();
            echo "\n<hr>\nDone!\n";
            return;
        }

        $reorder->iterate();
        $this->_getSession()->addSuccess($this->__('Successfully reordered by ' . $msg));
        $this->_redirectReferer();
        return;
    }

    public function byNameAction()
    {
        /** @var Zookal_ReorderCategories_Model_Reorder $reorder */
        $reorder = Mage::getModel('zookal_reordercategories/reorder');
        $reorder->setOrderByName();
        $this->_doIterate($reorder, 'name');
    }

    public function byIDAction()
    {
        /** @var Zookal_ReorderCategories_Model_Reorder $reorder */
        $reorder = Mage::getModel('zookal_reordercategories/reorder');
        $reorder->setOrderByID();
        $this->_doIterate($reorder, 'ID');
    }
}
