<?php
//
// Created on: <2007-12-28 06:09:00 dis>
//
// SOFTWARE NAME: eZ XML Installer extension for eZ Publish
// SOFTWARE RELEASE: 0.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2012 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
require 'autoload.php';

function changeSiteAccessSetting( $siteaccess )
{
    global $cli;
    if ( file_exists( 'settings/siteaccess/' . $siteaccess ) )
    {
        $cli->notice( 'Using siteaccess "' . $siteaccess . '" for installation from XML' );
    }
    elseif ( isExtensionSiteaccess( $siteaccess ) )
    {
        $cli->notice( 'Using extension siteaccess "' . $siteaccess . '" for installation from XML' );
        eZExtension::prependExtensionSiteAccesses( $siteaccess );
    }
    else
    {
        $cli->notice( 'Siteaccess "' . $siteaccess . '" does not exist, using default siteaccess' );
    }
}

function isExtensionSiteaccess( $siteaccessName )
{
    $siteINI            = eZINI::instance();
    $extensionDirectory = $siteINI->variable( 'ExtensionSettings', 'ExtensionDirectory' );
    $activeExtensions   = $siteINI->variable( 'ExtensionSettings', 'ActiveExtensions' );

    foreach ( $activeExtensions as $extensionName )
    {
        $possibleExtensionPath = $extensionDirectory . '/' . $extensionName . '/settings/siteaccess/' . $siteaccessName;
        if ( file_exists( $possibleExtensionPath ) )
        {
            return true;
        }
    }
    return false;
}

global $cli;

$cli    = eZCLI::instance();
$script = eZScript::instance( array( 'description'     => ( "eZ Publish XML installer\n\n" .
                                                            ""
                                                          ),
                                      'use-session'    => true,
                                      'use-modules'    => true,
                                      'use-extensions' => true
                                   )
                            );

$script->startup();

$options = $script->getOptions( "[file:][template:][user:]",
                                "",
                                array( 'file' => 'File with the xml definition to proceed',
                                       'template' => 'Location of the template to use',
                                       'user' => 'name of the user to use' ),
                                false,
                                array( 'user' => true ));

$siteAccess = $options['siteaccess'] ? $options['siteaccess'] : false;

if ( $siteAccess )
{
    changeSiteAccessSetting( $siteAccess );
    $script->setUseSiteAccess( $siteAccess );
}

$script->initialize();

if ( !$script->isInitialized() )
{
    $cli->error( 'Error initializing script: ' . $script->initializationError() . '.' );
    $script->shutdown( 0 );
}

$cli->output( $cli->stylize( "yellow", "Checking requirements...") );
if ( isset( $options['user'] ) && $options['user'] )
{
    $user = eZUser::fetch( $options['user'] );
}
else
{
    $user = eZUser::fetchByName( 'admin' );
}

if ( $user )
{
    eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
}

if( !( isset( $options['file'] ) || isset( $options['template'] ) ) )
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
$cli->output( $cli->stylize( "yellow", "Trying to install data from XML ...") );

if ( $xml == '' )
{
    $cli->error( "No XML data available." );
    $script->shutdown( 1 );
}

$dom = new DOMDocument( '1.0', 'utf-8' );
if ( !$dom->loadXML( $xml ) )
{
    $cli->error( "Failed to load XML." );
    $script->shutdown( 1 );
}

$xmlInstaller = new eZXMLInstaller( $dom );

if ( !$xmlInstaller->proccessXML() )
{
    $cli->error( "Errors while proccessing XML." );
    $script->shutdown( 1 );
}

$cli->output( "Finished." );
$script->shutdown();

?>
