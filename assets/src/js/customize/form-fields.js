import React from 'react';

/**
 * Text input.
 *
 * @since 2.0.0
 *
 * @prop (string) name Input name.
 * @prop (string) label Input label.
 * @prop (string) value Input value.
 */
class InputText extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            value: ''
        };

        this.handleChange = this.handleChange.bind(this);
    }

    handleChange(event) {

        this.setState({
            value: event.target.value
        });

        this.props.onHandleChange(this.props.name, event.target.value);
    }

    render() {
        return (
            <div className="cd-editor-input cd-editor-input-text">
                <label>
                    {this.props.label}
                    <input
                        type="text"
                        name={this.props.name}
                        defaultValue={this.props.value}
                        placeholder={this.props.placeholder}
                        onChange={this.handleChange}
                    />
                </label>
            </div>
        )
    }
}

/**
 * Textarea input.
 *
 * @since 2.0.0
 *
 * @prop (string) name Input name.
 * @prop (string) label Input label.
 * @prop (string) value Input value.
 */
class InputTextArea extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            value: ''
        };

        this.handleChange = this.handleChange.bind(this);
    }

    handleChange(event) {

        this.setState({
            value: event.target.value
        });

        this.props.onHandleChange(this.props.name, event.target.value);
    }

    render() {
        return (
            <div className="cd-editor-input cd-editor-input-text">
                <label>
                    {this.props.label}
                    <textarea
                        name={this.props.name}
                        defaultValue={this.props.value}
                        placeholder={this.props.placeholder}
                        rows="6"
                        onChange={this.handleChange}
                    />
                </label>
            </div>
        )
    }
}

/**
 * Select box.
 *
 * @since 2.0.0
 *
 * @prop (array) options The options to show in value => text format.
 * @prop (string) selected The option value which is currently selected.
 * @prop (bool) multi If the select allows multiple selections or not.
 */
class InputSelect extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            value: ''
        };

        this.handleChange = this.handleChange.bind(this);
    }

    handleChange(event) {

        this.setState({
            value: event.target.value
        });

        this.props.onHandleChange(this.props.name, event.target.value);
    }

    render() {

        var options = [];

        this.props.options.map((option) =>
            options.push(
                <SelectOption key={option.value} value={option.value} text={option.text}/>
            )
        );

        return (
            <div className="cd-editor-input cd-editor-input-select">
                {this.props.before}

                <label>
                    {this.props.label}
                    <select
                        name={this.props.name}
                        value={this.props.value}
                        multiple={this.props.multi}
                        onChange={this.handleChange}
                        disabled={this.props.disabled}>
                        {options}
                    </select>
                </label>

                {this.props.after}
            </div>
        )
    }
}

/**
 * Select box option.
 *
 * @since 2.0.0
 *
 * @prop (string) value The option value.
 * @prop (string) text The option text.
 * @prop (bool) selected If the option is selected or not.
 */
class SelectOption extends React.Component {
    render() {
        return (
            <option value={this.props.value}>
                {this.props.text}
            </option>
        )
    }
}

/**
 * Gets an input.
 *
 * @since 2.0.0
 *
 * @param {string} field Type of input field.
 * @param {[]} args Field arguments.
 * @returns {XML}
 */
const getInput = (field, args) => {

    switch ( field ) {
        case 'text':
            return <InputText key={args.name} {...args} />
            break;

        case 'textarea':
            return <InputTextArea key={args.name} {...args} />
            break;

        case 'select':
            return <InputSelect key={args.name} {...args} />
            break;

        default:
            return `Invalid field type: ${field}`;
    }
}

export {
    getInput
}