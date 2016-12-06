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

# $Id: Moods.class.php 187 2010-01-05 12:47:22Z julien $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Magento mood getter
 *
 * @author      Julien Dunand <julien@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_Eid_Moods extends tx_marvincatalog_Eid_Base
{
    public function __toString()
    {
        $xml = '<moods>' . PHP_EOL;
        
        $prefix = $this->_conf['db_prefix'];
        
        // Moods
        $fields = $prefix.'eav_attribute_option_value.option_id, '.$prefix.'eav_attribute_option_value.value';
        $tables = $prefix.'eav_attribute, '.$prefix.'eav_attribute_option, '.$prefix.'eav_attribute_option_value';
        $where = $prefix.'eav_attribute.attribute_code = "watch_mood" ';
        $where .= 'AND '.$prefix.'eav_attribute.attribute_id = '.$prefix.'eav_attribute_option.attribute_id ';
        $where .= 'AND '.$prefix.'eav_attribute_option.option_id = '.$prefix.'eav_attribute_option_value.option_id'; 
        
        $moods = array();
        $moodNames = array();
        $result = $this->_db->exec_SELECTquery($fields, $tables, $where);
        if ($result !== false) {
            while (($row = $this->_db->sql_fetch_assoc($result)) !== false) {
            	$mood = utf8_decode($row['value']);
                $moodNames[$row['option_id']] = $mood;
                $moods[$mood] = '';
            }
        }
        $this->_db->sql_free_result($result);
        
        // Attributes
        $fields = $prefix.'catalog_product_entity.sku, value';
        $tables = $prefix.'catalog_product_entity, '.$prefix.'catalog_product_entity_varchar, '.$prefix.'eav_attribute';
        $where = $prefix.'catalog_product_entity.entity_id = '.$prefix.'catalog_product_entity_varchar.entity_id ';
        $where .= 'AND '.$prefix.'catalog_product_entity_varchar.attribute_id = '.$prefix.'eav_attribute.attribute_id ';
        $where .= 'AND '.$prefix.'eav_attribute.attribute_code = "watch_mood"';
        $order = $prefix.'catalog_product_entity.entity_id';
        
        $result = $this->_db->exec_SELECTquery($fields, $tables, $where, '', $order);
        if ($result !== false) {
        	$count = 0;
            while (($row = $this->_db->sql_fetch_assoc($result)) !== false) {
                $moodArray = explode(',', $row['value']);
                foreach ($moodArray as $m => $mood) {
                    //$moods[$moodNames[$mood]] = substr_replace($moods[$moodNames[$mood]], '1', $count, 1);
                    $moods[$moodNames[$mood]] .= $count . ',';
                }
                $count ++;
            }
        }
        $this->_db->sql_free_result($result);
        
        foreach ($moods as $name => $value) {
            $xml .= '<mood>' . PHP_EOL;
            $xml .= '<name>' . $name . '</name>' . PHP_EOL;
            $xml .= '<value>' .substr($value, 0, -1) . '</value>' . PHP_EOL;
            $xml .= '</mood>' . PHP_EOL;
        }
        
        $xml .= '</moods>' . PHP_EOL;
        
        return $xml;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Eid/Moods.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Eid/Moods.class.php']);
}
