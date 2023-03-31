<?php
/*
Author: Javed Ur Rehman
Website: https://www.allphptricks.com
*/
$error = "";
$success = "";
$success_data ="";
// if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    if($_FILES["upload_csv"]["error"] == 4) {
        $error.="<li>Please select csv file to upload.</li>";
    }else{
        $file_path = pathinfo($_FILES['upload_csv']['name']);
        $file_ext = $file_path['extension'];
        $file_tmp = $_FILES['upload_csv']['tmp_name'];
        $file_size = $_FILES['upload_csv']['size'];	 
        // CSV file extension validation
        if ($file_ext != "csv"){
            $error.="<li>Sorry, only csv file format is allowed.</li>";
          }
        // 1MB file size validation
        if ($file_size > 1048576) {
            $error.="<li>Sorry, maximum 1 MB file size is allowed.</li>";
          }
        if(empty($error)){
            // Number of rows in CSV validation (3 rows are allowed for now)
            $file_rows = file($file_tmp);
            if(count($file_rows) > 3){
                $error.="<li>Sorry, you can upload maximum 3 rows of data in one go.</li>";
            }
        }
    }
    // if there is no error, then import CSV data into MySQL Database
    if(empty($error)){
		// Include the database connection file 
		require_once 'dbclass.php';
		$db = new DB;
        $file = fopen($file_tmp, "r");
        while (($row = fgetcsv($file)) !== FALSE) {
			// Insert csv data into the `import_csv_data` database table
            $db->query("INSERT INTO `import_csv_data` (`id`, `name`, `email`) VALUES (:id, :name, :email)");
            $db->bind(":id", $row[0]);
			$db->bind(":name", $row[1]);
            $db->bind(":email", $row[2]);
            $db->execute();
          	$success_data .= "<li>".$row[0]." ".$row[1]." ".$row[2]."</li>";
        }
        fclose($file);
		$db->close();
		$success = "Following CSV data is imported successfully.";
    }
}
?>
<html>
<head>
<title>Demo Import CSV File Data into MySQL Database using PHP - AllPHPTricks.com</title>
<link rel='stylesheet' href='css/style.css' type='text/css' media='all' />
</head>
<body>

<div style="width:700px; margin:50 auto;">
<h1>Demo Import CSV File Data into MySQL Database using PHP</h1>

<p><strong><a href="allphptricks.com-import-sample-data.csv" target="_blank">Download Sample CSV Testing Data for Uploading</a></strong></p>

<?php
if(!empty($error)){
    echo "<div class='alert alert-danger'><ul>";
    echo $error;
    echo "</ul></div>";
	}
if(!empty($success)){
	  echo "<div class='alert alert-success'><h2>".$success."</h2><ul>";
    echo $success_data;
    echo "</ul></div>";
	}
?>

<form method="post" action="" enctype="multipart/form-data">
<input type="file" name="upload_csv" />
<br /><br />
<input type="submit" value="Upload CSV Data"/>
</form>

<a href="https://www.allphptricks.com/how-to-import-csv-file-data-into-mysql-database-using-php/">Tutorial Link</a> <br /><br />
For More Web Development Tutorials Visit: <a href="https://www.allphptricks.com/">AllPHPTricks.com</a>

</div>
</body>
</html>