let camera = new Camera();

window.addEventListener("load", startCamera, false);

function startCamera()
{
    let mirrorCameraImage = new Style("camera-mirror-style");
    mirrorCameraImage.removeStyle();
    camera.constraints = { video: { facingMode: "environment" }, audio: false };
    camera.cameraView = document.querySelector("#camera-view");
    camera.cameraOutput = document.querySelector("#camera-output");
    camera.cameraSensor = document.querySelector("#camera-sensor");
    camera.cameraTrigger = document.querySelector("#camera-trigger");
    camera.cameraFlip = document.querySelector("#camera-flip");

    camera.cameraTrigger.onclick = function() 
    {
        camera.showTakenImage();
        camera.showOptions();
    };

    camera.cameraFlip.onclick = function()
    {
        camera.flipCamera();
    }
    
    camera.startCamera();
}