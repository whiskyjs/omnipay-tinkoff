<?php

namespace whiskyjs\Omnipay\Tinkoff;

use JsonSerializable;

class Items implements JsonSerializable
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @param array $items
     */
    public function __construct($items = [])
    {
        array_push($this->items, ...$items);
    }

    /**
     * @param Item $item
     */
    public function add($item)
    {
        $this->items[] = $item;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        $result = [];

        foreach ($this->items as $item) {
            /**
             * @var Item $item
             */

            if ($item instanceof JsonSerializable) {
                $result[] = $item->jsonSerialize();
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }
}
