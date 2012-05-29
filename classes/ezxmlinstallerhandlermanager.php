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

class eZXMLInstallerHandlerManager
{
    private function __construct()
    {
        $this->HandlerList    = array();
        $this->MessageArray   = array();
        $this->ReferenceArray = array();
        $this->Settings       = array();
        $this->StepCounter    = 0;
    }

    static function instance()
    {
        if(!eZXMLInstallerHandlerManager::$Instance)
        {
            eZXMLInstallerHandlerManager::$Instance = new eZXMLInstallerHandlerManager();
        }
        return eZXMLInstallerHandlerManager::$Instance;
    }
    /*!
     \static
     Initialize all extension input handler.
    */
    function initialize()
    {
        $ini = eZINI::instance( 'xmlinstaller.ini' );
        $extensionDirectoryList = array_unique( $ini->variable( 'XMLInstallerSettings', 'ExtensionDirectories' ) );
        $handlerList = array_unique( $ini->variable( 'XMLInstallerSettings', 'XMLInstallerHandler' ) );

        foreach( $extensionDirectoryList as $extensionDirectory )
        {
            $handler = reset( $handlerList );
            do
            {
                $fileName = eZExtension::baseDirectory() . '/' . $extensionDirectory . '/xmlinstallerhandler/' . $handler . '.php';
                if ( file_exists( $fileName ) )
                {
                    include_once( $fileName );
                    $className = $handler;
                    $info = call_user_func(array($className, 'handlerInfo'));
                    if ( array_key_exists( 'XMLName', $info ) && array_key_exists( 'Info', $info ) )
                    {
                        $this->HandlerList[$info['XMLName']] = array( 'Info' => $info['Info'],
                                                                      'File' => $fileName,
                                                                      'Class' => $className );
                    }
                    unset($handlerList[key($handlerList)]);
                    $handler = current( $handlerList );
                }
                else
                {
                    $handler = next( $handlerList );
                }
            } while ( $handler );
        }
    }

    function executeHandler( $name, &$xml )
    {
        $result = false;
        if ( array_key_exists( $name, $this->HandlerList ) )
        {
            $fileName = $this->HandlerList[$name]['File'];
            $className = $this->HandlerList[$name]['Class'];

            if ( file_exists( $fileName ) )
            {
                include_once( $fileName );

                $object = new $className();
                $object->initialize( $this->ReferenceArray, $this->Settings, $this->StepCounter );

                $object->execute( $xml );

                $this->ReferenceArray = $object->references();
                $this->Settings       = $object->settings();
                $this->StepCounter    = $object->counter();
            }
        }
        else
        {
            $this->writeMessage( 'Function ' . $name . ' is not registered.' , 'error' );
        }
        return $result;
    }

    function writeMessage( $message, $type = 'notice', $color = false )
    {
        $message = str_replace("\t", "  ", $message);
        if ( isset( $_SERVER['argv'] ) )
        {
            $cli = eZCLI::instance();
            if ($color)
            {
                $message = $cli->stylize( $color, $message);
            }
            switch ( $type )
            {
                case 'debug':
                {
                    $message = $cli->stylize( "dark-white", $message);
                    $cli->notice( $message );
                } break;
                case 'notice':
                {
                    $message = $cli->stylize( "gray", $message);
                    $cli->notice( $message );
                } break;
                case 'success':
                {
                    $message = $cli->stylize( "green", $message);
                    $cli->notice( $message );
                } break;
                case 'warn':
                case 'warning':
                {
                    $message = $cli->stylize( "yellow", $message);
                    $cli->warning( $message );
                } break;
                case 'error':
                {
                    $message = $cli->stylize( "red", $message);
                    $cli->error( $message );
                } break;
                default:
                {
                    $cli->notice( $message );
                } break;
            }
        }
        if ( !array_key_exists( $type, $this->MessageArray ) )
        {
            $this->MessageArray[$type] = array();
        }
        $this->MessageArray[$type][] = $message;
    }

    function setSettings( $settingArray )
    {
        if ( is_array( $settingArray ) )
        {
            $this->Settings = $settingArray;
        }
    }

    static private $Instance;

    var $HandlerList;
    var $MessageArray;
    var $ReferenceArray;
    var $Settings;
    var $StepCounter;
}

?>
