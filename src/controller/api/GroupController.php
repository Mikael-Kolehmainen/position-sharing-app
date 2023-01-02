<?php

namespace controller\api;

use misc\Redirect;
use misc\RandomString;
use model\Database;
use model\GroupModel;
use manager\ServerRequestManager;
use manager\SessionManager;


class GroupController extends BaseController
{
    /**
     * "index.php/map/create"
     */

    /** @var Database */
    private $db;

    /** @var string */
    private $markerColor;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function saveToDatabase(): void
    {
        $this->validateMarkerColor();
        $groupModel = new GroupModel($this->db, $this->createGroupCode());
        $groupModel->save();
        SessionManager::saveGroupCode($groupModel->groupCode);
    }

    private function createGroupCode(): string
    {
        $groupModel = new GroupModel($this->db, RandomString::getRandomString(3));

        if (count($groupModel->get())) {
            return $this->createGroupCode();
        }

        return $groupModel->groupCode;
    }

    public function findGroupInDatabase()
    {
        $this->validateMarkerColor();

        $groupCode = ServerRequestManager::postGroupCode() == null ? SessionManager::getGroupCode() : ServerRequestManager::postGroupCode();

        $groupModel = new GroupModel($this->db, $groupCode);

        if (count($groupModel->get())) {
            SessionManager::saveGroupCode(($groupModel->groupCode));
            return true;
        }

        return false;
    }

    public function removeGroupFromDatabase()
    {
        $groupModel = new GroupModel($this->db, SessionManager::getGroupCode());

        $groupModel->removeWithGroupCode();
    }

    private function validateMarkerColor(): void
    {
        if (ServerRequestManager::isPost()) {
            $this->markerColor = strtolower(ServerRequestManager::postUserColor());

            if ($this->notAllowedColor() && $this->notHexColor()) {
                Redirect::redirect("The color isn\'t valid.", "/index.php");
                exit();
            }
        }
    }

    private function notAllowedColor()
    {
        $allowedColors = ["aliceblue", "antiquewhite", "aqua", "aquamarine", "azure",
                                "beige", "bisque", "black", "blanchedalmond", "blue",
                                "blueviolet", "brown", "burlywood", "cadetblue", "chartreuse",
                                "chocolate", "coral", "cornflowerblue", "cornsilk", "crimson",
                                "cyan", "darkblue", "darkcyan", "darkgoldenrod", "darkgray",
                                "darkgrey", "darkgreen", "darkkhaki", "darkmagneta", "darkolivegreen",
                                "darkorange", "darkorchid", "darkred", "darksalmon", "darkseagreen",
                                "darkslateblue", "darkslategray", "darkturquoise", "darkviolet", "deeppink",
                                "deepskyblue", "dimgray", "dimgrey", "dodgerblue", "firebrick", "floralwhite",
                                "forestgreen", "fuchsia", "gainsboro", "ghostwhite", "gold", "goldenrod",
                                "gray", "grey", "green", "greenyellow", "honeydew", "hotpink", "indianred",
                                "indigo", "ivory", "khaki", "lavender", "lavenderblush", "lawngreen",
                                "lemonchiffon", "lightblue", "lightcoral", "lightcyan", "lightgoldenrodyellow",
                                "lightgray", "lightgrey", "lightgreen", "lightpink", "lightsalmon", "lightseagreen",
                                "lightskyblue", "lightslategray", "lightslategrey", "lightsteelblue",
                                "lightyellow", "lime", "limegreen", "linen", "magneta", "maroon", "mediumaquamarine",
                                "mediumblue", "mediumorchid", "mediumpurple", "mediumseagreen", "mediumslateblue",
                                "mediumspringgreen", "mediumturquoise", "mediumvioletred", "midnightblue",
                                "mintcream", "mistyrose", "moccasin", "navajowhite", "navy", "oldlace", "olive",
                                "olivedrab", "orange", "orangered", "orchid", "palegoldenrod", "palegreen",
                                "paleturquoise", "palevioletred", "papayawhip", "peachpuff", "peru", "pink",
                                "plum", "powderblue", "purple", "rebeccapurple", "red", "rosybrown", "royalblue",
                                "saddlebrown", "salmon", "sandybrown", "seagreen", "seashell", "sienna", "silver",
                                "skyblue", "slateblue", "slategray", "slategrey", "snow", "springgreen", "steelblue",
                                "tan", "teal", "thistle", "tomato", "turquoise", "violet", "wheat", "white",
                                "whitesmoke", "yellow", "yellowgreen"];

        return !in_array($this->markerColor, $allowedColors);
    }

    private function notHexColor()
    {
        return !str_starts_with($this->markerColor, '#') ||
                !(substr_count($this->markerColor, '#') == 1) ||
                !(strlen($this->markerColor) == 7 || strlen($this->markerColor) == 4);
    }
}