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

# $Id: Categories.class.php 201 2010-03-15 08:00:19Z julien $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Magento categories getter
 *
 * @author      Julien Dunand <julien@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_Eid_Categories extends tx_marvincatalog_Eid_Base
{
    public function __toString()
    {
        $xml = '<categories>' . PHP_EOL;
        
        $prefix = $this->_conf['db_prefix'];
        
        // add to config ?
        $catIds = array(1, 3, 4, 5, 6);
        
        // Attributes
        // Attention : "attribute_id = 32" is the "is_active" attribute (hardcoded)
        $tables = $prefix.'catalog_category_entity, '.$prefix.'catalog_category_entity_int, '.$prefix.'catalog_category_entity_varchar, '.$prefix.'eav_attribute';
        $where = $prefix.'catalog_category_entity.entity_id = '.$prefix.'catalog_category_entity_varchar.entity_id ';
        $where .= 'AND '.$prefix.'catalog_category_entity_varchar.attribute_id = '.$prefix.'eav_attribute.attribute_id ';
        $where .= 'AND ('.$prefix.'catalog_category_entity.entity_id = '.$prefix.'catalog_category_entity_int.entity_id ';
        $where .= 'AND '.$prefix.'catalog_category_entity_int.attribute_id = 32 AND '.$prefix.'catalog_category_entity_int.value = 1 OR catalog_category_entity.entity_id = 1) ';
        $where .= 'AND (0=1';
		foreach ($catIds as $catId) {
			$where .= ' OR catalog_category_entity.entity_id = ' . $catId;
			$where .= ' OR catalog_category_entity.parent_id = ' . $catId;
		}
		$where .= ')';
        $order = $prefix.'catalog_category_entity.level, ' . $prefix.'catalog_category_entity.position';        
  
        $data = array();
        $result = $this->_db->exec_SELECTquery('*', $tables, $where, '', $order);
        
        if ($result !== false) {
            while (($row = $this->_db->sql_fetch_assoc($result)) !== false) {
                if (!isset($data[$row['entity_id']])) {
                    $data[$row['entity_id']] = array();
                    $data[$row['entity_id']]['parent_id'] = $row['parent_id'];	
                }
                $data[$row['entity_id']][$row['attribute_code']] = $row['value'];
            }
        }
        $this->_db->sql_free_result($result);
        
        $attributesArray = array('name', 'parent_id', 'url_key', 'url_path', 'image');
        foreach ($data as $id => $attributes) {
            $xml .= '<category id="' . $id . '">' . PHP_EOL;
            foreach ($attributes as $key => $attribute) {
                if (in_array($key, $attributesArray)) {
                    $xml .= '<' . $key . '>' . $attribute . '</' . $key . '>' . PHP_EOL;
                }		
            }
            $xml .= '</category>' . PHP_EOL;
        }
        
        $xml .= '</categories>' . PHP_EOL;
        
        return $xml;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Eid/Categories.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Eid/Categories.class.php']);
}
