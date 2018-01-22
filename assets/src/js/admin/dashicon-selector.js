/**
 * Handles functionality for the Dashicons Selector field.
 *
 * @since 2.0.0
 */
(function ($, data) {
    'use strict';

    var l10n = data.l10n;

    var api = {

        $fields: [],

        init: function () {

            api.$fields = $('.cd-dashicon-selector');

            if (!api.$fields.length) {

                return;
            }

            $('body, html').click(api.closeSelectors);
            api.$fields.find('li').click(api.selectIcon);
            api.$fields.find('[data-toggle]').click(api.toggle);
        },

        selectIcon: function (e) {

            e.stopPropagation();

            var icon = $(this).attr('data-icon');

            $(this).siblings('li').removeClass('cd-dashicon-selector-selected');
            $(this).addClass('cd-dashicon-selector-selected');
            $(this).closest('.cd-dashicon-selector').find('> input[type="hidden"]').val(icon);
            $(this).closest('.cd-dashicon-selector').find('.cd-dashicon-selector-preview').attr('class',
                'cd-dashicon-selector-preview dashicons ' + icon
            );
            $(this).closest('.cd-dashicon-selector').removeClass('cd-dashicon-selector-open');
        },

        toggle: function (e) {

            e.stopPropagation();

            $(this).closest('.cd-dashicon-selector').toggleClass('cd-dashicon-selector-open');
        },

        closeSelectors: function () {

            api.$fields.removeClass('cd-dashicon-selector-open');
        }
    }

    $(api.init);
})(jQuery, ClientDash_Data);