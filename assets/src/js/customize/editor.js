import React from 'react';
import ReactCSSTransitionReplace from 'react-css-transition-replace';
import {arrayMove} from 'react-sortable-hoc';

import {
    getItem,
    deleteItem,
    modifyItem,
    getDeletedItems,
    getAvailableItems,
    getNewItems,
    getNewItemID,
    getItemIndex,
    ensureArray
} from './functions';

import {
    PanelPrimary,
    PanelConfirmReset,
    PanelAddItems,
    PanelBlank,
    PanelDashboard,
    PanelLoading,
    PanelMenu,
    PanelSubmenu
} from './panels';

import {SecondaryActions, SecondaryActionsPrimary} from './secondary-actions';
import PrimaryActions from './primary-actions';
import RoleSwitcher from './role-switcher';
import Message from './message';

const l10n          = ClientdashCustomize_Data.l10n || false;
const adminurl      = ClientdashCustomize_Data.adminurl || false;
const resturl       = ClientdashCustomize_Data.rest_url || false;
const cdresturl     = ClientdashCustomize_Data.cd_rest_url || false;
const api_nonce     = ClientdashCustomize_Data.api_nonce || false;
const customWidgets = ClientdashCustomize_Data.widgets || [];

/**
 * The Customize editor.
 *
 * @since 2.0.0
 */
class Editor extends React.Component {

