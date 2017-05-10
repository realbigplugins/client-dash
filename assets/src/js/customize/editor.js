import React from 'react';
import ReactCSSTransitionReplace from 'react-css-transition-replace';
import {arrayMove} from 'react-sortable-hoc';

import {
    getItem,
    deleteItem,
    modifyItem,
    getDeletedItems,
    getAvailableItems,
    getNewItemID,
    getItemIndex
} from './functions';
import {
    PanelPrimary,
    PanelAddItems,
    PanelBlank,
    PanelCDPages,
    PanelDashboard,
    PanelLoading,
    PanelMenu,
    PanelSubmenu
} from './panels';
import {SecondaryActions, SecondaryActionsPrimary} from './secondary-actions';
import PrimaryActions from './primary-actions';
import RoleSwitcher from './role-switcher';
import Message from './message';

const l10n     = ClientdashCustomize_Data.l10n || false;
const adminurl = ClientdashCustomize_Data.adminurl || false;
const api_nonce = ClientdashCustomize_Data.api_nonce || false;

/**
 * The Customize editor.
 *
 * @since {{VERSION}}
 */
class Editor extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            nextPanel: null,
            panelDirection: 'forward',
            activePanel: 'loading',
            hidden: false,
            submenuEdit: null,
            loadingPreview: false,
            saving: false,
            deleting: false,
            loading: true,
            changes: false,
            // cd_pages: ClientdashCustomize_Data.cd_pages || [],
            message: {
                type: 'default',
                text: null
            },
            customizations: {},
            history: {},
        }

        this.loadPanel         = this.loadPanel.bind(this);
        this.saveChanges       = this.saveChanges.bind(this);
        this.confirmReset      = this.confirmReset.bind(this);
        this.cancelReset       = this.cancelReset.bind(this);
        this.resetRole         = this.resetRole.bind(this);
        this.resetMessage      = this.resetMessage.bind(this);
        this.previewChanges    = this.previewChanges.bind(this);
        this.hideCustomizer    = this.hideCustomizer.bind(this);
        this.closeCustomizer   = this.closeCustomizer.bind(this);
        this.switchRole        = this.switchRole.bind(this);
        this.menuItemAdd       = this.menuItemAdd.bind(this);
        this.submenuItemAdd    = this.submenuItemAdd.bind(this);
        this.menuItemDelete    = this.menuItemDelete.bind(this);
        this.submenuItemDelete = this.submenuItemDelete.bind(this);
        this.submenuEdit       = this.submenuEdit.bind(this);
        this.menuItemEdit      = this.menuItemEdit.bind(this);
        this.reOrderMenu       = this.reOrderMenu.bind(this);
        this.reOrderSubmenu    = this.reOrderSubmenu.bind(this);
        this.submenuItemEdit   = this.submenuItemEdit.bind(this);
        this.widgetAdd         = this.widgetAdd.bind(this);
        this.widgetDelete      = this.widgetDelete.bind(this);
        this.widgetEdit        = this.widgetEdit.bind(this);
        this.handleEditorClick = this.handleEditorClick.bind(this);
        this.showMessage       = this.showMessage.bind(this);
        this.cdPageTabsEdit    = this.cdPageTabsEdit.bind(this);
        this.cdPageEdit        = this.cdPageEdit.bind(this);
        this.cdPageDelete      = this.cdPageDelete.bind(this);
        this.cdPageAdd         = this.cdPageAdd.bind(this);
    }

    componentDidMount() {

        let api = this;

        // Confirm before leave
        window.onbeforeunload = function () {

            if ( api.state.changes ) {

                return l10n['leave_confirmation'];
            }
        };
    }

    loadPanel(panel_ID, direction) {

        direction = direction || 'forward';

        this.setState({
            activePanel: panel_ID,
            panelDirection: direction,
            message: {
                type: 'default',
                text: ''
            }
        });
    }

    hideCustomizer() {

        this.props.onHideCustomizer();
    }

    closeCustomizer() {

        window.location.href = adminurl;
    }

    saveChanges() {

        let api = this;

        this.setState({
            saving: true,
        });

        fetch('wp-json/clientdash/v1/customizations/' + this.props.role, {
            method: 'POST',
            credentials: 'same-origin',
            headers: new Headers({
                'Content-Type': 'application/json',
                'X-WP-Nonce': api_nonce,
            }),
            body: JSON.stringify(this.state.customizations[this.props.role])
        }).then(function (response) {

            return response.json();

        }).then(function (customizations) {

            api.setState({
                saving: false,
                changes: false,
                message: {
                    type: 'success',
                    text: l10n['saved']
                }
            });

        }).catch(function (error) {

            console.log('error: ', error);
        });
    }

    previewChanges() {

        let api = this;

        clearTimeout(this.refreshingTimeout);

        this.setState({
            loadingPreview: true,
        });

        fetch('wp-json/clientdash/v1/customizations/preview_' + this.props.role, {
            method: 'POST',
            credentials: 'same-origin',
            headers: new Headers({
                'Content-Type': 'application/json',
                'X-WP-Nonce': api_nonce,
            }),
            body: JSON.stringify(this.state.customizations[this.props.role])
        }).then(function (response) {

            return response.json();

        }).then(function (customizations) {

            api.setState({
                loadingPreview: false,
            });

            api.props.refreshPreview();

        }).catch(function (error) {

            console.log('error: ', error);

        });
    }

    confirmReset() {

        this.setState({
            activePanel: 'confirmReset',
            panelDirection: 'forward',
            message: {
                type: 'default',
                text: l10n['confirm_role_reset'],
                noHide: true
            }
        });
    }

    cancelReset() {

        this.setState({
            activePanel: 'primary',
            panelDirection: 'backward',
            message: {
                type: 'default',
                text: ''
            }
        });
    }

    resetRole() {

        let role = this.props.role;
        let api  = this;

        this.setState({
            deleting: true,
            activePanel: 'deleting',
            panelDirection: 'forward',
            message: {
                type: 'default',
                text: ''
            }
        });

        fetch('wp-json/clientdash/v1/customizations/' + this.props.role, {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: new Headers({
                'Content-Type': 'application/json',
                'X-WP-Nonce': api_nonce,
            }),
        }).then(function (response) {

            return response.json();

        }).then(function (customizations) {

            api.setState((prevState) => {

                prevState.deleting       = false;
                prevState.loading        = true;
                prevState.changes        = false;
                prevState.activePanel    = 'loading';
                prevState.panelDirection = 'backward';
                prevState.message        = {
                    type: 'success',
                    text: l10n['role_reset']
                };

                delete prevState.customizations[role];

                return prevState;
            });

            api.props.onResetRole(role);

        }).catch(function (error) {

            console.log('error: ', error);
        });
    }

    switchRole(role) {

        this.props.onSwitchRole(role);

        this.setState({
            activePanel: 'loading',
            panelDirection: 'forward',
            loading: true
        });
    }

    loadRole() {

        // Get customizations
        if ( !this.state.customizations[this.props.role] ) {

            let role = this.props.role;
            let api  = this;

            this.setState({
                activePanel: 'loading',
                loading: true
            });

            fetch('wp-json/clientdash/v1/customizations/preview_' + role, {
                method: 'GET',
                credentials: 'same-origin',
                headers: new Headers({
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': api_nonce,
                })
            }).then(function (response) {

                return response.json();

            }).then(function (customizations) {

                api.setState((prevState) => {

                    prevState.activePanel = 'primary';
                    prevState.loading     = false;

                    prevState.customizations[role] = customizations;
                    prevState.history[role]        = {};

                    return prevState;
                });

            }).catch(function (error) {

                console.log('error: ', error);
            });

        } else if ( this.state.activePanel == 'loading' ) {

            this.setState((prevState) => {

                prevState.activePanel = 'primary';
                prevState.loading     = false;
            });
        }
    }

    getAvailableCDPages() {

        let available_pages = this.state.customizations[this.props.role].cdpages;

        available_pages = available_pages.filter((item) => {

            return !item.deleted && !item.parent;
        });

        return available_pages;
    }

    dataChange(refreshDelay) {

        clearTimeout(this.refreshingTimeout);

        refreshDelay = refreshDelay === false ? 10 : 2000;

        this.refreshingTimeout = setTimeout(() => this.previewChanges(), refreshDelay);

        this.setState({
            changes: true
        });
    }

    resetMessage() {

        this.setState({
            message: {
                text: null
            }
        });
    }

    menuItemAdd(item) {

        let role = this.props.role;

        this.setState((prevState) => {

            let menu = prevState.customizations[role].menu;

            switch ( item.type ) {
                case 'separator':
                case 'custom_link':

                    let new_item_id = getNewItemID(menu, item.type);

                    prevState.customizations[role].menu.unshift({
                        id: new_item_id,
                        original_title: l10n[item.type],
                        icon: 'dashicons-admin-generic',
                        type: item.type,
                    });

                    prevState.history[role].menuItemLastAdded = new_item_id;

                    break;

                case 'cd_page':

                    prevState.customizations[role].cdpages = modifyItem(
                        prevState.customizations[role].cdpages,
                        item.id,
                        {
                            parent: 'toplevel',
                            position: getItemIndex(menu, item.id)
                        }
                    );

                    prevState.customizations[role].menu.unshift({
                        id: item.id,
                        original_title: item.title || item.original_title,
                        original_icon: item.icon || item.original_icon,
                        type: 'cd_page',
                    });

                    prevState.history[role].menuItemLastAdded = item.id;

                    break;

                default:

                    prevState.customizations[role].menu = modifyItem(menu, item.id, {deleted: false});

                    // Move to beginning
                    prevState.customizations[role].menu = arrayMove(
                        menu,
                        getItemIndex(menu, item.id),
                        0
                    );

                    prevState.history[role].menuItemLastAdded = item.id;
            }

            return prevState;
        });

        this.dataChange(false);

        this.loadPanel('menu', 'backward');
    }

    submenuItemAdd(item) {

        let submenu_edit = this.state.submenuEdit;
        let role         = this.props.role;
        let submenu      = this.state.customizations[role].submenu[submenu_edit] || [];

        this.setState((prevState) => {

            switch ( item.type ) {
                case 'custom_link':

                    let new_item_id = getNewItemID(submenu, item.type);

                    submenu.unshift({
                        id: new_item_id,
                        original_title: l10n[item.type],
                        type: item.type,
                    });

                    this.state.customizations[role].submenu[submenu_edit] = submenu;
                    prevState.history[role].SubmenuItemLastAdded          = new_item_id;

                    break;

                case 'cd_page':

                    prevState.customizations[role].cdpages = modifyItem(
                        prevState.customizations[role].cdpages,
                        item.id,
                        {parent: submenu_edit}
                    );

                    submenu.unshift({
                        id: item.id,
                        original_title: item.title || item.original_title,
                        type: 'cd_page',
                    });

                    this.state.customizations[role].submenu[submenu_edit] = submenu;
                    prevState.history[role].SubmenuItemLastAdded          = item.id;

                    break;

                default:

                    prevState.customizations[role].submenu[submenu_edit] = modifyItem(
                        prevState.customizations[role].submenu[submenu_edit],
                        item.id,
                        {deleted: false}
                    );

                    // Move to beginning
                    prevState.customizations[role].submenu[submenu_edit] = arrayMove(
                        prevState.customizations[role].submenu[submenu_edit],
                        getItemIndex(prevState.customizations[role].submenu[submenu_edit], item.id),
                        0
                    );

                    prevState.history[role].SubmenuItemLastAdded = item.id;
            }

            return prevState;
        });

        this.dataChange(false);

        this.loadPanel('submenu', 'backward');
    }

    menuItemDelete(ID) {

        let role = this.props.role;

        this.setState((prevState) => {

            let item = getItem(prevState.customizations[role].menu, ID);

            switch ( item.type ) {
                case 'separator':
                case 'custom_link':
                case 'cd_page':

                    if ( item.type == 'cd_page' ) {

                        prevState.customizations[role].cdpages = modifyItem(
                            prevState.customizations[role].cdpages,
                            item.id,
                            {parent: false}
                        );
                    }

                    prevState.customizations[role].menu = deleteItem(prevState.customizations[role].menu, ID);

                    break;

                default:

                    prevState.customizations[role].menu = modifyItem(
                        prevState.customizations[role].menu,
                        ID,
                        {deleted: true, title: '', icon: ''}
                    );
            }

            return prevState;
        });

        this.dataChange(false);
    }


    submenuItemDelete(ID) {

        let submenu_edit = this.state.submenuEdit;
        let role         = this.props.role;
        let submenu      = this.state.customizations[role].submenu[submenu_edit];

        this.setState((prevState) => {

            let item = getItem(submenu, ID);

            switch ( item.type ) {
                case 'custom_link':
                case 'cd_page':

                    if ( item.type == 'cd_page' ) {

                        prevState.customizations[role].cdpages = modifyItem(
                            prevState.customizations[role].cdpages,
                            item.id,
                            {parent: false}
                        );
                    }

                    prevState.customizations[role].submenu[submenu_edit] =
                        deleteItem(submenu, ID);

                    break;

                default:

                    prevState.customizations[role].submenu[submenu_edit] = modifyItem(
                        submenu,
                        ID,
                        {deleted: true, title: ''}
                    )
            }

            return prevState;
        });

        this.dataChange(false);
    }

    submenuEdit(ID) {

        this.setState({
            submenuEdit: ID
        });

        this.loadPanel('submenu', 'forward');
    }

    menuItemEdit(item) {

        let role = this.props.role;

        this.setState((prevState) => {

            prevState.customizations[role].menu = modifyItem(
                prevState.customizations[role].menu,
                item.id,
                item
            );

            return prevState;
        });

        this.dataChange();
    }

    submenuItemEdit(item) {

        let submenu_edit = this.state.submenuEdit;
        let role         = this.props.role;

        this.setState((prevState) => {

            prevState.customizations[role].submenu[submenu_edit] = modifyItem(
                prevState.customizations[role].submenu[submenu_edit],
                item.id,
                item
            );

            return prevState;
        });

        this.dataChange();
    }

    reOrderMenu(new_order) {

        let role = this.props.role;

        this.setState((prevState) => {

            prevState.customizations[role].menu = new_order;

            return prevState;
        });

        this.dataChange(false);
    }

    reOrderSubmenu(new_order) {

        let submenu_edit = this.state.submenuEdit;
        let role         = this.props.role;

        this.setState((prevState) => {

            prevState.customizations[role].submenu[submenu_edit] = new_order;

            return prevState;
        });

        this.dataChange(false);
    }

    widgetAdd(widget) {

        let role = this.props.role;

        this.setState((prevState) => {

            prevState.customizations[role].dashboard = modifyItem(
                prevState.customizations[role].dashboard,
                widget.id,
                {deleted: false}
            );

            return prevState;
        });

        this.dataChange(false);

        this.loadPanel('dashboard', 'backward');
    }

    widgetDelete(ID) {

        let role = this.props.role;

        this.setState((prevState) => {

            prevState.customizations[role].dashboard = modifyItem(
                prevState.customizations[role].dashboard,
                ID,
                {deleted: true, title: ''}
            );

            return prevState;
        });

        this.dataChange(false);
    }

    widgetEdit(widget) {

        let role = this.props.role;

        this.setState((prevState) => {

            prevState.customizations[role].dashboard = modifyItem(
                prevState.customizations[role].dashboard,
                widget.id,
                widget
            );

            return prevState;
        });

        this.dataChange();
    }

    cdPageTabsEdit(ID) {

        this.setState({
            cdPagesTabsEdit: ID
        });

        this.loadPanel('cdPagesTabsEdit', 'forward');
    }

    cdPageEdit(page) {

        let role = this.props.role;

        this.setState((prevState) => {

            prevState.customizations[role].cdpages = modifyItem(
                prevState.customizations[role].cdpages,
                page.id,
                page
            );

            // Edit CD page accordingly within menu
            let cdpage = getItem(prevState.customizations[role].cdpages, page.id);

            if ( cdpage.parent == 'toplevel' ) {

                prevState.customizations[role].menu = modifyItem(
                    prevState.customizations[role].menu,
                    cdpage.id,
                    page
                );

            } else if ( cdpage.parent !== false ) {

                prevState.customizations[role].submenu[cdpage.parent] = modifyItem(
                    prevState.customizations[role].submenu[cdpage.parent],
                    cdpage.id,
                    page
                );
            }

            return prevState;
        });

        this.dataChange();
    }

    cdPageDelete(ID) {

        let role = this.props.role;

        this.setState((prevState) => {

            prevState.customizations[role].cdpages = modifyItem(
                prevState.customizations[role].cdpages,
                ID,
                {deleted: true, title: '', icon: ''}
            );

            return prevState;
        });

        this.dataChange(false);
    }

    cdPageAdd(page) {

        let role = this.props.role;

        this.setState((prevState) => {

            prevState.customizations[role].cdpages = modifyItem(
                prevState.customizations[role].cdpages,
                page.id,
                {deleted: false}
            );

            return prevState;
        });

        this.dataChange(false);

        this.loadPanel('cdPages', 'backward');
    }

    handleEditorClick() {

        if ( !this.state.message.noHide ) {

            this.resetMessage();
        }
    }

    showMessage(message) {

        this.setState({
            message: message
        });
    }

    render() {

        let customizations = this.state.customizations[this.props.role];
        let panel;
        let secondary_actions;
        let history        = this.state.history[this.props.role] || {};

        switch ( this.state.activePanel ) {

            case 'primary':
            case 'confirmReset':
            case 'deleting': {
                panel =
                    <PanelPrimary
                        key="primary"
                        onLoadPanel={this.loadPanel}
                    />
                ;

                secondary_actions =
                    <SecondaryActionsPrimary
                        key="primary"
                        onResetRole={this.resetRole}
                        onConfirmReset={this.confirmReset}
                        onCancelReset={this.cancelReset}
                        deleting={this.state.deleting}
                        disabled={this.state.saving || this.state.deleting}
                    />
                ;
                break;
            }
            case 'menu': {

                let current_items   = customizations.menu;
                let available_items = getAvailableItems(current_items);

                // Filter out disabled CD Pages
                available_items = available_items.filter((item) => {

                    if ( item.type == 'cd_page' && !item.deleted ) {

                        let page = getItem(customizations.cdpages, item.id);

                        if ( page.deleted ) {

                            return false;
                        }
                    }

                    return true;
                });

                panel =
                    <PanelMenu
                        key="menu"
                        menuItems={available_items}
                        editing={history.menuItemLastAdded || false}
                        onMenuItemEdit={this.menuItemEdit}
                        onDeleteItem={this.menuItemDelete}
                        onSubmenuEdit={this.submenuEdit}
                        onReOrderMenu={this.reOrderMenu}
                        onItemSubmitForm={this.previewChanges}
                    />
                ;

                secondary_actions =
                    <SecondaryActions
                        key="menu"
                        title={l10n['panel_actions_title_menu']}
                        previousPanel="primary"
                        nextPanel="addMenuItems"
                        loadNextText={l10n['action_button_add_items']}
                        onLoadPanel={this.loadPanel}
                        disabled={this.state.saving || this.state.deleting}
                    />
                ;
                break;
            }
            case 'submenu': {

                let current_items   = customizations.submenu[this.state.submenuEdit] || [];
                let available_items = getAvailableItems(current_items);
                let menu_item       = getItem(customizations.menu, this.state.submenuEdit);
                let item_info       =
                        <div className="cd-editor-panel-menuinfo">
                            <span className={"cd-editor-panel-menuinfo-icon dashicons " +
                            (menu_item.icon || menu_item.original_icon)}></span>
                            <span className="cd-editor-panel-menuinfo-title">
                                    {menu_item.title || menu_item.original_title}
                                </span>
                        </div>
                    ;

                panel =
                    <PanelSubmenu
                        key="submenu"
                        itemInfo={item_info}
                        editing={history.SubmenuItemLastAdded || false}
                        onSubmenuItemEdit={this.submenuItemEdit}
                        submenuItems={available_items}
                        onDeleteItem={this.submenuItemDelete}
                        onReOrderSubmenu={this.reOrderSubmenu}
                        onItemSubmitForm={this.previewChanges}
                    />
                ;

                secondary_actions =
                    <SecondaryActions
                        key="submenu"
                        title={l10n['panel_actions_title_submenu']}
                        nextPanel="addSubmenuItems"
                        previousPanel="menu"
                        loadNextText={l10n['action_button_add_items']}
                        onLoadPanel={this.loadPanel}
                        disabled={this.state.saving || this.state.deleting}
                    />
                ;
                break;
            }
            case 'addMenuItems': {

                let current_items   = customizations.menu;
                let available_items = getDeletedItems(current_items);

                // Skip separators
                available_items = available_items.filter((item) => {

                    return item.type != 'separator';
                });

                // Add custom link
                available_items.push({
                    id: 'custom_link',
                    original_title: l10n['custom_link'],
                    type: 'custom_link',
                });

                // Add separator to bottom always
                available_items.push({
                    id: 'separator',
                    original_title: l10n['separator'],
                    type: 'separator',
                });

                // Add CD Pages
                let available_cd_pages = this.getAvailableCDPages();
                available_cd_pages.map((item) => {

                    available_items.push({
                        id: item.id,
                        original_title: item.title || item.original_title,
                        original_icon: item.icon || item.original_icon,
                        type: 'cd_page',
                    });
                });

                panel =
                    <PanelAddItems
                        key="addMenuItems"
                        availableItems={available_items}
                        onAddItem={this.menuItemAdd}
                    />
                ;

                secondary_actions =
                    <SecondaryActions
                        key="addMenuItems"
                        title={l10n['panel_actions_title_menu_add']}
                        previousPanel="menu"
                        onLoadPanel={this.loadPanel}
                        disabled={this.state.saving || this.state.deleting}
                    />
                ;
                break;
            }
            case 'addSubmenuItems': {
                let menu_item       = getItem(customizations.menu, this.state.submenuEdit);
                let item_info       =
                        <div className="cd-editor-panel-menuinfo">
                            <span className={"cd-editor-panel-menuinfo-icon dashicons " +
                            (menu_item.icon || menu_item.original_icon)}></span>
                            <span className="cd-editor-panel-menuinfo-title">
                                {menu_item.title || menu_item.original_title}
                            </span>
                        </div>
                    ;
                let current_items   = customizations.submenu[this.state.submenuEdit] || [];
                let available_items = getDeletedItems(current_items);

                // Add custom link
                available_items.push({
                    id: 'custom_link',
                    original_title: l10n['custom_link'],
                    type: 'custom_link',
                });

                // Add CD Pages
                let available_cd_pages = this.getAvailableCDPages();
                available_cd_pages.map((item) => {

                    available_items.push({
                        id: item.id,
                        original_title: item.title || item.original_title,
                        type: 'cd_page',
                    });
                });

                panel =
                    <PanelAddItems
                        key="addSubmenuItems"
                        itemInfo={item_info}
                        availableItems={available_items}
                        onAddItem={this.submenuItemAdd}
                    />
                ;

                secondary_actions =
                    <SecondaryActions
                        key="addSubmenuItems"
                        title={l10n['panel_actions_title_submenu_add']}
                        previousPanel="submenu"
                        onLoadPanel={this.loadPanel}
                        disabled={this.state.saving || this.state.deleting}
                    />
                ;
                break;
            }
            case 'dashboard': {

                let current_items   = customizations.dashboard;
                let available_items = getAvailableItems(current_items);

                panel =
                    <PanelDashboard
                        key="dashboard"
                        widgets={available_items}
                        onWidgetEdit={this.widgetEdit}
                        onDeleteWidget={this.widgetDelete}
                        onLoadPanel={this.loadPanel}
                        onItemSubmitForm={this.previewChanges}
                    />
                ;

                secondary_actions =
                    <SecondaryActions
                        key="dashboard"
                        title={l10n['panel_actions_title_dashboard']}
                        previousPanel="primary"
                        nextPanel="addWidgets"
                        loadNextText={l10n['action_button_add_items']}
                        onLoadPanel={this.loadPanel}
                        disabled={this.state.saving || this.state.deleting}
                    />
                ;
                break;
            }
            case 'addWidgets': {

                let current_items   = customizations.dashboard;
                let available_items = getDeletedItems(current_items);

                panel =
                    <PanelAddItems
                        key="addWidgets"
                        availableItems={available_items}
                        onAddItem={this.widgetAdd}
                    />
                ;

                secondary_actions =
                    <SecondaryActions
                        key="addWidgets"
                        title={l10n['panel_actions_title_dashboard_add']}
                        previousPanel="dashboard"
                        onLoadPanel={this.loadPanel}
                        disabled={this.state.saving || this.state.deleting}
                    />
                ;
                break;
            }

            case 'cdPages': {

                let current_items   = customizations.cdpages;
                let available_items = getAvailableItems(current_items);

                available_items.map((item) => {

                    if ( item.parent == 'toplevel' ) {

                        item.parent_label = l10n['toplevel'];

                    } else if ( item.parent !== false ) {

                        let parent = getItem(customizations.menu, item.parent);

                        item.parent_label = parent.title || parent.original_title;

                    } else {

                        item.parent_label = l10n['none'];
                    }

                    return item;
                });

                panel =
                    <PanelCDPages
                        key="cdPages"
                        pages={available_items}
                        onPageTabsEdit={this.cdPageTabsEdit}
                        onPageEdit={this.cdPageEdit}
                        onPageDelete={this.cdPageDelete}
                        onLoadPanel={this.loadPanel}
                        onItemSubmitForm={this.previewChanges}
                    />
                ;

                secondary_actions =
                    <SecondaryActions
                        key="cdPages"
                        title={l10n['panel_actions_title_cdpages']}
                        previousPanel="primary"
                        nextPanel="addCDPages"
                        loadNextText={l10n['action_button_add_items']}
                        onLoadPanel={this.loadPanel}
                        disabled={this.state.saving || this.state.deleting}
                    />
                ;

                break;
            }

            case 'addCDPages': {

                let current_items   = customizations.cdpages;
                let available_items = getDeletedItems(current_items);

                panel =
                    <PanelAddItems
                        key="addCDPages"
                        availableItems={available_items}
                        onAddItem={this.cdPageAdd}
                    />
                ;

                secondary_actions =
                    <SecondaryActions
                        key="addWidgets"
                        title={l10n['panel_actions_title_cdpages_add']}
                        previousPanel="cdPages"
                        onLoadPanel={this.loadPanel}
                        disabled={this.state.saving || this.state.deleting}
                    />
                ;

                break;
            }

            case 'loading': {

                panel =
                    <PanelLoading
                        key="loading"
                    />
                ;

                secondary_actions =
                    <SecondaryActions
                        key="loading"
                    />
                ;
                break;
            }

            case 'blank': {

                panel =
                    <PanelBlank
                        key="blank"
                    />
                ;

                secondary_actions =
                    <SecondaryActions
                        key="blank"
                    />
                ;
                break;
            }
        }

        if ( this.state.activePanel == 'confirmReset' || this.state.activePanel == 'deleting' ) {

            panel =
                <PanelBlank
                    key="blank"
                />
            ;
        }

        return (
            <div id="cd-editor">

                <div className="cd-editor-header">
                    <PrimaryActions
                        onSaveChanges={this.saveChanges}
                        onPreviewChanges={this.previewChanges}
                        onHideCustomizer={this.hideCustomizer}
                        onCloseCustomizer={this.closeCustomizer}
                        loadingPreview={this.state.loadingPreview}
                        saving={this.state.saving}
                        disabled={this.state.deleting || this.state.loading}
                        changes={this.state.changes}
                    />

                    <RoleSwitcher
                        role={this.props.role}
                        disabled={this.state.saving || this.state.deleting || this.state.loading}
                        onSwitchRole={this.switchRole}
                    />

                    <Message
                        text={this.state.message.text || ''}
                        type={this.state.message.type || 'default'}
                        noHide={this.state.message.noHide || false}
                        onHide={this.resetMessage}
                    />
                </div>

                <div className={'cd-editor-panels' +
                (this.state.saving || this.state.deleting ? ' cd-editor-panels-disabled' : '')}
                     onClick={this.handleEditorClick}>
                    <ReactCSSTransitionReplace
                        transitionName={"panel-" + this.state.panelDirection}
                        transitionEnterTimeout={300}
                        transitionLeaveTimeout={300}>
                        {panel}
                    </ReactCSSTransitionReplace>

                    {(this.state.saving || this.state.deleting) &&
                    <div className="cd-editor-panels-cover"/>}
                </div>

                <div className="cd-editor-footer">
                    <ReactCSSTransitionReplace
                        transitionName={"panel-" + this.state.panelDirection}
                        transitionEnterTimeout={300}
                        transitionLeaveTimeout={300}>
                        {secondary_actions}
                    </ReactCSSTransitionReplace>
                </div>

            </div>
        );
    }
}

export default Editor