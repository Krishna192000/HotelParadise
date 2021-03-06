<!DOCTYPE html>
<html>
<?php 
  require_once '../login.php';
  $conn = new mysqli($hn, $un, $pw, $db);
  if ($conn->connect_error) die($conn->connect_error);

  if (isset($_GET['CheckIn'])   &&
      isset($_GET['CheckOut'])  &&
      isset($_GET['Adults'])    &&
      isset($_GET['Kids']))
  {
    $checkIn   = $_GET['CheckIn'];
    $checkOut  = $_GET['CheckOut'];
    $adults    = $_GET['Adults'];
    $kids      = $_GET['Kids'];
    $peoples = $adults + $kids - 1;
    $query     = "SELECT * FROM room WHERE beds>='$peoples' and status='Available'";
    $result    = $conn->query($query);

    $date1 = "$checkIn"; 
    $date2 = "$checkOut"; 
    $currentDate = date('m/d/Y');
    $currentStringDate = strtotime($currentDate); 
    $dateTimestamp1 = strtotime($date1); 
    $dateTimestamp2 = strtotime($date2); 

    if ($dateTimestamp1 >= $dateTimestamp2){
        echo "<script> 
                alert('CheckOut date cannot be on or before the CheckIn date.'); 
                window.location.href='index.php';
              </script>";
      }
       
    if($dateTimestamp1 > $currentDate)
    {
       echo "<script> 
                alert('CheckIn date cannot be before today's date.'); 
                window.location.href='index.php';
              </script>";
    }   
    if (!$result) 
      echo "INSERT failed: $query<br>" . $conn->error . "<br><br>";
  }
  if (isset($_GET["room"]))
  {
    $myRoom = $_GET['room'];
    $query  = "SELECT * FROM room WHERE type='$myRoom' and status='Available'";
    $result = $conn->query($query);

    if (!$result) 
      echo "INSERT failed: $query<br>" . $conn->error . "<br><br>";
  }
  
?>

<title>Paradise</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="../css/index.css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.12.4.js"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="../js/index.js"></script>
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: "Raleway", Arial, Helvetica, sans-serif}
</style>
<body class="w3-light-grey">
<!-- Navigation Bar -->
<div style="background-color: grey; color: white; ">
  <div style="text-align:center; ">
    <h3>Paradise Hotel</h3>
  </div>
  <div class="w3-row-padding w3-large w3-center">
    <div class="w3-third"><i class="fa fa-map-marker w3-text-red"></i> 423 Some adr, Toronto, ON Canada</div>
    <div class="w3-third"><i class="fa fa-phone w3-text-red"></i> Phone: +1 (999) 999-9999</div>
    <div class="w3-third"><i class="fa fa-envelope w3-text-red"></i> Email: hotelparadise012@gmail.com</div>
  </div>
</div>
<!-- Header -->
<header class="w3-display-container w3-content" style="max-width:1500px;">
  <br>
  <div class="allInfoDisplay">
    <div class="leftAllInfoDisplay">
      <div>
        Dates<br>
        <b><?php echo "$checkIn" . " - " . "$checkOut"; ?></b>
      </div>
      <div>
        Guests<br>
        <b><?php echo "$adults" . " Adults, " . "$kids" . " Kids"; ?></b>
      </div>
    </div>
    <div class="rightAllInfoDisplay">
      <button class="open-button SelectButton" onclick="openForm()">Edit</button>
    </div>
  </div>
