<?php

require_once( 'autoload.php' );
require_once( 'lib/ezutils/classes/ezextension.php' );
require_once( 'lib/ezutils/classes/ezcli.php' );
require_once( 'kernel/classes/ezscript.php' );


include_once( eZExtension::baseDirectory() . '/ezxmlinstaller/classes/ezxmlinstaller.php' );
include_once( eZExtension::baseDirectory() . '/ezxmlinstaller/classes/ezxmlinstallerhandlermanager.php' );
include_once( eZExtension::baseDirectory() . '/ezxmlinstaller/classes/ezpreparexml.php' );

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "eZ publish xml installer\n\n" .
                                                         "" ),
                                      'use-session' => false,
                                      'use-modules' => true,
                                      'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions( "[file:][template:]",
                                "",
                                array( 'file' => 'file with xml definition',
                                       'template' => 'name of template to use' ),
                                false,
                                array( 'user' => true ));
$script->initialize();

$cli->output( "Checking requirements..." );


$user = eZUser::fetch( 14 );
eZUser::setCurrentlyLoggedInUser( $user, 14 );


if( !(isset($options['file']) or isset($options['template'])) )
{
    $cli->error( "Need at least a file or a template." );
    $script->shutdown( 1 );
}


if ( isset( $options['file'] ) )
{
    $xml = eZPrepareXML::prepareXMLFromFile( $options['file'], $cli );
}
elseif ( isset( $options['template'] ) )
{
    $xml = eZPrepareXML::prepareXMLFromTemplate( $options['template'], $cli );
}
else
{
    $cli->error( "Need at least one argument." );
    $script->shutdown( 1 );
}
$cli->output( "Going to create new siteaccess from data..." );

$dom = new DOMDocument(  );
if ( !$dom->loadXML( $xml ) )
{
    $cli->error( "Couldn't load xml." );
    $script->shutdown( 1 );
}

$xmlInstaller = new eZXMLInstaller( $dom );

if (! $xmlInstaller->proccessXML() )
{
    $cli->error( "Errors during XML proccessing." );
    $script->shutdown( 1 );
}

$cli->output( "Finished." );
$script->shutdown();

?>
