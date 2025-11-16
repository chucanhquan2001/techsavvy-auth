<?php

namespace App\Domain\Entities;

class User
{
    public function __construct(
        public int $id,
        public string $email,
        public string $name
    ) {
        // có thể validate logic ở trong này, bảo đảm gọi user phải truyền đúng và đủ tham số
    }
}