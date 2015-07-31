<?php

$installer = $this;

$installer->addAttribute('catalog_product', 'method_add', array(
    'group'             => 'General',
    'type'              => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Method add',
    'input'             => 'text',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => true,
    'default'           => '',
    'searchable'        => true,
    'filterable'        => true,
    'comparable'        => true,
    'visible_on_front'  => true,
    'unique'            => false,
    'apply_to'          => 'simple,configurable,virtual',
    'is_configurable'   => false
));