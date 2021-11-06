<?php
require_once("asstInclude.php");
require_once("clsCreateRoadTestTable.php");

// Main

date_default_timezone_set ('America/Toronto');
$mysqlObj;
$TableName = "RoadTest";

WriteHeaders("Kim Zuniga Coding Assignment", "Kim Zuniga");

if (isset($_POST["f_DisplayData"]))
    DisplayDataForm($mysqlObj, $TableName);
else
    if (isset($_POST["f_CreateTable"]))
        CreateTableForm($mysqlObj, $TableName);
    else
        if (isset($_POST["f_AddRecord"]))
            AddRecordForm($mysqlObj, $TableName);
        else
            if (isset($_POST["f_Save"]))
                SaveRecordToTableForm($mysqlObj, $TableName);
            else
                if (isset($_POST["f_Home"]))
                    DisplayMainForm();
                else
                    DisplayMainForm();

if (isset($mysqlObj))
    $mysqlObj->close();

WriteFooters();

// Functions

function DisplayMainForm()
{
    echo "<h2>Infinity Road Test</h2>";
    echo "<img class=\"road\"src=\"road.png\" alt=\"road.png\">";
    echo "<form action=\"?\" method=\"POST\">";
        DisplayButton("f_CreateTable", "Create Table", "createtable.png", "createtable.png");
        DisplayButton("f_AddRecord", "Add Record", "addrecord.png", "addrecord.png");
        DisplayButton("f_DisplayData", "Display Data", "display.png", "display.png");
    echo "</form>";
}

function CreateTableForm(&$mysqlObj, $TableName)
{
    echo "<form action=\"?\" method=\"POST\">";
    DisplayButton("f_Home", "Home", "home.png", "home.png");
    echo "</form>";
    
    $tableObj = new clsCreateRoadTestTable;
    $tableObj->createTheTable($mysqlObj, $TableName);
}

function AddRecordForm(&$mysqlObj)
{
    $today = date('Y-m-d');
    $time = date('H:i');
    $mysqlObj = CreateConnectionObject();

    echo "<form class=\"mainForm\" action = \"?\" method=\"POST\">";
        echo "<h3>Vehicle Information</h3>";
        echo "<div class=\"formContainer\">";
        echo "<img class=\"car\"src=\"car.png\" alt=\"car.png\">";
            echo "<div class=\"datapair\">";
            DisplayLabel("License Plate: ");
            DisplayTextbox("text", "f_LicensePlate", "10", "");
            echo "</div>";

            echo "<div class=\"datapair\">";
            DisplayLabel("Date Stamp: ");
            DisplayTextbox("date", "f_DateStamp", "", "$today");
            echo "</div>";

            echo "<div class=\"datapair\">";
            DisplayLabel("Time Stamp: ");
            DisplayTextbox("time", "f_TimeStamp", "", "$time");
            echo "</div>";

            echo "<div class=\"datapair\">";
            DisplayLabel("Number of Passengers: ");
            DisplayTextbox("number", "f_NumberPassengers", "", "3");
            echo "</div>";

            echo "<div class=\"datapair\">";
            DisplayLabel("Incident free: ");
            DisplayTextbox("checkbox", "f_IsIncidentFree", "", "" );
            echo "</div>";

            echo "<div id=\"dangerStatus\" class=\"datapair\">";
            DisplayLabel("Danger Status: ");
            echo " <select name=\"f_DangerStatus\">
                    <option value=\"Low\">Low</option>
                    <option value=\"Medium\" selected>Medium</option>
                    <option value=\"High\">High</option>
                    <option value=\"Critical\">Critical</option>
                </select>";
            echo "</div>";

            echo "<div id=\"speed\" class=\"datapair\">";
            DisplayLabel("Speed: ");
            DisplayTextbox("text", "f_Speed", "1", "100" );
            echo "</div>";

        echo "</div>";

        DisplayButton("f_Save", "Save", "save.png", "save.png");
        DisplayButton("f_Home", "Home", "home.png", "home.png");
        
    echo "</form>";
    echo "<script src=\"asstScript.js\"></script>";
}

