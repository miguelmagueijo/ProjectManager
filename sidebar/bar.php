<a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars" style="padding-top: 15px"></i>
</a>
<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content">
    
        <!-- Sidebar Title -->
        <div class="sidebar-brand">
            <a href="/projectmanager/dashboard">Project Manager</a>
            <div id="close-sidebar">
                <i class="fas fa-times"></i>
            </div>
        </div>

        <!-- Sidebar User -->
        <div class="sidebar-header">
            <div class="user-pic">
                <!-- <img class="img-responsive img-rounded" style="height: auto !important;" src="/projectmanager/img/UIMG/<?php #echo $UserData["id"] ?>.png"
                 alt="User picture"> -->
                <img class="img-responsive img-rounded" style="height: auto !important;" src="/projectmanager/img/UIMG/<?php if ($UserData["role"] == 20){ echo "8";}else{echo "9";} ?>.png" alt="User picture">
            </div>
            <div class="user-info">
                <span class="user-name">
                    <?php
                        echo "<strong>".$UserData["username"]."</strong>";
                    ?>
                </span>
                <?php
                    if ($UserData["role"] == 20){
                        echo "<span class='user-role'>Administrator</span>";
                    } else {
                        echo "<span class='user-role'>Member</span>";
                    }
                ?>
            </div>
        </div>
        
        <?php
            if(isset($_POST["Sproject"])){
                if ($_POST["Sproject"] == "new"){
                    header("Location: /projectmanager/dashboard/newproject");
                } elseif (is_numeric($_POST["Sproject"])) {
                    header("Location: /projectmanager/project/?id=$_POST[Sproject]");
                }
            }
            if(isset($_GET["id"])){
                $barProjectID = $_GET["id"];
            }
        ?>
        <!-- Sidebar Project / now search -->
        <div class="sidebar-search">
            <div>
                <div class="input-group">
                    <form method="POST" class="col-md-12" style="margin-bottom:0px">
                        <select name="Sproject" class="form-control" style="background: #3a3f48; color:white; border: none" onchange="this.form.submit()">
                        <?php
                            if (!isset($barProjectID)){
                                echo "<option value='null' disabled selected> Select project </option>";
                            }
                            $query = "SELECT p.* FROM user AS u JOIN projectmembers as pm ON u.id = pm.idUser JOIN projects as p ON pm.idProject = p.id WHERE u.id=".$_SESSION['user']['id'].";";
                            if ($result = $conn->query($query)) {
                                if ($result->num_rows == 0) {
                                    echo "<option value='new'> New project </option>";
                                } else {
                                    while ($row = $result->fetch_array(MYSQLI_ASSOC)){
                                        if(isset($barProjectID) && $row["id"] == $barProjectID){
                                            echo "<option value='null' disabled selected> $row[name] </option>";
                                        } else {
                                            echo "<option value='$row[id]'>$row[name]</option>";
                                        }
                                    }
                                }
                                $result->close();
                            } else {
                                printf("Error in select user query");
                                die();
                            }
                        ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>

                    <!-- Menu -->
                    <div class="sidebar-menu">
                        <ul>
                            <!-- Menu Title -->
                            <li class="header-menu">
                                <span>General</span>
                            </li>

                            <?php
                                if (isset($barProjectID)){
                                    echo "
                                        <!-- Menu Projects -->
                                        <li>
                                            <a href='/projectmanager/project/?id=$projectID'>
                                                <i class='fas fa-project-diagram'></i>
                                                <span>Project page</span>
                                            </a>
                                        </li>
                                        <!-- End Menu Projects -->

                                        <!-- Menu Tasks -->
                                        <li>
                                            <a href='/projectmanager/project/tasks/?id=$projectID'>
                                                <i class='fa fa-tasks'></i>
                                                <span>All Tasks</span>
                                            </a>
                                        </li>
                                        <!-- End Menu Projects -->

                                        <!-- Menu issues -->
                                        <li>
                                            <a href='/projectmanager/project/issues/?id=$projectID'>
                                                <i class='fas fa-bug'></i>
                                                <span>All Issues</span>
                                            </a>
                                        </li>
                                        <!-- End Menu issues -->

                                        <!-- Menu Milestones -->
                                        <li>
                                            <a href='/projectmanager/project/milestones?id=$projectID'>
                                                <i class='fa fa-flag'></i>
                                                <span>All Milestones</span>
                                            </a>
                                        </li>
                                        <!-- End Menu Milestones -->
                                    ";
                                } else {
                                    echo "
                                        <!-- Menu Projects -->
                                        <li>
                                            <a href='/projectmanager/dashboard/projects'>
                                                <!-- <i class='fas fa-folder'></i> -->
                                                <i class='fas fa-project-diagram'></i>
                                                <span>Projects</span>
                                            </a>
                                        </li>
                                        <!-- End Menu Projects -->

                                        <!-- Menu Tasks -->
                                        <li>
                                            <a href='/projectmanager/dashboard/tasks'>
                                                <i class='fa fa-tasks'></i>
                                                <span>Tasks</span>
                                            </a>
                                        </li>
                                        <!-- End Menu Projects -->

                                        <!-- Menu issues -->
                                        <li>
                                            <a href='/projectmanager/dashboard/issues'>
                                                <i class='fas fa-bug'></i>
                                                <span>Issues</span>
                                            </a>
                                        </li>
                                        <!-- End Menu issues -->
                                    ";
                                }
                            ?>

                            <!-- Menu Example -->
                            <!-- <li class="sidebar-dropdown">
                                <a href="#">
                                    <i class="fa fa-globe"></i>
                                    <span>Example</span>
                                </a>
                                <div class="sidebar-submenu">
                                    <ul>
                                        <li>
                                        <a href="#">Option 1</a>
                                        </li>
                                        <li>
                                        <a href="#">Option 2</a>
                                        </li>
                                    </ul>
                                </div>
                            </li> -->
                            <!-- End Menu Example -->

                            <li class="header-menu">
                                <span>Extra</span>
                            </li>
                            
                            <li>
                                <a href="/projectmanager/dashboard/newproject">
                                    <i class="fa fa-project-diagram"></i>
                                    <span>Create/Join Project</span>
                                </a>
                            </li>

                            <li>
                                <a href="/projectmanager/report">
                                    <i class="fas fa-exclamation"></i>
                                    <span>Report bug/problem</span>
                                </a>
                            </li>
                            
                            <!-- Example -->
                            <!-- <li>
                                <a href="#">
                                    <i class="fa fa-folder"></i>
                                    <span>Examples</span>
                                </a>
                            </li> -->
                            <!-- Example -->
                            <?php 
                                if($UserData["role"] == 20){
                                    echo "
                                    <li class='header-menu'>
                                        <span>Admin Tools</span>
                                    </li>
                                    <li class='sidebar-dropdown'>
                                        <a class='pointer-mouse'>
                                            <i class='fa fa-tachometer-alt'></i>
                                            <span>Dashboard</span>
                                        </a>
                                        <div class='sidebar-submenu'>
                                            <ul>
                                                <li>
                                                    <a href='/projectmanager/admin/users'>All users</a>
                                                </li>
                                                <li>
                                                    <a href='/projectmanager/admin/reports'>Reports</a>
                                                </li>
                                                <li>
                                                    <a href='/projectmanager/admin/projects'>All projects</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    ";
                                }
                            ?>

                        </ul>
                    </div>

                </div>
                
    <div class="sidebar-footer">
        <a href="/projectmanager/user/" style="padding-top:5px">
            <i class="fas fa-user-cog"></i>
        </a>
        <a href="/projectmanager/logout.php" style="padding-top:5px">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</nav>