import React from 'react';

import {SortableContainer, SortableElement} from 'react-sortable-hoc';
import {getInput} from './form-fields';
import {getItem} from './functions';
import {DashiconsSelector} from './dashicons-selector';

const l10n          = ClientdashCustomize_Data.l10n || false;
const customWidgets = ClientdashCustomize_Data.widgets || {};

/**
 * Line item wrapper.
 *
 * @since {{VERSION}}
 */
class LineItem extends React.Component {
    render() {
        return (
            <li className="cd-editor-lineitem-li" data-id={this.props.id}>
                {this.props.children}
            </li>
        )
    }
}

/**
 * Generic line item.
 *
 * @since {{VERSION}}
 *
 * @prop (string) title Item title.
 * @prop (string) icon The item icon class (complete).
 * @prop (array) formInputs Inputs to send to the form, if any.
 */
class LineItemContent extends React.Component {

    constructor(props) {

        super(props);

        this.handleToggleEdit = this.handleToggleEdit.bind(this);
        this.handleAddItem    = this.handleAddItem.bind(this);
        this.handleDelete     = this.handleDelete.bind(this);
    }

    handleToggleEdit() {

        this.props.toggleEdit();
    }

    handleAddItem() {

        this.props.addItem();
    }

    handleDelete() {

        this.props.deleteItem();
    }

    render() {
        return (
            <div id={"cd-editor-lineitem-" + this.props.id}
                 className={"cd-editor-lineitem " + (this.props.classes ? this.props.classes : '')}>
                <div className="cd-editor-lineitem-block">
                    <div className="cd-editor-lineitem-title">
                        {this.props.icon &&
                        <span className={"cd-editor-lineitem-icon dashicons " + this.props.icon}></span>
                        }
                        {this.props.title}
                    </div>

                    {this.props.new &&
                    <span className="cd-editor-tip cd-editor-tip-right">{l10n['new']}</span>
                    }

                    {this.props.deleteItem &&
                    <button
                        type="button" title={l10n['delete']}
                        className={"cd-editor-lineitem-action"}
                        onClick={this.handleDelete}
                    >
                        <span className="cd-editor-lineitem-action-icon fa fa-trash"/>
                    </button>
                    }

                    {this.props.addItem &&
                    <button
                        type="button" title={l10n['add']}
                        className={"cd-editor-lineitem-action"}
                        onClick={this.handleAddItem}
                    >
                        <span className="cd-editor-lineitem-action-icon fa fa-plus"/>
                    </button>
                    }

                    {this.props.toggleEdit &&
                    <button
                        type="button" title={l10n['edit']}
                        className={"cd-editor-lineitem-action"}
                        onClick={this.handleToggleEdit}
                    >
                            <span className={"cd-editor-lineitem-action-icon fa fa-" +
                            (this.props.editing ? "chevron-up" : "chevron-down")}/>
                    </button>
                    }
                </div>

                {this.props.form && this.props.form}
            </div>
        )
    }
}

/**
 * Action buttons for line items.
 *
 * @since {{VERSION}}
 */
class LineItemAction extends React.Component {

    constructor(props) {

        super(props);

        this.handleClick = this.handleClick.bind(this);
    }

    handleClick(e) {

        this.props.onHandleClick();
    }

    render() {
        return (
            <button
                type="button" title={this.props.text}
                className={"cd-editor-lineitem-action " + (this.props.classes ? this.props.classes : "")}
                onClick={this.handleClick}
            >
                <span className={"cd-editor-lineitem-action-icon fa fa-" + this.props.icon}/>
            </button>
        )
    }
}

/**
 * The edit form for a line item.
 *
 * @since {{VERSION}}
 */
class LineItemForm extends React.Component {

    constructor(props) {

        super(props);

        this.handleSubmit      = this.handleSubmit.bind(this);
        this.handleDelete      = this.handleDelete.bind(this);
        this.handleSubmenuEdit = this.handleSubmenuEdit.bind(this);
    }

    handleSubmit(event) {

        event.preventDefault();

        this.props.onSubmit(event);
    }

    handleDelete(event) {

        event.preventDefault();

        this.props.onDelete();
    }

    handleSubmenuEdit(event) {

        event.preventDefault();

        this.props.onSubmenuEdit();
    }

    render() {
        return (
            <form className="cd-editor-lineitem-form" onSubmit={this.handleSubmit}>
                {this.props.children}

                <div className="cd-editor-lineitem-form-actions">
                    {this.props.onDelete &&
                    <button onClick={this.handleDelete} type="button"
                            className="cd-editor-lineitem-form-action cd-editor-lineitem-form-action-delete">
                        {l10n['delete']}
                    </button>
                    }
                    {this.props.onSubmenuEdit &&
                    <button onClick={this.handleSubmenuEdit} type="button"
                            className="cd-editor-lineitem-form-action cd-editor-lineitem-form-submenu">
                        {l10n['submenu']}
                    </button>
                    }
                </div>
            </form>
        )
    }
}

