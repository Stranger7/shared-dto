<?php

namespace dto;

class Success extends Output implements SuccessInterface
{
    public static function create(AbstractDto|array $data): Success
    {
        $self = new self();
        if ($data instanceof AbstractDto) {
            $data = $data->asArray();
        }
        if (is_array($data)) {
            foreach ($data as $field => $value) {
                $self->$field = $value;
            }
        }

        return $self;
    }
}
