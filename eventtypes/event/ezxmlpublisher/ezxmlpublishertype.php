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

require_once 'kernel/common/i18n.php';

class eZXMLPublisherType extends eZWorkflowEventType
{
    public function eZXMLPublisherType()
    {
        if( class_exists( 'ezpI18n' ) )
        {
            $this->eZWorkflowEventType( 'ezxmlpublisher', ezpI18n::tr( 'extension/ezxmkinstaller', 'XML Publisher' ) );
        }
        else
        {
            $this->eZWorkflowEventType( 'ezxmlpublisher', ezi18n( 'extension/ezxmkinstaller', 'XML Publisher' ) );
        }
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    public function attributeDecoder( $event, $attr )
    {
        $retValue = null;
        return $retValue;
    }

    public function typeFunctionalAttributes()
    {
        return array( );
    }

    public function unserializeUserGroupsConfig( &$event )
    {
        $retValue = array();
        return $retValue;
    }

    public function serializeUserGroupsConfig( $userGroups )
    {
        $xmlString = '';
        return $xmlString;
    }

    public function fetchHTTPInput( $http, $base, $event )
    {
    }

    /*!
     \reimp
    */
    public function customWorkflowEventHTTPAction( $http, $action, $workflowEvent )
    {
    }

    public function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $object = eZContentObject::fetch( $parameters['object_id'] );
        $objectVersion = $object->version( $parameters['version'] );

        $attribute = false;

        $dataMap = $objectVersion->attribute( 'data_map' );
        foreach ( $dataMap as $attr )
        {
            $dataType = $attr->attribute( 'data_type_string' );
            if ( $dataType == 'ezfeatureselect' )
            {
                $attribute = $attr;
                continue;
            }
        }

        // if object does not have a featureselect attribute.
        if ( $attribute == false )
        {
            return eZWorkflowType::STATUS_ACCEPTED;
        }

        // defer to cron, this is safer because we might do a lot of things here
        if ( eZSys::isShellExecution() == false )
        {
            return eZWorkflowType::STATUS_DEFERRED_TO_CRON_REPEAT;
        }

        $classAttribute = $attribute->attribute( 'contentclass_attribute' );
        $templateName = $classAttribute->attribute( 'data_text1' );

        $attributeContent = $attribute->attribute( 'content' );
        $installedFeatureList = $attributeContent['installed_feature_list'];
        $availibleFeatureList = $attributeContent['availible_feature_list'];

        if( $templateName == '' )
        {
            return eZWorkflowType::STATUS_ACCEPTED;
        }

        $template = 'design:' . $templateName;
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'xmlinstaller_feature_list', false );

        $content = $tpl->fetch( $template );
        $featureList = $tpl->variable( 'xmlinstaller_feature_list' );

        if ($featureList && is_array($featureList))
        {
            foreach ($featureList as $key => $feature)
            {
                $val = false;
                if (array_key_exists($key, $installedFeatureList) && $installedFeatureList[$key] != "" && $installedFeatureList[$key] != false )
                {
                    $val = $installedFeatureList[$key];
                }
                elseif (array_key_exists('default', $feature))
                {
                    $val = $feature['default'];
                }
                $tpl->setVariable( $key, $val );
            }
        }
       
        $tpl->setVariable( 'install_features', $installedFeatureList );

        $userID = $object->attribute( 'owner_id' );
        $tpl->setVariable( 'owner_object_id', $userID );

        $nodeID = $object->attribute( 'main_node_id' );
        $tpl->setVariable( 'main_node_id', $nodeID );

        $content = $tpl->fetch( $template );
        $xml = $tpl->variable( "xml_data" );

        $doc = new DOMDocument( '1.0', 'utf-8' );
        if( !$doc->loadXML( $xml ) )
        {
            eZDebug::writeError( "Cannot parse XML", "eZXMLPublisherType::execute" );
            return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;
        }

        $xmlInstaller = new eZXMLInstaller( $doc );

        if (! $xmlInstaller->proccessXML() )
        {
            eZDebug::writeError( "Cannot proccess XML", "eZXMLPublisherType::execute" );
            return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;
        }

        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( 'ezxmlpublisher', 'eZXMLPublisherType' );

?>
