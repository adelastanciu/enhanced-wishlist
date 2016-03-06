<?php

/**
 * Class AdelaPack_EnhancedWishlist_Helper_Data
 *
 * @category AdelaPack
 * @package  AdelaPack_EnhancedWishlist
 */
class AdelaPack_EnhancedWishlist_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Wishlist directory
     */
    const WISHLIST_DIR = 'wishlist_emails';

    /**
     * XPATHs to email configuration
     */
    const XML_PATH_WISHLIST_ITEMS_EMAIL = 'enhancedwishlist/general/email';
    const XML_PATH_WISHLIST_ITEMS_NAME = 'enhancedwishlist/general/name';

    /**
     * Retrieve wishlist items csv directory path
     * Creates directory, if missing
     *
     * @return string
     */
    public function getWishlistCsvDir()
    {
        $path = Mage::getBaseDir('media') . DS . self::WISHLIST_DIR . DS;
        if (!is_dir($path)) {
            mkdir($path);
        }

        return $path;
    }

    /**
     * Retrieve email address
     *
     * @param null $store
     * @return mixed
     */
    public function getEmailAddress($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_WISHLIST_ITEMS_EMAIL, $store);
    }

    /**
     * Retrieve recipient name
     *
     * @param null $store
     * @return mixed
     */
    public function getRecipientName($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_WISHLIST_ITEMS_NAME, $store);
    }

    /**
     * Prepare csv file with wishlist items
     *
     * @return string
     */
    public function prepareFile()
    {
        $wishlistItems = Mage::getModel('wishlist/item')->getCollection()
            ->addFieldToSelect('product_id');

        $wishlistItems->getSelect()
            ->group('product_id')
            ->order('count(product_id) DESC');

        $wishlistResult = array();
        $resource = Mage::getResourceSingleton('catalog/product');
        foreach ($wishlistItems as $item) {
            $wishlistResult[] = array(
                'product_id' => $item->getProductId(),
                'name'       => $resource->getAttributeRawValue($item->getProductId(), 'name', Mage_Core_Model_App::ADMIN_STORE_ID)
            );
        }

        array_unshift($wishlistResult, array_keys($wishlistResult[0]));

        $csv = new Varien_File_Csv();
        $filename = Mage::helper('enhancedwishlist')->getWishlistCsvDir() . 'wishlist_' . time() . '.csv';
        $csv->saveData($filename, $wishlistResult);

        return $filename;
    }
}