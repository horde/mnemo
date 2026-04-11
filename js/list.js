/**
 * Code for the list view.
 *
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @package Mnemo
 * @author  Jan Schneider <jan@horde.org>
 */

var Mnemo_List = {
    // Externally set properties:
    //  ajaxUrl
    sortCallback: function(column, sortDown)
    {
        fetch(this.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ pref: 'sortby', value: column.substring(1) })
        });
        fetch(this.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ pref: 'sortdir', value: sortDown })
        });
    },

    onDomLoad: function()
    {
        var quicksearchL = document.getElementById('quicksearchL');
        if (quicksearchL) {
            quicksearchL.addEventListener(
                'click',
                function(e) {
                    quicksearchL.hidden = true;
                    document.getElementById('quicksearch').hidden = false;
                    document.getElementById('quicksearchT').focus();
                    e.preventDefault();
                }
            );
            document.getElementById('quicksearchX').addEventListener(
                'click',
                function(e) {
                    document.getElementById('quicksearch').hidden = true;
                    var searchField = document.getElementById('quicksearchT');
                    searchField.value = '';
                    QuickFinder.filter(searchField);
                    quicksearchL.hidden = false;
                    e.preventDefault();
                }
            );
        }
    }
};

function table_sortCallback(tableId, column, sortDown)
{
    Mnemo_List.sortCallback(column, sortDown);
}

document.addEventListener('DOMContentLoaded', Mnemo_List.onDomLoad.bind(Mnemo_List));
