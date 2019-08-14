<?php
/**
 * Created by PhpStorm.
 * User: xinzheng
 * Date: 14/08/2014
 * Time: 10:41 PM
 */

$installer = $this;
$installer->startSetup();

// add a path to core_config_data table
$resource = Mage::getSingleton('core/resource');
$writeConnection = $resource->getConnection('core_write');


$query_storename = "INSERT INTO core_config_data (config_id, scope_id, scope, path, value) VALUES (null, 'default','0','sr_pushmessage/apiconfig/storename', '' )";
$writeConnection->query($query_storename);
echo "The store name database row has been set";


$query_secret = "INSERT INTO core_config_data (config_id, scope_id, scope, path, value) VALUES (null, 'default','0','sr_pushmessage/apiconfig/secret','' )";
$writeConnection->query($query_secret);
echo "The default secret key database row has been set";

$query_clientid = "INSERT INTO core_config_data (config_id, scope_id, scope, path, value) VALUES (null, 'default','0','sr_pushmessage/apiconfig/clientid','' )";
$writeConnection->query($query_clientid);
echo "The default client id database row has been set";

$installer->endSetup();








