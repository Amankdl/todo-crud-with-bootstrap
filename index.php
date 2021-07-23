<?php
  setCookie("name","Aman",time()+60,"/"); 
?>
<!doctype html>
<html lang="en">
<?php
$serverAddress = "localhost";
$username = "root";
$password = "";
$database = "todolist";
$connection = mysqli_connect($serverAddress, $username, $password, $database);
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>To-do list</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .w-60 {
            width: 70% !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">2-Do List</span>
        </div>
    </nav>
    <?php
    foreach($_COOKIE as $key => $value){
        echo $key.' '.$value.'<br>'; 
    }
    if (isset($_GET['delete'])) {
        if (!$connection) {
            die("Connection failed : " . mysqli_connect_error());
        }
        $sno = $_GET['delete'];
        $deleteRecord = "DELETE FROM `tasklist` WHERE `tasklist`.`sno` = $sno";
        mysqli_query($connection, $deleteRecord);
        $isRecordDeleted = mysqli_affected_rows($connection);
        if ($isRecordDeleted) {
            echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Task deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Oh Sorry!</strong> Task not deleted.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['serialNumberId'])) {
            $editTitle = $_POST['editTitle'];
            $editDescription = $_POST['editDesc'];
            
            if (!$connection) {
                die("Connection failed : " . mysqli_connect_error());
            }
            $sno = $_POST['serialNumberId'];
            $updateRecord = "UPDATE `tasklist` SET `title` = '$editTitle', `description` = '$editDescription' WHERE `tasklist`.`sno` = $sno;";
            $isRecordUpdated = mysqli_query($connection, $updateRecord);
            if ($isRecordUpdated) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Task updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Oh Sorry!</strong> Task not added.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
            }
        } else {
            $title = $_POST['title'];
            $description = $_POST['desc'];

            if (!$connection) {
                die("Connection failed : " . mysqli_connect_error());
            }

            $insertRecord = "INSERT INTO `tasklist` (`title`, `description`, `date`) VALUES ('$title', '$description', current_timestamp());";
            $isRecordInserted = mysqli_query($connection, $insertRecord);
            if ($isRecordInserted) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Task added successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Oh Sorry!</strong> Task not added.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
            }
        }
    }
    ?>
    <div class="container w-60">
        <h1 class="mt-5">Add a Note Broda</h1>
        <form action="index.php" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">New Title</label>
                <input name="title" type="text" class="form-control" id="title" placeholder="Enter task title here">
            </div>
            <div class="mb-3">
                <label for="desc" class="form-label">New Description</label>
                <textarea name="desc" class="form-control" id="desc" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Note</button>
        </form>
        <?php

        ?>
        <table class="table mt-5" id="myTable">
            <thead>
                <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Title</th>
                    <th scope="col">Discription</th>
                    <th scope="col">Date</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $getRecords = "SELECT * from tasklist";
                $records = mysqli_query($connection, $getRecords);
                $data = "";
                if (mysqli_num_rows($records) > 0) {
                    $count = 1;
                    while ($recordsArray = mysqli_fetch_assoc($records)) {
                        echo '<tr>
                            <th scope="row">' . $count++ . '</th>
                            <td>' . $recordsArray['title'] . '</td>
                            <td>' . $recordsArray['description'] . '</td>
                            <td>' . $recordsArray['date'] . '</td>
                            <td><button type="button" class="edit btn btn-sm btn-primary" id="' . $recordsArray['sno'] . '">Edit</button>
                            <button type="button" class="delete btn btn-sm btn-primary" id="d' . $recordsArray['sno'] . '">Delete</button></td>
                        </tr>';
                    }
                } else {
                    echo '<tr>
                    <td style="text-align: center;" colspan="4">No task available.</td>
                </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteBtn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="deleteConfirmation">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="deleteTask()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!--Edit Modal -->
    <div class="modal fade" id="editBtn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input name="serialNumberId" type="hidden" id="serialNumberId">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input name="editTitle" type="text" class="form-control" id="editTitle" placeholder="Enter task title here">
                        </div>
                        <div class="mb-3">
                            <label for="desc" class="form-label">Description</label>
                            <textarea name="editDesc" class="form-control" id="editDesc" rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        Array.from(document.getElementsByClassName('edit')).forEach(element => {
            element.addEventListener('click', (e) => {
                var myEditModal = new bootstrap.Modal(document.getElementById('editBtn'), {
                    keyboard: false
                });
                myEditModal.toggle();
                editTitle.value = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
                editDesc.value = e.target.parentElement.previousElementSibling.previousElementSibling.innerText;
                serialNumberId.value = e.target.id;
            }, false);
        });

        var taskId;
        Array.from(document.getElementsByClassName('delete')).forEach(element => {
            element.addEventListener('click', (e) => {
                var myDeleteModal = new bootstrap.Modal(document.getElementById('deleteBtn'), {
                    keyboard: false
                });
                myDeleteModal.toggle();
                taskId = e.target.id;
                document.getElementById("deleteConfirmation").innerText = `Are you sure you want to delete task number ${e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText} ?`;
            }, false);
        });

        function deleteTask(){
            window.location = `/login/index.php?delete=${taskId.substr(1,)}`;
        }
    </script>
</body>

</html>