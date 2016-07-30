<?php 
    include 'scripts/app-functions.php';
    include 'scripts/app-header.php'; 
?>
    <body>
        <div id='wrapper'>
            <div id='page-header'>
                <img src='static/KLZLogo.PNG' id='logo'><h1><?php echo $dwdata['page-title']; ?></h1>
            </div>
            <div id='widget-area'><?php show_widgets($dwdata); ?></div>
            <div id='page-footer'>
                <p>This Dashboard was created for Garlock Klozure by Adam Istvan | Last Major Update 7/29/2016</p>
            </div>
        </div>
    </body>
</html>
