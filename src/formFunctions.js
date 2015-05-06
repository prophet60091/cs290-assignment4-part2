
//
//function addVideo(){
//    var title = document.getElementById("title").value;
//    var category = document.getElementById("category").value;
//    var t = document.getElementById("length").value;
//
//    var form = new FormData();
//    form.append('title', title);
//    form.append('category', category);
//    form.append('length', t);
//
//    var req = new XMLHttpRequest();
//
//    req.onreadystatechange = function() {
//        if (req.readyState == 4 && req.status == 200) {
//            document.getElementById("status").innerHTML = req.responseText;
//        }
//    };
//
//    //append it to the table too
//
//    req.open("POST", "connect.php");
//
//    req.send(form);
//
//}

function    rent(id){

    var form = new FormData();
    form.append('rent', id);

    var req = new XMLHttpRequest();

    req.onreadystatechange = function() {
        if (req.readyState == 4 && req.status == 200) {
            document.getElementById("status").innerHTML = req.responseText;
        }
    };

    if(document.getElementById(id).lastElementChild.previousSibling.firstChild.value == "Rent" ){
        document.getElementById(id).lastElementChild.previousSibling.firstChild.setAttribute("value", "Check In");
        document.getElementById("status").innerHTML = "STATUS: Checked Out";
    }else{

        document.getElementById(id).lastElementChild.previousSibling.firstChild.setAttribute("value", "Rent");
        document.getElementById("status").innerHTML = "STATUS: Available";
    }


    req.open("POST", "connect.php");

    req.send(form);

}

function  deleteVid(id){

    var form = new FormData();
    form.append('deleteVid', id);

    var req = new XMLHttpRequest();

    req.onreadystatechange = function() {
        if (req.readyState == 4 && req.status == 200) {
            document.getElementById("status").innerHTML = req.responseText;
        }
    };

    document.getElementById(id).setAttribute("style", "display:none");

    req.open("POST", "connect.php");

    req.send(form);
    document.getElementById("status").innerHTML = "STATUS: Deleted";
};



