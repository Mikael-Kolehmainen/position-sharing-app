<?php
class ActiveController extends BaseController
{
    /**
     * "index.php/map/active"
     */

    /** @var string */
    public $groupCode;

    public function __construct($groupCode)
    {
        $this->groupCode = $groupCode;
    }

    public function showMapPage(): void
    {
        echo "
            <link href='/leaflet/leaflet.css' rel='stylesheet' type='text/css'/>
            <script src='/leaflet/leaflet.js'></script>
            <script src='/leaflet/leaflet.geometryutil.js'></script>
            <script src='/leaflet/turf.min.js'></script>
            <script src='/js/active-map/global-objects.js' defer></script>
            <script src='/js/active-map/LayerManagement.js' defer></script>
            <script src='/js/active-map/before-closing.js' defer></script>
            <script src='/js/active-map/data/Data.js' defer></script>
            <script src='/js/active-map/chat/Chat.js' defer></script>
            <script src='/js/active-map/chat/Message.js' defer></script>
            <script src='/js/active-map/goal/Goal.js' defer></script>
            <script src='/js/active-map/goal/Instructions.js' defer></script>
            <script src='/js/active-map/user/User.js' defer></script>
            <script src='/js/active-map/data/data-handler.js' defer></script>
            <script src='/js/active-map/map.js' defer></script>
            <title>Group map</title>
        </head>
        <body class='active-page'>
            <section>
                <article>
                    <div class='top'>
                        <p>Group code:</p>
                        <p>$this->groupCode</p>
                    </div>
                    <div class='disclaimer onclick' id='active-goal-disclaimer' style='display: none;'>
                        <p>There's an active goal</p>
                    </div>
                    <div id='map'></div>
                    <div class='instructions'>
                        <p style='display: none;' id='instruction-text'>Instruction text</p>
                    </div>
                    <div class='bottom'>
                        <a class='btn round onclick' id='open-chat-btn' style='display: inline-block;'>
                            <i class='fa-solid fa-message'></i>
                        </a>
                        <div class='chat' style='display: none;' id='chat'>
                            <div class='btn-container'>
                                <a class='btn round onclick' id='close-chat-btn'>
                                    <i class='fa-solid fa-xmark'></i>
                                </a>
                            </div>
                            <div class='messages' id='messages'>
                                
                            </div>
                            <form method='POST' action='/index.php/ajax/send-message' class='textbox'>
                                <input type='text' name='message' placeholder='Please be kind' maxlength='255' required>
                                <a href='/index.php/map/camera' class='camera-btn'></a>
                                <input type='submit' value='' id='send-btn'>
                            </form>
                        </div>
                        <a class='btn small onclick' id='delete-group-btn'>
                            <p>Delete group</p>
                        </a>
                        <a class='btn round onclick' id='add-goal-btn' style='display: inline-block;'>
                            <i class='fa-solid fa-location-dot'></i>
                        </a>
                        <div class='options' style='display: none;' id='goal-options'>
                            <a class='btn onclick' id='remove-draggable-goal'>
                                <i class='fa-solid fa-xmark'></i>
                            </a>
                            <a class='btn onclick' id='confirm-goal-positions-btn'>
                                <i class='fa-solid fa-check'></i>
                            </a>
                        </div>
                        <div class='options' style='display: none;' id='goal-route-options'>
                            <a class='btn onclick' id='remove-draggable-goal'>
                                <i class='fa-solid fa-xmark'></i>
                            </a>
                            <a class='btn onclick' id='confirm-route-btn'>
                                <i class='fa-solid fa-check'></i>
                            </a>
                        </div>
                        <a class='btn round onclick center small-circle' id='check-map-legends-btn' style='display: inline-block;'>
                            <i class='fa-solid fa-layer-group'></i>
                        </a>
                    </div>
                    <div class='popup' id='delete-popup' style='display: none;'>
                        <p>Are you sure you want to delete this group?</p>
                        <a class='btn onclick' id='reject-group-delete-btn'>
                            <p>No</p>
                        </a>
                        <a class='btn' href='/index.php/map/remove-group'>
                            <p>Yes</p>
                        </a>
                    </div>
                    <div class='popup' id='goal-popup' style='display: none;'>
                        <p>Choose which users get a goal?</p>
                        <table id='users-table'>
                        </table>
                        <a class='btn onclick' id='reject-add-goal-btn'>
                            <p>No</p>
                        </a>
                        <a class='btn onclick' id='show-draggable-goal'>
                            <p>Yes</p>
                        </a>
                    </div>
                    <div class='popup' id='map-legends-popup' style='display: none;'>
                        <div class='btn-container'>
                            <a class='btn round onclick' id='close-map-legends-btn'>
                                <i class='fa-solid fa-xmark'></i>
                            </a>
                        </div>
                        <p>Map legends</p>
                        <table>
                            <tr>
                                <td>
                                    <div class='user-marker map-legend'>
                                        <p>initials</p>
                                    </div>
                                </td>
                                <td>
                                    <p>User Marker</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class='start-marker map-legend'>
                                        <p>initials</p>
                                    </div>
                                </td>
                                <td>
                                    <p>Start Marker</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class='goal-marker map-legend'>
                                        <p>initials</p>
                                    </div>
                                </td>
                                <td>
                                    <p>Goal Marker</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class='route-polyline map-legend'></div>
                                </td>
                                <td>
                                    <p>Route Line</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </article>
            </section>
        ";
    }
}