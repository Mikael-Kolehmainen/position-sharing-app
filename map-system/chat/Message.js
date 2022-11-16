class Message 
{
    #MESSAGE_CLASS_NAME = "message";
    #SENT_CLASS_NAME = "sent";
    #PROFILE_CLASS_NAME = "profile";
    #TEXT_CONTAINER_CLASS_NAME = "text-container";
    #MESSAGES_ID = "messages";

    constructor(message, initials, elementClassName, timeOfMessage, sentByUser)
    {
        this.message = message;
        this.initials = initials;
        this.elementClassName = elementClassName;
        this.timeOfMessage = timeOfMessage;
        this.sentByUser = sentByUser;
    }
    
    /*
        <div class='message'>
            <div class='profile'>
                <p>MK</p>
            </div>
            <div class='text-container'>
                <p class='text'>Hello, this is a placeholder message.</p>
                <p class='time'>00:00</p>
            </div>
        </div>
    */
    createMessageElement()
    {
        const message = document.createElement("div");
        message.classList.add(this.#MESSAGE_CLASS_NAME);

        if (this.sentByUser) {
            message.classList.add(this.#SENT_CLASS_NAME);
        }

        const profile = document.createElement("div");
        profile.classList.add(this.#PROFILE_CLASS_NAME);
        message.appendChild(profile);
        const initialsText = document.createElement("p");
        profile.appendChild(initialsText);
        const textContainer = document.createElement("div");
        textContainer.classList.add(this.#TEXT_CONTAINER_CLASS_NAME);
        message.appendChild(textContainer)
        const messageText = document.createElement("p");
        messageText.classList.add("text");
        textContainer.appendChild(messageText);
        const timeText = document.createElement("p");
        timeText.classList.add("time");
        textContainer.appendChild(timeText);

        messageText.innerHTML = this.message;
        initialsText.innerHTML = this.initials;
        timeText.innerHTML = this.timeOfMessage;

        if (this.initials == null) {
            initialsText.innerHTML = "Removed User";
        }

        const messages = document.getElementById(this.#MESSAGES_ID);
        messages.appendChild(message);

        profile.classList.add(this.elementClassName);
    }

    clearPreviousMessages()
    {
        removeChilds(document.getElementById(this.#MESSAGES_ID));
    }
}