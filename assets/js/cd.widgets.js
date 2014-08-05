var cdWidgets;
(function ($) {
    cdWidgets = {
        init: function () {
            $('#cd-dash-widgets-droppable').sortable({
                placeholder: 'ui-state-highlight',
                revert: true,
                items: 'li',
                containment: 'document',
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

                        console.log(ui.item);
                        cdWidgets.toggle_init(ui.item);
                    }

                    // Allow clicking now that we've stopped
                    ui.item.data('dragging', false);
                }
            });

            $('#cd-dash-widgets-left').find('.ui-draggable').draggable({
                connectToSortable: '.ui-sortable',
                helper: 'clone',
                containment: 'document',
                start: function (event, ui) {
                    ui.helper.find('.cd-dash-widget-description').remove();
                }
            });
        },
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
        remove: function (e) {
            $(e).closest('.cd-dash-widget').remove();
        }
    };

    $(function () {
        cdWidgets.init();

        $('#cd-dash-widgets-droppable').find('.cd-dash-widget-title').each(function() {
            cdWidgets.toggle_init($(this));
        });
    });

})(jQuery);