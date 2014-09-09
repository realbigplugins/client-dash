/**
 * Functionality for the admin menu page under settings.
 *
 * The init function is only called when on MY nav menu edit page.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @since ClientDash 1.6
 */
var cdAdminMenu;
(function ($) {
    cdAdminMenu = {
        init: function () {
            this.modify_max_menu_depth();
            this.jQuery_extensions();
            this.separator_height();

            // TODO Decide if going to use, otherwise, delete
            //this.disableCheckboxes();
            //this.enableCheckboxes();
        },
        /**
         * Modifies the height of custom separators to reflect what was set.
         *
         * @since Client Dash 1.6
         */
        separator_height: function () {
            $('#adminmenu').find('li.wp-menu-separator').each(function () {
                var e_classes = $(this).attr('class'),
                    height = e_classes.match(/(?!height-)\d+/g);

                $(this).height(height + 'px');
            });
        },
        /**
         * Disables any checkboxes that are in use in the nav menu.
         *
         * @since Client Dash 1.6
         */
        disableCheckboxes: function () {
            // Add a listener to the "Add to Menu" button
            $('#menu-settings-column').find('input[type="submit"]').click(function () {

                // Cycle through every checkbox
                $('#menu-settings-column').find('input[type="checkbox"]').each(function () {

                    // If it is indeed checked, then it's being added, so disable it
                    if ($(this).prop('checked')) {
                        $(this).prop('disabled', true);
                        $(this).closest('label').addClass('disabled');
                    }
                });
            });
        },
        /**
         * Re-enables checkboxes that are no longer in use in the nav menu.
         *
         * @since Client Dash 1.6
         */
        enableCheckboxes: function () {
            // Add a listener to the "Remove" button for each menu item
            $('#menu-to-edit').on('click', '.item-delete', function () {
                var title = $(this).closest('li').find('input[name^="menu-item-title"]').val(),
                    url = $(this).closest('li').find('input[name^="menu-item-url"]').val();

                // Cycle through each checkbox
                $('#menu-settings-column').find('li').each(function () {
                    var t_title = $(this).find('input[name*="menu-item-title"]').val(),
                        t_url = $(this).find('input[name*="menu-item-url"]').val();

                    // If the checkbox "matches" the item deleted, re-enabled it
                    if (title == t_title && url == t_url) {
                        $(this).find('label').removeClass('disabled')
                            .find('input[type="checkbox"]').prop('disabled', false);
                    }
                });
            });
        },
        /**
         * Modifies some jQuery extensions that were defined in wpNavMenu.
         *
         * getItemData():
         * I love the people at WP, they're brilliant, but they didn't make interacting with
         * the edit nav menu api very easy. This is one example. A pre-defined list of allowed
         * input fields to be passed through AJAX. Sigh. So I've had to override this function
         * in order to add to the list my own custom input values.
         *
         * @since Client Dash 1.6
         */
        jQuery_extensions: function () {
            $.fn.extend({
                getItemData: function (itemType, id) {
                    itemType = itemType || 'menu-item';

                    var itemData = {}, i,

                    // The allowed input fields
                        fields = [
                            'menu-item-db-id',
                            'menu-item-object-id',
                            'menu-item-object',
                            'menu-item-parent-id',
                            'menu-item-position',
                            'menu-item-type',
                            'menu-item-title',
                            'menu-item-url',
                            'menu-item-description',
                            'menu-item-attr-title',
                            'menu-item-target',
                            'menu-item-classes',
                            'menu-item-xfn',

                            // Add custom meta HERE
                            'custom-meta-cd-object-type',
                            'custom-meta-cd-post-type',
                            'custom-meta-cd-original-title',
                            'custom-meta-cd-icon',
                            'custom-meta-cd-type',
                            'custom-meta-cd-separator-height',
                            'custom-meta-cd-url',
                            'custom-meta-cd-page-title'
                        ];

                    if (!id && itemType == 'menu-item') {
                        id = this.find('.menu-item-data-db-id').val();
                    }

                    if (!id) return itemData;

                    this.find('input').each(function () {
                        var field;
                        i = fields.length;
                        while (i--) {
                            if (itemType == 'menu-item')
                                field = fields[i] + '[' + id + ']';
                            else if (itemType == 'add-menu-item')
                                field = 'menu-item[' + id + '][' + fields[i] + ']';

                            if (
                                this.name &&
                                field == this.name
                            ) {
                                itemData[fields[i]] = this.value;
                            }
                        }
                    });

                    return itemData;
                }
            });
        },
        /**
         * Modifies the max depth for the sortable nav menu.
         *
         * WP Uses an api called "wpNavMenu" to do EVERYTHING related to the nav menu
         * edit screen. Unfortunately, there are no filters are places made available to modify
         * this script in any way. The property "wpNavMenu.options.globalMaxDepth" is the key
         * here. This property defines the max parent/child depth allowed for dragging/dropping.
         * The problem is, I can't modify it... So, rather than copy the ENTIRE api and change
         * that one value, I've only copied out the method "wpNavMenu.initSortables", but just
         * before I've changed "globalMaxDepth" to 1 instead of the default of 11.
         *
         * IMPORTANT: It is crucial to keep this script up to date.
         *
         * @since Client Dash 1.6
         */
        modify_max_menu_depth: function () {

            // Reset the max depth HERE (default is 11)
            wpNavMenu.options.globalMaxDepth = 1;

            var currentDepth = 0, originalDepth, minDepth, maxDepth,
                prev, next, prevBottom, nextThreshold, helperHeight, transport,
                menuEdge = wpNavMenu.menuList.offset().left,
                body = $('body'), maxChildDepth,
                menuMaxDepth = initialMenuMaxDepth();

            if (0 !== $('#menu-to-edit li').length)
                $('.drag-instructions').show();

            // Use the right edge if RTL.
            menuEdge += wpNavMenu.isRTL ? wpNavMenu.menuList.width() : 0;

            // TODO Don't allow items to be placed as a submenu of "Separator"
            // TODO Don't allow an item with children to be placed 1 level deep (then children are 2 levels deep)
            wpNavMenu.menuList.sortable({
                handle: '.menu-item-handle',
                placeholder: 'sortable-placeholder',
                start: function (e, ui) {
                    var height, width, parent, children, tempHolder;

                    // handle placement for rtl orientation
                    if (wpNavMenu.isRTL)
                        ui.item[0].style.right = 'auto';

                    transport = ui.item.children('.menu-item-transport');

                    // Set depths. currentDepth must be set before children are located.
                    originalDepth = ui.item.menuItemDepth();
                    updateCurrentDepth(ui, originalDepth);

                    // Attach child elements to parent
                    // Skip the placeholder
                    parent = ( ui.item.next()[0] == ui.placeholder[0] ) ? ui.item.next() : ui.item;
                    children = parent.childMenuItems();
                    transport.append(children);

                    // Update the height of the placeholder to match the moving item.
                    height = transport.outerHeight();
                    // If there are children, account for distance between top of children and parent
                    height += ( height > 0 ) ? (ui.placeholder.css('margin-top').slice(0, -2) * 1) : 0;
                    height += ui.helper.outerHeight();
                    helperHeight = height;
                    height -= 2; // Subtract 2 for borders
                    ui.placeholder.height(height);

                    // Update the width of the placeholder to match the moving item.
                    maxChildDepth = originalDepth;
                    children.each(function () {
                        var depth = $(this).menuItemDepth();
                        maxChildDepth = (depth > maxChildDepth) ? depth : maxChildDepth;
                    });
                    width = ui.helper.find('.menu-item-handle').outerWidth(); // Get original width
                    width += wpNavMenu.depthToPx(maxChildDepth - originalDepth); // Account for children
                    width -= 2; // Subtract 2 for borders
                    ui.placeholder.width(width);

                    // Update the list of menu items.
                    tempHolder = ui.placeholder.next();
                    tempHolder.css('margin-top', helperHeight + 'px'); // Set the margin to absorb the placeholder
                    ui.placeholder.detach(); // detach or jQuery UI will think the placeholder is a menu item
                    $(this).sortable('refresh'); // The children aren't sortable. We should let jQ UI know.
                    ui.item.after(ui.placeholder); // reattach the placeholder.
                    tempHolder.css('margin-top', 0); // reset the margin

                    // Now that the element is complete, we can update...
                    updateSharedVars(ui);
                },
                stop: function (e, ui) {
                    var children, subMenuTitle,
                        depthChange = currentDepth - originalDepth;

                    // Return child elements to the list
                    children = transport.children().insertAfter(ui.item);

                    // Add "sub menu" description
                    subMenuTitle = ui.item.find('.item-title .is-submenu');
                    if (0 < currentDepth)
                        subMenuTitle.show();
                    else
                        subMenuTitle.hide();

                    // Update depth classes
                    if (0 !== depthChange) {
                        ui.item.updateDepthClass(currentDepth);
                        children.shiftDepthClass(depthChange);
                        updateMenuMaxDepth(depthChange);
                    }
                    // Register a change
                    wpNavMenu.registerChange();
                    // Update the item data.
                    ui.item.updateParentMenuItemDBId();

                    // address sortable's incorrectly-calculated top in opera
                    ui.item[0].style.top = 0;

                    // handle drop placement for rtl orientation
                    if (wpNavMenu.isRTL) {
                        ui.item[0].style.left = 'auto';
                        ui.item[0].style.right = 0;
                    }

                    wpNavMenu.refreshKeyboardAccessibility();
                    wpNavMenu.refreshAdvancedAccessibility();
                },
                change: function (e, ui) {
                    // Make sure the placeholder is inside the menu.
                    // Otherwise fix it, or we're in trouble.
                    if (!ui.placeholder.parent().hasClass('menu'))
                        (prev.length) ? prev.after(ui.placeholder) : wpNavMenu.menuList.prepend(ui.placeholder);

                    updateSharedVars(ui);
                },
                sort: function (e, ui) {
                    var offset = ui.helper.offset(),
                        edge = wpNavMenu.isRTL ? offset.left + ui.helper.width() : offset.left,
                        depth = wpNavMenu.negateIfRTL * wpNavMenu.pxToDepth(edge - menuEdge);
                    // Check and correct if depth is not within range.
                    // Also, if the dragged element is dragged upwards over
                    // an item, shift the placeholder to a child position.
                    if (depth > maxDepth || offset.top < prevBottom) depth = maxDepth;
                    else if (depth < minDepth) depth = minDepth;

                    if (depth != currentDepth)
                        updateCurrentDepth(ui, depth);

                    // If we overlap the next element, manually shift downwards
                    if (nextThreshold && offset.top + helperHeight > nextThreshold) {
                        next.after(ui.placeholder);
                        updateSharedVars(ui);
                        $(this).sortable('refreshPositions');
                    }
                }
            });

            function updateSharedVars(ui) {
                var depth;

                prev = ui.placeholder.prev();
                next = ui.placeholder.next();

                // Make sure we don't select the moving item.
                if (prev[0] == ui.item[0]) prev = prev.prev();
                if (next[0] == ui.item[0]) next = next.next();

                prevBottom = (prev.length) ? prev.offset().top + prev.height() : 0;
                nextThreshold = (next.length) ? next.offset().top + next.height() / 3 : 0;
                minDepth = (next.length) ? next.menuItemDepth() : 0;

                if (prev.length)
                    maxDepth = ( (depth = prev.menuItemDepth() + 1) > wpNavMenu.options.globalMaxDepth ) ? wpNavMenu.options.globalMaxDepth : depth;
                else
                    maxDepth = 0;
            }

            function updateCurrentDepth(ui, depth) {
                ui.placeholder.updateDepthClass(depth, currentDepth);
                currentDepth = depth;
            }

            function initialMenuMaxDepth() {
                if (!body[0].className) return 0;
                var match = body[0].className.match(/menu-max-depth-(\d+)/);
                return match && match[1] ? parseInt(match[1], 10) : 0;
            }

            function updateMenuMaxDepth(depthChange) {
                var depth, newDepth = menuMaxDepth;
                if (depthChange === 0) {
                    return;
                } else if (depthChange > 0) {
                    depth = maxChildDepth + depthChange;
                    if (depth > menuMaxDepth)
                        newDepth = depth;
                } else if (depthChange < 0 && maxChildDepth == menuMaxDepth) {
                    while (!$('.menu-item-depth-' + newDepth, wpNavMenu.menuList).length && newDepth > 0)
                        newDepth--;
                }
                // Update the depth class.
                body.removeClass('menu-max-depth-' + menuMaxDepth).addClass('menu-max-depth-' + newDepth);
                menuMaxDepth = newDepth;
            }
        }
    };

    $(function () {
        if ($('body').hasClass('cd-nav-menu') && !$('#menu-settings-column').hasClass('metabox-holder-disabled')) {
            cdAdminMenu.init();
        }

        cdAdminMenu.separator_height();
    });
})(jQuery);