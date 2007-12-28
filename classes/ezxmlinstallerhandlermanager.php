<?php

class eZXMLInstallerHandlerManager
{

    private function __construct()
    {
        $this->HandlerList    = array();
        $this->MessageArray   = array();
        $this->ReferenceArray = array();
        $this->Settings       = array();
        $this->StepCounter    = 0;
    }

    static function instance()
    {
        if(!eZXMLInstallerHandlerManager::$Instance)
        {
            eZXMLInstallerHandlerManager::$Instance = new eZXMLInstallerHandlerManager();
        }
        return eZXMLInstallerHandlerManager::$Instance;
    }
    /*!
     \static
     Initialize all extension input handler.
    */
    function initialize()
    {
        $ini = eZINI::instance( 'xmlinstaller.ini' );
        $extensionDirectoryList = array_unique( $ini->variable( 'XMLInstallerSettings', 'ExtensionDirectories' ) );
        $handlerList = array_unique( $ini->variable( 'XMLInstallerSettings', 'XMLInstallerHandler' ) );

        foreach( $extensionDirectoryList as $extensionDirectory )
        {
            $handler = reset( $handlerList );
            do
            {
                $fileName = eZExtension::baseDirectory() . '/' . $extensionDirectory . '/xmlinstallerhandler/' . $handler . '.php';
                if ( file_exists( $fileName ) )
                {
                    include_once( $fileName );
                    $className = $handler;
                    $info = call_user_func(array($className, 'handlerInfo'));
                    if ( array_key_exists( 'XMLName', $info ) && array_key_exists( 'Info', $info ) )
                    {
                        $this->HandlerList[$info['XMLName']] = array( 'Info' => $info['Info'],
                                                                      'File' => $fileName,
                                                                      'Class' => $className );
                    }
                    unset($handlerList[key($handlerList)]);
                }
            } while ( $handler = current( $handlerList ) );
        }
    }

    function executeHandler( $name, &$xml )
    {
        $result = false;
        if ( array_key_exists( $name, $this->HandlerList ) )
        {
            $fileName = $this->HandlerList[$name]['File'];
            $className = $this->HandlerList[$name]['Class'];

            if ( file_exists( $fileName ) )
            {
                include_once( $fileName );

                $object = new $className();
                $object->initialize( $this->ReferenceArray, $this->Settings, $this->StepCounter );

                $object->execute( $xml );

                $this->ReferenceArray = $object->references();
                $this->Settings       = $object->settings();
                $this->StepCounter    = $object->counter();
            }
        }
        else
        {
            $this->writeMessage( 'Function ' . $name . ' is not registered.' , 'error' );
        }
        return $result;
    }

    function writeMessage( $message, $type = 'notice' )
    {
        if ( isset( $_SERVER['argv'] ) )
        {
            $cli = eZCLI::instance();
            switch ( $type )
            {
                case 'notice':
                {
                    $cli->notice( $message );
                } break;
                case 'warning':
                {
                    $cli->warning( $message );
                } break;
                case 'error':
                {
                    $cli->error( $message );
                } break;
                default:
                {
                    $cli->notice( $message );
                } break;
            }
        }
        if ( !array_key_exists( $type, $this->MessageArray ) )
        {
            $this->MessageArray[$type] = array();
        }
        $this->MessageArray[$type][] = $message;
    }

    function setSettings( $settingArray )
    {
        if ( is_array( $settingArray ) )
        {
            $this->Settings = $settingArray;
        }
    }

    static private $Instance;

    var $HandlerList;

    var $MessageArray;
    var $ReferenceArray;
    var $Settings;
    var $StepCounter;
}

?>
