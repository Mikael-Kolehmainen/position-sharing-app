function openMenu(sender, receiver, display = "block", optional = [], optionalDisplay = "none") {
    document.getElementById(sender).style.display = "none";
    document.getElementById(receiver).style.display = display;

    let element;
    for (var i = 0; i < optional.length; i++) {
        element = document.getElementById(optional[i]);
        
        element.style.display = optionalDisplay;
    }
}