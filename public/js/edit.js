
const btnsave = document.getElementById("btn-save")

const savePage = () => {

    const params = {
        id : document.getElementById("check-id").value,
        title : document.getElementById("editor-title").innerHTML,
        intro : document.getElementById("editor-intro").innerHTML,
        content : document.getElementById("editor-content").innerHTML,
        tags : tags
    }
    
    const http = new XMLHttpRequest()
    http.open('POST', '/save-edit')
    http.setRequestHeader('Content-type', 'application/json')
    http.send(JSON.stringify(params)) // Make sure to stringify
    http.onload = function() {
        alert('saved page')
    }
    
}

btnsave.onclick=()=>{
    console.log('okk')
    savePage();
    
}