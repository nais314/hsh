/**
 *  treeitem_cb = treeitem_callback
 */

/* function treeitem_cb(key){
    event.stopPropagation();

    document.getElementById("treeitem_childs-"+key).style.display = (document.getElementById("treeitem_childs-"+key).style.display=="block")? "none":"block";

    this.innerHTML=(this.innerHTML=="+")? "-": "+";
}
 */


function get_treeroot(element){
    /* console.log(element.getAttribute("role"));
    if(element == null) return false;*/
    if(element != null && element.getAttribute("role") != "tree")
    while (element.getAttribute("role") != "tree") {
        element = element.parentNode;
    }
    return element;
}






function ti_key_cb(event){
    //patch document scroll:
    //cannot patch doc scroll on FF... dont want document.prevent... because...
    //event.prevendDefault=true;


    switch (event.key) {
        case "Enter":
            event.target.click();
            break;
        case " ":
            break;
        case "Insert":

            break;
        case "Delete":

            break;


        case "ArrowLeft": console.log("ArrowLeft");
            event.preventDefault();
            event.stopPropagation();
            
            /* //console.log(event.target.parentNode.previousElementSibling);
            //scenarios: close ti, jump to top, jump up

            // jump to top
            if(event.target.previousElementSibling != null &&
                event.target.previousElementSibling.getAttribute('role') == 'treeitem'){
                    var prev_element = event.target.previousElementSibling;
                    while (prev_element != null) {
                        element = prev_element;
                        prev_element = element.previousElementSibling;
                    }
                    if (element != null) { element.focus(); }
                }
            // jump up
            try{
            if(event.target.parentNode.previousElementSibling.getAttribute('role') == 'treeitem'){
                event.target.parentNode.previousElementSibling.focus();
                return true;
            }
            }catch(err){ console.log('End')} */

            break;




        case "ArrowUp": console.log("ArrowUp\n");
            event.preventDefault();
            event.stopPropagation();
            var element = null;
            /**
             * if previousElementSibling.getAttribute('role') == 'treeitem'
             * else jump to END
             */
            try {
                if (event.target.previousElementSibling != null &&
                    event.target.previousElementSibling.getAttribute('role') == 'treeitem'){
                    element = event.target.previousElementSibling;
                    element.focus();
                }else{
                    var prev_element = event.target.nextElementSibling;
                    while (prev_element != null && 
                        prev_element.getAttribute('role') == 'treeitem'
                        ){
                            element = prev_element;
                            prev_element = prev_element.nextElementSibling;
                        }
                    element.focus();
                }
            }
            catch(err){}

            break;

        case "ArrowRight":
            event.preventDefault();
            event.stopPropagation();

            break;
        case "ArrowDown": console.log("ArrowDown\n");
            event.preventDefault();
            event.stopPropagation();
            //console.log("ArrowDown");
            //console.log(event.target.nextElementSibling.nextElementSibling);
            var element = null;
            /**
             * if nextElementSibling.getAttribute('role') == 'treeitem'
             * else jump to END
             */
            try {
                if (event.target.nextElementSibling != null &&
                    event.target.nextElementSibling.getAttribute('role') == 'treeitem'){
                    element = event.target.nextElementSibling;
                    element.focus();
                }else{
                    var prev_element = event.target.previousElementSibling;
                    while (prev_element != null && 
                        prev_element.getAttribute('role') == 'treeitem'
                        ){
                            element = prev_element;
                            prev_element = prev_element.previousElementSibling;
                        }
                    element.focus();
                }
            }
            catch(err){}

            break;
        case "PageDown":

            break;
        case "PageUp":

            break;

        default:
            break;
    }
}//............................................





/**
 * Default callback for FORMER tree components
 * @param {*} event
 * @param {*} ti_selfid
 */
function ti_activate(event, ti_selfid, treeid){
    event.stopPropagation();

    // self as selfs parent illogical:
    //console.log(">> "+event.currentTarget.dataset.id+" - "+ti_selfid);

    if(event.currentTarget.id == ti_selfid) return false;
    if(document.querySelector(('#form_'+treeid+' input[type="hidden"]')).value!=event.target.id){
        //.................
        try{
            document.querySelector("#overlay_"+treeid + " .iselected")
            .classList.remove('iselected');
        }catch(err){};

        event.target.classList.add('iselected');

        //.................
        try{
            // change Form input value for store
            document.querySelector(('#form_'+treeid+' input[type="hidden"]')).value=event.target.id;

            // set human readable Form value
            document.querySelector(('#form_'+treeid+' input[type="text"]')).value = event.target.dataset.value;
        }catch(err){}
    }
    toggleActionMenu(); // close

    //console.log("#form_"+treeid+" input[type='text']");
    //inp = document.querySelector("#form_"+treeid+" input[type='text']");
    //console.log(inp);
    //inp.onchange();

}






