document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("table").forEach(function (table) {
        table.dataset.sortTable = true;
        table.querySelectorAll("th").forEach(function (th) {
            th.dataset.direction = "";
            // seuls les en-têtes avec une icône de tri sont cliquables
            if (th.querySelector("i.fas")) {
                th.addEventListener("click", sortColumn, false);
            }
        });
    });
});

function sortColumn(e) {
    var th        = e.currentTarget;
    var table     = th.closest("table");
    var ths       = Array.prototype.slice.call(table.querySelectorAll("th"));
    var direction = th.dataset.direction === "up" ? "down" : "up";

    ths.forEach(function (other) {
        var icon = other.querySelector("i.fas");
        if (!icon) return;

        if (other === th) {
            icon.className = "fas fa-sort-" + direction;
            other.dataset.direction = direction;
        } else {
            icon.className = "fas fa-sort light";
            other.dataset.direction = "";
        }
    });

    sortTable(table, ths.indexOf(th), direction);
}

function sortTable(table, numColumn, direction) {
    var tbody = table.tBodies[0];
    var rows  = Array.prototype.slice.call(tbody.rows);

    if (!table.tHead) {
        rows.shift();
    }

    var valueOf = function (row) {
        var cell = row.cells[numColumn];
        var raw  = cell.dataset.sort !== undefined ? cell.dataset.sort : cell.innerText;
        return raw.trim().toLowerCase();
    };

    rows.sort(function (a, b) {
        var va = valueOf(a);
        var vb = valueOf(b);
        var result;

        if (va !== "" && vb !== "" && !isNaN(Number(va)) && !isNaN(Number(vb))) {
            result = Number(va) - Number(vb);
        } else {
            result = va > vb ? 1 : (va < vb ? -1 : 0);
        }

        return direction === "up" ? result : -result;
    });

    rows.forEach(function (row) {
        tbody.appendChild(row);
    });
}

// document.addEventListener("readystatechange", function(e) {  
//     if (document.readyState=="interactive") {    
//         let table=document.getElementsByTagName("table");    
//         for (let i=0; i<table.length; i++) {  
//             table[i].dataset.sortTable = true;
//             let ths=table[i].querySelectorAll("th");
//             for(let j=0; j<ths.length; j++){
//                 ths[j].dataset.direction = "";
//                 ths[j].addEventListener("click", sortColumn, false);
//             } 
//         }  
//     }
// });

// function sortColumn(e) {  
//     let th=e.currentTarget;
//     let table=th.parentElement.parentElement.parentElement;
//     let ths=table.querySelectorAll("th");

//     for(let i=0; i<ths.length; i++){
//         let icon=ths[i].querySelector("i.fas");
//         if(ths[i]===th){
//             if(ths[i].dataset.direction=="up"){
//                 var direction="down";
//             }else{
//                 var direction="up";
//             }
//             icon.className="fas fa-sort-"+direction;
//             ths[i].dataset.direction=direction;
//             sortTable(table, i, direction); 
//         }else{
//             if(icon != null){
//                 ths[i].dataset.direction="";
//                 icon.className="fas fa-sort light";   
//             }
//         }
//     }
// }

// function sortTable(table, numColumn, direction) {   
//     let tbody=table.getElementsByTagName("tbody")[0];
//     let trs=tbody.getElementsByTagName("tr");
//     let sortUpAndDown=true;

//     while(sortUpAndDown){
//         var isInversion=false;
//         for(var i=1; i < trs.length - 1; i++){
//             let tdOfTheColumn=trs[i].getElementsByTagName("td")[numColumn];
//             let tdOfTheColumnPlus1=trs[i+1].getElementsByTagName("td")[numColumn];
//             let columnContent=tdOfTheColumn.innerText.toLowerCase();
//             let columnContentPlus1=tdOfTheColumnPlus1.innerText.toLowerCase();  
            
//             if((direction=="up") && (columnContent > columnContentPlus1)){
//                 console.log(trs[i+1], trs[i]);
//                 isInversion=true;
//                 break;
//             }
//             if((direction=="down") && (columnContent < columnContentPlus1)){
//                 isInversion=true;
//                 break;
//             }
//         } 
//         if(isInversion) {
//             trs[i].parentNode.insertBefore(trs[i + 1], trs[i]);
//         } else{
//             sortUpAndDown=false;
//         }
//     }
// } 