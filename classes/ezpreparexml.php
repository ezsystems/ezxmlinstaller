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

if ( !function_exists( 'readline' ) )
{
    function readline( $prompt = '' )
    {
        echo $prompt . ' ';
        return trim( fgets( STDIN ) );
    }
}

class eZPrepareXML
{

    private function __construct()
    {
    }

    function prepareXMLFromTemplate( $templateName, $cli = false )
    {
        $template = 'design:' . $templateName;
        $tpl = eZTemplate::factory();

        $tpl->setVariable( 'xmlinstaller_feature_list', false );

        $content = $tpl->fetch( $template );
        $tplInfo = false;

        if ( $tpl->variable( "xmlinstaller_feature_list" ) !== false )
        {
            $tplInfo = $tpl->variable( "xmlinstaller_feature_list" );
        }
        if ( is_array( $tplInfo ) )
        {
            foreach ( $tplInfo as $var => $info )
            {
                if ( isset( $info['info'] ) )
                {
                    $query = $info['info'];
                }
                else
                {
                    $query = 'Info for ' . $var;
                }
                $default = '';
                if ( isset( $info['default'] ) )
                {
                    $default = $info['default'];
                }
                $value = "";
                $question = "Please enter \"" . $query . "\"";
                if ($default)
                {
                    $question .= " (" . $default . ")";
                }
                switch($info['type'])
                {
                    case "string":
                    {
                        $question .= ": ";
                        $value = eZPrepareXML::getUserInput( $question, $default );
                    } break;
                    case "selection":
                    {
                        $question .= " [";
                        $optionList = array();
                        foreach ($info['vars'] as $k => $v)
                        {
                            $optionList[] = $k;
                            $question .= " $k($v), ";
                        }
                        $question .= "]: ";
                        $value = eZPrepareXML::getUserInput( $question, $default );
                    } break;
                    case "boolean":
                    {
                        $optionList = array('yes', 'no', 'y', 'n');
                        $question .= " [" . implode(', ', $optionList) . "]: "; 
                        $value = eZPrepareXML::getUserInput( $question, $default, $optionList );
                        if ($value == 'y' || $value == 'yes')
                        {
                            $value = true;
                        }
                        else
                        {
                            $value = false;
                        }
                    } break;
                    default:
                    {
                        $question .= ": ";
                        $value = eZPrepareXML::getUserInput( $question, $default );
                    }
                }
                $tpl->setVariable( $var, $value );
            }
        }
        $content = $tpl->fetch( $template );
        $xml     = $tpl->variable( "xml_data" );
        return $xml;
    }

    function prepareXMLFromFile( $fileName, $cli = false )
    {
        if ( !file_exists( $fileName ) )
        {
            $cli->error( "Can not open file \"$fileName\"." );
            return false;
        }

        $xml = file_get_contents( $fileName );

        if ( !$xml )
        {
            $cli->error( "File \"$fileName\" is empty." );
            return false;
        }
        return $xml;
    }

    function getUserInput( $query, $defaultValue = false, $acceptValues = false )
    {
        $validInput = false;
        while ( !$validInput )
        {
            $input = readline( $query );
            if ( $acceptValues === false || in_array( $input, $acceptValues ) || $defaultValue != false )
            {
                $validInput = true;
            }
        }
        if ( !$input )
        {
            return $defaultValue;
        }
        else
        {
            return $input;
        }
    }
}

?>
