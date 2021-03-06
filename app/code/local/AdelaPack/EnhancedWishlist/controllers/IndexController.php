<?php

require_once Mage::getModuleDir('controllers', 'Mage_Wishlist') . DS . 'IndexController.php';

/**
 * Class AdelaPack_EnhancedWishlist_IndexController
 *
 * @category AdelaPack
 * @package  AdelaPack_EnhancedWishlist
 */
class AdelaPack_EnhancedWishlist_IndexController extends Mage_Wishlist_IndexController
{
    /**
     * Add product to wishlist
     */
    public function addAction()
    {
        $response = array();
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $response['status'] = 'ERROR';
            $response['message'] = $this->__('Wishlist Has Been Disabled By Admin');
        }
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $response['status'] = 'REDIRECT';
            $response['message'] = $this->__('Please Login First');
        }

        if (empty($response)) {
            $session = Mage::getSingleton('customer/session');
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Unable to Create Wishlist');
            } else {

                $productId = (int)$this->getRequest()->getParam('product');
                if (!$productId) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Product Not Found');
                } else {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    if (!$product->getId() || !$product->isVisibleInCatalog()) {
                        $response['status'] = 'ERROR';
                        $response['message'] = $this->__('Cannot specify product.');
                    } else {

                        try {
                            $requestParams = $this->getRequest()->getParams();
                            $buyRequest = new Varien_Object($requestParams);

                            $result = $wishlist->addNewItem($product, $buyRequest);
                            if (is_string($result)) {
                                Mage::throwException($result);
                            }
                            $wishlist->save();

                            Mage::dispatchEvent(
                                'wishlist_add_product',
                                array(
                                    'wishlist' => $wishlist,
                                    'product'  => $product,
                                    'item'     => $result
                                )
                            );

                            Mage::helper('wishlist')->calculate();

                            $message = $this->__('%1$s has been added to your wishlist.', "<span class='green'>" . $product->getName() . "</span>");
                            $response['status'] = 'SUCCESS';
                            $response['message'] = $message;

                            Mage::unregister('wishlist');
                        } catch (Mage_Core_Exception $e) {
                            $response['status'] = 'ERROR';
                            $response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
                        } catch (Exception $e) {
                            mage::log($e->getMessage());
                            $response['status'] = 'ERROR';
                            $response['message'] = $this->__('An error occurred while adding item to wishlist.');
                        }
                    }
                }
            }

        }

        $wishlistUrl = Mage::getUrl("wishlist");
        $response['message'] = "<p>" . $response['message'] . "</p><p class='clearfix no-margin'><a class='pull-left close-modal' href='javascript:void(0)'>" . $this->__("Continue shopping") . "</a><a class='pull-right' href='" . $wishlistUrl . "'>" . $this->__("Go to wishlist") . "</a></p>";

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

        return;
    }
}