window.addEventListener('beforeunload', function(e) {
    let xmlhttp = new XMLHttpRequest();
    let sent = false;
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            sent = true;
        }
    };
    if (sent == false) {
        const groupCode = new URLSearchParams(window.location.search).get('groupcode');
        xmlhttp.open("GET", "remove-user.php?groupcode=" + groupCode, true);
        xmlhttp.send();
    }
});