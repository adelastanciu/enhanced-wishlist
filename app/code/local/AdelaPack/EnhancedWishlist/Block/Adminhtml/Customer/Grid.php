<?php

/**
 * Class AdelaPack_EnhancedWishlist_Block_Adminhtml_Customer_Grid
 *
 * @category AdelaPack
 * @package  AdelaPack_EnhancedWishlist
 */
class AdelaPack_EnhancedWishlist_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
    /**
     * Prepare grid collection object
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        $collection->getSelect()
            ->joinLeft(
                array('w' => $collection->getTable('wishlist/wishlist')),
                'e.entity_id = w.customer_id',
                array('*')
            )
            ->joinLeft(
                array('wi' => $collection->getTable('wishlist/item')),
                'w.wishlist_id = wi.wishlist_id',
                array(
                    'item_count' => 'count(wi.product_id)'
                )
            );
        $collection->addFilterToMap('item_count', new Zend_Db_Expr('count(wi.product_id)'));

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('item_count', array(
                'header'    => Mage::helper('customer')->__('Wishlist Items Count'),
                'type'      => 'text',
                'width'     => '50px',
                'align'     => 'center',
                'index'     => 'item_count',
//                'filter_condition_callback' => array($this, '_wishlistCountCallback')
//                'renderer'  => 'enhancedwishlist/adminhtml_customer_renderer_wishlistCount',
            ),
            'billing_region'
        );

        $this->sortColumnsByOrder();
    }

    protected function _wishlistCountCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
//$this->getCollection()->printLogQuery(true);
//        $this->getCollection()->getSelect()->having("COUNT(item_count) like ?", "%$value%");
//        $this->getCollection()->getSelect()->where("item_count like ?", "%$value%");

        return $this;
    }
}