    constructor(props) {

        super(props);

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
        this.showMessage       = this.showMessage.bind(this);
        this.setPanel          = this.setPanel.bind(this);
        this.setSecondaryActions = this.setSecondaryActions.bind(this);
        this.addPanel          = this.addPanel.bind(this);
        this.addSecondaryAction = this.addSecondaryAction.bind(this);

        this.state = {
            nextPanel: null,
            panelDirection: 'forward',
            activePanel: 'loading',
            activeSecondaryAction: 'loading',
            hidden: false,
            submenuEdit: null,
            loadingPreview: false,
            saving: false,
            deleting: false,
            loading: true,
            changes: false,
            message: {
                type: 'default',
                text: null
            },
            customizations: {},
            history: {},
            panels: {
                'loading': {
                    component: PanelLoading,
                    props: {
                        key: "loading",
                    }
                },
                'deleting': {
                    component: PanelLoading,
                    props: {
                        key: "deleting",
                    }
                },
                'blank': {
                    component: PanelBlank,
                    props: {
                        key: "blank",
                    }
                },
                'primary': {
                    component: PanelPrimary,
                    props: {
                        key: "primary",
                        loadPanel: this.loadPanel,
                        confirmReset: this.confirmReset,
                        butts: 'test',
                    }
                },
                'menu': {
                    component: PanelMenu,
                    props: {
                        key: "menu",
                        menuItems: [],
                        editing: false,
                        onMenuItemEdit: this.menuItemEdit,
                        onDeleteItem: this.menuItemDelete,
                        onSubmenuEdit: this.submenuEdit,
                        reOrderMenu: this.reOrderMenu,
                        onItemSubmitForm: this.previewChanges,
                    }
                },
                'addMenuItems': {
                    component: PanelAddItems,
                    props: {
                        key: 'addMenuItems',
                        availableItems: [],
                        onAddItem: this.menuItemAdd,
                    }
                },
                'submenu': {
                    component: PanelSubmenu,
                    props: {
                        key: "submenu",
                        itemInfo: null,
                        editing: false,
                        onSubmenuItemEdit: this.submenuItemEdit,
                        submenuItems: [],
                        onDeleteItem: this.submenuItemDelete,
                        reOrderSubmenu: this.reOrderSubmenu,
                        onItemSubmitForm: this.previewChanges,
                    },
                },
                'addSubmenuItems': {
                    component: PanelAddItems,
                    props: {
                        key: 'addSubmenuItems',
                        itemInfo: null,
                        availableItems: [],
                        onAddItem: this.submenuItemAdd,
                    }
                },
                'dashboard': {
                    component: PanelDashboard,
                    props: {
                        key: 'dashboard',
                        widgets: [],
                        editing: false,
                        onWidgetEdit: this.widgetEdit,
                        onDeleteWidget: this.widgetDelete,
                        onLoadPanel: this.loadPanel,
                        onItemSubmitForm: this.previewChanges,
                    },
                },
                'addWidgets': {
                    component: PanelAddItems,
                    props: {
                        key: "addWidgets",
                        availableItems: [],
                        onAddItem: this.widgetAdd,
                    },
                },
                'confirmReset': {
                    component: PanelConfirmReset,
                    props: {
                        key: "confirmReset",
                        resetRole: this.resetRole,
                        cancelReset: this.cancelReset,
                    },
                },
            },
            secondaryActions: {
                'loading': {
                    component: SecondaryActions,
                    props: {
                        key: "loading",
                    }
                },
                'deleting': {
                    component: SecondaryActions,
                    props: {
                        key: "deleting",
                    }
                },
                'blank': {
                    component: SecondaryActions,
                    props: {
                        key: 'blank',
                    }
                },
                'primary': {
                    component: SecondaryActionsPrimary,
                    props: {
                        key: "primary",
                        title: l10n['choose_something_to_customize'],
                        deleting: false,
                        disabled: false,
                    }
                },
                'menu': {
                    component: SecondaryActions,
                    props: {
                        key: "menu",
                        title: l10n['panel_actions_title_menu'],
                        previousPanel: "primary",
                        nextPanel: "addMenuItems",
                        loadNextText: l10n['action_button_add_items'],
                        loadPanel: this.loadPanel,
                        disabled: false,
                        nextPanelNotification: false,
                    }
                },
                'addMenuItems' : {
                    component: SecondaryActions,
                    props: {
                        key: 'addMenuItems',
                        title: l10n['panel_actions_title_menu_add'],
                        previousPanel: 'menu',
                        loadPanel: this.loadPanel,
                        //disabled={this.state.saving || this.state.deleting}
                    }
                },
                'submenu': {
                    component: SecondaryActions,
                    props: {
                        key: "submenu",
                        title: l10n['panel_actions_title_submenu'],
                        nextPanel: "addSubmenuItems",
                        previousPanel: "menu",
                        loadNextText: l10n['action_button_add_items'],
                        loadPanel: this.loadPanel,
                        disabled: false,
                        nextPanelNotification: false,
                    }
                },
                'addSubmenuItems': {
                    component: SecondaryActions,
                    props: {
                        key: 'addSubmenuItems',
                        title: l10n['panel_actions_title_submenu_add'],
                        previousPanel: 'submenu',
                        loadPanel: this.loadPanel,
                        //disabled={this.state.saving || this.state.deleting}
                    }
                },
                'dashboard': {
                    component: SecondaryActions,
                    props: {
                        key: 'dashboard',
                        title: l10n['panel_actions_title_dashboard'],
                        previousPanel: 'primary',
                        nextPanel: 'addWidgets',
                        loadNextText: l10n['action_button_add_items'],
                        loadPanel: this.loadPanel,
                        disabled: false,
                        nextPanelNotification: false,
                    },
                },
                'addWidgets': {
                    component: SecondaryActions,
                    props: {
                        key: "addWidgets",
                        title: l10n['panel_actions_title_dashboard_add'],
                        previousPanel: "dashboard",
                        loadPanel: this.loadPanel,
                        disabled: false,
                    },
                }
            },

        }

    }

    componentDidMount() {

        let api = this;

        // Confirm before leave
        window.onbeforeunload = function () {

            if ( api.state.changes ) {

                return l10n['leave_confirmation'];
            }
        };

        clientDashEvents.dispatch( 'addPanels', {
            addPanel: this.addPanel, // Use this method to add your Panel. Provide a Panel object similar to the State above
        } );

        clientDashEvents.dispatch( 'addSecondaryActions', {
            addSecondaryAction: this.addSecondaryAction, // Use this method to add your Secondary Actions. Provide a Secondary Actions object similar to the State above
        } );

    }

    loadPanel(panel_ID, direction ) {

        direction = direction || 'forward';

        this.setState({
            activePanel: panel_ID,
            activeSecondaryAction: panel_ID,
            panelDirection: direction,
            message: {
                //type: this.getState( 'message' ).type || 'default',
                type: 'default',
                text: ''
            }
        });
    }

