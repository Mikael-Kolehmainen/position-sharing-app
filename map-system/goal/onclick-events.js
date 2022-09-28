document.getElementById("send-goal-data").addEventListener("click", function()
{
    const goal = new Goal();
    goal.sendDataToPHP();
});

document.getElementById("goal-btn").addEventListener("click", function() 
{
    const goal = new Goal();
    goal.clearPreviousPopup();
    goal.createPopup();
});

document.getElementById("show-draggable-goal").addEventListener("click", function() 
{
    const goal = new Goal();
    goal.showDraggable();
});

document.getElementById("remove-draggable-goal").addEventListener("click", function() 
{
    const goal = new Goal();
    goal.remove();
});

document.getElementById("active-goal-disclaimer").addEventListener("click", function() 
{
    const goal = new Goal();
    goal.remove();
});