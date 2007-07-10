<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Graduate programs TCA
	 */
	$TCA['tx_eespmodules_programs'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_eespmodules_programs']['ctrl'],
		
		// BE interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,starttime,endtime,startyear,term1_startweek,term2_startweek,terms'
		),
		
		// FE interface
		'feInterface' => $TCA['tx_eespmodules_programs']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'starttime' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
				'config' => Array (
					'type' => 'input',
					'size' => '8',
					'max' => '20',
					'eval' => 'date',
					'default' => '0',
					'checkbox' => '0'
				)
			),
			'endtime' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
				'config' => Array (
					'type' => 'input',
					'size' => '8',
					'max' => '20',
					'eval' => 'date',
					'checkbox' => '0',
					'default' => '0',
					'range' => Array (
						'upper' => mktime(0,0,0,12,31,2020),
						'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
					)
				)
			),
			'startyear' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_modules/locallang_db.php:tx_eespmodules_programs.startyear',
				'config' => Array (
					'type' => 'select',
					'itemsProcFunc' => 'tx_eespmodules_helpers->handleYears',
					'size' => 1,
					'maxitems' => 1,
				)
			),
			'term1_startweek' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_modules/locallang_db.php:tx_eespmodules_programs.term1_startweek',
				'config' => Array (
					'type' => 'select',
					'size' => 1,
					'maxitems' => 1,
					'default' => '43',
					'items' => array(
						array('1',1),
						array('2',2),
						array('3',3),
						array('4',4),
						array('5',5),
						array('6',6),
						array('7',7),
						array('8',8),
						array('9',9),
						array('10',10),
						array('11',11),
						array('12',12),
						array('13',13),
						array('14',14),
						array('15',15),
						array('16',16),
						array('17',17),
						array('18',18),
						array('19',19),
						array('20',20),
						array('21',21),
						array('22',22),
						array('23',23),
						array('24',24),
						array('25',25),
						array('26',26),
						array('27',27),
						array('28',28),
						array('29',29),
						array('30',30),
						array('31',31),
						array('32',32),
						array('33',33),
						array('34',34),
						array('35',35),
						array('36',36),
						array('37',37),
						array('38',38),
						array('39',39),
						array('40',40),
						array('41',41),
						array('42',42),
						array('43',43),
						array('44',44),
						array('45',45),
						array('46',46),
						array('47',47),
						array('48',48),
						array('49',49),
						array('50',50),
						array('51',51),
						array('52',52),
						array('53',53),
					),
				)
			),
			'term2_startweek' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_modules/locallang_db.php:tx_eespmodules_programs.term2_startweek',
				'config' => Array (
					'type' => 'select',
					'size' => 1,
					'maxitems' => 1,
					'default' => '11',
					'items' => array(
						array('1',1),
						array('2',2),
						array('3',3),
						array('4',4),
						array('5',5),
						array('6',6),
						array('7',7),
						array('8',8),
						array('9',9),
						array('10',10),
						array('11',11),
						array('12',12),
						array('13',13),
						array('14',14),
						array('15',15),
						array('16',16),
						array('17',17),
						array('18',18),
						array('19',19),
						array('20',20),
						array('21',21),
						array('22',22),
						array('23',23),
						array('24',24),
						array('25',25),
						array('26',26),
						array('27',27),
						array('28',28),
						array('29',29),
						array('30',30),
						array('31',31),
						array('32',32),
						array('33',33),
						array('34',34),
						array('35',35),
						array('36',36),
						array('37',37),
						array('38',38),
						array('39',39),
						array('40',40),
						array('41',41),
						array('42',42),
						array('43',43),
						array('44',44),
						array('45',45),
						array('46',46),
						array('47',47),
						array('48',48),
						array('49',49),
						array('50',50),
						array('51',51),
						array('52',52),
						array('53',53),
					),
				)
			),
			'terms' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_modules/locallang_db.php:tx_eespmodules_programs.terms',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:eesp_modules/flexform_ds.xml',
					),
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, startyear;;;;2-2-2, term1_startweek, term2_startweek, terms;;;;3-3-3')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime')
		)
	);
?>
