<?php
include_once('extension/ezxmlinstaller/classes/ezxmlinstallerhandler.php');

class eZCreateClass extends eZXMLInstallerHandler
{

    function eZCreateClass( )
    {
    }

    function execute( $xml )
    {
        $classList = $xml->getElementsByTagName( 'ContentClass' );
        $refArray = array();
        foreach ( $classList as $class )
        {
            $user = eZUser::currentUser();
            $userID = $user->attribute( 'contentobject_id' );

            $classIdentifier        = $class->getAttribute( 'identifier' );
            $classRemoteID          = $class->getAttribute( 'remoteID' );
            $classObjectNamePattern = $class->getAttribute( 'objectNamePattern' );
            $classExistAction       = $class->getAttribute( 'classExistAction' );
            $referenceID            = $class->getAttribute( 'referenceID' );

            $this->writeMessage( "\tClass '$classIdentifier' will be updated.", 'notice' );

            $classURLAliasPattern   = is_object( $class->getAttribute( 'urlAliasPattern' ) ) ? $class->getAttribute( 'urlAliasPattern' ) : null;

            $classIsContainer       = $class->getAttribute( 'isContainer' );
            if ( $classIsContainer !== false )
                $classIsContainer = $classIsContainer == 'true' ? 1 : 0;

            $classGroupsNode        = $class->getElementsByTagName( 'Groups' )->item( 0 );
            $classAttributesNode    = $class->getElementsByTagName( 'Attributes' )->item( 0 );

            $nameListObject = $class->getElementsByTagName( 'Names' )->item( 0 );
            if ( $nameListObject->hasAttributes() )
            {
                if ( $nameListObject->hasAttributes())
                {
                    $attributes = $nameListObject->attributes;
                    if ( !is_null($attributes) )
                    {
                        $nameList = array();
                        foreach ( $attributes as $index=>$attr )
                        {
                            $nameList[$attr->name] = $attr->value;
                        }
                    }
                }
            }


            $classNameList = new eZContentClassNameList( serialize($nameList) );
            $classNameList->validate( );


            $dateTime = time();
            $classCreated = $dateTime;
            $classModified = $dateTime;

            $class = eZContentClass::fetchByRemoteID( $classRemoteID );

            if (!$class)
            {
                $class = eZContentClass::fetchByIdentifier( $classIdentifier );
            }

            if ( $class )
            {
                $className = $class->name();
                switch( $classExistAction )
                {
                    case 'replace':
                    {
                        $this->writeMessage( "\t\tClass '$classIdentifier' will be replaced.", 'notice' );
                        /* TODO: Remove all attributes */
                    } break;
                    case 'new':
                    {
                        unset( $class );
                        $class = false;
                    break;
                    } break;
                    case 'extend':
                    {
                        $this->writeMessage( "\t\tClass '$classIdentifier' will be extended.", 'notice' );
                    } break;
                    case 'skip':
                    default:
                    {
                        continue;
                    } break;
                }
            }
            if (!$class)
            {
                // Try to create a unique class identifier
                $currentClassIdentifier = $classIdentifier;
                $unique = false;
                while( !$unique )
                {
                    $classList = eZContentClass::fetchByIdentifier( $currentClassIdentifier );
                    if ( $classList )
                    {
                        // "increment" class identifier
                        if ( preg_match( '/^(.*)_(\d+)$/', $currentClassIdentifier, $matches ) )
                            $currentClassIdentifier = $matches[1] . '_' . ( $matches[2] + 1 );
                        else
                            $currentClassIdentifier = $currentClassIdentifier . '_1';
                    }
                    else
                        $unique = true;
                    unset( $classList );
                }
                $classIdentifier = $currentClassIdentifier;

                // create class
                $class = eZContentClass::create( $userID,
                                                array( 'version' => 0,
                                                        'serialized_name_list' => $classNameList->serializeNames(),
                                                        'create_lang_if_not_exist' => true,
                                                        'identifier' => $classIdentifier,
                                                        'remote_id' => $classRemoteID,
                                                        'contentobject_name' => $classObjectNamePattern,
                                                        'url_alias_name' => $classURLAliasPattern,
                                                        'is_container' => $classIsContainer,
                                                        'created' => $classCreated,
                                                        'modified' => $classModified ) );
                $class->store();
                $classID = $class->attribute( 'id' );
                $this->writeMessage( "\t\tClass '$classIdentifier' will be newly created.", 'notice' );
            }

            // create class attributes
            $classAttributeList = $classAttributesNode->getElementsByTagName( 'Attribute' );
            $classDataMap = $class->attribute( 'data_map' );
            if( $classDataMap == NULL ) $classDataMap = array();
            foreach ( $classAttributeList as $classAttributeNode )
            {
                $attributeDatatype = $classAttributeNode->getAttribute( 'datatype' );
                $attributeIsRequired = strtolower( $classAttributeNode->getAttribute( 'required' ) ) == 'true';
                $attributeIsSearchable = strtolower( $classAttributeNode->getAttribute( 'searchable' ) ) == 'true';
                $attributeIsInformationCollector = strtolower( $classAttributeNode->getAttribute( 'informationCollector' ) ) == 'true';
                $attributeIsTranslatable = strtolower( $classAttributeNode->getAttribute( 'translatable' ) ) == 'true';
                $attributeIdentifier = $classAttributeNode->getAttribute( 'identifier' );
                $attributePlacement = $classAttributeNode->getAttribute( 'placement' );

                $attributeNameListObject = $classAttributeNode->getElementsByTagName( 'Names' )->item( 0 );
                if ( $attributeNameListObject->hasAttributes() )
                {
                    if ( $attributeNameListObject->hasAttributes())
                    {
                        $attributes = $attributeNameListObject->attributes;
                        if ( !is_null($attributes) )
                        {
                            $attributeNameList = array();
                            foreach ( $attributes as $index=>$attr )
                            {
                                $attributeNameList[$attr->name] = $attr->value;
                            }
                        }
                    }
                }

                $classAttributeNameList = new eZContentClassNameList( serialize($attributeNameList) );
                $classAttributeNameList->validate( );

                $attributeDatatypeParameterNode = $classAttributeNode->getElementsByTagName( 'DatatypeParameters' )->item( 0 );

                $classAttribute = $class->fetchAttributeByIdentifier( $attributeIdentifier );
                if ( !array_key_exists( $attributeIdentifier, $classDataMap ) )
                {
                    $this->writeMessage( "\t\tClass '$classIdentifier' will get new Attribute '$attributeIdentifier'.", 'notice' );

                    $params = array();
                    $params['identifier']               = $attributeIdentifier;
                    $params['serialized_name_list']     = $classAttributeNameList->serializeNames();
                    $params['data_type_string']         = $attributeDatatype;
                    $params['default_value']            = '';
                    $params['can_translate']            = '';
                    $params['is_required']              = $attributeIsRequired;
                    $params['is_searchable']            = $attributeIsSearchable;
                    $params['content']                  = '';
                    $params['placement']                = $attributePlacement;
                    $params['is_information_collector'] = $attributeIsInformationCollector;
                    $params['datatype-parameter']       = $attributeDatatypeParameterNode;
                    $params['attribute-node']           = $classAttributeNode;

                    $this->addClassAttribute( $class, $params );
                }
                else
                {
                    /* TODO update! */
                }
            }

            $classNameList->store( $class );


            // add class to a class group
            $classGroupsList = $classGroupsNode->getElementsByTagName( 'Group' );
            foreach ( $classGroupsList as $classGroupNode )
            {
                $classGroupName = $classGroupNode->getAttribute( 'name' );
                $classGroup = eZContentClassGroup::fetchByName( $classGroupName );
                if ( !$classGroup )
                {
                    $classGroup = eZContentClassGroup::create();
                    $classGroup->setAttribute( 'name', $classGroupName );
                    $classGroup->store();
                }
                $classGroup->appendClass( $class );
            }

            if ( $referenceID )
            {
                $refArray[$referenceID] = $class->attribute( 'id' );
            }
        }
        $this->addReference( $refArray );
        eZContentCacheManager::clearAllContentCache();
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'CreateClass', 'Info' => 'create content class' );
    }


    /*!
     Remove attribute from the content class
     Params:
        'class_id'              - ID of content class to remove attribute from;
        'attribute_identifier'  - attibute identifier to remove;
    */
