<?php
$installer = $this;
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('product_to_mirakle')};
DROP TABLE IF EXISTS {$this->getTable('cn_cmi_crons')};
DROP TABLE IF EXISTS {$this->getTable('cn_cmi_imports')};
DROP TABLE IF EXISTS {$this->getTable('cn_cmi_orders')};
DROP TABLE IF EXISTS {$this->getTable('cn_cmi_messages')};
DROP TABLE IF EXISTS {$this->getTable('cn_cmi_offers')};

CREATE TABLE IF NOT EXISTS " . $this->getTable('product_to_mirakle') . " (
id int(11) NOT NULL,
  meta_key varchar(255) NOT NULL,
  meta_value text NOT NULL
) AUTO_INCREMENT=10  ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('product_to_mirakle')}
 ADD PRIMARY KEY (id);
");
$installer->run('
INSERT INTO ' . $this->getTable('product_to_mirakle') . ' (id, meta_key, meta_value) VALUES
(1, "stored_fields", "[]"),
(2, "api_url", \'{"url":"","api_key":""}\'),
(3, "product_map", \'[{"name":"sku","value":"sku"},{"name":"price","value":"price"},{"name":"quantity","value":"qty"},{"name":"min-quantity-alert","value":"min_qty"},{"name":"available-start-date","value":""},{"name":"available-end-date","value":""},{"name":"logistic-class","value":""}]\'),
(4, "order_acceptance", "0"),
(5, "state_mapping", \'{"ship_state_mapping":"","track_state_mapping":""}\'),
(6, "status_mapping", ""),
(7, "mirakle_status", \'["STAGING","WAITING_ACCEPTANCE","WAITING_DEBIT","WAITING_DEBIT_PAYMENT","SHIPPING","SHIPPED","TO_COLLECT","RECEIVED","CLOSED","REFUSED","CANCELED"]\'),
(8, "carrier_mapping", ""),
(9, "config_valid_data", ""),
(10, "config_log_data", "0");
');
$installer->run('CREATE TABLE IF NOT EXISTS ' . $this->getTable("cn_cmi_crons") . ' (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  type varchar(255) NOT NULL,
  date date NOT NULL,
  executed varchar(255) NOT NULL,
  next_time datetime NOT NULL,
  recursive_value int(11) NOT NULL,
  days varchar(255) NOT NULL,
  weekdays int(11) NOT NULL,
  PRIMARY KEY (id)
) CHARSET=utf8 AUTO_INCREMENT=1;
');
$installer->run('CREATE TABLE IF NOT EXISTS ' . $this->getTable("cn_cmi_imports") . ' (
  id int(11) NOT NULL AUTO_INCREMENT,
  import_id int(11) NOT NULL,
  date_created datetime NOT NULL,
  type varchar(255) NOT NULL,
  status varchar(255) NOT NULL,
  error varchar(255) NOT NULL,
  transform varchar(255) NOT NULL,
  transform_error varchar(255) NOT NULL,
  PRIMARY KEY (id)
) CHARSET=utf8 AUTO_INCREMENT=1;
');
$installer->run('CREATE TABLE IF NOT EXISTS ' . $this->getTable("cn_cmi_orders") . ' (
  id int(11) NOT NULL AUTO_INCREMENT,
  orderid varchar(255) CHARACTER SET utf8 NOT NULL,
  last_updated_date datetime NOT NULL,
  total_price int(11) NOT NULL,
  order_state varchar(255) CHARACTER SET utf8 NOT NULL,
  all_fields longtext CHARACTER SET utf8 NOT NULL,
  m_order_id varchar(255) NOT NULL,
  message varchar(255) NOT NULL,
  order_read tinyint(1) NOT NULL DEFAULT "0",
  has_incident tinyint(1) NOT NULL DEFAULT "0",
  incident_read tinyint(1) NOT NULL DEFAULT "0",
  message_read TINYINT(1) NOT NULL DEFAULT "0",
  PRIMARY KEY (id)
) CHARSET=utf8 AUTO_INCREMENT=1;
');
$installer->run('CREATE TABLE IF NOT EXISTS ' . $this->getTable("cn_cmi_messages") . ' (
  id int(11) NOT NULL AUTO_INCREMENT,
  message_id varchar(255) NOT NULL,
  order_offer_id varchar(255) NOT NULL,
  type_user varchar(255) NOT NULL,
  type_msg varchar(255) NOT NULL,
  all_fields text NOT NULL,
  date_created datetime NOT NULL,
  read_msg int(11) NOT NULL,
  PRIMARY KEY (id)
) CHARSET=utf8 AUTO_INCREMENT=1;
');
$installer->run('CREATE TABLE IF NOT EXISTS ' . $this->getTable("cn_cmi_offers") . ' (
  id int(11) NOT NULL AUTO_INCREMENT,
  offer_sku varchar(255) NOT NULL,
  offer_id varchar(255) NOT NULL,
  all_fields text NOT NULL,
  PRIMARY KEY (id)
) CHARSET=utf8 AUTO_INCREMENT=1;
');
/* Order custom attribute - to identify if automation orders of magento orders */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->getConnection()->addColumn($installer->getTable('sales_flat_order'), 'identity_style_com', 'varchar(255) DEFAULT NULL');
$setup->startSetup();
$setup->addAttribute('order', 'identity_style_com', array(
    'type' => 'int', // or text or whatever
    'label' => 'Style order'
));
$installer->endSetup();
$installer->endSetup();
$installer_attribute = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$data = array(
    'attribute_set' => 'Default',
    'group' => 'General',
    'label' => 'Display in style.com',
    'visible' => true,
    'type' => 'varchar',
    'input' => 'boolean',
    'system' => true,
    'required' => true,
    'user_defined' => 1
);
$installer_attribute->addAttribute('catalog_product', 'display_style_com', $data);
$role_exists = false;
$roles = Mage::getModel('admin/roles')->getCollection();
foreach ($roles as $role) {
    if ($role->getRoleName() == 'Style.com User') {
        $role_exists = true;
    }
}
if (!$role_exists) {
    $roles = array(
        'Style.com User'
    );
    $roleIds = array();
    $resources = explode(',', '__root__,admin/sales,admin/sales/order,admin/sales/order/actions,admin/sales/order/actions/hold,admin/sales/order/actions/creditmemo,admin/sales/order/actions/unhold,admin/sales/order/actions/ship,admin/sales/shipment,admin/sales/invoice,admin/sales/order/actions/comment,admin/sales/order/actions/invoice,admin/sales/order/actions/capture,admin/sales/order/actions/email,admin/sales/order/actions/view,admin/sales/order/actions/reorder,admin/sales/order/actions/edit,admin/sales/order/actions/review_payment,admin/sales/order/actions/cancel,admin/sales/order/actions/create,admin/dashboard,admin/catalog,admin/catalog/products,admin/system,admin/system/acl,admin/system/acl/users,admin/marketplace,admin/marketplace/configure,admin/marketplace/products,admin/marketplace/offers,admin/marketplace/orders,admin/marketplace/scheduler');
    foreach ($roles as $role) {
        $col = Mage::getModel('admin/role')->setRoleName($role)->setRoleType('G')->setTreeLevel(1)->save();
        if ($col->getRoleId()) {
            if (in_array($role, $enabledRoles))
                $roleIds[] = $col->getRoleId();
            $rules = Mage::getModel('admin/rules')->setRoleId($col->getRoleId())->setResources($resources);
            $rules = Mage::getModel('admin/resource_rules')->saveRel($rules);
        }
    }
}

