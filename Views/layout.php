<DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style> 
      td input, td b {
        width: 70%;
        padding: 2px;
        margin: 0px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
      }
    </style>
  </head>
  <body>
    <header>
      <a href='http://localhost/magshop/'>Home</a>
    </header>

    <?php require_once('routes.php'); ?>

    <footer>
      
    </footer>
    <script type="text/javascript">
      $('#deep').change(function(){
        var input = $("<input>")
               .attr("type", "hidden")
               .attr("name", "deep").val(true);
        $("form").not(":has([name='deep'])").append($(input));
        $("form [name='deep']").val($('#deep').val());
      });
    </script>
  <body>
<html>