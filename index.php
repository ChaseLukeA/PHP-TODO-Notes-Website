<?php


if (!session_id()) {

    session_start();
}


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>TODO Notes</title>
        <meta author="Luke A Chase">
        <meta description="CodeChallenge - Week 5">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div id="wrapper">
            <header>
                <h1>TODO Notes</h1>
                <h3>Keeping Track of Your Life</h3>
                <hr>
            </header>
            <?php


            //-database connection definitions-==============================-//

                $HOST = "localhost";
                $PORT = "3306";
                $DBNAME = "todo_notes";
                $USERNAME = "lchase1";
                $PASSWORD = "phpIsDaBomb";

            //-==============================================================-//


            // setting form variables so form displays blank on start, redisplays
            // current fields if add not successful, and displays the current
            // values of an existing note for editing
            $formTodoId = $_POST['todoId'] != "" ? $_POST['todoId'] : "";
            $formTodoNote = $_POST['todoNote'] != "" ? $_POST['todoNote'] : "";
            $formTodoDueDate = $_POST['todoDueDate'] != "" ? $_POST['todoDueDate'] : "";
            $formTodoCategory = $_POST['todoCategory'] != "" ? $_POST['todoCategory'] : "1";


            // my custom functions for this app
            include_once './includes/utility.php';


            if (isset($_POST['action'])  // cancel pressed
                    && $_POST['action'] == "Cancel") {

                $_SESSION['action'] = "";
                echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';
            }  // end cancel


            if (isset($_POST['action'])  // display form
                    && $_POST['action'] == "New TODO Note") {

                // included this so on refresh the form doesn't show the
                // "New TODO Note" button again
                $_SESSION['action'] = "addTodo";
                include './includes/todoNoteForm.html.php';
            }  // end display form

            elseif ((isset($_POST['action'])  // add note
                    && $_POST['action'] == "Add TODO Note")
                    || $_SESSION['action'] == "addTodo") {

                $action = "addTodo";
                $_SESSION['action'] = $action;

                include './includes/todoNoteForm.html.php';
                include './includes/todoNoteActions.php';
            }  // end add note


            // the issue with edit note is I can get the exiting note's info
            // to redisplay in the form and modify the form so it states
            // "Update TODO Note" - but the note ends up deleting when running
            // the UPDATE SQL script I have written in todoNoteActions.php
            if ((isset($_POST['action'])  // edit note
                    && $_POST['action'] == "Edit")
                    || $_SESSION['action'] == "editTodo") {

                $action = "editTodo";
                $_SESSION['action'] = $action;
                
                $noteId = $formTodoId;
                
                require_once './includes/dbAdapter.php';

                try {
                    
                    $sql = "SELECT noteId, todoNote, dueDate, catId "
                         . "FROM Todo_Notes "
                         . "WHERE noteId=$noteId";

                    $todoNotes = $pdo->query($sql);
                }
                catch (PDOException $ex) {

                    echo "ERROR!";
                        $errorMsg = "Error querying the database for edit: " . $ex->getMessage();
                        $_SESSION['action'] = "";

                        include './includes/exception.php';
                }

                $note = $todoNotes->fetch();

                $formTodoId = $note['noteId'];
                $formTodoNote = $note['todoNote'];
                $formTodoDueDate = dateFormatFromSql($note['dueDate']);
                $formTodoCategory = $note['catId'];

                include './includes/todoNoteForm.html.php';
                include './includes/todoNoteActions.php';
            }  // end edit note

            elseif (isset($_POST['action'])  // delete note
                    && $_POST['action'] == "Delete") {

                $currentAction = $action;
                $action = "delete";
                include './includes/todoNoteActions.php';
            }  // end delete note


            if ($_SESSION['action'] != "addTodo"  // display "New TODO Note" button
                    && $action != "addTodo"       // only when add/edit form not shown
                    && $_SESSION['action'] != "editTodo"
                    && $action != "editTodo") {


                ?>
            <form method="post">
                <input type="submit" id="newTodo" name="action" value="New TODO Note">
            </form>
            <?php


            }  // end display "New TODO Note" button


            ?>
            <div id="todoNotesDiv">
                <h2>TODO Notes:</h2>
                <?php


                
                require_once './includes/dbAdapter.php';

                try {
                    
                    $sql = "SELECT noteId, todoNote, dueDate, addDate, catName, completed "
                         . "FROM Todo_Notes, Category "
                         . "WHERE Todo_Notes.catId=Category.catId "
                         . "ORDER BY dueDate";

                    $todoNotes = $pdo->query($sql);
                }
                catch (PDOException $ex) {

                    echo "ERROR!";
                        $errorMsg = "Error reading the database: " . $ex->getMessage();

                        $_SESSION['action'] = "";

                        include './includes/exception.php';
                }


                ?>
                <table id="todoNotesTable">
                    <tr >
                        <th>TODO Note:</th>
                        <th>Category:</th>
                        <th>Date Due:</th>
                        <th>Done?</th>
                        <th>Date Added:</th>
                        <th></th>
                    </tr>
                <?php


                while ($note = $todoNotes->fetch()) {


                    ?>
                    <tr>
                        <td><?= $note["todoNote"] ?></td>
                        <td><?= $note["catName"] ?></td>
                        <td><?= dateFormatFromSql($note["dueDate"]) ?></td>
                        <td><?= $note["completed"] == 0 ? "[&nbsp;&nbsp;]" : "[X]" ?></td>
                        <td><?= dateFormatFromSql($note["addDate"]) ?></td>
                        <td>
                            <form method="post">
                                <!-- I can't get the edit function to work properly
                                so am commenting out the button for now, code stays
                                <input type="submit" name="action" value="Edit">-->
                                <input type="submit" id="deleteButton" name="action" value="Delete">
                                <input type="hidden" name="todoId" value="<?= $note["noteId"] ?>">
                            </form>
                        </td>
                    </tr>
                <?php


                }


                ?>
                </table>
            </div>
        </div>
    </body>
</html>