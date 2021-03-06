// <![CDATA[

/**
 * JavaScript shell helper class for the TYPO3 'terminal' extension
 * 
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */
function Shell() {
    
    // Form HTML elements
    var _form                = null;
    var _result              = null;
    var _command             = null;
    var _history             = null;
    var _lastCommand         = null;
    var _cwd                 = null;
    
    // Terminal prompt
    var _prompt              = '';
    
    // Number of commands to keep in the history
    var _historyLength       = 50;
    
    // Current history command
    var _currentHistoryIndex = -1;
    
    // URL for Ajax requests
    var _ajaxUrl             = '';
    
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
        _form.onsubmit   = _exec;
        
        // Input keypress method
        _command.onkeyup = _keyUp;
    }
    
    /**
     * Executes a shell command
     * 
     * @param   string      An optionnal shell command. If it's not defined, the value form the text input will be taken.
     * @return  boolean     True if an argument is passed, otherwise false to prevent the form to be submitted.
     * @see     _addHistory
     */
    function _exec()
    {
        // Command to run (from arguments or text input)
        if( typeof arguments[ 0 ] == 'string' ) {
            
            // Gets the command to run from the arguments
            var command       = arguments[ 0 ];
            var formSubmitted = 0;
        
        } else {
            
            // Gets the command to run from the text input
            var command       = _command.value;
            var formSubmitted = 1;
        }
        
        // Resets the text input
        _command.value  = '';
        
        // Creates the command line element
        var commandLine = document.createElement( 'div' );
        
        // Creates the command prompt element
        var promptSpan       = document.createElement( 'span' );
        promptSpan.className = 'prompt';
        promptSpan.appendChild( document.createTextNode( _prompt ) );
        
        // Creates the command element
        var commandSpan       = document.createElement( 'span' );
        commandSpan.className = 'command';
        commandSpan.appendChild( document.createTextNode( ' ' + command ) );
        
        // Appends the prompt and the command
        commandLine.appendChild( promptSpan );
        commandLine.appendChild( commandSpan );
        
        // Adds the command line to result
        _result.appendChild( commandLine );
        
        // Adds the command to the history
        _addHistory( command );
        
        // Creates a new Ajax request
        new Ajax.Request(
            _ajaxUrl,
            {
                // Method to use
                method     : 'get',
                
                // Ajax parameters
                parameters : {
                    ajaxCall : 1,
                    command  : command
                },
                
                // Function to call in case of success
                onSuccess  : function( transport )
                {
                    // Gets the response from the server
                    var commandReturn = transport.responseText;
                    
                    // Gets each line of the response
                    var cwdParts      = commandReturn.split( '\r\n' );
                    
                    // Current directory is the last line
                    var cwd           = cwdParts[ cwdParts.length - 1 ];
                    
                    // Removes the last line from the response
                    commandReturn     = commandReturn.replace( /\r\n.+$/i, '' );
                    
                    // Creates the result element
                    var result        = document.createElement( 'div' );
                    result.className  = 'result';
                    result.appendChild( document.createTextNode( commandReturn ) );
                    
                    // Appends the result to the console
                    _result.appendChild( result );
                    
                    // Scrolls to the bottom
                    _result.scrollTop = _result.scrollHeight;
                    
                    // Removes the old working directory
                    _cwd.removeChild( _cwdPath );
                    
                    // Creates the element for the new working directory
                    _cwdPath    = document.createElement( 'path' );
                    _cwdPath.id = 'cwdPath';
                    _cwdPath.appendChild( document.createTextNode( cwd ) );
                    
                    // Appends the working directory
                    _cwd.appendChild( _cwdPath );
                }
            }
        );
        
        // Return false if the command has been taken from the text input
        // This will prevent the page to relaod
        return ( formSubmitted ) ? false : true;
    }
    
    /**
     * Adds a command to the history select
     * 
     * @param   string      command     The shell command
     * @return  boolean
     */
    function _addHistory( command )
    {
        // Creates the option element
        var option   = document.createElement( 'option' );
        var label    = document.createTextNode( command );
        option.value = command;
        option.appendChild( label );
        
        // Checks if the select contains items
        if( _history.childNodes.length > 0 ) {
            
            // Inserts the new command at the top
            _history.insertBefore( option, _history.firstChild );
            
        } else {
            
            // Insert the first command
            _history.appendChild( option );
        }
        
        // Unselects the last selected option
        if( _lastCommand !== null ) {
            
            _lastCommand.selected = false;
        }
        
        // Memorizes the current command
        _lastCommand          = option;
        
        // Selects the current comand
        option.selected       = true;
        
        // Checks the length of the history menu
        if( _history.childNodes.length > _historyLength ) {
            
            // Removes the last item from the history
            _history.removeChild( _history.lastChild );
        }
        
        return true;
    }
    
    /**
     * Sets the command prompt
     * 
     * @param   string      prompt      The shell command
     * @return  boolean
     */
    function _setPrompt( prompt )
    {
        _prompt = prompt;
        
        return true;
    }
    
    /**
     * Sets the URL for the Ajax requests
     * 
     * @param   string      url         The shell command
     * @return  boolean
     */
    function _setAjaxUrl( url )
    {
        _ajaxUrl = url;
        
        return true;
    }
    
    /**
     * Process keyboard events for the command input
     * 
     * @param   object      event       The event object
     * @return  boolean
     */
    function _keyUp( event )
    {
        // Normalize event object
        var event = ( !event ) ? window.event : event;
        
        // History direction
        var direction = ( event.keyCode == 38 ) ? 1 : ( ( event.keyCode == 40 ) ? -1 : false );
        
        if( direction ) {
            
            // New history index
            var newIndex = _currentHistoryIndex + direction;
            
            // Checks the new index
            if( newIndex == history.length ) {
                
                // Restart from beginning of history
                newIndex = 0;
                
            } else if( newIndex <= -1 ) {
                
                // Restart from end of history
                newIndex = history.length - 1;
            }
            
            // Writes the command and stores the new index
            _command.value       = _history.options[ newIndex ].value;
            _currentHistoryIndex = newIndex;
            
            return true;
        }
        
        return false;
    }
    
    // Public methods
    this.exec       = _exec;
    this.keyUp      = _keyUp;
    this.setPrompt  = _setPrompt;
    this.setAjaxUrl = _setAjaxUrl;
}

// Creates a new shell
shell = new Shell();

// ]]>