/**
 * Sortable line item wrapper.
 *
 * @since {{VERSION}}
 */
const SortableLineItem = SortableElement(({item}) => {
    return (
        <li className="cd-editor-lineitem-li" data-id={item.key}>
            {item}
        </li>
    );
})

/**
 * Line items container.
 *
 * @since {{VERSION}}
 */
class LineItems extends React.Component {
    render() {
        return (
            <ul className="cd-editor-lineitems">
                {this.props.items.map((item, index) =>
                    <LineItem key={`item-${index}`} id={item.props.id}>
                        {item}
                    </LineItem>
                )}
            </ul>
        )
    }
}
;

/**
 * Sortable line items container.
 *
 * @since {{VERSION}}
 */
const SortableLineItems = SortableContainer(({items}) => {
    return (
        <ul className="cd-editor-lineitems sortable">
            {items.map((item, index) =>
                <SortableLineItem
                    key={`item-${index}`}
                    index={index}
                    item={item}
                />
            )}
        </ul>
    );
});

/**
 * Line item for editing a menu item.
 *
 * @since {{VERSION}}
 */
class MenuItemEdit extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            editing: this.props.editing || false,
        }

        this.toggleEdit  = this.toggleEdit.bind(this);
        this.titleChange = this.titleChange.bind(this);
        this.iconChange  = this.iconChange.bind(this);
        this.deleteItem  = this.deleteItem.bind(this);
        this.submenuEdit = this.submenuEdit.bind(this);
        this.submitForm  = this.submitForm.bind(this);
    }

    toggleEdit() {

        this.setState((prevState) => ({
            editing: !prevState.editing
        }));
    }

    titleChange(value) {

        this.props.onMenuItemEdit({
            id: this.props.id,
            title: value,
        });
    }

    iconChange(value) {

        this.props.onMenuItemEdit({
            id: this.props.id,
            icon: value,
        });
    }

    deleteItem() {

        this.props.onDeleteItem(this.props.id);
    }

    submenuEdit() {

        this.props.onSubmenuEdit(this.props.id);
    }

    submitForm(event) {

        this.props.onItemFormSubmit();
    }

    render() {

        let actions = [];


        const form =
                  <LineItemForm
                      onSubmit={this.submitForm}
                      onDelete={this.deleteItem}
                      onSubmenuEdit={this.submenuEdit}
                  >
                      <InputText
                          label={l10n['title']}
                          value={this.props.title}
                          placeholder={this.props.original_title}
                          onHandleChange={this.titleChange}
                      />

                      <p className="cd-editor-lineitem-form-subfield cd-editor-lineitem-form-subtext">
                          {l10n['original_title'] + " "}<strong>{this.props.original_title}</strong>
                      </p>

                      <DashiconsSelector
                          value={this.props.icon}
                          placeholder={this.props.original_icon}
                          onSelectDashicon={this.iconChange}
                      />

                      <p className="cd-editor-lineitem-form-subfield cd-editor-lineitem-form-subtext">
                          {l10n['original_icon'] + " "}<span className={"dashicons " + this.props.original_icon}/>
                      </p>

                  </LineItemForm>
        ;

        return (
            <LineItemContent
                key={this.props.id}
                id={this.props.id}
                title={this.props.title || this.props.original_title}
                icon={this.props.icon || this.props.original_icon}
                form={this.state.editing ? form : false}
                editing={this.state.editing}
                toggleEdit={this.props.type !== 'clientdash' ? this.toggleEdit : false}
            />
        )
    }
}

/**
 * Line item for a separator.
 *
 * @since {{VERSION}}
 */
class MenuItemSeparator extends React.Component {

    constructor(props) {

        super(props);

        this.deleteItem = this.deleteItem.bind(this);
    }

    deleteItem() {

        this.props.onDeleteItem(this.props.id);
    }

    render() {

        return (
            <LineItemContent
                key={this.props.id}
                id={this.props.id}
                title={l10n['separator']}
                classes="cd-editor-menuitem-separator"
                deleteItem={this.deleteItem}
            />
        )
    }
}

/**
 * Line item for editing a menu item custom link.
 *
 * @since {{VERSION}}
 */
