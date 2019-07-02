<?php
    session_start();
    if (!isset($_SESSION["user"])){
        header("Location: /projectmanager/");
    } else {
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/getFunctions.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/sessionCheckTime.php";
        include "$_SERVER[DOCUMENT_ROOT]/projectmanager/php/databaseConnections.php";
        $conn = ConnectRoot();
        $UserData = getSessionUserData($conn, $_SESSION["user"]);
    }

    if (isset($_GET["id"]) && is_numeric($_GET["id"])){
        $projectID = $_GET["id"];
    } else {
        header("location: /projectmanager/dashboard/projects");
    }

    if (isset($projectID)){
        $projectData = getSingleProjectData($conn, $projectID, $UserData["id"]);
        if (isset($projectData)){
            $tasksData = getTasks($conn, $projectID);
            if(!isset($tasksData)){
                $createTask = true;
            }
        } else {
            header("location: /projectmanager/dashboard/projects");
        }
    }

    // Tasks Data
    function getTasks($conn, $projectID){
        $tasksData = array();
        $query = "SELECT t.*, s.name AS status, s.badge FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id INNER JOIN tstatus AS s ON t.idStatus=s.id WHERE p.id=$projectID ORDER BY t.lastupdatedDate DESC LIMIT 25";
        if ($result = $conn->query($query)) {
            if ($result->num_rows >= 1){
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($tasksData, $row);
                }
            } elseif ($result->num_rows == 0) {
                return;
            } else {
                die();
            }
        } else {
            die();
        }
        return $tasksData;
    }

    $UserRole = getUserProjectRole($conn, $projectID, $UserData["id"]);

    if (isset($_POST["searchBTN"])){
        if(isset($_POST["searchTask"])){
            $StaskName = $_POST["searchTask"];
            $StaskName = "%".$StaskName."%";
        }
        if (isset($StaskName)){
            if(!($stmt = $conn->prepare("SELECT t.*, s.name AS status, s.badge FROM tasks AS t INNER JOIN projects AS p ON t.idProject=p.id INNER JOIN tstatus AS s ON t.idStatus=s.id WHERE p.id=$projectID AND t.name LIKE ? ORDER BY t.lastupdatedDate DESC LIMIT 25"))) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            if(!$stmt->bind_param("s", $StaskName)) {
                die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            if(!$stmt->execute()) {
                die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            if ($result = $stmt->get_result()) {
                if ($result->num_rows > 0){
                    unset($tasksData);
                    $tasksData = array();
                    while ($row = $result->fetch_array(MYSQLI_ASSOC)){
                        array_push($tasksData, $row); 
                    }
                    $stmt->close();
                } else {
                    $NoTasks = true;
                }
            } else {
                printf("Error in select user query");
                return false;
            }
        }
    }
?>

<html lang="en">
    <head>
    <title><?php echo "$projectData[name] - Tasks"; ?></title>
        <?php
            include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/Headcontent.html";
            include "$_SERVER[DOCUMENT_ROOT]/projectmanager/html/CSSimport.html";
        ?>
    </head>

    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php include "$_SERVER[DOCUMENT_ROOT]/projectmanager/sidebar/bar.php"; ?>


            <main class="page-content">
                <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <span style="font-size:2rem; font-weight: 500;">
                            <?php
                                echo "
                                    <a href='/projectmanager/project/?id=$projectData[id]' style='text-decoration:none;'>
                                        <span style='color: black;'>$projectData[name]</span>
                                        <span class='badge badge-$projectData[badge]'>$projectData[Sname]</span>
                                    </a>
                                ";
                                if ($UserRole < 3){
                                    echo "
                                        <a href='/projectmanager/project/edit?id=$projectData[id]' class='edit-pen'>
                                            <i class='fas fa-pen'></i>
                                        </a>
                                    ";
                                }
                            ?>
                        </span>
                        <br>
                        <span style="font-size:1.3rem; font-weight: 400;">
                            <?php
                                echo $projectData["des"];
                            ?>        
                        </span>
                        <hr>
                    </div>
                    
                    <div class="col-lg-12 filter-DIV">
                        <div class="row" style='margin-top:15px;'>
                            <div class="col-lg-4 filter-DIV-text">
                                <form method="POST" action="">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search by task name" name="searchTask">
                                        <div class="input-group-append">
                                            <button type="submit" name="searchBTN" class="btn  btn-dark">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-6">
                                <form method="POST" action="">
                                    <div class="input-group">
                                        <select name="FilterSelect" class="form-control" style="background: #3a3f48; color:white; border: none" onchange="this.form.submit()">
                                            <option selected disabled> Order by... </option>
                                            <option value=""> Creation date </option>
                                            <option value=""> Name </option>
                                            <option value=""> Last update date </option>
                                        </select>
                                        &nbsp
                                        <select name="FilterStatusSelect" class="form-control" style="background: #3a3f48; color:white; border: none" onchange="this.form.submit()">
                                            <option selected disabled> All status </option>
                                            <option value=""> Cancelled </option>
                                            <option value=""> Completed </option>
                                            <option value=""> In Progress </option>
                                            <option value=""> Paused </option>
                                            <option value=""> Stopped </option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-2">
                                <?php
                                    if ($UserRole < 4){
                                        echo "
                                            <a class='btn btn-dark' data-toggle='modal' href='#newTaskModal' style='margin-bottom: 15px'>
                                                New Task
                                            </a>
                                        ";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Task -->
                    <?php
                        if(isset($tasksData) && !isset($NoTasks)){
                            foreach($tasksData as $task){
                                echo "
                                <div class='col-lg-12 col-xl-6 task-DIV'>
                                    <div class='btn-toolbar row' style='margin-top:15px'>
                                        <div class='col-lg-12' style='margin-top:5px;'>
                                            <span class='task-DIV-title2 task-DIV-text'>
                                                <a href='/projectmanager/project/task?id=$projectData[id]&task=$task[id]'>
                                                    $task[name]
                                                </a>
                                                <span class='badge badge-$task[badge]'>$task[status]</span>
                                                <span class='badge badge-dark'>$task[lastupdatedDate]</span>";
                                                if ($UserRole < 3){
                                                    echo "
                                                        <a href='#' class='edit-pen'>
                                                            <i class='fas fa-pen'></i>
                                                        </a>
                                                    ";
                                                }
                                echo "
                                                <a href='#' class='btn bg-dark text-white float-right'>
                                                    <i class='fas fa-comments'></i>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                    <hr class='hr-task'>
                                    <div class='task-DIV-Des' style='word-break: break-word; margin-bottom: 15px'>
                                        $task[Des]
                                    </div>
                                </div>
                                ";
                            }
                        } elseif (isset($createTask) && $createTask) {
                            echo "<p class='task-DIV-list'> No tasks yet, create them! </p>";
                        } elseif (isset($NoTasks) && $NoTasks){
                            echo "<p class='task-DIV-list'> No tasks found! </p>";
                        }
                    ?>
                    <!-- END Task -->
                        
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