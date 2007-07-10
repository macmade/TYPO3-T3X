<?php
    
    if ( !defined( 'TYPO3_MODE' ) ) {
        die( 'Access denied.' );
    }
    
    // Add FE plugin
    t3lib_extMgm::addPItoST43( $_EXTKEY, 'pi1/class.tx_cjf_pi1.php', '_pi1', 'list_type', 1 );
    
    // Save & New options
    t3lib_extMgm::addUserTSConfig( 'options.saveDocNew.tx_cjf_artists=1' );
    t3lib_extMgm::addUserTSConfig( 'options.saveDocNew.tx_cjf_styles=1' );
    t3lib_extMgm::addUserTSConfig( 'options.saveDocNew.tx_cjf_groups=1' );
    t3lib_extMgm::addUserTSConfig( 'options.saveDocNew.tx_cjf_places=1' );
    t3lib_extMgm::addUserTSConfig( 'options.saveDocNew.tx_cjf_clients=1' );
    
    // RTE configuration for groups
    t3lib_extMgm::addPageTSConfig( '
        # ***************************************************************************************
        # CONFIGURATION of RTE in table "tx_cjf_groups", field "description"
        # ***************************************************************************************
        RTE.config.tx_cjf_groups.description {
            hidePStyleItems = H1
            hideButtons = *
            showButtons = formatblock, bold, italic, underline, strikethrough, subscript, superscript, left, center, right, justifyfull, textindicator, orderedlist, unorderedlist, outdent, indent, link, image, findreplace, removeformat, spellcheck, copy, cut, paste, undo, redo, chMode, showhelp, about
            proc.exitHTMLparser_db = 1
            proc.exitHTMLparser_db {
                keepNonMatchedTags = 1
            }
        }
    ' );
    
    // RTE configuration for artists
    t3lib_extMgm::addPageTSConfig( '
        # ***************************************************************************************
        # CONFIGURATION of RTE in table "tx_cjf_artists", field "description"
        # ***************************************************************************************
        RTE.config.tx_cjf_artists.description {
            hidePStyleItems = H1
            hideButtons = *
            showButtons = formatblock, bold, italic, underline, strikethrough, subscript, superscript, left, center, right, justifyfull, textindicator, orderedlist, unorderedlist, outdent, indent, link, image, findreplace, removeformat, spellcheck, copy, cut, paste, undo, redo, chMode, showhelp, about
            proc.exitHTMLparser_db = 1
            proc.exitHTMLparser_db {
                keepNonMatchedTags = 1
            }
        }
    ' );
?>
