<?php

/**
 * NOTICE OF LICENSE
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright  Copyright (c) Zookal Services Pte Ltd
 * @author     Cyrill Schumacher @schumacherfm
 * @license    See LICENSE.txt
 */

/**
 * @method Zookal_ReorderCategories_Model_Reorder setCategoryId(int)
 * Class Zookal_ReorderCategories_Model_Reorder
 */
class Zookal_ReorderCategories_Model_Reorder extends Varien_Object
{
    /**
     * @var Mage_Catalog_Model_Resource_Category_Collection
     */
    private $_collection = null;

    private $_flushOutput = false;

    private $_maxSize = 0;

    public function __construct()
    {
        $this->_maxSize = (int)Mage::getStoreConfig('catalog/zookal_reordercategories/max_count');
    }

    /**
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCollection()
    {
        if (null === $this->_collection) {
            $this->_collection = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('level')
                ->setLoadProductCount(false);
            $id                = (int)$this->getData('category_id');
            if ($id > 0) {
                $this->_collection->addFieldToFilter('parent_id', ['eq' => $id]);
            }
            $this->_collection->setOrder('level', 'ASC');
        }
        return $this->_collection;
    }

    /**
     * If true then the categories are too many and we flush directly to the browser.
     * if false we will redirect to the referrer page.
     *
     * @return bool
     */
    public function checkCount()
    {
        return $this->_flushOutput = $this->getCollection()->getSize() > $this->_maxSize;
    }

    /**
     *
     */
    public function iterate()
    {
        // only load generates the real getSelect statement :-(
        // @todo find a away around that ...
        $this->_collection->load();
        /** @var Mage_Core_Model_Resource_Iterator $collectionIterator */
        $collectionIterator = Mage::getResourceModel('core/iterator');
        $collectionIterator->walk(
            $this->getCollection()->getSelect(),
            [
                [$this, 'categorySavePosition']
            ]
        );
    }

    public function categorySavePosition(array $row)
    {
        $idx = $row['idx'] + 100;
        $c   = Mage::getModel('catalog/category');
        $c->setData($row['row']);
        $c->setPosition($idx);
        $c->getResource()->isPartialSave(true);
        $c->getResource()->save($c);
        if (true === $this->_flushOutput) {
            echo $c->getId() . ' ';
            flush();
        }
    }

    public function setOrderByName()
    {
        $this->getCollection()
            ->addAttributeToSelect('name')
            ->setOrder('name', 'ASC');
    }

    public function setOrderByID()
    {
        $this->getCollection()
            ->setOrder('entity_id', 'ASC');
    }

    public function setOrderByRand()
    {
        /** @var Varien_Db_Select $s */
        $s = $this->getCollection()->getSelect();
        $s->orderRand();
    }
}
