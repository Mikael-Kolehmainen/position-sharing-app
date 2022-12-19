class Instructions
{
    constructor(instructionText)
    {
        this.instructionText = instructionText;
        this.instructionTextElement = document.getElementById('instruction-text');
    }

    show()
    {
        this.instructionTextElement.style.display = 'block';
    }

    hide()
    {
        this.instructionTextElement.style.display = 'none';
    }

    add()
    {
        this.instructionTextElement.innerText += this.instructionText;
    }

    replace(text)
    {
        this.instructionTextElement.innerText = text;
    }
}