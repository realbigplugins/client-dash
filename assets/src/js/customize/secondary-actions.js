import React from 'react';

import ActionButton from './action-button';

const l10n = ClientdashCustomize_Data.l10n || false;

/**
 * Secondary actions for panels.
 *
 * @since 2.0.0
 */
class SecondaryActions extends React.Component {

    constructor(props) {

        super(props);

        this.loadNextPanel     = this.loadNextPanel.bind(this);
        this.loadPreviousPanel = this.loadPreviousPanel.bind(this);
    }

    loadNextPanel() {

        if (this.props.disabled) {
            return;
        }

        this.props.loadPanel(this.props.nextPanel, 'forward');
    }

    loadPreviousPanel() {

        if (this.props.disabled) {
            return;
        }

        this.props.loadPanel(this.props.previousPanel, 'backward');
    }

    render() {
        return (
            <div className="cd-editor-secondary-actions">
                {this.props.title &&
                <div className="cd-editor-panel-actions-title">
                    {this.props.title}
                </div>
                }

                {(this.props.previousPanel || this.props.nextPanel) &&
                <div className="cd-editor-panel-actions-buttons">
                    {this.props.previousPanel &&
                    <ActionButton
                        title={l10n['action_button_back']}
                        text={<span className="fa fa-chevron-left"/>}
                        align="left"
                        size="large"
                        onHandleClick={this.loadPreviousPanel}
                        disabled={this.props.disabled}
                    />
                    }

                    {this.props.nextPanel &&
                        <a href="#" className="cd-editor-sub-action cd-editor-add-items"
                           onClick={this.loadNextPanel}>
                            <span className="fa fa-plus-square" /> {this.props.loadNextText}
                        </a>
                    }

                    {this.props.nextPanelNotification &&
                    <div className="cd-editor-panel-actions-notification cd-editor-tip cd-editor-tip-above next">
                        {this.props.nextPanelNotification}
                    </div>
                    }
                </div>
                }
            </div>
        )
    }
}

export {
    SecondaryActions
}