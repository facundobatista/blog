
html = """

<style type="text/css">
img {
    vertical-align: middle;
    align-content: center; 
    max-width: {{image_width}}px;
    max-height: {{image_heigth}}px;
}
</style>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script type="text/javascript">
window.onload = toggleSlide;

function toggleSlide() {
    var images = [];
    {{advertise_list}}

    var choosed = images[Math.floor((Math.random() * images.length))];
    var base = "/propaganda/";
    $("a").attr("href", choosed[1]);
    $("img").attr("alt", choosed[2]);
    $("img").attr("src", base.concat(choosed[0]));
}

</script>

<a href="#">
    <img src="/propaganda/loading.gif" />
</a>
"""