document.querySelectorAll(".onclick").forEach(element => 
{
    element.addEventListener("click", event => 
    {
        switch(element.id) {
            case "water-switch":
                waterSwitchClicked();
                break;
            case "confirm-goal-positions-btn":
                confirmGoalPositionsClicked();
                break;
            case "confirm-route-btn":
                confirmRouteClicked();
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
            case "check-map-legends-btn":
                checkMapLegendsClicked();
                break;
            case "close-map-legends-btn":
                closeMapLegendsClicked();
                break;
        }
    });
});

function waterSwitchClicked()
{
    showWaterEntities();
}

function confirmGoalPositionsClicked()
{
    goal.disableMarkerDraggability();
    goal.enableOuterRouteDrawing();
    openMenu("goal-options", "goal-route-options", "block");

    instructions.instructionText = "Draw the outer routes, by adding waypoints";
    instructions.replace();
}

function confirmRouteClicked()
{
    goal.goalIsBeingPlanned = false;
    goal.saveOuterRouteSegments();
    goal.saveInnerRouteSegments();
    goal.saveSegmentsAsRoutes();
    goal.removeUserDrawnRoutes();
    goal.drawAllRoutes();
    goal.sendDataToPHP();
    openMenu("goal-route-options", "add-goal-btn", "block", ["open-chat-btn", "delete-group-btn", "check-map-legends-btn"])

    instructions.hide();
}

function addGoalClicked()
{
    goal.clearPreviousPopup();
    goal.createPopup();
    openMenu("add-goal-btn", "goal-popup", "block", ["open-chat-btn", "delete-group-btn", "check-map-legends-btn"]);
}

function rejectAddGoalClicked()
{
    openMenu("goal-popup", "add-goal-btn", "block", ["open-chat-btn", "delete-group-btn", "check-map-legends-btn"]);

    instructions.hide();
}

function showDraggableGoalClicked()
{
    goal.goalIsBeingPlanned = true;
    goal.goalRoutes = [];
    goal.userCanChooseStartGoalMarkerPositions();
    openMenu("goal-popup", "goal-options", "block");

    instructions.instructionText = "Add outer start marker #1";
    instructions.replace();
    instructions.show();
}

function removeDraggableGoalClicked()
{
    goal.remove();
    openMenu("goal-options", "add-goal-btn", "inline-block", ["open-chat-btn", "delete-group-btn", "check-map-legends-btn"]);
    openMenu("goal-route-options", "add-goal-btn", "inline-block");

    instructions.hide();
}

function activeGoalDisclaimerClicked()
{
    goal.remove();
    refreshCounter = 0;
    ElementDisplay.change('active-goal-disclaimer', 'none');
    ElementDisplay.change('add-goal-btn', 'block');
}

function openChatClicked()
{
    openMenu("open-chat-btn", "chat", "block", ["add-goal-btn", "delete-group-btn", "check-map-legends-btn"]);
}

function closeChatClicked()
{
    openMenu("chat", "open-chat-btn", "inline-block", ["add-goal-btn", "delete-group-btn", "check-map-legends-btn"])
}

function deleteGroupClicked()
{
    openMenu("delete-group-btn", "delete-popup", "block");
}

function rejectGroupDeleteClicked()
{
    openMenu("delete-popup", "delete-group-btn", "inline-block");
}

function checkMapLegendsClicked()
{
    openMenu("check-map-legends-btn", "map-legends-popup", "block", ["open-chat-btn", "delete-group-btn", "add-goal-btn"]);
}

function closeMapLegendsClicked()
{
    openMenu("map-legends-popup", "check-map-legends-btn", "block", ["open-chat-btn", "delete-group-btn", "add-goal-btn"]);
}