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

class eZSendMail extends eZXMLInstallerHandler
{

    function __construct( )
    {
    }

    function execute( $xml )
    {
        $template   = $xml->getAttribute( 'template' );
        $receiverID = $xml->getAttribute( 'receiver' );
        $nodeID     = $xml->getAttribute( 'node' );

        $ini = eZINI::instance();
        $mail = new eZMail();
        $tpl = eZTemplate::factory();

        $node = eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$node )
        {
            $node = eZContentObjectTreeNode::fetch( 2 );
        }

        $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$emailSender )
            $emailSender = $ini->variable( "MailSettings", "AdminEmail" );

        $receiver = eZUser::fetch( $receiverID );
        if ( !$receiver )
        {
            $emailReceiver = $emailSender;
        }
        else
        {
            $emailReceiver = $receiver->attribute( 'email' );
        }

        $tpl->setVariable( 'node', $node );
        $tpl->setVariable( 'receiver', $receiver );

        $body = $tpl->fetch( 'design:' . $template );
        $subject = $tpl->variable( 'subject' );

        $mail->setReceiver( $emailReceiver );
        $mail->setSender( $emailSender );
        $mail->setSubject( $subject );
        $mail->setBody( $body );

        $mailResult = eZMailTransport::send( $mail );
        return $mailResult;
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'SendMail', 'Info' => 'send mail' );
    }
}

?>
