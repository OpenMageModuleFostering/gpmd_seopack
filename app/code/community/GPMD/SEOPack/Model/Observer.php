<?php
/**
 * Observer for custom meta robots. Better than rewriting.
 *
 * @author Adrian Duke <adrian@gpmd.co.uk>, Martin Dines <mdines@gpmd.co.uk>
 */
class GPMD_SEOPack_Model_Observer
{
    const XML_PATH_METAROBOTS_SITEENABLED = 'seopack/metarobots/site_enabled';
    const XML_PATH_METAROBOTS_PRODUCTENABLED = 'seopack/metarobots/product_enabled';
    const XML_PATH_METAROBOTS_CATEGORYENABLED = 'seopack/metarobots/category_enabled';
    const XML_PATH_METAROBOTS_FILTERENABLED = 'seopack/metarobots/filter_enabled';
    const XML_PATH_METAROBOTS_TREATCATEGORYASFILTER = 'seopack/metarobots/treat_category_as_filter';
    const XML_PATH_METAROBOTS_TREATSORTASFILTER = 'seopack/metarobots/treat_sort_as_filter';
    const XML_PATH_METAROBOTS_SEARCHENABLED = 'seopack/metarobots/search_enabled';
    const XML_PATH_METAROBOTS_CMSPAGEENABLED = 'seopack/metarobots/cms_page_enabled';
    const XML_PATH_RELPAGINATION_ENABLED = 'seopack/misc/rel_pagination_enabled';
    const XML_PATH_GA_IGNORE = 'seopack/misc/ga_ignore';

    const ADMIN_CMS_PAGE_ROBOTS_FIELD_NAME = 'meta_robots';

    static $_matches = array(
        'HTTP_X_PURPOSE' => array(
            'preview',
        ),
        'HTTP_X_MOZ' => array(
            'prefetch',
        ),
    );

    public function setMetaRobots(Varien_Event_Observer $observer)
    {
        if (empty($this->_siteEnabled)) {
            $this->_siteEnabled = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_SITEENABLED);
        }

