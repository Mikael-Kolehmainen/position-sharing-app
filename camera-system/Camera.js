class Camera
{
    constructor(constraints, cameraViewElement, cameraOutputElement, cameraSensorElement, cameraTriggerElement, url)
    {
        this.constraints = constraints;
        this.cameraView = cameraViewElement;
        this.cameraOutput = cameraOutputElement;
        this.cameraSensor = cameraSensorElement;
        this.cameraTrigger = cameraTriggerElement;
        this.url = url;
        this.imageBlob = "";
    }

    startCamera()
    {
        navigator.mediaDevices
            .getUserMedia(this.constraints)
            .then(function(stream) {
                camera.cameraView.srcObject = stream;
            })
            .catch(function(error) 
            {
                console.error("Something went wrong.", error);
            });
    }

    showTakenImage()
    {
        this.cameraSensor.width = this.cameraView.videoWidth;
        this.cameraSensor.height = this.cameraView.videoHeight;
        this.cameraSensor.getContext("2d").drawImage(this.cameraView, 0, 0);
        this.cameraOutput.style.display = "inline-block";
        this.cameraSensor.toBlob(function(blob) { camera.cameraOutput.src = URL.createObjectURL(blob); camera.imageBlob = blob; });
        this.cameraOutput.classList.add("taken");
    }

    hideTakenImage()
    {
        this.cameraSensor.width = 0;
        this.cameraSensor.height = 0;
        this.cameraOutput.style.display = "none";
        this.cameraOutput.src = "";
        this.cameraOutput.classList.remove("taken");
    }

    showOptions()
    {
        ElementDisplay.change("image-options", "block");
    }

    sendImagePathToDatabase()
    {
        let xmlhttp = new XMLHttpRequest();
        const groupCode = new URLSearchParams(window.location.search).get("groupcode");
        const url = 'send-image.php?groupcode=' + groupCode;
        
        let formData = new FormData();
        formData.append("webimagepath", this.imageBlob);
        formData.append("webimagetype", this.imageBlob.type.split("/")[1]);

        xmlhttp.open('POST', url, true);
        xmlhttp.send(formData);

        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log("Successfully sent data.");
                window.location.replace("./../map-system/active.php?groupcode=" + groupCode);
            }
        };
    }
}