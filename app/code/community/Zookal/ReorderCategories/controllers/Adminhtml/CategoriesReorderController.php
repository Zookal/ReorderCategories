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
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config');
    }

    public function byNameAction()
    {
        $this->getResponse()->sendResponse();
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('level')
            ->setLoadProductCount(false);
        $collection->setOrder('level', 'ASC');
        $collection->setOrder('name', 'ASC');
        $collection->load(); // only load generates the real getSelect statement :-(

        /** @var Mage_Core_Model_Resource_Iterator $collectionIterator */
        $collectionIterator = Mage::getResourceModel('core/iterator');
        $collectionIterator->walk(
            $collection->getSelect(),
            array(
                array($this, 'categorySavePosition')
            )
        );
        echo "\n<hr>\nDone!\n";
    }

    public function categorySavePosition(array $row)
    {
        $idx = $row['idx'] + 100;
        $c   = Mage::getModel('catalog/category');
        $c->setData($row['row']);
        $c->setPosition($idx);
        $c->getResource()->isPartialSave(true);
        $c->getResource()->save($c);

        echo $c->getId() . ' ';
        flush();
    }
}
