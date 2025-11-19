async function totalStats(){
    try{
        const request = await fetch(`controller/index-post.php`,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "GET_ALL_TOTAL"
            })
        });

        const result = await request.json();

        document.getElementById("verified").textContent = result.verified;
        document.getElementById("unverified").textContent = result.unverified;
        document.getElementById("total").textContent = result.total_rec;
        // dnutChartBuilder(result);
    }
    catch(error){
        console.log("ERROR: ", error)
    }
}

async function fetchMonthlyTotal(){

    try{
        const request = await fetch(`controller/index-post.php`,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "GET_TOTAL_PER_MONTH"
            })
        });

        const result = await request.json();
        barChartMonthlyBuilder(result)
    }
    catch(error){
        console.log("ERROR: ", error)
    }
}

async function fetchPerCutOffTotal(){

    try{
        const request = await fetch(`controller/index-post.php`,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "GET_TOTAL_PER_CUTOFF"
            })
        });

        const result = await request.json();
        barChartPerCutBuilder(result)
    }
    catch(error){
        console.log("ERROR: ", error)
    }
}

async function fetchDeptTotal(){
    try{
        const request = await fetch(`controller/index-post.php`,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "GET_ALL_PROG_TOTAL"
            })
        });
        const result = await request.json();
        vertBarChartPerDeptBuilder(result);
    }
    catch(error){
        console.log("ERROR: ", error)
    }
}

document.getElementById('generateBtn').addEventListener("click", (e)=>{
    genReport();
});

async function genReport(){
    try{
        const request = await fetch(`controller/index-post.php`, {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "GET_TADI_DETAILS_BY_CUTOFF"
            })
        });

        const result = await request.json();
        console.log(result);
    }
    catch(error){
         console.log("ERROR: ", error);
    }
}