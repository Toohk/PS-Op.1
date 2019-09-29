const picture= document.getElementById("daily-picture");
const title= document.getElementById("daily-picture-title");
const description= document.getElementById("daily-picture-description");
const req = new XMLHttpRequest();

req.open('GET', 'https://api.nasa.gov/planetary/apod?api_key=cK14Bgf0A4AWSv1GZVxTTSweWk5RZSnI0Wgf0KKz');
req.send()

req.onreadystatechange = function(event) {
    if (this.readyState === XMLHttpRequest.DONE) {
        if (this.status === 200) {
            const data = JSON.parse(this.responseText);
            title.innerHTML = data.title;
            description.innerHTML = data.explanation;
            picture.src = data.url;
        }
    }
};