function treegrid_init(selector){
    // init callbacks on nodes
    // for same model: select all except self:
    // @Todo: for diff model select all

    selector = (typeof(selector) == "string") ? document.querySelector(selector) : selector;
    tree_root = get_treeroot(selector);
    //console.log('treegrid_init: ' + tree_root);
    ti_selfid = tree_root.dataset.ti_selfid;
    //console.log('ti_selfid: ' + ti_selfid);
    treeid = tree_root.id;

    //..................................
    //var buf = tree_root.querySelectorAll(".treeitem:not([id='" + ti_selfid+"'])");

    var buf = selector.querySelectorAll(".treeitem");

    for(i=0; i< buf.length; i++){ //console.log(i + '\n');
            if(tree_root.dataset.action != '' && typeof tree_root.dataset.action != 'undefined')
                buf[i] .addEventListener('click', function(event){
                    window[tree_root.dataset.action](event, ti_selfid, treeid);
                });

        // keyboard nav functions for ALL treeitems incl self
        buf[i] .addEventListener('keydown', ti_key_cb, true);
    }


    //............................

    //! ti_btn_create(selector);

    //! ti_btn_init_cb(selector);
    //function openToSelected(){};

}










/* 
// maybe overhead? should merge with init ??
function ti_btn_init_cb(selector, tree_root=null){


    selector = (typeof(selector) == "string") ? document.querySelector(selector) : selector;




    var ti_btns = selector.querySelectorAll('.ti_btn');//, i;

    for (i = 0; i < ti_btns.length; ++i) {
        ti_btns[i].addEventListener('click', function (event){
            event.stopPropagation();

            //console.log("ti_btn_cb: "+event.target.parentNode.id); // works ok
            //console.log("ti_btn_cb: "+event.target.parentNode.nextElementSibling.id);
            var treeitem_childs = event.target.parentNode.nextElementSibling;
            var treeitem = event.target.parentNode;

            //console.log('isopen?: '+treeitem.dataset.is_open);
            if(treeitem.dataset.has_child > 0)
                if( parseInt( treeitem.dataset.is_open) > 0){
                    // close:
                    this.innerHTML='+';
                    treeitem.dataset.is_open = 0;
                    treeitem_childs.style.display='none';

                }else if( parseInt( treeitem.dataset.is_open) == 0){
                    // has child, open:
                    this.innerHTML='-';
                    treeitem.dataset.is_open = 1;
                        //console.log(treeitem.dataset.childs_loaded);
                    if(treeitem.dataset.childs_loaded != 'true' || event.ctrlKey) // it parses as strint :-O ???
                    {// ajax load childs to element
                        var xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                                treeitem_childs.innerHTML = this.responseText;
                                treeitem.dataset.childs_loaded = true; //cache
                                //init callbacks
                                treegrid_init(treeitem_childs);
                            }
                        };

                        if(tree_root == null) tree_root = get_treeroot(selector);
                        //ti_selfid = tree_root.dataset.ti_selfid;
                        //treeid = tree_root.id;
                        //treeTableName = tree_root.dataset.tablename;

                        // switch on tree_root type?
                        xhttp.open("GET", "?r="+tree_root.dataset.loader+"&tree[parent]="+treeitem.id+"&tree[level]="+treeitem.dataset.level+"&tree[tableName]="+tree_root.dataset.tablename+"&tree[template]="+tree_root.dataset.template+"&tree[droptarget]="+tree_root.dataset.droptarget, true);
                        xhttp.send();
                    }

                    treeitem_childs.style.display='block';

                }

            //document.getElementById("treeitem_childs-$ti_id").style.display= (document.getElementById("treeitem_childs-$ti_id").style.display=="block")? "none":"block"; this.innerHTML=(this.innerHTML=="+")? "-": "+";
        })
    }
}
 */


/* 
function ti_btn_create(selector){
    selector = (typeof(selector) == "string") ? document.querySelector(selector) : selector;

    var treeitem = selector.querySelectorAll(".treeitem");
    for(i=0; i< treeitem.length; i++){ //console.log(i + '\n');
        var btn = document.createElement("div");
        btn.classList.add("ti_btn");
        btn.innerHTML=(parseInt(treeitem[i].dataset.has_child) > 0)? "+":"&nbsp";

        treeitem[i] .prepend(btn);
    }

}

function ti_btn_change(selector){
    treeitem = (typeof(selector) == "string") ? document.querySelector(selector) : selector;
    var btn = treeitem.querySelector(".ti_btn");
    btn.innerHTML=(parseInt(treeitem.dataset.has_child) > 0)? ((treeitem.dataset.is_open)?"-":"+"):"&nbsp";

}
 */


