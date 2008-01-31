// <![CDATA[

function tx_countdownmacmade_pi1_counter( seconds )
{
    // Customizable informations
    this.infos         = {};
    
    // Counter elements
    var elements    = {};
    
    // Placeholder for errors
    var errors      = [];
    
    // Flag to know if the date is passed
    var datePassed  = ( seconds <= 0 );
    
    /**
     * Initializes the counter
     * 
     * @return  void
     */
    this.init = function()
    {
        // Process each element ID
        for( var i in this.infos ) {
            
            // Gets the element
            elements[ i ] = document.getElementById( this.infos[ i ] );
            
            // Checks the element
            if( elements[ i ] == null ) {
                
                // Element missing - Registers the error
                errors[ errors.length ] = 'Missing counter element!\nGiven ID: ' + this.infos[ i ];
            }
        }
        
        // Checks for errors
        if( errors.length ) {
            
            // Error header
            var str = 'Errors were detected with the counter:';
            
            // Process each error
            for( var i = 0; i < errors.length; i++ ) {
                
                // Adds the error string
                str += '\n\n' + errors[ i ];
            }
            
            // Alert and exit
            alert( str );
            return false;
        }
        
        // No negative number
        seconds = Math.abs( seconds );
        
        // Counter values
        var daysLeft    = Math.floor( ( ( seconds / 60 ) / 60 ) / 24 );
        var hoursLeft   = Math.floor( ( seconds / 60 ) / 60 - ( daysLeft * 24 ) );
        var minutesLeft = Math.floor( ( seconds / 60 - ( daysLeft * 24 * 60 ) ) - ( hoursLeft * 60 ) );
        var secondsLeft = Math.floor( ( ( seconds - ( daysLeft * 24 * 60 * 60 ) ) - ( hoursLeft * 60 * 60 ) ) - ( minutesLeft * 60 ) );
        
        // Writes initial values
        setValue( elements.days,    daysLeft );
        setValue( elements.hours,   hoursLeft );
        setValue( elements.minutes, minutesLeft );
        setValue( elements.seconds, secondsLeft );
        
        // Updates the counter
        updateCounter();
    }
    
    /**
     * Sets a counter value
     * 
     * @param   object  element The HTML element to update
     * @param   int     value   The new value
     * @return  void
     */
    function setValue( element, value )
    {
        element.firstChild.data = ( value < 10 ) ? '0' + value : value;
    }
    
    /**
     * Updates the counter
     *
     * This method will be called each second, and updates all the counter
     * values.
     * 
     * @return  void
     */
    function updateCounter()
    {
        // Gets current value
        var values = [
            Number( elements.seconds.firstChild.data ),
            Number( elements.minutes.firstChild.data ),
            Number( elements.hours.firstChild.data ),
            Number( elements.days.firstChild.data )
        ];
        
        // Checks if the counter has reached 0
        if( values[ 0 ] == 0 && values[ 1 ] == 0 && values[ 2 ] == 0 && values[ 3 ] == 0 ) {
            
            // The date has passed, so the counter will now increment
            datePassed = 1;
        }
        
        // Checks if the date has passed
        if( datePassed ) {
            
            // Sets the increment to use
            var increment = 1;
            
        } else {
            
            // Sets the increment to use
            var increment = -1;
        }
        
        // Changes the seconds
        values[ 0 ] += increment;
        
        // Process each value
        for( i = 0; i < values.length; i++ ) {
            
            // Maximum value
            var maxValue = ( i < 2 ) ? 60 : ( ( i == 2 ) ? 24 : 0 );
            
            // Checks if the number is out of range
            if( maxValue && ( values[ i ] == -1 || values[ i ] == maxValue ) ) {
                
                // Resets the value
                values[ i ]      = maxValue - Math.abs( values[ i ] );
                
                // Updates the next value
                values[ i + 1 ] += increment;
            }
        }
        
        // Sets the new values
        setValue( elements.days,    values[ 3 ] );
        setValue( elements.hours,   values[ 2 ] );
        setValue( elements.minutes, values[ 1 ] );
        setValue( elements.seconds, values[ 0 ] );
        
        // Repeats the operation each second
        window.setTimeout( updateCounter, 1000 );
    }
}

// ]]>
