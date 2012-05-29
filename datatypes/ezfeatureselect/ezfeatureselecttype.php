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

require_once( 'kernel/common/i18n.php' );

class eZFeatureSelectType extends eZDataType
{
    const DATA_TYPE_STRING = 'ezfeatureselect';
    const TEMPLATE_LOCATION_FIELD = "data_text1";
    const TEMPLATE_LOCATION_VARIABLE = "_ezfeatureselect_template_location_";

    /*!
     Initializes with a string id and a description.
    */
    function eZFeatureSelectType()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, ezpI18n::tr( 'ezxmlinstaller/datatypes', 'Feature Select', 'Datatype name' ),
                           array( 'serialize_supported' => true,
                                  'object_serialize_map' => array( 'data_text' => 'text' ) ) );
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion == NULL )
        {

            $classAttribute = $contentObjectAttribute->attribute( 'contentclass_attribute' );
            $templateLocation = $classAttribute->attribute( self::TEMPLATE_LOCATION_FIELD );
            $template = 'design:' . $templateLocation;
            $attrContent = array();
            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'xmlinstaller_feature_list', false );

            $content = $tpl->fetch( $template );

            $featureList = false;
            if ( $tpl->variable( "xmlinstaller_feature_list" ) !== false )
            {
                $featureList = $tpl->variable( "xmlinstaller_feature_list" );
            }
            $defaultFeatureList = array();
            if ( $featureList )
            {
                foreach ( $featureList as $key => $feature )
                {
                    if ( array_key_exists( 'default', $feature ) && $feature['default'] )
                    {
                        $defaultFeatureList[] = $key;
                    }
                }
            }
            $dataText = implode( ',', $defaultFeatureList );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
    }

    /*
     Private method, only for using inside this class.
    */
    function validateStringHTTPInput( $data, $contentObjectAttribute, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }


    /*!
     \reimp
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     \reimp
    */
    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches the http post var string input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $variableName = $base . '_ezfeatureselect_list_' . $contentObjectAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $variableName ) )
        {
            $data = $http->postVariable( $variableName );
            $contentObjectAttribute->setAttribute( 'data_text', serialize( $data ) );
        }
        else
        {
            // All features have been deselected
            $contentObjectAttribute->setAttribute( 'data_text', '' );
        }
        return true;
    }

    /*!
     Fetches the http post variables for collected information
    */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_ezfeatureselect_data_text_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $dataText = $http->postVariable( $base . "_ezfeatureselect_data_text_" . $contentObjectAttribute->attribute( "id" ) );
            $collectionAttribute->setAttribute( 'data_text', serialize( $dataText ) );
            return true;
        }
        return false;
    }

    /*!
     Does nothing since it uses the data_text field in the content object attribute.
     See fetchObjectAttributeHTTPInput for the actual storing.
    */
    function storeObjectAttribute( $attribute )
    {
    }

    /*!
     \reimp
     Simple string insertion is supported.
    */
    function isSimpleStringInsertionSupported()
    {
        return true;
    }

    /*!
     \reimp
     Inserts the string \a $string in the \c 'data_text' database field.
    */
    function insertSimpleString( $object, $objectVersion, $objectLanguage,
                                 $objectAttribute, $string,
                                 &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => true );
        $objectAttribute->setContent( $string );
        $objectAttribute->setAttribute( 'data_text', $string );
        return true;
    }

    function storeClassAttribute( $attribute, $version )
    {
    }

    function storeDefinedClassAttribute( $attribute )
    {
    }

    /*!
     \reimp
    */
    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     \reimp
    */
    function fixupClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
    }

    /*!
     \reimp
    */
    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $templateLocation = $base . self::TEMPLATE_LOCATION_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $templateLocation ) )
        {
            $templateLocationValue = $http->postVariable( $templateLocation );

            $classAttribute->setAttribute( self::TEMPLATE_LOCATION_FIELD, $templateLocationValue );
        }
        return true;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->attribute( 'contentclass_attribute' );
        $templateLocation = $classAttribute->attribute( self::TEMPLATE_LOCATION_FIELD );
        $template = 'design:' . $templateLocation;
        $attrContent = array();
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'xmlinstaller_feature_list', false );


        $content = $tpl->fetch( $template );
        $featureList = false;
        if ( $tpl->variable( "xmlinstaller_feature_list" ) !== false )
        {
            $featureList = $tpl->variable( "xmlinstaller_feature_list" );
        }

        $attrContent['xmlinstaller_feature_list'] = $featureList;
        $attrContent['installed_feature_list'] = unserialize( $contentObjectAttribute->attribute( 'data_text' ) );
        return $attrContent;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return false;
    }
    /*!
     \return string representation of an contentobjectattribute data for simplified export

    */
    function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    function fromString( $contentObjectAttribute, $string )
    {
        return $contentObjectAttribute->setAttribute( 'data_text', $string );
    }


    /*!
     Returns the content of the string for use as a title
    */
    function title( $contentObjectAttribute, $name = null )
    {
        return false;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return trim( $contentObjectAttribute->attribute( 'data_text' ) ) != '';
    }

    /*!
     \reimp
    */
    function isIndexable()
    {
        return false;
    }

    /*!
     \reimp
    */
    function isInformationCollector()
    {
        return false;
    }

    /*!
     \reimp
    */
    function sortKey( $contentObjectAttribute )
    {
        $trans = eZCharTransform::instance();
        return $trans->transformByGroup( $contentObjectAttribute->attribute( 'data_text' ), 'lowercase' );
    }

    /*!
     \reimp
    */
    function sortKeyType()
    {
        return 'string';
    }

    /*!
     \reimp
    */
    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $templateLocationString = $classAttribute->attribute( self::TEMPLATE_LOCATION_FIELD );
        $dom = $attributeParametersNode->ownerDocument;
        if ( $templateLocationString )
        {
            $templateLocationNode = $dom->createElement( 'default-string', $templateLocationString );
        }
        else
        {
            $templateLocationNode = $dom->createElement( 'default-string' );
        }
        $attributeParametersNode->appendChild( $templateLocationNode );
    }

    /*!
     \reimp
    */
    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $templateLocation = $attributeParametersNode->getElementsByTagName( 'default-string' )->item( 0 )->textContent;
        $classAttribute->setAttribute( self::TEMPLATE_LOCATION_FIELD, $templateLocation );
    }

    /*!
      \reimp
    */
    function diff( $old, $new, $options = false )
    {
        $diff = new eZDiff();
        $diff->setDiffEngineType( $diff->engineType( 'text' ) );
        $diff->initDiffEngine();
        $diffObject = $diff->diff( $old->content(), $new->content() );
        return $diffObject;
    }

    /// \privatesection
    /// The max len validator
    public $MaxLenValidator;
}

eZDataType::register( eZFeatureSelectType::DATA_TYPE_STRING, 'eZFeatureSelectType' );

?>
