import React from 'react';
import ReactDOM from 'react-dom';
import Promise from 'promise-polyfill';
import 'whatwg-fetch';

import './functions';
import Editor from './editor';
import Preview from './preview';

const l10n      = ClientdashCustomize_Data.l10n || false;
const roles     = ClientdashCustomize_Data.roles || false;
const adminurl  = ClientdashCustomize_Data.adminurl || false;
const domain    = ClientdashCustomize_Data.domain || false;
const dashicons = ClientdashCustomize_Data.dashicons || false;

/**
 * The main Customize component.
 *
 * @since {{VERSION}}
 */
class Customize extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            hidden: false,
            role: 'administrator',
            loadedRoles: ['administrator'],
            saveRole: true,
            previewLoading: true,
        }

        this.hideCustomizer = this.hideCustomizer.bind(this);
        this.showCustomizer = this.showCustomizer.bind(this);
        this.switchRole     = this.switchRole.bind(this);
        this.resetRole      = this.resetRole.bind(this);
        this.loadData       = this.loadData.bind(this);
        this.refreshPreview = this.refreshPreview.bind(this);
        this.showMessage    = this.showMessage.bind(this);
    }

    shouldComponentUpdate(nextProps, nextState) {

        // Don't reload if setting saveRole to false
        if ( this.state.saveRole === true && nextState.saveRole === false ) {

            return false;
        }

        return true;
    }

    hideCustomizer() {

        this.setState({
            hidden: true,
        });
    }

    showCustomizer() {

        this.setState({
            hidden: false,
        });
    }

    resetRole(role) {

        this.setState((prevState) => {

            prevState.saveRole       = true;
            prevState.previewLoading = true;
            prevState.role           = role;
            prevState.saveRole       = true;

            return prevState;
        });

        this.refs.preview.load(adminurl + '?cd_customizing=1&cd_save_role=1&role=' + role);
    }

    switchRole(role) {

        if ( this.state.loadedRoles.indexOf(role) === -1 ) {

            this.setState((prevState) => {

                prevState.role     = role;
                prevState.saveRole = true;
                prevState.loadedRoles.push(role);

                return prevState;
            });

        } else {

            this.setState({
                role: role
            });
        }
    }

    loadData() {

        this.setState({
            previewLoading: false,
            saveRole: this.state.saveRole || false,
        });

        this.refs.editor.loadRole();
    }

    refreshPreview() {

        this.setState({
            previewLoading: true,
        });

        this.refs.preview.refresh();
    }

    showMessage(message) {

        this.refs.editor.showMessage(message);
    }

    render() {

        return (
            <div className={"cd-customize-container " + (this.state.hidden ? "hidden" : "")}>
                {this.state.hidden &&
                <button type="button" className="cd-customize-show"
                        title={l10n['show_controls']} onClick={this.showCustomizer}>
                    <span className="cd-customize-show-icon fa fa-chevron-circle-right"/>
                </button>
                }

                <Editor
                    onHideCustomizer={this.hideCustomizer}
                    onSwitchRole={this.switchRole}
                    onResetRole={this.resetRole}
                    refreshPreview={this.refreshPreview}
                    role={this.state.role}
                    ref="editor"
                />

                <Preview
                    role={this.state.role}
                    onLoad={this.loadData}
                    saveRole={this.state.saveRole}
                    onShowMessage={this.showMessage}
                    ref="preview"
                />

                {this.state.previewLoading &&
                <div id="cd-editor-preview-cover">
                    <span className="cd-editor-preview-cover-icon fa fa-circle-o-notch fa-spin"/>
                </div>}
            </div>
        )
    }
}

// Renders the Customizer
ReactDOM.render(
    <Customize />,
    document.getElementById('clientdash-customize')
);