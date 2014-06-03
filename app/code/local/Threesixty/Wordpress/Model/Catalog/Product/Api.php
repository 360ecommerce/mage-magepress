<?php
/**
 * Catalog product api
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Threesixty_Wordpress_Model_Catalog_Product_Api extends Mage_Catalog_Model_Product_Api
{
    /**
     * Retrieve list of products with basic info (id, sku, type, set, name)
     *
     * @param null|object|array $filters
     * @param string|int $store
     * @return array
     */
    public function items($filters = null, $store = null)
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addStoreFilter($this->_getStoreId($store))
            ->addAttributeToSelect('name');

        // Count
        if( isset( $filters['count'] ) ) {
            $count = $filters['count'];
            $collection->setPageSize($count);
            unset($filters['count']);
        }

         // IDs
        if( isset( $filters['ids'] ) ) {
            $ids = $filters['ids'];
            $collection->addAttributeToFilter( 'entity_id', array( 'in' => $ids ) );
            unset($filters['ids']);
        } else {
            unset($filters['ids']);
        }

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_filtersMap);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach ($collection as $product) {
            try {
                $image = Mage::helper('catalog/image')->init($product, 'small_image');
            } catch( Exception $ex ) {
                Mage::log( $ex->__toString() );
            }

            $result[] = array(
                'product_id'        => $product->getId(),
                'sku'               => $product->getSku(),
                'name'              => $product->getName(),
                'set'               => $product->getAttributeSetId(),
                'type'              => $product->getTypeId(),
                'price'             => $product->getPrice(),
                'url'               => rtrim( Mage::getUrl( $product->getUrlPath() ), '/' ),
                'special_price'     => $product->getSpecialPrice(),
                'category_ids'      => $product->getCategoryIds(),
                'website_ids'       => $product->getWebsiteIds(),
                'image'             => $image->__toString(),
            );
        }
        return $result;
    }

    /**
     * Retrieve product info
     *
     * @param int|string $productId
     * @param string|int $store
     * @param array      $attributes
     * @param string     $identifierType
     * @return array
     */
    public function info($productId, $store = null, $attributes = null, $identifierType = null)
    {
        // make sku flag case-insensitive
        if (!empty($identifierType)) {
            $identifierType = strtolower($identifierType);
        }

        $product = $this->_getProduct($productId, $store, $identifierType);

        $result = array( // Basic product data
            'product_id' => $product->getId(),
            'sku'        => $product->getSku(),
            'set'        => $product->getAttributeSetId(),
            'type'       => $product->getTypeId(),
            'categories' => $product->getCategoryIds(),
            'websites'   => $product->getWebsiteIds()
        );

        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $attributes)) {
                $result[$attribute->getAttributeCode()] = $product->getData(
                                                                $attribute->getAttributeCode());
            }
        }

        return $result;
    }
} // Class Mage_Catalog_Model_Product_Api End
