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


class eZCreateContent extends eZXMLInstallerHandler
{

    /**
     * Priority management modes
     * Mode can be specified in each CreateContent or Childs node within a "priorityMode" attribute
     * Available values :
     * - none : no management at all
     * - fixed : priority is expected to be specified on each ContentObject node within a "priority" attribute
     * - auto :  priorities are automatically incremented
     *
     * Default is none
     * @since 1.2.1
     */
    const PRIORITY_MODE_NONE  = 'none';
    const PRIORITY_MODE_FIXED = 'fixed';
    const PRIORITY_MODE_AUTO  = 'auto';

    const EDIT_MODE_SKIP       = 'skip';
    const EDIT_MODE_NEWVERSION = 'newversion';
    const EDIT_MODE_NEWOBJECT  = 'newobject';
    const EDIT_MODE_OVERWRITE  = 'overwrite';

    const ATTR_MODE_SKIP       = 'skip';
    const ATTR_MODE_CHANGE     = 'change';
    const ATTR_MODE_REMOVE     = 'remove';

    function __construct( )
    {
        $this->EditModeList = array( self::EDIT_MODE_SKIP, self::EDIT_MODE_NEWVERSION, self::EDIT_MODE_NEWOBJECT, self::EDIT_MODE_OVERWRITE);
    }

