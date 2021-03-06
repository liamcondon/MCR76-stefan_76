<?php
  $myRoot = substr($_SERVER['DOCUMENT_ROOT'], 0, strpos($_SERVER['DOCUMENT_ROOT'], 'public_html'));
  $myPage = $_SERVER['PHP_SELF'];
  require  $myRoot . 'mcr76_hidden/script.php';
  
  $data = json_decode($_POST['data'], true);
  $tableName = array_keys($data)[0];
  if ($config['myRoot'] != '/home/' . $data[$tableName]['apiKey'] . '/') {
    echo 'Authorization Required ...';
    exit;
  }
  $timeZone = $data[$tableName]['timeZone'];
  //echo $timeZone;
  $debugD = '';
  $dbPhpRev = '0.2';
  $strOrig = array('"');
  $strEsc = array('\"');
  $aok = false;
  
  if ($data[$tableName]['purpose'] == 'CT') {
    //echo 'Create Table';
          $conn = mysqli_connect($config['dbServer'], $config['dbUserName'], $config['dbPassWord'], $config['dbName']);
      if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
      }

    $sql = 'CREATE TABLE ' . $tableName . ' (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,';

//echo sizeof($data[$tableName]['data']);
//print_r($data[$tableName]['data'][1]);
//$rows = json_decode($data[$tableName]['data'], true);
//echo $rows;
foreach($data[$tableName]['data'] as $x) {
  $sql .= ' ' . $x['colName'];
  if ($x['dataType'] == 'String') {
    $sql .= ' VARCHAR(' . $x['dataLength'] . ') CHARACTER SET utf8 COLLATE utf8_unicode_ci';
  }
  if ($x['dataType'] == 'Number') {
    $sql .= ' DECIMAL (14,5)';
  }
  if ($x['dataType'] == 'Boolean') {
    $sql .= ' BOOLEAN';
  }
  if ($x['unique'] == 1) {
    $sql .= ' NOT NULL UNIQUE';
  }
  $sql .= ', ';
//for($x = 0; $x <= sizeof($data[$tableName]['data']); $x++) {
   //echo $x;
   //echo $x['colName'];
   //echo $data[$tableName]['data'][$x]['colName'];
}


//lastname VARCHAR(30) NOT NULL,
//email VARCHAR(50),
$sql .= 'createDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updateDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)';

//exit;

if ($conn->query($sql) === TRUE) {
  echo 'Table ' . $tableName . ' created successfully';
} else {
  echo 'Error creating table: ' . $conn->error;
}

$conn->close();
  }
  
  
  
  else if ($data[$tableName]['purpose'] == 'RT') {
    //echo 'Remove Table';
          $conn = mysqli_connect($config['dbServer'], $config['dbUserName'], $config['dbPassWord'], $config['dbName']);
      if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
      }

    $sql = 'DROP TABLE ' . $tableName;
if ($conn->query($sql) === TRUE) {
  echo 'Table ' . $tableName . ' removed successfully';
} else {
  echo 'Error removing table: ' . $conn->error;
}

$conn->close();
      
  }
  
  
  else if ($data[$tableName]['purpose'] == 'AC') {
    //echo 'Add Column(s)';
          $conn = mysqli_connect($config['dbServer'], $config['dbUserName'], $config['dbPassWord'], $config['dbName']);
      if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
      }

    $sql = 'ALTER TABLE ' . $tableName;
foreach($data[$tableName]['data'] as $x) {
  $sql .= ' ADD COLUMN ' . $x['colName'];
  if ($x['dataType'] == 'String') {
    $sql .= ' VARCHAR(' . $x['dataLength'] . ') CHARACTER SET utf8 COLLATE utf8_unicode_ci';
  }
  if ($x['dataType'] == 'Number') {
    $sql .= ' DECIMAL (14,5)';
  }
  if ($x['dataType'] == 'Boolean') {
    $sql .= ' BOOLEAN';
  }
  if ($x['unique'] == 1) {
    $sql .= ' NOT NULL UNIQUE';
  }
  $sql .= ', ';
}

$sql = substr($sql, 0, -2);

