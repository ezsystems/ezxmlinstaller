<?php

$Module = array( 'name' => 'XML Export',
                 'variable_params' => true );


$ViewList['classes'] = array(
    'functions' => array( 'export' ),
    'script' => 'classes.php',
    'params' => array( ) );
$ViewList['roles'] = array(
    'functions' => array( 'export' ),
    'script' => 'roles.php',
    'params' => array( ) );


$FunctionList = array();
$FunctionList['export'] = array( );

?>
