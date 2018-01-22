import React from 'react';

const l10n = ClientdashCustomize_Data.l10n || false;

/**
 * Shows a message to the user.
 *
 * @since 2.0.0
 */
class Message extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            visible: true,
        };

        this.hide = this.hide.bind(this);
    }

    componentWillReceiveProps(nextProps) {

        clearTimeout(this.hideFinish);
        clearTimeout(this.hiding);

        if ( nextProps.text ) {

            this.setState({
                visible: true
            });
        }
    }

    hide() {

        this.setState({
            visible: false
        });

        // Don't signal parent until animation finishes. This must match animation transition time in CSS.
        this.hideFinish = setTimeout(() => this.props.onHide(), 300);
    }

    render() {

        if ( this.state.visible && !this.props.noHide ) {

            this.hiding = setTimeout(() => this.hide(), 4000);
        }

        let classes = [
            'cd-editor-message',
            'cd-editor-message-' + this.props.type
        ]

        if ( this.props.noHide) {

            classes.push('cd-editor-message-inflow');
        }

        if ( this.props.text && this.state.visible ) {

            classes.push('cd-editor-message-visible');
        }

        return (
            <div className={classes.join(' ')}>
                {this.props.text}

                {!this.props.noHide &&
                <span className="cd-editor-message-close dashicons dashicons-no"
                      onClick={this.hide}
                      title={l10n['close']}></span>}
            </div>
        )
    }
}

export default Message