/* function _xhttp_load_treeitemchilds(treeitem, treeitem_childs){
    //var treeitem = target;
    //var treeitem_childs = target.nextElementSibling;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            treeitem_childs.innerHTML = this.responseText;
            treeitem.dataset.childs_loaded = true;
            treeitem.dataset.is_open=1;
            //init callbacks
            treegrid_init(treeitem_childs);
            treeitem_childs.style.display="block";
            treeitem.dataset.has_child=(treeitem_childs.innerHTML=="")? 0:1;
            if(treeitem.getAttribute("role")!="tree")ti_btn_change(treeitem);
        }
    };

    var tree_root = get_treeroot(treeitem);
    // switch on tree_root type?
    xhttp.open("GET", "?r="+tree_root.dataset.loader+"&tree[parent]="+treeitem.dataset.id+"&tree[level]="+treeitem.dataset.level+"&tree[tableName]="+tree_root.dataset.tablename+"&tree[template]="+tree_root.dataset.template+"&tree[droptarget]="+tree_root.dataset.droptarget, true);
    xhttp.send();
} */

/** may move to common.js */
/* function xhttp_load2overlay(url=""){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var overlay_holder = document.getElementById("xhttp_overlay");
            overlay_holder.innerHTML = this.responseText;
            toggleActionMenu(overlay_holder);
        }
    };

    xhttp.open("GET", url, true);
    xhttp.send();
} */

/**
██████╗ ██████╗  ██████╗ ██████╗
██╔══██╗██╔══██╗██╔═══██╗██╔══██╗
██║  ██║██████╔╝██║   ██║██████╔╝
██║  ██║██╔══██╗██║   ██║██╔═══╝
██████╔╝██║  ██║╚██████╔╝██║
╚═════╝ ╚═╝  ╚═╝ ╚═════╝ ╚═╝
 *
 * http://patorjk.com/software/taag/#p=display&f=ANSI%20Shadow&t=DROP
 * @param {*} event
 */
function treeitem_drop(event){
    console.log("treeitem_drop "+event.dataTransfer.getData("text"));
    event.preventDefault();
    event.stopPropagation();
    //.....................................
    
    var data = event.dataTransfer.getData("text").split(" ");
    var target = event.currentTarget;

    target.classList.remove('dragover');
    //.....................................

    // check if drop of this type supported:
    if(!event.currentTarget.dataset.droptarget){
        //console.log("no dataset");
        elem = get_treeroot(event.currentTarget);
        if(!elem.dataset.droptarget) {
            console.log("no droptarget @ all");
            return false;
        }
    }else elem = event.currentTarget;

    // check if modelName exist is droptarget string:
    if(elem.dataset.droptarget.indexOf(data[1]) != -1){
        // check if not drop onto self:
        var buf = event.currentTarget.dataset.dragdata + " " + get_treeroot(event.currentTarget).id;
        if(buf == event.dataTransfer.getData("text")) return false;
    }else return false;

    //.....................................

    // init some vars
    if(target.hasAttribute("role") && target.getAttribute("role") != "tree")
    {
        treeroot = get_treeroot(target);
    }else{
        treeroot = target;
    }

    var prev_treeroot = document.querySelector("[id='"+data[2]+"']");

    // MOVE if(parent-child relationship):
    if(prev_treeroot.dataset.model == treeroot.dataset.model){
        

        // DELETE source - save infos first SOURCE_DRAGDATA
        var deleteSrc = prev_treeroot.querySelector("[data-dragdata='"+data[0]+" "+data[1]+"']");
        var deleteSrcChilds = deleteSrc.nextElementSibling;
        var deleteSrcParent = deleteSrc.parentNode;
        var SOURCE_DRAGDATA = deleteSrc.dataset.dragdata;
        deleteSrcParent.removeChild(deleteSrcChilds);
        deleteSrcParent.removeChild(deleteSrc);
        var srcParentChilds = (deleteSrcParent.getAttribute("role")=="tree")? deleteSrcParent : deleteSrcParent.nextElementSibling;

        // DELETE target
        var deleteTarget = treeroot.querySelector("[data-dragdata='"+SOURCE_DRAGDATA+"']");
        if(deleteTarget){
            var deleteTargetChilds = deleteTarget.nextElementSibling;
            var deleteTargetParent = deleteTarget.parentNode;
            deleteTargetParent.removeChild(deleteTargetChilds);
            deleteTargetParent.removeChild(deleteTarget);
            //var targetParentChilds = (deleteTargetParent.getAttribute("role")=="tree")? deleteTargetParent : deleteTargetParent.nextElementSibling;
        }

        // drop
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", '?r=bony\\Tree/drop&drop[model]='+data[1]+"&drop[id]="+data[0]+"&drop[parent]="+target.dataset.id ,true);

        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                //if item updated successfully

                // RELOAD source's sourcenode
                //_xhttp_load_treeitemchilds(deleteSrcParent, srcParentChilds);
                // RELOAD target's Sourcenode
                // tsn = treeroot.querySelector("[data-dragdata='"+deleteSrcParent.dataset.dragdata+"']");
                // if(!tsn) tsn = treeroot; // patch
                // tsnChilds = (tsn.getAttribute("role")=="tree")? tsn : tsn.nextElementSibling;
                // _xhttp_load_treeitemchilds(tsn, tsnChilds);

                // RELOAD source's targetnode
                stn = prev_treeroot.querySelector("[data-dragdata='"+target.dataset.dragdata+"']");
                if(!stn) stn = prev_treeroot; //patch root
                stnChilds = (stn.getAttribute("role")=="tree")? stn : stn.nextElementSibling;
                _xhttp_load_treeitemchilds(stn, stnChilds);
                // RELOAD target's targetnode
                targetChilds = (target.getAttribute("role")=="tree")? target : target.nextElementSibling;
                _xhttp_load_treeitemchilds(target, targetChilds);


            }
        };

        xhttp.send();



    }else{
        // link (inter object connect)
    };


}//--------------------------------------------------------


