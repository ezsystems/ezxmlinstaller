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

require_once( 'autoload.php' );
require_once( 'kernel/common/template.php' );

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

    function eZPrepareXML( )
    {
    }

    function prepareXMLFromTemplate( $templateName, $cli = false )
    {
        $template = 'design:' . $templateName . '.tpl';
        $tpl      = templateInit();

        $tpl->setVariable( 'tpl_info', false );

        $content = $tpl->fetch( $template );
        $tplInfo = false;

        if ( $tpl->variable( "tpl_info" ) !== false )
        {
            $tplInfo = $tpl->variable( "tpl_info" );
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
                $value = eZPrepareXML::getUserInput( "Please enter \"" . $query . "\" (" . $default . "): ", $default );
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
            if ( $acceptValues === false ||
                 in_array( $input, $acceptValues ) )
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
