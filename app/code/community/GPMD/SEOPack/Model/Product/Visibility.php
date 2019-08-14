<?php
/**
 * GPMD SEOPack V0.1.1
 * @copyright Copyright (c) 2012 GPMD Ltd (http://www.gpmd.co.uk)
 */

/**
 * Rewrite to add orphan functionality
 *
 * @author Adrian Duke
 */
class GPMD_SEOPack_Model_Product_Visibility extends Mage_Catalog_Model_Product_Visibility
{
    const VISIBILITY_ORPHANED = 5;

    /**
     * Retrieve visible in site ids array
     *
     * @return array
     */
    public function getVisibleInSiteIds()
    {
        return array(self::VISIBILITY_IN_SEARCH, self::VISIBILITY_IN_CATALOG, self::VISIBILITY_BOTH, self::VISIBILITY_ORPHANED);
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::VISIBILITY_NOT_VISIBLE => Mage::helper('catalog')->__('Not Visible Individually'),
            self::VISIBILITY_ORPHANED    => Mage::helper('catalog')->__('Orphaned'),
            self::VISIBILITY_IN_CATALOG  => Mage::helper('catalog')->__('Catalog'),
            self::VISIBILITY_IN_SEARCH   => Mage::helper('catalog')->__('Search'),
            self::VISIBILITY_BOTH        => Mage::helper('catalog')->__('Catalog, Search')
        );
    }

    /**
     * Retrieve all options
     *
     * @return array
     */
    static public function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value' => '', 'label' => ''));
        return $options;
    }

    /**
     * Retireve all options
     *
     * @return array
     */
    static public function getAllOptions()
    {
        $res = array();
        $res[] = array('value' => '', 'label' => Mage::helper('catalog')->__('-- Please Select --'));
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
                'value' => $index,
                'label' => $value
            );
        }
        return $res;
    }

    /**
     * Retrieve option text
     *
     * @param int $optionId
     * @return string
     */
    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}