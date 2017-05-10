/**
 * Loads on page-load for inside the customize preview iframe.
 *
 * @since {{VERSION}}
 */
function cd_customize_links(e) {

    var links = document.getElementsByTagName('a');

    for (var i = 0, len = links.length; i < len; i++) {

        links[i].onclick = function (event) {

            event.preventDefault();

            // Try to get nearest link if clicked item isn't a link (nested)
            var node = event.target

            while (node && !node.href) {

                node = node.parentNode
            }

            if (!node.href) {

                return false;
            }

            window.parent.postMessage({
                id: 'cd_customize_preview_link_clicked',
                link: node.href
            }, ClientDashCustomizeInPreview_Data.domain);

            return false;
        }
    }
}

/**
 * Adds a "protective" overlay to non-dashboard pages to signal user that it is only a preview.
 *
 * @since {{VERSION}}
 */
function cd_customize_overlay() {

    var content = document.getElementById('wpwrap');
    var cover = document.createElement('div');
    var cover_text = document.createElement('div');

    cover.id = 'cd-customize-preview-cover';
    cover_text.className = 'cd-customize-preview-cover-text';
    cover_text.innerHTML = ClientDashCustomizeInPreview_Data['l10n']['preview_only'];

    cover.appendChild(cover_text);
    content.appendChild(cover);
}

/**
 * Deal with forms.
 *
 * @since {{VERSION}}
 */
function cd_customize_forms() {

    for (var i = 0; i < document.forms.length; i++) {

        var form = document.forms[i];

        form.addEventListener('submit', cd_customize_forms_prevent_submit, false);
    }
}

/**
 * Prevent forms from submitting.
 *
 * @since {{VERSION}}
 *
 * @param event
 * @returns {boolean}
 */
function cd_customize_forms_prevent_submit(event) {

    window.parent.postMessage({
        id: 'cd_customize_preview_form_submit'
    }, ClientDashCustomizeInPreview_Data.domain);

    event.preventDefault();
    return false;
}

document.addEventListener('DOMContentLoaded', cd_customize_links, false);
document.addEventListener('DOMContentLoaded', cd_customize_overlay, false);
document.addEventListener('DOMContentLoaded', cd_customize_forms, false);