<?php

class Threesixty_Wordpress_Model_Menu_Item extends Fishpig_Wordpress_Model_Menu_Item 
{
    public function getClasses()
    {
        return $this->getMetaValue('_menu_item_classes');
    }
}