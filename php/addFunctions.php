<?php 

// TASKS

    // Follow task
    function addFollowToTask($conn, $taskID, $userID){
        $query = "INSERT INTO taskfollow (idTask, idUser) VALUES ($taskID, $userID)";
        if (!$conn->query($query)) {
            die();
        } else {
            header("Refresh: 0");
            return;
        }
        die();
    }


    // New task
    function addNewTask($conn, $projectID, $userID, $task){
        $currentDate = getCurrentDate();

        if(!($stmt = $conn->prepare("INSERT INTO tasks (idProject, name, des, idStatus, idCreator, idUpdateUser, creationDate, lastupdatedDate) VALUES (?,?,?,?,?,?,?,?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("issiiiss", $projectID, $task["name"], $task["des"], $task["status"], $userID, $userID, $currentDate, $currentDate)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            $last_id = mysqli_insert_id($conn);
            $stmt->close();
        }
        header("Refresh: 0");
    }

    // New comment for certain task
    function addTaskNewComment($conn, $taskID, $comment, $userID){
        $currentDate = getCurrentDate();

        if(!($stmt = $conn->prepare("INSERT INTO taskcomments (idTask, idUser, comment, creationDate) VALUES (?,?,?,?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("iiss", $taskID, $userID, $comment, $currentDate)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            $stmt->close();
        }
        header("Refresh: 0");
    }

// END TASK

// ISSUE

    // Follow issue
    function addFollowToIssue($conn, $issueID, $userID){
        $query = "INSERT INTO issuefollow (idIssue, idUser) VALUES ($issueID, $userID)";
        if (!$conn->query($query)) {
            die();
        } else {
            header("Refresh: 0");
            return;
        }
        die();
    }


// New issue
    function addNewIssue($conn, $projectID, $userID, $issue){
        $currentDate = getCurrentDate();

        if(!($stmt = $conn->prepare("INSERT INTO issues (idProject, name, des, idStatus, idCreator, idUpdateUser, creationDate, lastupdatedDate) VALUES (?,?,?,?,?,?,?,?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("issiiiss", $projectID, $issue["name"], $issue["des"], $issue["status"], $userID, $userID, $currentDate, $currentDate)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            $last_id = mysqli_insert_id($conn);
            $stmt->close();
        }
        header("Refresh: 0");
    }

    // New comment for certain issue
    function addIssueNewComment($conn, $issueID, $comment, $userID){
        $currentDate = getCurrentDate();

        if(!($stmt = $conn->prepare("INSERT INTO issuecomments (idIssue, idUser, comment, creationDate) VALUES (?,?,?,?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("iiss", $issueID, $userID, $comment, $currentDate)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            $stmt->close();
        }
        header("Refresh: 0");
    }

// END ISSUE

// Invite

    function addUserToProject($conn, $projectID, $userID){
        $memberRole = 4;
        $query = "INSERT INTO projectmembers (idProject, idUser, idRole) VALUES ('$projectID', '$userID', $memberRole);";
        if (!$conn->query($query)) {
            die("Error adding user to project");
        } else {
            header("Location: /projectmanager/dashboard/projects");
        }
        return;
    }

// END invite

    // New Milestone
    function addNewMilestone($conn, $projectID, $userID, $milestone){
        $currentDate = getCurrentDate();

        if(!($stmt = $conn->prepare("INSERT INTO milestones (idProject, name, idStatus, targetDate, idCreator, creationDate, idUpdateUser, lastupdateDate) VALUES (?,?,?,?,?,?,?,?)"))) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        if(!$stmt->bind_param("isisisis", $projectID, $milestone["name"], $milestone["status"], $milestone["targetDate"], $userID, $currentDate, $userID, $currentDate)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        if(!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        } else{
            $last_id = mysqli_insert_id($conn);
            $stmt->close();
        }
        header("Refresh: 0");
    }

?>