        if ($this->_siteEnabled) {
            $layout = $observer->getEvent()->getLayout();
            $action = $observer->getEvent()->getAction();

            $this->_setMetaRobots($layout, $action);
        }
    }

    public function setGAIgnore(Varien_Event_Observer $observer)
    {
        if (empty($this->_gaIgnoreEnabled)) {
            $this->_gaIgnoreEnabled = Mage::getStoreConfig(self::XML_PATH_GA_IGNORE);
        }

        if ($this->_gaIgnoreEnabled) {
            $layout = $observer->getEvent()->getLayout();

            $this->_setGAIgnore($layout);
        }
    }

    protected function _setGAIgnore($layout)
    {
        $intersect = array_intersect_key(self::$_matches, $_SERVER);

        if (!empty($intersect)) {
            foreach ($intersect as $match => $values) {
                foreach ($values as $value) {
                    if ($_SERVER[$match] == $value) {
                        $block = $layout->getBlock('after_body_start');
                        if ($block){
                            $block->unsetChild('google_analytics');
                        }
                        break 2;
                    }
                }
            }
        }
    }

    protected function _setMetaRobots($layout, $action)
    {
        if (empty($this->_productEnabled)) {
            $this->_productEnabled = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_PRODUCTENABLED);
        }

        if (empty($this->_categoryEnabled)) {
            $this->_categoryEnabled = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_CATEGORYENABLED);
        }

        if (empty($this->_filterEnabled)) {
            $this->_filterEnabled = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_FILTERENABLED);
        }

        if (empty($this->_treatCategoryAsFilter)) {
            $this->_treatCategoryAsFilter = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_TREATCATEGORYASFILTER);
        }

        if (empty($this->treatSortAsFilter)) {
            $this->_treatSortAsFilter = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_TREATSORTASFILTER);
        }

        if (empty($this->_searchEnabled)) {
            $this->_searchEnabled = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_SEARCHENABLED);
        }

        if (empty($this->_cmsPageEnabled)) {
            $this->_cmsPageEnabled = Mage::getStoreConfig(self::XML_PATH_METAROBOTS_CMSPAGEENABLED);
        }

        if ($head = $layout->getBlock('head')) {
            if ($this->_productEnabled && $action instanceof Mage_Catalog_ProductController) {
                $product = Mage::registry('current_product');
                // Set meta robots if set in product
                if ($robots = $product->getMetaRobots()) {
                    $head->setData('robots', $robots);
                }

                if ($action->getRequest()->getParam('options') && $action->getRequest()->getParam('options') === 'cart') {
                    $head->setData('robots', 'NOINDEX, FOLLOW');
                }
            } elseif ($this->_searchEnabled
                && ($action instanceof Mage_CatalogSearch_ResultController
                    || ($action instanceof Mage_CatalogSearch_AdvancedController
                        && $action->getRequest()->getActionName() === 'result'))
            ) {
                // Set custom meta robots if viewing a search page displaying results
                $head->setData('robots', 'NOINDEX, FOLLOW');
            } elseif ($this->_categoryEnabled && $action instanceof Mage_Catalog_CategoryController) {
                $category = Mage::registry('current_category');

                // Set meta robots if set in category
                if ($robots = $category->getMetaRobots()) {
                    $head->setData('robots', $robots);
                }

                if ($this->_filterEnabled) {
                    $layer = Mage::getModel('catalog/layer')->setCurrentCategory($category);
                    $attributes = $layer->getFilterableAttributes();

                    // Iterate over filterable attributes. Check if attribute is present in parameters
                    foreach ($attributes as $attribute) {
                        $attributeCode = $attribute->getAttributeCode();
                        if (array_key_exists($attributeCode, $action->getRequest()->getParams())) {
                            // Set NOINDEX, FOLLOW when a filter is applied
                            $head->setData('robots', 'NOINDEX, FOLLOW');
                            break;
                        }
                    }

                    // Treat category sort as a filters
                    if ($this->_treatSortAsFilter) {
                        $sortParams = array('dir', 'mode', 'limit', 'order');
                        if (count(array_intersect($sortParams, array_keys($action->getRequest()->getParams()))) > 0) {
                            $head->setData('robots', 'NOINDEX, FOLLOW');
                        }
                    }

                    // Treat category as a filterable attribute
                    if ($this->_treatCategoryAsFilter) {
                        if (array_key_exists('cat', $action->getRequest()->getParams())) {
                            $head->setData('robots', 'NOINDEX, FOLLOW');
                        }
                    }
                }
            } elseif ($this->_cmsPageEnabled && $action instanceof Mage_Cms_PageController) {
                $pageId = $action->getRequest()->getParam('page_id');
                $page = Mage::getModel('cms/page')->load($pageId);

                if ($page->getId()) {
                    if ($robots = $page->getMetaRobots()) {
                        $head->setData('robots', $robots);
                    }
                }
            }
        }
    }

    /**
     * doPagination() - kick off the process of adding the next and prev rel links to category pages where necessary
     *
     * @return GPMD_SEOPack_Model_Observer
     * @author Drew Hunter <drewdhunter@gmail.com>
     */
    public function doPagination()
    {
        if (empty($this->_relPaginationEnabled)) {
            $this->_relPaginationEnabled = Mage::getStoreConfig(self::XML_PATH_RELPAGINATION_ENABLED);
        }

        try {
            if ($this->_relPaginationEnabled) {
                Mage::getModel('seopack/paginator')->createLinks();
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    public function addRobotsToCmsPages(Varien_Event_Observer $observer)
    {
        $model = Mage::registry('cms_page');
        $form = $observer->getForm();

        $form->getElement('meta_fieldset')->addField(self::ADMIN_CMS_PAGE_ROBOTS_FIELD_NAME, 'select', array(
            'name' => self::ADMIN_CMS_PAGE_ROBOTS_FIELD_NAME,
            'label' => Mage::helper('cms')->__('Meta Robots'),
            'title' => Mage::helper('cms')->__('Meta Robots'),
            'values' => Mage::getModel('seopack/attribute_source_robots')->getAllOptions(),
            'value' => $model->getMetaRobots()
        ));
    }

    public function saveRobotsToCmsPages(Varien_Event_Observer $observer)
    {
    }
}
