function buildDate(UNIX_timestamp) {
    var a = new Date(UNIX_timestamp * 1000);
    var months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    var year = a.getFullYear();
    var month = months[a.getMonth()];
    var day = a.getDate();
    var date = day + '-' + month + '-' + year;
    return date;
}


function makeUL(comments) {
    var list = document.createElement('div');
    comments.reverse()

    for (var i = 0; i < comments.length; i++) {
        var comment = comments[i];

        var list_item = document.createElement('p');
        list_item.className = 'comments_list_item'

        // prepare the text for the items
        var text = comment.text.replace(/<[^>]*>?/gm, '');
        if (text.length > 120) {
            text = text.substring(0, 120) + "...";
        }
        
        // prepare the author; trim it just in case it's too long (but the idea is show it full)
        var author = comment.author ? comment.author.replace(/<[^>]*>?/gm, '') : 'anónimo';
        author = author.substring(0, 40)

        // prepare the date
        var date = buildDate(comment.created)

        // prepare the link
        var link_text = " (" + author + ", " + date + ")"
        var link_node = document.createElement('a');
        link_node.setAttribute('href', comment.uri);
        link_node.appendChild(document.createTextNode(link_text));

        // set up everything
        list_item.appendChild(document.createTextNode(text))
        list_item.appendChild(document.createTextNode("   "))
        list_item.appendChild(link_node)
        list.appendChild(list_item);
    }

    return list;
}


function process(text) {
    var data = JSON.parse(text)
    document.getElementById('comments_list').appendChild(makeUL(data));
}


// async HTTP call
var xmlHttp = new XMLHttpRequest();
xmlHttp.onreadystatechange = function() { 
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
        process(xmlHttp.responseText);
}
xmlHttp.open("GET", "https://comentarios.taniquetil.com.ar/latest?limit=5", true); // true for async
xmlHttp.send(null);
