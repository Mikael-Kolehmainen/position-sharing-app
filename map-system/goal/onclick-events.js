document.getElementById("send-goal-data").addEventListener("click", function()
{
    goal.sendDataToPHP();
});

document.getElementById("goal-btn").addEventListener("click", function() 
{
    goal.clearPreviousPopup();
    goal.createPopup();
});

document.getElementById("show-draggable-goal").addEventListener("click", function() 
{
    goal.showDraggable();
});

document.getElementById("remove-draggable-goal").addEventListener("click", function() 
{
    goal.remove();
});

document.getElementById("active-goal-disclaimer").addEventListener("click", function() 
{
    goal.remove();
});