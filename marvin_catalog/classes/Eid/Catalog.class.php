<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 netinfluence                                                        #
# All rights reserved                                                          #
#                                                                              #
# This script is part of the TYPO3 project. The TYPO3 project is free          #
# software. You can redistribute it and/or modify it under the terms of the    #
# GNU General Public License as published by the Free Software Foundation,     #
# either version 2 of the License, or (at your option) any later version.      #
#                                                                              #
# The GNU General Public License can be found at:                              #
# http://www.gnu.org/copyleft/gpl.html.                                        #
#                                                                              #
# This script is distributed in the hope that it will be useful, but WITHOUT   #
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or        #
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for    #
# more details.                                                                #
#                                                                              #
# This copyright notice MUST APPEAR in all copies of the script!               #
################################################################################

# $Id: Catalog.class.php 196 2010-01-27 10:18:42Z julien $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Magento catalog getter
 *
 * @author      Julien Dunand <julien@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_Eid_Catalog extends tx_marvincatalog_Eid_Base
{
    public function __toString()
    {
    	$moodOnly = is_null(t3lib_div::_GET('mood_only')) ? 0 : t3lib_div::_GET('mood_only');
    	$storeId = is_null(t3lib_div::_GET('lang')) ? 'admin' : t3lib_div::_GET('lang') . '_store';
    	$prefix = $this->_conf['db_prefix'];
    	$this->_db->debugOutput = 1;
        $xml = '<watches>' . PHP_EOL;
        
        // Moods
        $fields = $prefix.'eav_attribute_option_value.option_id, '.$prefix.'eav_attribute_option_value.value';
        $tables = $prefix.'eav_attribute, '.$prefix.'eav_attribute_option, '.$prefix.'eav_attribute_option_value';
        $where = $prefix.'eav_attribute.attribute_code = "watch_mood" ';
        $where .= 'AND '.$prefix.'eav_attribute.attribute_id = '.$prefix.'eav_attribute_option.attribute_id ';
        $where .= 'AND '.$prefix.'eav_attribute_option.option_id = '.$prefix.'eav_attribute_option_value.option_id'; 
        
        $moodNames = array();
        $result = $this->_db->exec_SELECTquery($fields, $tables, $where);
        if ($result !== false) {
            while (($row = $this->_db->sql_fetch_assoc($result)) !== false) {
                $moodNames[$row['option_id']] = str_replace(' ', '', strtoupper(($row['value'])));
            }
        }
        $this->_db->sql_free_result($result);
        
        // Attributes
        $columns = $prefix.'catalog_product_entity.*, '.$prefix.'catalog_product_entity_varchar.*, '.$prefix.'eav_attribute.*, '.$prefix.'catalog_category_product.*, '.$prefix.'core_store.*, '
                 . $prefix.'catalog_product_entity_text.value_id       AS text_value_id, '
                 . $prefix.'catalog_product_entity_text.entity_type_id AS text_attribute_id, '
                 . $prefix.'catalog_product_entity_text.attribute_id   AS text_attribute_id, '
                 . $prefix.'catalog_product_entity_text.store_id       AS text_store_id, '
                 . $prefix.'catalog_product_entity_text.entity_id      AS text_entity_id, '
                 . $prefix.'catalog_product_entity_text.value          AS text_value';
        $tables  = $prefix.'catalog_product_entity, '.$prefix.'catalog_product_entity_varchar, '.$prefix.'eav_attribute, '.$prefix.'catalog_category_product, '.$prefix.'core_store, '.$prefix.'catalog_product_entity_text';
        $where   = $prefix.'catalog_product_entity.entity_id = '.$prefix.'catalog_product_entity_varchar.entity_id ';
        $where  .= 'AND '.$prefix.'catalog_product_entity_varchar.attribute_id = '.$prefix.'eav_attribute.attribute_id ';
        $where  .= 'AND '.$prefix.'catalog_product_entity_text.entity_id = '.$prefix.'catalog_product_entity.entity_id ';
        $where  .= 'AND '.$prefix.'catalog_product_entity.entity_id = '.$prefix.'catalog_category_product.product_id ';
        $where  .= 'AND '.$prefix.'core_store.code = "'. mysql_escape_string($storeId) . '" AND '.$prefix.'core_store.store_id = '.$prefix.'catalog_product_entity_varchar.store_id';
        $order = $prefix.'catalog_product_entity.entity_id';
        
        $data = array();
        $moods = array();
        $result = $this->_db->exec_SELECTquery($columns, $tables, $where, '', $order);
        if ($result !== false) {
            while (($row = $this->_db->sql_fetch_assoc($result)) !== false) {
                if (!isset($data[$row['sku']])) $data[$row['sku']] = array('categories'=>array());
                if (!isset($data[$row['sku']])) $data[$row['sku']] = array();
                if (!isset($data[$row['sku']]['description'])) $data[$row['sku']]['description'] = $row['text_value'];
                if (!isset($moods[$row['sku']])) $moods[$row['sku']] = null;
                if ($row['attribute_code'] == 'watch_mood') {
                    $moodArray = explode(',', $row['value']);
                    foreach ($moodArray as $m => $mood) {
                        $moodArray[$m] = $moodNames[$mood];
                    }
                    $moods[$row['sku']] = implode(',', $moodArray);
                } else {
                    $data[$row['sku']][$row['attribute_code']] = $row['value'];
                }
                if (!isset($data[$row['sku']]['categories'][$row['category_id']])) {
                    $data[$row['sku']]['categories'][$row['category_id']] = $row['category_id'];
                }
            }
        }
        $this->_db->sql_free_result($result);
        
        $cpt = 0;
        $attributesArray = array('name', 'image', 'watch_move', 'watch_display', 'watch_case', 'thumbnail', 'small_image',
                                 'watch_dial', 'watch_glass', 'watch_atm', 'watch_bracelet', 'description', 'url_key',
                                 'watch_details_image', 'watch_catalog_image', 'watch_zoom_image', 'shop_urls');
        foreach ($data as $sku => $attributes) {
            $mood = $moods[$sku];
            if ($moodOnly > 0 && $mood == '') continue;
            $xml .= '<watch tag="' . $mood . '" sku="' . $sku . '">' . PHP_EOL;
            $xml .= '<mood_id>' . $cpt . '</mood_id>';
            foreach ($attributes as $key => $attribute) {
                if (in_array($key, $attributesArray)) {
                    $attributeValue = ( strstr( $attribute, '&' ) || strstr( $attribute, '<' ) ) ? '<![CDATA[' . $attribute . ']]>' : $attribute;
                    $xml .= '<' . $key . '>' . $attributeValue . '</' . $key . '>' . PHP_EOL;
                } elseif ($key==='categories') {
                    $xml .= '<' . $key . '>' . implode( ',', $attribute ) . '</' . $key . '>' . PHP_EOL;
                }
            }
            $xml .= '</watch>' . PHP_EOL;
            $cpt ++;
        }
        
        $xml .= '</watches>' . PHP_EOL;
        
        return $xml;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Eid/Catalog.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Eid/Catalog.class.php']);
}
