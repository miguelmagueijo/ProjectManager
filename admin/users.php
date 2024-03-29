<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        error_reporting(E_ERROR | E_PARSE);
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-OF-ADMINU"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-GF-ADMINU"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/editFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-EF-ADMINU"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/addFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-AF-ADMINU"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/checkFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-CF-ADMINU"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/removeFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-RF-ADMINU"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php"){
            die(header("Location: /projectmanager/errors/?id=CI-SC-ADMINU"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php"){
            die(header("Location: /projectmanager/errors/?id=CI-DF-ADMINU"));
        }
        
        $conn = ConnectRoot();
        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    if($UserData["role"] != 20){
        header("Location: /projectmanager/");
    }

    $usersData = array();
    $hasUsers = false;
    $query = "SELECT * FROM user ORDER BY id DESC";
    if ($result = $conn->query($query)) {
        if ($result->num_rows >= 1){
            $hasUsers = true;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_push($usersData, $row);
            }
        } else {
            $hasUsers = false;
        }
        $result->close();
    } else {
        die("Can't get users");
    }

    if(isset($_POST["REMuser"])){
        if(isset($_POST["REMid"]) && is_numeric($_POST["REMid"])){
            if($_POST["REMid"] != 13 && $_POST["REMid"] != $UserData["id"]){
                REMOVEALLuserInfoFromDataBaseADMIN($conn, $_POST["REMid"]);
            }
        }
    }
    
?>

<html lang="en">
    <head>
        <title>Admin - Users</title>
        <?php
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html"){
                die(header("Location: /projectmanager/errors/?id=CI-HEAD-ADMINU"));
            }
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html"){
                die(header("Location: /projectmanager/errors/?id=CI-CSS-ADMINU"));
            }
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    die(header("Location: /projectmanager/errors/?id=CI-BAR-ADMINU"));
                }
            ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row">
                    <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                        <div class="col-12 page-title">
                            All users
                        </div>    
                    </div>
                    <hr class='w-100'>

                    <div class="col-12">
                        <div class='row'>

                            <?php
                                if(isset($usersData) && $hasUsers){
                                    foreach($usersData as $user){
                                        if(!($user["id"] == 13 || $user["id"] == $UserData["id"])){
                                            echo "
                                            <div class='col-md-12 col-xl-6' style='margin-bottom: 10px'>
                                                <div class='col-12 bg-dark text-white' style='border-radius:5px;'>
                                                    <div class='row task-border-bottom'>
                                                        <form method='POST' action='' class='task-margin-tb-10'>
                                                            <div class='col-12 project-title'>
                                                                ID: $user[id] & Role: $user[role]
                                                                <input type='submit' class='btn btn-danger' name='REMuser' value='Remove'>
                                                                <input type='hidden' name='REMid' value='$user[id]'>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <div class='row' style='border-radius:5px;'>
                                                        <div class='col-12 task-margin-tb-10 project-text'>
                                                            <div class='row'>
                                                                <div class='col-lg-12 col-xl-6'>
                                                                    <b>Username</b>: $user[username]
                                                                    <br>
                                                                    <b>Email</b>: $user[email]
                                                                </div>
                                                                <div class='col-lg-12 col-xl-6'>
                                                                    <b>Last updated</b>: $user[lastUpdateDate]
                                                                    <br>
                                                                    <b>Created at</b>: $user[creationDate]
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            ";
                                        }
                                    }
                                } else {
                                    echo "<p class='issue-DIV-list col-12' style='margin-top: 5px'> There is no users. </p>";
                                }

                            ?>
                            
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