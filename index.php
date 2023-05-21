<?php
    $errors = "";

    $db = mysqli_connect('localhost', 'root', '', 'todo');

    if (isset($_POST['submit'])) {
        $task = $_POST['task'];
        $schedule = $_POST['schedule'];
        if (empty($task)) {
            $errors = "You must fill in the task";
        } else {
            mysqli_query($db, "INSERT INTO tasks (task, schedule) VALUES ('$task', '$schedule')");
            header('location: index.php');
            exit();
        }
    }

    if (isset($_GET['del_task'])) {
        $id = $_GET['del_task'];
        mysqli_query($db, "DELETE FROM tasks WHERE id=$id");
        header('location: index.php');
        exit();
    }

    $task = mysqli_query($db, "SELECT * FROM tasks ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Todo List Application</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
    <div class="header">
        <h2><span>Do</span>IT<span>Now</span></h2>
    </div>

    <form method="POST" action="index.php">
    <?php if (isset($errors)) { ?>
        <p><?php echo $errors; ?></p>
    <?php } ?>

        <input type="text" name="task" class="task-input">
        <input type="datetime-local" name="schedule" class="task-input" id="sched" placeholder="Schedule">
        <button type="submit" class="task-btn" name="submit">Add Task</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>New</th>
                <th>Task</th>
                <th>Schedule</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php $i = 1; while ($row = mysqli_fetch_array($task)) { ?>
            <?php
                $scheduleTime = strtotime($row['schedule']);
                $currentTime = time();
                $isTaskScheduled = $scheduleTime <= $currentTime;
                $taskClass = $isTaskScheduled ? 'task scheduled' : 'task';
            ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td class="task <?php echo $taskClass; ?>"><?php echo $row['task']; ?></td>
                <td class="schedule"><?php echo $row['schedule']; ?></td>
                <td class="delete">
                    <a href="index.php?del_task=<?php echo $row['id']; ?>">x</a>
                </td>
            </tr>

        <?php $i++; } ?>
        </tbody>
    </table>
    </div>

    <script>
         setTimeout(function() {
            location.reload();
        }, 60000);

        var scheduledTasks = document.getElementsByClassName('scheduled');
        for (var i = 0; i < scheduledTasks.length; i++) {
            var task = scheduledTasks[i];
            var scheduleTime = new Date(task.textContent).getTime();
            var currentTime = new Date().getTime();

            if (currentTime >= scheduleTime) {
                var notification = document.createElement('div');
                notification.className = 'notification';
                notification.textContent = 'Task schedule has been triggered!';
                task.parentNode.insertBefore(notification, task.nextSibling);
            }
        }   
    </script>
</body>
</html>