/**
 * Takes an array of indexes, sorts them, then finds the first available.
 *
 * Example: You have [1,2,4]. This will return 3.
 * Example2: You have [1,2,3]. This will return 4.
 *
 * @since 2.0.0
 *
 * @param {[]} indexes
 *
 * @return {int}
 */
function getFirstAvailableIndex(indexes) {

    indexes.sort((a, b) => a - b);

    let index = 1;
    let i     = 0;

    while ( index === indexes[i] ) {
        i++;
        index++;
    }

    return index;
}

/**
 * Given a list of items, this finds the first available index for the item of a given type and then returns the new ID.
 *
 * @since 2.0.0
 *
 * @param {[]} items
 * @param {string} type
 * @returns {string}
 */
function getNewItemID(items, type) {

    let indexes = [];

    // Get all current item indexes
    items.map((item) => {

        if ( item.type === type ) {

            let regex   = new RegExp(type + '(\\d+)');
            let matches = item.id.match(regex);

            if ( matches ) {

                indexes.push(parseInt(matches[1]));
            }
        }
    });

    return type + getFirstAvailableIndex(indexes);
}

/**
 * Returns an item in an array based on the item ID.
 *
 * @since 2.0.0
 *
 * @param items
 * @param ID
 * @returns {boolean}
 */
function getItem(items, ID) {

    let found = false;

    items.map((item) => {

        if ( item.id === ID ) {

            found = item;
        }
    });

    return found;
}

/**
 * Gets an items index by the item ID.
 *
 * @since 2.0.0
 *
 * @param items
 * @param ID
 * @returns {int|bool}
 */
function getItemIndex(items, ID) {

    let index = false;

    items.map((item, i) => {

        if ( item.id === ID ) {

            index = i;
        }
    });

    return index;
}

/**
 * Delets an item in an array based on the item ID.
 *
 * @since 2.0.0
 *
 * @param items
 * @param ID
 * @returns {boolean}
 */
function deleteItem(items, ID) {

    items = items.filter((item) => {

        return item.id !== ID;
    });

    return items;
}

/**
 * Modifies an item in an array based on the supplied new item.
 *
 * @since 2.0.0
 *
 * @param items
 * @param ID
 * @param new_item
 * @returns {*}
 */
function modifyItem(items, ID, new_item) {

    items = items.map((item) => {

        if ( item.id === ID ) {

            Object.keys(new_item).map((key) => {

                item[key] = new_item[key];
            });
        }

        return item;
    });

    return items;
}

/**
 * Returns all items marked "deleted" from a list.
 *
 * @since 2.0.0
 *
 * @param {[]} items
 * @returns {[]}
 */
function getDeletedItems(items) {

    items = items.filter((item) => {

        return item.deleted;
    });

    return items;
}

/**
 * Returns all items not marked "deleted" from a list.
 *
 * @since 2.0.0
 *
 * @param {{}} items
 * @returns {{}}
 */
function getAvailableItems(items) {

    items = items.filter((item) => {

        return !item.deleted;
    });

    return items;
}

/**
 * Sets each item's title to the original title.
 *
 * @since 2.0.0
 *
 * @param items
 * @returns {*}
 */
function setToOriginalTitles(items) {

    items = items.map((item) => {

        item.title = item.original_title;

        return item;
    });

    return items;
}

/**
 * Modify the sortable cancel start callback to include <button>'s.
 *
 * @since 2.0.0
 *
 * @param e
 */
function sortableCancelStart(e) {

    let unallowed_classes = [
        'cd-editor-lineitem-action',
        'cd-editor-dashicons-selector',
        'cd-editor-dashicons-selector-field',
    ];

    let unallowed_tags = [
        'input',
        'textarea',
        'select',
        'option',
        'button',
        'label',
        'form'
    ];

    let cancel = false;

    unallowed_classes.map((className) => {

        if ( e.target.className.includes(className) || e.target.parentNode.className.includes(className) ) {

            cancel = true;
        }
    });

    if ( !cancel ) {

        cancel = unallowed_tags.indexOf(e.target.tagName.toLowerCase()) !== -1;
    }

    return cancel;
}

/**
 * Makes sure the supplied parameter comes out an array.
 *
 * @since 2.0.0
 *
 * @param maybeObject
 * @returns {*}
 */
function ensureArray(maybeObject) {

    if ( typeof maybeObject === 'array' ) {

        return maybeObject;
    }

    let array = []

    // Walk through object and push values to array
    Object.keys(maybeObject).map((index) => array.push(maybeObject[index]));

    return array;
}

export {
    getFirstAvailableIndex,
    getNewItemID,
    getItem,
    getItemIndex,
    deleteItem,
    modifyItem,
    getDeletedItems,
    getAvailableItems,
    setToOriginalTitles,
    sortableCancelStart,
    ensureArray
}