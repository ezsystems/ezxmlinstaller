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

class eZXMLInstaller
{
    function __construct( $domDocument )
    {
        $this->rootDomNode = $domDocument->documentElement;
        $this->cli = eZCLI::instance();
    }

    function proccessXML( )
    {
        $installerHandlerManager = eZXMLInstallerHandlerManager::instance();
        $installerHandlerManager->initialize();
        if ( $this->rootDomNode &&
             $this->rootDomNode->nodeType == XML_ELEMENT_NODE &&
             $this->rootDomNode->nodeName == 'eZXMLInstaller' )
        {
            if ( $this->rootDomNode->hasAttributes() )
            {
                if($this->rootDomNode->hasAttributes())
                {
                    $attributes = $this->rootDomNode->attributes;
                    if(!is_null($attributes))
                    {
                        $settings = array();
                        foreach ($attributes as $index=>$attr)
                        {
                            $settings[$attr->name] = $attr->value;
                        }
                    }
                }
                $installerHandlerManager->setSettings( $settings );
            }
            if ( $this->rootDomNode->hasChildNodes() )
            {
                $children = $this->rootDomNode->childNodes;
                foreach ( $children as $child )
                {
                    if ( $child->nodeType == XML_ELEMENT_NODE )
                    {
                        $installerHandlerManager->executeHandler( $child->nodeName, $child );
                    }
                }
            }
            else
            {
                $installerHandlerManager->writeMessage( "XML has no valid information.", 'error' );
                return false;
            }
        }
        else
        {
            $installerHandlerManager->writeMessage( "XML is not initialized.", 'error' );
            return false;
        }
        return true;
    }

    var $rootDomNode;
    var $cli;
}

?>
