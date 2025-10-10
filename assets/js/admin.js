/**
 * Admin JavaScript for NextJS GraphQL Hooks
 *
 * @package NextJSGraphQLHooks
 * @since 1.0.4
 */

(function($) {
    "use strict";

    /**
     * Initialize admin functionality
     */
    $(document).ready(function() {
        // Add smooth scroll for internal links
        $('a[href^="#"]').on("click", function(e) {
            const target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                $("html, body").animate({
                    scrollTop: target.offset().top - 32
                }, 500);
            }
        });

        // Handle code copy on click
        $(".query-example pre, .filter-example pre").on("click", function() {
            const code = $(this).find("code").text();
            
            // Try to copy to clipboard
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(function() {
                    showNotice(
                        nextjsGraphQLHooksData.strings.copied || "Code copied to clipboard!",
                        "success"
                    );
                }).catch(function() {
                    // Fallback if clipboard API fails
                    fallbackCopyToClipboard(code);
                });
            } else {
                // Fallback for older browsers
                fallbackCopyToClipboard(code);
            }
        });

        // Add copy button indication
        $(".query-example pre, .filter-example pre").css({
            cursor: "pointer",
            position: "relative"
        }).attr("title", "Click to copy");

        // Add visual feedback on hover
        $(".query-example pre, .filter-example pre").hover(
            function() {
                $(this).css("opacity", "0.9");
            },
            function() {
                $(this).css("opacity", "1");
            }
        );
    });

    /**
     * Fallback copy to clipboard for older browsers
     * 
     * @param {string} text - Text to copy
     */
    function fallbackCopyToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.top = "-9999px";
        textArea.style.left = "-9999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand("copy");
            if (successful) {
                showNotice(
                    nextjsGraphQLHooksData.strings.copied || "Code copied to clipboard!",
                    "success"
                );
            } else {
                console.error("Fallback copy failed");
            }
        } catch (err) {
            console.error("Fallback copy error:", err);
        }

        document.body.removeChild(textArea);
    }

    /**
     * Display WordPress-style admin notice
     * 
     * @param {string} message - Notice message
     * @param {string} type - Notice type (success, error, warning, info)
     */
    function showNotice(message, type) {
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
    }

    // Expose showNotice globally for use by other scripts
    window.nextjsGraphQLHooksShowNotice = showNotice;

})(jQuery);
