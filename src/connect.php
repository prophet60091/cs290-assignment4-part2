<?php
//Connect to mysql
session_start();
class video{

   function __construct(){

       date_default_timezone_set('UTC');
       $this->usr = 'jackrobe-db';
       $this->db= 'jackrobe-db';
       $this->host= '';
       $this->password = '';
       $this->err = array();
        $this->success = '';

       $this->ms = new mysqli($this->host, $this->usr, $this->password ,$this->db);

       if($this->ms->connect_error){
           array_push($this->err, $this->ms->connect_error );
           echo $this->ms->connect_error;
           return false;
       }
       return true;
    }

    function __destruct() {
        //print "Destroying " . $this->usr . "\n";
    }

    //checks the status of RENT from the database
    //@param id is the id you plan to check
    public function check_rent($id){

        $rentCheck = 'SELECT rented FROM a4 WHERE id=' . $id;

        if($resp = $this->ms->query($rentCheck)){

            while($row = $resp->fetch_array()){

                    return $row[0];
            }
        }else{
            array_push($this->err, "getting rented status failed");

        }
        return false;

    }

    //Updates the staus fo the video if checked becomes checked in
    public function status_change($id){

        //depending on the current state change it
        if($this->check_rent($id)){
            $statusChange = 0;
        }else{
            $statusChange = 1;
        }

        $query = 'UPDATE a4 SET a4.rented =' . $statusChange . ' WHERE a4.id=' . $id;

        if($resp = $this->ms->query($query)){

            $this->success = 'Checked Out';
            return $statusChange;

        }else{
            array_push($this->err, 'Couldnt update status:' . $query);
        }

        return "no comprende'";
    }


    //Gets only rthe response from the server of the categories available
    public function get_categories(){
        $query = 'SELECT DISTINCT category FROM a4';

        if($resp = $this->ms->query($query)){
            return $resp;

        }else{
            array_push($this->err, "getting categories failed");
        }

        return '0';
    }

    //Gets all the possible categories  based on the vidoes in the db
    //displays them them inside a select tag.
    public function display_categories(){

        $dropdown = '<form id="cat" action="connect.php" method="POST"><select  name="Filter" id="categories">';
        $dropdown .=  '<option  value="All">All Videos</option>';

        if($result = $this->get_categories()){

            while ($row =  $result->fetch_assoc()){
                $dropdown .=  '<option value="' . $row['category'] .'">'. $row['category'].'</option>';

            }

            $dropdown .= '</option><input type="submit" id="filtercategories"  name="submit"></form>';
        }
        return $dropdown;
    }

//Returns a variable holding all videos in  rows of HTML
//@PARAM a value to filter by defualts to 'All'
    public function display_all($filter){
        $tableRows = 'nothing to display';
        if($filter == "All" || $filter == ''){
            $query = 'SELECT a4.id,a4.title,a4.category,a4.len,a4.rented FROM a4 ORDER BY a4.category';
        }else{

            $query = "SELECT a4.id,a4.title,a4.category,a4.len,a4.rented FROM a4 WHERE a4.category='$filter'";
        }
        $vids = $this->ms->query($query);
        if($vids && $vids->num_rows != 0) {

            $tableRows = '<thead>';
            $tableRows .= '<tr>';
            $tableRows .= '<th>ID</th><th>TITLE</th><th>CATEGORY</th><th>LENGTH</th><th></th><th></th>';
            $tableRows .='</tr>';
            $tableRows .= '<tbody>';
            while ($row = $vids->fetch_row()) {

                $tableRows .= '<tr id="'.$row[0].'">';
                $tableRows .= '<td>' . $row[0] . '</td><td> ' . $row[1] . ' </td><td> ' . $row[2] . ' </td><td> ' . $row[3] . ' </td>';
                if ($row[4] == 0) {
                    $tableRows .= '<td><input type="button" onclick="rent(' . $row[0] . ')" value="Rent" id="rented"></button></td>';
                } else {
                    $tableRows .= '<td><input type="button" onclick="rent(' . $row[0] . ')" value="Check In"  id="rented"></td>';
                }

                $tableRows .= '<td><input type="button" onclick="deleteVid(' . $row[0] . ')" value="Delete" id="deleteVideo"></td>';

                $tableRows .= "</tr>";
            }
            $tableRows .= "</tbody>";

        }else{

            $tableRows = '<tbody><tr id="empty"><td>TABLE IS EMPTY</td></tr></tbody>';
            array_push($this->err, "getting videos failed");
        }

        return $tableRows;


    }

