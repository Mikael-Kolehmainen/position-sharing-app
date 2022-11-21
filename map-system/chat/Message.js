class Message 
{
    #MESSAGE_CLASS_NAME = "message";
    #SENT_CLASS_NAME = "sent";
    #PROFILE_CLASS_NAME = "profile";
    #DATE_CLASS_NAME = "date";
    #TEXT_CONTAINER_CLASS_NAME = "text-container";
    #MESSAGES_ID = "messages";

    constructor(message, initials, imagePath, elementClassName, timeOfMessage, dateOfMessage, sentByUser)
    {
        this.message = message;
        this.initials = initials;
        this.imagePath = imagePath;
        this.elementClassName = elementClassName;
        this.timeOfMessage = timeOfMessage;
        this.dateOfMessage = dateOfMessage;
        this.sentByUser = sentByUser;
    }
    
    /*
        <div class='date'>
            <p>00.00.0000</p>
        </div>
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
    createDateElement()
    {
        const date = document.createElement("div");
        date.classList.add(this.#DATE_CLASS_NAME);
        const dateText = document.createElement("p");
        date.appendChild(dateText);

        dateText.innerText = this.dateOfMessage;

        const messages = document.getElementById(this.#MESSAGES_ID);
        messages.appendChild(date);
    }

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
        message.appendChild(textContainer);

        if (this.message != null) {
            const messageText = document.createElement("p");
            messageText.classList.add("text");
            textContainer.appendChild(messageText);
            messageText.innerText = this.message;
        } else if (this.imagePath != null) {
            const messageImage = document.createElement("img");
            messageImage.classList.add("image");
            textContainer.appendChild(messageImage);

            let relativeImagePath = "./../" + this.imagePath;
            messageImage.addEventListener("error", function() { messageImage.src = "./../media/image-not-found.png" });
            messageImage.src = relativeImagePath;
        }

        const timeText = document.createElement("p");
        timeText.classList.add("time");
        textContainer.appendChild(timeText);

        initialsText.innerText = this.initials;
        timeText.innerText = this.timeOfMessage;

        if (this.initials == null) {
            initialsText.innerText = "Removed User";
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