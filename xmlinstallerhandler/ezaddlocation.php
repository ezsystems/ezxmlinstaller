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

class eZAddLocation extends eZXMLInstallerHandler
{

    function __construct( )
    {
    }

    function execute( $xmlNode )
    {
        $xmlObjectID = $xmlNode->getAttribute( 'contentObject' );
        $xmlParentNodeID = $xmlNode->getAttribute( 'addToNode' );
        $setReferenceID = $xmlNode->getAttribute( 'setReference' );
        $priority = $xmlNode->getAttribute( 'priority' );

        $objectID = $this->getReferenceID( $xmlObjectID );
        $parentNodeID = $this->getReferenceID( $xmlParentNodeID );

        $this->writeMessage( "\tAdding location(s) to object [$objectID].", "notice", "cyan" );

        if ( !$objectID )
        {
            $this->writeMessage( "\t\tNo object defined.", 'error' );
            return false;
        }
        if ( !$parentNodeID )
        {
            $this->writeMessage( "\t\tNo location defined.", 'error' );
            return false;
        }

        $object = eZContentObject::fetch( $objectID );
        if ( !$object )
        {
            $this->writeMessage( "\t\tObject not found.", 'error' );
            return false;
        }

        $parentNode =  eZContentObjectTreeNode::fetch( $parentNodeID );
        if ( !$parentNode )
        {
            $this->writeMessage( "\t\tParent node not found.", 'error' );
            return false;
        }

        $node = $object->attribute( 'main_node' );

        $nodeAssignmentList = eZNodeAssignment::fetchForObject( $objectID, $object->attribute( 'current_version' ), 0, false );
        $assignedNodes = $object->assignedNodes();

        $parentNodeIDArray = array();
        $setMainNode = false;
        $hasMainNode = false;
        foreach ( $assignedNodes as $assignedNode )
        {
            if ( $assignedNode->attribute( 'is_main' ) )
                $hasMainNode = true;
            $append = false;
            foreach ( $nodeAssignmentList as $nodeAssignment )
            {
                if ( $nodeAssignment['parent_node'] == $assignedNode->attribute( 'parent_node_id' ) )
                {
                    $append = true;
                    break;
                }
            }
            if ( $append )
            {
                $parentNodeIDArray[] = $assignedNode->attribute( 'parent_node_id' );
            }
        }
        if ( !$hasMainNode )
            $setMainNode = true;

        $mainNodeID = $parentNode->attribute( 'main_node_id' );
        $objectName = $object->attribute( 'name' );

        $db = eZDB::instance();
        $db->begin();
        $locationAdded = false;
        $destNode = null;
        if ( !in_array( $parentNodeID, $parentNodeIDArray ) )
        {
            $parentNodeObject = $parentNode->attribute( 'object' );

            $insertedNode = $object->addLocation( $parentNodeID, true );

            // Now set is as published and fix main_node_id
            $insertedNode->setAttribute( 'contentobject_is_published', 1 );
            $insertedNode->setAttribute( 'main_node_id', $node->attribute( 'main_node_id' ) );
            $insertedNode->setAttribute( 'contentobject_version', $node->attribute( 'contentobject_version' ) );
            // Make sure the url alias is set updated.
            $insertedNode->updateSubTreePath();
            $insertedNode->sync();

            $locationAdded = true;
        }
        if ( $locationAdded )
        {
            $ini = eZINI::instance();
            $userClassID = $ini->variable( "UserSettings", "UserClassID" );
            if ( $object->attribute( 'contentclass_id' ) == $userClassID )
            {
                eZUser::cleanupCache();
            }
            $this->writeMessage( "\t\tAdded location of " . $object->attribute( 'name' ) . " to node [$parentNodeID].", 'success' );

            $destNode = $insertedNode;
        }
        else
        {
            $this->writeMessage( "\t\tLocation of " . $object->attribute( 'name' ) . " to node [$parentNodeID] already exists.", 'warning' );

            $destNode = eZContentObjectTreeNode::fetchObject( eZContentObjectTreeNode::definition(), null, array( 'parent_node_id' => $parentNodeID, 'contentobject_id' => $objectID ) );
        }
        $db->commit();

        if( $destNode && $priority )
        {
            $destNode->setAttribute( 'priority', $priority );
            $destNode->store();
        }

        if( $destNode && $setReferenceID )
        {
            $this->addReference( array( $setReferenceID => $destNode->attribute( 'node_id' ) ) );
        }

        eZContentCacheManager::clearContentCacheIfNeeded( $objectID );
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'AddLocation', 'Info' => 'add location of content object' );
    }
}

?>
