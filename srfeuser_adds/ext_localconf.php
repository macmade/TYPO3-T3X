<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Register hook
	$TYPO3_CONF_VARS['EXTCONF']['sr_feuser_register']['tx_srfeuserregister_pi1']['registrationProcess'][] = 'EXT:srfeuser_adds/class.tx_srfeuseradds_regsitrationprocess.php:tx_srfeuseradds_registrationProcess';
?>
