<?php

/**
 * GPMD SEOPack V1.1.3
 * @copyright Copyright (c) 2012 GPMD Ltd (http://www.gpmd.co.uk)
 */

/**
 * Entities to be installed. Assuming Magento 1.6+
 *
 * @author Adrian Duke
 */
class GPMD_SEOPack_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup {
	
    /**
     * Install a new entity
     *
     * @return array entities to install
     * @access public
     */
    public function getDefaultEntities() {
        return array(
            'catalog_category' => array(
                'entity_model'					=> 'catalog/category',
                'attribute_model'				=> 'catalog/resource_eav_attribute',
                'table'							=> 'catalog/category',
                'additional_attribute_table'	=> 'catalog/eav_attribute',
                'entity_attribute_collection'	=> 'catalog/category_attribute_collection',
                'attributes' => array(
                    'meta_robots' => array(
                        'type'              => 'varchar',
						'source'			=> 'seopack/attribute_source_robots',
                        'label'             => 'Meta Robots',
                        'input'             => 'select',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true,
                        'default'           => '',
                        'group'             => 'General Information',
						'sort_order'        => 100,
                    ),
                ),
            ),
			'catalog_product'                => array(
                'entity_model'                   => 'catalog/product',
                'attribute_model'                => 'catalog/resource_eav_attribute',
                'table'                          => 'catalog/product',
                'additional_attribute_table'     => 'catalog/eav_attribute',
                'entity_attribute_collection'    => 'catalog/product_attribute_collection',
                'attributes'                     => array(
                    'meta_robots' => array(
                        'type'              => 'varchar',
						'source'			=> 'seopack/attribute_source_robots',
                        'label'             => 'Meta Robots',
                        'input'             => 'select',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'unique'            => false,
                        'group'             => 'General',
						'attribute_set'		=> 'Default',
						'sort_order'        => 100,
                    ),
				),
			),
        );
    }
}