    function execute( $xml )
    {
        $this->proccessCreateContent( $xml );
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'CreateContent', 'Info' => 'create new content structure' );
    }


    function proccessCreateContent( $xmlNode, $parentNodeID = false )
    {
        if ( !$parentNodeID )
        {
            $parentNodeID = $xmlNode->getAttribute( 'parentNode' );
            if ( !$parentNodeID )
            {
                if ($this->setting( 'defaultParentNode' ))
                {
                    $parentNodeID = $this->setting( 'defaultParentNode' );
                }
                else
                {
                    $parentNodeID = 2;
                }
                $this->writeMessage( "\tNo parent node defined. Using node $parentNodeID.", 'warning' );
            }
        }

        $defautEditMode = $xmlNode->getAttribute( 'creationMode' );
        if (!in_array($defautEditMode, $this->EditModeList))
        {
            $defautEditMode = self::EDIT_MODE_NEWVERSION;
        }

        $priorityMode = $xmlNode->hasAttribute( 'priorityMode' ) ? $xmlNode->getAttribute( 'priorityMode' ) : self::PRIORITY_MODE_NONE;
        $priorityCounter = 0;

        $objectList = $xmlNode->childNodes;
        foreach ( $objectList as $objectNode )
        {
            if ( $objectNode->nodeName != 'ContentObject' )
                continue;

            $editMode = $objectNode->getAttribute( 'creationMode' );
            if (!in_array($editMode, $this->EditModeList))
            {
                $editMode = $defautEditMode;
            }

            $objectInformation = array();
            $objectInformation['edit_mode'] = $editMode;
            $objectInformation['parentNode'] = $this->getReferenceID($parentNodeID);

            $objectInformation['classID'] = $this->getReferenceID( $objectNode->getAttribute( 'contentClass' ) );
            $objectInformation['remoteID'] = $objectNode->getAttribute( 'remoteID' );
            $objectInformation['objectID'] = $objectNode->getAttribute( 'objectID' );
            $objectInformation['sectionID'] = $this->getReferenceID( $objectNode->getAttribute( 'section' ) );
            $objectInformation['ownerID'] = $objectNode->getAttribute( 'owner' );
            $objectInformation['creatorID'] = $objectNode->getAttribute( 'creator' );
            $objectInformation['attributes'] = array();
            $objectInformation['sort_field'] = $objectNode->hasAttribute( 'sort_field' ) ? $objectNode->getAttribute( 'sort_field' ) : 'path';
            $objectInformation['sort_order'] = $objectNode->hasAttribute( 'sort_order' ) ? $objectNode->getAttribute( 'sort_order' ) : 'asc';

            switch( $priorityMode )
            {
                case self::PRIORITY_MODE_AUTO:
                    $objectInformation['priority'] = $priorityCounter;
                    break;

                case self::PRIORITY_MODE_FIXED:
                    $objectInformation['priority'] = $objectNode->hasAttribute( 'priority' ) ? intval( $objectNode->getAttribute( 'priority' ) ) : 0;
                    break;

                case self::PRIORITY_MODE_NONE:
                default:
                    $objectInformation['priority'] = 0;
                    break;
            }

            $attributeObject = $objectNode->getElementsByTagName( 'Attributes' )->item( 0 );
            if ( $attributeObject )
            {
                $attributes = $attributeObject->childNodes;
                foreach ( $attributes as $attribute )
                {
                    if ( $attribute->nodeName == "#text")
                        continue;
                    $objectInformation['attributes'][$attribute->nodeName] = array();
                    $objectInformation['attributes'][$attribute->nodeName]['content'] = $attribute->textContent;
                    if ( $attribute->hasAttributes() )
                    {
                        $attr = $attribute->attributes;
                        if ( !is_null($attr) )
                        {
                            $attrList = array();
                            foreach ( $attr as $i => $a )
                            {
                                $attrList[$a->name] = $a->value;
                            }
                        }
                        $objectInformation['attributes'][$attribute->nodeName] = array_merge( $attrList, $objectInformation['attributes'][$attribute->nodeName] );
                    }
                }
            }

            $relationNodes = $objectNode->getElementsByTagName( 'Relation' );
            if( $relationNodes->length )
            {
                $objectInformation['relations'] = array();
                foreach ( $relationNodes as $relationNode )
                {
                    $targetRef = $relationNode->getAttribute( 'target' );
                    if( $targetRef )
                    {
                        $objectInformation['relations'][] = $this->getReferenceID( $targetRef );
                    }
                    else
                    {
                        $this->writeMessage( 'No target defined for object relation', 'warning' );
                    }
                }
            }

            $locationNodes = $objectNode->getElementsByTagName( 'AdditionalLocation' );
            if( $locationNodes->length )
            {
                $objectInformation['locations'] = array();
                foreach ( $locationNodes as $locationNode )
                {
                    $tmpParentNodeID = $locationNode->getAttribute( 'parentNode' );
                    if( $tmpParentNodeID )
                    {
                        $objectInformation['locations'][] = $this->getReferenceID( $tmpParentNodeID );
                    }
                    else
                    {
                        $this->writeMessage( 'No target defined for object relation', 'warning' );
                    }
                }
            }

            $refInfo = $this->createContentObject( $objectInformation );

            if( $refInfo )
            {
                $priorityCounter++;
            }

            $referenceList = $objectNode->childNodes;
            $refArray = array();
            foreach ( $referenceList as $reference )
            {
                if ( $reference->nodeName != 'SetReference' )
                    continue;
                if ( $reference->hasAttributes() )
                {
                    $attr = $reference->attributes;
                    if ( !is_null($attr) )
                    {
                        $attributes = array();
                        foreach ( $attr as $i => $a )
                        {
                            $attributes[$a->name] = $a->value;
                        }
                    }
                }

                if ( is_array( $refInfo ) && array_key_exists( $attributes['attribute'], $refInfo ) )
                {
                    $refArray[$attributes['value']] = $refInfo[$attributes['attribute']];
                }
            }
            $this->addReference( $refArray );

            $this->writeMessage( "\t\tDone: " . $refInfo['name'], 'notice' );

            $childs = $objectNode->getElementsByTagName( 'Childs' )->item( 0 );
            if ( $childs )
            {
                $this->proccessCreateContent( $childs, $refInfo['node_id'] );
            }
            unset( $objectInformation );
        }
    }

    function createContentObject( $objectInformation )
    {
        $this->writeMessage( "\tCreating object [" . $objectInformation['remoteID']."] (" . $objectInformation['classID'] . ").", "notice", "cyan" );

        $db = eZDB::instance();
        $contentObjectVersion = false;
        if ( $objectInformation['ownerID'] )
        {
            $userID = $objectInformation['ownerID'];
        }
        else
        {
            $user = eZUser::currentUser();
            $userID = $user->attribute( 'contentobject_id' );
        }
        if ( $objectInformation['remoteID'] )
        {
            $contentObject = eZContentObject::fetchByRemoteId( $objectInformation['remoteID'] );
        }
        elseif ( $objectInformation['objectID'] )
        {
            $contentObject = eZContentObject::fetch( $objectInformation['objectID'] );
        }

        if (  $contentObject )
        {
            if ($objectInformation['edit_mode'] == self::EDIT_MODE_SKIP)
            {
                $this->writeMessage( "\t[".$objectInformation['remoteID']."] Object exists: skipping", "notice", "blue" );
                $newNodeArray = eZContentObjectTreeNode::fetchByContentObjectID( $contentObject->attribute( 'id' ) );
                $contentObjectVersion = $contentObject->currentVersion();
                $refArray = false;
                if ( $newNodeArray && count($newNodeArray) >= 1 )
                {
                    $newNode = $newNodeArray[0];
                    if ( $newNode )
                    {
                        $refArray = array( "node_id"   => $newNode->attribute( 'node_id' ),
                                           "name"      => $contentObjectVersion->attribute( 'name' ),
                                           "object_id" => $contentObject->attribute( 'id' ) );

                        if( $objectInformation['priority'] )
                        {
                            $this->updateNodePriority( $refArray['node_id'], $objectInformation['priority'] );
                        }
                    }
                }
                unset($contentObjectVersion);
                unset($contentObject);
                return $refArray;
            }
            elseif ($objectInformation['edit_mode'] == self::EDIT_MODE_NEWVERSION)
            {
                $contentObjectVersion = $contentObject->createNewVersion();
            }
            elseif ($objectInformation['edit_mode'] == self::EDIT_MODE_NEWOBJECT)
            {
                unset($contentObject);
            }
            elseif ($objectInformation['edit_mode'] == self::EDIT_MODE_OVERWRITE)
            {
                $contentObjectVersion = $contentObject->currentVersion();
            }
            else
            {
                $this->writeMessage( "\t[".$objectInformation['remoteID']."] No valid edit mode given!", "error" );
            }
        }

        if ( !$contentObjectVersion )
        {
            if ( is_numeric( $objectInformation['classID'] ) )
            {
                $contentClass = eZContentClass::fetch( $objectInformation['classID'] );
            }
            elseif ( is_string( $objectInformation['classID'] ) && $objectInformation['classID'] != "" )
            {
                $contentClass = eZContentClass::fetchByIdentifier( $objectInformation['classID'] );
            }
            else
            {
                $this->writeMessage( "\tNo class defined. Using class article.", 'warning' );
                $contentClass = eZContentClass::fetchByIdentifier( 'article' );
            }
            if ( !$contentClass )
            {
                $this->writeMessage( "\tCannot instantiate class '". $objectInformation['classID'] ."'." , 'error' );
                return false;
            }
            $contentObject = $contentClass->instantiate( $userID );
            
            if ($objectInformation['edit_mode'] == self::EDIT_MODE_NEWOBJECT)
            {
                $origRemoteId = $objectInformation['remoteID'];
                $counter = 1;
                do 
                {
                    $remoteiD = $origRemoteId . "_" . $counter++;
                    $tmp = eZContentObject::fetchByRemoteId($remoteiD);
                    if(!$tmp)
                    {
                        $objectInformation['remoteID'] = $remoteiD;
                        unset($tmp);
                        break;
                    } 
                } while(true);
            }
            $contentObject->setAttribute( 'remote_id',  $objectInformation['remoteID'] );
            
            if ( $contentObject )
            {
                $contentObjectVersion = $contentObject->currentVersion();
            }
        }
        if ( $contentObjectVersion )
        {
            $db->begin();
            $versionNumber  = $contentObjectVersion->attribute( 'version' );
            $sortField = intval( eZContentObjectTreeNode::sortFieldID( $objectInformation['sort_field'] ) );
            $sortOrder = strtolower( $objectInformation['sort_order'] ) == 'desc' ? eZContentObjectTreeNode::SORT_ORDER_DESC : eZContentObjectTreeNode::SORT_ORDER_ASC;

            $this->writeMessage( "\t\tSetting location to node [" . $objectInformation['parentNode'] . "].", "notice" );
            $nodeAssignment = eZNodeAssignment::create(
                    array(  'contentobject_id'      => $contentObject->attribute( 'id' ),
                            'contentobject_version' => $versionNumber,
                            'parent_node'           => $objectInformation['parentNode'],
                            'is_main'               => 1,
                            'sort_field'			=> $sortField,
                            'sort_order'			=> $sortOrder,
                            )
            );
            $nodeAssignment->store();
            
            if ( isset($objectInformation['locations']) && count($objectInformation['locations']) > 0 )
            {
                foreach ( $objectInformation['locations'] as $location )
                {
                    $this->writeMessage( "\t\tSetting location to node [$location].", "notice" );
                    $nodeAssignment = eZNodeAssignment::create(
                            array(  'contentobject_id'      => $contentObject->attribute( 'id' ),
                                    'contentobject_version' => $versionNumber,
                                    'parent_node'           => $location,
                                    'is_main'               => 0,
                                    'sort_field'			=> $sortField,
                                    'sort_order'			=> $sortOrder,
                                    )
                    );
                    $nodeAssignment->store();
                }
            }
            
            
            $dataMap = $contentObjectVersion->dataMap();
            foreach ( $objectInformation['attributes'] as $attributeName => $attributesContent )
            {
                $editMode = self::ATTR_MODE_CHANGE;
                
                if ( isset($attributesContent['editMode']) )
                {
                    if ($attributesContent['editMode'] == "remove")
                    {
                        $editMode = self::ATTR_MODE_REMOVE;
                    }
                    elseif ($attributesContent['editMode'] == "skip")
                    {
                        $editMode = self::ATTR_MODE_SKIP;
                    }
                    elseif ($attributesContent['editMode'] == "change")
                    {
                        $editMode = self::ATTR_MODE_CHANGE;
                    }
                }

                if ( array_key_exists( $attributeName, $dataMap ) )
                {
                    $attribute = $dataMap[$attributeName];
                    $classAttributeID = $attribute->attribute( 'contentclassattribute_id' );
                    $dataType = $attribute->attribute( 'data_type_string' );

                    $this->writeMessage( "\t\tSetting attribute [$attributeName].", "notice" );
                    $this->writeMessage( "\t\t\tEdit for attribute is $editMode.", "debug" );
                    if ($attribute->attribute('has_content') && $editMode == self::ATTR_MODE_SKIP)
                    {
                        $this->writeMessage( "\t\t\tskipping!.", "notice" );
                        continue;                   
                    }
                    elseif ($editMode == self::ATTR_MODE_REMOVE)
                    {
                        $this->writeMessage( "\t\t\tremoving content!.", "notice" );
                        
                        switch ( $dataType )
                        {
                            case 'ezimage':
                            {
                                $content = $attribute->content();
                                if ( $content )
                                {
                                    $content->removeAliases( $attribute );
                                    $content->store( $attribute );
                                }
                            } break;
                            default:
                            {
                                $attribute->fromString("");
                            }
                        };
                        $attribute->store();
                        continue;                   
                    }
                    switch ( $dataType )
                    {
                        case 'ezstring':
                        case 'eztext':
                        case 'ezselection':
                        case 'ezemail':
                        {
                            if ( array_key_exists( 'parseReferences', $attributesContent ) && $attributesContent['parseReferences'] == "true" )
                            {
                                $attribute->setAttribute( 'data_text', $this->parseAndReplaceStringReferences( $attributesContent['content'] ) );
                            }
                            else
                            {
                                $attribute->setAttribute( 'data_text', $attributesContent['content'] );
                            }
                        } break;

                        case 'ezboolean':
                        case 'ezinteger':
                        {
                            $attribute->setAttribute( 'data_int', (int)$attributesContent['content'] );
                        } break;

                        case 'ezxmltext':
                        {
                            if ( array_key_exists( 'parseReferences', $attributesContent ) && $attributesContent['parseReferences'] == "true" )
                            {
                                $attributesContent['content'] = $this->parseAndReplaceStringReferences( $attributesContent['content'] );
                            }
                            if ( array_key_exists( 'htmlDecode', $attributesContent ) && $attributesContent['htmlDecode'] == "true" )
                            {
                                $content = html_entity_decode( $attributesContent['content'] );
                            }
                            else
                            {
                                $content = $attributesContent['content'];
                            }

                            if( array_key_exists( 'fullxml', $attributesContent ) && $attributesContent['fullxml'] == "true" )
                            {
                                $xml = $content;
                            }
                            else
                            {
                                $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n".
                                        '<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/"'."\n".
                                        '         xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/"'."\n".
                                        '         xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">'."\n".
                                        '  <section>'."\n";
                                $xml .= '    <paragraph>' . $content . "</paragraph>\n";
                                $xml .= "  </section>\n</section>\n";
                            }

                            $attribute->setAttribute( 'data_text', $xml );
                        } break;

                        case 'ezprice':
                        case 'ezfloat':
                        {
                            $attribute->setAttribute( 'data_float', (float)$attributesContent['content'] );
                        } break;
                        case 'ezimage':
                        {
                            $imagePath = $this->setting( 'dataSource' ) . '/' . $attributesContent['src'];
                            $imageName = $attributesContent['title'];
                            $path = realpath( $imagePath );
                            if ( file_exists( $path ) )
                            {
                                $content = $attribute->content();
                                $content->initializeFromFile( $path, $imageName, basename( $attributesContent['src'] ) );
                                $content->store( $attribute );
                            }
                            else
                            {
                                $this->writeMessage( "\tFile " . $path . " not found.", 'warning' );
                            }
                        } break;
                        case 'ezobjectrelation':
                        {
                            $relationContent = trim( $attributesContent['content'] );
                            if ( $relationContent != '' )
                            {
                                $objectID = $this->getReferenceID( $attributesContent['content'] );
                                if ( $objectID )
                                {
                                    $attribute->setAttribute( 'data_int', $objectID );
                                    eZContentObject::fetch( $objectID )->addContentObjectRelation( $objectID, $versionNumber, $contentObject->attribute( 'id' ), $attribute->attribute( 'contentclassattribute_id' ),    eZContentObject::RELATION_ATTRIBUTE );
                                }
                                else
                                {
                                    $this->writeMessage( "\tReference " . $attributesContent['content'] . " not set.", 'warning' );
                                }
                            }
                        } break;
                        case 'ezurl':
                        {
                            $url = '';
                            $title = '';
                            if (  array_key_exists( 'url', $attributesContent ) )
                                $url   = $attributesContent['url'];
                            if (  array_key_exists( 'title', $attributesContent ) )
                                $title   = $attributesContent['title'];

                            if ( array_key_exists( 'parseReferences', $attributesContent ) && $attributesContent['parseReferences'] == "true" )
                            {
                                $title = $this->parseAndReplaceStringReferences( $title );
                                $url   = $this->parseAndReplaceStringReferences( $url );
                            }

                            $attribute->setAttribute( 'data_text', $title );
                            $attribute->setContent( $url );
                        } break;
                        case 'ezuser':
                        {
                            $login    = '';
                            $email    = '';
                            $password = '';
                            if (  array_key_exists( 'login', $attributesContent ) )
                                $login    = $attributesContent['login'];
                            if (  array_key_exists( 'email', $attributesContent ) )
                                $email    = $attributesContent['email'];
                            if (  array_key_exists( 'password', $attributesContent ) )
                                $password = $attributesContent['password'];

                            $contentObjectID = $attribute->attribute( "contentobject_id" );

                            $user =& $attribute->content();
                            if ( $user === null )
                            {
                                $user = eZUser::create( $contentObjectID );
                            }

                            $ini =& eZINI::instance();
                            $generatePasswordIfEmpty = $ini->variable( "UserSettings", "GeneratePasswordIfEmpty" );
                            if (  $password == "" )
                            {
                                if ( $generatePasswordIfEmpty == 'true' )
                                {
                                    $passwordLength = $ini->variable( "UserSettings", "GeneratePasswordLength" );
                                    $password = $user->createPassword( $passwordLength );
                                }
                                else
                                {
                                    $password = null;
                                }
                            }

                            if ( $password == "_ezpassword" )
                            {
                                $password = false;
                                $passwordConfirm = false;
                            }

                            $user->setInformation( $contentObjectID, $login, $email, $password, $password );
                            $attribute->setContent( $user );
                        } break;
                        case 'ezkeyword':
                        {
                            $keyword = new eZKeyword();
                            $keyword->initializeKeyword( $attributesContent['content'] );
                            $attribute->setContent( $keyword );
                        } break;
                        case 'ezmatrix':
                        {
                            if ( is_array( $attributesContent ) )
                            {
                                $matrix = $attribute->attribute( 'content' );
                                $cells = array();
                                $matrix->Matrix['rows']['sequential'] = array();
                                $matrix->NumRows = 0;
                                foreach( $attributesContent as $key => $value )
                                {
                                    $cells = array_merge( $cells, $value );
                                    $newRow['columns'] = $value;
                                    $newRow['identifier'] =  'row_' . ( $matrix->NumRows + 1 );
                                    $newRow['name'] = 'Row_' . ( $matrix->NumRows + 1 );
                                    $matrix->NumRows++;
                                    $matrix->Matrix['rows']['sequential'][] = $newRow;
                                }
                                $matrix->Cells = $cells;
                                $attribute->setAttribute( 'data_text', $matrix->xmlString() );
                                $matrix->decodeXML( $attribute->attribute( 'data_text' ) );
                                $attribute->setContent( $matrix );
                            }
                        } break;

                        case 'ezobjectrelationlist':
                        {
                            $relationContent = explode( ',', $attributesContent['content'] );
                            if ( count( $relationContent ) )
                            {
                                $objectIDs = array();
                                foreach( $relationContent as $relation )
                                {
                                    $objectIDs[] = $this->getReferenceID( trim ( $relation ) );
                                }
                                $attribute->fromString( implode( '-', $objectIDs ) );
                            }
                            else
                            {
                                eZDebug::writeWarning( $attributesContent['content'], "No relation declared" );
                            }
                        } break;


                        case 'ezauthor':
                        case 'ezbinaryfile':
                        case 'ezcountry':
                        case 'ezdate':
                        case 'ezdatetime':
                        case 'ezenum':
                        case 'ezidentifier':
                        case 'ezinisetting':
                        case 'ezisbn':
                        case 'ezmedia':
                        case 'ezmultioption':
                        case 'ezmultiprice':
                        case 'ezoption':
                        case 'ezpackage':
                        case 'ezproductcategory':
                        case 'ezrangeoption':
                        case 'ezsubtreesubscription':
                        case 'eztime':
                        default:
                        {
                            try
                            {
                                $attribute->fromString($attributesContent['content']);
                            }
                            catch ( Exception $e )
                            {
                                $this->writeMessage( "\tDatatype " . $dataType . " fromString function rejected value " . $attributesContent['content'], 'warning' );
                            }
                        } break;

                    }
                    $attribute->store();
                }
                else
                {
                    $this->writeMessage( "\t\tAttribute [$attributeName] not found!" , 'error' );
                
                }
                $this->writeMessage( "\t\t\tAttribute [$attributeName] set sucessfully.", "debug" );
            }
            if ( isset($objectInformation['sectionID']) && $objectInformation['sectionID'] != '' && $objectInformation['sectionID'] != 0 )
            {
                $contentObject->setAttribute( 'section_id',  $objectInformation['sectionID'] );
            }

            if ( isset($objectInformation['creatorID']) && $objectInformation['creatorID'] != '' && $objectInformation['creatorID'] != 0 )
            {
                $contentObjectVersion->setAttribute( 'creator_id',  $objectInformation['creatorID'] );
            }

            if ( isset($objectInformation['ownerID']) && $objectInformation['ownerID'] != '' && $objectInformation['ownerID'] != 0 )
            {
                $contentObject->setAttribute( 'owner_id',  $objectInformation['ownerID'] );
            }

            if( isset( $objectInformation['relations'] ) && is_array( $objectInformation['relations'] ) )
            {
                foreach( $objectInformation['relations'] as $toObjectID )
                {
                    $this->writeMessage( "\t\tSetting reference to object [" . $toObjectID . "].", "notice" );
                    $contentObject->addContentObjectRelation( $toObjectID );
                }
            }

            $contentObjectVersion->store();
            $contentObject->store();
            $db->commit();


            try
            {
                $this->writeMessage( "\t\tPublishing object...", "error", "gray" );
                $behaviour = new ezpContentPublishingBehaviour();
                $behaviour->isTemporary = true;
                $behaviour->disableAsynchronousPublishing = false;
                ezpContentPublishingBehaviour::setBehaviour( $behaviour );

                $operationHandlerResult = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $contentObject->attribute( 'id' ), 'version'   => $versionNumber ) );

            }
            catch (Exception $e)
            {
                $operationHandlerResult = -1;
                $this->writeMessage( "\t\tPublishing object failed!", "error" );
            }

            if ($operationHandlerResult == -1)
            {
                $this->writeMessage( "\t\tPublishing object failed!", "error" );
            }
            if (is_array($operationHandlerResult) &&  array_key_exists('status', $operationHandlerResult))
            {
                if($operationHandlerResult['status'] == 3)
                {
                    $this->writeMessage( "\t\tObject deferred to cron", "warning" );
                }
                elseif($operationHandlerResult['status'] == 1)
                {
                    $this->writeMessage( "\t\tObject published successfully!", "success" );
                    $this->writeMessage( "\t\t\tObject ID:    " . $contentObject->attribute('id') , "success" );
                    $this->writeMessage( "\t\t\tMain Node ID: " . $contentObject->attribute('main_node_id') , "success" );
                    
                }
                else
                {
                    $this->writeMessage( "\t\tPublishing return unknown status!", "error" );
                }
            }
            else
            {
                $this->writeMessage( "\t\tPublishing return unknown result!", "error" );
            }

            $newNodeArray = eZContentObjectTreeNode::fetchByContentObjectID( $contentObject->attribute( 'id' ) );
            $refArray = false;
            if ( $newNodeArray && count($newNodeArray) >= 1 )
            {
                $newNode = $newNodeArray[0];
                if ( $newNode )
                {
                    $refArray = array( "node_id"   => $newNode->attribute( 'node_id' ),
                                       "name"      => $contentObjectVersion->attribute( 'name' ),
                                       "object_id" => $contentObject->attribute( 'id' ) );

                    if( $objectInformation['priority'] )
                    {
                        $this->updateNodePriority( $refArray['node_id'], $objectInformation['priority'] );
                    }
                }
            }
            unset($contentObjectVersion);
            unset($contentObject);
            return $refArray;
        }
        return false;
    }

    /**
     * Updates the priority for node $node_id to $priority
     * @param int $node_id
     * @param int $priority
     * @since 1.2.1
     */
    protected function updateNodePriority( $node_id, $priority )
    {
        $node = eZContentObjectTreeNode::fetch( $node_id );
        if( $node )
        {
            $node->setAttribute( 'priority', $priority );
            $node->store();
        }
    }
    
    var $EditModeList;

}

?>