//     function removeClassAttribute( $params )
//     {
//         //include_once( 'kernel/classes/ezcontentclassattribute.php' );
// 
//         $contentClassID = $params['class_id'];
//         $classAttributeIdentifier = $params['attribute_identifier'];
// 
//         // get attributes of 'temporary' version as well
//         $classAttributeList = eZContentClassAttribute::fetchFilteredList( array( 'contentclass_id' => $contentClassID,
//                                                                                   'identifier' => $classAttributeIdentifier ),
//                                                                            true );
// 
//         $validation = array();
//         foreach( $classAttributeList as $classAttribute )
//         {
//             $dataType = $classAttribute->dataType();
//             if( $dataType->isClassAttributeRemovable( $classAttribute ) )
//             {
//                 $objectAttributes = eZContentObjectAttribute::fetchSameClassAttributeIDList( $classAttribute->attribute( 'id' ) );
//                 foreach( $objectAttributes as $objectAttribute )
//                 {
//                     $objectAttributeID = $objectAttribute->attribute( 'id' );
//                     $objectAttribute->removeThis( $objectAttributeID );
//                 }
// 
//                 $classAttribute->removeThis();
//             }
//             else
//             {
//                 $removeInfo = $dataType->classAttributeRemovableInformation( $classAttribute );
//                 if( $removeInfo === false )
//                     $removeInfo = "Unknow reason";
// 
//                 $validation[] = array( 'id' => $classAttribute->attribute( 'id' ),
//                                        'identifier' => $classAttribute->attribute( 'identifier' ),
//                                        'reason' => $removeInfo );
//             }
//         }
// 
//         if( count( $validation ) > 0 )
//         {
//             $this->reportError( $validation, 'eZSiteInstaller::removeClassAttribute: Unable to remove eZClassAttribute(s)' );
//         }
// 
//     }

    function addClassAttribute( $class, $params )
    {
        $classID = $class->attribute( 'id' );

        $classAttributeIdentifier = $params['identifier'];
        $classAttributeName = $params['serialized_name_list'];

        $datatype = $params['data_type_string'];
        $defaultValue = isset( $params['default_value'] ) ? $params['default_value'] : false;
        $canTranslate = isset( $params['can_translate'] ) ? $params['can_translate'] : 1;
        $isRequired   = isset( $params['is_required']   ) ? $params['is_required'] : 0;
        $isSearchable = isset( $params['is_searchable'] ) ? $params['is_searchable'] : 0;
        $attrContent  = isset( $params['content'] )       ? $params['content'] : false;

        $attrCreateInfo = array( 'identifier' => $classAttributeIdentifier,
                                    'serialized_name_list' => $classAttributeName,
                                    'can_translate' => $canTranslate,
                                    'is_required' => $isRequired,
                                    'is_searchable' => $isSearchable );
        $newAttribute = eZContentClassAttribute::create( $classID, $datatype, $attrCreateInfo  );

        $dataType = $newAttribute->dataType();
        if ( !$dataType )
        {
            $this->writeMessage( "\t\tUnknown datatype: '$datatype'", 'error' );
            return false;
        }
        $dataType->initializeClassAttribute( $newAttribute );
        $newAttribute->store();
        $dataType->unserializeContentClassAttribute( $newAttribute, $params['attribute-node'], $params['datatype-parameter'] );
        $newAttribute->sync();


        // not all datatype can have 'default_value'. do check here.
        if( $defaultValue !== false  )
        {
            switch( $datatype )
            {
                case 'ezboolean':
                {
                    $newAttribute->setAttribute( 'data_int3', $defaultValue );
                }
                break;

                default:
                    break;
            }
        }

        if( $attrContent )
            $newAttribute->setContent( $attrContent );

        // store attribute, update placement, etc...
        $attributes = $class->fetchAttributes();
        $attributes[] = $newAttribute;

        // remove temporary version
        if ( $newAttribute->attribute( 'id' ) !== null )
        {
            $newAttribute->remove();
        }

        $newAttribute->setAttribute( 'version', eZContentClass::VERSION_STATUS_DEFINED );
        $newAttribute->setAttribute( 'placement', count( $attributes ) );

        $class->adjustAttributePlacements( $attributes );
        foreach( $attributes as $attribute )
        {
            $attribute->storeDefined();
        }

        // update objects
        $classAttributeID = $newAttribute->attribute( 'id' );
        $objects = eZContentObject::fetchSameClassList( $classID );
        foreach( $objects as $object )
        {
            $contentobjectID = $object->attribute( 'id' );
            $objectVersions = $object->versions();
            foreach( $objectVersions as $objectVersion )
            {
                $translations = $objectVersion->translations( false );
                $version = $objectVersion->attribute( 'version' );
                foreach( $translations as $translation )
                {
                    $objectAttribute = eZContentObjectAttribute::create( $classAttributeID, $contentobjectID, $version );
                    $objectAttribute->setAttribute( 'language_code', $translation );
                    $objectAttribute->initialize();
                    $objectAttribute->store();
                    $objectAttribute->postInitialize();
                }
            }
        }
    }
}

?>