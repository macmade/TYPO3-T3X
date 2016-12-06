// <![CDATA[

var tx_ymregister_Utils = null;

( function()
{
    var _instance = null;
    var _$        = null;
    
    tx_ymregister_Utils = function tx_ymregister_Utils()
    {
        if( _instance ) {
            
            return _instance;
        }
        
        _instance = this;
        _$        = jQuery.noConflict();
    }
    
    function _getCities( url, select )
    {
        var id = select.options[ select.selectedIndex ].value;
        
        if( !id ) {
            
            _$( '#tx_ymregister_pi1_cities' ).html(  '' );
            return;
        }
        
        html = _$.ajax(
            {
                url:     url + '&stateId=' + id,
                async:   false
            }
        ).responseText;
        
        _$( '#tx_ymregister_pi1_cities' ).html( html );
    }
    
    tx_ymregister_Utils.prototype.getCities = _getCities;
    
    tx_ymregister_Utils.getInstance = function()
    {
        if ( _instance === null ) {
            
            _instance = new tx_ymregister_Utils();
        }
        
        return _instance;
    }
    
} )();

// ]]>
