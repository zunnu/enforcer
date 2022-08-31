<?php
declare(strict_types=1);

namespace Enforcer\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Cache\Cache;
use Cake\Core\Configure;

/**
 * EnforcerSessionCleaner component
 */
class SessionCleanerComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'sessionPath' => TMP . 'sessions/',
        // clean time in minutes
        'cleanTime' => 60 * 24 * 7, // 1 week
        // how old the file needs to be in days before deletion
        'fileDeleteTime' => 30,
    ];

    public function initialize(array $config) {
        if(empty($config['sessionPath'])) {
            $config['sessionPath'] = $this->_defaultConfig['sessionPath'];
        }

        if(empty($config['cleanTime'])) {
            $config['cleanTime'] = $this->_defaultConfig['cleanTime'];
        }

        if(empty($config['fileDeleteTime'])) {
            $config['fileDeleteTime'] = $this->_defaultConfig['fileDeleteTime'];
        }

        $this->EnforcerConfig = $config;

        if(!Cache::config('enforcer_session_clean')) {
            Cache::config('enforcer_session_clean', [
                'className' => 'Cake\Cache\Engine\FileEngine',
                'duration' => '+50 year',
                'path' => CACHE . 'enforcer' . DS,
            ]);

            if(!Cache::read('last_session_clean_time', 'enforcer_session_clean')) {
                Cache::write('last_session_clean_time', time(), 'enforcer_session_clean');
            }
        }
    }

    /**
     * Check if the session files need to be cleaned
     * @return boolean
     */
    public function check() {
        if(!empty(Configure::read('Session.defaults')) && strtolower(Configure::read('Session.defaults')) != 'cake') {
            return false;
        }

        $lastClean = new \DateTime();
        $lastClean->setTimestamp(Cache::read('last_session_clean_time', 'enforcer_session_clean'));
        $nextClean = new \DateTime();
        $nextClean->setTimestamp($lastClean->getTimestamp());
        $nextClean->modify('+ ' . $this->EnforcerConfig['cleanTime'] . ' minutes');
        
        if(time() >= $nextClean->getTimestamp()) {
            if($this->clean()) {
                Cache::write('last_session_clean_time', time(), 'enforcer_session_clean');
                return true;
            }

            return false;
        }
    }

    /**
     * Clean the session files that are no longer needed
     * @return boolean
     */
    private function clean() {
        // get all session files
        $files = glob($this->EnforcerConfig['sessionPath'] . "*");
        $time = new \DateTime();
        $time->modify('- ' . $this->EnforcerConfig['fileDeleteTime'] . ' day');

        if(!empty($files)) {
            foreach ($files as $file) {
                if(is_file($file)) {
                    if($time->getTimestamp() >= filectime($file)) {
                        // delete file
                        unlink($file);
                    }
                }
            }
        }

        return true;
    }
}
