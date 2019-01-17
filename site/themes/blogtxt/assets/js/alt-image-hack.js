$(document).ready(function() {
    $.each($(".entry-content img"), function() {
        var altText = $(this).attr('alt');
        $(this).wrap('<figure>');
        $(this).after('<figcaption>'+altText+'</figcaption>');
    });
});