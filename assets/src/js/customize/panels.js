import React from 'react';

import {sortableCancelStart} from './functions';
import {
    LineItems,
    ItemAdd,
    MenuItemEdit,
    MenuItemSeparator,
    MenuItemCustomLink,
    MenuItemCDPage,
    SubmenuItemEdit,
    SubmenuItemCustomLink,
    SortableLineItems,
    WidgetEdit,
    CDPageEdit
} from './line-items';
import {arrayMove} from 'react-sortable-hoc';

const l10n = ClientdashCustomize_Data.l10n || false;

/**
 * A Customizer panel.
 *
 * One panel shows at a time and they can be loaded and unloaded.
 *
 * @since {{VERSION}}
 */
class Panel extends React.Component {
    render() {
        return (
            <div className={"cd-editor-panel " + "cd-editor-panel-" + this.props.id}>
                {this.props.children}
            </div>
        )
    }
}

/**
 * The primary (and first loading) panel.
 *
 * @since {{VERSION}}
 */
class PanelPrimary extends React.Component {

    constructor(props) {

        super(props);

        this.loadPanel = this.loadPanel.bind(this);
    }

    loadPanel(panel_ID) {

        this.props.onLoadPanel(panel_ID, 'forward');
    }


    render() {
        return (
            <Panel id="primary">
                <PanelLoadButton
                    text={l10n['panel_text_menu']}
                    target="menu"
                    onLoadPanel={this.loadPanel}
                />
                <PanelLoadButton
                    text={l10n['panel_text_dashboard']}
                    target="dashboard"
                    onLoadPanel={this.loadPanel}
                />
                <PanelLoadButton
                    text={l10n['panel_text_cd_pages']}
                    target="cdPages"
                    onLoadPanel={this.loadPanel}
                />
            </Panel>
        )
    }
}

/**
 * Primary panel action button.
 *
 * Shows on primary panel. Clicking loads new panels.
 *
 * @since {{VERSION}}
 *
 * @prop (string) text The text to show in the action.
 * @prop (string) target The target panel ID.
 */
class PanelLoadButton extends React.Component {

    constructor(props) {

        super(props);

        this.handleClick = this.handleClick.bind(this);
    }

    handleClick() {

        this.props.onLoadPanel(this.props.target);
    }

    render() {
        return (
            <div className="cd-editor-panel-load-button" onClick={this.handleClick}>
                {this.props.text}
                <span className="cd-editor-panel-icon fa fa-chevron-right"></span>
            </div>
        )
    }
}

/**
 * Panel for setting visibility of core CD pages.
 *
 * @since {{VERSION}}
 */
class PanelCDPages extends React.Component {

    constructor(props) {

        super(props);

        this.pageDelete     = this.pageDelete.bind(this);
        this.pageEdit       = this.pageEdit.bind(this);
        this.pageTabsEdit   = this.pageTabsEdit.bind(this);
        this.itemSubmitForm = this.itemSubmitForm.bind(this);
    }

    pageDelete(ID) {

        this.props.onPageDelete(ID);
    }

    pageEdit(page) {

        this.props.onPageEdit(page);
    }

    pageTabsEdit(ID) {

        this.props.onPageTabsEdit(ID);
    }

    itemSubmitForm(event) {

        this.props.onItemSubmitForm(event);
    }

    render() {

        let pages = [];
        let panel_contents;

        if ( this.props.pages.length ) {

            this.props.pages.map((item) => {

                pages.push(
                    <CDPageEdit
                        key={item.id}
                        id={item.id}
                        title={item.title}
                        original_title={item.original_title}
                        icon={item.icon}
                        original_icon={item.original_icon}
                        parent={item.parent}
                        parent_label={item.parent_label}
                        onPageEdit={this.pageEdit}
                        onPageDelete={this.pageDelete}
                        onItemFormSubmit={this.itemSubmitForm}
                    />
                );
            });

            panel_contents =
                <LineItems items={pages}/>
            ;

        } else {

            panel_contents =
                <div className="cd-editor-panel-helptext">
                    {l10n['no_items_added']}
                </div>
        }
        return (
            <Panel id="cd-pages">
                {panel_contents}
            </Panel>
        )
    }
}

/**
 * The menu editor panel.
 *
 * Edits the admin menu.
 *
 * @since {{VERSION}}
 */
class PanelMenu extends React.Component {

    constructor(props) {

        super(props);

        this.onSortEnd      = this.onSortEnd.bind(this);
        this.deleteItem     = this.deleteItem.bind(this);
        this.menuItemEdit   = this.menuItemEdit.bind(this);
        this.submenuEdit    = this.submenuEdit.bind(this);
        this.itemSubmitForm = this.itemSubmitForm.bind(this);
    }

