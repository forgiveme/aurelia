<?php
$installer = $this;
$installer->startSetup();

/*
 * Add _styledotcom_group_id to list of fields to be exported in CSVs
 * and shown as checked checkboxes on products page. 
 */

// Get current list of fields included in csv uploads 
$selected_fields = $this->_conn->fetchAll( '
    SELECT meta_value FROM product_to_mirakle 
    WHERE meta_key = "stored_fields";
');
$selected_fields = $selected_fields[0]["meta_value"];
$sel_fields_arr = json_decode($selected_fields);


if (count($sel_fields_arr) > 0) {

    // Append 'group_id' field to array
    $sel_fields_arr[] = '_styledotcom_group_id';

    // Sort array so 'group_id' sits in the right place 
    // for alphabetical order
    asort($sel_fields_arr);

    // Re-encode field as json, ensure that if field is already 
    // present we don't include it twice.
    $selected_fields = json_encode(array_unique(array_values($sel_fields_arr)));

    // Use a placeholder for selected_fields value in update
    // statement so quotes are handled nicely
    $this->_conn->query('
            UPDATE '. $this->getTable( 'product_to_mirakle' ) .
            ' SET meta_value = ? WHERE meta_key="stored_fields";
    ', [$selected_fields] );
}

$installer->endSetup();
?>
