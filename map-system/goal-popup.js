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

    const titleRow = document.createElement("tr");
    const titleCell_1 = document.createElement("td");
    const titleCell_2 = document.createElement("td");
    titleCell_1.innerText = "User";
    titleCell_2.innerText = "Goal";

    usersTable.appendChild(titleRow);
    titleRow.appendChild(titleCell_1);
    titleRow.appendChild(titleCell_2);

    for (let i = 0; i < markerInitialsArr.length; i++) {
        const userRow = document.createElement("tr");
        const userCell_1 = document.createElement("td");
        const userCell_2 = document.createElement("td");
        
        const userProfile = document.createElement("div");
        const initialsText = document.createElement("p");
        initialsText.innerHTML = markerInitialsArr[i];
        userProfile.classList.add('profile');
        
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.id = "userindex-" + i;

        usersTable.appendChild(userRow);
        userRow.appendChild(userCell_1);
        userRow.appendChild(userCell_2);
        userCell_1.appendChild(userProfile);
        userProfile.appendChild(initialsText);
        userCell_2.appendChild(checkbox);

        const className = 'goal-menu-user-marker-' + i;
        userProfile.classList.add(className);
        styleSheetContent += '.' + className + '{ background-color: ' + markerColorsArr[i] + '; }';
    }
    createStyle(styleSheetContent, 'js-style');
}

// make onchange function that checks what value checkbox has and updates goal array accordingly