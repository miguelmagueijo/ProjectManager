<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        error_reporting(E_ERROR | E_PARSE);
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/otherFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-OF-MP"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php"){
            die(header("Location: /projectmanager/errors/?id=CI-GF-MP"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php"){
            die(header("Location: /projectmanager/errors/?id=CI-SC-MP"));
        }
        if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php"){
            die(header("Location: /projectmanager/errors/?id=CI-DF-MP"));
        }
        $conn = ConnectRoot();
        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    $ProjectData = array();
    $hasProjects = false;
    $query = "SELECT p.*, s.name as Sname, s.badge AS Sbadge, u.username, pm.idRole AS Role FROM projects AS p INNER JOIN pstatus AS s ON p.idStatus=s.id INNER JOIN projectmembers AS pm ON p.id = pm.idProject INNER JOIN user AS u ON p.idCreator = u.id WHERE pm.idUser =$UserData[id] ORDER BY p.creationDate DESC LIMIT 6";
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
        sendError("GPD-MPI");
    }

    $TasksData = array();
    $hasTasks = false;
    $query = "SELECT t.*, s.name AS status, s.badge, p.id AS projectID, p.name AS projectName FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id INNER JOIN tstatus AS s ON t.idStatus=s.id INNER JOIN taskfollow AS tf ON t.id=tf.idTask WHERE tf.idUser=$UserData[id] LIMIT 6";
    if ($result = $conn->query($query)) {
        if ($result->num_rows >= 1){
            $hasTasks = true;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_push($TasksData, $row);
            }
        } else {
            $hasTasks = false;
        }
        $result->close();
    } else {
        sendError("GTD-MPI");
    }

    $IssuesData = array();
    $hasIssues = false;
    $query = "SELECT i.*, s.name AS status, s.badge, p.id AS projectID, p.name AS projectName FROM issues AS i INNER JOIN projects AS p ON i.idProject=p.id INNER JOIN istatus AS s ON i.idStatus=s.id INNER JOIN issuefollow AS iff ON i.id=iff.idIssue WHERE iff.idUser=$UserData[id] LIMIT 5";
    if ($result = $conn->query($query)) {
        if ($result->num_rows >= 1){
            $hasIssues = true;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                array_push($IssuesData, $row);
            }
        } else {
            $hasIssues = false;
        }
        $result->close();
    } else {
        sendError("GID-MPI");
    }
?>

