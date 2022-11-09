window.addEventListener('beforeunload', function() 
{
    let xmlhttp = new XMLHttpRequest();
    let sent = false;
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            sent = true;
        }
    };
    if (sent == false) {
        xmlhttp.open("GET", "user/remove-user.php?groupcode=" + groupCode, true);
        xmlhttp.send();
    }
});