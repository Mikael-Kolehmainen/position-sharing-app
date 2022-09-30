document.getElementById("send-goal-data").addEventListener("click", function()
{
    goal.sendDataToPHP();
    ElementDisplay.change('goal-btn', 'none');
});

document.getElementById("goal-btn").addEventListener("click", function() 
{
    goal.clearPreviousPopup();
    goal.createPopup();
});

document.getElementById("show-draggable-goal").addEventListener("click", function() 
{
    goal.calculatePositionsOfStartGoalMarkers();
    goal.drawDraggablePolyline(true);
});

document.getElementById("remove-draggable-goal").addEventListener("click", function() 
{
    goal.remove();
});

document.getElementById("active-goal-disclaimer").addEventListener("click", function() 
{
    goal.remove();
});