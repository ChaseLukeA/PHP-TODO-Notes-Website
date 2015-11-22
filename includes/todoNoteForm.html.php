            <form id="todoNoteForm" method="post">
                <input type="hidden" name="todoId" value="<?= $formTodoId ?>">
                <label id="todoNoteLabel" for="todoNote">TODO:</label>
                <input type="text" id="todoNote" name="todoNote" placeholder="new TODO here" 
                       value="<?= $formTodoNote ?>"><br>
                <label id="todoDueDateLabel" for="todoDueDate">Due Date:</label>
                <input type="text" id="todoDueDate" name="todoDueDate" placeholder="mm-dd-yyyy" 
                       value="<?= $formTodoDueDate ?>"><br>
                <label id="todoCategoryLabel" for="todoCategory">Type:</label>
                <select id="todoCategory" name="todoCategory">
                <?php

                
                require_once 'dbAdapter.php';

                try {
                    
                    $sql = "SELECT * FROM Category";

                    $items = $pdo->query($sql);
                }
                catch (DBException $ex) {
                    
                    $errorMsg = "Error reading the Category table: " . $ex->getMessage();

                    $_SESSION['action'] = "";

                    include 'exception.php';
                }

                while ($row = $items->fetch()) {

                    if ($row['catId'] == $formTodoCategory) {
                        $isSelected = " selected";
                    }
                    else {
                        $isSelected = "";
                    }


                    ?>
                    <option value="<?= $row[catId] ?>"<?= $isSelected ?>><?= $row[catName] ?></option>
                    <?php


                }  // end while $row

                if ($action == "addTodo") {

                    $label = "Add TODO Note";
                }
                elseif ($action == "editTodo") {

                    $label = "Update TODO Note";
                }
                else {

                    $label = "Add TODO Note";
                }


                ?>
                </select><br>
                <input id="cancelButton" type="submit" name="action" value="Cancel">
                <input id="addEditButton" type="submit" name="action" value="<?= $label ?>">
            </form>