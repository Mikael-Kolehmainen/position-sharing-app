<?php

namespace controller\api;

use misc\Redirect;
use model;
use manager;

class GroupController extends BaseController
{
    /**
     * "index.php/map/create"
     */

    /** @var string */
    public $groupCode;
    
    public function saveToDatabase(): void
    {
        $this->validateMarkerColor();
        $groupModel = new model\GroupModel();
        $groupModel->createGroupCode();
        $groupModel->save();
        manager\SessionManager::saveGroupCode($groupModel->groupCode);
    }

    public function findGroupInDatabase()
    {
        $this->validateMarkerColor();
        $groupModel = new model\GroupModel();
        $this->groupCode = filter_input(INPUT_POST, GROUP_GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
        $groupModel->groupCode = $this->groupCode != null ? $this->groupCode : manager\SessionManager::getGroupCode();

        if ($groupModel->getRowCount()) {
            manager\SessionManager::saveGroupCode(($groupModel->groupCode));
            return true;
        }

        return false;
    }

    private function validateMarkerColor(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $color = strtolower(filter_input(INPUT_POST, USER_COLOR, FILTER_DEFAULT));
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
        $groupModel = new model\GroupModel();
        $groupModel->groupCode = manager\SessionManager::getGroupCode();

        $groupModel->removeWithGroupCode();
    }
}