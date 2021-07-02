<?php

namespace wavecell;

class call
{
        public $packet;
        public $msisdn;
	    public $name;
        public $message;

        public function __construct()
        {
                // receive packet from core
                $packet = trim(file_get_contents("php://input"));
                $this->packet= trim(preg_replace('/\s\s+/', '', $packet));
                $object = json_decode($this->packet, true);

                // get msisdn
                $msisdn = $object['user']['msisdn'];
                if($msisdn[0] == '0')
                        $msisdn = '6'.$msisdn;
                else if($msisdn[0] == '+')
                        $msisdn = substr($msisdn,1);
                $this->msisdn = $msisdn;

                $this->name = $object['user']['name'];
                $this->message = $object['content']['text'];
        }
}

?>
