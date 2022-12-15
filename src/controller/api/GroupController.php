<?php

namespace controller\api;

use misc\Redirect;
use misc\RandomString;
use model\Database;
use model\GroupModel;
use manager\ServerRequestManager;
use manager\SessionManager;
use manager;

class GroupController extends BaseController
{
    /**
     * "index.php/map/create"
     */

    /** @var string */
    public $groupCode;

    /** @var Database */
    private $db;

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

        if (count($groupModel->getRowCount())) {
            return $this->createGroupCode();
        }

        return $groupModel->groupCode;
    }

    public function findGroupInDatabase()
    {
        $this->validateMarkerColor();
        
        $groupCode = ServerRequestManager::postGroupCode() == null ? SessionManager::getGroupCode() : ServerRequestManager::postGroupCode();

        $groupModel = new GroupModel($this->db, $groupCode);

        if ($groupModel->getRowCount()) {
            SessionManager::saveGroupCode(($groupModel->groupCode));
            return true;
        }

        return false;
    }

    private function validateMarkerColor(): void
    {
        if (ServerRequestManager::isPost()) {
            $color = strtolower(ServerRequestManager::postUserColor());
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

            if (!str_starts_with($color, '#') && !in_array($color, $allowedColors)) {
                Redirect::redirect("The color isn\'t allowed.", "/index.php");
                exit();
            }
        }
    }

    public function removeGroupFromDatabase()
    {
        $groupModel = new GroupModel($this->db, SessionManager::getGroupCode());

        $groupModel->removeWithGroupCode();
    }
}