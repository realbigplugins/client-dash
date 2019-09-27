import React from 'react';

/**
 * A Customizer panel.
 *
 * One panel shows at a time and they can be loaded and unloaded.
 *
 * @since 2.0.0
 */
class Panel extends React.Component {
    render() {
        return (
            <div className={"cd-editor-panel " + "cd-editor-panel-" + this.props.id}>
                {this.props.children}
            </div>
        )
    }
}

export {
    Panel
}