function dnutChartBuilder(result){

    let valueNames = ["Verified","Unverified"];
    let valueData = [result.verified, result.unverified];
    let valueColors = ["#ffd700", "#032a74"];

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
                    backgroundColor: "#ffd700",
                    data: verifiedData
                },
                {
                    label: "Unverified",
                    backgroundColor: "#032a74",
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
                    backgroundColor: "#ffd700",
                    data: verifiedData
                },
                {
                    label: "Unverified",
                    backgroundColor: "#032a74",
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

function vertBarChartPerDeptBuilder(result){
    
    let programNames = [];
    let verifiedData = [];
    let unverifiedData = [];

    if(Array.isArray(result)){
        result.forEach(data =>{
            programNames.push(data.program_name);
            verifiedData.push(data.verified_count);
            unverifiedData.push(data.unverified_count);
        });
    }

    new Chart("vertPerDeptChart", {
        type: "bar",
        data: {
            labels: programNames,
            datasets: [
                {
                    label: "Verified",
                    backgroundColor: "#ffd700",
                    data: verifiedData
                },
                {
                    label: "Unverified",
                    backgroundColor: "#032a74",
                    data: unverifiedData
                }
            ]
        },
        options:{
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                    text: "Per Program TADI Records"
                }
            }
        }
    });
}