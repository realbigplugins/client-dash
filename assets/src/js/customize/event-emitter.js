const EventEmitter = {

    events: {},

    dispatch: function( event, data ) {

        if ( ! this.events[ event ] ) return;

        this.events[ event ].forEach( callback => callback( data ) );
        
    },

    subscribe: function( event, callback ) {

        if ( ! this.events[ event ] ) this.events[ event ] = [];

        this.events[ event ].push( callback );

    }

}

// Making an Event Bus global like this is a little dirty, but it helps make 3rd party integration clearer
window.clientDashEvents = EventEmitter;

export default EventEmitter;