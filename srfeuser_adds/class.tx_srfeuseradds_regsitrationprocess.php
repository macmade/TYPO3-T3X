<?php
    
    class tx_srfeuseradds_registrationProcess
    {
        
        function registrationProcess_afterSaveCreate( $user, &$pObj )
        {
            
            // User ID
            $uid = $user[ 'uid' ];
            
            // Change fields
            $user[ 'endtime' ] = time();
            
            // Update FE User
            $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( 'fe_users' , 'uid=' . $uid, $user);
        }
    }
    
?>
