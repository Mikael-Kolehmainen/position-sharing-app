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

        let messageClassName, datesOfMessages = [];
        this.messageStyleSheetContent = "";
        for (let i = 0; i < this.messagesData.length; i++) {
            messageClassName = this.#MESSAGE_CLASS_NAME + i;

            message.message = this.messagesData[i].message;
            message.initials = this.messagesData[i].initials;
            message.imagePath = this.messagesData[i].imagepath;
            message.elementClassName = messageClassName;
            message.timeOfMessage = this.messagesData[i].timeofmessage;
            message.sentByUser = this.messagesData[i].message_sent_by_user;

            if (!datesOfMessages.includes(this.messagesData[i].dateofmessage)) {
                let messageDateObj = new Date(this.messagesData[i].dateofmessage);
                let day = messageDateObj.getDate();
                let month = messageDateObj.getMonth() + 1;
                let year = messageDateObj.getFullYear();
                let messageDate = day + "." + month + "." + year;

                message.dateOfMessage = messageDate;
                message.createDateElement();
                datesOfMessages.push(this.messagesData[i].dateofmessage);
            }
            
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