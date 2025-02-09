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

    public array $notes = [];
    
    public function __construct(
        private int    $id,
        private string $first_name,
        private string $last_name,
        private string $email,
        private string $phone_number
    )
    {

    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
            return $this->id;
    }

    /**
     * Get the value of first_name
     */ 
    public function getFirstName()
    {
            return $this->first_name;
    }

    /**
     * Get the value of last_name
     */ 
    public function getLastName()
    {
            return $this->last_name;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
            return $this->email;
    }

    /**
     * Get the value of phone_number
     */ 
    public function getPhoneNumber()
    {
            return $this->phone_number;
    }
}