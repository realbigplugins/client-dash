import React from 'react';

import {sortableCancelStart} from './functions';
import LoadingIcon from './loading-icon';
import {
    LineItems,
    ItemAdd,
    MenuItemEdit,
    MenuItemSeparator,
    MenuItemCustomLink,
    SubmenuItemEdit,
    SubmenuItemCustomLink,
    SortableLineItems,
    WidgetEdit,
} from './line-items';

const l10n = ClientdashCustomize_Data.l10n || false;

/**
 * A Customizer panel.
 *
 * One panel shows at a time and they can be loaded and unloaded.
 *
 * @since 2.0.0
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
 * @since 2.0.0
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

                <a href="#" className="cd-editor-reset-role cd-editor-sub-action delete"
                   onClick={this.props.confirmReset}>
                    <span className="fa fa-trash"/> Reset role customizations
                </a>
            </Panel>
        )
    }
}

/**
 * Panel for confirming or cancelling resetting the role.
 *
 * @since 2.0.0
 */
class PanelConfirmReset extends React.Component {
    render() {
        return (
            <Panel id="confirmReset">
                <div className="cd-editor-lineitem-button delete"
                        onClick={this.props.resetRole}>
                    <span className="fa fa-trash"/>
                    {l10n['yes_understand']}
                </div>

                <div className="cd-editor-lineitem-button"
                        onClick={this.props.cancelReset}>
                    <span className="fa fa-ban"/>
                    {l10n['nevermind']}
                </div>
            </Panel>
        )
    }
}

/**
 * Primary panel action button.
 *
 * Shows on primary panel. Clicking loads new panels.
 *
 * @since 2.0.0
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
 * The menu editor panel.
 *
 * Edits the admin menu.
 *
 * @since 2.0.0
 */
class PanelMenu extends React.Component {

    constructor(props) {

        super(props);

        this.deleteItem     = this.deleteItem.bind(this);
        this.menuItemEdit   = this.menuItemEdit.bind(this);
        this.submenuEdit    = this.submenuEdit.bind(this);
        this.itemSubmitForm = this.itemSubmitForm.bind(this);
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
                                hasSubmenu={item.hasSubmenu}
                                onMenuItemEdit={this.menuItemEdit}
                                onSubmenuEdit={this.submenuEdit}
                                onDeleteItem={this.deleteItem}
                                onItemFormSubmit={this.itemSubmitForm}
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
                                missing={item.missing}
                                original_icon={item.original_icon}
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
                    onSortEnd={this.props.reOrderMenu}
                    axis="y"
                    lockAxis="y"
                    lockToContainerEdges={true}
                    pressDelay={50}
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
 * @since 2.0.0
 */
class PanelSubmenu extends React.Component {

    constructor(props) {

        super(props);

        this.deleteItem      = this.deleteItem.bind(this);
        this.submenuItemEdit = this.submenuItemEdit.bind(this);
        this.itemSubmitForm  = this.itemSubmitForm.bind(this);
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
                                onSubmenuItemEdit={this.submenuItemEdit}
                                onDeleteItem={this.deleteItem}
                                onItemFormSubmit={this.itemSubmitForm}
                            />
                        ;
                        break;

                    default:

                        menu_item =
                            <SubmenuItemEdit
                                key={item.id}
                                id={item.id}
                                title={item.title}
                                type={item.type}
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
                    onSortEnd={this.props.reOrderSubmenu}
                    axis="y"
                    lockAxis="y"
                    pressDelay={50}
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
 * @since 2.0.0
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
                        new={item.new || false}
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
 * @since 2.0.0
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

    widgetEdit(args) {

        this.props.onWidgetEdit(args);
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
                        type={item.type}
                        original_title={item.original_title}
                        settings={item.settings || {}}
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
 * @since 2.0.0
 */
class PanelLoading extends React.Component {
    render() {

        return (
            <Panel id="loading">
                <LoadingIcon />
            </Panel>
        )
    }
}

/**
 * Panel with nothing in it.
 *
 * @since 2.0.0
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
    PanelConfirmReset,
    PanelAddItems,
    PanelBlank,
    PanelDashboard,
    PanelLoadButton,
    PanelLoading,
    PanelMenu,
    PanelSubmenu
}