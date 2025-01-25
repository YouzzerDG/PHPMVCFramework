<?php namespace Model;

class Service extends Model
{
    protected static array $table = [
        'name' => 'services',
        'columns' => [
            'id',
            'name',
            'price',
            'page_content',
            'url',
            'image'
        ]
    ];

    public function __construct(
        private int    $id,
        private string $name,
        private float  $price,
        private string $page_content,
        private string $url,
        private string $image
    )
    {

    }
}