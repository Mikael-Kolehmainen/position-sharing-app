<?php

namespace model;


class GroupModel
{
    private const TABLE_NAME = 'groups';
    private const USER_TABLE_NAME = 'users';
    private const MESSAGE_TABLE_NAME = 'messages';
    private const GOAL_TABLE_NAME = 'goals';
    private const FIELD_ID = 'id';
    private const FIELD_GROUP_CODE = 'groupcode';
    private const FIELD_GROUPS_GROUP_CODE = 'groups_groupcode';
    private const FIELD_TIME = 'timeofmessage';

    /** @var int */
    public $id;

    /** @var string */
    public $groupCode;

    /** @var Database */
    private $db;

    public function __construct($database, $groupCode)
    {
        $this->db = $database;
        $this->groupCode = $groupCode;
    }

    public function save(): void
    {
        $this->db->insert(
            'INSERT INTO ' . self::TABLE_NAME .
                ' (' .
                self::FIELD_GROUP_CODE .
                ') VALUES (?)',
            [
                ['s'],
                [$this->groupCode]
            ]);
    }

    public function removeWithGroupCode(): void
    {
        $this->db->remove(
            'DELETE FROM ' . self::TABLE_NAME .
            ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?',
            [
                ['s'], [$this->groupCode]
            ]);
    }

    /** @return GroupModel[] */
    public function get(): array
    {
        $records = $this->db->select('SELECT * FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
        $groups = [];
        foreach ($records as $record) {
            $group = new GroupModel($this->db, $this->groupCode);
            $group->mapFromDbRecord($record);
            $groups[] = $group;
        }
        return $groups;
    }

    public function deleteAllGroupMembers()
    {
        foreach($this->getGroupMembers() as $member) {
            $member->delete();
        }
    }

    /** @return UserModel[] */
    public function getGroupMembers(): array
    {
        $records = $this->db->select('SELECT * FROM ' . self::USER_TABLE_NAME . ' WHERE ' . self::FIELD_GROUPS_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
        $users = [];
        foreach ($records as $record) {
            $user = new UserModel($this->db);
            $user->mapFromDbRecord($record);
            $users[] = $user;
        }
        return $users;
    }

    /** @return MessageModel[] */
    public function getGroupMessages(): array
    {
        $records = $this->db->select('SELECT *, DATE_FORMAT(' . self::FIELD_TIME  . ', "%H:%i") AS ' . self::FIELD_TIME . ' FROM ' . self::MESSAGE_TABLE_NAME . ' WHERE ' . self::FIELD_GROUPS_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
        $messages = [];
        foreach ($records as $record) {
            $message = new MessageModel($this->db);
            $message->mapFromDbRecord($record);
            $messages[] = $message;
        }
        return $messages;
    }

    /** @return GoalModel[] */
    public function getGroupGoals(): array
    {
        $records = $this->db->select('SELECT * FROM ' . self::GOAL_TABLE_NAME . ' WHERE ' . self::FIELD_GROUPS_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
        $goals = [];
        foreach ($records as $record) {
            $goal = new GoalModel($this->db);
            $goal->mapFromDbRecord($record);
            $goals[] = $goal;
        }
        return $goals;
    }

    /**
     * @param mixed[] $record Associative array of one db record
     * @return $this
     */
    public function mapFromDbRecord($record)
    {
        $this->id = $record[self::FIELD_ID];
        $this->groupCode = $record[self::FIELD_GROUP_CODE];
        return $this;
    }
}
