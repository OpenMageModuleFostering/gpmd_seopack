<?php
/**
 * GPMD SEOPack V0.1.1
 * @copyright Copyright (c) 2012 GPMD Ltd (http://www.gpmd.co.uk)
 */

/**
 * Source class for robots select
 *
 * @author Adrian Duke
 */
class GPMD_SEOPack_Model_Attribute_Source_Robots extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'value' => '',
                    'label' => Mage::helper('seopack')->__('-- Magento Default --'),
                ),
                array(
                    'value' => 'NOINDEX,FOLLOW',
                    'label' => Mage::helper('seopack')->__('NOINDEX, FOLLOW'),
                ),
                array(
                    'value' => 'INDEX,NOFOLLOW',
                    'label' => Mage::helper('seopack')->__('INDEX, NOFOLLOW'),
                ),
                array(
                    'value' => 'NOINDEX,NOFOLLOW',
                    'label' => Mage::helper('seopack')->__('NOINDEX, NOFOLLOW'),
                )
            );
        }
        return $this->_options;
    }
}

