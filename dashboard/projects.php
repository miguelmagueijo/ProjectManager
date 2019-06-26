<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        $dbHost = "localhost";
        $dbUser = "root";
        $dbPassword = "";
        $dbName = "pmanager";
        $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

        $UserData = array();
        $query = "SELECT * FROM  user WHERE id=".$_SESSION["user"]["id"];
        if ($result = $conn->query($query)) {
            if ($result->num_rows == 1){
                if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $UserData += ["id" => $row["id"]];
                    $UserData += ["username" => $row["username"]];
                    $UserData += ["role" => $row["role"]];
                    $_SESSION["user"]["role"] = $row["role"];
                } else {
                    printf("MAJOR ERROR CAN'T CONVERT USER ROW TO ARRAY");
                    die();
                }
            } else {
                die();
            }
            $result->close();
        } else {
            printf("Error in select user query");
            die();
        }
    }

    $ProjectData = array();
    $hasProjects = false;
    // Get all projects user is assigned
    $query = "SELECT p.*, s.name as Sname, u.username FROM projects AS p INNER JOIN pstatus AS s ON p.idStatus=s.id INNER JOIN projectmembers AS pm ON p.id = pm.idProject INNER JOIN user AS u ON p.idCreator = u.id WHERE pm.idUser =".$UserData["id"];
    if ($result = $conn->query($query)) {
        if ($result->num_rows >= 1){
            $hasProjects = true;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_push($ProjectData, $row);
            }
        } else {
            $hasProjects = false;
        }
        $result->close();
    } else {
        printf("Error in select user query");
        die();
    }
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=320, height=device-height, target-densitydpi=medium-dpi" />
        <title>Home</title>
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
                    <div>
                        <span style="font-size:2rem; font-weight: 500;">All projects</span>
                        <input type="submit" value="New Project" class="btn btn-success float-right" style="margin-top:8px">
                    </div>
                    <hr>
                    <div class="card-deck">
                        <?php
                            if($hasProjects){
                                foreach($ProjectData as $Project){
                                    $dateTimeStamp = strtotime($Project["creationDate"]);
                                    $Project["creationDate"] = date('d-m-Y', $dateTimeStamp);
                                    echo "
                                        <div class='card text-white bg-primary mb-3' style='max-width: 18rem;'>
                                            <div class='card-header'>
                                                $Project[name]
                                                <a href='#' class='btn btn-light float-right'>
                                                    <i class='fas fa-cog'></i>
                                                </a>
                                            </div>
                                            <div class='card-body'>
                                                <h5 class='card-title'>
                                                    $Project[des]
                                                </h5>
                                                <p class='card-text'>
                                                    Status: $Project[Sname] <br>
                                                    Creation Date: $Project[creationDate]
                                                </p>
                                            </div>
                                            <div class='card-footer'>
                                                <a href='#' class='btn btn-light'>
                                                    <i class='fas fa-tasks'></i>
                                                </a>
                                                <a href='#' class='btn btn-light'>
                                                    <i class='fas fa-bug'></i>
                                                </a>
                                                <a href='#' class='btn btn-light'>
                                                    <i class='fas fa-calendar'></i>
                                                </a>
                                                <a href='#' class='btn btn-light'>
                                                    <i class='fas fa-comments'></i>
                                                </a>
                                                <a href='#' class='btn btn-light'>
                                                    <i class='fas fa-flag'></i>
                                                </a>
                                            </div>
                                        </div>
                                    ";
                                }
                            } else {
                                echo "No projects found, what about creating a new one?";
                            }  
                        ?>
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