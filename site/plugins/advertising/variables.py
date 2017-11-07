
html = """

<style type="text/css">
.hideable {
    display: none;
}
img {
    width: {{image_width}}px;
    height: {{image_heigth}}px;
}
</style>
<base href="propaganda/" />

<script type="text/javascript">
window.onload = toggleSlide;

function toggleSlide() {
    var elements = document.getElementsByClassName("hideable"); // gets all the "slides" in our slideshow

    // Find the LI that's currently displayed
    var makeVisible = Math.floor((Math.random() * elements.length) + 1);
    elements[makeVisible].style.display = "block";

}

</script>

<ul style="list-style-type:none; margin-left:-2em;">
    {{advertise_list}}
</ul>
</body>"""