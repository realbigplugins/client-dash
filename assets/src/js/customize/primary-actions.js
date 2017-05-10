import React from 'react';

import ActionButton from './action-button';

const l10n = ClientdashCustomize_Data.l10n || false;

/**
 * Header of Customizer that contains the primary actions.
 *
 * Actions are: Hide Customizer, Close Customizer, Save Settings
 *
 * @since {{VERSION}}
 */
class PrimaryActions extends React.Component {

    constructor(props) {

        super(props);

        this.saveChanges     = this.saveChanges.bind(this);
        this.previewChanges  = this.previewChanges.bind(this);
        this.hideCustomizer  = this.hideCustomizer.bind(this);
        this.closeCustomizer = this.closeCustomizer.bind(this);
    }

    saveChanges() {

        if ( this.props.saving ) {

            return;
        }

        this.props.onSaveChanges();
    }

    previewChanges() {

        this.props.onPreviewChanges();
    }

    hideCustomizer() {

        this.props.onHideCustomizer();
    }

    closeCustomizer() {

        this.props.onCloseCustomizer();
    }

    render() {
        return (
            <div className="cd-editor-primary-actions">

                <ActionButton
                    text="Hide"
                    icon="chevron-circle-left"
                    disabled={this.props.saving || this.props.disabled}
                    onHandleClick={this.hideCustomizer}
                />
                <ActionButton
                    text="Close"
                    icon="times"
                    disabled={this.props.saving || this.props.disabled}
                    onHandleClick={this.closeCustomizer}
                />
                <ActionButton
                    text="Preview"
                    icon={this.props.loadingPreview ? "circle-o-notch fa-spin" : "refresh"}
                    disabled={this.props.saving || this.props.disabled}
                    onHandleClick={this.previewChanges}
                />
                <ActionButton
                    text={this.props.changes ? l10n['save'] : l10n['up_to_date']}
                    icon={this.props.saving ? "circle-o-notch fa-spin" : (this.props.changes ? "floppy-o" : "check")}
                    disabled={!this.props.changes || this.props.saving || this.props.disabled}
                    type="primary"
                    onHandleClick={this.saveChanges}
                />
            </div>
        )
    }
}

export default PrimaryActions