    addPanel( panel ) {

        this.setState( ( prevState ) => {

            return {
                ...prevState,
                panels: [
                    ...prevState.panels,
                    panel,
                ]
            }

        } );

    }

    addSecondaryAction( secondaryAction ) {

        this.setState( ( prevState ) => {

            return {
                ...prevState,
                secondaryActions: [
                    ...prevState.secondaryActions,
                    secondaryAction,
                ]
            }

        } );

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

        fetch(cdresturl + 'customizations/' + this.props.role, {
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
                    text: l10n['saved_and_live']
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

        fetch(cdresturl + 'customizations/preview_' + this.props.role, {
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
                type: 'warning',
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
                type: 'warning',
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
                type: this.state.message.type || 'default',
                text: ''
            }
        });

        fetch(cdresturl + 'customizations/' + this.props.role, {
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
                prevState.activeSecondaryAction = 'loading';
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
            activeSecondaryAction: 'loading',
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
                activeSecondaryAction: 'loading',
                loading: true
            });

            fetch(cdresturl + 'customizations/preview_' + role, {
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

                    // Force some customizations to array
                    customizations.menu      = ensureArray(customizations.menu);
                    customizations.dashboard = ensureArray(customizations.dashboard);
                    //customizations.submenu = ensureArray(customizations.submenu);

                    prevState.customizations[role] = customizations;
                    prevState.history[role]        = {};

                    // Get Menu data

                    let current_items   = prevState.customizations[role].menu;
                    let available_items = getAvailableItems(current_items);
                    let new_items       = getNewItems(current_items);

                    prevState.activePanel = 'primary';
                    prevState.loading     = false;

                    prevState.panels['menu'].props.menuItems = available_items || [];
                    prevState.panels['menu'].props.editing = prevState.history.menuItemLastAdded || false;
                    prevState.panels['menu'].props.newItems = new_items || false;

                    // Get Add Menu Item Data

                    available_items = getDeletedItems(current_items);

                    // Skip separators
                    available_items = available_items.filter((item) => {
                        return item.type !== 'separator';
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

                    prevState.panels['addMenuItems'].props.availableItems = available_items;

                    // Get Submenu Data. This ends up being mainly defaults as everything gets properly set in this.submenuEdit()

                    current_items = prevState.customizations[role].submenu[ prevState.submenuEdit ] || [];
                    available_items = getAvailableItems( current_items );
                    let menu_item = getItem( prevState.customizations[role].menu, prevState.submenuEdit );

                    let item_info       =
                        <div className="cd-editor-panel-menuinfo">
                            <span className={"cd-editor-panel-menuinfo-icon dashicons " +
                            (menu_item.icon || menu_item.original_icon)}></span>
                            <span className="cd-editor-panel-menuinfo-title">
                                    {menu_item.title || menu_item.original_title}
                                </span>
                        </div>
                    ;

                    new_items = getNewItems( current_items );

                    prevState.panels['submenu'].props.itemInfo = item_info;
                    prevState.panels['submenu'].props.editing = prevState.history.submenuItemLastAdded || false;
                    prevState.panels['submenu'].props.submenuItems = available_items;

                    prevState.secondaryActions['submenu'].props.disabled = prevState.saving || prevState.deleting;
                    prevState.secondaryActions['submenu'].props.nextPanelNotification = new_items ? l10n['new_items'] : false;

                    // Get Widget Data

                    current_items   = prevState.customizations[role].dashboard;
                    available_items = getAvailableItems(current_items);
                    new_items       = getNewItems(current_items);

                    prevState.panels['dashboard'].props.widgets = current_items;
                    prevState.panels['dashboard'].props.editing = prevState.history.widgetItemLastAdded || false;
                    prevState.panels['dashboard'].props.newItems = new_items || false;

                    // Get Widgets for addWidget panel

                    let addWidgets = getDeletedItems( current_items );

                    if ( customWidgets ) {

                        customWidgets.map( (widget) => {
    
                            addWidgets.push( {
                                deleted: true,
                                new: false,
                                id: widget.id,
                                original_title: widget.label,
                                title: '',
                                settings: {},
                                type: widget.id,
                            } );
                        } );

                    }

                    prevState.panels['addWidgets'].props.availableItems = addWidgets;

                    prevState.activeSecondaryAction = 'primary';

                    return prevState;
                });

            }).catch(function (error) {

                console.log('error: ', error);
            });

        } else if ( this.state.activePanel === 'loading' ) {

            this.setState((prevState) => {

                prevState.activePanel = 'primary';
                prevState.loading     = false;
                prevState.activeSecondaryAction = 'primary';
            });
        }
    }

