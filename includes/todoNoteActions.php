<?php                

if ($action == "addTodo" || $action == "editTodo") {  // validate the form

    if ($todoNote = $formTodoNote) {  // 'TODO' field not empty

        $todoNoteValid = true;
    }
    else {  // 'TODO' field empty
        
        validationError("TODO note is blank");
    }


    if ($dueDate = dateFormat($formTodoDueDate)) {  // 'Due Date' field valid date
        
        $dueDateValid = true;
    }
    else {  // 'Due Date' field not valid

        validationError("Date is not valid - please use \"dd|mm|yyyy\" format");
    }


    if (dateIsFuture($dueDate)) {  // 'Due Date' not date in past

        $dueDateIsFuture = true;
    }
    else {  // 'Due Date' already passed

        validationError("Date has already passed - please choose today or a future date");
    }
}


if ($todoNoteValid && $dueDateValid && $dueDateIsFuture) {

    if ($action == "addTodo") {

        $dueDate = dateFormatToSql($dueDate);
        $catId = $formTodoCategory;
        $completed = 0;

        require_once 'dbAdapter.php';

        try {

            $sql = "INSERT INTO Todo_Notes (todoNote, catId, dueDate, addDate, completed) "
                . "VALUES (:todoNote, :catId, :dueDate, CURDATE(), :completed)";

            $insert = $pdo->prepare($sql);
            $insert->bindValue(':todoNote', $todoNote);
            $insert->bindValue(':catId', $catId);
            $insert->bindValue(':dueDate', $dueDate);
            $insert->bindValue(':completed', $completed);

            $insert->execute();
        }
        catch (PDOException $ex) {

                $validationError = "Error creating new TODO note: " . $ex->getMessage();
                $_SESSION['action'] = "";

                include 'exception.php';
        }

        $_POST['todoId'] = "";
        $_POST['todoNote'] = "";
        $_POST['todoDueDate'] = "";
        $_POST['todoCategory'] = "";

        $action = "";
        $_SESSION['action'] = "";
        echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';
    }
    elseif ($action == "editTodo") {  // section is currently not working as intended;
                                      // commented out the "Edit" button for now
        require_once 'dbAdapter.php';

        try {  // SQL insertion of new TODO note


            $sql = "UPDATE Todo_Notes SET todoNote=:todoNote, "
                    . "catId=:catId, dueDate=:dueDate "
                    . "WHERE noteId=$formTodoId";

            $update = $pdo->prepare($sql);
            $update->bindValue(':todoNote', $todoNote);
            $update->bindValue(':catId', $catId);
            $update->bindValue(':dueDate', $dueDate);

            $update->execute();
        }
        catch (PDOException $ex) {

                $validationError = "Error updating TODO note: " . $ex->getMessage();
                $_SESSION['action'] = "";

                include 'exception.php';
        }

        $_POST['todoId'] = "";
        $_POST['todoNote'] = "";
        $_POST['todoDueDate'] = "";
        $_POST['todoCategory'] = "";

        $action = "";
        $_SESSION['action'] = "";
    }

    //echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';
}


if ($action == "delete") {

    $noteId = $formTodoId;
    
    require_once 'dbAdapter.php';

    try {

        $sql = "DELETE FROM Todo_Notes WHERE noteId=:noteId";

        $delete = $pdo->prepare($sql);
        $delete->bindValue(':noteId', $noteId);

        $delete->execute();
    }
    catch (Exception $ex) {

            $errorMsg = "Error deleting TODO note: " . $ex->getMessage();
            $_SESSION['action'] = "";

            include 'exception.php';
    }
}