<?php
/**
 * GPMD SEOPack V1.3.0
 * @copyright Copyright (c) 2013 GPMD Ltd (http://www.gpmd.co.uk)
 */

$installer = $this;
$installer->startSetup();
$installer->run("DELETE FROM `{$installer->getTable('eav_attribute')}` WHERE `source_model` = 'seopack/attribute_source_robots'");
$installer->run("ALTER TABLE `{$installer->getTable('cms_page')}` DROP 'meta_robots'");
$installer->endSetup();