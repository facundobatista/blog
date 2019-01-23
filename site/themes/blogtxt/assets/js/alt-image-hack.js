$(document).ready(function() {
    $.each($(".entry-content img"), function() {
        var imgSrc = $(this)
        var altText = $(this).attr('alt');

        $(this).wrap('<figure>');
        $(this).after('<figcaption>'+altText+'</figcaption>');

    });
});
