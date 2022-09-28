function createStyle(content, className) 
{
    let head = document.head;
    let style = document.createElement('style');

    style.classList.add(className);
    style.appendChild(document.createTextNode(content));
    
    head.appendChild(style);
}