    //Check to make sure the user entered a plausible time for the movie running time
    //@param time is altered in the event it is invalid
    public function valid_time(&$time){

        if(!is_numeric($time) || $time > 300 || $time <= 0){

            $time = 'undefined';
                array_push($this->err, "Length of movie is invalid");
            return false;
        }
        $time= mysqli_real_escape_string($this->ms, $time);
        return true;

    }

    //Check to make sure the user entered a plausible text
    //@param text is altered in the event it is invalid
    public function valid_text(&$text){

        if(is_numeric($text) || strlen($text) > 255 || strlen($text) == 0 || $text == ''){

            $text = 'INVALID DATA';
            array_push($this->err, "Your entry is too long/short or it was numeric!");

            return false;
        }


        return true;
    }

    //VALIDATES THE FORM INFO
    //REQUIRES POST DATA ARRAY
    public function validate($data){

        $flag_undefined = false;
        foreach ($data as $k => $v) {

            if($v == ''){
                $data[$k] = 'undefined';
                $flag_undefined = true;
            }
        }

        if(!$this->valid_text($data['title']) || !$this->valid_time($data['length']) || !$this->valid_text($data['category']) || $flag_undefined){
            $_SESSION['formvalues'] = $data;
            array_push($this->err, "You entered invalid data try again");
            return false;
        }

        //store the valuse now possibly altered in the session for recall into the form
        $_SESSION['formvalues'] = $data;

        return true;

    }

    // DELETES EITHER ALL OR ONE ID
    // @param id -the id to be deleted
    //set to a string it will delete all
    public function delete($id){

        if(is_numeric($id)){
            $query = 'DELETE FROM a4 WHERE a4.id=' . $id;

        }else{

            $query = 'DELETE FROM a4';
        }


        if($resp = $this->ms->query($query)){

            $this->success = 'deleted';

        }else{

            array_push($this->err, 'Couldnt update status:' . $query);
        }

        return $status = "no comprende'";
    }

    //ADDS THE VIDEO FROM POST
    //REQUIRES a SET POST VARIALBES
    //@param data is the post data
    function addVideo($data){
        $title = mysqli_real_escape_string($this->ms, $data['title']);
        $category = mysqli_real_escape_string($this->ms, $data['category']);

        if($this->validate($data)){

            $query =" INSERT INTO a4 (title, category, len )VALUES ('$title', '$category', '$data[length]')";

            $result = $this->ms->query($query);
            if($result){
               $this->success = 'Added Video';

                $_SESSION['formvalues'] = '';
            }else{
                array_push($this->err, 'Couldnt add video: bad query ');

            }
        }else{
            array_push($this->err, 'Couldnt validate before adding');

        }

    }

}

//LOGIC Changes display based on what was submitted.
$ta = new video;

if($_POST){

    if(isset($_POST['title']) || isset($_POST['length']) || isset($_POST['category'])){
        $ta->addVideo($_POST);
        include 'content.php';

    }

    if($_POST['rent']){

        $ta->status_change($_POST['rent']);
        $ta->success = 'Rented';

    }

    if($_POST['deleteVid']){

        $ta->delete($_POST['deleteVid']);
        $ta->success = 'Deleted Video';

    }

    if($_POST['deleteALL']){

        $ta->delete('all');
        $ta->success = 'Deleted Video';
        require 'content.php';

    }
    if($_POST['Filter']){

        require 'content.php';

    }



}else {


    include_once 'content.php';
}

    ?>