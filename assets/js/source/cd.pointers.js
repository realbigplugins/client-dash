var cdPointers;
(function($) {
    cdPointers = {
        init: function() {
            $('.cd-pointer').each(function() {
                var e = $(this),
                    settings = {
                        content: 'Client Dash is AWESOME',
                        edge: 'left',
                        align: 'center'
                    };

                // Override any defaults that have been set with data-{prop}
                for (var k in settings) {
                    if (settings.hasOwnProperty(k)) {
                        // Content isn't data, it's html
                        if ( k == 'content' ) {
                            settings[k] = e.html();
                            continue;
                        }
                        if (e.attr('data-cd-pointer-' + k)) {
                            settings[k] = e.attr('data-cd-pointer-' + k);
                        }
                    }
                }

                e.pointer({
                    content: settings.content,
                    position: {
                        edge: settings.edge,
                        align: settings.align
                    }
                }).pointer('open');
            });
        }
    };

    $(function() {
        cdPointers.init();
    });
})(jQuery);