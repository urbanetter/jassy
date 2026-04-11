<?php

namespace Jass\Style;


class BottomUp extends TopDown
{
    public string $name = "bottom up";

    #[\Override]
    protected function order(): array
    {
        return array_reverse(parent::order());
    }

}
