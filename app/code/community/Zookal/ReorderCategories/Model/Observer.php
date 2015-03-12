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
class Zookal_ReorderCategories_Model_Observer
{

    /**
     * @dispatch adminhtml_block_html_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function addReorderButton(Varien_Event_Observer $observer)
    {

        /** @var Mage_Adminhtml_Block_Catalog_Category_Edit_Form $block */
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Category_Edit_Form) {
            $categoryId = $block->getCategoryId();
            $block->addAdditionalButton('reorder',
                [
                    'label'   => Mage::helper('catalog')->__('Reorder Children by Name'),
                    'onclick' => "categoryReset('" . $block->getUrl(
                            '*/categoriesReorder/byName',
                            [
                                'category_id' => $categoryId
                            ]
                        ) . "', false)"
                ]);
        }
    }
}
