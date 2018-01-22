import React from 'react';

/**
 * Action button that appears in header and footer of Customizer.
 *
 * @since 2.0.0
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

        if (this.props.size) {

            classes.push(this.props.size);
        }

        if (this.props.disabled) {

            classes.push('cd-editor-action-button-disabled');
        }

        return (
            <button type="button" title={this.props.title || this.props.text} aria-label={this.title || this.props.text}
                    className={classes.join(' ')}
                    onClick={this.handleClick}>
                {this.props.icon &&
                <span className={`fa fa-${this.props.icon}`} />
                }
                {this.props.text || ''}
            </button>
        )
    }
}

export default ActionButton