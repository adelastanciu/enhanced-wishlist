<?php

/**
 * Class AdelaPack_EnhancedWishlist_Block_Adminhtml_Customer_Renderer_WishlistCount
 *
 * @category AdelaPack
 * @package  AdelaPack_EnhancedWishlist
 */
class AdelaPack_EnhancedWishlist_Block_Adminhtml_Customer_Renderer_WishlistCount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render column data.
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }
}