import React from 'react';

import {Select} from './form-fields';

const roles = ClientdashCustomize_Data.roles || false;
const l10n  = ClientdashCustomize_Data.l10n || false;

/**
 * Select box for switching witch role is being edited.
 *
 * @since {{VERSION}}
 */
class RoleSwitcher extends React.Component {

    constructor(props) {

        super(props);

        this.switchRole = this.switchRole.bind(this);
    }

    switchRole(value) {

        this.props.onSwitchRole(value);
    }

    render() {

        return (
            <div className="cd-editor-role-switcher">
                <Select
                    options={roles}
                    label={l10n['role_switcher_label']}
                    selected={this.props.role}
                    disabled={this.props.disabled}
                    onHandleChange={this.switchRole}
                />
            </div>
        )
    }
}

export default RoleSwitcher