var cdWidgets;
(function ($) {
    cdWidgets = {
        init: function () {
            $('#cd-dash-widgets-droppable').sortable({
                placeholder: 'ui-state-highlight',
                revert: true,
                items: 'li',
                start: function (event, ui) {
                    ui.item.find('.cd-up-down').removeClass('open');
                    ui.item.find('.c,d-dash-widget-settings').removeClass('open');
                    
                    // Prevent up-down while dragging
                    ui.item.data('dragging', true);
                },
                stop: function (event, ui) {
                    if (ui.item.hasClass('ui-draggable')) {
                        ui.item.removeClass('ui-draggable')
                            .append('<span class="cd-up-down"></span>')
                            .wrap('<li class="cd-dash-widget">')
                            .closest('.cd-dash-widget')
                            .append('<div class="cd-dash-widget-settings">Test</div>');

                        console.log(ui.item);
                        cdWidgets.drag_init(ui.item);
                    }

                    // Allow clicking now that we've stopped
                    ui.item.data('dragging', false);
                }
            });

            $('.cd-dash-widget-title.ui-draggable').draggable({
                connectToSortable: '.ui-sortable',
                helper: 'clone'
            });
        },
        drag_init: function (e) {
            e.on('click', function () {
                if ($(this).data('dragging')) {
                    console.log('NOCLICKING');
                    return;
                }

                $(this).find('.cd-up-down').toggleClass('open')
                    .closest('.cd-dash-widget-title')
                    .siblings('.cd-dash-widget-settings').toggleClass('open');
            });
        }
    };

    $(function () {
        cdWidgets.init();
    });

})(jQuery);