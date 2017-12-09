<?php

namespace Jass\Style;


class BottomUp extends TopDown
{
    public $name = "bottom up";

    protected function order()
    {
        return array_reverse(parent::order());
    }

}