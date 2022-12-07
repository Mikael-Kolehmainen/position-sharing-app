<?php
class CameraController extends BaseController
{
    /**
     * "index.php/map/camera"
     */

    /** @var string */
    public $groupCode;

    /** @var string */
    public $imagePath;

    /** @var string */
    public $webImagePath;

    /** @var string */
    public $webImageType;

    public function showCamera()
    {
        echo "
            <script src='/js/active-map/data/Data.js' defer></script>
            <script src='/js/active-map/camera/Camera.js' defer></script>
            <script src='/js/active-map/camera/initialize-camera.js' defer></script>
            <title>Take picture</title>
        </head>
        <body>
        <section>
            <main id='camera'>
                <video id='camera-view' autoplay playsinline></video>
                <canvas id='camera-sensor'></canvas>
                <img src='' alt='' id='camera-output' style='display: none;'>
                <button id='camera-trigger'><img src='/appearance/media/icons/camera-light.svg'></button>
                <div class='top'>
                    <a class='btn round onclick' id='close-camera'>
                        <i class='fa-solid fa-chevron-left'></i>
                    </a>
                </div>
                <div class='bottom' id='image-options' style='display: none;'>
                    <a class='btn onclick' id='reject-image'>
                        <i class='fa-solid fa-xmark'></i>
                    </a>
                    <a class='btn onclick' id='accept-image'>
                        <i class='fa-solid fa-check'></i>
                    </a>
                </div>
            </main>
        </section>
        ";
    }

    public function sendImage(): void
    {
        $this->groupCode = SessionManager::getGroupCode();
        $this->webImagePath = $_FILES[MESSAGE_WEB_IMAGE_PATH];
        $this->webImageType = filter_input(INPUT_POST, MESSAGE_WEB_IMAGE_TYPE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        $this->createImagePath();

        if ($this->saveImageToServer()) {
            $messageController = new MessageController();
            $messageController->imagePath = $this->imagePath;
            $messageController->saveToDatabase();
        } else {
            Redirect::redirect("Something went wrong with saving the image to the server.", "/index.php/map/camera");
        }
    }

    private function createImagePath(): void
    {
        $this->imagePath = "./appearance/media/chat_images/".$this->groupCode;
        $this->createDirIfDoesNotExist();
        $fileExt = $this->webImageType;
        $this->imagePath = substr($this->imagePath, 1)."/".RandomString::getRandomString(10).".".$fileExt;
    }

    private function createDirIfDoesNotExist(): void
    {
        if (!file_exists($this->imagePath) || !is_dir($this->imagePath)) {
            mkdir($this->imagePath, 750, true);
        }
    }

    private function saveImageToServer()
    {
        return move_uploaded_file($this->webImagePath["tmp_name"], "./".$this->imagePath);
    }
}