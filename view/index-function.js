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
        dnutChartBuilder(result);
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

function dnutChartBuilder(result){

    let valueNames = ["Verified","Unverified"];
    let valueData = [result.verified, result.unverified];
    let valueColors = ["#032a74", "#ffd700"];

    new Chart("totalChart", {
        type: "doughnut",
        data: {
            labels: valueNames,
            datasets: [{
                backgroundColor: valueColors,
                data: valueData
            }]
        },
        options:{
            plugins: {
                title: {
                    display: true,
                    text: "TADI Records Summary"
                },
                legend: {
                    display: true
                }
            }
        }
    });
}

function barChartMonthlyBuilder(result){
    
    let monthNames = [];
    let verifiedData = [];
    let unverifiedData = [];

    if(Array.isArray(result)){
        result.forEach(data =>{
            monthNames.push(data.month_name);
            verifiedData.push(data.verified);
            unverifiedData.push(data.unverified);
        });
    }

    new Chart("monthlyTotalChart", {
        type: "bar",
        data: {
            labels: monthNames,
            datasets: [
                {
                    label: "Verified",
                    backgroundColor: "#032a74",
                    data: verifiedData
                },
                {
                    label: "Unverified",
                    backgroundColor: "#ffd700",
                    data: unverifiedData
                }
            ]
        },
        options:{
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                    text: "Monthly TADI Records"
                }
            }
        }
    });
}

function barChartPerCutBuilder(result){
    
    let periodNames = [];
    let verifiedData = [];
    let unverifiedData = [];

    if(Array.isArray(result)){
        result.forEach(data =>{
            periodNames.push(data.cutoff_period);
            verifiedData.push(data.verified);
            unverifiedData.push(data.unverified);
        });
    }

    new Chart("perCutOffChart", {
        type: "bar",
        data: {
            labels: periodNames,
            datasets: [
                {
                    label: "Verified",
                    backgroundColor: "#032a74",
                    data: verifiedData
                },
                {
                    label: "Unverified",
                    backgroundColor: "#ffd700",
                    data: unverifiedData
                }
            ]
        },
        options:{
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                    text: "Per Cut-off TADI Records"
                }
            }
        }
    });
}