echo $sql . '<br><br><br>';  
    
    
if ($conn->query($sql) === TRUE) {
  echo 'Table ' . $tableName . ' added column(s) successfully';
} else {
  echo 'Error adding column(s): ' . $conn->error;
}

$conn->close();
  }  
  
  
  


  else if ($data[$tableName]['purpose'] == 'RC') {
    echo 'Remove Column(s)';
          $conn = mysqli_connect($config['dbServer'], $config['dbUserName'], $config['dbPassWord'], $config['dbName']);
      if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
      }

    $sql = 'ALTER TABLE ' . $tableName;
foreach($data[$tableName]['data'] as $x) {
  $sql .= ' DROP COLUMN ' . $x['colName'] . ', ';
}

$sql = substr($sql, 0, -2);

echo $sql . '<br><br><br>';  
    
    
if ($conn->query($sql) === TRUE) {
  echo 'Table ' . $tableName . ' removed column(s) successfully';
} else {
  echo 'Error removing column(s): ' . $conn->error;
}

$conn->close();
  }  
  
  





  else if ($data[$tableName]['purpose'] == 'ID') {
    //echo 'Import JSON Data File';
          $conn = mysqli_connect($config['dbServer'], $config['dbUserName'], $config['dbPassWord'], $config['dbName']);
      if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
      }
      
  $jsonFile = file_get_contents($myRoot . 'mcr76_hidden/' . $tableName . '.json');
  $jsonFile = mb_convert_encoding($jsonFile, 'UTF-8', mb_detect_encoding($jsonFile, 'UTF-8, ISO-8859-1', true));
  $jsonData = json_decode($jsonFile, true);
  
  //echo $jsonData[$tableName][0]['PartNo'].  '<br>';
  //echo $jsonData[$tableName][0].  '<br>';
  print_r($jsonData[$tableName][6]);
  //echo sizeof($jsonData[$tableName][0]) . '<br>';
  $keys = array_keys($jsonData[$tableName][0]);
  //print_r($keys);
  //var_dump(array_keys($jsonData[$tableName][0]));
  //echo ' <br>';
  //exit;

  //print_r($jsonData);  
$before = array('"');
$after = array('\"');
//$before = array();
//$after = array();
foreach($jsonData[$tableName] as $row) {
//echo $row['PartNo'];
$sql = 'INSERT INTO ' . $tableName . ' (';
foreach($keys as $key) {
$sql .= $key . ', ';    
}
$sql = substr($sql, 0, -2);
$sql .= ') VALUES ('; 
foreach($keys as $key) {
  if (gettype($row[$key]) == 'boolean' && $row[$key] == 1) {
    $sql .= '1, '; 
  }
  else if (gettype($row[$key]) == 'boolean') {
    $sql .= '0, '; 
  }
  else if (gettype($row[$key]) == 'string') {
    //$sql .= '"' . str_replace($before, $after, $row[$key]) . '", ';
    $abc = mb_convert_encoding($row[$key], 'UTF-8', mb_detect_encoding($row[$key], 'UTF-8, ISO-8859-1', true));
    $sql .= '"' . str_replace($before, $after, $abc) . '", ';
  }
  else if (gettype($row[$key]) == 'integer' || gettype($row[$key]) == 'double') {
    $sql .= str_replace($before, $after, $row[$key]) . ', ';
  }
  else {
    $sql .= '"' . str_replace($before, $after, $row[$key]) . '", ';
    //echo gettype($row[$key]) . ' ... ' . $row[$key] . ' ... ';
  }
}
$sql = substr($sql, 0, -2);
$sql .= ')'; 


if ($conn->query($sql) === TRUE) {
  //echo 'Table ' . $tableName . ' imported JSON data successfully';
} else {
    echo '>>>' . $sql . '<<<';
  echo 'Error importing JSON data: ' . $conn->error;
}

//echo '<br>';echo '<br>';
//echo $sql;

  //exit;
    
    
}
$conn->close();
  }  
  
  

