/**
 * Functionality shared across the board.
 *
 * @since {{VERSION}}
 */

(function ($) {
    'use strict';

    /**
     * Removes a query parameter.
     *
     * @author bobince
     * @url https://stackoverflow.com/questions/1634748/how-can-i-delete-a-query-string-parameter-in-javascript
     *
     * @param url
     * @param parameter
     * @returns {*}
     */
    function removeURLParameter(url, parameter) {
        //prefer to use l.search if you have a location/link object
        var urlparts= url.split('?');
        if (urlparts.length>=2) {

            var prefix= encodeURIComponent(parameter)+'=';
            var pars= urlparts[1].split(/[&;]/g);

            //reverse iteration as may be destructive
            for (var i= pars.length; i-- > 0;) {
                //idiom for string.startsWith
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                    pars.splice(i, 1);
                }
            }

            url= urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
            return url;
        } else {
            return url;
        }
    }

    /**
     * Removes any non-necessary query params.
     *
     * @since {{VERSION}}
     */
    function init_url() {

        var url = window.location.href;

        url = removeURLParameter(url, 'clientdash_upgraded' );

        window.history.replaceState( null, null, url);
    }

    /**
     * Initializes all select2's on the page.
     *
     * @since {{VERSION}}
     */
    function init_select2() {

        var $selects = $('.clientdash-select2');

        $selects.each(function () {

            var options = $(this).data();

            $(this).trigger('clientdash-select2-pre-init', [options]);

            $(this).select2(options);

            // Helper data for detecting if open
            $(this).data('select2:open', false);

            $(this).on('select2:open', function() {
                $(this).data('select2:open', true);
            });

            $(this).on('select2:close', function() {
                $(this).data('select2:open', false);
            });

            $(this).trigger('clientdash-select2-post-init', [options]);
        });
    }

    init_url();
    $(init_select2);
})(jQuery);