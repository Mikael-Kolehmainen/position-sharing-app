<?php
class GoalController extends BaseController
{
    private const FIELD_GOAL_SESSION = 'goalsession';
    private const FIELD_GOAL_ID = 'goalIndex';

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

    public function saveToDatabase()
    {
        $goalModel = new GoalModel();
        $goalModel->startPositionId = $this->startPositionId;
        $goalModel->goalPositionId = $this->goalPositionId;
        $goalModel->groupCode = $this->groupCode;
        $goalModel->goalSession = $this->goalSession;
        $goalModel->userId = $this->userId;
        $goalModel->goalOrderNumber = $this->goalOrderNumber;
        return $goalModel->save();
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

    public function createGoalSession()
    {
        $goalSession = RandomString::getRandomString(15);

        if ($goalSession == $_SESSION[SESSION_GOALSESSION]) {
            return $this->createGoalSession();
        } else {
            return $goalSession;
        }
    }

    public function removeFromDatabase()
    {
        $goalModel = new GoalModel();
        $goalModel->groupCode = $this->groupCode;

        $goalModel->removeWithGroupCode();
    }
}