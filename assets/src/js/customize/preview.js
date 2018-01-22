import React from 'react';

const adminurl = ClientdashCustomize_Data.adminurl || false;
const l10n     = ClientdashCustomize_Data.l10n || false;

/**
 * The Customize preview.
 *
 * @since 2.0.0
 */
class Preview extends React.Component {

    constructor(props) {

        super(props);

        this.handleFrameTasks = this.handleFrameTasks.bind(this);
        this.loaded           = this.loaded.bind(this);
    }

    shouldComponentUpadte() {

        // iframe should NEVER re-render, because it would refresh
        return false;
    }

    componentDidMount() {

        window.addEventListener('message', this.handleFrameTasks);
    }

    handleFrameTasks(e) {

        if ( !e.data.id ) {

            return;
        }

        switch ( e.data.id ) {

            case 'cd_customize_preview_link_clicked' :

                if ( !this.isLinkValid(e.data.link) ) {

                    this.props.onShowMessage({
                        type: 'error',
                        text: l10n['cannot_view_link']
                    });

                    return;
                }

                let link_base = e.data.link.includes('?') ? e.data.link + '&' : e.data.link + '?';
                let link      = link_base + 'cd_customizing=1&role=' + this.props.role;

                this.load(link);

                break;

            case 'cd_customize_preview_form_submit' :

                this.props.onShowMessage({
                    type: 'error',
                    text: l10n['cannot_submit_form']
                });

                break;
        }
    }

    isLinkValid(link) {

        // Not admin
        if ( !link.includes('/wp-admin') ) {

            return false;
        }

        // Not customizer
        if ( link.includes('customize.php') ) {

            return false;
        }

        return true;
    }

    loaded() {

        this.props.onLoad();
    }

    getSrc() {

        return adminurl + '?cd_customizing=1&role=' + this.props.role;
    }

    load(url) {

        this.iframe.src = url;
    }

    refresh() {

        this.iframe.src = this.iframe.src.includes('cd_save_role') ? this.getSrc() : this.iframe.src;
    }

    render() {

        return (
            <section id="cd-preview">
                <iframe
                    id="cd-preview-iframe"
                    src={this.getSrc() + (this.props.saveRole ? '&cd_save_role=1' : '')}
                    onLoad={this.loaded}
                    sandbox="allow-scripts allow-forms allow-same-origin"
                    ref={(f) => this.iframe = f}
                />
            </section>
        )
    }
}

export default Preview