function DEP_treeitem_drop(event) {
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




    if(data[1] == treeroot.dataset.module){
        // parent - child relationship
        xhttp.open("GET", '?r=bony\\Tree/drop&drop[model]='+data[1]+"&drop[id]="+data[0]+"&drop[parent]="+target.dataset.id ,true);

        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                // NEW parent .....................................................
                // Remove treeitem from Target Tree if exists - cleanup
                /* if(treeroot != prev_treeroot){
                    var duplicate = treeroot.querySelector("[data-dragdata='"+data[0]+" "+data[1]+"']");
                    var duplicateChilds = duplicate.nextElementSibling;
                    var duplicateParent = duplicate.parentNode;
                    duplicateParent.removeChild(duplicate);
                    duplicateParent.removeChild(duplicateChilds);
                } */

                // Refresh target treeitem childs
                var treeitem = target;
                var treeitem_childs = (target == treeroot)? target : target.nextElementSibling;
                _xhttp_load_treeitemchilds(treeitem, treeitem_childs);

                // PREV parent .....................................................
                //console.log(tree_root); console.log("[data-id='"+data[0]+"']");

                if(treeroot != prev_treeroot){ //if not loaded by previous step
                    var droppedItem = prev_treeroot.querySelector("[data-id='"+data[0]+"']");
                    var droppedItemChilds = droppedItem.nextElementSibling;
                    var prev_treechilds = droppedItem.parentNode;
                    if(prev_treechilds.getAttribute("role") == "tree") {
                        prev_treechilds.removeChild(droppedItem);
                        prev_treechilds.removeChild(droppedItemChilds);
                        console.log("tree root");
                        // update prev tree's target treeitem:
                        var duplicate = prev_treeroot.querySelector("[data-dragdata='"+target.dataset.dragdata+"']");
                        var duplicateChilds = duplicate.nextElementSibling;
                        //var duplicateParent = duplicate.parentNode;
                        _xhttp_load_treeitemchilds(duplicate,duplicateChilds);
                    }else{
                        console.log("NOT tree root");
                        //prev_treechilds = prev_treechilds.previousElementSibling;
                        //var prev_treeitem_childs = prev_treechilds.nextElementSibling;
                        _xhttp_load_treeitemchilds(prev_treechilds.previousElementSibling,prev_treechilds);
                    }
                    //TODO: refresh target root too!!!!!
                    /* if(treeroot != prev_treeroot){
                        var duplicate = treeroot.querySelector("[data-dragdata='"+data[0]+" "+data[1]+"']");
                        var duplicateChilds = duplicate.nextElementSibling;
                        var duplicateParent = duplicate.parentNode;
                        duplicateParent.removeChild(duplicate);
                        duplicateParent.removeChild(duplicateChilds);
                    } */
                    if(target == treeroot){
                        _xhttp_load_treeitemchilds(prev_treeroot, prev_treechilds);
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