    onSortEnd(args) {

        const new_order = arrayMove(this.props.menuItems, args.oldIndex, args.newIndex);

        this.props.onReOrderMenu(new_order);
    }

    deleteItem(ID) {

        this.props.onDeleteItem(ID);
    }

    menuItemEdit(item) {

        this.props.onMenuItemEdit(item);
    }

    submenuEdit(ID) {

        this.props.onSubmenuEdit(ID);
    }

    itemSubmitForm(event) {

        this.props.onItemSubmitForm(event);
    }

    render() {

        let menu_items = [];
        let menu_item;
        let panel_contents;

        if ( this.props.menuItems.length ) {

            this.props.menuItems.map((item) => {

                switch ( item.type ) {

                    case 'separator':

                        menu_item =
                                <MenuItemSeparator
                                    key={item.id}
                                    id={item.id}
                                    onDeleteItem={this.deleteItem}
                                />
                            ;

                        break;

                    case 'custom_link':

                        menu_item =
                                <MenuItemCustomLink
                                    key={item.id}
                                    id={item.id}
                                    title={item.title}
                                    link={item.link}
                                    original_title={item.original_title}
                                    icon={item.icon}
                                    original_icon={item.original_icon}
                                    editing={this.props.editing === item.id}
                                    hasSubmenu={item.hasSubmenu}
                                    onMenuItemEdit={this.menuItemEdit}
                                    onSubmenuEdit={this.submenuEdit}
                                    onDeleteItem={this.deleteItem}
                                    onItemFormSubmit={this.itemSubmitForm}
                                />
                            ;

                        break;

                    case 'cd_page':

                        menu_item =
                            <MenuItemCDPage
                                key={item.id}
                                id={item.id}
                                title={item.title || item.original_title}
                                icon={item.icon || item.original_icon}
                                onDeleteItem={this.deleteItem}
                            />
                        ;

                        break;

                    default:

                        menu_item =
                                <MenuItemEdit
                                    key={item.id}
                                    id={item.id}
                                    title={item.title}
                                    original_title={item.original_title}
                                    icon={item.icon}
                                    type={item.type}
                                    original_icon={item.original_icon}
                                    editing={this.props.editing === item.id}
                                    hasSubmenu={item.hasSubmenu}
                                    onMenuItemEdit={this.menuItemEdit}
                                    onSubmenuEdit={this.submenuEdit}
                                    onDeleteItem={this.deleteItem}
                                    onItemFormSubmit={this.itemSubmitForm}
                                />
                            ;
                }


                menu_items.push(menu_item);
            });

            panel_contents =
                <SortableLineItems
                    items={menu_items}
                    onSortEnd={this.onSortEnd}
                    axis="y"
                    lockAxis="y"
                    lockToContainerEdges={true}
                    shouldCancelStart={sortableCancelStart}
                />
            ;

        } else {

            panel_contents =
                <div className="cd-editor-panel-helptext">
                    {l10n['no_items_added']}
                </div>
        }

        return (
            <Panel id="menu">
                {panel_contents}
            </Panel>
        )
    }
}

/**
 * The sub-menu editor panel.
 *
 * Edits the admin menu.
 *
 * @since {{VERSION}}
 */
class PanelSubmenu extends React.Component {

    constructor(props) {

        super(props);

        this.onSortEnd       = this.onSortEnd.bind(this);
        this.deleteItem      = this.deleteItem.bind(this);
        this.submenuItemEdit = this.submenuItemEdit.bind(this);
        this.itemSubmitForm  = this.itemSubmitForm.bind(this);
    }

    onSortEnd(args) {

        let new_order = arrayMove(this.props.submenuItems, args.oldIndex, args.newIndex);

        this.props.onReOrderSubmenu(new_order);
    }

    deleteItem(item_id) {

        this.props.onDeleteItem(item_id);
    }

    submenuItemEdit(item) {

        this.props.onSubmenuItemEdit(item);
    }

    itemSubmitForm(event) {

        this.props.onItemSubmitForm(event);
    }

