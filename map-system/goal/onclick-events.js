document.getElementById("send-goal-data").addEventListener("click", function()
{
    const goal = new Goal();
    goal.sendDataToPHP();
});

document.getElementById("goal-btn").addEventListener("click", function() 
{
    const goal = new Goal();
    goal.clearPreviousGoalPopup();
    goal.createGoalPopup();
});

document.getElementById("show-draggable-goal").addEventListener("click", function() 
{
    const goal = new Goal();
    goal.showDraggableGoal();
});