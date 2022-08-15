function openMenu(sender, receiver, display = "block", optional = []) {
    document.getElementById(sender).style.display = "none";
    document.getElementById(receiver).style.display = display;

    let element;
    for (var i = 0; i < optional.length; i++) {
        element = document.getElementById(optional[i]);
        if (element.style.display != "none") {
            element.style.display = "none";
        } else {
            element.style.display = display;
        }
    }
}