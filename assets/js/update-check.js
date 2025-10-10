/**
 * Update Check Functionality with WordPress Notifications
 * 
 * @package NextJSGraphQLHooks
 * @since 1.0.4
 */

(function($) {
    "use strict";

    /**
     * Display WordPress-style admin notice
     * 
     * @param {string} message - Notice message
     * @param {string} type - Notice type (success, error, warning, info)
     * @returns {void}
     */
    const showAdminNotice = function(message, type) {
        type = type || "info";

        // Remove existing notices
        $(".notice.nextjs-graphql-hooks-notice").remove();

        // Create notice HTML
        const noticeClass = "notice notice-" + type + " is-dismissible nextjs-graphql-hooks-notice";
        const noticeHtml = 
            '<div class="' + noticeClass + '">' +
                '<p><strong>' + message + '</strong></p>' +
                '<button type="button" class="notice-dismiss">' +
                    '<span class="screen-reader-text">Dismiss this notice.</span>' +
                '</button>' +
            '</div>';

        // Insert notice after first h1 heading
        const $notice = $(noticeHtml);
        $("h1").first().after($notice);

        // Handle dismiss button
        $notice.find(".notice-dismiss").on("click", function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        });

        // Auto-dismiss success and info notices after 5 seconds
        if (type === "success" || type === "info") {
            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    /**
     * Check for plugin updates via AJAX
     * 
     * @returns {void}
     */
    window.nextjsGraphQLHooksCheckUpdates = function() {
        // Verify data object exists
        if (typeof nextjsGraphQLHooksUpdateData === "undefined") {
            console.error("Update data not loaded");
            showAdminNotice("Error: Update data not loaded", "error");
            return;
        }

        // Show checking notice
        showAdminNotice(nextjsGraphQLHooksUpdateData.strings.checking, "info");

        // AJAX request to check updates
        $.ajax({
            url: nextjsGraphQLHooksUpdateData.ajaxurl,
            type: "POST",
            data: {
                action: "nextjs_graphql_hooks_check_updates",
                nonce: nextjsGraphQLHooksUpdateData.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    if (response.data.update_available) {
                        // Update available - show success and redirect
                        showAdminNotice(nextjsGraphQLHooksUpdateData.strings.available, "success");
                        
                        setTimeout(function() {
                            window.location.href = nextjsGraphQLHooksUpdateData.updateUrl;
                        }, 2000);
                    } else {
                        // Plugin up to date
                        showAdminNotice(nextjsGraphQLHooksUpdateData.strings.upToDate, "success");
                    }
                } else {
                    // Error in response
                    const errorMessage = response.data && response.data.message 
                        ? response.data.message 
                        : nextjsGraphQLHooksUpdateData.strings.error;
                    showAdminNotice(errorMessage, "error");
                }
            },
            error: function(xhr, status, error) {
                // AJAX error
                showAdminNotice(nextjsGraphQLHooksUpdateData.strings.error, "error");
                console.error("Update check failed:", error);
            }
        });
    };

})(jQuery);
