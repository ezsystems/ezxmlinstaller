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


class eZCreateClass extends eZXMLInstallerHandler
{
    /**
     * Tells execute algorithm to adjust current class's attributes placement if needed
     * @since 1.2.1
     * @var boolean
     */
    private $adjustAttributesPlacement = false;

    function __construct( )
    {
    }

    function execute( $xml )
    {
        $classList = $xml->getElementsByTagName( 'ContentClass' );
        $refArray = array();
        $availableLanguageList = eZContentLanguage::fetchLocaleList();
        foreach ( $classList as $class )
        {
            $this->adjustAttributesPlacement = false;

            $user = eZUser::currentUser();
            $userID = $user->attribute( 'contentobject_id' );

            $classIdentifier        = $class->getAttribute( 'identifier' );
            $classRemoteID          = $class->getAttribute( 'remoteID' );
            $classObjectNamePattern = $class->getAttribute( 'objectNamePattern' );
            $classExistAction       = $class->getAttribute( 'classExistAction' );
            $referenceID            = $class->getAttribute( 'referenceID' );

            $this->writeMessage( "\tClass '$classIdentifier' will be updated.", 'notice' );

            $classURLAliasPattern   = $class->getAttribute( 'urlAliasPattern' ) ? $class->getAttribute( 'urlAliasPattern' ) : null;

            $classIsContainer       = $class->getAttribute( 'isContainer' );
            if ( $classIsContainer !== false )
                $classIsContainer = $classIsContainer == 'true' ? 1 : 0;

            $classGroupsNode        = $class->getElementsByTagName( 'Groups' )->item( 0 );
            $classAttributesNode    = $class->getElementsByTagName( 'Attributes' )->item( 0 );

            $nameList = array();
            $nameListObject = $class->getElementsByTagName( 'Names' )->item( 0 );
            if ( $nameListObject && $nameListObject->parentNode === $class && $nameListObject->hasAttributes() )
            {
                $attributes = $nameListObject->attributes;
                if ( !is_null($attributes) )
                {
                    foreach ( $attributes as $index=>$attr )
                    {
                        if ( in_array( $attr->name, $availableLanguageList ) )
                        {
                            $nameList[$attr->name] = $attr->value;
                        }
                    }
                }
            }

            if( !empty( $nameList ) )
            {
                $classNameList = new eZContentClassNameList( serialize($nameList) );
                $classNameList->validate( );
            }
            else
            {
                $classNameList = null;
            }

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
                        foreach ( $nameList as $lang => $name )
                        {
                            if ( in_array( $lang, $availableLanguageList ) )
                            {
                                $class->setName( $name, $lang );
                            }
                        }
                        $class->setAttribute( 'contentobject_name', $classObjectNamePattern );
                        $class->setAttribute( 'identifier', $classIdentifier );
                        $class->setAttribute( 'is_container', $classIsContainer );
                        $class->setAttribute( 'url_alias_name', $classURLAliasPattern );

                        $class->store();
                        $class->removeAttributes();
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
                        foreach ( $nameList as $lang => $name )
                        {
                            if ( in_array( $lang, $availableLanguageList ) )
                            {
                                $class->setName( $name, $lang );
                            }
                        }
                        $class->setAttribute( 'contentobject_name', $classObjectNamePattern );
                        $class->setAttribute( 'identifier', $classIdentifier );
                        $class->setAttribute( 'is_container', $classIsContainer );
                        $class->setAttribute( 'url_alias_name', $classURLAliasPattern );
                        $class->store();
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
                                                array( 'version' => 1,
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
                $attributes = $class->fetchAttributes();
                $class->storeDefined( $attributes );
                $classID = $class->attribute( 'id' );
                $this->writeMessage( "\t\tClass '$classIdentifier' will be newly created.", 'notice' );
            }

            // create class attributes
            $classAttributeList = $classAttributesNode->getElementsByTagName( 'Attribute' );
            $classDataMap = $class->attribute( 'data_map' );
            $updateAttributeList = array();
            if( $classDataMap == NULL ) $classDataMap = array();
            foreach ( $classAttributeList as $classAttributeNode )
            {
                $attributeDatatype = $classAttributeNode->getAttribute( 'datatype' );
                $attributeIsRequired = strtolower( $classAttributeNode->getAttribute( 'required' ) ) == 'true';
                $attributeIsSearchable = strtolower( $classAttributeNode->getAttribute( 'searchable' ) ) == 'true';
                $attributeIsInformationCollector = strtolower( $classAttributeNode->getAttribute( 'informationCollector' ) ) == 'true';
                $attributeIsTranslatable = (strtolower( $classAttributeNode->getAttribute( 'translatable' ) ) == 'false') ? 0 : 1;
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

                $params = array();
                $params['identifier']               = $attributeIdentifier;
                $params['name_list']                = $classAttributeNameList;
                $params['data_type_string']         = $attributeDatatype;
                $params['default_value']            = '';
                $params['can_translate']            = $attributeIsTranslatable;
                $params['is_required']              = $attributeIsRequired;
                $params['is_searchable']            = $attributeIsSearchable;
                $params['content']                  = '';
                $params['placement']                = $attributePlacement;
                $params['is_information_collector'] = $attributeIsInformationCollector;
                $params['datatype-parameter']       = $this->parseAndReplaceNodeStringReferences( $attributeDatatypeParameterNode );
                $params['attribute-node']           = $classAttributeNode;

                if ( !array_key_exists( $attributeIdentifier, $classDataMap ) )
                {
                    $this->writeMessage( "\t\tClass '$classIdentifier' will get new Attribute '$attributeIdentifier'.", 'notice' );
                    $updateAttributeList[] = $this->addClassAttribute( $class, $params );

                }
                else
                {
                    $this->writeMessage( "\t\tClass '$classIdentifier' will get updated Attribute '$attributeIdentifier'.", 'notice' );
                    $this->updateClassAttribute( $class, $params );
                }
            }

            if( $this->adjustAttributesPlacement )
            {
                //once every attribute has been processed, we may reset placement
                $this->writeMessage( "\t\tAdjusting attributes placement.", 'notice' );
                $this->adjustClassAttributesPlacement($class);
            }

            if ( count( $updateAttributeList ) )
            {
                $this->writeMessage( "\t\tUpdating content object attributes.", 'notice' );
                $classID = $class->attribute( 'id' );
                // update object attributes
                $objects = eZContentObject::fetchSameClassList( $classID, false );
                foreach( $objects as $objectID )
                {
                    $object = eZContentObject::fetch( $objectID['id'] );
                    if ( $object )
                    {
                        $contentobjectID = $object->attribute( 'id' );
                        $objectVersions = $object->versions();
                        foreach( $objectVersions as $objectVersion )
                        {
                            $translations = $objectVersion->translations( false );
                            $version = $objectVersion->attribute( 'version' );
                            foreach( $translations as $translation )
                            {
                                foreach ( $updateAttributeList as $classAttributeID )
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
                    unset( $object );
                }
            }

            if( $classNameList ) $classNameList->store( $class );


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
        $classAttributeNameList = $params['name_list'];

        $datatype = $params['data_type_string'];
        $defaultValue = isset( $params['default_value'] ) ? $params['default_value'] : false;
        $canTranslate = isset( $params['can_translate']   ) ? $params['can_translate'] : 0;
        $isRequired   = isset( $params['is_required']   ) ? $params['is_required'] : 0;
        $isSearchable = isset( $params['is_searchable'] ) ? $params['is_searchable'] : 0;
        $isCollector  = isset( $params['is_information_collector'] ) ? $params['is_information_collector'] : false;
        $attrContent  = isset( $params['content'] )       ? $params['content'] : false;

        $attrCreateInfo = array( 'identifier' => $classAttributeIdentifier,
                                    'serialized_name_list' => $classAttributeNameList->serializeNames(),
                                    'can_translate' => $canTranslate,
                                    'is_required' => $isRequired,
                                    'is_searchable' => $isSearchable,
                                    'is_information_collector' => $isCollector );
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
        $placement = $params['placement'] ? intval( $params['placement'] ) : count( $attributes );
        $newAttribute->setAttribute( 'placement',  $placement);

        $this->adjustAttributesPlacement = true;

        $newAttribute->storeDefined();
        $classAttributeID = $newAttribute->attribute( 'id' );
        return $classAttributeID;
    }

    function updateClassAttribute( $class, $params )
    {
        $classID = $class->attribute( 'id' );

        $classAttributeIdentifier = $params['identifier'];
        $classAttributeNameList = $params['name_list'];

        $classAttribute = $class->fetchAttributeByIdentifier( $classAttributeIdentifier );

        if ( $classAttribute->attribute( 'data_type_string' ) != $params['data_type_string'] )
        {
            $this->writeMessage( "\t\tDatatype conversion not possible: '" . $params['data_type_string'] . "'", 'error' );
            return false;
        }

        $classAttribute->NameList = $classAttributeNameList;
        $classAttribute->setAttribute( 'data_type_string',  $params['data_type_string']  );
        $classAttribute->setAttribute( 'identifier', $classAttributeIdentifier  );
        $classAttribute->setAttribute( 'is_required', $params['is_required']  );
        $classAttribute->setAttribute( 'is_searchable', $params['is_searchable']  );
        $classAttribute->setAttribute( 'can_translate', $params['can_translate']  );
        $classAttribute->setAttribute( 'is_information_collector', $params['is_information_collector']  );

        if( $params['placement'] )
        {
            $classAttribute->setAttribute( 'placement', $params['placement'] );
            $this->adjustAttributesPlacement = true;
        }

        $dataType = $classAttribute->dataType();
        $dataType->unserializeContentClassAttribute( $classAttribute, $params['attribute-node'], $params['datatype-parameter'] );
        $classAttribute->sync();
        $classAttribute->store();

    }

    /**
     * Updates placement for each attribute in a class instance
     *
     * @since 1.2.1
     * @param eZContentClass $class class instance to update
     */
    protected function adjustClassAttributesPlacement(eZContentClass $class)
    {
        $attributes = $class->fetchAttributes();
        $class->adjustAttributePlacements( $attributes );
        foreach( $attributes as $attribute )
        {
            $attribute->store();
        }
    }
}

?>
