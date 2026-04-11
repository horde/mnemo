/**
 * Code for mnemo/memo.php.
 *
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @package Mnemo
 * @author  Jan Schneider <jan@horde.org>
 */

var Mnemo_Memo = {
    // Externally set properties:
    //  confirm
    updateCharacterCount: function()
    {
        var body = document.getElementById('mnemo-body');
        if (body) {
            document.getElementById('mnemo-count').textContent =
                body.value.replace(/[\r\n]/g, '').length;
        }
    },

    onDomLoad: function()
    {
        var passphrase = document.getElementById('mnemo-passphrase');
        if (passphrase) {
            passphrase.focus();
        }
        var body = document.getElementById('mnemo-body');
        if (body) {
            body.focus();
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

        if (body) {
            body.addEventListener('change', this.updateCharacterCount);
            body.addEventListener('click', this.updateCharacterCount);
            body.addEventListener('keypress', function() {
                setTimeout(Mnemo_Memo.updateCharacterCount, 0);
            });
        }
    }
};
document.addEventListener('DOMContentLoaded', Mnemo_Memo.onDomLoad.bind(Mnemo_Memo));
