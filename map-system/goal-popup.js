// used in create-goal.js
let idsOfGoals = [];

function createPopup() {
    // We put the rows to the table
    // Structure of row
    /*
        <tr>
            <td>User</td>
            <td>Goal</td>
        </tr>
        <tr>
            <td>
                <div class='profile'>
                    <p>MK</p>
                </div>
            </td>
            <td><input type='checkbox' id='index-of-user'></td>
        </tr>
    */
    const usersTable = document.getElementById('users-table');

    // Clear previous rows
    removeChilds(usersTable);

    idsOfGoals = [];

    const titleRow = document.createElement("tr");
    const titleCell_1 = document.createElement("td");
    const titleCell_2 = document.createElement("td");
    titleCell_1.innerText = "User";
    titleCell_2.innerText = "Goal";

    usersTable.appendChild(titleRow);
    titleRow.appendChild(titleCell_1);
    titleRow.appendChild(titleCell_2);

    for (let i = 0; i < usersData.length; i++) {
        const userRow = document.createElement("tr");
        const userCell_1 = document.createElement("td");
        const userCell_2 = document.createElement("td");
        
        const userProfile = document.createElement("div");
        const initialsText = document.createElement("p");
        initialsText.innerHTML = usersData[i].initials;
        userProfile.classList.add('profile');
        
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.id = "userindex-" + i;
        checkbox.onchange = function(){ getIdOfCheckbox(this)};

        usersTable.appendChild(userRow);
        userRow.appendChild(userCell_1);
        userRow.appendChild(userCell_2);
        userCell_1.appendChild(userProfile);
        userProfile.appendChild(initialsText);
        userCell_2.appendChild(checkbox);

        const className = 'goal-menu-user-marker-' + i;
        userProfile.classList.add(className);
        styleSheetContent += '.' + className + '{ background-color: ' + usersData[i].color + '; }';
    }
    createStyle(styleSheetContent, 'js-style');
}

// make onchange function that checks what value checkbox has and updates goal array accordingly
function getIdOfCheckbox(checkbox)
{
    let idOfCheckbox = checkbox.id;
    const idSplitted = idOfCheckbox.split('-');
    idOfCheckbox = idSplitted[1];
    
    updateIdsOfGoals(idOfCheckbox);
}

function updateIdsOfGoals(idOfGoal)
{
    let indexOfId = idsOfGoals.indexOf(idOfGoal);

    if (indexOfId == -1) 
    {
        idsOfGoals.push(idOfGoal);
    } 
    else 
    {
        idsOfGoals.splice(indexOfId, 1);
    }
}