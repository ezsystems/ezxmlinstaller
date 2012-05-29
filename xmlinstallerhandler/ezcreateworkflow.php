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

class eZCreateWorkflow extends eZXMLInstallerHandler
{
    function __construct( )
    {
    }

    function execute( $xml )
    {
        $workflowGroupList = $xml->getElementsByTagName( 'WorkflowGroup' );
        $user              = eZUser::currentUser();
        $userID            = $user->attribute( "contentobject_id" );

        foreach ( $workflowGroupList as $workflowGroupNode )
        {
            $groupName        = $workflowGroupNode->getAttribute( 'name' );
            $referenceID      = $workflowGroupNode->getAttribute( 'referenceID' );
            $groupExistAction = $workflowGroupNode->getAttribute( 'groupExistAction' );
            $workflowGroup    = null;

            if ( $groupExistAction == 'keep' )
            {
                $workflowGroupList = eZWorkflowGroup::fetchList();
                foreach ( $workflowGroupList as $workflowGroupItem )
                {
                    if ( $workflowGroupItem->attribute( 'name' ) == $groupName )
                    {
                        $workflowGroup = $workflowGroupItem;
                        break;
                    }
                }
            }
            if ( $workflowGroup !== null )
            {
                $this->writeMessage( "\tWorkflow Group '$groupName' already exists." , 'notice' );
            }
            else
            {
                $this->writeMessage( "\tWorkflow Group '$groupName' will be created." , 'notice' );
                $workflowGroup = eZWorkflowGroup::create( $userID );
                $workflowGroup->setAttribute( "name", $groupName );
                $workflowGroup->store();
            }

            $WorkflowGroupID = $workflowGroup->attribute( "id" );
            $refArray        = array();

            if ( $referenceID )
            {
                $refArray[$referenceID] = $WorkflowGroupID;
            }
            $this->addReference( $refArray );

            $workflowList = $workflowGroupNode->getElementsByTagName( 'Workflow' );
            foreach ( $workflowList as $workflowNode )
            {
                $refArray = array();

                $workflowName        = $workflowNode->getAttribute( 'name' );
                $workflowTypeString  = $workflowNode->getAttribute( 'workflowTypeString' );
                $referenceID         = $workflowNode->getAttribute( 'referenceID' );
                $workflowExistAction = $workflowNode->getAttribute( 'workflowExistAction' );
                $WorkflowID          = $workflowNode->getAttribute( 'id' );
                $workflow            = null;
                $hasWorkflowDraft    = false;
                $db                  = eZDB::instance();

                if ( !$workflowExistAction )
                {
                    $workflowExistAction = 'extend';
                }

                if ( $WorkflowID )
                {
                    $workflow = eZWorkflow::fetch( $WorkflowID, true, 1 );
                    if ( !is_object( $workflow ) )
                    {
                        $workflow = eZWorkflow::fetch( $WorkflowID, true, 0 );
                        if ( is_object( $workflow ) )
                        {
                            $workflowGroups = eZWorkflowGroupLink::fetchGroupList( $WorkflowID, 0, true );

                            $db->begin();
                            foreach ( $workflowGroups as $workflowGroup )
                            {
                                $groupID   = $workflowGroup->attribute( "group_id" );
                                $groupName = $workflowGroup->attribute( "group_name" );
                                $ingroup   = eZWorkflowGroupLink::create( $WorkflowID, 1, $groupID, $groupName );

                                $ingroup->store();
                            }
                            $db->commit();

                        }
                        else
                        {
                            $this->writeMessage( "\tFailed to fetch workflow with ID '$WorkflowID'." , 'notice' );
                            $workflow = null;
                        }
                    }
                }

                $db->begin();

                if ( $workflow === null )
                {
                    $this->writeMessage( "\tWorkflow '$workflowName' will be created." , 'notice' );
                    $workflow = eZWorkflow::create( $userID );
                    $workflow->setAttribute( "name",  $workflowName );
                    if ( $workflowTypeString )
                    {
                        $workflow->setAttribute( "workflow_type_string",  $workflowTypeString );
                    }
                    $workflow->store();
                    $ingroup = eZWorkflowGroupLink::create( $workflow->attribute( "id" ), $workflow->attribute( "version" ), $WorkflowGroupID, $groupName );
                    $ingroup->store();
                }
                else
                {
                    $hasWorkflowDraft = true;
                    switch ( $workflowExistAction )
                    {
                        case 'extend':
                        {
                            $this->writeMessage( "\tExtending existing workflow '" . $workflow->attribute( 'name' ) . "'." , 'notice' );
                        }
                        break;
                        case 'replace':
                        {
                            $this->writeMessage( "\tReplacing existing workflow '" . $workflow->attribute( 'name' ) . "'." , 'notice' );
                            eZWorkflow::removeEvents( false, $workflow->attribute( "id" ), $workflow->attribute( "version" ) );
                        }
                        break;
                        default:
                        {
                            $this->writeMessage( "\tUnknown workflowExistAction '" . $workflowExistAction . "'." , 'notice' );
                        }
                    }
                }

                $WorkflowID      = $workflow->attribute( "id" );
                $WorkflowVersion = $workflow->attribute( "version" );

                $db->commit();

                if ( $referenceID )
                {
                    $refArray[$referenceID] = $WorkflowID;
                }

                $eventList     = $workflow->fetchEvents();
                $eventNodeList = $workflowNode->getElementsByTagName( 'Event' );
                $maxPlacement  = -1;

                foreach ( $eventList as $event )
                {
                    if ( $event->attribute( 'placement' ) > $maxPlacement )
                    {
                        $maxPlacement = $event->attribute( 'placement' );
                    }
                }

                foreach ( $eventNodeList as $eventNode )
                {
                    $description        = $eventNode->getAttribute( 'description' );
                    $workflowTypeString = $eventNode->getAttribute( 'workflowTypeString' );
                    $placement          = $eventNode->getAttribute( 'placement' );
                    $event              = eZWorkflowEvent::create( $WorkflowID, $workflowTypeString );
                    $eventType          = $event->eventType();

                    $db->begin();

                    $workflow->store( $eventList );
                    $eventType->initializeEvent( $event );

                    if ( is_numeric( $placement ) )
                    {
                        $eventType->setAttribute( 'placement', (int)$placement );
                    }
                    else
                    {
                        ++$maxPlacement;
                        $eventType->setAttribute( 'placement', $maxPlacement );
                    }

                    $eventDataNode = $eventNode->getElementsByTagName( 'Data' )->item( 0 );

                    if ( $eventDataNode )
                    {
                        $attributes = $eventDataNode->childNodes;
                        foreach ( $attributes as $attribute )
                        {
                            if ( $event->hasAttribute( $attribute->nodeName ) )
                            {
                                $data = $this->parseAndReplaceStringReferences( $attribute->textContent );
                                $event->setAttribute( $attribute->nodeName, $data );
                            }
                        }
                    }
                    $event->store();

                    $db->commit();

                    $eventList[] = $event;

                }

                // Discard existing events, workflow version 1 and store version 0
                $db->begin();

                $workflow->store( $eventList ); // store changes.

                // Remove old version 0 first
                eZWorkflowGroupLink::removeWorkflowMembers( $WorkflowID, 0 );

                $workflowgroups = eZWorkflowGroupLink::fetchGroupList( $WorkflowID, 1 );
                foreach( $workflowgroups as $workflowgroup )
                {
                    $workflowgroup->setAttribute("workflow_version", 0 );
                    $workflowgroup->store();
                }
                // Remove version 1
                eZWorkflowGroupLink::removeWorkflowMembers( $WorkflowID, 1 );

                eZWorkflow::removeEvents( false, $WorkflowID, 0 );
                $workflow->removeThis( true );
                $workflow->setVersion( 0, $eventList );
                $workflow->adjustEventPlacements( $eventList );
                $workflow->storeDefined( $eventList );
                $workflow->cleanupWorkFlowProcess();

                $db->commit();


                if ( $referenceID )
                {
                    $refArray[$referenceID] = $WorkflowID;
                }
                $this->addReference( $refArray );
            }
        }

        $triggerList = $xml->getElementsByTagName( 'Trigger' );
        foreach ( $triggerList as $triggerNode )
        {
            $module      = $triggerNode->getAttribute( 'module' );
            $operation   = $triggerNode->getAttribute( 'operation' );
            $connectType = $triggerNode->getAttribute( 'connectType' );
            $workflowID  = $this->getReferenceID( $triggerNode->getAttribute( 'workflowID' ) );

            $this->writeMessage( "\tTrigger '$module/$operation/$connectType' will be created/updated." , 'notice' );

            if ( $connectType == 'before' )
            {
                $connectType = 'b';
            }
            else
            {
                $connectType = 'a';
            }

            $parameters = array();
            $parameters['module']      = $module;
            $parameters['function']    = $operation;
            $parameters['connectType'] = $connectType;

            $triggerList = eZTrigger::fetchList( $parameters );

            if ( count( $triggerList ) )
            {
                $trigger = $triggerList[0];
                $trigger->setAttribute( 'workflow_id', $workflowID );
                $trigger->store();
            }
            else
            {
                $db = eZDB::instance();
                $db->begin();
                $newTrigger = eZTrigger::createNew( $module, $operation, $connectType, $workflowID );
                $db->commit();
            }
        }
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'CreateWorkflow', 'Info' => 'create new workflows' );
    }
}

?>
