import React from 'react';

const l10n      = ClientdashCustomize_Data.l10n || false;
const dashicons = ClientdashCustomize_Data.dashicons || false;

/**
 * Provies an expandable window for choosing a Dashicon.
 *
 * @since 2.0.0
 */
class DashiconsSelector extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            open: false
        }

        this.toggleWindow   = this.toggleWindow.bind(this);
        this.selectDashicon = this.selectDashicon.bind(this);
        this.handleClear    = this.handleClear.bind(this);
    }

    toggleWindow() {

        this.setState((prevState) => ({
            open: !prevState.open
        }));
    }

    selectDashicon(dashicon) {

        this.setState({
            open: false
        });

        this.props.onSelectDashicon(dashicon);
    }

    handleClear(e) {

        e.stopPropagation();

        this.setState({
            open: false
        });

        this.props.onSelectDashicon('');
    }

    render() {

        let dashicon_options = [];

        dashicons.map((dashicon) => {
            dashicon_options.push(
                <DashiconsSelectorOption
                    key={dashicon}
                    dashicon={dashicon}
                    onSelectDashicon={this.selectDashicon}
                />
            );
        });

        return (
            <div className="cd-editor-dashicons-selector cd-editor-input">
                <div className="cd-editor-dashicons-selector-label" onClick={this.toggleWindow}>
                    {l10n['icon']}
                </div>

                <div className="cd-editor-dashicons-selector-field" onClick={this.toggleWindow}>
                    <span className={"dashicons " +
                    (this.props.value || this.props.placeholder + " cd-editor-dashicons-placeholder")}/>

                    <span className={"cd-editor-dashicons-selector-open " +
                    "fa fa-chevron-" + (this.state.open ? "up" : "down")}/>

                    {this.props.value &&
                    <span className="cd-editor-dashicons-selector-clear dashicons dashicons-no"
                          onClick={this.handleClear}/>}
                </div>

                {this.state.open &&
                <div className="cd-editor-dashicons-selector-window">
                    {dashicon_options}
                </div>
                }
            </div>
        )
    }
}

/**
 * Individual Dashicon option within the DashiconsSelector
 *
 * @since 2.0.0
 */
class DashiconsSelectorOption extends React.Component {

    constructor(props) {

        super(props);

        this.selectDashicon = this.selectDashicon.bind(this);
    }

    selectDashicon() {

        this.props.onSelectDashicon(this.props.dashicon);
    }

    render() {
        return (
            <span
                key={this.props.dashicon}
                className={"cd-editor-dashicons-selector-option dashicons " + this.props.dashicon}
                onClick={this.selectDashicon}
            />
        )
    }
}

export {
    DashiconsSelector,
    DashiconsSelectorOption
}