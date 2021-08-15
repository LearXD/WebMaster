<?php

/*
 *   _        ______              _____   __   __  _____
 *  | |      |  ____|     /\     |  __ \  \ \ / / |  __ \
 *  | |      | |__       /  \    | |__) |  \ V /  | |  | |
 *  | |      |  __|     / /\ \   |  _  /    > <   | |  | |
 *  | |____  | |____   / ____ \  | | \ \   / . \  | |__| |
 *  |______| |______| /_/    \_\ |_|  \_\ /_/ \_\ |_____/
 *
 *  This API was developed by LearXD for other uses, it is allowed to modify
 *  almost everything here! Leave my watermark to help me ;)
 *
 *  Twitter: @XDLear
 *
 */

namespace bridge\utils {

    use pocketmine\Server;

    class WebMaster
    {

        /**
         * @var array
         */
        protected static $process = [];

        /**
         * @param string $url
         * @param array $data
         * @param callable $function
         * @return bool
         */
        public static function createProcess(string $url, callable $function, array $data = []): bool
        {
            self::$process[$pid = count(self::$process)] = $function;
            Server::getInstance()->getScheduler()->scheduleAsyncTask(new RequestAsync($pid, $url, $data));
            return true;
        }

        /**
         * @param int $pid
         * @param array $result
         * @return bool
         */
        public static function processResult(int $pid, array $result): bool
        {
            $callable = self::$process[$pid];
            $callable->call(Server::getInstance(), $result);

            unset(self::$process[$pid]);
            return true;
        }

    }

    class RequestAsync extends \pocketmine\scheduler\AsyncTask
    {

        /**
         * @var int
         */
        private $pid = 0;

        /**
         * @var string
         */
        protected $url = "";
        /**
         * @var array
         */
        protected $data;


        /**
         * RequestAsync constructor.
         * @param int $pid
         * @param string $url
         * @param array $pdata
         */
        public function __construct(int $pid, string $url, array $pdata = [])
        {
            $this->pid = $pid;
            $this->url = serialize($url);
            $this->data = serialize($pdata);
        }

        public function onRun()
        {
            $data = unserialize($this->data);
            $url = unserialize($this->url);

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge(["User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36 OPR/77.0.4054.298"], []));

            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            if ($curl) {
                $result = [];
                if (\count($data) <= 0) {
                    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
                    curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                } else {
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                }
                $result = curl_exec($curl);
                curl_close($curl);
                $this->setResult(json_decode($result, true) ?? [ 'error' => 'Unable to perform the search!']);
            } else {
                $this->setResult([ 'error' => 'Unable to resolve search data!' ], true);
            }
        }

        /**
         * @param Server $server
         */
        public function onCompletion(Server $server)
        {
            WebMaster::processResult($this->pid, $this->getResult());
        }
    }
}



