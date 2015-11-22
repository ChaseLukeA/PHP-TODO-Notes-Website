<?php

// -----------------------------------------------------------------------------

// returns a string of $value that has been
// hashed with md5 and then hashed with sha1
function encrypt($value) {

    return sha1(md5($value));
}

// -----------------------------------------------------------------------------

// compares a string $key [that has been
// previously hashed with the encrypt()
// function] to a string $value; returns
// true for a match between $key and
// $value, false if not a match
function decrypt($key, $value) {

    return $key == sha1(md5($value));
}

// -----------------------------------------------------------------------------

// Takes in string date in 'dd|mm|yyyy' or 'd|m|yy format',
// allowing (-) (/) (\) (,) (.) (:) or ( ) as separators;
// if date is valid, returns string date as "dd-mm-yyyy";
// returns false if string $date not valid;
// 
// Automatically prefixes "0" to 'dd' and 'mm' if only one
// number provided, prefixes "20" to 'yy' if only two year
// numbers are provided
function dateFormat($date) {

    list($month, $day, $year) = explode(
            "-", str_replace(array("-","/","\\",",",".",":"," "), "-", $date));

    $month = strlen($month) == 1 ? "0$month" : $month;
    $day = strlen($day) == 1 ? "0$day" : $day;
    $year = strlen($year) == 2 ? "20$year" : $year;

    if (checkdate($month, $day, $year)) {

        return "$month-$day-$year";
    }

    return false;
}

// -----------------------------------------------------------------------------

// Converts a string date in 'dd|mm|yyyy' or 'd|m|yy' format
// and returns a string date in 'yyyy-mm-dd' format for SQL
// injection; returns false if $date argment is not valid
function dateFormatToSql($date) {
    
    if ($date = dateFormat($date)) {

        return substr($date, -4) . "-"
                . substr($date, 0, 2) . "-"
                . substr($date, 3, 2);
    }

    return false;
}

// -----------------------------------------------------------------------------

// Converts a SQL string date in 'yyyy-mm-dd' format
// and returns a string date in 'mm-dd-yyyy' format
function dateFormatFromSql($date) {

    list($year, $month, $day) = explode("-", $date);
    
    return dateFormat("$month-$day-$year");
}

// -----------------------------------------------------------------------------
// Compares a string date in 'dd|mm|yyyy' or 'd|m|yy' format
// to today's date; returns true if date is today's date or
// in the future, false if the date is in the past
function dateIsFuture($date) {

    if ($date = dateFormat($date)) {

        $date = substr($date, -4) . substr($date, 0, 2) . substr($date, 3, 2);

        if ($date >= date('Ymd')) {

            return true;
        }
    }

    return false;
}

// -----------------------------------------------------------------------------

function sanitize($string) {

    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// -----------------------------------------------------------------------------

function validationError($message) {

    echo "<h4 class=\"errorMsg\">&xotime; " . $message . "</h4>";
}