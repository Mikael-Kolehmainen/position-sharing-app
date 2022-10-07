document.querySelectorAll(".onchange").forEach(element => 
{
    element.addEventListener("change", event => 
    {
        switch (element.id) {
            case "distance-number-changer":
                distanceNumberChanged(element);
                break;
            case "degrees-number-changer":
                degreesNumberChanged(element);
                break;
        }
    });
});

function distanceNumberChanged(element)
{
    if (element.value.length > 2) {
        element.value = 99;
    }
    goal.applyDistanceBetweenGoals();
}

function degreesNumberChanged(element)
{
    if (element.value > 360) {
        element.value = 360;
    }
    goal.applyDegreesBetweenGoals();
}