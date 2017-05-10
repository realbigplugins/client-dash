import React from 'react';

/**
 * Action button that appears in header and footer of Customizer.
 *
 * @since {{VERSION}}
 *
 * @prop (string) text The button text.
 * @prop (string) icon The button icon.
 * @prop (string) align Alignment.
 */
class ActionButton extends React.Component {

    constructor(props) {

        super(props);

        this.handleClick = this.handleClick.bind(this);
    }

    handleClick() {

        if (!this.props.disabled) {

            this.props.onHandleClick();
        }
    }

    render() {

        var type_class = 'default';

        switch (this.props.type) {
            case 'primary':

                type_class = 'primary';
                break;

            case 'delete':

                type_class = 'delete';
                break;
        }

        var classes = [
            'cd-editor-action-button',
            'cd-editor-action-button-' + type_class
        ];

        if (this.props.align) {

            classes.push(this.props.align);
        }

        if (this.props.disabled) {

            classes.push('cd-editor-action-button-disabled');
        }

        return (
            <button type="button" title={this.props.text} aria-label={this.props.text} className={classes.join(' ')}
                    onClick={this.handleClick}>

                {this.props.icon &&
                <span className={"cd-editor-action-button-icon fa fa-" + this.props.icon}></span>
                }
            </button>
        )
    }
}

export default ActionButton