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

class eZSetSettings extends eZXMLInstallerHandler
{

    function __construct( )
    {
    }

    function execute( $xml )
    {
        $settingsFileList = $xml->getElementsByTagName( 'SettingsFile' );
        foreach ( $settingsFileList as $settingsFile )
        {
            $fileName = $settingsFile->getAttribute( 'name' );
            $location = $settingsFile->getAttribute( 'location' );

            eZDir::mkdir( $location );
            $fileNamePath = $location . eZDir::separator( eZDir::SEPARATOR_LOCAL ) . $fileName;

            $this->writeMessage( "\tSetting settings: $fileNamePath", 'notice' );

            if ( !file_exists( $fileNamePath ) )
            {
                if ( !is_writeable( $location ) )
                {
                    $this->writeMessage( "\tFile $fileNamePath can not be created. Skipping.", 'notice' );
                    continue;
                }
            }
            else
            {
                if ( !is_readable( $fileName ) )
                {
                    $this->writeMessage( "\tFile $fileNamePath is not readable. Skipping.", 'notice' );
                    continue;
                }
                if ( !is_writeable( $fileName ) )
                {
                    $this->writeMessage( "\tFile $fileNamePath is not writeable. Skipping.", 'notice' );
                    continue;
                }
            }
            $ini = eZINI::instance( $fileName, $location, null, null, null, true, true );
            $settingsBlockList = $settingsFile->getElementsByTagName( 'SettingsBlock' );
            foreach ( $settingsBlockList as $settingsBlock )
            {
                $blockName = $settingsBlock->getAttribute( 'name' );
                $values = $settingsBlock->childNodes;
                $settingValue = false;
                foreach ( $values as $value )
                {
                    $variableName = $value->nodeName;
                    if ( $value->nodeName == "#text" )
                    {
                        continue;
                    }

                    if ( get_class($value) == 'DOMElement' && $value->hasAttribute( 'value' ) )
                    {
                        $settingValue = $value->getAttribute( 'value' );
                    }
                    elseif ( get_class($value) == 'DOMElement' && $value->hasChildNodes() )
                    {
                        if ( $value->firstChild->nodeName == $value->lastChild->nodeName && $value->childNodes->length>1 )
                        {
                            $variableName = $value->tagName;
                            $vals = $value->getElementsByTagName( 'value' );
                            $settingValue = array();
                            foreach ( $vals as $val )
                            {
                                $key = $val->getAttribute( 'key' );
                                if ( $key )
                                {
                                    $settingValue[$key] = $this->parseAndReplaceStringReferences( $val->textContent );
                                }
                                else
                                {
                                    $settingValue[] = $this->parseAndReplaceStringReferences( $val->textContent );
                                }
                            }
                        }
                        else
                        {
                            $settingValue  = $this->parseAndReplaceStringReferences( $value->nodeValue );
                       }
                    }
                    elseif ( $value->textContent )
                    {
                        $settingValue = $value->textContent;
                    }
                    else
                    {
                        $settingValue = $this->parseAndReplaceStringReferences( $value->textContent );
                    }
                    $existingVar = false;
                    if ( $ini->hasVariable( $blockName, $variableName ) )
                    {
                        $existingVar = $ini->variable( $blockName, $variableName );
                    }
                    if ( is_string( $existingVar ) && is_string( $settingValue ) )
                    {
                        $ini->setVariable( $blockName, $variableName, $settingValue );
                    }
                    elseif ( is_array( $existingVar ) && is_string( $settingValue ) )
                    {
                        $existingVar[] = $settingValue;
                        $ini->setVariable( $blockName, $variableName, $existingVar );
                    }
                    elseif ( is_array( $existingVar ) && is_array( $settingValue ) )
                    {
                        // an empty value in a list means a reset of the setting
                        if ( array_search( "", $settingValue, true ) !== false )
                            $mergedArray = $settingValue;
                        else
                            $mergedArray = array_merge( $existingVar, $settingValue );
                        $ini->setVariable( $blockName, $variableName, array_unique( $mergedArray ) );
                    }
                    else
                    {
                        $ini->setVariable( $blockName, $variableName, $settingValue );
                    }
                }
            }
            $ini->save( false, ".append.php" );
            unset( $ini );
        }
        eZCache::clearByID( array( 'ini', 'global_ini' ) );
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'SetSettings', 'Info' => 'manipulate settings files' );
    }
}

?>
