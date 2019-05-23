const EventEmitter = {

    events: {},

    dispatch: function( event, data ) {

        window.dispatchEvent( new CustomEvent( event, data ) );
        
    },

    subscribe: function( event, callback ) {

        window.addEventListener( event, callback );

    },

    unsubscribe: function( event, callback ) {

        window.removeEventListener( event, callback );

    }

}

export default EventEmitter;