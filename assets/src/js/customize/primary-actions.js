import React from 'react';

import ActionButton from './action-button';

const l10n = ClientdashCustomize_Data.l10n || false;

/**
 * Header of Customizer that contains the primary actions.
 *
 * Actions are: Hide Customizer, Close Customizer, Save Settings
 *
 * @since 2.0.0
 */
class PrimaryActions extends React.Component {

    constructor(props) {

        super(props);

        this.saveChanges     = this.saveChanges.bind(this);
        this.hideCustomizer  = this.hideCustomizer.bind(this);
        this.closeCustomizer = this.closeCustomizer.bind(this);
    }

    saveChanges() {

        if ( this.props.saving ) {

            return;
        }

        this.props.onSaveChanges();
    }

    hideCustomizer() {

        this.props.onHideCustomizer();
    }

    closeCustomizer() {

        this.props.onCloseCustomizer();
    }

    render() {

        let saveText;

        if ( !this.props.changes ) {

            saveText = l10n['saved'];

        } else if ( this.props.saving ) {

            saveText = <span className="fa fa-circle-o-notch fa-spin"/>;

        } else {

            saveText = l10n['save'];
        }

        return (
            <div className="cd-editor-primary-actions">
                <ActionButton
                    title="Close"
                    icon="times"
                    size="large"
                    disabled={this.props.saving || this.props.disabled}
                    onHandleClick={this.closeCustomizer}
                />

                <ActionButton
                    text={saveText}
                    title={this.props.saving ? l10n['saving'] : saveText}
                    align="right"
                    disabled={!this.props.changes || this.props.saving || this.props.disabled}
                    type="primary"
                    onHandleClick={this.saveChanges}
                />
            </div>
        )
    }
}

export default PrimaryActions