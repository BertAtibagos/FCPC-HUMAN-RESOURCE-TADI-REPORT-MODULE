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

            <div class="col-md name-search hide">
                <input type="text" class="form-control shadow" placeholder="Name">
            </div>

            <div class="col-md dept-select hide">
                <select class="form-select shadow">
                    <option value="cams">College of Allied Medical Science</option>
                    <option value="cas">College of Arts and Sciences</option>
                    <option value="ccs">College of Computer Science</option>
                    <option value="ccj">College of Criminal Justice</option>
                    <option value="ce">College of Engineering</option>
                    <option value="coa">College of Accountancy</option>
                    <option value="cobm">College of Business Management</option>
                    <option value="coed">College of Education</option>
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
        <div class="row">
            <div class="card col-md m-3 border border-success border-3 text-success stats verified">
                <h6>Total Verified</h6>
                <h3 id="verified">0</h3>
            </div>
            <div class="card col-md m-3 border border-danger border-3 text-danger stats unverified">
                <h6>Total Unverified</h6>
                <h3 id="unverified">0</h3>
            </div>
            <div class="card col-md m-3 border border-secondary border-3 text-dark stats total-rec">
                <h6>Total Records</h6>
                <h3 id="total">0</h3>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="view/index-script.js?t=<?php echo time(); ?>"></script>