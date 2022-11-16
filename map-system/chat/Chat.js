class Chat
{
    #STYLE_CLASS_NAME = "message-messageStyle";
    #MESSAGE_CLASS_NAME = "message-profile-icon-"

    constructor(messagesData)
    {
        this.messagesData = messagesData;
        this.messageStyleSheetContent = "";
    }

    updateChat()
    {
        const message = new Message();
        message.clearPreviousMessages();

        let messageClassName;
        this.messageStyleSheetContent = "";
        for (let i = 0; i < this.messagesData.length; i++) {
            messageClassName = this.#MESSAGE_CLASS_NAME + i;

            message.message = this.messagesData[i].message;
            message.initials = this.messagesData[i].initials;
            message.elementClassName = messageClassName;
            message.sentByUser = this.messagesData[i].message_sent_by_user;
            
            message.createMessageElement();

            this.messageStyleSheetContent += '.' + messageClassName + ' { background-color: ' + this.messagesData[i].color + '; }';
        }

        this.#updateMarkerStyle();
    }

    #updateMarkerStyle()
    {
        const messageStyle = new Style(this.#STYLE_CLASS_NAME);
        messageStyle.removeStyle();

        messageStyle.styleSheetContent = this.messageStyleSheetContent;
        messageStyle.createStyle();
    }
}