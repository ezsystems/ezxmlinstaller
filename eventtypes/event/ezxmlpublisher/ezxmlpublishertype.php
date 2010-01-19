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

class eZXMLPublisherType extends eZWorkflowEventType
{
    public function eZXMLPublisherType()
    {
        $this->eZWorkflowEventType( 'ezxmlpublisher', ezi18n( 'extension/ezxmkinstaller', 'XML Publisher' ) );
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
        $attribute = false;

        $dataMap = $object->attribute( 'data_map' );
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

        // if we have not the first version published, we only need to enable/disable features
        if ( $object->attribute( 'modified' ) != $object->attribute( 'published' ) )
        {
            $attributeContent = $attribute->attribute( 'content' );
            $installedFeatureList = $attributeContent['installed_feature_list'];
            $availibleFeatureList = $attributeContent['availible_feature_list'];

            $mainNodeID = $object->attribute( 'main_node_id' );

            foreach( $availibleFeatureList as $feature => $featureName )
            {
                $featureObject = eZContentObject::fetchByRemoteID( $mainNodeID . '_' . $feature );
                if( !$featureObject )
                {
                    eZDebug::writeError( "Cannot find feature object", "eZXMLPublisherType::execute" );
                    continue;
                }
                $featureNode = $featureObject->attribute( 'main_node' );
                if( !$featureNode )
                {
                    eZDebug::writeError( "Cannot find feature node", "eZXMLPublisherType::execute" );
                    continue;
                }
                if ( in_array( $feature, $installedFeatureList ) )
                {
                    if ( $featureNode->attribute( 'is_hidden' ) )
                    {
                        eZContentObjectTreeNode::unhideSubTree( $featureNode );
                    }
                    $featureObject = $featureNode->attribute( 'object' );
                    $list          = $featureObject->reverseRelatedObjectList( false, 0, false,
                                                                               array( 'AllRelations' => eZContentObject::RELATION_ATTRIBUTE, 'IgnoreVisibility' => true )
                                                                             );
                    if ( is_array( $list ) )
                    {
                        foreach ( $list as $reverseRelatedContentObject )
                        {
                            $reverseRelatedMainNode = $reverseRelatedContentObject->attribute( 'main_node' );
                            eZContentObjectTreeNode::unhideSubTree( $reverseRelatedMainNode );
                        }
                    }
                }
                elseif ( !in_array( $feature, $installedFeatureList ) && !$featureNode->attribute( 'is_hidden' ) )
                {
                    if ( !$featureNode->attribute( 'is_hidden' ) )
                    {
                        eZContentObjectTreeNode::hideSubTree( $featureNode );
                    }
                    $featureObject = $featureNode->attribute( 'object' );
                    $list          = $featureObject->reverseRelatedObjectList( false, 0, false,
                                                                               array( 'AllRelations' => eZContentObject::RELATION_ATTRIBUTE, 'IgnoreVisibility' => true )
                                                                             );
                    if ( is_array( $list ) )
                    {
                        foreach ( $list as $reverseRelatedContentObject )
                        {
                            $reverseRelatedMainNode = $reverseRelatedContentObject->attribute( 'main_node' );
                            eZContentObjectTreeNode::hideSubTree( $reverseRelatedMainNode );
                        }
                    }
                }
            }
        }

        // defer to cron, this is safer because we might do a lot of things here
        include_once( 'lib/ezutils/classes/ezsys.php' );
        if ( eZSys::isShellExecution() == false )
        {
            return eZWorkflowType::STATUS_DEFERRED_TO_CRON_REPEAT;
        }

        // if we have the first version published, we need to set up the related things.
        if ( $object->attribute( 'modified' ) == $object->attribute( 'published' ) )
        {
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
            $tpl = templateInit();
            $tpl->setVariable( 'tpl_info', false );

            $content = $tpl->fetch( $template );

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
        // otherwise we need only to enable, disable the selected features.
        else
        {
        }


        return eZWorkflowType::STATUS_ACCEPTED;
    }
}


eZWorkflowEventType::registerEventType( 'ezxmlpublisher', 'eZXMLPublisherType' );

?>