function SaveRecordToTableForm(&$mysqlObj, $TableName)
{
    $mysqlObj = CreateConnectionObject();

    $LicensePlate = $_POST["f_LicensePlate"];
    $DateAndTime = $_POST["f_DateStamp"] . " " . $_POST["f_TimeStamp"];
    $NumberOfPassengers = $_POST["f_NumberPassengers"];
    if (isset($_POST["f_IsIncidentFree"]))
        $IsIncidentFree = true;
    else
        $IsIncidentFree = false;
    $DangerStatus = substr($_POST["f_DangerStatus"], 0, 1);
    $Speed = $_POST["f_Speed"];
    $Cost = 5000 + (100 * $NumberOfPassengers);

    $query = "Insert Into $TableName (licensePlate, dateTimeStamp, nbrPassengers,
                                      incidentFree, dangerStatus, speed, cost) 
                                      Values (?, ?, ?, ?, ?, ?, ?)";
                                    
    $stmt = $mysqlObj->prepare($query);
    $BindSuccess = $stmt->bind_param("ssiisdd",
                            $LicensePlate, $DateAndTime, $NumberOfPassengers,
                            $IsIncidentFree, $DangerStatus, $Speed, $Cost);

    if ($BindSuccess)
        $success = $stmt-> execute();
    else
        echo "Bind failed" . $stmt->error;
    
    if ($success)
        echo "Record successfully added to $TableName";
    else
        echo "Unable to add record to $TableName. " . $mysqlObj->error;

    echo "<form action=\"?\" method=\"POST\">";
    DisplayButton("f_Home", "Home", "home.png", "home.png");
    echo "</form>";  

    $stmt->close();
}

function DisplayDataForm(&$mysqlObj, $TableName)
{
    $mysqlObj = CreateConnectionObject();

    $query = "Select licensePlate, dateTimeStamp, nbrPassengers,
              incidentFree, dangerStatus, speed, cost
              From $TableName
              Order by dangerStatus DESC";
    $stmt = $mysqlObj->prepare($query);
    $stmt->execute();
    $stmt->bind_result($LicensePlate, $DateTimeStamp, $NbrPassengers,
                       $IncidentFree, $DangerStatus, $Speed, $Cost);
    
    echo "<h3>Vehicle Records</h3>";
    echo "
    <table>
        <tr>
            <th>Danger Status</th>
            <th>License Plate</th>
            <th>Number of Passengers</th>
            <th>Date Time</th>
            <th>Incident Free</th>
            <th>Speed</th>
            <th>Cost</th>
        </tr>
         ";
    while ($stmt->fetch())
    {
        switch($DangerStatus)
            {
                case "L":
                    $DangerStatus = "Low";
                    break;
                case "H":
                    $DangerStatus = "High";
                    break;
                case "C":
                    $DangerStatus = "Critical";
                    break;
                default:
                    $DangerStatus = "Medium";
                    break;
            }

    if ($IncidentFree)
            $IncidentFree = "Yes";
    else
            $IncidentFree = "No";
    echo "
        <tr>
            <td>$DangerStatus</td>
            <td>" . strtoupper($LicensePlate) . "</td>
            <td>$NbrPassengers</td>
            <td>" . substr($DateTimeStamp, 0, 10) . " at " . substr($DateTimeStamp, 11) . "</td>
            <td>$IncidentFree</td>
            <td>$Speed</td>
            <td>\$$Cost</td>
        </tr>";
    }
    echo "
    </table>
         ";

    echo "<form action=\"?\" method=\"POST\">";
    DisplayButton("f_Home", "Home", "home.png", "home.png");
    echo "</form>";

    $stmt->close();
}

?>