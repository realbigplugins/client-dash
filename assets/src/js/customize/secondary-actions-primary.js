import React from 'react';

const l10n = ClientdashCustomize_Data.l10n || false;

/**
 * Secondary actions for the Primary panel.
 *
 * @since 2.0.0
 */
class SecondaryActionsPrimary extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            confirming: false
        }

        this.resetRole    = this.resetRole.bind(this);
        this.cancelReset  = this.cancelReset.bind(this);
        this.confirmReset = this.confirmReset.bind(this);
    }

    resetRole() {

        if ( this.props.deleting ) {

            return;
        }

        this.props.onResetRole();

        this.setState({
            confirming: false
        });
    }

    confirmReset() {

        this.props.onConfirmReset();

        this.setState({
            confirming: true,
        });
    }

    cancelReset() {

        this.props.onCancelReset();

        this.setState({
            confirming: false
        });
    }

    render() {

        return (
            <div className="cd-editor-secondary-actions">
                {this.props.title &&
                <div className="cd-editor-panel-actions-title">
                    {this.props.title}
                </div>
                }
            </div>
        )
    }
}

export {
    SecondaryActionsPrimary
}