const navBar = document.getElementById("navBar")
const btnMenu = document.getElementById("btn-menu")
const modalBackground = document.getElementById("modal-background")


btnMenu.onclick = () => {
    navBar.style.display = "block";
    modalBackground.style.display = "block";
}
window.onclick = (event) => {
    if (event.target == modalBackground) {
      navBar.style.display = "none";
      modalBackground.style.display = "none";
    }
  }  

  const display = (x) => {
    if (x.matches) {
        navBar.style.display = "block";
    } else {
        navBar.style.display = "none";
        modalBackground.style.display = "none";
    }
  }
  
  const x = window.matchMedia("(min-width: 968px)")
  display(x)
  x.addListener(display)