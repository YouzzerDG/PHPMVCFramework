<?php namespace Model;

class Note extends Model
{
    protected static array $table = [
        'name' => 'notes',
        'columns' => [
            'id',
            'content',
        ],
        'foreignKeys' => [
            'contact_id'
        ]
    ];

    public function __construct(
        private int    $id,
        private string $content,
    )
    {

    }
}