<?php

$module = $Params['Module'];
$http       = eZHTTPTool::instance();


require_once( 'kernel/common/template.php' );
$tpl        = templateInit();

$list = eZContentClass::fetchList( );

$optList = array();
foreach ($list as $class )
{
    $optList[$class->attribute('identifier')] = array();
    foreach( $class->attribute('data_map') as $attribute )
    {
        $dataType = $attribute->attribute( 'data_type' );

        $doc = new DOMDocument;
        $attributeNode = $doc->createElement( 'attribute' );
        $attributeParametersNode = $doc->createElement( 'datatype-parameters' );
        $attributeNode->appendChild( $attributeParametersNode );

        $dataType->serializeContentClassAttribute( $attribute, $attributeNode, $attributeParametersNode );
        $doc->appendChild( $attributeNode );

        $attributes = $attributeParametersNode->childNodes;
        foreach ( $attributes as $attr )
        {
            $optList[$class->attribute('identifier')][$attribute->attribute('identifier')][$attr->nodeName] = $attr->textContent;
        }

    }
}
$tpl->setVariable( 'class_list', $list );
$tpl->setVariable( 'opt_list', $optList );
$tpl->setVariable( "class_count", count( $list ) );


$result = $tpl->fetch( 'design:xmlexport/classes.tpl' );

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
