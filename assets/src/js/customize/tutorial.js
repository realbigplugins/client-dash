import React from 'react';

const l10n           = ClientdashCustomize_Data.l10n || false;
const tutorialPanels = ClientdashCustomize_Data.tutorial_panels || {};
const panelList      = Object.keys(tutorialPanels);

/**
 * A highlight item to help the user see what is being described.
 *
 * @since 2.0.0
 */
class TutorialHighlight extends React.Component {
    render() {

        let anchor          = document.getElementsByClassName(this.props.selector);
        let positionY       = 0;
        let styles          = {};
        let classes         = [
            'cd-tutorial-highlight',
            this.props.position,
            this.props.size || 'medium',
            this.props.classes || '',
        ];
        let arrow_direction = '';

        if ( !anchor.length ) {

            return '';
        }

        anchor = anchor[0];

        let offsetY = anchor.getBoundingClientRect().top + window.scrollY;
        let offsetX = anchor.getBoundingClientRect().left + window.scrollX;

        switch ( this.props.position ) {

            case 'bottom':

                arrow_direction = 'up';
                styles.top      = offsetY + anchor.offsetHeight + 'px';
                styles.left     = offsetX + (anchor.offsetWidth / 2) + 'px';
                break;

            case 'top':

                arrow_direction = 'down';
                styles.top      = offsetY + 'px';
                styles.left     = offsetX + (anchor.offsetWidth / 2) + 'px';
                break;

            case 'left':

                arrow_direction = 'right';
                styles.top      = offsetY + (anchor.offsetHeight / 2) + 'px';
                styles.left     = offsetX + 'px';
                break;

            case 'right':

                arrow_direction = 'left';
                styles.top      = offsetY + (anchor.offsetHeight / 2) + 'px';
                styles.left     = offsetX + (anchor.offsetWidth) + 'px';
                break;
        }

        return (
            <div className={classes.join(' ')} style={styles}>
                <span className={"fa fa-arrow-" + arrow_direction}/>
            </div>
        )
    }
}

/**
 * Tutorial Panel
 *
 * @since 2.0.0
 */
class TutorialPanel extends React.Component {
    render() {

        const panel   = tutorialPanels[this.props.id];
        const content = panel['content'].map((line, index) => {

            let classes = line['classes'] || '';

            switch ( line['type'] ) {

                case 'link':
                    return <p key={index} className={classes}>
                        <a href={line['link']} className={(line['link_classes'] || '')} target="_blank">
                            {line['text']}
                        </a>
                    </p>;
                    break;

                case 'h2':
                    return <h2 key={index} className={classes}>{line['text']}</h2>;
                    break;

                case 'h3':
                    return <h3 key={index} className={classes}>{line['text']}</h3>;
                    break;

                default:
                    return <p key={index} className={classes}>{line['text']}</p>;
            }
        });

        let highlights = panel['highlights'] || [];

        return (
            <div>
                {highlights.map((highlight, index) =>
                    <TutorialHighlight key={index} {...highlight} />
                )}

                <div className="cd-tutorial-panel">
                    <h2 className="cd-tutorial-panel-title">
                        {panel['title']}
                    </h2>

                    {panel['image'] &&
                    <img
                        src={panel['image']['src']}
                        altText={panel['image']['alt']}
                        className="cd-tutorial-panel-image"
                    />
                    }

                    <div className="cd-tutorial-panel-content">
                        {content}
                    </div>
                </div>
            </div>
        )
    }
}

/**
 * The Tutorial modal.
 *
 * @since 2.0.0
 */
class Tutorial extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            panel: 'intro',
        }

        this.close       = this.close.bind(this);
        this.nextPanel   = this.nextPanel.bind(this);
        this.prevPanel   = this.prevPanel.bind(this);
        this.changePanel = this.changePanel.bind(this);
    }

    close() {

        fetch('wp-json/wp/v2/users/' + ClientdashCustomize_Data['current_user_id'], {
            method: 'POST',
            credentials: 'same-origin',
            headers: new Headers({
                'Content-Type': 'application/json',
                'X-WP-Nonce': ClientdashCustomize_Data['api_nonce'],
            }),
            body: JSON.stringify({
                'clientdash_hide_customize_tutorial': 'yes',
            })
        }).then(function (response) {

            console.log(response);

        }).catch(function (error) {

            console.log('error: ', error);
        });

        this.props.onClose();
    }

    nextPanel() {

        this.changePanel(panelList[this.getPanelIndex() + 1]);
    }

    prevPanel() {

        this.changePanel(panelList[this.getPanelIndex() - 1]);
    }

    changePanel(panel) {

        let panel_options = tutorialPanels[panel];

        if ( panel_options['editor_panel'] ) {

            this.props.onChangeEditorPanel(panel_options['editor_panel']);
        }

        this.setState({
            panel: panel,
        })
    }

    getPanelIndex() {

        return panelList.indexOf(this.state.panel);
    }

    render() {

        return (
            <section id="cd-tutorial">
                <div className="cd-tutorial-wrap">
                    <div className="cd-tutorial-container">
                    <span className="cd-tutorial-close" onClick={this.close}>
                        <span className="fa fa-close"/>
                    </span>

                        <TutorialPanel id={this.state.panel}/>

                        <div className="cd-tutorial-nav">
                            {this.getPanelIndex() > 0 &&
                            <span className="cd-tutorial-nav-prev" onClick={this.prevPanel}>
                            {l10n['previous']}
                        </span>
                            }

                            {this.getPanelIndex() < panelList.length - 1 &&
                            <span className="cd-tutorial-nav-next cd-tutorial-button" onClick={this.nextPanel}>
                            {l10n['next']}
                        </span>
                            }

                            {this.getPanelIndex() === panelList.length - 1 &&
                            <span className="cd-tutorial-nav-finish cd-tutorial-button" onClick={this.close}>
                            {l10n['finish']}
                        </span>
                            }
                        </div>
                    </div>
                </div>
            </section>
        )
    }
}

export default Tutorial