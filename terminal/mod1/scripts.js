// <![CDATA[

function Shell() {
    
    // Form HTML elements
    var _form          = null;
    var _result        = null;
    var _command       = null;
    var _history       = null;
    var _lastCommand   = null;
    var _cwd           = null;
    
    // Terminal prompt
    var _prompt        = '';
    
    // Number of commands to keep in the history
    var _historyLength = 50;
    
    // URL for Ajax requests
    var _ajaxUrl       = '';
    
    // Calls the constructor
    __construct.apply( this, arguments );
    
    /**
     * Class constructor
     */
    function __construct()
    {
        // Get HTML elements
        _form    = document.getElementById( 'shellForm' );
        _result  = document.getElementById( 'result' );
        _command = document.getElementById( 'command' );
        _history = document.getElementById( 'history' );
        _cwd     = document.getElementById( 'cwd' );
        _cwdPath = document.getElementById( 'cwdPath' );
        
        // Form submission method
        _form.onsubmit = _exec;
    }
    
    function _exec()
    {
        // Command to run (from arguments or text input)
        if( typeof arguments[ 0 ] == 'string' ) {
            
            var command       = arguments[ 0 ];
            var formSubmitted = 0;
        
        } else {
            
            var command       = _command.value;
            var formSubmitted = 1;
        }
        
        // Reset text input
        _command.value  = '';
        
        // Command line
        var commandLine = document.createElement( 'div' );
        var promptSpan  = document.createElement( 'span' );
        promptSpan.appendChild(
            document.createTextNode( _prompt )
        );
        promptSpan.className = 'prompt';
        commandLine.appendChild( promptSpan );
        var commandSpan  = document.createElement( 'span' );
        commandSpan.appendChild(
            document.createTextNode( ' ' + command )
        );
        commandSpan.className = 'command';
        commandLine.appendChild( commandSpan );
        
        // Add command line to result
        _result.appendChild( commandLine );
        
        _addHistory( command );
        
        new Ajax.Request(
            _ajaxUrl,
            {
                method     : 'get',
                parameters : {
                    ajaxCall : 1,
                    command  : command
                },
                onSuccess  : function( transport )
                {
                    var commandReturn = transport.responseText;
                    var cwdParts      = commandReturn.split( '\n' );
                    var cwd           = cwdParts[ cwdParts.length - 1 ];
                    commandReturn     = commandReturn.replace( /\n.+$/i, '' );
                    var result        = document.createElement( 'div' );
                    result.className  = 'result';
                    result.appendChild(
                        document.createTextNode(
                            commandReturn
                        )
                    );
                    _result.appendChild( result );
                    _result.scrollTop = _result.scrollHeight;
                    _cwd.removeChild( _cwdPath );
                    _cwdPath    = document.createElement( 'path' );
                    _cwdPath.id = 'cwdPath';
                    _cwdPath.appendChild(
                        document.createTextNode( cwd )
                    );
                    _cwd.appendChild( _cwdPath );
                }
            }
        );
        
        return ( formSubmitted ) ? false : true;
    }
    
    function _addHistory( command )
    {
        var option   = document.createElement( 'option' );
        var label    = document.createTextNode( command );
        option.value = command;
        
        option.appendChild( label );
        
        if( _lastCommand != null ) {
            
            _history.insertBefore( option, _lastCommand );
            
        } else {
            
            _history.appendChild( option );
        }
        
        _lastCommand          = option;
        _lastCommand.selected = false;
        option.selected       = true;
        
        if( _history.childNodes.length > _historyLength ) {
            
            _history.removeChild( _history.lastChild );
        }
        
        return true;
    }
    
    function _setPrompt( prompt )
    {
        _prompt = prompt;
        
        return true;
    }
    
    function _setAjaxUrl( url )
    {
        _ajaxUrl = url;
        
        return true;
    }
    
    this.exec       = _exec;
    this.setPrompt  = _setPrompt;
    this.setAjaxUrl = _setAjaxUrl;
}

shell = new Shell();

// ]]>