else if ($data[$tableName]['purpose'] == 'ED') {
  //echo 'Export JSON Data File';
  $conn = mysqli_connect($config['dbServer'], $config['dbUserName'], $config['dbPassWord'], $config['dbName']);
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }
  $sql = 'SELECT * FROM ' . $tableName;
  $result = mysqli_query($conn, $sql);
  $fieldCount = $result->field_count;
  //echo $fieldCount;
  for($x = 0; $x < $fieldCount; $x++) {
    $finfo = $result->fetch_field_direct($x);
    $dbCols[] = $finfo->name;
    $dbType[] = $finfo->type;
  }
  //while($mysql_query_fields = mysqli_fetch_field($result)){
    //$dbCols[] = $mysql_query_fields->name;
    //echo ucfirst($mysql_query_fields->name);
  //}
  //print_r($dbCols);
  $before = array('"');
  $after = array('\"');
  $jsonStr = '{"' . $tableName . '": [';
  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
      $jsonStr .= '{';
        for($x = 0; $x < sizeof($dbCols); $x++) {
          $jsonStr .= '"' . $dbCols[$x] . '": ';
          if ($dbType[$x] == 253) {
            //echo 'String Data Type'; // OK
            $jsonStr .= '"' . str_replace($before, $after, $row[$dbCols[$x]]) . '"';
          }
          else if ($dbType[$x] == 3 || $dbType[$x] == 246 || $dbType[$x] == 4 || $dbType[$x] == 5) {
            //echo 'Number Data Type (JS Exercise)'; // OK
            $jsonStr .= $row[$dbCols[$x]];
          }
          else if ($dbType[$x] == 7 || $dbType[$x] == 10 || $dbType[$x] == 12 || $dbType[$x] == 14) {
            //echo 'DateTime Data Type';
            $jsonStr .= '"' . $row[$dbCols[$x]] . '"';
          }
          else if ($dbType[$x] == 1 || $dbType[$x] == 254) {
            //echo 'Boolean Data Type'; // OK
            if ($row[$dbCols[$x]] == 1) {
              $jsonStr .= 'true';    
            }
            else {
              $jsonStr .= 'false';    
            }
          }
          else {
            //echo 'as String Data Type';
            $jsonStr .= '"?' . $row[$dbCols[$x]] . ' [' . $dbType[$x] . ']?"';
          }
          $jsonStr .= ', ';
        }
      $jsonStr = substr($jsonStr, 0, -2);
      $jsonStr .= '}, ';
    }
  }  
  else {
    echo "0 results";
  }
  $jsonStr = substr($jsonStr, 0, -2);
  $jsonStr .= ']}';
  //echo $jsonStr;
  date_default_timezone_set($timeZone);
  $timeStamp = time();
  $saveTime = date('_YmdHis', $timeStamp);
  file_put_contents($myRoot . 'mcr76_hidden/' . $tableName . $saveTime . '.json', $jsonStr);
}




