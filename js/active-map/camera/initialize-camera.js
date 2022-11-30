let camera = new Camera();

window.addEventListener("load", startCamera, false);

function startCamera()
{
    camera.constraints = { video: { facingMode: "environment" }, audio: false };
    camera.cameraView = document.querySelector("#camera-view");
    camera.cameraOutput = document.querySelector("#camera-output");
    camera.cameraSensor = document.querySelector("#camera-sensor");
    camera.cameraTrigger = document.querySelector("#camera-trigger");

    camera.cameraTrigger.onclick = function() 
    {
        camera.showTakenImage();
        camera.showOptions();
    };
    
    camera.startCamera();
}