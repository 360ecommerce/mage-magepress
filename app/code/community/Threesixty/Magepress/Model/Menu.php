<?php

class Threesixty_Magepress_Model_Menu extends Fishpig_Wordpress_Model_Menu 
{
    /**
     * Inject links into the top navigation
     *
     * @param Fishpig_Wordpress_Model_Resource_Menu_Item_Collection $items
     * @param Varien_Data_Tree_Node $parentNode
     * @return bool
     */
    protected function _injectLinks($items, $parentNode)
    {
        if ( !$parentNode ) {
            return false;
        }

        foreach($items as $item) {
            try {
                $nodeId = 'wp-node-' . $item->getId();

                $data = array(
                    'name'      => $item->getLabel(),
                    'id'        => $nodeId,
                    'url'       => $item->getUrl(),
                    'is_active' => $item->isItemActive(),
                    'classes'   => unserialize($item->getClasses())
                );

                if ($data['is_active']) {
                    $parentNode->setIsActive(true);
                    $buffer = $parentNode;

                    while($buffer->getParent()) {
                        $buffer = $buffer->getParent();
                        $buffer->setIsActive(true);
                    }
                }

                $itemNode = new Varien_Data_Tree_Node($data, 'id', $parentNode->getTree(), $parentNode);
                $parentNode->addChild($itemNode);

                if (count($children = $item->getChildrenItems()) > 0) {
                    $this->_injectLinks($children, $itemNode);
                }
            }
            catch (Exception $e) {
                Mage::helper('wordpress')->log($e->getMessage());
            }
        }

        return true;
    }
}