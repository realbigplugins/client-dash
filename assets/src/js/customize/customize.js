import React from 'react';
import ReactDOM from 'react-dom';
import 'whatwg-fetch';

import './functions';
import Editor from './editor';
import Preview from './preview';
import Tutorial from './tutorial';
import LoadingIcon from "./loading-icon";

const l10n         = ClientdashCustomize_Data.l10n || false;
const roles        = ClientdashCustomize_Data.roles || false;
const adminurl     = ClientdashCustomize_Data.adminurl || false;
const domain       = ClientdashCustomize_Data.domain || false;
const dashicons    = ClientdashCustomize_Data.dashicons || false;
const loadTutorial = ClientdashCustomize_Data.load_tutorial || false;

/**
 * The main Customize component.
 *
 * @since 2.0.0
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
            loadTutorial: loadTutorial,
        }

        this.handleEditorHideClick = this.handleEditorHideClick.bind(this);
        this.hideEditor            = this.hideEditor.bind(this);
        this.showEditor            = this.showEditor.bind(this);
        this.switchRole            = this.switchRole.bind(this);
        this.resetRole             = this.resetRole.bind(this);
        this.loadData              = this.loadData.bind(this);
        this.refreshPreview        = this.refreshPreview.bind(this);
        this.showMessage           = this.showMessage.bind(this);
        this.closeTutorial         = this.closeTutorial.bind(this);
        this.tutorialChangePanel   = this.tutorialChangePanel.bind(this);
    }

    shouldComponentUpdate(nextProps, nextState) {

        // Don't reload if setting saveRole to false
        if ( this.state.saveRole === true && nextState.saveRole === false ) {

            return false;
        }

        return true;
    }

    handleEditorHideClick(event) {

        event.preventDefault();

        if (this.state.hidden) {

            this.showEditor();

        } else {

            this.hideEditor();
        }
    }

    hideEditor() {

        this.setState({
            hidden: true,
        });
    }

    showEditor() {

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

    closeTutorial() {

        this.setState({
            loadTutorial: false,
        });
    }

    tutorialChangePanel(panel) {

        this.refs.editor.loadPanel(panel);
    }

    render() {

        return (
            <div className={"cd-customize-container " + (this.state.hidden ? "hidden" : "")}>
                <Editor
                    onHideCustomizer={this.hideEditor}
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

                <div className={`cd-editor-hide ${this.state.hidden ? 'hidden' : ''}`}>
                    <a href="#" onClick={this.handleEditorHideClick}>
                        <span className={`fa fa-chevron-circle-${this.state.hidden && 'right' || 'left'}`}/>
                    </a>
                </div>

                {this.state.previewLoading &&
                <div id="cd-editor-preview-cover">
                    <LoadingIcon/>
                </div>}

                {(this.state.loadTutorial && !this.state.previewLoading) &&
                <Tutorial
                    onClose={this.closeTutorial}
                    onChangeEditorPanel={this.tutorialChangePanel}
                />
                }
            </div>
        )
    }
}

// Renders the Customizer
ReactDOM.render(
    <Customize />,
    document.getElementById('clientdash-customize')
);