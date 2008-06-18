<?php

class eZModifyContent extends eZXMLInstallerHandler
{

    function eZModifyContent( )
    {
    }

    function execute( $xml )
    {
        $this->proccessModifyContent( $xml );
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'ModifyContent', 'Info' => 'modify existing content' );
    }


    function proccessModifyContent( $xmlNode )
    {
        $objectID = $xmlNode->getAttribute( 'objectID' );
        if ( !$objectID )
        {
            $nodeID = $xmlNode->getAttribute( 'nodeID' );
            $contentNode = eZContentObjectTreeNode::fetch( $nodeID );
            if ( $contentNode )
            {
                $objectID = $contentNode->attribute( 'contentobject_id' );
            }
        }
        if ( !$objectID )
        {
            $remoteID = $xmlNode->getAttribute( 'remoteID' );
            $contentObject = eZContentObject::fetchByRemoteID( $nodeID );
            $objectID = $contentObject->attribute( 'id' );
        }

        $contentObject = eZContentObject::fetch( $objectID );
        if ( !$contentObject)
        {
            $this->writeMessage( "\tObject not found: " . $refInfo['name'], 'error' );
            return false;
        }

        $objectInformation = array();
        $objectInformation['parentNode'] = $contentObject->attribute( 'main_parent_node_id' );
        $objectInformation['object']     = $contentObject;
        $objectInformation['classID']    = $this->getReferenceID( $xmlNode->getAttribute( 'contentClass' ) );
        $objectInformation['sectionID']  = $this->getReferenceID( $xmlNode->getAttribute( 'section' ) );
        $objectInformation['ownerID']    = $xmlNode->getAttribute( 'owner' );
        $objectInformation['creatorID']  = $xmlNode->getAttribute( 'creator' );
        $objectInformation['attributes'] = array();


        $attributeObject = $xmlNode->getElementsByTagName( 'Attributes' )->item( 0 );
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

        $refInfo = $this->editContentObject( $objectInformation );

        $referenceList = $xmlNode->childNodes;
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

        $this->writeMessage( "\tModified: " . $refInfo['name'], 'notice' );

        unset( $objectInformation );
    }

    function editContentObject( $objectInformation )
    {
        $db = eZDB::instance();
        $contentObjectVersion = false;
        $contentObject = $objectInformation['object'];

        if ( $objectInformation['ownerID'] )
        {
            $userID = $objectInformation['ownerID'];
        }
        else
        {
            $userID = $contentObject->attribute( 'owner_id' );
        }

        if (  $contentObject )
        {
            $contentObjectVersion = $contentObject->createNewVersion();
        }

        if ( $contentObjectVersion )
        {
            $db->begin();
            $versionNumber  = $contentObjectVersion->attribute( 'version' );

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
                            $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n".
                                    '<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/"'."\n".
                                    '         xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/"'."\n".
                                    '         xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">'."\n".
                                    '  <section>'."\n";
                            $xml .= '    <paragraph>' . $content . "</paragraph>\n";
                            $xml .= "  </section>\n</section>\n";

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
                        case 'ezobjectrelationlist':
                        case 'ezoption':
                        case 'ezpackage':
                        case 'ezproductcategory':
                        case 'ezrangeoption':
                        case 'ezsubtreesubscription':
                        case 'eztime':
                        {
                            $this->writeMessage( "\tDatatype " . $dataType . " not supported yet.", 'warning' );
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

            $contentObjectVersion->store();
            $contentObject->store();
            $db->commit();

            eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $contentObject->attribute( 'id' ), 'version'   => $versionNumber ) );

            // set chosen hidden/invisible attributes for object nodes
            $http          = eZHTTPTool::instance();
            $assignedNodes = $contentObject->assignedNodes( true );
            foreach ( $assignedNodes as $node )
            {
                $nodeID               = $node->attribute( 'node_id' );
                $parentNodeID         = $node->attribute( 'parent_node_id' );

                $db = eZDB::instance();
                $db->begin();
                $parentNode = eZContentObjectTreeNode::fetch( $parentNodeID );
                eZContentObjectTreeNode::updateNodeVisibility( $node, $parentNode, /* $recursive = */ false );
                $db->commit();
                unset( $node, $parentNode );
            }
            unset( $assignedNodes );




            eZContentCacheManager::clearObjectViewCacheIfNeeded( $contentObject->attribute( 'id' ) );

            $newNodeArray = eZContentObjectTreeNode::fetchByContentObjectID( $contentObject->attribute( 'id' ) );
            $refArray = false;
            if ( $newNodeArray && count($newNodeArray) >= 1 )
            {
                $newNode = $newNodeArray[0];
                if ( $newNode )
                {
                    $refArray = array( "node_id"   => $newNode->attribute( 'node_id' ),
                                       "name"      => $contentObject->attribute( 'name' ),
                                       "object_id" => $contentObject->attribute( 'id' ) );
                }
            }
            unset($contentObjectVersion);
            unset($contentObject);
            return $refArray;
        }
        return false;
    }
}

?>