<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/hr.css">
</head>
<body>
    <div class="card-filter mb-3 p-3">
        <div class="row g-3 align-items-end">

            <div class="col-md">
                <select class="form-select shadow" id="perCutoffByDate">
                    <option value="currCutOff">Current cut off</option>
                    <option value="prevCutOff">Previous cut off</option>
                    <option value="date">By date</option>
                </select>
            </div>

            <div class="col-md date-search hide">
                <input type="date" class="form-control shadow" id="startDate">
            </div>

            <div class="col-md date-search hide">
                <input type="date" class="form-control shadow" id="endDate">
            </div>

            <div class="col-md">
                <select class="form-select shadow" id="byAllNameDept">
                    <option value="all">All</option>
                    <option value="byName">By Name</option>
                    <option value="byDept">By Department</option>
                </select>
            </div>

            <div class="col-md name-search shadow hide">
                <input type="text" class="form-control" placeholder="Name">
            </div>

            <div class="col-md dept-select shadow hide">
                <select class="form-select">
                    <option value="cams">CAMS</option>
                    <option value="cas">CAS</option>
                    <option value="ccs">CCS</option>
                    <option value="ccj">CCJ</option>
                    <option value="ce">CE</option>
                    <option value="coa">COA</option>
                    <option value="cobm">COBM</option>
                    <option value="coed">COED</option>
                </select>
            </div>

            <div class="col-md text-end">
                <button id="generateBtn" class="btn btn-primary px-4 shadow">
                    Generate Report
                </button>   
            </div>
        </div>
    </div>
    <div class="card mx-auto p-3 report-view">
        
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="view/index-function.js?t=<?php echo time(); ?>"></script>