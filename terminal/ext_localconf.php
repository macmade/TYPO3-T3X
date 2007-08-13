<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// User TSConfig
t3lib_extMgm::addUserTSConfig(
    'mod.tools_txterminalM1 {
        
        // Display options
        display {
            
            fontSize   =
            background =
            foreground =
            prompt     =
        }
        
        // Shortcuts options
        shortcuts {
            
            // Predefined shortcuts
            defaults {
                
                processes  = 1
                diskUse    = 1
                networking = 1
                pathInfo   = 1
                userName   = 1
                date       = 1
                listing    = 1
                home       = 1
                fileadmin  = 1
                typo3conf  = 1
                uploads    = 1
                t3lib      = 1
                typo3      = 1
                typo3temp  = 1
            }
            
            // Custom shortcuts
            custom {
                
            }
        }
    }'
);
?>
