<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php"){
            header("Location: /projectmanager/errors/?id=FIM-OF");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php"){
            sendError("FIM-GF");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php"){
            sendError("FIM-SC");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php"){
            sendError("FIM-ADD");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php"){
            sendError("FIM-SCF");
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php"){
            sendError("FIM-DBF");
        }
        
        $conn = ConnectRoot();

        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    $nameERR = $desERR = -1;

    $Invalid = true;
    do {
        $InviteCode = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 12);
        $query = "SELECT code FROM projects WHERE code='$InviteCode'";
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 0){
                $Invalid = false;
            } elseif($result->num_rows > 1) {
                die("Report with error I2");
            }
            $result->close();
        } else {
            die();
        }
    } while($Invalid);

    if (isset($_POST["projectC"])) {
        $pname = $_POST["pname"];
        $pdes = $_POST["pdes"];
        if (strlen($pname) > 20 || strlen($pname) < 6){
            $nameERR = 0;
        } else {
            if (strlen($pdes) > 60 || strlen($pdes) < 6){
                $nameERR = 1;
                $desERR = 0;
            } else {
                addProject($conn, $pname, $pdes, $UserData, $InviteCode);
            }
        }
    }

    function addProject($conn, $pname, $pdes, $UserData, $InviteCode){
        $currentDate = date("Y-m-d H:i:s");
        $status = 2;
        $role = 1;
        if(!($stmt = $conn->prepare("INSERT INTO projects (name, des, code, idStatus, idCreator, creationDate, idUpdateUser, lastupdatedDate) VALUES (?,?,?,?,?,?,?,?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("sssiisss", $pname, $pdes, $InviteCode, $status, $UserData["id"], $currentDate, $UserData["id"], $currentDate)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            $last_id = mysqli_insert_id($conn);
            $stmt->close();
        }

        if(!($stmt = $conn->prepare("INSERT INTO projectmembers (idProject, idUser, idRole) VALUES (?,?,?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("iii", $last_id, $UserData["id"], $role)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            $query = "DELETE FROM projects WHERE id=$last_id";
            if($result = $conn->query($query)){
                die("Report with error NPD");
            }
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else {
            header("location: projects.php");
        }
        return;
    }

    $invalidCode = "";
    // Check invite code
    if(isset($_POST["validateCode"])){
        if(isset($_POST["code"]) && !empty($_POST["code"]) && strlen($_POST["code"]) == 12){
            $InputCode = $_POST["code"];
        } else {
            $invalidCode = "Please input a valid code! Ex: e5txvai2lxy9";
        }
        if (isset($InputCode)){
            $projectCodeID = checkCode($conn, $InputCode);
            if(isset($projectCodeID) && is_numeric($projectCodeID)){
                if (!is_numeric(checkUserInProject($conn, $projectCodeID, $UserData["id"]))){
                    addUserToProject($conn, $projectCodeID, $UserData["id"]);
                } else {
                    $invalidCode = "You are already in this project!";
                }
            } else {
                $invalidCode = "Invalid code!";
            }
        }
    }

    

?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=320, height=device-height, target-densitydpi=medium-dpi" />
        <title>New project</title>
        <meta name="description" content="Project Manager">
        <meta name="author" content="Miguel Magueijo">
        <link rel="icon" href="/projectmanager/img/icon.png">

        <!-- CSS -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css" integrity="sha384-i1LQnF23gykqWXg6jxC2ZbCbUMxyw5gLZY6UiUS98LYV5unm8GWmfkIS6jqJfb4E" crossorigin="anonymous">
        <!-- Remove comment to get local fontawesome, comment link above -->
        <!-- <link rel="stylesheet" href="/projectmanager/fontawesome/css/all.css"> -->
        <link rel="stylesheet" href="/projectmanager/css/db.css">
        <link rel="stylesheet" href="/projectmanager/css/Custom.css">
        <link rel="stylesheet" href="/projectmanager/css/bootstrap.min.css">
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"; ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row">
                    <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                        <div class="col-12 page-title">
                            Create / Join new project
                        </div> 
                    </div>
                    <hr class='w-100'>
                </div>

                <div class="row">
                    <div class='col-lg-12 col-xl-6 text-light'>
                        <div class='col-12 bg-dark' style='border-radius: 5px;'>
                            <div class='row project-border-bottom'>
                                <div class="col-12 project-title task-margin-tb-10">
                                    Create new project
                                </div>
                            </div>

                            <form method="post" action="">
                                <div class="row">
                                    <div class="col-md-12 task-margin-tb-10 project-text" style="word-break: break-all;">
                                        <span style="font-weight: 600">Project name (6-20 characters): *</span>
                                        <div class="form-group">
                                            <?php
                                                if ($nameERR == 0){
                                                    echo "
                                                    <input type='text' class='form-control is-invalid' name='pname' autocomplete='off' value='$pname' />
                                                    <div class='invalid-feedback'>
                                                        Must be have 1 to 60 characters.
                                                    </div>
                                                    ";
                                                } elseif ($nameERR == 1){
                                                    echo "
                                                    <input type='text' class='form-control is-valid' name='pname' autocomplete='off' value='$pname' />
                                                    <div class='valid-feedback'>
                                                        Good to go!
                                                    </div>
                                                    ";
                                                } else {
                                                    echo "<input type='text' class='form-control' name='pname' autocomplete='off' />";
                                                }
                                            ?>
                                        </div>
                                        <span style="font-weight: 600">Small description (6-60 characters): *</span>
                                        <div class="form-group">
                                            <?php
                                                if ($desERR == 0){
                                                    echo "
                                                    <input type='text' class='form-control is-invalid' name='pdes' autocomplete='off' value='$pdes' />
                                                    <div class='invalid-feedback'>
                                                        Must be have 1 to 60 characters.
                                                    </div>
                                                    ";
                                                } elseif ($desERR == 1){
                                                    echo "
                                                    <input type='text' class='form-control is-valid' name='pdes' autocomplete='off' value='$pdes' />
                                                    <div class='valid-feedback'>
                                                        Good to go!
                                                    </div>
                                                    ";
                                                } else {
                                                    echo "<input type='text' class='form-control' name='pdes' autocomplete='off' />";
                                                }
                                            ?>
                                        </div>
                                        <span style="font-weight: 600">Link to invite users to project:</span>
                                        <div class="alert alert-light">
                                            https://prothyx.icu/projectmanager/invite/?code=<?php echo $InviteCode ?>
                                        </div>
                                    </div>
                                </div>
                                            
                                <div class="row project-border-top">
                                    <div class="col-md-12 task-margin-tb-10">
                                        <input type="submit" class="btn btn-light edit-DIV-InputTitle" name="projectC" value="Create new project"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class='col-lg-12 col-xl-6 text-light'>
                        <div class='col-12 bg-dark' style='border-radius: 5px;'>
                            <div class='row project-border-bottom'>
                                <div class="col-12 project-title task-margin-tb-10">
                                    Join new project
                                </div>
                            </div>

                            <form method="post" action="">
                                <div class="row">
                                    <div class="col-12 task-margin-tb-10 project-text">
                                        <span style="font-weight: 600">Invite code (12 characters): *</span>
                                        <div class="form-group">
                                            <?php
                                                if(!empty($invalidCode)){
                                                    echo "
                                                        <input type='text' class='form-control is-invalid' name='code' autocomplete='off'/>
                                                        <div class='invalid-feedback'>
                                                            $invalidCode
                                                        </div>
                                                    ";
                                                } else {
                                                    echo "<input type='text' class='form-control' name='code' autocomplete='off'/>";
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row project-border-top">
                                    <div class="col-md-12 task-margin-tb-10">
                                        <input type='submit' class='btn btn-light edit-DIV-InputTitle' name='validateCode' value='Join project' />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </main>
        </div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="/projectmanager/js/db.js"></script>
        <script src="/projectmanager/js/bootstrap.min.js"></script>
    </body>
</html>