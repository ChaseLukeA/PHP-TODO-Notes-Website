<?php

// NOTE:
// You need to create a database called "todo_notes" to use this
// php file to create and populate the tables
//

//-database connection definitions-==============================-//

    $HOST = "localhost";
    $PORT = "3306";
    $DBNAME = "todo_notes";
    $USERNAME = "lchase1";
    $PASSWORD = "phpIsDaBomb";

//-==============================================================-//

require './includes/dbAdapter.php';


try {

    $pdo->exec("DROP TABLE IF EXISTS Todo_Notes");

    $sql = "CREATE TABLE IF NOT EXISTS Todo_Notes ("
         . "noteId int(10) NOT NULL AUTO_INCREMENT,"
         . "todoNote varchar(255) NOT NULL,"
         . "catId int(10) NOT NULL,"
         . "dueDate date NOT NULL,"
         . "addDate date NOT NULL,"
         . "completed boolean NOT NULL,"
         . "PRIMARY KEY (noteId))";

    $pdo->exec($sql);

    echo "Create Todo_Notes Success!<br>";

}
catch (PDOException $ex) {

    $errorMsg = "Could not create Todo_Notes table: " . $ex->getMessage();

    include 'exception.php';
}


try {

    $sql = "INSERT INTO Todo_Notes (todoNote, catId, dueDate, addDate, completed) VALUES "
         . "('Write a php program to create and edit TODO notes',3,'2015-11-20',CURDATE(),1),"
         . "('Work on ASP.NET project',3,'2015-11-22',CURDATE(),0),"
         . "('Buy a new video card for desktop computer',2,'2016-1-1',CURDATE(),0),"
         . "('Pay house taxes - first installment',5,'2016-6-30',CURDATE(),0),"
         . "('Pay house taxes - second installment',5,'2016-12-31',CURDATE(),0)";

    $pdo->exec($sql);

    echo "Populate Todo_Notes Success!<br>";

} catch (Exception $ex) {

    $errorMsg = "Could not populate Todo_Notes table: " . $ex->getMessage();

    include 'exception.php';
}


try {

    $pdo->exec("DROP TABLE IF EXISTS Category");

    $sql = "CREATE TABLE IF NOT EXISTS Category ("
         . "catId int(10) NOT NULL AUTO_INCREMENT,"
         . "catName varchar(63),"
         . "PRIMARY KEY (catId))";

    $pdo->exec($sql);

    echo "Create Category Success!<br>";
}
catch (PDOException $ex) {

    $errorMsg = "Could not create Category table: " . $ex->getMessage();

    include 'exception.php';
}


try {

    $sql = "INSERT INTO Category (catName) VALUES "
         . "('General'),"
         . "('Shopping'),"
         . "('Homework'),"
         . "('Chores'),"
         . "('Bills')";

    $pdo->exec($sql);

    echo "Populate Category Success!<br>";
}
catch (PDOException $ex) {

    $errorMsg = "Could not populate Category table: " . $ex->getMessage();

    include 'exception.php';
}

