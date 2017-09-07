<?php

namespace Jass\Style;


class BottomUp extends TopDown
{
    protected function order()
    {
        return array_reverse(parent::order());
    }

    public function name()
    {
        return "Unäufä";
    }

}