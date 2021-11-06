<?php
function WriteHeaders($Heading="Welcome", $TitleBar="MySite")
{
    echo "
    <!doctype html> 
    <html lang = \"en\">
        <head>
            <meta charset = \"UTF-8\">
            <title>$TitleBar</title>
            <link rel =\"stylesheet\" type = \"text/css\" href=\"asstStyle.css\"/>
        </head>
    <body>
    <h1>$Heading</h1>\n";
}

function DisplayContactInfo()
{
    echo "
    <footer>
        Questions? Comments?
        <a href = \"mailto:kimeli.zuniga@student.sl.on.ca\">kimeli.zuniga@student.sl.on.ca</a>
    </footer>";
}

function WriteFooters()
{
    DisplayContactInfo();
    echo "
    </body>
    </html>
    ";
}

function DisplayLabel($prompt)
{
    echo "<label>" . $prompt . "</label>";
}

function DisplayTextbox($Type, $Name, $Size, $Value)
{
    echo "<input type = \"$Type\" name = \"$Name\" Size = \"$Size\" value = \"$Value\">";
}   

function DisplayImage($FileName, $Alt, $Height, $Width)
{
    echo "<img src = \"$FileName\" alt = \"$Alt\" height = \"$Height\" width = \"$Width\">";
}

function DisplayButton($Name, $Text, $FileName = "", $alt = "")
{
    if($FileName == "")
        echo "<button name=\"$Name\" >$Text</button>";
    else
    {
        echo "<button name=\"$Name\" >";
        DisplayImage($FileName, $alt, 60, 60);
        echo "</button>";
        
    }
}

function CreateConnectionObject()
{
    $fh = fopen('auth.txt', 'r');
    $Host = trim(fgets($fh));
    $UserName = trim(fgets($fh));
    $Password = trim(fgets($fh));
    $Database = trim(fgets($fh));
    $Port = trim(fgets($fh));
    fclose($fh);
    $mysqlObj = new mysqli ($Host, $UserName, $Password, $Database, $Port);
    // If the connection and authentication are successful.
    // the error number is 0
    // connect_errno is a public attribute of the mysqli class.
    if ($mysqlObj -> connect_errno != 0)
    {
        echo "<p>Connection failed. Unable to open database $Database. Error: "
                . $mysqlObj->connect_error . "</p>";
        // Stop executing the php script
        exit;
    }
    return ($mysqlObj);
}

?>