<br>
  <div class="flexContainer">
    <?php
    if(mysqli_num_rows($result) > 0)  
    {
    while($row = $result->fetch_assoc()) {?>

    <div class="col-md-4">  
      <div style="background-color:white; border-radius:5px; padding:16px; height:400px">  
          <h3 class="text-info" style="text:align:left; font-weight: bold"><?php echo $row["roomDescription"]; ?></h3>
          <div class="bigLine"></div><br>
          <div class="flexSelectRoom">
            <div class="leftSelection">
              <?php 
                 if($row["type"] == "SingleRoom" && $row["beds"] == 1){
                    $imageName = "single1.jpg";
                 }
                 elseif($row["type"] == "SingleRoom" && $row["beds"] == 2){
                    $imageName = "single2.jpg";
                 }
                 elseif($row["type"] == "DoubleRoom" && $row["beds"] == 1){
                    $imageName = "double1.jpg";
                 }
                 elseif($row["type"] == "DoubleRoom" && $row["beds"] == 2){
                    $imageName = "double2.jpg";
                 }
                 elseif($row["type"] == "DoubleRoom" && $row["beds"] == 3){
                    $imageName = "double3.jpg";
                 }
                 elseif($row["type"] == "DeluxeRoom" && $row["beds"] == 1){
                    $imageName = "deluxe3.jpg";
                 }
                 else{
                    $imageName = "double1.jpg";
                 }
              ?>
              <img alt="This is an image of hotel room" src="../Images/<?php echo $imageName ?>" width="100%"><br>
            </div>
            <div class="rightSectionRoom">
              <div class"leftInRight" style="float:left;font-size: 135%;">
                <br><br>Standard Rate
              </div>
              <h4 class="text-danger" style="text-align:right"><?php echo $row["price"]; ?> USD/night</h4>
              <form action="finaliseRoom.php" target="POST">
                <input type="hidden" name="roomId" value="<?php echo $row["roomId"]; ?>">
                <input type="hidden" name="roomDescription" value="<?php echo $row["roomDescription"]; ?>">
                <input type="hidden" name="type" value="<?php echo $row["type"]; ?>">
                <input type="hidden" name="beds" value="<?php echo $row["beds"]; ?>">
                <input type="hidden" name="price" value="<?php echo $row["price"]; ?>">
                <input type="hidden" name="status" value="<?php echo $row["status"]; ?>">
                <input type="hidden" name="tv" value="<?php echo $row["tv"]; ?>">
                <input type="hidden" name="kitchen" value="<?php echo $row["kitchen"]; ?>">
                <input type="hidden" name="bar" value="<?php echo $row["bar"]; ?>">
                <input type="hidden" name="CheckIn" value="<?php echo "$checkIn"; ?>">
                <input type="hidden" name="CheckOut" value="<?php echo "$checkOut"; ?>">
                <input type="hidden" name="adults" value="<?php echo "$adults"; ?>">
                <input type="hidden" name="kids" value="<?php echo "$kids"; ?>">
                <input type="hidden" name="breakfast" value="0">
                <input type="hidden" name="imageName" value="<?php echo "$imageName"; ?>">
                <input type="submit" class="SelectButton" value="Select" style="background-color: darkblue;"/>
              </form>
              <br><br><br>

              <div class="smallLine"></div>
              <div class"leftInRight" style="float:left;font-size: 135%;">
                <br><br>Standard Rate (Including Breakfast)
              </div>
              <h4 class="text-danger" style="text-align:right"><?php echo $row["price"]+20; ?> USD/night</h4>
              <form action="finaliseRoom.php" target="POST">
                <input type="hidden" name="roomId" value="<?php echo $row["roomId"]; ?>">
                <input type="hidden" name="roomDescription" value="<?php echo $row["roomDescription"]; ?>">
                <input type="hidden" name="type" value="<?php echo $row["type"]; ?>">
                <input type="hidden" name="beds" value="<?php echo $row["beds"]; ?>">
                <input type="hidden" name="price" value="<?php echo $row["price"]; ?>">
                <input type="hidden" name="status" value="<?php echo $row["status"]; ?>">
                <input type="hidden" name="tv" value="<?php echo $row["tv"]; ?>">
                <input type="hidden" name="kitchen" value="<?php echo $row["kitchen"]; ?>">
                <input type="hidden" name="bar" value="<?php echo $row["bar"]; ?>">
                <input type="hidden" name="CheckIn"     value="<?php echo "$checkIn"; ?>">
                <input type="hidden" name="CheckOut"    value="<?php echo "$checkOut"; ?>">
                <input type="hidden" name="adults" value="<?php echo "$adults"; ?>">
                <input type="hidden" name="kids" value="<?php echo "$kids"; ?>">
                <input type="hidden" name="breakfast"   value="1">
                <input type="hidden" name="imageName" value="<?php echo "$imageName"; ?>">
                <input type="submit" class="SelectButton" value="Select" style="background-color: darkblue;"/>
            </form>
            </div>
          </div>
      </div>  
    </div>

    <?php
    }
    }
    else {
      echo "Nothing to display. Please check back later";
    }
    ?>
  </div>
