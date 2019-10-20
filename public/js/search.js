const liveSearch = document.getElementById("livesearch")


const showResult = (str) => {
    if (str.length==0) {
      liveSearch.innerHTML="";
      liveSearch.style.border="0px";
      return;
    }
    if (window.XMLHttpRequest) {
      // code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    } else {  // code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
      if (this.readyState==4 && this.status==200) {
        const entities = JSON.parse(this.responseText).entities;
        let results = '';
        for (let [key, value] of Object.entries(entities)) {
            let result = `<div class="result font-medium card"><a class="black" href="/p/${key}"> <strong> ${value} </strong></a> </div>`
            results+= result
        }
        liveSearch.innerHTML=results;
      }
    }
    xmlhttp.open("GET","/search/?q="+str,true);
    xmlhttp.send();
  }

