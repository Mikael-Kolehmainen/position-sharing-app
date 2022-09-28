function updateChat(messagesData) 
{
    const style = new Style('message-style');
    style.removeStyle();

    Message.clearMessages();

    let styleSheetContent = "";
    for (let i = 0; i < messagesData.length; i++) {
        const messageElementClassName = 'message-profile-icon-' + i;

        const message = new Message(messagesData[i].message, messagesData[i].initials, messageElementClassName);
        message.createMessageElement();

        styleSheetContent += '.' + messageElementClassName + ' { background-color: ' + messagesData[i].color + '; }';
    }

    style.styleSheetContent = styleSheetContent;
    style.createStyle();
}