class MenuItemCustomLink extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            editing: this.props.editing || false,
        }

        this.toggleEdit  = this.toggleEdit.bind(this);
        this.titleChange = this.titleChange.bind(this);
        this.linkChange  = this.linkChange.bind(this);
        this.iconChange  = this.iconChange.bind(this);
        this.deleteItem  = this.deleteItem.bind(this);
        this.submenuEdit = this.submenuEdit.bind(this);
        this.submitForm  = this.submitForm.bind(this);
    }

    toggleEdit() {

        this.setState((prevState) => ({
            editing: !prevState.editing
        }));
    }

    titleChange(value) {

        this.props.onMenuItemEdit({
            id: this.props.id,
            title: value,
        });
    }

    linkChange(value) {

        this.props.onMenuItemEdit({
            id: this.props.id,
            link: value,
        });
    }

    iconChange(value) {

        this.props.onMenuItemEdit({
            id: this.props.id,
            icon: value,
        });
    }

    deleteItem() {

        this.props.onDeleteItem(this.props.id);
    }

    submenuEdit() {

        this.props.onSubmenuEdit(this.props.id);
    }

    submitForm(event) {

        this.props.onItemFormSubmit();
    }

    render() {

        let actions = [];

        const form =
                  <LineItemForm
                      onSubmit={this.submitForm}
                      onDelete={this.deleteItem}
                  >
                      <InputText
                          label={l10n['title']}
                          value={this.props.title}
                          placeholder={this.props.original_title}
                          onHandleChange={this.titleChange}
                      />

                      <p className="cd-editor-lineitem-form-subfield cd-editor-lineitem-form-subtext">
                          {l10n['original_title'] + " "}<strong>{this.props.original_title}</strong>
                      </p>

                      <InputText
                          label={l10n['link']}
                          value={this.props.link}
                          placeholder="http://"
                          onHandleChange={this.linkChange}
                      />

                      <DashiconsSelector
                          value={this.props.icon}
                          placeholder={this.props.original_icon}
                          onSelectDashicon={this.iconChange}
                      />

                      <p className="cd-editor-lineitem-form-subfield cd-editor-lineitem-form-subtext">
                          {l10n['original_icon'] + " "}<span className={"dashicons " + this.props.original_icon}/>
                      </p>

                  </LineItemForm>
        ;

        return (
            <LineItemContent
                key={this.props.id}
                id={this.props.id}
                title={this.props.title || this.props.original_title}
                icon={this.props.icon || this.props.original_icon}
                form={this.state.editing ? form : false}
                editing={this.state.editing}
                toggleEdit={this.toggleEdit}
            />
        )
    }
}

/**
 * Line item for editing a sub menu item.
 *
 * @since {{VERSION}}
 */
class SubmenuItemEdit extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            editing: this.props.editing || false,
            title: this.props.title,
            icon: this.props.icon
        }

        this.toggleEdit  = this.toggleEdit.bind(this);
        this.titleChange = this.titleChange.bind(this);
        this.deleteItem  = this.deleteItem.bind(this);
        this.submitForm  = this.submitForm.bind(this);
    }

    toggleEdit() {

        this.setState((prevState) => ({
            editing: !prevState.editing,
        }));
    }

    titleChange(value) {

        this.props.onSubmenuItemEdit({
            id: this.props.id,
            title: value,
            original_title: this.props.original_title,
        });
    }

    deleteItem() {

        this.props.onDeleteItem(this.props.id);
    }

    submitForm(event) {

        this.props.onItemFormSubmit();
    }

    render() {

        const after_title =
                  <span className="cd-editor-lineitem-form-subtext">
                    {l10n['original_title'] + " "}<strong>{this.props.original_title}</strong>
                </span>
        ;

        const form =
                  <LineItemForm
                      onSubmit={this.submitForm}
                      onDelete={this.deleteItem}
                  >
                      <InputText
                          label={l10n['title']}
                          value={this.props.title}
                          placeholder={this.props.original_title}
                          onHandleChange={this.titleChange}
                      />

                      <p className="cd-editor-lineitem-form-subfield cd-editor-lineitem-form-subtext">
                          {l10n['original_title'] + " "}<strong>{this.props.original_title}</strong>
                      </p>
                  </LineItemForm>
        ;


        return (
            <LineItemContent
                key={this.props.id}
                id={this.props.id}
                title={this.props.title || this.props.original_title}
                form={this.state.editing ? form : false}
                editing={this.state.editing}
                toggleEdit={this.toggleEdit}
            />
        )
    }
}

/**
 * Line item for editing a submenu item custom link.
 *
 * @since {{VERSION}}
 */
