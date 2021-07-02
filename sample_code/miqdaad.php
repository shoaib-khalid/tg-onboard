<?php

namespace miqdaad;

class get_response
{
        public $packet;
        public $object;

        public function __construct()
        {
                $packet = trim(file_get_contents("php://input"));
                $packet = trim(preg_replace('/\s\s+/', '', $packet));
                $this->object = json_decode($packet, true);
        }
}

?>
