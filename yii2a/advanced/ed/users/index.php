<?php
session_start();
if((isset( $_SESSION['isSupport']) && $_SESSION['isSupport']== 1) || (isset( $_SESSION['isAdmin']) && $_SESSION['isAdmin']== 1))
{
    ?>
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=5,IE=9"/><![endif]-->
    <!DOCTYPE html>

    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>EventDraw List of Users</title>
        <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/responsive.css" type="text/css" media="screen">

        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
        <link rel="stylesheet" href="css/font-awesome-4.7.0/css/font-awesome.min.css" type="text/css" media="screen">
    </head>
    <body>
    <div id="message"></div>
    <div id="wrap">
        <h1>List of Users</h1>
        <div id="toolbar">
            <input type="text" id="filter" name="filter" placeholder="Filter :type any text here"  />
            <a id="showaddformbutton" class="button green"><i class="fa fa-plus"></i> Add new user</a>
        </div>
        <!-- Grid contents -->
        <div id="tablecontent"></div>

        <!-- Paginator control -->
        <div id="paginator"></div>


    </div>


    <script src="js/jquery-1.11.1.min.js" ></script>
    <script src="js/editablegrid-2.1.0-49.js"></script>
    <!-- EditableGrid test if jQuery UI is present. If present, a datepicker is automatically used for date type -->
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script src="js/demo.js" ></script>

    <script type="text/javascript">

        var datagrid;

        window.onload = function() {
            datagrid = new DatabaseGrid();
            $("#filter").keyup(function() {
                datagrid.editableGrid.filter( $(this).val());
            });

            $("#showaddformbutton").click( function()  {
                showAddForm();
            });
            $("#cancelbutton").click( function() {
                showAddForm();
            });

            $("#addbutton").click(function() {
                datagrid.addRow();
            });
        }

        $(function () {

        });

    </script>

    <!-- simple form, used to add a new row -->
    <div id="addform">

        <div class="row">
            <input type="text" id="name" name="name" placeholder="User Name" />
        </div>

        <div class="row">
            <input type="text" id="firstname" name="firstname" placeholder="Email" />
        </div>

        <div class="row tright">
            <a id="addbutton" class="button green" ><i class="fa fa-save"></i> Apply</a>
            <a id="cancelbutton" class="button delete">Cancel</a>
        </div>
    </div>

    </body>

    </html>

    <?php
}
else
{
    // User is not login
    header("Location: ../login.php");
}
?>