window.addEventListener('beforeunload', function(e) {
    let xmlhttp = new XMLHttpRequest();
    let sent = false;
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log("successfully sent data to php.");
            sent = true;
        }
    };
    if (sent == false) {
        const groupCode = new URLSearchParams(window.location.search).get('groupcode');
        xmlhttp.open("GET", "remove-member.php?groupcode=" + groupCode, true);
        xmlhttp.send();
    }
});