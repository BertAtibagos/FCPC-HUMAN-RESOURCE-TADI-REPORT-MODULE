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
    const byDateOrByCutOff = document.getElementById('perCutoffByDate').value;
    const byAllOrByNameDept = document.getElementById('byAllNameDept').value;

    const params = new URLSearchParams({
        type: "GET_TADI_DETAILS_BY_CUTOFF"
    });

    if(byDateOrByCutOff == 'date'){
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        if(byAllOrByNameDept == 'all'){
            params.append('rangeType', 'byDate');
            params.append('startDate', startDate);
            params.append('endDate', endDate);
            params.append('filterType', 'deptName_all');
        }else if(byAllOrByNameDept == 'byName'){
            const nameSearch = document.getElementById('nameSearch').value;
            params.append('rangeType', 'byDate');
            params.append('filterType', 'name_Search');
            params.append('startDate', startDate);
            params.append('endDate', endDate);
            params.append('name', nameSearch);
        }else if(byAllOrByNameDept == 'byDept'){
            const deptSelect = document.getElementById('deptSelect').value;
            params.append('rangeType', 'byDate');
            params.append('filterType', 'dept_Search');
            params.append('startDate', startDate);
            params.append('endDate', endDate);
            params.append('dept', deptSelect);
        }
    }
    
    if(byDateOrByCutOff == 'currCutOff'){
        if(byAllOrByNameDept == 'all'){
            params.append('rangeType', 'currCutOff');
            params.append('filterType', 'deptName_all');
        }else if(byAllOrByNameDept == 'byName'){
            const nameSearch = document.getElementById('nameSearch').value;
            params.append('rangeType', 'currCutOff');
            params.append('filterType', 'name_Search');
            params.append('name', nameSearch);
        }else if(byAllOrByNameDept == 'byDept'){
            const deptSelect = document.getElementById('deptSelect').value;
            params.append('rangeType', 'currCutOff');
            params.append('filterType', 'dept_Search');
            params.append('dept', deptSelect);
        }
    }
    
    if(byDateOrByCutOff == 'prevCutOff'){
        if(byAllOrByNameDept == 'all'){
            params.append('rangeType', 'prevCutOff');
            params.append('filterType', 'deptName_all');
        }else if(byAllOrByNameDept == 'byName'){
            const nameSearch = document.getElementById('nameSearch').value;
            params.append('rangeType', 'prevCutOff');
            params.append('filterType', 'name_Search');
            params.append('name', nameSearch);
        }else if(byAllOrByNameDept == 'byDept'){
            const deptSelect = document.getElementById('deptSelect').value;
            params.append('rangeType', 'prevCutOff');
            params.append('filterType', 'dept_Search');
            params.append('dept', deptSelect);
        }
    }

    const reportCard = document.getElementById('reportView');
    const srchBtn = document.getElementById('generateBtn');
    srchBtn.disabled = true;
    reportCard.innerHTML = loadingRow();

    try{
        
        const request = await fetch(`controller/index-post.php`, {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: params
        });

        const result = await request.json();
        reportView(result)
        
    }
    catch(error){
         console.log("ERROR: ", error);
         srchBtn.disabled = false;
         document.getElementById('reportView').innerHTML = '<div class="alert alert-danger">Error loading report. Please try again.</div>';
    }
}

function formatTime(timeString){
    if(!timeString || timeString === '-') return '-';
    
    try {
        const [hours, minutes, seconds] = timeString.split(':');
        let hour = parseInt(hours);
        const minute = minutes;
        const ampm = hour >= 12 ? 'PM' : 'AM';
        
        hour = hour % 12;
        hour = hour ? hour : 12;
        
        return `${hour}:${minute} ${ampm}`;
    } catch(e) {
        return timeString;
    }
}

function exportTableToCSV(tableId, filename){
    const table = document.getElementById(tableId);
    let csv = [];
    
    // Get headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push('"' + th.textContent.trim().replace(/"/g, '""') + '"');
    });
    csv.push(headers.join(','));
    
    // Get rows (excluding professor header rows)
    table.querySelectorAll('tbody tr').forEach(tr => {
        if(!tr.classList.contains('table-info')) {
            const row = [];
            tr.querySelectorAll('td').forEach(td => {
                let text = td.textContent.trim().replace(/"/g, '""');
                row.push('"' + text + '"');
            });
            if(row.length > 0) csv.push(row.join(','));
        }
    });
    
    // Create blob and download
    const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
    const link = document.createElement('a');
    link.setAttribute('href', encodeURI(csvContent));
    link.setAttribute('download', filename);
    link.click();
}

function loadingRow() {
  return `
    <tr>
      <td colspan="4">
        <div class="text-center p-3">
          <div class="spinner-border" role="status">
        </div>
      </td>
    </tr>`;
}