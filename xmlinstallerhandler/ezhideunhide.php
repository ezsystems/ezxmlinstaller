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

class eZHideUnhide extends eZXMLInstallerHandler
{

    function __construct( )
    {
    }

    function execute( $xmlNode )
    {
        $action = $xmlNode->getAttribute( 'action' );
        $xmlNodeID = $xmlNode->getAttribute( 'nodeID' );

        $nodeID = $this->getReferenceID( $xmlNodeID );
        if ( !$nodeID )
        {
            $this->writeMessage( "\tInvalid node $nodeID does not exist.", 'warning' );
            return false;
        }

        if ( !$action )
        {
            $action = 'toogle';
        }
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$node )
        {
            $this->writeMessage( "\tNo node defined.", 'error' );
            return false;
        }

        switch ( $action )
        {
            case 'unhide':
            {
                if ( $node->attribute( 'is_hidden' ) )
                {
                    eZContentObjectTreeNode::unhideSubTree( $node );
                    $this->writeMessage( "\tNode " . $node->attribute( 'name' ) . " has be unhidden.", 'error' );
                }
            } break;
            case 'hide':
            {
                if ( !$node->attribute( 'is_hidden' ) )
                {
                    eZContentObjectTreeNode::hideSubTree( $node );
                    $this->writeMessage( "\tNode " . $node->attribute( 'name' ) . " has be hidden.", 'error' );
                }
            } break;
            case 'toogle':
            {
                if ( $node->attribute( 'is_hidden' ) )
                {
                    eZContentObjectTreeNode::unhideSubTree( $node );
                    $this->writeMessage( "\tNode " . $node->attribute( 'name' ) . " has be unhidden.", 'error' );
                }
                else
                {
                    eZContentObjectTreeNode::hideSubTree( $node );
                    $this->writeMessage( "\tNode " . $node->attribute( 'name' ) . " has be hidden.", 'error' );
                }
            } break;
        }
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'HideUnhide', 'Info' => 'hide/unhide subtree' );
    }
}

?>
