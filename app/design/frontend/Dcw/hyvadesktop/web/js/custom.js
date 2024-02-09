window.addEventListener("scroll", function() {
  var header = document.querySelector('header.page-header');
  var body = document.querySelector("body");
  const header_top = document.querySelector('.header_top');
  const notification4 = document.querySelector('.notification4');
  var top_1 = document.querySelector("header.page-header > .panel.wrapper");
  var content = document.getElementById("textContent");
  

  if(header_top && notification4){
    if ((header_top.textContent.length > 10) && (notification4.textContent.length > 10)) {
      if (window.pageYOffset > 76) {
          //document.querySelector("#header").style.cssText = "position:fixed;left:0;top:0;width:100%;transition: all ease-in-out 0.5s";
          //document.querySelector(".page-wrapper").style.cssText = "padding-top:112px";
          document.querySelector('body').classList.add('sticki');
        } else {
          //document.querySelector("#header").style.cssText = "position:static;transition: all ease-in-out 0.5s";
          //document.querySelector(".page-wrapper").style.cssText = "padding-top:0px";
          document.querySelector('body').classList.remove('sticki');
        }
    }
  }
  
  if(header_top && notification4){
    if ((header_top.textContent.length > 10) && (notification4.textContent.length < 10)) {
      if (window.pageYOffset > 36) {
         //document.querySelector("#header").style.cssText = "position:fixed;left:0;top:0;width:100%;transition: all ease-in-out 0.5s";
          //document.querySelector(".page-wrapper").style.cssText = "padding-top:112px";
          document.querySelector('body').classList.add('sticki');
        } else {
          //document.querySelector("#header").style.cssText = "position:static;transition: all ease-in-out 0.5s";
          //document.querySelector(".page-wrapper").style.cssText = "padding-top:0px";
          document.querySelector('body').classList.remove('sticki');
        }
    }
  }

  if(header_top && !notification4){
    if (header_top.textContent.length > 10) {
      if (window.pageYOffset > 36) {
          //document.querySelector("#header").style.cssText = "position:fixed;left:0;top:0;width:100%;transition: all ease-in-out 0.5s";
          //document.querySelector(".page-wrapper").style.cssText = "padding-top:112px";
          document.querySelector('body').classList.add('sticki');
        } else {
          //document.querySelector("#header").style.cssText = "position:static;transition: all ease-in-out 0.5s";
          //document.querySelector(".page-wrapper").style.cssText = "padding-top:0px";
          document.querySelector('body').classList.remove('sticki');
        }
    }
  }

});