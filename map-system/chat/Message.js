class Message 
{
    constructor(message, initials)
    {
        this.message = message;
        this.initials = initials;
    }
    
    /*
        <div class='message'>
            <div class='profile'>
                <p>MK</p>
            </div>
            <p class='text'>Hello, this is a placeholder message.</p>
        </div>
    */
    createMessage()
    {
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

        messageText.innerHTML = this.message;
        initialsText.innerHTML = this.initials;

        const messages = document.getElementById("messages");
        messages.appendChild(message);
    }
}