class Style
{
    constructor(styleClassName, styleSheetContent = "")
    {
        this.styleClassName = styleClassName;
        this.styleSheetContent = styleSheetContent;
    }

    createStyle()
    {
        let head = document.head;
        let style = document.createElement('style');

        style.classList.add(this.styleClassName);
        style.appendChild(document.createTextNode(this.styleSheetContent));
        
        head.appendChild(style);
    }

    removeStyle()
    {
        let styles = document.getElementsByClassName(this.styleClassName);

        for (let i = 0; i < styles.length; i++) {
            styles[i].remove();
        }
    }
}