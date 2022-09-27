function updateChat(messagesData) {
    // Call function from Style.js class that removes stylesheet

    // Put this function to Message.js class
    removeChilds(document.getElementById('messages'));

    styleSheetContent = "";
    for (let i = 0; i < messagesData.length; i++) {
        const message = new Message(messagesData[i].message, messagesData[i].initials);
        message.createMessage();

        classNameOtherUsers = 'other-profile-icon-' + i;
        profile.classList.add(classNameOtherUsers);
        styleSheetContent += '.' + classNameOtherUsers + '{ background-color: ' + messagesData[i].color + '; }';
    }

    // Create Style.js class then call method
    createStyle(styleSheetContent, 'js-style');
}