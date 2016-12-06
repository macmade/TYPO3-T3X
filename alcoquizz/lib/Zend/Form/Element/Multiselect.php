<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Element_Select */
require_once 'Zend/Form/Element/Select.php';

/**
 * Multiselect form element
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Multiselect.php 25 2010-06-21 08:05:58Z macmade $
 */
class Zend_Form_Element_Multiselect extends Zend_Form_Element_Select
{
    /**
     * 'multiple' attribute
     * @var string
     */
    public $multiple = 'multiple';

    /**
     * Use formSelect view helper by default
     * @var string
     */
    public $helper = 'formSelect';

    /**
     * Multiselect is an array of values by default
     * @var bool
     */
    protected $_isArray = true;
}
