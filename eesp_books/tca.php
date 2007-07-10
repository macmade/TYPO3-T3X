<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Books TCA
	 */
	$TCA['tx_eespbooks_books'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_eespbooks_books']['ctrl'],
		
		'interface' => Array (
			
			// Field list in the BE-interface
			'showRecordFieldList' => 'hidden,starttime,endtime,bookid,isbn,pubyear,pubplace,pubmanagers,reedition,price_chf,price_eur,pages,format,physicaldetails,copies,flyers,pdf_locked,pdf_unlocked,title,subtitle,authors,abstract,analysis,bibliography,cover,original'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_eespbooks_books']['feInterface'],
		
		// Backend fields configuration
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
			'bookid' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.bookid',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'max' => '5',
					'range' => Array ('lower'=>0,'upper'=>1000),
					'eval' => 'required,int',
				)
			),
			'isbn' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.isbn',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'pubyear' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.pubyear',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'max' => '4',
					'eval' => 'year',
				)
			),
			'pubplace' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.pubplace',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'pubmanagers' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.pubmanagers',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'reedition' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.reedition',
				'config' => Array (
					'type' => 'check',
					'default' => 0,
				)
			),
			'price_chf' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.price_chf',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'price_eur' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.price_eur',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'pages' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.pages',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'range' => Array ('lower'=>0,'upper'=>1000),
					'eval' => 'int',
				)
			),
			'format' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.format',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'physicaldetails' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.physicaldetails',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'copies' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.copies',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'range' => Array ('lower'=>0,'upper'=>10000),
					'checkbox' => '0',
					'eval' => 'int',
				)
			),
			'stock' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.stock',
				'config' => Array (
					'type' => 'none',
				)
			),
			'flyers' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.flyers',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'range' => Array ('lower'=>0,'upper'=>10000),
					'checkbox' => '0',
					'eval' => 'int',
				)
			),
			'pdf_locked' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.pdf_locked',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'pdf',
					'max_size' => 10240,
					'uploadfolder' => 'uploads/tx_eespbooks',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'pdf_unlocked' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.pdf_unlocked',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'pdf',
					'max_size' => 10240,
					'uploadfolder' => 'uploads/tx_eespbooks',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'subtitle' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.subtitle',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'authors' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.authors',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'collection' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.collection',
				'config' => Array (
					'type' => 'radio',
					'items' => Array (
						Array('LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.collection.I.0', '0'),
						Array('LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.collection.I.1', '1'),
						Array('LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.collection.I.2', '2'),
					),
					'size' => 1,
					'maxitems' => 1,
				)
			),
			'abstract' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.abstract',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'analysis' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.analysis',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'bibliography' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.bibliography',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'cover' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.cover',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
					'max_size' => 500,
					'uploadfolder' => 'uploads/tx_eespbooks',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'original' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.original',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'tx_eespbooks_books',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => '--div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.s_product, hidden;;1;;1-1-1, bookid;;;;--1, isbn, pubyear;;;;--1, pubplace, pubmanagers, reedition;;;;--1, price_chf;;;;--1, price_eur, pages;;;;--1, format, physicaldetails, copies;;;;--1, flyers, pdf_locked;;;;--1, pdf_unlocked, --div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.s_notice, title;;;;1-1-1, subtitle, authors;;;;--1, collection, abstract;;;;--1, analysis, bibliography, cover;;;;--1, --div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.s_stock, stock;;;;1-1-1'),
			'1' => Array('showitem' => '--div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.s_product, hidden;;1;;1-1-1, bookid;;;;--1, isbn, pubyear;;;;--1, pubplace, pubmanagers, reedition;;;;--1, price_chf;;;;--1, price_eur, pages;;;;--1, format, physicaldetails, copies;;;;--1, flyers, pdf_locked;;;;--1, pdf_unlocked, --div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.s_notice, title;;;;1-1-1, original;;;;--1, bibliography;;;;--1, cover;;;;--1, --div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books.s_stock, stock;;;;1-1-1'),
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime'),
		)
	);
	
	/**
	 * Monthes TCA
	 */
	$TCA['tx_eespbooks_monthes'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_eespbooks_monthes']['ctrl'],
		
		'interface' => Array (
			
			// Field list in the BE-interface
			'showRecordFieldList' => 'stock_month,stock_year'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_eespbooks_monthes']['feInterface'],
		
		// Backend fields configuration
		'columns' => Array (
			'stock_month' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_monthes.stock_month',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'stock_year' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_monthes.stock_year',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'stock_month;;;;1-1-1, stock_year'),
		),
		
		// Palettes configuration
		'palettes' => Array ()
	);
	
	/**
	 * Stock TCA
	 */
	$TCA['tx_eespbooks_stock'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_eespbooks_stock']['ctrl'],
		
		'interface' => Array (
			
			// Field list in the BE-interface
			'showRecordFieldList' => 'rel_book,rel_month,sales_reception,sales_bill,sales_free,students_distribution,students_exchange,events_out,events_back,send_albert,send_cid,back_students,back_cid,comments'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_eespbooks_stock']['feInterface'],
		
		// Backend fields configuration
		'columns' => Array (
			'rel_book' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.rel_book',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'tx_eespbooks_books',
					'size' => 1,
					'minitems' => 1,
					'maxitems' => 1,
				)
			),
			'rel_month' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.rel_month',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'tx_eespbooks_monthes',
					'size' => 1,
					'minitems' => 1,
					'maxitems' => 1,
				)
			),
			'sales_reception' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.sales_reception',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'sales_bill' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.sales_bill',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'sales_free' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.sales_free',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'students_distribution' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.students_distribution',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'students_exchange' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.students_exchange',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'events_out' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.events_out',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'events_back' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.events_back',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'send_albert' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.send_albert',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'send_cid' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.send_cid',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'back_students' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.back_students',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'back_cid' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.back_cid',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
				)
			),
			'comments' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.comments',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => '--div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.s_rel,rel_book;;;;1-1-1,rel_month,--div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.s_sales,sales_reception;;;;1-1-1,sales_bill,sales_free,--div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.s_students,students_distribution;;;;1-1-1,students_exchange,--div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.s_events,events_out;;;;1-1-1,events_back,--div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.s_send,send_albert;;;;1-1-1,send_cid,--div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.s_back,back_students;;;;1-1-1,back_cid,--div--;LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock.s_misc,comments;;;;1-1-1'),
		),
		
		// Palettes configuration
		'palettes' => Array ()
	);
?>
