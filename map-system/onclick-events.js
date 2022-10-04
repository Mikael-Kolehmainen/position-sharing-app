document.querySelectorAll(".onclick").forEach(element => 
{
    element.addEventListener("click", event => 
    {
        switch(element.id) {
            case "water-switch":
                waterSwitchClicked();
                break;
            case "confirm-goal-btn":
                confirmGoalClicked();
                break;
            case "add-goal-btn":
                addGoalClicked();
                break;
            case "reject-add-goal-btn":
                rejectAddGoalClicked();
                break;
            case "show-draggable-goal":
                showDraggableGoalClicked();
                break;
            case "remove-draggable-goal":
                removeDraggableGoalClicked();
                break;
            case "remove-waypoint":
                removeWaypointClicked();
                break;
            case "active-goal-disclaimer":
                activeGoalDisclaimerClicked();
                break;
            case "open-chat-btn":
                openChatClicked();
                break;
            case "close-chat-btn":
                closeChatClicked();
                break;
            case "delete-group-btn":
                deleteGroupClicked();
                break;
            case "reject-group-delete-btn":
                rejectGroupDeleteClicked();
                break;
        }
    });
});

function waterSwitchClicked()
{
    console.log("clicked");
    showWaterEntities();
}

function confirmGoalClicked()
{
    goal.sendDataToPHP();
    openMenu("goal-options", "add-goal-btn", "block", ["open-chat-btn", "delete-group-btn"]);
}

function addGoalClicked()
{
    goal.clearPreviousPopup();
    goal.createPopup();
    openMenu("add-goal-btn", "goal-popup", "block");
}

function rejectAddGoalClicked()
{
    openMenu("goal-popup", "add-goal-btn", "block");
}

function showDraggableGoalClicked()
{
    goal.calculatePositionsOfStartGoalMarkers();
    goal.drawPolyline(true);
    openMenu("goal-popup", "goal-options", "block", ["open-chat-btn", "delete-group-btn"]);
}

function removeDraggableGoalClicked()
{
    goal.remove();
    openMenu("goal-options", "add-goal-btn", "inline-block", ["open-chat-btn", "delete-group-btn"]);
}

function removeWaypointClicked()
{
    waypoint.remove();
}

function activeGoalDisclaimerClicked()
{
    goal.remove();
}

function openChatClicked()
{
    openMenu("open-chat-btn", "chat", "block", ["add-goal-btn", "delete-group-btn"]);
}

function closeChatClicked()
{
    openMenu("chat", "open-chat-btn", "inline-block", ["add-goal-btn", "delete-group-btn"])
}

function deleteGroupClicked()
{
    openMenu("delete-group-btn", "delete-popup", "block");
}

function rejectGroupDeleteClicked()
{
    openMenu("delete-popup", "delete-group-btn", "inline-block");
}