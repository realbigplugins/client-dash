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
var cdMenus;
(function ($) {
    cdMenus = {
        init: function () {
            this.modify_max_menu_depth();
            this.jQuery_extensions();
            this.separator_checkbox();
            this.link_checkbox();
            this.addItemToMenu();
            this.icon_selector();
        },
        /**
         * Replaces wpNavMenu.addItemToMenu() (nav-menu.js:~918).
         *
         * I want to use my own save menu function. So I need to replace this function
         * in order to change the post action.
         *
         * @since Client Dash 1.6
         */
        addItemToMenu: function () {
            wpNavMenu.addItemToMenu = function (menuItem, processMethod, callback) {
                var menu = $('#menu').val(),
                    nonce = $('#menu-settings-column-nonce').val(),
                    params;

                processMethod = processMethod || function(){};
                callback = callback || function(){};

                params = {
                    'action': 'cd_add_menu_item',
                    'menu': menu,
                    'menu-settings-column-nonce': nonce,
                    'menu-item': menuItem
                };

                $.post( ajaxurl, params, function(menuMarkup) {
                    var ins = $('#menu-instructions');

                    menuMarkup = $.trim( menuMarkup ); // Trim leading whitespaces
                    processMethod(menuMarkup, params);

                    // Make it stand out a bit more visually, by adding a fadeIn
                    $( 'li.pending' ).hide().fadeIn('slow');
                    $( '.drag-instructions' ).show();
                    if( ! ins.hasClass( 'menu-instructions-inactive' ) && ins.siblings().length )
                        ins.addClass( 'menu-instructions-inactive' );

                    callback();
                });
            };
        },
        /**
         * Keeps the checkbox checked! (required by WP AJAX call) Also submit form on enter.
         *
         * @since Client Dash 1.6
         */
        link_checkbox: function () {

            // Keep checkbox checked
            $('#submit-custom-link').click(function () {
                $('#custom-checkbox').prop('checked', true);

                // Clear fields
                setTimeout(function () {
                    wait_for_load();
                }, 50 );
            });

            // Submit on enter
            $('#custom-link').find('input[type="text"]').keypress(function (e) {
                if (e.which == 13) {
                    $('#submit-custom-link').click();

                    // Clear fields
                    wait_for_load();
                }
            });

            /**
             * Checks if the spinner is visible, and waits until it's not. Then clears the input fields.
             *
             * @since Client Dash 1.6
             */
            function wait_for_load() {
                if ($('#add-custom-links').find('.spinner').is(':visible')) {
                    setTimeout(wait_for_load, 100);
                } else {
                    $('#add-custom-links').find('input[type="text"]').val('');
                }
            }
        },
        /**
         * Keeps the checkbox checked! (required by WP AJAX call)
         *
         * @since Client Dash 1.6
         */
        separator_checkbox: function () {
            $('#submit-separator').click(function () {
                $('#separator-checkbox').prop('checked', true);
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
                            // (removed some)
                            'menu-item-db-id',
                            'menu-item-parent-id',
                            'menu-item-position',
                            'menu-item-title',
                            'menu-item-original-title',
                            'menu-item-url',
                            'menu-item-classes',
                            //
                            // CD added
                            'menu-item-cd-type',
                            'menu-item-cd-icon',
                            'menu-item-cd-page-title',
                            'menu-item-cd-submenu-parent',
                            'menu-item-cd-params',
                            'menu-item-cd-hookname'
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

            // CD Modification {
            var hasChildren;
            // } End CD Modification

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

                    // CD {
                    hasChildren = !!children.length;
                    // } End CD
                },
                stop: function (e, ui) {
                    var children, subMenuTitle, menuIcon,
                        depthChange = currentDepth - originalDepth;

                    // Return child elements to the list
                    children = transport.children().insertAfter(ui.item);

                    // Add "sub menu" description
                    subMenuTitle = ui.item.find('.item-title .is-submenu');
                    if (0 < currentDepth)
                        subMenuTitle.show();
                    else
                        subMenuTitle.hide();

                    // CD {
                    // Hide or show icon
                    menuIcon = ui.item.find('.item-title .dashicons');
                    if (currentDepth == 0)
                        menuIcon.removeClass('hidden');
                    else
                        menuIcon.addClass('hidden');
                    // } End CD

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

                    // CD {
                    // If is a separator AND was trying to be placed as a child, well, STOP IT!
                    if (ui.item.hasClass('menu-item-separator') && currentDepth != 0){

                        // Cancel the sort altogether
                        wpNavMenu.menuList.sortable( 'cancel' );

                        // Reset some other properties that may have been improperly updated
                        // Make sure it's depth is at base level
                        ui.item.updateDepthClass(0);

                        // Hide the "sub item" message
                        subMenuTitle.hide();

                        // Get rid of the input data for its parent
                        ui.item.find('.menu-item-data-parent-id').val('0');

                        // Shake, shake, sh-sh-sh shake it!
                        ui.item.effect('shake');
                    }

                    // } End CD

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

                    // CD {

                    // Make sure this isn't the child of a separator
                    var separatorIsParent = false;
                    $('#menu-to-edit').find('li.menu-item-separator').each(function () {

                        // If next child after separator is the placeholder
                        // OR if the next child is a helper AND the child after THAT is a placeholder
                        if ($(this).next('li.sortable-placeholder').length
                            || $(this).next('li.ui-sortable-helper').next('li.sortable-placeholder').length) {
                            separatorIsParent = true;
                        }
                    });

                    // If doesn't meet requirements, make it a parent
                    if (depth != currentDepth && !hasChildren && !separatorIsParent)
                        updateCurrentDepth(ui, depth);
                    else if (hasChildren || separatorIsParent)
                        updateCurrentDepth(ui, 0);

                    // } End CD

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
        },
        icon_selector: function () {

            // Show on click
            $('.edit-menu-item-cd-icon').click(function (e) {

                e.stopPropagation();

                if ($(this).is(':focus')) {
                    $(this).closest('.cd-menu-icon-field').find('.cd-menu-icon-selector').show();
                }
            });

            // Hide on click
            $('body').click(function () {
                $('.cd-menu-icon-selector').hide();
            });

            // Use new icon val on click
            $('.cd-menu-icon-selector').find('li').click(function () {

                var icon = $(this).attr('data-icon'),
                    new_class = $(this).closest('.menu-item').hasClass('menu-item-depth-1') ? 'dashicons hidden ' + icon : 'dashicons ' + icon;

                $(this).closest('.cd-menu-icon-field').find('input[type="text"]').val(icon);

                $(this).closest('.menu-item').find('.item-title').find('.dashicons').attr('class', new_class);
            });
        }
    };

    $(function () {
        // Only initialize if on CD page and if the page isn't "disabled"
        if ($('body').hasClass('cd-nav-menu') && !$('#menu-settings-column').hasClass('metabox-holder-disabled')) {
            cdMenus.init();
        }
    });
})(jQuery);