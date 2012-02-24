<?php
class Mage_Task_BuiltIn_Deployment_Releases
    extends Mage_Task_TaskAbstract
    implements Mage_Task_Releases_BuiltIn
{
    public function getName()
    {
        return 'Releasing [built-in]';
    }

    public function run()
    {
        if ($this->_config->release('enabled', false) == true) {
            $releasesDirectory = $this->_config->release('directory', 'releases');
            $symlink = $this->_config->release('symlink', 'current');

            $currentCopy = $releasesDirectory . '/' . $this->_config->getReleaseId();

            // Fetch the user and group from base directory
            $userGroup = '33:33';
            $resultFetch = $this->_runRemoteCommand('ls -ld . | awk \'{print \$3\":\"\$4}\'', $userGroup);
            
            // Remove symlink if exists; create new symlink and change owners
            $command = 'rm -f ' . $symlink
                     . ' ; '
                     . 'ln -sf ' . $currentCopy . ' ' . $symlink
                     . ' && '
                     . 'chown -h ' . $userGroup . ' ' . $symlink
                     . ' && '
                     . 'chown -R ' . $userGroup . ' ' . $currentCopy; 
            $result = $this->_runRemoteCommand($command);
            return $result;

        } else {
            return false;
        }
    }

}