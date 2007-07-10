<?php
	
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Categories TCA
	 */
	$TCA['tx_eespbasedocs_categories'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_eespbasedocs_categories']['ctrl'],
		
		// BE interface
		'interface' => Array (
			
			// Fields available
			'showRecordFieldList' => 'hidden,name'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_eespbasedocs_categories']['feInterface'],
		
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
			'name' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_categories.name',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, name;;;;2-2-2')
		),
		
		// Palettes configuration
		'palettes' => Array ()
	);
	
	/**
	 * Instances TCA
	 */
	$TCA['tx_eespbasedocs_instances'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_eespbasedocs_instances']['ctrl'],
		
		// BE interface
		'interface' => Array (
			
			// Fields available
			'showRecordFieldList' => 'hidden,title,operation,members,responsabilities,goals,communications,activities'
		),
		
		// FE interface
		'feInterface' => $TCA['tx_eespbasedocs_instances']['feInterface'],
		
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
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_instances.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
			'operation' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_instances.operation',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'members' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_instances.members',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:eesp_basedocs/flexform_ds_instances_members.xml',
					),
				)
			),
			'responsabilities' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_instances.responsabilities',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'goals' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_instances.goals',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'communications' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_instances.communications',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:eesp_basedocs/flexform_ds_instances_communications.xml',
					),
				)
			),
			'activities' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_instances.activities',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:eesp_basedocs/flexform_ds_instances_activites.xml',
					),
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, title;;;;2-2-2, operation;;;richtext[cut|copy|paste|bold|italic|underline|orderedlist|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_eespbasedocs/rte/];3-3-3, members, responsabilities;;;richtext[cut|copy|paste|bold|italic|underline|orderedlist|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_eespbasedocs/rte/], goals;;;richtext[cut|copy|paste|bold|italic|underline|orderedlist|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_eespbasedocs/rte/], communications, activities')
		),
		
		// Palettes configuration
		'palettes' => Array ()
	);
	
	/**
	 * Activities TCA
	 */
	$TCA['tx_eespbasedocs_activity'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_eespbasedocs_activity']['ctrl'],
		
		// BE interface
		'interface' => Array (
			
			// Fields available
			'showRecordFieldList' => 'hidden,title,cat,description,incharge,secretariat,reference,public,goals,domains,collab_internal,collab_external,documents,instances'
		),
		
		// FE interface
		'feInterface' => $TCA['tx_eespbasedocs_activity']['feInterface'],
		
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
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
			'cat' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.cat',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_eespbasedocs_categories',
					'foreign_table_where' => 'AND tx_eespbasedocs_categories.pid=###CURRENT_PID### ORDER BY tx_eespbasedocs_categories.sorting',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.description',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'incharge' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.incharge',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:eesp_basedocs/flexform_ds_activity_incharge.xml',
					),
				)
			),
			'secretariat' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.secretariat',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:eesp_basedocs/flexform_ds_activity_secretariat.xml',
					),
				)
			),
			'reference' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.reference',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'public' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.public',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'goals' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.goals',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'domains' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.domains',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'collab_internal' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.collab_internal',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'collab_external' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.collab_external',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'documents' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.documents',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:eesp_basedocs/flexform_ds_activity_documents.xml',
					),
				)
			),
			'instances' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity.instances',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_eespbasedocs_instances',
					'foreign_table_where' => 'AND tx_eespbasedocs_instances.pid=###CURRENT_PID### ORDER BY tx_eespbasedocs_instances.uid',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 25,
					'MM' => 'tx_eespbasedocs_activity_instances_mm',
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_eespbasedocs_instances',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
						'edit' => Array(
							'type' => 'popup',
							'title' => 'Edit',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					),
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, title;;;;2-2-2, cat, description;;;richtext[cut|copy|paste|bold|italic|underline|orderedlist|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_eespbasedocs/rte/];3-3-3, incharge, secretariat, reference;;;richtext[cut|copy|paste|bold|italic|underline|orderedlist|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_eespbasedocs/rte/], public;;;richtext[cut|copy|paste|bold|italic|underline|orderedlist|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_eespbasedocs/rte/], goals;;;richtext[cut|copy|paste|bold|italic|underline|orderedlist|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_eespbasedocs/rte/], domains;;;richtext[cut|copy|paste|bold|italic|underline|orderedlist|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_eespbasedocs/rte/], collab_internal;;;richtext[cut|copy|paste|bold|italic|underline|orderedlist|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_eespbasedocs/rte/], collab_external;;;richtext[cut|copy|paste|bold|italic|underline|orderedlist|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_eespbasedocs/rte/], documents, instances')
		),
		
		// Palettes configuration
		'palettes' => Array ()
	);
?>
