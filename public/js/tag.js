const inputTag = document.getElementById("input-tag")
const resultTagBlock = document.getElementById("search-tag-result")
const tagList = document.getElementById("tag-list")
const btnAddTag = document.getElementById("btn-add-tag")

let searchTagResult = [];

let tags = [];
let tagsHtml = [];
let tagsListContent = "";



const tagResult = (str) => {
    
    if (window.XMLHttpRequest) {
      // code for IE7+, Firefox, Chrome, Opera, Safari
      xml=new XMLHttpRequest();
    } else {  // code for IE6, IE5
      xml=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xml.onreadystatechange=function() {
      if (this.readyState==4 && this.status==200) {
       
        const entities = JSON.parse(this.responseText).entities;
        let results = '';

        for (let [key, value] of Object.entries(entities)) {

          let tagExist = tags.filter(function( obj ) {
            return obj.id == key;
          });
          
          let tagClass = '';
          let result ='';

          if(tagExist[0] !== undefined) {
            tagClass = 'selectedTag'
          }
          if(key == 'error'){
            result = `
              <div class=" result-line-tag ${tagClass}">
                ${value}
              </div>`
          }else{
            result = `
              <div onClick= "select('${key}','${value}')" class=" result-line-tag ${tagClass}">
                ${value}
              </div>`
          }
          
          
            results+= result
        }

        resultTagBlock.innerHTML=results;
        searchTagResult =document.getElementsByClassName('result-line-tag')
      
      }
    }
    xml.open("GET","/search-tag/?q="+str,true);
    xml.send();
}

const select = (id, name )=>{
  
  let tagExist = tags.filter(function( obj ) {
    return obj.id == id;
  });

  if(tagExist[0] == undefined) {

    let tag = {  
        'html':`<div class="tag"> ${name} <button onClick="removeTag('${name}')"> X </button>  </div>`,
        'id': id,
        'name': name
    };
    
    tags.push(tag)

    tagsHtml = [];
    tags.forEach(tagObject => {
      tagsHtml.push(tagObject.html)
    });

    tagsListContent = "";
    tagsHtml.forEach(tagHtml => {
      tagsListContent += `${tagHtml}`
    });

    tagList.innerHTML = tagsListContent

  }
 // tagResult(inputTag.value)
}

const removeTag = (name)=> {

  const index = tags.findIndex(obj => obj.name === name);

  if(index!=-1){
    tags.splice(index, 1);
  }

  tagsHtml = [];
  tags.forEach(tagObject => {
    tagsHtml.push(tagObject.html)
  });

  tagsListContent = "";
  tagsHtml.forEach(tagHtml => {
    tagsListContent += `${tagHtml}`
  });

  tagList.innerHTML = tagsListContent
  tagResult(inputTag.value)
};

const addTag =() => {
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    reqTag=new XMLHttpRequest();
  } else {  // code for IE6, IE5
    reqTag=new ActiveXObject("Microsoft.XMLHTTP");
  }

  reqTag.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      tagResult(inputTag.value)
    }
  }

  reqTag.open("GET","/tag/add/?q="+inputTag.value,true);
  reqTag.send();
  
}

const setTags = ()=>{
  console.log(document.getElementsByClassName("tag").length)
  
  for (let i= 0; i < document.getElementsByClassName("tag").length; i++){

    let id = document.getElementsByClassName("tag")[i].childNodes[1].value;
  let name = document.getElementsByClassName("tag")[i].childNodes[0].data;
  

  let tag = {  
    'html':`<div class="tag"> ${name} <button onClick="removeTag('${name}')"> X </button>  </div>`,
    'id': id,
    'name': name
};

tags.push(tag)

tagsHtml = [];
    tags.forEach(tagObject => {
      tagsHtml.push(tagObject.html)
    });
    
    tagsListContent = "";
    tagsHtml.forEach(tagHtml => {
      tagsListContent += `${tagHtml}`
    });

    

  }
  tagList.innerHTML = tagsListContent
}



inputTag.onfocus =()=> {tagResult(inputTag.value)}
btnAddTag.onclick =()=> {addTag()}

setTags();




    

