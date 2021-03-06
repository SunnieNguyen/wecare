<?php

namespace Wappointment\Messages;

trait HasTagsToReplace
{
    public function replaceTags()
    {
        foreach ($this->replacing as $property) {
            if (!empty($this->{$property})) {
                $this->{$property} = (new \Wappointment\Messages\TagsReplacement($this->params))->replace($this->{$property});
            }
        }
    }
}
