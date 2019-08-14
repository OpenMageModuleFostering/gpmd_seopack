<?php

$installer = $this;

$installer->startSetup();
$installer->getConnection()->addColumn($this->getTable('cms_page'), 'meta_robots', 'VARCHAR(40) CHARACTER SET utf8 DEFAULT NULL');
$installer->endSetup();