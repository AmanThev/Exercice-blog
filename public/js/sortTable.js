document.addEventListener("readystatechange", function(e) {  
    if (document.readyState=="interactive") {    
        let table=document.getElementsByTagName("table");    
        for (let i=0; i<table.length; i++) {  
            table[i].dataset.sortTable = true;
            let ths=table[i].querySelectorAll("th");
            for(let j=0; j<ths.length; j++){
                ths[j].dataset.direction = "";
                ths[j].addEventListener("click", sortColumn, false);
            } 
        }  
    }
});

function sortColumn(e) {  
    let th=e.currentTarget;
    let table=th.parentElement.parentElement.parentElement;
    let ths=table.querySelectorAll("th");

    for(let i=0; i<ths.length; i++){
        let icon=ths[i].querySelector("i.fas");
        if(ths[i]===th){
            if(ths[i].dataset.direction=="up"){
                var direction="down";
            }else{
                var direction="up";
            }
            icon.className="fas fa-sort-"+direction;
            ths[i].dataset.direction=direction;
            sortTable(table, i, direction); 
        }else{
            if(icon != null){
                ths[i].dataset.direction="";
                icon.className="fas fa-sort light";   
            }
        }
    }
}

function sortTable(table, numColumn, direction) {   
    let tbody=table.getElementsByTagName("tbody")[0];
    let trs=tbody.getElementsByTagName("tr");
    let sortUpAndDown=true;

    while(sortUpAndDown){
        var isInversion=false;
        for(var i=1; i < trs.length - 1; i++){
            let tdOfTheColumn=trs[i].getElementsByTagName("td")[numColumn];
            let tdOfTheColumnPlus1=trs[i+1].getElementsByTagName("td")[numColumn];
            let columnContent=tdOfTheColumn.innerText.toLowerCase();
            let columnContentPlus1=tdOfTheColumnPlus1.innerText.toLowerCase();  
            
            if((direction=="up") && (columnContent > columnContentPlus1)){
                console.log(trs[i+1], trs[i]);
                isInversion=true;
                break;
            }
            if((direction=="down") && (columnContent < columnContentPlus1)){
                isInversion=true;
                break;
            }
        } 
        if(isInversion) {
            trs[i].parentNode.insertBefore(trs[i + 1], trs[i]);
        } else{
            sortUpAndDown=false;
        }
    }
} 