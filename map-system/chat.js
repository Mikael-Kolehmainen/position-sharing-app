function updateChat(messagesArr, initialsArr, colorsArr) {
    removeChilds(document.getElementById('messages'));

    // Create structure of message
    /*
        <div class='message'>
            <div class='profile'>
                <p>MK</p>
            </div>
            <p class='text'>Hello, this is a placeholder message.</p>
        </div>
    */
    styleSheetContent = "";
    for (let i = 0; i < messagesArr.length; i++) {
        const message = document.createElement("div");
        message.classList.add('message');
        const profile = document.createElement("div");
        profile.classList.add('profile');
        message.appendChild(profile);
        const initialsText = document.createElement("p");
        profile.appendChild(initialsText);
        const messageText = document.createElement("p");
        messageText.classList.add('text');
        message.appendChild(messageText);

        messageText.innerHTML = messagesArr[i];
        initialsText.innerHTML = initialsArr[i];

        const messages = document.getElementById("messages");
        messages.appendChild(message);

        classNameOtherUsers = 'other-profile-icon-' + i;
        profile.classList.add(classNameOtherUsers);
        styleSheetContent += '.' + classNameOtherUsers + '{ background-color: ' + colorsArr[i] + '; }';
    }
    createStyle(styleSheetContent, 'js-style');
}