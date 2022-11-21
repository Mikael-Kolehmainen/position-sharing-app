class Data
{
    constructor(url)
    {
        this.url = url;
    }

    sendToPHP(_callback)
    {
        let xmlhttp = new XMLHttpRequest();

        xmlhttp.open("GET", this.url, true);

        xmlhttp.onload = function()
        {
            if (xmlhttp.status >= 200 && xmlhttp.status < 400)
            {
                _callback();
            }
        }

        xmlhttp.send();
    }

    getFromPHP(_callback)
    {
        let xmlhttp = new XMLHttpRequest();

        xmlhttp.open("GET", this.url, true);

        xmlhttp.onload = function()
        {
            if (xmlhttp.status >= 200 && xmlhttp.status < 400) 
            {
                _callback(JSON.parse(this.responseText));
            }
        }

        xmlhttp.send();
    }
}