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
            // Sortable list
            $('#cd-dash-widgets-sortable').sortable({
                placeholder: 'ui-state-highlight',
                items: 'li',
                containment: 'document',
                delay: 100,
                cursor: 'move',
                start: function (event, ui) {
                    ui.item.find('.cd-up-down').removeClass('open');
                    ui.item.find('.cd-dash-widget-settings').removeClass('open');

                    // Prevent up-down while dragging
                    ui.item.data('dragging', true);
                },
                stop: function (event, ui) {
                    if (ui.item.hasClass('ui-draggable')) {
                        ui.item.removeClass('ui-draggable');
                        ui.item.find('input').prop('disabled', false);

                        cdWidgets.toggle_init(ui.item);
                    }

                    cdWidgets.update_numbers();

                    // Allow clicking now that we've stopped
                    ui.item.data('dragging', false);
                }
            });

            // Draggable list
            $('#cd-dash-widgets-left').find('.ui-draggable').draggable({
                connectToSortable: '.ui-sortable',
                helper: 'clone',
                containment: 'document',
                handle: '.cd-dash-widget-title',
                cursor: 'pointer',
                start: function (event, ui) {
                    ui.helper.find('.cd-dash-widget-description').remove();
                }
            });
        },
        /**
         * Re-initializing the toggle buttons when created. (Because jQuery isn't live... blah)
         *
         * @since ClientDash 1.5
         *
         * @param e The supplied object.
         */
        toggle_init: function (e) {
            e.on('click', function () {
                if ($(this).data('dragging')) {
                    return;
                }

                $(this).find('.cd-up-down').toggleClass('open')
                    .closest('.cd-dash-widget-title')
                    .siblings('.cd-dash-widget-settings').toggleClass('open');
            });
        },
        /**
         * Cycles through all widgets and modifies the "name" integers
         * to match the index of each current item.
         *
         * @since ClientDash 1.5
         */
        update_numbers: function () {
            $('#cd-dash-widgets-sortable').find('.cd-dash-widget').each(function () {
                $(this).find('input').each(function () {
                    var index = $(this).closest('.cd-dash-widget').index(),
                        name_old = $(this).attr('name'),
                        name_new = name_old.replace(/(cd_widgets\[)(\d+)/g, 'cd_widgets[' + index);

                    $(this).attr('name', name_new);
                });
            });
        },
        /**
         * Removes the current widget.
         *
         * @since ClientDash 1.5
         *
         * @param e The supplied object.
         */
        remove: function (e) {
            $(e).closest('.cd-dash-widget').remove();
        }
    };

    // Launch on ready
    $(function () {
        cdWidgets.init();

        $('#cd-dash-widgets-sortable').find('.cd-dash-widget').each(function () {
            cdWidgets.toggle_init($(this));
        });
    });
})(jQuery);