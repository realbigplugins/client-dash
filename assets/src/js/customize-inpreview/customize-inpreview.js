(function ($) {
    /**
     * Loads on page-load for inside the customize preview iframe.
     *
     * @since 2.0.0
     */
    function cd_customize_links(e) {

        $('a').click(function (e) {
            e.preventDefault();
            return false;
        });
    }

    /**
     * Adds a "protective" overlay to non-dashboard pages to signal user that it is only a preview.
     *
     * @since 2.0.0
     */
    function cd_customize_overlay() {

        $('#wpwrap').append(
            '<div id="cd-customize-preview-cover">' +
            '<div class="cd-customize-preview-cover-text">' +
            ClientDashCustomizeInPreview_Data['l10n']['preview_only'] +
            '</div>' +
            '</div>'
        );
    }

    /**
     * Deal with forms.
     *
     * @since 2.0.0
     */
    function cd_customize_forms() {

        $('form').submit(function (e) {

            window.parent.postMessage({
                id: 'cd_customize_preview_form_submit'
            }, ClientDashCustomizeInPreview_Data.domain);

            e.preventDefault();
            return false;
        });
    }

    // On Ready
    $(function () {

        cd_customize_links();
        cd_customize_overlay();
        cd_customize_forms();
    });

}(jQuery));