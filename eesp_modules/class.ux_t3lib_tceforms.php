<?php
	/***************************************************************
	 * Copyright notice
	 * 
	 * (c) 2004 Jean-David Gadina (info@macmade.net)
	 * All rights reserved
	 * 
	 * This script is part of the TYPO3 project. The TYPO3 project is 
	 * free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 * 
	 * The GNU General Public License can be found at
	 * http://www.gnu.org/copyleft/gpl.html.
	 * 
	 * This script is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 * 
	 * This copyright notice MUST APPEAR in all copies of the script!
	***************************************************************/
	
	/** 
	 * TCEForms extension
	 *
	 * @author		Jean-David Gadina (info@macmade.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *        :		function handleYears(&$params,&$pObj)
	 * 
	 *				TOTAL FUNCTIONS: 1
	 */
	
	class ux_t3lib_TCEforms extends t3lib_TCEforms {
		
		/**
		 * [Describe function...]
		 *
		 * @param	[type]		$dataStruct: ...
		 * @param	[type]		$editData: ...
		 * @param	[type]		$cmdData: ...
		 * @param	[type]		$table: ...
		 * @param	[type]		$field: ...
		 * @param	[type]		$row: ...
		 * @param	[type]		$PA: ...
		 * @param	[type]		$formPrefix: ...
		 * @param	[type]		$level: ...
		 * @param	[type]		$tRows: ...
		 * @return	[type]		...
		 */
		function getSingleField_typeFlex_draw($dataStruct,$editData,$cmdData,$table,$field,$row,&$PA,$formPrefix='',$level=0,$tRows=array())	{
	
				// Data Structure array must be ... and array of course...
			if (is_array($dataStruct))	{
				foreach($dataStruct as $key => $value)	{
					if (is_array($value))	{	// The value of each entry must be an array.
	
							// ********************
							// Making the row:
							// ********************
						$rowCells=array();
	
							// Icon:
						$rowCells['title'] = '<img src="clear.gif" width="'.($level*16).'" height="1" alt="" /><strong>'.htmlspecialchars(t3lib_div::fixed_lgd_cs($this->sL($value['tx_templavoila']['title']),30)).'</strong>';;
	
						$rowCells['formEl']='';
						if ($value['type']=='array')	{
							if ($value['section'])	{
									// Render "NEW [container]" selectorbox:
								if (is_array($value['el']))	{
									$opt=array();
									$opt[]='<option value=""></option>';
									foreach($value['el'] as $kk => $vv)	{
										$opt[]='<option value="'.$kk.'">'.htmlspecialchars($this->getLL('l_new') . ' "'.$this->sL($value['el'][$kk]['tx_templavoila']['title']).'"').'</option>';
									}
									$rowCells['formEl']='<select name="flexFormsCmdData'.$formPrefix.'['.$key.'][value]">'.implode('',$opt).'</select>';
								}
	
									// Put row together
								$tRows[]='<tr class="bgColor2">
									<td nowrap="nowrap" valign="top">'.$rowCells['title'].'</td>
									<td>'.$rowCells['formEl'].'</td>
								</tr>';
	
								$cc=0;
								if (is_array($editData[$key]['el']))	{
									foreach ($editData[$key]['el'] as $k3 => $v3)	{
										$cc=$k3;
										$theType = key($v3);
										$theDat = $v3[$theType];
										$newSectionEl = $value['el'][$theType];
										if (is_array($newSectionEl))	{
											$tRows = $this->getSingleField_typeFlex_draw(
												array($theType => $newSectionEl),
												array($theType => $theDat),
												$cmdData[$key]['el'][$cc],
												$table,
												$field,
												$row,
												$PA,
												$formPrefix.'['.$key.'][el]['.$cc.']',
												$level+1,
												$tRows
											);
										}
									}
								}
	
	
	
									// New form?
								if ($cmdData[$key]['value'])	{
									$newSectionEl = $value['el'][$cmdData[$key]['value']];
									if (is_array($newSectionEl))	{
										$tRows = $this->getSingleField_typeFlex_draw(
											array($cmdData[$key]['value'] => $newSectionEl),
											array(),
											array(),
											$table,
											$field,
											$row,
											$PA,
											$formPrefix.'['.$key.'][el]['.($cc+1).']',
											$level+1,
											$tRows
										);
									}
								}
								
									// Put row together
								$tRows[]='<tr class="bgColor2">
									<td nowrap="nowrap" valign="top">'.$rowCells['title'].'</td>
									<td>'.$rowCells['formEl'].'</td>
								</tr>';
								
							} else {
								$idTagPrefix = uniqid('id',true); // ID attributes are used for the move and delete checkboxes for referencing to them in the label tag (<label for="the form field id">) that's rendered around the icons
	
									// Put row together
								$tRows[]='<tr class="bgColor2">
									<td nowrap="nowrap" valign="top">'.
									'<input name="_DELETE_FLEX_FORM'.$PA['itemFormElName'].$formPrefix.'" id="'.$idTagPrefix.'-del" type="checkbox"'.$this->insertDefStyle('check').' value="1" /><label for="'.$idTagPrefix.'-del"><img src="'.$this->backPath.'gfx/garbage.gif" border="0" alt="" /></label>'.
									'<input name="_MOVEUP_FLEX_FORM'.$PA['itemFormElName'].$formPrefix.'" id="'.$idTagPrefix.'-mvup" type="checkbox"'.$this->insertDefStyle('check').' value="1" /><label for="'.$idTagPrefix.'-mvup"><img src="'.$this->backPath.'gfx/button_up.gif" border="0" alt="" /></label>'.
									'<input name="_MOVEDOWN_FLEX_FORM'.$PA['itemFormElName'].$formPrefix.'" id="'.$idTagPrefix.'-mvdown" type="checkbox"'.$this->insertDefStyle('check').' value="1" /><label for="'.$idTagPrefix.'-mvdown"><img src="'.$this->backPath.'gfx/button_down.gif" border="0" alt="" /></label>'.
									$rowCells['title'].'</td>
									<td>'.$rowCells['formEl'].'</td>
								</tr>';
	
								$tRows = $this->getSingleField_typeFlex_draw(
									$value['el'],
									$editData[$key]['el'],
									$cmdData[$key]['el'],
									$table,
									$field,
									$row,
									$PA,
									$formPrefix.'['.$key.'][el]',
									$level+1,
									$tRows
								);
							}
	
						} elseif (is_array($value['TCEforms']['config'])) {	// Rendering a single form element:
	
							if (is_array($PA['_valLang']))	{
								$rotateLang = $PA['_valLang'];
							} else {
								$rotateLang = array($PA['_valLang']);
							}
	
							foreach($rotateLang as $vDEFkey)	{
								$vDEFkey = 'v'.$vDEFkey;
	
								if (!$value['TCEforms']['displayCond'] || $this->isDisplayCondition($value['TCEforms']['displayCond'],$editData,$vDEFkey)) {
									$fakePA=array();
									$fakePA['fieldConf']=array(
										'label' => $this->sL(trim($value['TCEforms']['label'])),
										'config' => $value['TCEforms']['config'],
										'defaultExtras' => $value['TCEforms']['defaultExtras'],
										'onChange' => $value['TCEforms']['onChange']
									);
									if ($PA['_noEditDEF'] && $PA['_lang']==='lDEF') {
										$fakePA['fieldConf']['config'] = array(
											'type' => 'none',
											'rows' => 2
										);
									}
	
									if (
										$fakePA['fieldConf']['onChange'] == 'reload' ||
										($GLOBALS['TCA'][$table]['ctrl']['type'] && !strcmp($key,$GLOBALS['TCA'][$table]['ctrl']['type'])) ||
										($GLOBALS['TCA'][$table]['ctrl']['requestUpdate'] && t3lib_div::inList($GLOBALS['TCA'][$table]['ctrl']['requestUpdate'],$key))) {
										if ($GLOBALS['BE_USER']->jsConfirmation(1))	{
											$alertMsgOnChange = 'if (confirm('.$GLOBALS['LANG']->JScharCode($this->getLL('m_onChangeAlert')).') && TBE_EDITOR_checkSubmit(-1)){ TBE_EDITOR_submitForm() };';
										} else {
											$alertMsgOnChange = 'if(TBE_EDITOR_checkSubmit(-1)){ TBE_EDITOR_submitForm();}';
										}
									} else {
										$alertMsgOnChange = '';
									}
	
									$fakePA['fieldChangeFunc']=$PA['fieldChangeFunc'];
									if (strlen($alertMsgOnChange)) {
										$fakePA['fieldChangeFunc']['alert']=$alertMsgOnChange;
									}
									$fakePA['onFocus']=$PA['onFocus'];
									$fakePA['label']=$PA['label'];
	
									$fakePA['itemFormElName']=$PA['itemFormElName'].$formPrefix.'['.$key.']['.$vDEFkey.']';
									$fakePA['itemFormElName_file']=$PA['itemFormElName_file'].$formPrefix.'['.$key.']['.$vDEFkey.']';
	
									if(isset($editData[$key][$vDEFkey])) {
										$fakePA['itemFormElValue']=$editData[$key][$vDEFkey];
									} else {
										$fakePA['itemFormElValue']=$fakePA['fieldConf']['config']['default'];
									}
	
									$rowCells['formEl']= $this->getSingleField_SW($table,$field,$row,$fakePA);
									$rowCells['title']= htmlspecialchars($fakePA['fieldConf']['label']);
	
									if (!in_array('DEF',$rotateLang))	{
										$defInfo = '<div class="typo3-TCEforms-originalLanguageValue">'.nl2br(htmlspecialchars($editData[$key]['vDEF'])).'&nbsp;</div>';
									} else {
										$defInfo = '';
									}
	
										// Put row together
									$tRows[]='<tr>
										<td nowrap="nowrap" valign="top" class="bgColor5">'.$rowCells['title'].($vDEFkey=='vDEF' ? '' : ' ('.$vDEFkey.')').'</td>
										<td class="bgColor4">'.$rowCells['formEl'].$defInfo.'</td>
									</tr>';
								}
							}
						}
					}
				}
			}
	
			return $tRows;
		}
	}
	
	/**
	 * XClass inclusion.
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_modules/class.ux_t3lib_tceforms.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_modules/class.ux_t3lib_tceforms.php']);
	}
?>
