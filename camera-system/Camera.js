class Camera
{
    constructor(constraints, cameraViewElement, cameraOutputElement, cameraSensorElement, cameraTriggerElement)
    {
        this.constraints = constraints;
        this.cameraView = cameraViewElement;
        this.cameraOutput = cameraOutputElement;
        this.cameraSensor = cameraSensorElement;
        this.cameraTrigger = cameraTriggerElement;
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
        this.cameraOutput.src = this.cameraSensor.toDataURL("image/webp");
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
}