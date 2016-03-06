<?php

/**
 * Class AdelaPack_EnhancedWishlist_Model_Job
 *
 * @category AdelaPacka
 */
class AdelaPack_EnhancedWishlist_Model_Job
{
    /**
     * Sends daily emails at midnight
     *
     * @schedule 0 0 * * *
     */
    public function emailWishlists()
    {
        $_helper = Mage::helper('enhancedwishlist');
        $file = $_helper->prepareFile();

        if ($file) {
            $mail = new Zend_Mail('utf-8');

            $recipients = array(
                $_helper->getRecipientName() => $_helper->getEmailAddress(),
            );

            $mail->setBodyHtml('Please check the attached document.')
                ->setSubject('Wishlist items')
                ->addTo($recipients)
                ->setFrom(
                    Mage::getStoreConfig('trans_email/ident_general/email'),
                    Mage::getStoreConfig('trans_email/ident_general/name')
                );


            $attachment = file_get_contents($file);
            $parts = pathinfo($file);
            $mail->createAttachment(
                $attachment,
                Zend_Mime::TYPE_OCTETSTREAM,
                Zend_Mime::DISPOSITION_ATTACHMENT,
                Zend_Mime::ENCODING_BASE64,
                isset($parts['basename']) ? $parts['basename'] : 'attachment.csv'
            );

            try {
                $mail->send();
                unlink($file);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }
}