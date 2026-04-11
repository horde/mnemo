/**
 * Code for mnemo/view.php.
 *
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @package Mnemo
 * @author  Jan Schneider <jan@horde.org>
 */

var Mnemo_View = {
    // Externally set properties:
    //  confirm
    onDomLoad: function()
    {
        var passphrase = document.getElementById('mnemo-passphrase');
        if (passphrase) {
            passphrase.focus();
        }

        var deleteBtn = document.getElementById('mnemo-delete');
        if (deleteBtn) {
            deleteBtn.addEventListener(
                'click',
                function(e)
                {
                    if (this.confirm) {
                        if (!window.confirm(this.confirm)) {
                            e.preventDefault();
                        }
                    }
                }.bind(this)
            );
        }
    }
};
document.addEventListener('DOMContentLoaded', Mnemo_View.onDomLoad.bind(Mnemo_View));
