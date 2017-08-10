(function ($) {
    'use strict';

    function init() {

        select2_helper_pages_buttons_init();
        helper_page_role_select_init();
    }

    function select2_helper_pages_buttons_init() {

        var $buttons = $('[data-select-toggle]');

        $buttons.click(select2_helper_pages_button_click);
    }

    function select2_helper_pages_button_click() {

        var $select = $(this).siblings('select');

        $select.select2('open');
    }

    function helper_page_role_select_init() {

        var $role_selects = $('.clientdash-helper-page-tab-roles-input select');

        $role_selects.change(helper_page_set_disabled);
        $role_selects.each(helper_page_set_disabled);
    }

    function helper_page_set_disabled() {

        var $item         = $(this).closest('.clientdash-helper-page-wrap');
        var $roleSelects = $item.find('.clientdash-helper-page-tab-roles-input select');
        var pageEmpty         = true;

        $roleSelects.each(function () {

            if ( $(this).val() ) {

                pageEmpty = false;
                $(this).closest('.clientdash-helper-page-tab-wrap').removeClass('clientdash-helper-page-tab-disabled');

            } else {

                $(this).closest('.clientdash-helper-page-tab-wrap').addClass('clientdash-helper-page-tab-disabled');
            }
        });

        if (pageEmpty) {

            $item.addClass('clientdash-helper-page-disabled');

        } else {

            $item.removeClass('clientdash-helper-page-disabled');
        }
    }

    $(init);
})(jQuery);