else if ($data[$tableName]['purpose'] == 'R') {
  //echo 'Read Records';
  $conn = mysqli_connect($config['dbServer'], $config['dbUserName'], $config['dbPassWord'], $config['dbName']);
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }
  $sql = 'SELECT * FROM ' . $tableName . ' WHERE ';
  foreach($data[$tableName]['data'] as $x) {
    //echo $x['colName'] . $x['search'] . $x['dataType'];
    if ($x['dataType'] == 'String') {
      if ($x['strict'] == 1) {
        $sql .= $x['colName'] . ' LIKE "' . $x['search'] . '"';
      } else {
        $sql .= $x['colName'] . ' LIKE "%' . $x['search'] . '%"';
      }
    }
    else if ($x['dataType'] == 'Number') {
      $sql .= $x['colName'] . ' = ' . $x['search'];
    }
    else if ($x['dataType'] == 'Boolean') {
      if ($x['search'] == 1) {
        $sql .= $x['colName'] . ' = 1';
      } else {
        $sql .= $x['colName'] . ' != 1';
      }
    }
    $sql .= ' AND ';
  }
  $sql = substr($sql, 0, -5);
  if ($data[$tableName]['sort'] != 'n/a') {
    $sql .= ' ORDER BY ' . $data[$tableName]['sort']; 
  }
  if ($data[$tableName]['pageSize'] >= 0) {
    $sql .= ' LIMIT ' . $data[$tableName]['pageSize'];
    if ($data[$tableName]['startVal'] >= 0) {
      $sql .= ' OFFSET ' . $data[$tableName]['startVal']; 
    }
  }
  //echo $sql;
  //exit;

  $result = mysqli_query($conn, $sql);
  $fieldCount = $result->field_count;
  //echo $fieldCount;
  for($x = 0; $x < $fieldCount; $x++) {
    $finfo = $result->fetch_field_direct($x);
    $dbCols[] = $finfo->name;
    $dbType[] = $finfo->type;
  }
  $before = array('"');
  $after = array('\"');
  $jsonStr = '{"' . $tableName . '": [';
  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
      $jsonStr .= '{';
        for($x = 0; $x < sizeof($dbCols); $x++) {
          $jsonStr .= '"' . $dbCols[$x] . '": ';
          if ($dbType[$x] == 253) {
            //echo 'String Data Type'; // OK
            $jsonStr .= '"' . str_replace($before, $after, $row[$dbCols[$x]]) . '"';
          }
          else if ($dbType[$x] == 3 || $dbType[$x] == 246 || $dbType[$x] == 4 || $dbType[$x] == 5) {
            //echo 'Number Data Type (JS Exercise)'; // OK
            $jsonStr .= $row[$dbCols[$x]];
          }
          else if ($dbType[$x] == 7 || $dbType[$x] == 10 || $dbType[$x] == 12 || $dbType[$x] == 14) {
            //echo 'DateTime Data Type';
            $jsonStr .= '"' . $row[$dbCols[$x]] . '"';
          }
          else if ($dbType[$x] == 1 || $dbType[$x] == 254) {
            //echo 'Boolean Data Type'; // OK
            if ($row[$dbCols[$x]] == 1) {
              $jsonStr .= 'true';    
            }
            else {
              $jsonStr .= 'false';    
            }
          }
          else {
            //echo 'as String Data Type';
            $jsonStr .= '"?' . $row[$dbCols[$x]] . ' [' . $dbType[$x] . ']?"';
          }
          $jsonStr .= ', ';
        }
      $jsonStr = substr($jsonStr, 0, -2);
      $jsonStr .= '}, ';
    }
    $jsonStr = substr($jsonStr, 0, -2);
    $debugD .= "Found matching results. ";
    $aok = true;
  }  
  else {
    $aok = true;
    $debugD .= "No matching results. ";
  }
  $jsonStr .= ']}';
  returnJson($dbPhpRev, $aok, $jsonStr, mysqli_num_rows($result), $data[$tableName]['startVal'], $data[$tableName]['pageSize'], $data[$tableName]['debug'], $debugD, $strOrig, $strEsc, $sql, $data[$tableName]['sort'], $timeZone);
  $conn->close();
}  
  
function returnJson($dbPhpRev, $aok, $jsonStr, $rows, $startVal, $pageSize, $debug, $debugD, $strOrig, $strEsc, $sql, $sort, $timeZone) {
  $rtnStr = '{"aok": ';
  if ($aok == true) {
    $rtnStr .= 'true, ';
  }
  else {
    $rtnStr .= 'false, ';
  }
  if ($debug == 1) {
    $rtnStr .= '"sql": "' . str_replace($strOrig, $strEsc, $sql) . '", ';
    $rtnStr .= '"debug": "' . str_replace($strOrig, $strEsc, trim($debugD)) . '", ';
    $rtnStr .= '"sort": "' . $sort . '", ';
    $rtnStr .= '"date": "' . $sort . '", ';
    $rtnStr .= '"dbPhpRev": "' . $dbPhpRev. '", ';
    date_default_timezone_set($timeZone);
    $timeStamp = time();
    $rtnStr .= '"localTime": "' . date('H:i:s d.m.Y', $timeStamp). '", ';
  }
  $rtnStr .= '"data": ' . $jsonStr . ', ';
  $rtnStr .= '"startVal": ' . $startVal . ', ';
  $rtnStr .= '"pageSize": ' . $pageSize . ', ';
  $rtnStr .= '"rows": ' . $rows . '}';
  echo $rtnStr;
}


  
  
?>
