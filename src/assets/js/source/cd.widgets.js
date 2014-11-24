/**
 * This object contains all functionality for the Settings -> Widgets
 * page.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @since ClientDash 1.6
 */
var cdWidgets;
(function ($) {
    cdWidgets = {
        /**
         * Initialization for the widgets page.
         *
         * @since Clientdash 1.5
         */
        init: function () {

        }
    };

    // Launch on ready
    $(function () {
        if ($('#cd-widgets').length) {
            cdWidgets.init();
        }
    });
})(jQuery);