<html lang="en">
    <head>
        <title>Home</title>
        <?php
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html"){
                die(header("Location: /projectmanager/errors/?id=CI-HEAD-MP"));
            }
            if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html"){
                die(header("Location: /projectmanager/errors/?id=CI-CSS-MP"));
            }
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php
                if(!include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"){
                    die(header("Location: /projectmanager/errors/?id=CI-BAR-MP"));
                }
            ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row">
                    <!-- Projects -->
                    <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                        <div class="col-9">
                            <a href="/projectmanager/dashboard/projects" class="page-title">All projects</a>
                        </div>
                        <div class="col-3">
                            <a href="/projectmanager/dashboard/projects" class="btn btn-dark float-right" style="margin-top:8px; color:white;">All Projects</a>
                        </div>    
                    </div>
                    <hr class='w-100'>
                    <?php
                        if($hasProjects){
                            foreach($ProjectData as $Project){
                                if ($Project["Role"] < 3){
                                    $code = $Project["code"];
                                }
                                $dateTimeStamp = strtotime($Project["creationDate"]);
                                $Project["creationDate"] = date('d-m-Y', $dateTimeStamp);
                                $dateTimeStamp = strtotime($Project["lastupdatedDate"]);
                                $Project["lastupdatedDate"] = date('d-m-Y', $dateTimeStamp);

                                echo "
                                <div class='col-12 col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 text-light' style='margin-bottom: 10px'>
                                    <div class='col-12 bg-dark' style='border-radius: 5px;'>
                                        <div class='row project-border-bottom'>
                                            <div class='col-12 task-margin-tb-10'>
                                                <a href='/projectmanager/project/?id=$Project[id]' class='project-title'>
                                                   $Project[name]
                                                </a>
                                            </div>
                                        </div>

                                        <div class='row project-text' style='margin-top: 10px;'>
                                            <div class='col-12' style='margin-bottom: 10px; word-break: break-all;'>
                                                $Project[des]
                                            </div>
                                            <div class='col-md-12 col-xl-6' style='margin-top: 10px'>
                                                Status: ";
                                if ($Project["Sbadge"] == "dark"){
                                    echo "<span class='badge badge-$Project[Sbadge] custom-badge-border'>$Project[Sname]</span>";
                                } else {
                                    echo "<span class='badge badge-$Project[Sbadge]'>$Project[Sname]</span>";
                                }
                                echo "
                                                <br>
                                                Updated: <span class='badge badge-light'>$Project[lastupdatedDate]</span>
                                            </div>
                                            <div class='col-md-12 col-xl-6' style='margin-top: 5px'>
                                                Created: <span class='badge badge-light'>$Project[creationDate]</span>
                                                <br>";
                                if (isset($code)){
                                    echo "Code: <span class='badge badge-light'>$code</span>";
                                }
                                echo "    
                                            </div>
                                        </div>
                                        <div class='row project-border-top' style='padding: 10px; margin-top: 10px'>
                                            <div class='col-12 text-center'>
                                                <a href='/projectmanager/project/tasks/?id=$Project[id]' class='btn btn-light' style='margin: 5px'>
                                                    <i class='fas fa-tasks'></i>
                                                </a>
                                                <a href='/projectmanager/project/issues/?id=$Project[id]' class='btn btn-light' style='margin: 5px'>
                                                    <i class='fas fa-bug'></i>
                                                </a>
                                                <a href='/projectmanager/project/members/?id=$Project[id]' class='btn btn-light' style='margin: 5px'>
                                                    <i class='fas fa-users'></i>
                                                </a>
                                                <a href='/projectmanager/project/milestones/?id=$Project[id]' class='btn btn-light' style='margin: 5px'>
                                                    <i class='fas fa-flag'></i>
                                                </a>";
                                if ($Project["Role"] < 3){
                                    echo "
                                        <a href='/projectmanager/project/edit?id=$Project[id]' class='btn btn-primary' style='margin: 5px'>
                                            <i class='fas fa-cog'></i>
                                        </a>";
                                }            
                                echo "
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                ";
                                if (isset($code)){
                                    unset($code);
                                }
                            }
                        } else {
                            echo "<div class='col-12'><h4>No projects found, what about creating or joining a new one?</h4></div>";
                        }
                    ?>
                </div>

                <br>

                <!-- Tasks -->
                <div class="row">
                    <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                        <div class="col-9">
                            <a href="/projectmanager/dashboard/tasks" class="page-title">Followed tasks</a>
                        </div>
                        <div class="col-3">
                            <a href="/projectmanager/dashboard/tasks" class="btn btn-success float-right" style="margin-top:8px; color:white;">All followed tasks</a>
                        </div>    
                    </div>
                    <hr class='w-100'>

                    <div class="col-12">
                        <div class="row">
                            <?php
                                if(isset($TasksData) && $hasTasks){
                                    foreach($TasksData as $task){
                                        echo "
                                        <div class='col-md-12 col-xl-4' style='margin-bottom: 10px'>
                                            <div class='col-12 text-white bg-success' style='border-radius:5px;'>
                                                <div class='row task-border-bottom'>
                                                    <div class='col-12 task-margin-tb-10'>
                                                        <a href='/projectmanager/project/tasks/task?id=$task[projectID]&task=$task[id]' class='task-title'>
                                                            $task[name]
                                                        </a>";
                                        if($task["badge"] == "success"){
                                            echo "<span class='badge badge-$task[badge] custom-badge-border task-badge-text'>$task[status]</span>";
                                        } else {
                                            echo "<span class='badge badge-$task[badge] task-badge-text'>$task[status]</span>";
                                        }
                                        echo "         
                                                        </div>
                                                    </div>

                                                    <div class='row task-margin-tb-10'>
                                                        <div class='col-12 project-text'>
                                                            $task[Des]
                                                        </div>
                                                    </div>

                                                    <div class='row task-border-top'>
                                                        <div class='col-12 project-text task-margin-tb-10'>
                                                            From:
                                                            <a href='/projectmanager/project/?id=$task[projectID]' style='color:white; text-decoration:none'>
                                                                <b>$task[projectName]</b>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ";
                                    }
                                } elseif (!$hasTasks) {
                                    echo "<p class='task-DIV-list col-12' style='margin-top: 5px'> No tasks assigned, go to projects and follow some tasks! </p>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <!-- End Tasks -->

                <br>

                <!-- Issues -->
                <div class="row">
                    <div class='col-12 row' style="padding-left:0px; padding-right:0px">
                        <div class="col-9">
                            <a href="/projectmanager/dashboard/issues" class="page-title">Followed issues</a>
                        </div>
                        <div class="col-3">
                            <a href="/projectmanager/dashboard/issues" class="btn btn-danger float-right" style="margin-top:8px; color:white;">All followed issues</a>
                        </div>    
                    </div>
                    <hr class='w-100'>

                    <div class="col-12">
                        <div class="row">
                            <?php
                                if(isset($IssuesData) && $hasIssues){
                                    foreach($IssuesData as $issue){
                                        echo "
                                        <div class='col-md-12 col-xl-4' style='margin-bottom: 10px'>
                                            <div class='col-12 text-white bg-danger' style='border-radius:5px;'>
                                                <div class='row issues-border-bottom'>
                                                    <div class='col-12 task-margin-tb-10'>
                                                        <a href='/projectmanager/project/issues/issue?id=$issue[projectID]&issue=$issue[id]' class='issues-title'>
                                                            $issue[name]
                                                        </a>";
                                        if($issue["badge"] == "danger"){
                                            echo "<span class='badge badge-$issue[badge] custom-badge-border issues-badge-text'>$issue[status]</span>";
                                        } else {
                                            echo "<span class='badge badge-$issue[badge] issues-badge-text'>$issue[status]</span>";
                                        }
                                        echo "         
                                                        </div>
                                                    </div>

                                                    <div class='row task-margin-tb-10'>
                                                        <div class='col-12 project-text'>
                                                            $issue[Des]
                                                        </div>
                                                    </div>

                                                    <div class='row issues-border-top'>
                                                        <div class='col-12 project-text task-margin-tb-10'>
                                                            From:
                                                            <a href='/projectmanager/project/?id=$issue[projectID]' style='color:white; text-decoration:none'>
                                                                <b>$issue[projectName]</b>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ";
                                    }
                                } elseif (!$hasIssues) {
                                    echo "<p class='issue-DIV-list col-12' style='margin-top: 5px'> No issues assigned, go to projects and follow some issues! </p>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <!-- End Issues -->
                
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