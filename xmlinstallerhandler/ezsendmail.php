<?php
include_once('extension/ezxmlinstaller/classes/ezxmlinstallerhandler.php');

class eZSendMail extends eZXMLInstallerHandler
{

    function eZSendMail( )
    {
    }

    function execute( $xml )
    {
        $template   = $xml->getAttribute( 'template' );
        $receiverID = $xml->getAttribute( 'receiver' );
        $nodeID     = $xml->getAttribute( 'node' );

        include_once( 'kernel/common/template.php' );
        $ini = eZINI::instance();
        $mail = new eZMail();
        $tpl = templateInit();

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