class SubmenuItemCustomLink extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            editing: this.props.editing || false,
        }

        this.toggleEdit  = this.toggleEdit.bind(this);
        this.titleChange = this.titleChange.bind(this);
        this.linkChange  = this.linkChange.bind(this);
        this.deleteItem  = this.deleteItem.bind(this);
        this.submitForm  = this.submitForm.bind(this);
    }

    toggleEdit() {

        this.setState((prevState) => ({
            editing: !prevState.editing
        }));
    }

    titleChange(value) {

        this.props.onSubmenuItemEdit({
            id: this.props.id,
            title: value,
            link: this.props.link,
            original_title: this.props.original_title,
            type: 'custom_link',
        });
    }

    linkChange(value) {

        this.props.onSubmenuItemEdit({
            id: this.props.id,
            link: value,
            title: this.props.title,
            original_title: this.props.original_title,
            type: 'custom_link',
        });
    }

    deleteItem() {

        this.props.onDeleteItem(this.props.id);
    }

    submitForm(event) {

        this.props.onItemFormSubmit();
    }

    render() {

        const form =
                  <LineItemForm
                      onSubmit={this.submitForm}
                      onDelete={this.deleteItem}
                  >
                      <InputText
                          label={l10n['title']}
                          value={this.props.title}
                          placeholder={this.props.original_title}
                          onHandleChange={this.titleChange}
                      />

                      <p className="cd-editor-lineitem-form-subfield cd-editor-lineitem-form-subtext">
                          {l10n['original_title'] + " "}<strong>{this.props.original_title}</strong>
                      </p>

                      <InputText
                          label={l10n['link']}
                          value={this.props.link}
                          placeholder="http://"
                          onHandleChange={this.linkChange}
                      />
                  </LineItemForm>
        ;

        return (
            <LineItemContent
                key={this.props.id}
                id={this.props.id}
                title={this.props.title || this.props.original_title}
                form={this.state.editing ? form : false}
                editing={this.state.editing}
                toggleEdit={this.toggleEdit}
            />
        )
    }
}

/**
 * Line item for adding an item.
 *
 * @since {{VERSION}}
 */
class ItemAdd extends React.Component {

    constructor(props) {

        super(props);

        this.addItem = this.addItem.bind(this);
    }

    addItem() {

        // Note the "title" and "original_title" to bring to compatibility with the MenuPanel
        this.props.onAddItem({
            id: this.props.id,
            title: '',
            original_title: this.props.title,
            icon: '',
            original_icon: this.props.icon,
            type: this.props.type,
        });
    }

    render() {

        let classes = '';

        if ( this.props.id === 'separator' ) {

            classes += ' separator';
        }

        if ( this.props.new ) {

            classes += ' new';
        }

        return (
            <LineItemContent
                key={this.props.id}
                id={this.props.id}
                title={this.props.title}
                icon={this.props.icon}
                classes={classes}
                new={this.props.new || false}
                addItem={this.addItem}
            />
        )
    }
}

/**
 * Line item for editing a widget.
 *
 * @since {{VERSION}}
 */
class WidgetEdit extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            editing: this.props.editing || false,
        }

        this.toggleEdit    = this.toggleEdit.bind(this);
        this.settingChange = this.settingChange.bind(this);
        this.widgetDelete  = this.widgetDelete.bind(this);
        this.submitForm    = this.submitForm.bind(this);
    }

    toggleEdit() {

        this.setState((prevState) => ({
            editing: !prevState.editing
        }));
    }

    settingChange(setting, value) {

        this.props.onWidgetEdit({
            id: this.props.id,
            setting,
            value,
        })
    }

    widgetDelete() {

        this.props.onWidgetDelete(this.props.id);
    }

    submitForm(event) {

        this.props.onItemFormSubmit();
    }

    render() {

        const customWidget   = getItem(customWidgets, this.props.id);
        const widgetSettings = customWidget ? customWidget.settings : [];

        const form =
                  <LineItemForm
                      onSubmit={this.submitForm}
                      onDelete={this.widgetDelete}
                  >

                      {getInput('text', {
                          label: l10n['title'],
                          name: 'title',
                          value: this.props.title,
                          placeholder: this.props.original_title,
                          onHandleChange: this.settingChange,
                      })}

                      <p className="cd-editor-lineitem-form-subfield cd-editor-lineitem-form-subtext">
                          {l10n['original_title'] + " "}<strong>{this.props.original_title}</strong>
                      </p>

                      {widgetSettings.map((setting) => {

                          let {type, ...args} = setting;

                          args.onHandleChange = this.settingChange;
                          args.value          = this.props.settings[args.name] || '';

                          return getInput(type, args);
                      })}

                  </LineItemForm>
        ;

        return (
            <LineItemContent
                key={this.props.id}
                id={this.props.id}
                title={this.props.title || this.props.original_title}
                form={this.state.editing ? form : false}
                toggleEdit={this.toggleEdit}
            />
        )
    }
}

export {
    LineItem,
    LineItemContent,
    LineItemForm,
    LineItemAction,
    LineItems,
    SortableLineItem,
    SortableLineItems,
    ItemAdd,
    MenuItemEdit,
    MenuItemSeparator,
    MenuItemCustomLink,
    SubmenuItemEdit,
    SubmenuItemCustomLink,
    WidgetEdit,
}