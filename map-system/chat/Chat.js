class Chat
{
    #STYLE_CLASS_NAME = "message-style";
    #MESSAGE_CLASS_NAME = "message-profile-icon-"

    constructor(messagesData)
    {
        this.messagesData = messagesData;
    }

    updateChat()
    {
        const style = new Style(this.#STYLE_CLASS_NAME);
        style.removeStyle();

        Message.clearPreviousMessages();

        let styleSheetContent = "";
        for (let i = 0; i < this.messagesData.length; i++) {
            const messageElementClassName = this.#MESSAGE_CLASS_NAME + i;

            const message = new Message(this.messagesData[i].message, this.messagesData[i].initials, messageElementClassName);
            message.createMessageElement();

            styleSheetContent += '.' + messageElementClassName + ' { background-color: ' + this.messagesData[i].color + '; }';
        }

        style.styleSheetContent = styleSheetContent;
        style.createStyle();
    }
}