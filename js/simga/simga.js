
function T_albumObj() {
  this.imglist = [];
  this.imgpreloader = []; 
  this.titlelist = [];  
  this.origilist = [];  
  this.thumblist = [];  
  this.desclist = [];

  this.listpos = -1;
  this.numimg = 0;
  this.do_resize = true;
}

var curr_albumObj = new T_albumObj();

//let imgInPopup = document.getElementById('imgInPopup');
// ------------------------------------------------------------------------



function toggleOverlay(element = null, overlay_bin_id = "overlay_pasteboard"){
  //console.log(element);
  if( element == null ) element = document.getElementById("actionmenu");
  console.log(element);

  if(document.getElementById("shadow").style.display == "block") {
    document.getElementById("shadow").style.display = "none";

    // may apply this clear func for all events?
    for (var i = 0; i < document.getElementById('shadow').children.length; i++) {
      //console.log(foo.children[i].tagName);
      document.getElementById(overlay_bin_id)
      .appendChild(document.getElementById('shadow').children[i]);
    }

    document.getElementById("container").style.display = "block";
    //document.getElementById("actionmenu").style.display = "none";
  }else{
    document.getElementById("shadow").appendChild(element);
    //element.style.display='block';
    document.getElementById("shadow").style.display = "block";
    document.getElementById("container").style.display = "none";
    //document.getElementById("actionmenu").style.display = "block";
    //document.getElementById("component-menu").style.display = "none";
  }
}


function showImg( src ) {
  //$(imgInPopup).hide(0);
  //showLoadbarLayer(); // console.log(src);
  
  if( typeof src != 'undefined' ) {
    //$('#imgInPopup').attr('src', src );
    imgInPopup.src = src;
  }else{
    //$('#imgInPopup').attr('src', curr_albumObj.imglist[ curr_albumObj.listpos ] );
    imgInPopup.src = curr_albumObj.imglist[ curr_albumObj.listpos ];
    //console.log( curr_albumObj.imglist[ curr_albumObj.listpos ] );
  }

  //$(imgInPopup).show();

  imgInPopup.title = (curr_albumObj.desclist[curr_albumObj.listpos])?
    curr_albumObj.desclist[curr_albumObj.listpos] : 
    curr_albumObj.titlelist[curr_albumObj.listpos];

  toggleOverlay(document.getElementById('imgcontainer'));

}

// ------------------------------------------------------------------------


  function showNextImg(event) {
    if(curr_albumObj.numimg < 2) return false;
    //alert('showNextImg');
    //console.log(curr_albumObj.listpos);
    event.preventDefault(); event.stopPropagation();

    curr_albumObj.listpos++;
    if(curr_albumObj.listpos >= curr_albumObj.numimg) curr_albumObj.listpos = 0;

    imgInPopup.style.opacity = 0;
    setTimeout(function(){
      imgInPopup.style.left = '0px';
      imgInPopup.src = curr_albumObj.imglist[curr_albumObj.listpos];
      imgInPopup.style.opacity = 1;
    }, 310);
    
    imgInPopup.title = (curr_albumObj.desclist[curr_albumObj.listpos])?
    curr_albumObj.desclist[curr_albumObj.listpos] : 
    curr_albumObj.titlelist[curr_albumObj.listpos];
    
  }
  function showPrevImg(event) {
    if(curr_albumObj.numimg < 2) return false;
    //alert('showNextImg');
    //console.log(curr_albumObj.listpos);
    event.preventDefault(); event.stopPropagation();

    curr_albumObj.listpos--;
    if (curr_albumObj.listpos < 0 ) curr_albumObj.listpos = curr_albumObj.numimg - 1;

    imgInPopup.style.opacity = 0;
    setTimeout(function(){
      imgInPopup.style.left = '0px';
      imgInPopup.src = curr_albumObj.imglist[curr_albumObj.listpos];
      imgInPopup.style.opacity = 1;
    }, 310);

    imgInPopup.title = (curr_albumObj.desclist[curr_albumObj.listpos])?
    curr_albumObj.desclist[curr_albumObj.listpos] : 
    curr_albumObj.titlelist[curr_albumObj.listpos];
    
  }
