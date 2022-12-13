document.getElementById('marker-initials').onkeyup = function(e)
{
    document.getElementById('marker-preview-initials').innerText = e.target.value;
}

document.getElementById('marker-color').onkeyup = function(e)
{
    document.getElementById('marker-preview-color').style.backgroundColor = e.target.value;
}