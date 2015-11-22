<?php

if(!session_id()) {

    session_start();
}

//session_destroy();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>TODO Notes</title>
        <meta author="Luke A Chase">
        <meta description="CodeChallenge - Week 5">
    </head>
    <body>
        <div id="wrapper">

            <a id="addTodo" href="<?= $_SERVER[PHP_SELF] ?>?todo=new">Add TODO Note</a>


            <?php

            include 'utility.php';

            
            if ($_GET['todo'] == "new") {  // "addTodo" button clicked?

                ?>

            <form action="" method="post">
                <label id="todoNoteLabel" for="todoNote">TODO:</label>
                <input type="text" id="todoNote" name="todoNote" placeholder="new TODO here"
                        value="<?= isset($_SESSION['todoNote']) ? $_SESSION['todoNote'] : "" ?>"><br>
                <label id="dateDueLabel" for="dueDate">Due Date:</label>
                <input type="date" id="dueDate" name="dueDate" placeholder="mm|dd|yyyy"
                        value="<?= isset($_SESSION['dueDate']) ? $_SESSION['dueDate'] : "" ?>"><br>
                <label id="todoCategoryLabel" for="todoCategory">Type:</label>
                <select id="todoCategory" name="todoCategory">

                    <?php

                    require 'dbAdapter.php';

                    try {
                        
                        $sql = "SELECT * FROM Category";

                        $items = $pdo->query($sql);
                    }
                    catch (DBException $ex) {
                        
                        $errorMsg = "Error reading the Category table: " . $ex->getMessage();

                        include 'exception.php';
                    }

                    while ($row = $items->fetch()) {

                        echo "\t<option value=\"$row[catId]\">$row[catName]</option>\n";
                    }
                    
                    ?>

                    <option value="-1"<?= isset($_SESSION['setNewCategory']) ? " selected" : "" ?>>
                        [ New Category ]</option>
                </select><br>
                
                <?php


                if (isset($_SESSION['newCategoryAdded'])) {

                    echo "<h3 id=\"errorMsg\">Please select your new category from the list</h3>";
                }


                if ($newNote = ($_POST['todoNote'])) {  // 'TODO' field not empty

                    //$_SESSION['todoNote'] = $newNote;

                    if ($dueDate = dateFormat($_POST['dueDate'])) {  // 'Due Date' field valid date
                        
                        //$_SESSION['dueDate'] = $dueDate;

                        if (dateIsFuture($dueDate)) {  // 'Due Date' not date in past

                            //$_SESSION['dueDate'] = $dueDate;

                            if ($_POST['todoCategory'] == -1) { // Category is "New Category"

                                $_SESSION['setNewCategory'] = true;

                                ?>

                <label id="newCategoryLabel" for="newCategory">New Category:</label>
                <input type="text" id="newCategory" name="newCategory" placeholder="new category name"
                        value="<?= $_SESSION['newCategory'] == true ? $_SESSION['newCategory'] : "" ?>"><br>

                                <?php

                                if ($newCategory = $_POST['newCategory']) {  // Category is not blank

                                    $_SESSION['newCategory'] = $newCategory;

                                    require 'dbAdapter.php';

                                    try {

                                        $sql = "SELECT catName FROM Category "
                                                . "WHERE catName = '$newCategory'";

                                        $categories = $pdo->query($sql);
                                        $isNewCategory = $categories->fetch()[0];

                                        if ($isNewCategory == "") {  // Category does not already exist

                                            try {

                                                $sql = "INSERT INTO Category (catName) "
                                                        . "VALUES (:catName)";

                                                $insert = $pdo->prepare($sql);
                                                $insert->bindValue(':catName', $catName);

                                                $insert->execute();

                                                $_SESSION['newCategoryAdded'] = true;
                                            }
                                            catch (PDOException $ex) {

                                                $errorMsg = "Error creating new category: " . $ex->getMessage();

                                                include 'exception.php';
                                            }
                                        }
                                        else {

                                            $errorMsg = "Category already exists";
                                        }
                                    }
                                    catch (PDOException $ex) {

                                        $errorMsg = "Error searching for existing category: "
                                                    . $ex->getMessage();

                                        include 'exception.php';
                                    }
                                }  // end category is not blank
                            }
                            else {  // category is not -1

                                require 'dbAdapter.php';

                                $category = $_POST['todoCategory'];
                                $dueDate = dateFormatToSql($dueDate);
                                $completed = 0;

                                try {  // SQL insertion of new TODO note

                                    $sql = "INSERT INTO Todo_Notes (todoNote, catId, dueDate, completed) "
                                        . "VALUES (:todoNote, :catId, :dueDate, :completed)";

                                    $insert = $pdo->prepare($sql);
                                    $insert->bindValue(':todoNote', $newNote);
                                    $insert->bindValue(':catId', $category);
                                    $insert->bindValue(':dueDate', $dueDate);
                                    $insert->bindValue(':completed', $completed);

                                    $insert->execute();

                                    session_destroy();
                                }
                                catch (Exception $ex) {

                                        $errorMsg = "Error creating new TODO note: " . $ex->getMessage();

                                        include 'exception.php';
                                }
                            }
                        }
                        else {  // 'Due Date' already passed

                            $errorMsg = "Date has already passed - please choose today or a future date";
                        }
                    }
                    else {  // 'Due Date' field not valid

                        $errorMsg = "Date is not valid - please use \"dd|mm|yyyy\" format";
                    }
                }
                elseif (isset($_POST['todoNote'])) {  // 'TODO' field empty
                    
                    $errorMsg = "New note is blank";
                }

                echo "<h3 id=\"errorMsg\">" . $errorMsg . "</h3><br>";

                ?>

                <input type="submit" name="addTodoNote" value="Add TODO Note">
            </form>

                <?php

            }  // end "addTodo" button clicked?

            ?>

            <h2 id="todoHeading">TODO Notes:</h2>
            <div id="todoNotesDiv">


                <?php
                
                require 'dbAdapter.php';

                try {
                    
                    $sql = "SELECT todoNote, dueDate, catName, completed "
                         . "FROM Todo_Notes, Category "
                         . "WHERE Todo_Notes.catId = Category.catId "
                         . "ORDER BY dueDate";

                    $todoNotes = $pdo->query($sql);
                }
                catch (PDOException $ex) {

                    echo "ERROR!";
                        $errorMsg = "Error reading the database: " . $ex->getMessage();

                        include 'exception.php';
                }

                ?>


                <table id="todoNotesTable" style="border: 1px solid #000">
                    <tr>
                        <th>TODO:</th><th>Category:</th><th>Due Date</th><th>Completed?</th>
                    </tr>
                

                <?php
                while ($note = $todoNotes->fetch()) {

                    ?>

                    <tr>
                        <td><?= $note["todoNote"] ?></td>
                        <td><?= $note["catName"] ?></td>
                        <td><?= dateFormatFromSql($note["dueDate"]) ?></td>
                        <td><?= $note["completed"] == 0 ? "NO" : "YES" ?></td>
                    </tr>


                    <?php
                }
                ?>


                </table>
            </div>
        </div>
    </body>
</html>
