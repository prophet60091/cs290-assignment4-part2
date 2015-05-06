<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="author" content="Robert Jackson">
    <meta name="description" content="">
    <title>Videos</title>
    <script src="formFunctions.js" type="application/javascript"></script>
    <link rel="stylesheet" href="style.css" type="text/css"/>


</head>
<body>
<div class="filters">
    <?php echo $ta->display_categories();   ?>


</div>
<div class="alerts">
    <span id="status">STATUS: <?php echo $ta->success; ?></span>
</div>

<div class="content">
    <table id="videos">
        <?php
        if($_POST['Filter']){
            echo $ta->display_all( $_POST['Filter']);
        }else{
           echo $ta->display_all('All');
        };?>
  </table>
</div>

<div class="add">
    <form id="addVideo"  name="video" action="connect.php" method="POST">
        <label for="title">Title</label>
        <input id="title" name="title" type="text" value="<?php if ($_SESSION['formvalues']){ echo $_SESSION['formvalues']['title'];}; ?>">
        <label for="category">category</label>
        <input id="category" name="category" value="<?php if ($_SESSION['formvalues']){ echo $_SESSION['formvalues']['category'];}; ?>" >
        <label for="length">Running Time</label>
        <input id="length" name="length" value="<?php if ($_SESSION['formvalues']){ echo $_SESSION['formvalues']['length'];}; ?>">
        <input type="submit" id="videoSubmit"  value="ADD VIDEO" name="Add">
    </form>

    <span id="status">STATUS: <?php echo $ta->success; ?></span>
    <span id="errors"><?php
        if($ta->err[0]){
            echo "Errors: ";
                foreach(  $ta->err as $value ){
                    echo $value . ', ';
            }
        }  ?></span>
</div>
<div class="deleteall">
    <form id="delVideo"  name="deleteALL" action="connect.php" method="POST">
        <input type="submit"   value="DELETE ALL" name="deleteALL">
    </form>
</div>

</body>
</html>