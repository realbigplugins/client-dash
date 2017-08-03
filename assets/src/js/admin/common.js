/**
 * Functionality shared across the board.
 *
 * @since {{VERSION}}
 */

(function ($) {
    'use strict';

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

    $(init_select2);
})(jQuery);