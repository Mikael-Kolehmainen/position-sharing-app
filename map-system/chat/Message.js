class Message 
{
    #MESSAGE_CLASS_NAME = "message";
    #PROFILE_CLASS_NAME = "profile";
    #MESSAGES_ID = "messages";

    constructor(message, initials, elementClassName)
    {
        this.message = message;
        this.initials = initials;
        this.elementClassName = elementClassName;
    }
    
    /*
        <div class='message'>
            <div class='profile'>
                <p>MK</p>
            </div>
            <p class='text'>Hello, this is a placeholder message.</p>
        </div>
    */
    createMessageElement()
    {
        const message = document.createElement("div");
        message.classList.add(this.#MESSAGE_CLASS_NAME);
        const profile = document.createElement("div");
        profile.classList.add(this.#PROFILE_CLASS_NAME);
        message.appendChild(profile);
        const initialsText = document.createElement("p");
        profile.appendChild(initialsText);
        const messageText = document.createElement("p");
        messageText.classList.add("text");
        message.appendChild(messageText);

        messageText.innerHTML = this.message;
        initialsText.innerHTML = this.initials;

        const messages = document.getElementById(this.#MESSAGES_ID);
        messages.appendChild(message);

        profile.classList.add(this.elementClassName);
    }

    static clearPreviousMessages()
    {
        removeChilds(document.getElementById(this.#MESSAGES_ID));
    }
}