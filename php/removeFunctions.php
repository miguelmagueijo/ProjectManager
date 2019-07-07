<?php

// TASK
    
    // Remove user follow from data base
    function removeUserTaskFollow($conn, $taskID, $userID){
        $query = "DELETE FROM taskfollow WHERE idTask=$taskID AND idUser=$userID";
        if (!$conn->query($query)) {
            die("Error DUTF");
        } else {
            header("Refresh: 0");
            return;
        }
        die();
    }

    // Remove task from database
    function removeTask($conn, $projectID, $taskID){
        $query = "DELETE FROM tasks WHERE id=$taskID AND idProject=$projectID";
        if (!$conn->query($query)) {
            die("Error DT");
        } else {
            header("Location: /projectmanager/project/tasks?id=$projectID");
            return;
        }
        die("Error FDT");
    }

// END OF TASK

// Issue

    // Remove user follow from data base
    function removeUserIssueFollow($conn, $issueID, $userID){
        $query = "DELETE FROM issuefollow WHERE idIssue=$issueID AND idUser=$userID";
        if (!$conn->query($query)) {
            die("Error DUIF");
        } else {
            header("Refresh: 0");
            return;
        }
        die();
    }

    // Remove issue from database
    function removeIssue($conn, $projectID, $issueID){
        $query = "DELETE FROM issues WHERE id=$issueID AND idProject=$projectID";
        if (!$conn->query($query)) {
            die("Report with error DT");
        } else {
            header("Location: /projectmanager/project/tasks?id=$projectID");
            return;
        }
        die("Repor with error FDT");
    }

// Issue

// PROJECT

    // Remove user from project
    function removeUserFromProject($conn, $userID, $projectID){
        $query = "DELETE FROM projectmembers WHERE idProject=$projectID AND idUser=$userID";
        if (!$conn->query($query)) {
            die();
        }
        $query = "DELETE FROM taskfollow WHERE idUser=$userID";
        if (!$conn->query($query)) {
            die();
        }
        header("Refresh: 0");
    }

// END PROJECT

// Remove user from database ALL DATA IS ERASED

    function REMOVEALLuserInfoFromDataBase($conn, $userID){
        $sql = "DELETE FROM usersecurity WHERE idUser = $userID";
        if (!mysqli_query($conn, $sql)) {
            die("Error deleting user security!");
        }
        $sql = "DELETE FROM issuecomments WHERE idUser=$userID";
        if (!mysqli_query($conn, $sql)) {
            die("Error deleting issuecomments!");   
        }
        $sql = "DELETE FROM issuefollow WHERE idUser=$userID";
        if (!mysqli_query($conn, $sql)) {
            die("Error deleting user!");   
        }
        $sql = "UPDATE issues SET idUpdateUser=13 WHERE idUpdateUser=$userID;";
        if (!mysqli_query($conn, $sql)) {
            die("Error updating issues!");   
        }
        $sql = "DELETE FROM issues WHERE idCreator=$userID";
        if (!mysqli_query($conn, $sql)) {
            die("Error deleting issues!");   
        }
        $sql = "DELETE FROM issues WHERE idCreator=$userID";
        if (!mysqli_query($conn, $sql)) {
            die("Error deleting issues!");   
        }
        $sql = "DELETE FROM taskcomments WHERE idUser=$userID";
        if (!mysqli_query($conn, $sql)) {
            die("Error deleting taskscomments!");   
        }
        $sql = "DELETE FROM taskfollow WHERE idUser=$userID";
        if (!mysqli_query($conn, $sql)) {
            die("Error deleting taskfollow!");   
        }
        $sql = "UPDATE tasks SET idUpdateUser=13 WHERE idUpdateUser=$userID;";
        if (!mysqli_query($conn, $sql)) {
            die("Error updating tasks!");   
        }
        $sql = "DELETE FROM tasks WHERE idCreator=$userID";
        if (!mysqli_query($conn, $sql)) {
            die("Error deleting tasks!");   
        }
        $sql = "DELETE FROM projectmembers WHERE idUser=$userID";
        if (!mysqli_query($conn, $sql)) {
            die("Error deleting projectmembers!");   
        }
        $sql = "UPDATE projects SET idUpdateUser=13 WHERE idUpdateUser=$userID;";
        if (!mysqli_query($conn, $sql)) {
            die("Error updating projects!");   
        }
        $sql = "DELETE FROM projects WHERE idCreator=$userID";
        if (!mysqli_query($conn, $sql)) {
            die("Error deleting projects!");   
        }
        $sql = "DELETE FROM user WHERE id=$userID";
        if (!mysqli_query($conn, $sql)) {
            die("Error deleting user!");   
        }
        session_destroy();
        header("Refresh:0");
    }

// End Remove user from database

?>