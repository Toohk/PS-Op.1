const inputSearch = document.getElementById("search-input")
const background = document.getElementById("background")
const resultBlock = document.getElementById("block-result")


inputSearch.onfocus =()=>{
    background.style.height = '100vh';
    resultBlock.style.height = '70vh';
}
inputSearch.onblur =()=>{
    background.style.height = '650px'
    resultBlock.style.height = '2px';
}