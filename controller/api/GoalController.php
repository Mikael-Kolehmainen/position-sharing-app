<?php
class GoalController extends BaseController
{
    private const FIELD_GOAL_SESSION = 'goalsession';
    private const FIELD_GOAL_ID = 'goalordernumber';

    /** @var int */
    public $id;

    /** @var int */
    public $startPositionId;

    /** @var int */
    public $goalPositionId;

    /** @var int */
    public $goalOrderNumber;

    /** @var string */
    public $groupCode;

    /** @var string */
    public $goalSession;

    /** @var string */
    public $userId;

    /** @var string */
    public $fallbackInitials;

    public function saveToDatabase()
    {
        $goalModel = new GoalModel();
        $goalModel->startPositionId = $this->startPositionId;
        $goalModel->goalPositionId = $this->goalPositionId;
        $goalModel->groupCode = $this->groupCode;
        $goalModel->userId = $this->userId;
        $goalModel->goalOrderNumber = $this->goalOrderNumber;
        $goalModel->fallbackInitials = $this->fallbackInitials;
        return $goalModel->save();
    }

    public function updateGoalSessionInDatabase()
    {
        $goalModel = new GoalModel();
        $goalModel->goalSession = $this->goalSession;
        $goalModel->id = $this->id;
        $goalModel->update();
    }

    public function getIdsFromDatabase()
    {
        $goalModel = new GoalModel();
        $goalModel->groupCode = $this->groupCode;

        return $goalModel->getWithGroupCode();
    }

    public function getGoalSessionFromDatabase()
    {
        $goalModel = new GoalModel();
        $goalModel->groupCode = $this->groupCode;

        return isset($goalModel->getWithGroupCode()[0][self::FIELD_GOAL_SESSION]) ? $goalModel->getWithGroupCode()[0][self::FIELD_GOAL_SESSION] : null;
    }

    public function goalSessionEqualsDbGoalSession()
    {
        $goalSession = $this->getGoalSessionFromDatabase();

        return $goalSession == SessionManager::getGoalSession();
    }

    public function getOrderNumbersOfGoalsFromDatabase()
    {
        $goalModel = new GoalModel();
        $goalModel->groupCode = $this->groupCode;

        return $goalModel->getWithGroupCode();
    }

    public function getRowIdsOfGoalPositionsFromDatabase()
    {
        $goalModel = new GoalModel();
        $goalModel->groupCode = $this->groupCode;
        
        return $goalModel->getWithGroupCode();
    }

    public function getFallbackInitialsFromDatabase()
    {
        $goalModel = new GoalModel();
        $goalModel->groupCode = $this->groupCode;

        return $goalModel->getWithGroupCode();
    }

    public function createGoalSession()
    {
        $goalSession = RandomString::getRandomString(15);

        if ($goalSession == $_SESSION[SESSION_GOALSESSION]) {
            $this->createGoalSession();
        } else {
            $this->goalSession = $goalSession;
        }
    }

    public function removeFromDatabase()
    {
        $goalModel = new GoalModel();
        $goalModel->groupCode = $this->groupCode;

        $goalModel->removeWithGroupCode();
    }
}