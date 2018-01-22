(function ($) {
    'use strict';

    function init() {

        select2_helper_pages_buttons_init();
    }

    function select2_helper_pages_buttons_init() {

        var $buttons = $('[data-select-toggle]');

        $buttons.click(select2_helper_pages_button_click);
    }

    function select2_helper_pages_button_click() {

        var $select = $(this).siblings('select');

        $select.select2('open');
    }

    $(init);
})(jQuery);