    render() {

        let menu_items = [];
        let panel_contents;

        if ( this.props.submenuItems.length ) {

            this.props.submenuItems.map((item) => {

                let menu_item;

                switch ( item.type ) {

                    case 'custom_link':

                        menu_item =
                            <SubmenuItemCustomLink
                                key={item.id}
                                id={item.id}
                                title={item.title}
                                link={item.link}
                                original_title={item.original_title}
                                editing={this.props.editing === item.id}
                                onSubmenuItemEdit={this.submenuItemEdit}
                                onDeleteItem={this.deleteItem}
                                onItemFormSubmit={this.itemSubmitForm}
                            />
                        ;
                        break;

                    case 'cd_page':

                        menu_item =
                            <MenuItemCDPage
                                key={item.id}
                                id={item.id}
                                title={item.title || item.original_title}
                                onDeleteItem={this.deleteItem}
                            />
                        break;

                    default:

                        menu_item =
                            <SubmenuItemEdit
                                key={item.id}
                                id={item.id}
                                title={item.title}
                                type={item.type}
                                editing={this.props.editing === item.id}
                                onSubmenuItemEdit={this.submenuItemEdit}
                                original_title={item.original_title}
                                onDeleteItem={this.deleteItem}
                                onItemFormSubmit={this.itemSubmitForm}
                            />
                        ;
                }

                menu_items.push(menu_item);
            });

            panel_contents =
                <SortableLineItems
                    items={menu_items}
                    onSortEnd={this.onSortEnd}
                    axis="y"
                    lockAxis="y"
                    lockToContainerEdges={true}
                    shouldCancelStart={sortableCancelStart}
                />
            ;

        } else {

            panel_contents =
                <div className="cd-editor-panel-helptext">
                    {l10n['no_items_added']}
                </div>
        }

        return (
            <Panel id="submenu">
                {this.props.itemInfo}
                {panel_contents}
            </Panel>
        )
    }
}

/**
 * The add items panel.
 *
 * @since {{VERSION}}
 */
class PanelAddItems extends React.Component {

    constructor(props) {

        super(props);

        this.addItem = this.addItem.bind(this);
    }

    addItem(item) {

        this.props.onAddItem(item);
    }

    render() {

        let items = [];
        let panel_contents;

        if ( this.props.availableItems.length ) {

            this.props.availableItems.map((item) => {

                items.push(
                    <ItemAdd
                        key={item.id}
                        id={item.id}
                        title={item.title || item.original_title}
                        icon={item.icon || item.original_icon}
                        type={item.type || null}
                        onAddItem={this.addItem}
                    />
                );
            });

            panel_contents = <LineItems items={items}/>;

        } else {

            panel_contents =
                <div className="cd-editor-panel-helptext">
                    {l10n['no_items_available']}
                </div>
            ;
        }

        return (
            <Panel id="add-items">
                {this.props.itemInfo}

                {panel_contents}
            </Panel>
        )
    }
}

/**
 * The Dashboard panel
 *
 * @since {{VERSION}}
 */
class PanelDashboard extends React.Component {

    constructor(props) {

        super(props);

        this.widgetDelete   = this.widgetDelete.bind(this);
        this.widgetEdit     = this.widgetEdit.bind(this);
        this.itemSubmitForm = this.itemSubmitForm.bind(this);
    }

    widgetDelete(ID) {

        this.props.onDeleteWidget(ID);
    }

    widgetEdit(widget) {

        this.props.onWidgetEdit(widget);
    }

    itemSubmitForm(event) {

        this.props.onItemSubmitForm(event);
    }

    render() {

        let widgets = [];
        let panel_contents;

        if ( this.props.widgets.length ) {

            this.props.widgets.map((item) => {

                widgets.push(
                    <WidgetEdit
                        key={item.id}
                        id={item.id}
                        title={item.title}
                        original_title={item.original_title}
                        onWidgetEdit={this.widgetEdit}
                        onWidgetDelete={this.widgetDelete}
                        onItemFormSubmit={this.itemSubmitForm}
                    />
                );
            });

            panel_contents =
                <LineItems items={widgets}/>
            ;

        } else {

            panel_contents =
                <div className="cd-editor-panel-helptext">
                    {l10n['no_items_added']}
                </div>
        }

        return (
            <Panel id="dashboard">
                {panel_contents}
            </Panel>
        )
    }
}

/**
 * Panel for loading indicator.
 *
 * @since {{VERSION}}
 */
class PanelLoading extends React.Component {
    render() {

        return (
            <Panel id="loading">
                <span className="fa fa-circle-o-notch fa-spin"></span>
            </Panel>
        )
    }
}

/**
 * Panel with nothing in it.
 *
 * @since {{VERSION}}
 */
class PanelBlank extends React.Component {
    render() {

        return (
            <Panel id="blank">
            </Panel>
        )
    }
}

export {
    Panel,
    PanelPrimary,
    PanelAddItems,
    PanelBlank,
    PanelCDPages,
    PanelDashboard,
    PanelLoadButton,
    PanelLoading,
    PanelMenu,
    PanelSubmenu
}