$(document).ready(function() {
    $.each($(".entry-content img"), function() {
        var altText = $(this).attr('alt');
        if (altText != null) {
            var altHtml = '<div class="alt_text">' + altText + '</div>';
            $(this).after(altHtml);
        }
    });
});