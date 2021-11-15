//var current_target;

function dragover(event) {
    console.log("dragover: ");
    //event.preventDefault();
    event.stopPropagation();
    //if(!event.currentTarget.draggable) return false;
    // check if drop of this type supported:
    if(!event.currentTarget.dataset.droptarget){
        //console.log("no dataset");
        elem = get_treeroot(event.currentTarget);
        if(!elem.dataset.droptarget) {
            console.log("no droptarget @ all");
            return false;
        }
    }else elem = event.currentTarget;

    var data = event.dataTransfer.getData("text").split(" ");

    // if modelName exist is droptarget string:
    if(elem.dataset.droptarget.indexOf(data[1]) != -1){
        // check if not drop onto self:
        var buf = event.currentTarget.dataset.dragdata + " " + get_treeroot(event.currentTarget).id;
        if(buf == event.dataTransfer.getData("text")) return false;
        // do:
        event.preventDefault();
        event.currentTarget.classList.add('dragover');
    }
}

function dragleave(event){
    //console.log("dragleave");
    //event.preventDefault();
    event.currentTarget.classList.remove('dragover');
}

function dragstart(event) {
    console.log("dragstart");
    //event.preventDefault();
    treeroot = get_treeroot(event.currentTarget);
    event.dataTransfer.setData("text", event.currentTarget.dataset.dragdata + " " + treeroot.id);
}

function test(){
    alert("TEST");
}

// ERR not generic, tied to tree... :(
// move to treegrid, add to _treeitem.php
function drop(event) { 
    console.log("drop "+event.dataTransfer.getData("text"));
    event.preventDefault();
    event.stopPropagation();


    
}