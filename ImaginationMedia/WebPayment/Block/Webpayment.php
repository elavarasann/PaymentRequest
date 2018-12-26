<?php

namespace ImaginationMedia\WebPayment\Block;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Checkout\Block\Cart\AbstractCart;
use ImaginationMedia\WebPayment\Helper\Data;
//use ImaginationMedia\WebPayment\Model\Order\Create;

/**
 * Webpayment block
 */
class Webpayment extends Template
{

    private $_cart;
    private $_helper;

    /**
     * Webpayment constructor.
     * @param Template\Context $context
     * @param AbstractCart $cart
     * @param Create $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AbstractCart $cart,
        Data $helper,
        array $data = [])
    {

        $this->_helper = $helper;
        $this->_cart = $cart;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return string
     */
    public function getCurrency(){
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
//        $abstractCart = $objectManager->create('Magento\Checkout\Block\Cart\AbstractCart'); // Instance of Pricing Helper
        $currency = $this->_cart->getQuote()->getQuoteCurrencyCode();
        return $currency;
    }

    public function getAllItems(){
        $allItems = $this->_cart->getQuote()->getAllItems();
        return $allItems;
    }

    public function getTotal(){
        $quote = $this->_cart->getTotalsCache();
        $getGrandTotal = $quote['grand_total']->getData('value');

        return $getGrandTotal;
    }

    public function getShippingRate(){
        $quote = $this->_cart->getTotalsCache();
        $getShippingRate = $quote['shipping']->getData('value');

        return $getShippingRate;
    }

    public function getSubTotal(){
        $quote = $this->_cart->getTotalsCache();
        $getSubTotal = $quote['subtotal']->getData('value');

        return $getSubTotal;
    }

    /**
     * @param $orderData
     * @return int
     */
    public function createOrder($orderData){
        try {
            return $this->_helper->createWebPaymentOrder($orderData);
        } catch (CouldNotSaveException $e) {
            var_dump($e);
        } catch (NoSuchEntityException $e) {
            var_dump($e);
        } catch (LocalizedException $e) {
            var_dump($e);
        }
    }

    public function deleteQuoteItems(){
        $checkoutSession = $this->getCheckoutSession();
        $allItems = $checkoutSession->getQuote()->getAllVisibleItems();//returns all teh items in session
        foreach ($allItems as $item) {
            $itemId = $item->getItemId();//item id of particular item
            $quoteItem=$this->getItemModel()->load($itemId);//load particular item which you want to delete by his item id
            $quoteItem->delete();//deletes the item
        }
    }
    public function getCheckoutSession(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
        $checkoutSession = $objectManager->get('Magento\Checkout\Model\Session');//checkout session
        return $checkoutSession;
    }

    public function getItemModel(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
        $itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item model to load quote item
        return $itemModel;
    }

}