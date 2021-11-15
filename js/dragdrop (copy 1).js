//var current_target;

function dragover(event) {
    console.log("dragover: ");
    //event.preventDefault();
    event.stopPropagation();
    //if(!event.currentTarget.draggable) return false;
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

// ERR not generic, tied to tree... :(
function drop(event) {
    console.log("drop "+event.dataTransfer.getData("text"));
    event.preventDefault();
    event.stopPropagation();
    var data = event.dataTransfer.getData("text").split(" ");

    var target = event.currentTarget;

    target.classList.remove('dragover');
    //alert(data +"\n"+ event.currentTarget.dataset.dragdata);

    if(target.getAttribute("role") != "tree")
    {
        treeroot = get_treeroot(target);
    }else{
        treeroot = target;
    }

    var prev_treeroot = document.querySelector("[id='"+data[2]+"']");

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            //if item updated successfully
        }
    };

    if(data[1] == treeroot.dataset.module){
        // parent - child relationship
        xhttp.open("GET", '?r=bony\\Tree/drop&drop[model]='+data[1]+"&drop[id]="+data[0]+"&drop[parent]="+target.dataset.id ,true);

        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                
                // NEW parent .....................................................
                var treeitem = target;
                var treeitem_childs = (target == treeroot)? target : target.nextElementSibling;
                _xhttp_load_treeitemchilds(treeitem, treeitem_childs);

                // PREV parent .....................................................
                //console.log(tree_root); console.log("[data-id='"+data[0]+"']");

                if(target != treeroot || treeroot != prev_treeroot){ //if not loaded by previous step
                    var droppedItem = prev_treeroot.querySelector("[data-id='"+data[0]+"']");
                    var droppedItemChilds = droppedItem.nextElementSibling;
                    var prev_treechilds = droppedItem.parentNode;
                    if(prev_treechilds.getAttribute("role") == "tree") {
                        prev_treechilds.removeChild(droppedItem);
                        prev_treechilds.removeChild(droppedItemChilds);
                        console.log("tree");
                    }else{
                        console.log("NOT tree");
                        //prev_treechilds = prev_treechilds.previousElementSibling;
                        //var prev_treeitem_childs = prev_treechilds.nextElementSibling;
                        _xhttp_load_treeitemchilds(prev_treechilds.previousElementSibling,prev_treechilds);
                    }
                    //TODO: refresh target root too!!!!!
                    if(treeroot != prev_treeroot){
                        var duplicate = treeroot.querySelector("[data-dragdata='"+data[0]+" "+data[1]+"']");
                        var duplicateChilds = duplicate.nextElementSibling;
                        var duplicateParent = duplicate.parentNode;
                        duplicateParent.removeChild(duplicate);
                        duplicateParent.removeChild(duplicateChilds);
                    }
                }

            }
        };

        xhttp.send();
    }else{
        // module-s custom drop handler
        xhttp.open("GET", "?r="+treeroot.dataset.module+"/drop&drop[model]="+data[1]+"&drop[id]="+data[0] ,true);
        xhttp.send();
    }
    

    
}