</header>
<p style="visibility:hidden;">hi</p>
<!-- Page content -->
<div class="w3-content" style="max-width:1532px;">
  <div class="w3-container w3-padding-32 w3-black w3-opacity w3-card w3-hover-opacity-off" style="margin:32px 0;">
    <h2>Get the best offers first!</h2>
    <p>Join our newsletter.</p>
    <label>E-mail</label>
    <input class="w3-input w3-border" type="text" placeholder="Your Email address">
    <button type="button" class="w3-button w3-red w3-margin-top">Subscribe</button>
  </div>
  <div class="w3-container" id="contact">
    <h2>Contact</h2>
    <p>If you have any questions, do not hesitate to ask them.</p>
    <i class="fa fa-map-marker w3-text-red" style="width:30px"></i> Chicago, US<br>
    <i class="fa fa-phone w3-text-red" style="width:30px"></i> Phone: +1 (999) 999-9999<br>
    <i class="fa fa-envelope w3-text-red" style="width:30px"> </i> Email: hotelparadise012@gmail.com<br>
    <form action="index.php" method="post">
      <p><input class="w3-input w3-padding-16 w3-border" type="text" placeholder="Name" required name="Name"></p>
      <p><input class="w3-input w3-padding-16 w3-border" type="text" placeholder="Email" required name="Email"></p>
      <p><input class="w3-input w3-padding-16 w3-border" type="text" placeholder="Message" required name="Message"></p>
      <p><button class="w3-button w3-black w3-padding-large" type="submit">SEND MESSAGE</button></p>
    </form>
  </div>

<!-- End page content -->
</div>
<!-- Footer -->
<footer class="w3-padding-32 w3-black w3-center w3-margin-top">
  <h5>Find Us On</h5>
  <div class="w3-xlarge w3-padding-16">
    <i class="fa fa-facebook-official w3-hover-opacity"></i>
    <i class="fa fa-instagram w3-hover-opacity"></i>
    <i class="fa fa-snapchat w3-hover-opacity"></i>
    <i class="fa fa-pinterest-p w3-hover-opacity"></i>
    <i class="fa fa-twitter w3-hover-opacity"></i>
    <i class="fa fa-linkedin w3-hover-opacity"></i>
  </div>
</footer>

  <div class = "bg-modal">
    <div class = "modal-contents">
          <div onclick="closeForm()" class = "close">+</div>
              <form action="hotelRooms.php" target="post">
                <div class="w3-row-padding" style="margin:0 -16px;">

                    <br><br>
                  <div class="w3-half w3-margin-bottom">
                    <label><i class="fa fa-calendar-o"></i> Check In</label>
                    <input class="w3-input w3-border" type="text" id="datepicker" value="<?php echo $checkIn ?>" name="CheckIn" required>
                  </div>
                  <div class="w3-half">
                    <label><i class="fa fa-calendar-o"></i> Check Out</label>
                    <input class="w3-input w3-border" type="text" id="datepicker1" value="<?php echo $checkOut ?>" name="CheckOut" required>
                  </div>
                </div>
                <div class="w3-row-padding" style="margin:4px -16px;">
                  <div class="w3-half w3-margin-bottom">
                    <label><i class="fa fa-male"></i> Adults</label>
                    <input class="w3-input w3-border" type="number" value="<?php echo $adults ?>" name="Adults" min="1" max="6">
                  </div>
                  <div class="w3-half">
                    <label><i class="fa fa-child"></i> Kids</label>
                  <input class="w3-input w3-border" type="number" value="<?php echo $kids ?>" name="Kids" min="0" max="6">
                  </div>
                </div>
                <button class="w3-button w3-dark-grey" type="submit" ><i class="fa fa-search w3-margin-right"></i> Search availability</button>
              </form>
    </div>
  </div>

</body>

<?php
  $result->close();
  $conn->close();
  
  function get_post($conn, $var)
  {
    return $conn->real_escape_string($_POST[$var]);
  }
?>

</html>