<?php

$module = $Params['Module'];
$http       = eZHTTPTool::instance();


require_once( 'kernel/common/template.php' );
$tpl        = templateInit();

$list = eZRole::fetchList( );

$tpl->setVariable( 'role_list', $list );
$tpl->setVariable( "role_count", count( $list ) );


$result = $tpl->fetch( 'design:xmlexport/roles.tpl' );

$doc = new DOMDocument;
$doc->loadXML( $result );


eZExecution::cleanup();
eZExecution::setCleanExit();
header('Content-Type: text/xml');
// header('Content-Type: text/html');
header('Pragma: no-cache' );
header('Expires: 0' );

echo $doc->saveXML();
// echo $result;

exit(0);



?>
