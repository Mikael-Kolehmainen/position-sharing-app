function removeStyles(className) {
    let styles = document.getElementsByClassName(className);

    for (let i = 0; i < styles.length; i++) {
        styles[i].remove();
    }
}