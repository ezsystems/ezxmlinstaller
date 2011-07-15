<?php
//
// Created on: <2007-12-28 06:09:00 dis>
//
// SOFTWARE NAME: eZ XML Installer extension for eZ Publish
// SOFTWARE RELEASE: 0.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2010 eZ Systems AS
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

include_once('extension/ezxmlinstaller/classes/ezxmlinstallerhandler.php');

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
    const PRIORITY_MODE_NONE = 'none';
    const PRIORITY_MODE_FIXED = 'fixed';
    const PRIORITY_MODE_AUTO = 'auto';

    function eZCreateContent( )
    {
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
                $this->writeMessage( "\tNo parent node defined. Using node 2.", 'warning' );
                $parentNodeID = 2;
            }
        }

        $priorityMode = $xmlNode->hasAttribute( 'priorityMode' ) ? $xmlNode->getAttribute( 'priorityMode' ) : self::PRIORITY_MODE_NONE;
        $priorityCounter = 0;

        $objectList = $xmlNode->childNodes; //getElementsByTagName( 'ContentObject' );
        foreach ( $objectList as $objectNode )
        {
            if ( $objectNode->nodeName != 'ContentObject' )
                continue;
            $objectInformation = array();
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
                        $this->writeMessage( 'Warning : No target defined for object relation' );
                    }
                }
            }

            $refInfo = $this->createContentObject( $objectInformation );

            if( $refInfo )
            {
                $priorityCounter++;
            }

            // $referenceList = $objectNode->getElementsByTagName( 'SetReference' );
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

            $this->writeMessage( "\tCreated: " . $refInfo['name'], 'notice' );

            $childs = $objectNode->getElementsByTagName( 'Childs' )->item( 0 );
            if ( $childs )
            {
                $this->proccessCreateContent( $childs, $refInfo['node_id'] );
            }
            unset( $objectInformation );
        }
//         $objectList = $xmlNode->getElementsByTagName( 'ContentObject' );

    }

    function createContentObject( $objectInformation )
    {
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
            if (  $contentObject )
            {
                $this->writeMessage( "\t[".$objectInformation['remoteID']."] Object exists: " . $contentObject->attribute("name") . ". Creating new version.", 'notice' );
                $contentObjectVersion = $contentObject->createNewVersion();
            }
        }
        elseif ( $objectInformation['objectID'] )
        {
            $contentObject = eZContentObject::fetch( $objectInformation['objectID'] );
            if (  $contentObject )
            {
                $this->writeMessage( "\t[".$objectInformation['remoteID']."] Object exists: " . $contentObject->attribute("name") . ". Creating new version.", 'notice' );
                $contentObjectVersion = $contentObject->createNewVersion();
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
            $dataMap = $contentObjectVersion->dataMap();
            foreach ( $objectInformation['attributes'] as $attributeName => $attributesContent )
            {
                if ( array_key_exists( $attributeName, $dataMap ) )
                {
                    $attribute = $dataMap[$attributeName];
                    $classAttributeID = $attribute->attribute( 'contentclassattribute_id' );
                    $dataType = $attribute->attribute( 'data_type_string' );
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
                            $imagePath = $this->setting( 'data_source' ) . '/' . $attributesContent['src'];
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
                    $contentObject->addContentObjectRelation( $toObjectID );
                }
            }

            $contentObjectVersion->store();
            $contentObject->store();
            $db->commit();

            eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $contentObject->attribute( 'id' ), 'version'   => $versionNumber ) );

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
}

?>
