@twillBlockTitle('Quote')
@twillBlockIcon('text')
@twillBlockGroup('twill')

<x-twill::input
    name="quote"
    type="textarea"
    label="Quote text"
    :maxlength="250"
    :rows="4"
/>

<x-twill::input
    name="author"
    label="Quote author"
/>
