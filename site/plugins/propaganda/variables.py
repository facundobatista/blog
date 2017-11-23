
html = """

<style type="text/css">
.img_{{key}} {
    vertical-align: middle;
    align-content: center; 
    width: {{image_width}}px;
    max-height: {{image_heigth}}px;
}
</style>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script type="text/javascript">

$( document ).ready(function toggleSlide() {
    var images = [];
    {{propaganda_list}}

    var choosed = images[Math.floor((Math.random() * images.length))];
    var base = "/propaganda/";
    $("#link_{{key}}").attr("href", choosed[1]);
    var $downloadingImage = $("<img>");
    $downloadingImage.load(function(){
        $("#img_{{key}}").attr("src", $(this).attr("src"));
        $("#img_{{key}}").attr("alt", choosed[2]);
    });
    $downloadingImage.attr("src", base.concat(choosed[0]));
});

</script>
<a id="link_{{key}}" href="#">
    <img id="img_{{key}}" class="img_{{key}}" src="/propaganda/loading.gif" />
</a>
"""