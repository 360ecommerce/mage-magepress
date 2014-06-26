<?php
/**
 * Catalog category api
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Threesixty_Magepress_Model_Catalog_Category_Api extends Mage_Catalog_Model_Category_Api
{
    /**
     * Convert node to array
     *
     * @param Varien_Data_Tree_Node $node
     * @return array
     */
    protected function _nodeToArray(Varien_Data_Tree_Node $node)
    {
        // Only basic category data
        $result = array();
        $result['category_id']      = $node->getId();
        $result['parent_id']        = $node->getParentId();
        $result['name']             = $node->getName();
        $result['is_active']        = $node->getIsActive();
        $result['position']         = $node->getPosition();
        $result['level']            = $node->getLevel();
        $result['url_path']         = $node->getUrlPath();
        $result['url']              = rtrim(Mage::getUrl($node->getUrlPath()), '/');
        $result['thumbnail']        = $node->getThumbnail();
        $result['children']         = array();
        $result['include_in_menu']  = $node->getIncludeInMenu();

        foreach ($node->getChildren() as $child) {
            $result['children'][] = $this->_nodeToArray($child);
        }

        return $result;
    }

    /**
     * Retrieve category data
     *
     * @param int $categoryId
     * @param string|int $store
     * @param array $attributes
     * @return array
     */
    public function info($categoryId, $store = null, $attributes = null)
    {
        $category = $this->_initCategory($categoryId, $store);

        // Basic category data
        $result = array();
        $result['category_id'] = $category->getId();

        $result['is_active']   = $category->getIsActive();
        $result['position']    = $category->getPosition();
        $result['level']       = $category->getLevel();
        $result['thumbnail']   = $category->getThumbnail();

        foreach ($category->getAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $attributes)) {
                $result[$attribute->getAttributeCode()] = $category->getData($attribute->getAttributeCode());
            }
        }
        $result['url']              = rtrim(Mage::getUrl($category->getUrlPath()), '/');
        $result['parent_id']        = $category->getParentId();
        $result['children']         = $category->getChildren();
        $result['all_children']     = $category->getAllChildren();
        $result['product_count']    = $category->getProductCount();

        return $result;
    }
}