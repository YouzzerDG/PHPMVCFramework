<?php namespace Model;

class Contact extends Model
{
    protected static array $table = [
        'name' => 'contacts',
        'columns' => [
            'id',
            'first_name',
            'last_name',
            'email',
            'phone_number'
        ]
    ];

    protected static array $constraints = [
        'notes' => [
            'model' => 'Model\Note',
            'relationType' => 'hasMany',
            'on' => [
                'primaryKey' => 'id',
                'foreignKey' => 'contact_id'
            ]
        ],
    ];
    // 'contacts.id = notes.contact_id'

    public array $notes;
    
    public function __construct(
        private int    $id,
        private string $first_name,
        private string $last_name,
        private string $email,
        private string $phone_number
    )
    {

    }
}