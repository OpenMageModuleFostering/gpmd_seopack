<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Observer for custom meta robots. Better than rewriting.
 *
 * @author adrian
 */
class GPMD_SEOPack_Model_Observer {
	
	const XML_PATH_METAROBOTS_SITEENABLED = 'seopack/activated/site_enabled';
	const XML_PATH_METAROBOTS_FILTERENABLED = 'seopack/activated/filter_enabled';
	const XML_PATH_METAROBOTS_PRODUCTENABLED = 'seopack/activated/product_enabled';
	const XML_PATH_METAROBOTS_CATEGORYENABLED = 'seopack/activated/category_enabled';
	const XML_PATH_GA_IGNORE = 'seopack/activated/ga_ignore';
	
	static $_matches = array(
		'HTTP_X_PURPOSE' => array(
			'preview',
		),
		'HTTP_X_MOZ' => array(
			'prefetch',
		),
	);
	
	public function setMetaRobots(Varien_Event_Observer $observer){
		
		if(empty($this->_siteEnabled)){
			$this->_siteEnabled = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_SITEENABLED);
		}
		
		if($this->_siteEnabled){
			$layout = $observer->getEvent()->getLayout();
			$action = $observer->getEvent()->getAction();
			
			$this->_setMetaRobots($layout, $action);
		}
		
		$this->_setGAIgnore($layout);
	}
	
	protected function _setGAIgnore($layout){
		
		if(empty($this->_gaIgnoreEnabled)){
			$this->_gaIgnoreEnabled = Mage::getStoreConfig(self::XML_PATH_GA_IGNORE);
		}
		
		if($this->_gaIgnoreEnabled){
			$intersect = array_intersect_key(self::$_matches, $_SERVER);

			if(!empty($intersect)){
				foreach($intersect as $match => $values){
					foreach($values as $value){
						if($_SERVER[$match] == $value){
							$layout->getBlock('after_body_start')->unsetChild('google_analytics');
							break 2;
						}
					}
				}
			}
		}
	}
	
	protected function _setMetaRobots($layout, $action){
		
		if(empty($this->_filterEnabled)){
			$this->_filterEnabled = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_FILTERENABLED);
		}
		
		if(empty($this->_productEnabled)){
			$this->_productEnabled = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_PRODUCTENABLED);
		}
		
		if(empty($this->_categoryEnabled)){
			$this->_categoryEnabled = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_CATEGORYENABLED);
		}
		
		if($head = $layout->getBlock('head')){
			if($this->_productEnabled && $action instanceof Mage_Catalog_ProductController){
				$product = Mage::registry('current_product');
				// Set meta robots if set in product
				if($robots = $product->getMetaRobots()){
					$head->setData('robots',$robots);
				}
			}elseif($this->_categoryEnabled && $action instanceof Mage_Catalog_CategoryController){
				$category = Mage::registry('current_category');
				// Set meta robots if set in category
				if($robots = $category->getMetaRobots()){
					$head->setData('robots',$robots);
				}
			}
			if($this->_filterEnabled){
				if($block = $layout->getBlockSingleton('catalog/layer_state')){
					if($block->getActiveFilters()) {
						// Set NOINDEX,FOLLOW when a filter is applied
						$head->setData('robots','NOINDEX,FOLLOW');
					}
				}
			}
		}
	}
}
