<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class UserCollection extends Collection
{
    public function active(): UserCollection
    {
        return $this->whereNull('deleted_at');
    }

    public function inactive(): UserCollection
    {
        return $this->whereNotNull('deleted_at');
    }
}