    dataChange(refreshDelay) {

        clearTimeout(this.refreshingTimeout);

        refreshDelay = refreshDelay === false ? 10 : 1200;

        this.refreshingTimeout = setTimeout(() => this.previewChanges(), refreshDelay);

        this.setState({
            changes: true
        });
    }

    resetMessage() {

        this.setState({
            message: {
                type: this.state.message.type || 'default',
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

                default:

                    prevState.customizations[role].menu = modifyItem(menu, item.id, {deleted: false, new: false});

                    // Move to beginning
                    prevState.customizations[role].menu = arrayMove(
                        menu,
                        getItemIndex(menu, item.id),
                        0
                    );

                    prevState.history[role].menuItemLastAdded = item.id;

                    prevState.panels.addMenuItems.props.availableItems = deleteItem(
                        prevState.panels.addMenuItems.props.availableItems,
                        item.id
                    );

            }

            prevState.panels.menu.props.menuItems = getAvailableItems( prevState.customizations[role].menu );

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
                    prevState.history[role].submenuItemLastAdded          = new_item_id;
                    this.state.panels.submenu.props.submenuItems = getAvailableItems( submenu );

                    break;

                default:

                    prevState.customizations[role].submenu[submenu_edit] = modifyItem(
                        prevState.customizations[role].submenu[submenu_edit],
                        item.id,
                        {deleted: false, new: false}
                    );

                    // Move to beginning
                    prevState.customizations[role].submenu[submenu_edit] = arrayMove(
                        prevState.customizations[role].submenu[submenu_edit],
                        getItemIndex(prevState.customizations[role].submenu[submenu_edit], item.id),
                        0
                    );

                    this.state.panels.submenu.props.submenuItems = getAvailableItems( prevState.customizations[role].submenu[submenu_edit] );
                    this.state.panels.addSubmenuItems.props.availableItems = deleteItem(
                        prevState.panels.addSubmenuItems.props.availableItems,
                        item.id
                    );

                    prevState.history[role].submenuItemLastAdded = item.id;
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

                    prevState.customizations[role].menu = deleteItem(prevState.customizations[role].menu, ID);

                    break;

                default:

                    prevState.customizations[role].menu = modifyItem(
                        prevState.customizations[role].menu,
                        ID,
                        {deleted: true, title: '', icon: ''}
                    );

                    prevState.panels.addMenuItems.props.availableItems.push( item );

            }

            prevState.panels.menu.props.menuItems = getAvailableItems( prevState.customizations[role].menu );

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

                    prevState.customizations[role].submenu[submenu_edit] =
                        deleteItem(submenu, ID);

                    break;

                default:

                    prevState.customizations[role].submenu[submenu_edit] = modifyItem(
                        submenu,
                        ID,
                        {deleted: true, title: ''}
                    );

                    prevState.panels.addSubmenuItems.props.availableItems.push( item );

            }

            prevState.panels.submenu.props.submenuItems = getAvailableItems( prevState.customizations[role].submenu[submenu_edit] );

            return prevState;
        });

        this.dataChange(false);
    }

    submenuEdit(ID) {

        let current_items = this.state.customizations[ this.props.role ].submenu[ ID ] || [];
        let available_items = getAvailableItems( current_items );
        let menu_item = getItem( this.state.customizations[ this.props.role ].menu, ID );

        let item_info       =
            <div className="cd-editor-panel-menuinfo">
                <span className={"cd-editor-panel-menuinfo-icon dashicons " +
                (menu_item.icon || menu_item.original_icon)}></span>
                <span className="cd-editor-panel-menuinfo-title">
                        {menu_item.title || menu_item.original_title}
                    </span>
            </div>
        ;

        let new_items = false;

        new_items = getNewItems( current_items );

        let submenuAddAvailableItems = [];

        submenuAddAvailableItems = getDeletedItems( current_items );

        // Add custom link
        submenuAddAvailableItems.push({
            id: 'custom_link',
            original_title: l10n['custom_link'],
            type: 'custom_link',
        });

        // We have to set our Panel and Secondary Action data in here on-change
        this.setState( ( prevState ) => {
            return { 
                ...prevState,
                submenuEdit: ID,
                panels: {
                    ...prevState.panels,
                    'submenu': {
                        ...prevState.panels.submenu,
                        props: {
                            ...prevState.panels.submenu.props,
                            submenuItems: available_items,
                            itemInfo: item_info,
                        }
                    },
                    'addSubmenuItems': {
                        ...prevState.panels.addSubmenuItems,
                        props: {
                            ...prevState.panels.addSubmenuItems.props,
                            availableItems: submenuAddAvailableItems,
                            itemInfo: item_info,
                        }
                    }
                },
                secondaryActions: {
                    ...prevState.secondaryActions,
                    'submenu': {
                        ...prevState.secondaryActions.submenu,
                        props: {
                            ...prevState.secondaryActions.submenu.props,
                            nextPanelNotification: new_items ? l10n['new_items'] : false,
                        }
                    },
                    'addSubmenuItems': {
                        ...prevState.secondaryActions.addSubmenuItems,
                        props: {
                            ...prevState.secondaryActions.addSubmenuItems.props,
                            disabled: this.state.saving || this.state.deleting,
                        }
                    }
                }
            }
        } );

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

    reOrderMenu(args) {

        let role = this.props.role;

        this.setState((prevState) => {

            let menu = prevState.customizations[role].menu;

            // Indices won't match because sorting doesn't count deleted items. Use sorted indices to get TRUE indicies
            // of the customized menu.
            let availableItems = getAvailableItems(menu);
            let oldItem        = availableItems[args.oldIndex];
            let newItem        = availableItems[args.newIndex];
            let oldIndex       = getItemIndex(menu, oldItem.id);
            let newIndex       = getItemIndex(menu, newItem.id);

            prevState.customizations[role].menu =
                arrayMove(prevState.customizations[role].menu, oldIndex, newIndex);

            return prevState;
        });

        this.dataChange(false);
    }

    reOrderSubmenu(args) {

        let submenu_edit = this.state.submenuEdit;
        let role         = this.props.role;

        this.setState((prevState) => {

            prevState.customizations[role].submenu[submenu_edit] =
                arrayMove(prevState.customizations[role].submenu[submenu_edit], args.oldIndex, args.newIndex);

            return prevState;
        });

        this.dataChange(false);
    }

    widgetAdd(widget) {

        this.setState((prevState) => {

            let role      = this.props.role;
            let dashboard = prevState.customizations[role].dashboard;

            switch ( widget.type ) {

                case 'default': // Core/Plugin added widget

                    prevState.customizations[role].dashboard = modifyItem(
                        prevState.customizations[role].dashboard,
                        widget.id,
                        {deleted: false, new: false}
                    );

                    prevState.panels.addWidgets.props.availableItems = deleteItem(
                        prevState.panels.addWidgets.props.availableItems,
                        widget.id
                    );

                    prevState.history[role].widgetItemLastAdded = widget.id;

                    break;

                default: // Custom added widget

                    let new_item_id = getNewItemID(dashboard, widget.id);

                    prevState.customizations[role].dashboard.unshift({
                        id: new_item_id,
                        original_title: widget.original_title,
                        type: widget.type,
                    });

                    prevState.history[role].widgetItemLastAdded = new_item_id;
            }

            prevState.panels.dashboard.props.widgets = getAvailableItems( prevState.customizations[role].dashboard );

            return prevState;
        });

        this.dataChange(false);

        this.loadPanel('dashboard', 'backward');
    }

    widgetDelete(ID) {

        this.setState((prevState) => {

            let role   = this.props.role;
            let widget = getItem(prevState.customizations[role].dashboard, ID);

            switch ( widget.type ) {

                case 'default': // Core/Plugin added widget

                    prevState.customizations[role].dashboard = modifyItem(
                        prevState.customizations[role].dashboard,
                        ID,
                        {deleted: true, title: '', settings: {}}
                    );

                    prevState.panels.addWidgets.props.availableItems.push( widget );

                    break;

                default: // Custom added widget

                    prevState.customizations[role].dashboard = deleteItem(prevState.customizations[role].dashboard, ID);
            }

            prevState.panels.dashboard.props.widgets = getAvailableItems( prevState.customizations[role].dashboard );

            return prevState;
        });

        this.dataChange(false);
    }

    widgetEdit(args) {

        this.setState((prevState) => {

            let role                 = this.props.role;
            let {id, setting, value} = args;
            let changes              = {};

            // Reserved vs generic settings
            switch ( setting ) {
                case 'title':

                    changes[setting] = value;
                    break;

                default:

                    let prevItem              = getItem(prevState.customizations[role].dashboard, id);
                    changes.settings          = prevItem.settings || {};
                    changes.settings[setting] = value;
                    break;
            }

            prevState.customizations[role].dashboard = modifyItem(
                prevState.customizations[role].dashboard,
                id,
                changes
            );

            return prevState;
        });

        this.dataChange();
    }

    showMessage(message) {

        this.setState({
            message: message
        });
    }

    setPanel( panel ) {

        this.setState( {
            panel: panel,
        } );

    }

    setSecondaryActions( secondaryActions ) {

        this.setState( {
            secondaryActions: secondaryActions,
        } );

    }

    render() {

        if ( typeof this.state.panels[ this.state.activePanel ] !== 'undefined' ) {

            const PanelName = this.state.panels[ this.state.activePanel ].component;

            var panel = <PanelName {...this.state.panels[ this.state.activePanel ].props} />;

        }
        else {
            console.error( 'The Panel, "' + this.state.activePanel +'", being accessed has not been added using the addPanels Event' );
        }

        if ( typeof this.state.secondaryActions[ this.state.activeSecondaryAction ] !== 'undefined' ) {

            const SecondaryActionsName = this.state.secondaryActions[ this.state.activeSecondaryAction ].component;

            var secondary_actions = <SecondaryActionsName {...this.state.secondaryActions[ this.state.activeSecondaryAction ].props} />;

        }
        else {
            console.error( 'The SecondaryActions, "' + this.state.activeSecondaryAction +'", being accessed has not been added using the addSecondaryActions Event' );
        }

        return (
            <div id="cd-editor">

                <div className="cd-editor-header">
                    <PrimaryActions
                        onSaveChanges={this.saveChanges}
                        onHideCustomizer={this.hideCustomizer}
                        onCloseCustomizer={this.closeCustomizer}
                        loadingPreview={this.state.loadingPreview}
                        saving={this.state.saving}
                        disabled={this.state.deleting || this.state.loading}
                        changes={this.state.changes}
                    />

                    <Message
                        text={this.state.message.text || ''}
                        type={this.state.message.type || 'default'}
                        noHide={this.state.message.noHide || false}
                        onHide={this.resetMessage}
                    />
                </div>

                <div className="cd-editor-sub-header">
                    <ReactCSSTransitionReplace
                        transitionName={"panel-" + this.state.panelDirection}
                        transitionEnterTimeout={300}
                        transitionLeaveTimeout={300}>
                        {secondary_actions}
                    </ReactCSSTransitionReplace>
                </div>

                <div className={'cd-editor-panels' +
                (this.state.saving || this.state.deleting ? ' cd-editor-panels-disabled' : '')}>
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
                    <RoleSwitcher
                        role={this.props.role}
                        disabled={this.state.saving || this.state.deleting || this.state.loading}
                        onSwitchRole={this.switchRole}
                    />
                </div>

            </div>
        );
    }
}

export default Editor