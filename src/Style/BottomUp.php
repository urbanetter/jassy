<?php

namespace Jass\Style;


class BottomUp extends TopDown
{
    public $name = "Uneufä";

    protected function order()
    {
        return array_reverse(parent::order());
    }

}