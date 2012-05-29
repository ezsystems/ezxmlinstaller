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

class eZXMLInstallerHandler
{

    function __construct()
    {
    }


    function initialize( &$references, &$settings, &$counter )
    {
        $this->ReferenceArray = $references;
        $this->Settings       = $settings;
        $this->StepCounter    = $counter;
    }

    function execute()
    {
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => '', 'Info' => '' );
    }

    function writeMessage( $message, $type = 'notice', $color = false )
    {
        $handlerManager = eZXMLInstallerHandlerManager::instance();
        $handlerManager->writeMessage( $message, $type, $color );
    }

    function addReference( $refInfo )
    {
        if ( is_array( $refInfo ) )
        {
            $this->ReferenceArray = array_merge( $this->ReferenceArray, $refInfo );
        }
    }

    function references( )
    {
        return $this->ReferenceArray;
    }

    function getReferenceID( $refInfo )
    {
        $splitted = explode( ':', $refInfo );
        if ( !(is_array( $splitted ) && count($splitted) == 2 ) )
        {
            return $refInfo;
        }
        $type = $splitted[0];
        $refID = $splitted[1];
        $referenceID = false;

        switch( $type )
        {
            case 'internal':
            {
                if ( array_key_exists( $refID, $this->ReferenceArray ) )
                {
                    $referenceID = $this->ReferenceArray[$refID];
                }
            } break;
            case 'object_id':
            {
                $intRef = (int)$refID;
                if( $intRef > 0 )
                {
                    $relContentObject = eZContentObject::fetch( $intRef );
                    if ( $relContentObject )
                    {
                        $referenceID = $intRef;
                    }
                }
            } break;
            case 'node_id':
            {
                $intRef = (int)$refID;
                if( $intRef > 0 )
                {
                    $relNode = eZContentObjectTreeNode::fetch( $intRef );
                    if ( $relNode )
                    {
                        $referenceID = $intRef;
                    }
                }
            } break;
            case 'remote_id':
            {
                $relContentObject = eZContentObject::fetchByRemoteID( $refID );
                if ( $relContentObject )
                {
                    $referenceID = $relContentObject->ID;
                }
            } break;
            case 'remote_id_node_id':
            {
                $relContentObject = eZContentObject::fetchByRemoteID( $refID );
                if ( $relContentObject )
                {
                    $referenceID = $relContentObject->mainNodeID();
                }
            } break;
        }
        return $referenceID;
    }

    function parseAndReplaceStringReferences( $string )
    {
        $result = array();
        $count = preg_match_all( '|\[([^\]\[]*)\]|', $string, $result );
        if ( count( $result ) > 1 )
        {
            foreach ( $result[1] as $i => $refInfo )
            {
                $id = $this->getReferenceID( $refInfo );
                $string = str_replace( $result[0][$i], $id, $string );
            }
        }
        $string = str_replace( '&#93;', ']', $string );
        $string = str_replace( '&#91;', '[', $string );
        return $string;
    }

    /**
     * Browses $node to replace any string references in subnodes or attributes
     *
     * @since 1.2.1
     * @param DOMNode 	$node	node to inspect
     * @return DOMNode	Node with replaced string references
     */
    function parseAndReplaceNodeStringReferences( DOMNode $node )
    {
        $attrs = $node->attributes;
        foreach ( $attrs as $attr )
        {
            $node->setAttribute( $attr->name, $this->getReferenceID( $attr->value ) );
        }

        $children = $node->childNodes;
        foreach ( $children as $child )
        {
            switch ( $child->nodeType )
            {
                case XML_TEXT_NODE:
                    $child->textContent = $this->parseAndReplaceStringReferences( $child->textContent );
                    break;

                case XML_CDATA_SECTION_NODE:
                    $child->replaceData( $this->parseAndReplaceStringReferences( $child->data ) );
                    break;

                case XML_ELEMENT_NODE:
                    $child = $this->parseAndReplaceNodeStringReferences($child);
                    break;
            }
        }

        return $node;
    }

    function settings( )
    {
        return $this->Settings;
    }

    function setting( $key )
    {
        if ( array_key_exists( $key, $this->Settings ) )
        {
            return $this->Settings[$key];
        }
        else
        {
            return NULL;
        }
    }

    function setSettings( $settingArray )
    {
        if ( is_array( $settingArray ) )
        {
            $this->Settings = $settingArray;
        }
    }

    function counter()
    {
        return $this->StepCounter;
    }

    function increaseCouter()
    {
        return ++$this->StepCounter;
    }

    var $ReferenceArray;
    var $Settings;
    var $StepCounter;
}

?>
