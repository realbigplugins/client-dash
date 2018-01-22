import React from 'react';

import {getInput} from './form-fields';

const roles = ClientdashCustomize_Data.roles || false;
const l10n  = ClientdashCustomize_Data.l10n || false;

/**
 * Select box for switching witch role is being edited.
 *
 * @since 2.0.0
 */
class RoleSwitcher extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            confirming: false,
        }

        this.switchRole = this.switchRole.bind(this);
    }

    switchRole(name, value) {

        this.props.onSwitchRole(value);
    }

    render() {

        return (
            <div className="cd-editor-role-switcher">
                {getInput('select', {
                    options: roles,
                    label: l10n['role_switcher_label'],
                    value: this.props.role,
                    disabled: this.props.disabled,
                    onHandleChange: this.switchRole,
                })}
            </div>
        )
    }
}

export default RoleSwitcher